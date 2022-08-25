<?php

use App\Core\App;
use App\Database\SQLite;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new App();
try  {
    echo $app->handle($argv) . PHP_EOL;
} catch (LogicException $e) {
    echo $e->getMessage() . PHP_EOL;
}