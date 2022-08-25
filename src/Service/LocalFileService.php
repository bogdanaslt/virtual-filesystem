<?php

namespace App\Service;

use App\Model\Folder;
use App\Repository\FolderRepository;
use App\Repository\LocalFileRepository;
use LogicException;

class LocalFileService
{

    private FolderRepository $folderRepository;

    private LocalFileRepository $localFileRepository;

    public function __construct()
    {
        $this->folderRepository = new FolderRepository();
        $this->localFileRepository = new LocalFileRepository();
    }

    public function copy($from, $to)
    {
        if (!$from || !$to) {
            throw new LogicException('From and To paths must be provided');
        }
        
        if (!is_readable($from)) {
            throw new LogicException("File {$from} in local file system is not readable");
        }

        $folder = $this->folderRepository->getFolder(trim($to, '/'));
        if (!$folder) {
            throw new LogicException("Folder {$to} in virtual file system does not exist");
        }

        if ($this->localFileRepository->exists($name = basename($from), $folder)) {
            throw new LogicException("File {$name} already exists in folder {$to}");
        }

        $file = $this->localFileRepository->copy($from, $folder);

        return "File {$file->filename} saved successfuly";
    }

    public function list(?string $path)
    {
        if (!$path) {
            throw new LogicException("Folder path must be provided");
        }

        $folder = $this->folderRepository->getFolder(trim($path, '/'));
        if (!$folder) {
            throw new LogicException("No such folder {$path}");
        }

        $files = $this->localFileRepository->getFilesInFolder($folder);
        if (empty($files)) {
            return "Folder {$path} is empty";
        }

        $response = '';
        foreach ($files as $file) {
            $response .= "{$file->filename} => {$file->local_filesystem_path}\n";
        }

        return $response;
    }

    public function remove(?string $path)
    {
        if (!$path) {
            throw new LogicException('Filepath with filename must be provided');
        }

        $parts = array_filter(explode('/', $path));
        $filename = array_pop($parts);
        $path = implode('/', $parts);

        $folder = $this->folderRepository->getFolder(trim($path, '/'));
        if (!$folder) {
            throw new LogicException("No such folder {$path}");
        }

        if (!$this->localFileRepository->exists($filename, $folder)) {
            throw new LogicException("File {$filename} does not exists in folder {$path}");
        }

        $this->localFileRepository->remove($filename, $folder);

        return "File {$filename} removed from folder {$path}";
    }
}