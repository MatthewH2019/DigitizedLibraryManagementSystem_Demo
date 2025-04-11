<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
foreach ($_ENV as $k => $v) putenv("$k=$v");

// Include Database and Author
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/Author.php';

// Connect and fetch
$db = new Database();
$conn = $db->connect();

// Fetch all authors from the database
$stmt = $conn->prepare('SELECT authorid, name FROM authors ORDER BY authorid');
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display results
echo "Current authors in database:\n";
foreach ($rows as $row) {
    echo "- [{$row['authorid']}] {$row['name']}\n";
}
echo "Total: " . count($rows) . " authors.\n";
