<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
if($method === 'OPTIONS'){
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

if($method === "POST") { // POST Request
    require_once 'create.php';
} else if ($method === "GET") { // GET Request
    if(isset($_GET['id']) || isset($_GET['title']) ||
        isset($_GET['author']) || isset($_GET['author_name']) || isset($_GET['genre']) ||
        isset($_GET['isbn']) || isset($_GET['year']) ||
        isset($_GET['available'])) {
        require_once 'get.php';
    } else {
        require_once 'getAll.php';
    }
} else if ($method === "PUT") { // PUT Request
    require_once 'update.php';
} else if ($method === "DELETE") { // DELETE Request
    require_once 'delete.php';
} else {
    echo("Incorrect method being used. It must be either GET, PUT, DELETE, or POST.");
}