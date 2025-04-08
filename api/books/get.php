<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/Database.php';
include_once '../../models/Book.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate book object
$book = new Book($db);


// Set other search parameters if they exist
if(isset($_GET['id'])) {
    $book->bookid = $_GET['id'];
}
if(isset($_GET['title'])) {
    $book->title = $_GET['title'];
}
if(isset($_GET['author'])) {
    if(is_numeric($_GET['author'])) {
        $book->authorid = $_GET['author'];
    } else {
        $book->name = $_GET['author'];
    }
}
if(isset($_GET['genre'])) {
    $book->bookgenre = $_GET['genre'];
}
if(isset($_GET['isbn'])) {
    $book->isbn = $_GET['isbn'];
}
if(isset($_GET['year'])) {
    $book->publicationyear = $_GET['year'];
}
if(isset($_GET['available'])) {
    $book->availability = $_GET['available'];
}

// Get book
$result = $book->getSpecificBook();

// Get row count
$num = $result->rowCount();

if($num > 0) {
    // Book array
    $books_arr = array();
    $books_arr['data'] = array();

    // Fetch and format data
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        $book_item = array(
            'id' => $bookid,
            'title' => $title,
            'genre' => $bookgenre,
            'isbn' => $isbn,
            'publication_year' => $publicationyear,
            'availability' => $availability,
            'authors' => array()
        );
        
        // Process author data
        if($author_ids && $author_names) {
            $ids = explode(',', $author_ids);
            $names = explode(',', $author_names);
            
            for($i = 0; $i < count($ids); $i++) {
                if(isset($ids[$i]) && isset($names[$i])) {
                    $book_item['authors'][] = array(
                        'id' => $ids[$i],
                        'name' => $names[$i]
                    );
                }
            }
        }

        // Push to "data"
        array_push($books_arr['data'], $book_item);
    }

    // Output to JSON format
    echo json_encode($books_arr['data']);
} else {
    // No Books
    echo json_encode(
        array('message' => 'No Books Found')
    );
}