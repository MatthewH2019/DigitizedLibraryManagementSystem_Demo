<?php
include_once '../../config/Database.php';
include_once '../../models/Book.php';

$database = new Database();
$db = $database->connect();

// Instantiate book object
$book = new Book($db);

// Book query
$result = $book->getBooks();

// Get row count
$num = $result->rowCount();

// Check if any books
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