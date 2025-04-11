<?php
// remove_test_records.php - Combined removal for test authors and books
// Deletes test authors whose names begin with 'Test Author ' or 'Updated Test Author '
// and test books whose titles begin with 'Test Book ' or 'Updated Test Book '

require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Propagate environment variables
foreach ($_ENV as $k => $v) {
    putenv("$k=$v");
}

require_once __DIR__ . '/config/Database.php';

$db = new Database();
$conn = $db->connect();

if (!$conn) {
    echo "Connection Error: Could not connect to the database.\n";
    exit;
}

// Remove test authors
$queryAuthors = "DELETE FROM authors WHERE name LIKE :prefix1 OR name LIKE :prefix2";
$stmtAuthors = $conn->prepare($queryAuthors);
$prefix1 = 'Test Author %';
$prefix2 = 'Updated Test Author %';
$stmtAuthors->bindParam(':prefix1', $prefix1);
$stmtAuthors->bindParam(':prefix2', $prefix2);
$stmtAuthors->execute();
echo "Deleted {$stmtAuthors->rowCount()} test author(s).\n";

// Remove test books
$queryBooks = "DELETE FROM books WHERE title LIKE :bprefix1 OR title LIKE :bprefix2";
$stmtBooks = $conn->prepare($queryBooks);
$bprefix1 = 'Test Book %';
$bprefix2 = 'Updated Test Book %';
$stmtBooks->bindParam(':bprefix1', $bprefix1);
$stmtBooks->bindParam(':bprefix2', $bprefix2);
$stmtBooks->execute();
echo "Deleted {$stmtBooks->rowCount()} test book(s).\n";
?>
