<?php
// test_author.php - A test script for the Author model
// Tests: getAuthors(), createAuthor(), updateAuthor(), deleteAuthor()
// Automatically cleans up after itself

require_once __DIR__ . '/vendor/autoload.php';

// Load and propagate .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
foreach ($_ENV as $k => $v) putenv("$k=$v");

// Include classes
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
        echo ($i+1) . ". [{$row['authorid']}] {$row['name']}\n";
    }
    echo "Total records: " . count($authors) . "\n";

    // TEST 2: createAuthor()
    echo "\n### TEST 2: createAuthor() ###\n";
    $uniqueName = "Test Author " . time(); // Use timestamp to avoid collisions
    $authorModel->author = $uniqueName;

    // Capture output (JSON) from createAuthor()
    ob_start();                             // Start output buffer
    $ok = $authorModel->createAuthor();     // This function echoes a JSON response
    $out = ob_get_clean();                  // Retrieve and clear buffer

    echo $ok ? "createAuthor() succeeded.\n" : "createAuthor() failed.\n";
    echo "Output: $out\n";

    // Parse returned JSON to get author ID
    $data = json_decode($out, true);
    $createdId = $data['authorid'] ?? null;
    echo $createdId ? "Created ID: $createdId\n" : "No ID returned\n";

    // Halt if creation failed
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
}
catch (Exception $e) {
    // Catch any major failure during test flow
    echo "\n Test error: " . $e->getMessage() . "\n";
}
finally {
    // Cleanup: ensure record is deleted even if test failed mid-way
    if ($createdId) {
        echo "\n### CLEANUP: ensuring delete ###\n";
        $authorModel->id = $createdId;
        $authorModel->deleteAuthor();
        echo "Cleanup delete executed.\n";
    }
    echo "\n--- End of Tests ---\n";
}