<?php
// Include the database class file (adjust the path if needed)
require_once __DIR__ . '/config/database.php';

// Create an instance of the Database class and attempt a connection
$db = new Database();
$conn = $db->connect();

if ($conn) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed!";
}
