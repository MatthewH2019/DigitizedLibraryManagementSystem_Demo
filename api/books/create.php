<?php
// Headers
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Book.php';

$database = new Database();
$db = $database->connect();

// Instantiate a Book Object
$book = new Book($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Check if minimum required data is provided
if(!isset($data->title) || !isset($data->isbn)) {
    echo json_encode(
        array('message' => 'Missing Required Parameters')
    );
    exit();
}

// Set book properties
$book->title = $data->title;
$book->isbn = $data->isbn;

// Optional properties
$book->bookgenre = isset($data->genre) ? $data->genre : '';
$book->publicationyear = isset($data->publication_year) ? $data->publication_year : null;
$book->availability = isset($data->availability) ? $data->availability : 1; // Default to available

// Check for author information - can be IDs or names
if(isset($data->authors) && is_array($data->authors)) {
    // Check if first item is numeric (assuming it's an ID) or string (assuming it's a name)
    if(!empty($data->authors) && is_numeric($data->authors[0])) {
        // Numeric IDs
        $book->authors = $data->authors;
    } else {
        // Author names
        $book->authorNames = $data->authors;
    }
} else if(isset($data->author_names) && is_array($data->author_names)) {
    // Explicit author_names field
    $book->authorNames = $data->author_names;
}

// Create book
if($book->create()) {
    // Book creation response is handled in the model
} else {
    echo json_encode(
        array('message' => 'Book Not Created')
    );
}