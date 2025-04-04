<?php
// Headers
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();

// Instantiate an Author Object
$author = new Author($db);

// Get Raw Author Data
$data = json_decode(file_get_contents("php://input"));

if(!get_object_vars($data) || !isset($data->id)) {
    echo json_encode(array('message' => 'Missing Required Parameters'));
} else {
    $author->id = $data->id;

    // Delete Author
    if(!$author->delete()) {
        echo json_encode(array('message' => 'Author Not Deleted'));
    }
}