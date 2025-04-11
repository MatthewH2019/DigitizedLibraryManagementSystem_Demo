<?php
// test_db.php
// Tests the database connection and prints success or failure.

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Make sure all .env variables are accessible by older libraries or system functions
foreach ($_ENV as $k => $v) putenv("$k=$v");

require_once __DIR__ . '/config/Database.php';

$db = new Database();
$conn = $db->connect();

if ($conn) {
    echo "Database connection successful!\n";
} else {
    echo "Database connection failed!\n";
}





