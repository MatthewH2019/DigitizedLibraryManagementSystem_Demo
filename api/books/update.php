<?php
// Headers
header('Access-Control-Allow-Methods: PUT');
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

// Book id is required
if(!isset($data->bookid)) {
    echo json_encode(
        array('message' => 'Missing Required Parameters (Book ID)')
    );
    exit();
}

// Set ID property
$book->bookid = $data->bookid;

// Optional properties - only set if provided
if(isset($data->title)) {
    $book->title = $data->title;
}
if(isset($data->genre)) {
    $book->bookgenre = $data->genre;
}
if(isset($data->isbn)) {
    $book->isbn = $data->isbn;
}
if(isset($data->publication_year)) {
    $book->publicationyear = $data->publication_year;
}
if(isset($data->availability)) {
    $book->availability = $data->availability;
}

// Authors handling
// If numeric IDs are provided, use them directly
if(isset($data->authors) && is_array($data->authors) && !empty($data->authors)) {
    // Check if the first element seems to be a numeric ID
    if(is_numeric($data->authors[0])) {
        $book->authors = $data->authors;
    } 
    // Otherwise, treat them as author names
    else {
        $book->authorNames = $data->authors;
    }
}

// Update book
$book->update();