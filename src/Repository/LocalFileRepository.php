<?php

namespace App\Repository;

use App\Core\App;
use App\Model\Folder;
use App\Model\LocalFile;
use PDO;

class LocalFileRepository
{
    public function copy(string $from, Folder $to)
    {
        $file = new LocalFile();
        $file->folder_id = $to->id;
        $file->local_filesystem_path = $from;
        $file->filename = basename($from);
        $statement = App::$db->prepare('INSERT INTO local_file (folder_id, filename, local_filesystem_path) VALUES (:folder_id, :filename, :local_filesystem_path)');
        $statement->execute([
            'folder_id' => $to->id,
            'filename' => $file->filename,
            'local_filesystem_path' => $file->local_filesystem_path
        ]);
        $file->id = App::$db->query('SELECT last_insert_rowid()')->fetchColumn();

        return $file;
    }

    public function exists(string $filename, Folder $folder)
    {
        $statement = App::$db->prepare('SELECT id FROM local_file WHERE folder_id = :folder_id AND filename = :filename');
        $statement->execute([
            'filename' => $filename,
            'folder_id' => $folder->id
        ]);

        return $statement->fetch();
    }

    public function getFilesInFolder(Folder $folder)
    {
        $statement = App::$db->prepare('SELECT id, filename, local_filesystem_path FROM local_file WHERE folder_id = :folder_id');
        $statement->execute([
            'folder_id' => $folder->id
        ]);

        return $statement->fetchAll(PDO::FETCH_CLASS, LocalFile::class);
    }

    public function remove(string $filename, Folder $folder)
    {
        $statement = App::$db->prepare('DELETE FROM local_file WHERE folder_id = :folder_id AND filename = :filename');
        return $statement->execute([
            'folder_id' => $folder->id,
            'filename' => $filename
        ]);
    }
}