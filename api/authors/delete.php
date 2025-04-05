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

if(isset($data->id)) {
    $author->id = $data->id;
} elseif(isset($data->author)) {
    $author->author = $data->author;
} else {
    echo json_encode(
        array('message' => 'Missing required parameter: id or author name')
    );
    exit();
}

// Delete author
$author->delete();