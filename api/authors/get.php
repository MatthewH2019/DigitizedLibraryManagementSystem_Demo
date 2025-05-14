<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();

// Instantiate an Author Object
$author = new Author($db);

// Get Author ID
$author->id = isset($_GET['id']) ? $_GET['id'] :  die();

// Get Author
$author->getSingleAuthor();

if($author->author === false) {
    echo json_encode(array('message' => 'author_id Not Found'));
} else {
    print_r(json_encode($author->author));
}