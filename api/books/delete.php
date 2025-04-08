<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');
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

// Set ID
if(!isset($data->id)) {
    echo json_encode(
        array('message' => 'Missing Required Parameter: Book ID')
    );
    exit();
}

$book->bookid = $data->id;

// Delete book
$book->delete();