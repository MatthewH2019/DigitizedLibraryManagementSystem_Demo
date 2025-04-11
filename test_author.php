<?php
// test_author.php - A test script for the Author model
// Tests: getAuthors(), createAuthor(), updateAuthor(), deleteAuthor()
// Automatically cleans up after itself

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Propagate environment variables
foreach ($_ENV as $k => $v) {
    putenv("$k=$v");
}

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/Author.php';

// Setup database and model
$database = new Database();
$conn = $database->connect();
$authorModel = new Author($conn);

$createdId = null;

try {
    // TEST 1: getAuthors()
    echo "\n### TEST 1: getAuthors() ###\n";
    $stmt = $authorModel->getAuthors();
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($authors as $i => $row) {
        echo ($i + 1) . ". [{$row['authorid']}] {$row['name']}\n";
    }
    echo "Total records: " . count($authors) . "\n";

    // TEST 2: createAuthor()
    echo "\n### TEST 2: createAuthor() ###\n";
    $uniqueName = "Test Author " . time();
    $authorModel->author = $uniqueName;
    ob_start();
    $ok = $authorModel->createAuthor();
    $out = ob_get_clean();
    echo $ok ? "createAuthor() succeeded.\n" : "createAuthor() failed.\n";
    echo "Output: $out\n";
    $data = json_decode($out, true);
    $createdId = $data['authorid'] ?? null;
    echo $createdId ? "Created ID: $createdId\n" : "No ID returned\n";
    if (!$createdId) throw new Exception("Cannot proceed without createdId");

    // TEST 3: updateAuthor()
    echo "\n### TEST 3: updateAuthor() ###\n";
    $authorModel->id = $createdId;
    $authorModel->author = "Updated $uniqueName";
    ob_start();
    $ok = $authorModel->updateAuthor();
    $out = ob_get_clean();
    echo $ok ? "updateAuthor() succeeded.\n" : "updateAuthor() failed.\n";
    echo "Output: $out\n";

    // TEST 4: deleteAuthor()
    echo "\n### TEST 4: deleteAuthor() ###\n";
    $authorModel->id = $createdId;
    $ok = $authorModel->deleteAuthor();
    echo $ok ? "deleteAuthor() succeeded.\n" : "deleteAuthor() failed.\n";
} catch (Exception $e) {
    echo "\nTest error: " . $e->getMessage() . "\n";
} finally {
    // Final Cleanup for both Authors and Books
    echo "\n### FINAL CLEANUP: calling remove_test_records.php ###\n";
    include_once __DIR__ . '/remove_test_records.php';
    echo "\n--- End of Tests ---\n";
}
?>