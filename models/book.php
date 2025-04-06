<?php
class Books{
    // Data Base Stuff
    private $conn;
    private $table = 'books';

    // Book Properties
    public $bookid;
    public $authorid;
    public $booktitle;
    public $bookgenre;
    public $isbn;
    public $publicationyear;
    public $availability;

    // Constructor with Data Base
    public function __construct($db){
        $this->conn = $db;
    }

    // Get All Books
    public function getBooks()
    {
       
    }

    // Find Books By: Author, Title, Genre, ISBN, Publication Year, or Availabilty
    public function getSpecificBook()
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