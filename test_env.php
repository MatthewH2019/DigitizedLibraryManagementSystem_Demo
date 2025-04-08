<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo 'Database Host: ' . getenv('HOST') . "\n";
var_dump($_ENV);