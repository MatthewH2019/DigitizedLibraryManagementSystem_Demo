<?php
// test_env.php
// Verifies that required environment variables are loaded from the .env file.

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Output the required variables to confirm they are present
$required = ['HOST','DBNAME','USERNAME','PASSWORD'];
foreach ($required as $var) {
    $val = $_ENV[$var] ?? '';
    echo "$var = $val\n";
}