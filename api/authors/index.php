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
    include_once 'create.php';
} else if ($method === "GET") { // GET Request
    if(isset($_GET['id'])) {
        include_once 'read_single.php';
    } else {
        include_once 'read.php';
    }
} else if ($method === "PUT") { // PUT Request
    include_once 'update.php';
} else if ($method === "DELETE") { // DELETE Request
    include_once 'delete.php';
} else {
    echo("Incorrect method being used. It must be either GET, PUT, DELETE, or POST.");
}