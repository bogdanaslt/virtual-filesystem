<?php

namespace App\Model;

class LocalFile
{
    public int $id;

    public int $folder_id;

    public string $filename;

    public string $local_filesystem_path;
}