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
include_once '../../models/Author.php';

try {
    // Instantiate Database & connect
    $database = new Database();
    $db = $database->connect();
    
    // Instantiate Author object
    $author = new Author($db);
    
    // Get authors
    $result = $author->getAuthors();
    $num = $result->rowCount();
    
    // Check if any authors
    if($num > 0) {
        // Initialize array for results
        $author_arr = array();
        
        // Fetch and process results
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $author_item = array(
                'id' => $authorid,
                'author' => $name
            );
            array_push($author_arr, $author_item);
        }
        
        // Output JSON
        echo json_encode($author_arr);
    } else {
        // No authors found
        echo json_encode(array('message' => 'No Authors found'));
    }
} catch (Exception $e) {
    // Log the error
    error_log('Error in getAll.php: ' . $e->getMessage());
    
    // Return error as JSON
    echo json_encode(array('error' => $e->getMessage()));
}