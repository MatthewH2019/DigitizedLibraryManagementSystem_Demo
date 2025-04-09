<?php
// remove_test_author.php
// Deletes authors whose names start with 'Test Author'.

require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
foreach ($_ENV as $k => $v) putenv("$k=$v");

require_once __DIR__ . '/config/Database.php';

$db = new Database();
$conn = $db->connect();

// Delete any author whose name begins with 'Test Author'
// Using LIKE ensures only test-created entries are targeted
$stmt = $conn->prepare("DELETE FROM authors WHERE name LIKE :prefix");
$prefix = 'Test Author %';
$stmt->bindParam(':prefix', $prefix);
$stmt->execute();

echo "Deleted {$stmt->rowCount()} test author(s).\n";