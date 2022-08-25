<?php

namespace App\Core;

use App\Database\SQLite;
use App\Service\FolderService;
use App\Service\LocalFileService;
use PDO;

class App
{
    public static PDO $db;

    public function __construct()
    {
        self::$db = (new SQLite())->getConnection();
    }

    public function handle($argv)
    {
        [, $command, ] = $argv;
        return match($command) {
            'mkdir' => (new FolderService())->createFolder(!empty($argv[2]) ? $argv[2] : null),
            'rmdir' => (new FolderService())->removeFolder(!empty($argv[2]) ? $argv[2] : null),
            'tree' => (new FolderService())->getTree(count($argv) > 2 ? $argv[2] : null),
            'cp' => (new LocalFileService())->copy(!empty($argv[2]) ? $argv[2] : null, !empty($argv[3]) ? $argv[3] : null),
            'ls' => (new LocalFileService())->list(!empty($argv[2]) ? $argv[2] : null),
            'rm' => (new LocalFileService())->remove(!empty($argv[2]) ? $argv[2] : null),
            'help' => $this->showHelpInfo(),
            default => $this->showHelpInfo()
        };
    }

    private function showHelpInfo()
    {
        return file_get_contents(__DIR__.'/../../storage/help.txt');
    }
}