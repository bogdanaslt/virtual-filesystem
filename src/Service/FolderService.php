<?php

namespace App\Service;

use App\Repository\FolderRepository;
use LogicException;

class FolderService
{

    private FolderRepository $folderRepository;

    public function __construct()
    {
        $this->folderRepository = new FolderRepository();
    }

    public function createFolder($path)
    {
        if ($this->folderRepository->getFolder(trim($path, '/'))) {
            throw new LogicException("Folder with path {$path} already exists");
        }

        if (empty(trim($path, '/'))) {
            throw new LogicException("Folder name must be provided");
        }

        $parts = array_filter(explode('/', $path));
        $name = array_pop($parts);
        $path = $parts ? ('/' . implode('/', $parts)) : null;
        if ($path && !$this->folderRepository->getFolder(trim($path, '/'))) {
            throw new LogicException("Parent path {$path} does not exists");
        }

        $this->folderRepository->createFolder($name, trim($path, '/'));

        return "Folder {$path}/{$name} has been created";
    }

    public function getTree(string $root = null)
    {
        $folders = $this->folderRepository->getTree($root);
        $response = '';
        foreach ($folders as $folder) {
            $response .= $folder->depth > 0 ? str_repeat(' ', $folder->depth * 2) : '';
            $response .= $folder->name . PHP_EOL;
        }

        return $response;
    }

    public function removeFolder($path)
    {
        if (empty(trim($path, '/'))) {
            throw new LogicException("Folder name must be provided");
        }

        $folder = $this->folderRepository->getFolder(trim($path, '/'));
        if (!$folder) {
            throw new LogicException("No such folder {$path}");
        }

        $this->folderRepository->removeFolder($folder);

        return "Folder {$path} removed successfuly";
    }
}