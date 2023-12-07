<?php

include __DIR__ . '/src/Framework/Database.php';
require __DIR__ . "/vendor/autoload.php";


use App\Config\Paths;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(Paths::ROOT);
$dotenv->load();

use Framework\Database;

$db = new Database(
    $_ENV['DB_DRIVER'],
    ['host' => $_ENV['DB_HOST'], 'port' => $_ENV['DB_PORT'], 'dbname' => $_ENV['DB_NAME']],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
);

$sqlFile = file_get_contents("./database.sql");
echo $sqlFile;

$db->query($sqlFile);
