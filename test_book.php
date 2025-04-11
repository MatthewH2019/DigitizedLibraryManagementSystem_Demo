<?php
// test_book.php - A test script for the Book model
// Tests: getBooks(), create(), update(), delete()
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
require_once __DIR__ . '/models/book.php'; // file is named "book.php" (all lowercase)

// Connect to the database
$db = (new Database())->connect();
if (!$db) {
    echo "Connection Error: Could not connect to the database.\n";
    exit;
}

$book = new Book($db);

// Helper function to print JSON output prettily
function printJSON($data) {
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
}

$timestamp = time();
$createdId = null;

try {
    // TEST 1: getBooks()
    echo "### TEST 1: getBooks() ###\n";
    $stmt = $book->getBooks();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $i = 1;
    foreach ($books as $b) {
        echo "$i. [{$b['bookid']}] {$b['title']} ({$b['author_names']})\n";
        $i++;
    }
    echo "Total records: " . count($books) . "\n\n";

    // TEST 2: createBook()
    echo "### TEST 2: createBook() ###\n";
    $book->title = "Test Book $timestamp";
    $book->bookgenre = "Test Genre";
    $book->isbn = "ISBN$timestamp";
    $book->publicationyear = 2024;
    $book->availability = true;
    $book->authorNames = ["Test Author $timestamp"];
    if ($book->create()) {
        echo "createBook() succeeded.\n";
        echo "Output: ";
        printJSON([
            'bookid' => $book->bookid,
            'title' => $book->title,
            'bookgenre' => $book->bookgenre,
            'isbn' => $book->isbn,
            'publicationyear' => $book->publicationyear,
            'availability' => $book->availability,
            'author_ids' => $book->authors,
            'author_names' => $book->authorNames
        ]);
        $createdId = $book->bookid;
        echo "Created ID: {$book->bookid}\n\n";
    } else {
        echo "createBook() failed.\n\n";
        exit;
    }

    // TEST 3: updateBook()
    echo "### TEST 3: updateBook() ###\n";
    $book->title = "Updated Test Book $timestamp";
    $book->bookgenre = "Updated Genre";
    $book->isbn = "ISBN$timestamp";
    $book->publicationyear = 2025;
    $book->availability = false;
    $book->authorNames = ["Updated Test Author $timestamp"];
    if ($book->update()) {
        echo "updateBook() succeeded.\n";
        echo "Output: ";
        printJSON([
            'bookid' => $book->bookid,
            'title' => $book->title
        ]);
        echo "\n";
    } else {
        echo "updateBook() failed.\n\n";
        exit;
    }

    // TEST 4: deleteBook()
    echo "### TEST 4: deleteBook() ###\n";
    if ($book->delete()) {
        echo "deleteBook() succeeded.\n\n";
    } else {
        echo "deleteBook() failed.\n\n";
    }
} catch (Exception $e) {
    echo "\nTest error: " . $e->getMessage() . "\n";
} finally {
    // Final Cleanup for both Authors and Books
    echo "\n### FINAL CLEANUP: calling remove_test_records.php ###\n";
    include_once __DIR__ . '/remove_test_records.php';
    echo "\n--- End of Tests ---\n";
}
?>