<?php

use App\Core\App;
use App\Service\FolderService;
use PHPUnit\Framework\TestCase;

final class FolderServiceTest extends BaseTest
{
    public function testCreateFolderWithoutName()
    {
        $path = '';
        $this->expectException(LogicException::class);
        self::$folderService->createFolder($path);
    }

    public function testCreateFolder()
    {
        $path = '/root';
        $this->assertEquals('Folder /root has been created', self::$folderService->createFolder($path));
    }

    public function testCreateSubfolder()
    {
        $path = '/root/test';
        $this->assertEquals('Folder /root/test has been created', self::$folderService->createFolder($path));
    }

    public function testCreateNonExistingSubfolder()
    {
        $path = '/root/test/rest/nest';
        $this->expectException(LogicException::class);
        self::$folderService->createFolder($path);
    }

    public function testRemoveNonExistingFolder()
    {
        $path = '/home';
        $this->expectException(LogicException::class);
        self::$folderService->removeFolder($path);
    }

    public function testRemoveFolder()
    {
        $path = '/root/test';
        $this->assertEquals('Folder /root/test removed successfuly', self::$folderService->removeFolder($path));
    }
}