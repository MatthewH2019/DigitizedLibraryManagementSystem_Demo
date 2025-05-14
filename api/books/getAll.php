<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly, but log them

// Set proper headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Clear any existing output buffer to prevent stray characters
if (ob_get_length()) ob_clean();

include_once '../../config/Database.php';
include_once '../../models/Book.php';

try {
    // Instantiate Database & connect
    $database = new Database();
    $db = $database->connect();
    
    // Instantiate Book object
    $book = new Book($db);
    
    // Get books
    $result = $book->getBooks();
    $num = $result->rowCount();
    
    // Check if any books
    if($num > 0) {
        // Book array
        $books_arr = array();
        
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
            if(isset($author_ids) && isset($author_names)) {
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
            
            // Add book to array
            array_push($books_arr, $book_item);
        }
        
        // Output JSON
        echo json_encode($books_arr);
    } else {
        // No Books
        echo json_encode(array('message' => 'No Books Found'));
    }
} catch (Exception $e) {
    // Log the error
    error_log('Error in books/getAll.php: ' . $e->getMessage());
    
    // Return error as JSON
    echo json_encode(array('error' => $e->getMessage()));
}