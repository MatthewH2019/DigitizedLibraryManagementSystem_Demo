<?php
class Books{
    // Data Base Stuff
    private $conn;
    private $table = 'books';

    // Author Properties
    public $id;
    public $author;

    // Constructor with Data Base
    public function __construct($db){
        $this->conn = $db;
    }

    // Get All Books
    public function read_Authors()
    {
       
    }

    // Find 
    public function read_SingleAuthor()
    {
        
    }

    // Create Book
    public function create()
    {
        
    }

    // Update Book
	public function update()
	{
        
    }

    // Delete Book
    public function delete()
    {
        
    }
}