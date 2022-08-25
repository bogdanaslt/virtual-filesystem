<?php

use App\Core\App;
use App\Service\FolderService;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected static FolderService $folderService;

    public static function setUpBeforeClass(): void
    {
        App::$db = new PDO('sqlite::memory:');
        App::$db->exec('PRAGMA foreign_keys = ON');
        App::$db->exec('CREATE TABLE local_file (
            id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            folder_id INTEGER NOT NULL,
            filename TEXT(255) NOT NULL,
            local_filesystem_path TEXT(1024) NOT NULL,
            CONSTRAINT local_file_FK FOREIGN KEY (folder_id) REFERENCES folder(id) ON DELETE CASCADE
        )');
        App::$db->exec('CREATE UNIQUE INDEX local_file_folder_id_IDX ON local_file (folder_id,filename)');
        App::$db->exec('CREATE TABLE folder (
            id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            parent_id INTEGER,
            name TEXT(255) NOT NULL,
            CONSTRAINT folder_FK FOREIGN KEY (parent_id) REFERENCES folder(id) ON DELETE CASCADE
        )');
        App::$db->exec('CREATE INDEX folder_parent_IDX ON folder (parent_id)');
        App::$db->exec('CREATE UNIQUE INDEX folder_parent_id_IDX ON folder (parent_id,name)');

        self::$folderService = new FolderService();
    }
}