<?php

if (file_exists(__DIR__.'/../storage/db.sq3')) {
    echo "Database already exists. Doing nothing.\n";
    exit;
}

$db = new PDO('sqlite:'.__DIR__.'/../storage/db.sq3');
$db->exec('PRAGMA foreign_keys = ON');
$db->exec('CREATE TABLE local_file (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    folder_id INTEGER NOT NULL,
    filename TEXT(255) NOT NULL,
    local_filesystem_path TEXT(1024) NOT NULL,
    CONSTRAINT local_file_FK FOREIGN KEY (folder_id) REFERENCES folder(id) ON DELETE CASCADE
)');
$db->exec('CREATE UNIQUE INDEX local_file_folder_id_IDX ON local_file (folder_id,filename)');
$db->exec('CREATE TABLE folder (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    parent_id INTEGER,
    name TEXT(255) NOT NULL,
    CONSTRAINT folder_FK FOREIGN KEY (parent_id) REFERENCES folder(id) ON DELETE CASCADE
)');
$db->exec('CREATE INDEX folder_parent_IDX ON folder (parent_id)');
$db->exec('CREATE UNIQUE INDEX folder_parent_id_IDX ON folder (parent_id,name)');
echo "Database created.\n";