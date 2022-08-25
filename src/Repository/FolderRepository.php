<?php

namespace App\Repository;

use App\Core\App;
use App\Model\Folder;
use LogicException;
use PDO;

class FolderRepository
{
    public function getFolder(string $path): Folder|bool
    {
        $sql = <<<SQL
            WITH RECURSIVE folder_path (id, parent_id, name) AS
            (
            SELECT id, parent_id, name
                FROM folder
                WHERE parent_id IS NULL
            UNION ALL
            SELECT f.id, f.parent_id, fp.name || '/' || f.name
                FROM folder_path AS fp JOIN folder AS f
                ON fp.id = f.parent_id
            )
            SELECT id, parent_id, name FROM folder_path where name = :path LIMIT 1;
        SQL;
        $statement = App::$db->prepare($sql);
        $statement->execute(['path' => $path]);

        return $statement->fetchObject(Folder::class);
    }

    public function createFolder(string $name, ?string $path)
    {
        $parent = $path ? $this->getFolder($path) : null;
        $folder = new Folder();
        $folder->parent_id = $parent ? $parent->id : null;
        $folder->name = $name;
        $statement = App::$db->prepare('INSERT INTO folder (parent_id, name) VALUES (:parent_id, :name)');
        $statement->execute([
            'parent_id' => $folder->parent_id,
            'name' => $folder->name
        ]);
        $folder->id = App::$db->query('SELECT last_insert_rowid()')->fetchColumn();

        return $folder;
    }

    public function getTree(?string $path)
    {
        $parent = $path ? $this->getFolder($path) : null;
        if ($path && !$parent) {
            throw new LogicException("No such folder {$path}");
        }

        $addSql = $path ? 'WHERE id = :parent_id' : 'WHERE parent_id IS NULL';
        $sql = <<<SQL
            WITH RECURSIVE folder_path (id, parent_id, name, depth) AS
            (
            SELECT id, parent_id, name, 0 as depth
                FROM folder {$addSql}
            UNION ALL
            SELECT f.id, f.parent_id, f.name, depth + 1
                FROM folder_path AS fp JOIN folder AS f
                ON fp.id = f.parent_id
            )
            SELECT id, parent_id, name, depth FROM folder_path order by COALESCE(parent_id, id), parent_id is not null, id
        SQL;
        $statement = App::$db->prepare($sql);
        $statement->execute($path ? ['parent_id' => $parent->id] : []);

        return $statement->fetchAll(PDO::FETCH_CLASS, Folder::class);
    }

    public function removeFolder(Folder $folder)
    {
        $statement = App::$db->prepare("DELETE FROM folder WHERE id = :id");

        return $statement->execute(['id' => $folder->id]);
    }
}