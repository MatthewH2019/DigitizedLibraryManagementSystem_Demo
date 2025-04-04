<?php
// Headers
include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();

// Instantiate an Author Object
$author = new Author($db);

// Get Author ID
$author->id = isset($_GET['id']) ? $_GET['id'] :  die();

// Get Author
$author->read_SingleAuthor();

if($author->author === false) {
    echo json_encode(array('message' => 'author_id Not Found'));
} else {
    print_r(json_encode($author->author));
}