<?php

namespace App\Model;

use App\Core\App;

class Folder
{
    public int $id;

    public ?int $parent_id;

    public string $name;

    public int $depth = 0;
}