<?php
include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();

// Instantiate an Author Object
$author = new Author($db);

$result = $author->read_authors();
$num = $result->rowCount();

// Check And Read Authors
if($num > 0) {
	$author_arr = array();
	$author_arr['data'] = array();
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		extract($row);

		$author_item = array('id' => $id, 'author' => $author);

		array_push($author_arr['data'], $author_item);
	}
	echo json_encode($author_arr['data']);
} else {
	echo json_encode(array('message' => 'No Authors found'));
}