<?php
class Book{
    // Data Base Stuff
    private $conn;
    private $table = 'books';

    // Book Properties
    public $bookid;
    public $authorid;
    public $name;
    public $title;
    public $bookgenre;
    public $isbn;
    public $publicationyear;
    public $availability;

    // Constructor with Data Base
    public function __construct($db){
        $this->conn = $db;
    }

    // Additional properties for joining with authors
    public $authors = [];
    public $authorNames = [];

    // Get All Books
    public function getBooks()
    {
        // Create query with join to get author information
        $query = 'SELECT b.bookid, b.title, b.bookgenre, b.isbn, b.publicationyear, b.availability,
            string_agg(a.authorID::text, \',\') as author_ids, string_agg(a.name, \',\') as author_names
            FROM ' . $this->table . ' b
            LEFT JOIN booksauthors ba ON b.bookid = ba.bookid
            LEFT JOIN authors a ON ba.authorid = a.authorID
            GROUP BY b.bookid, b.title, b.bookgenre, b.isbn, b.publicationyear, b.availability
            ORDER BY b.title ASC';
                 
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }

    // Find Books By: Author, Title, Genre, ISBN, Publication Year, or Availability
    public function getSpecificBook()
    {
        // If no search criteria provided, return false
        if(!$this->bookid && !$this->authorid && !$this->name && !$this->title && 
        !$this->bookgenre && !$this->isbn && 
        !$this->publicationyear && $this->availability === null) {
        return false;
        }
        
        // Build the base query
        $query = 'SELECT b.bookid, b.title, b.bookgenre, b.isbn, b.publicationyear, b.availability,
                string_agg(a.authorID::text, \',\') as author_ids, string_agg(a.name, \',\') as author_names
                FROM ' . $this->table . ' b
                LEFT JOIN booksauthors ba ON b.bookid = ba.bookid
                LEFT JOIN authors a ON ba.authorid = a.authorID';
        
        $conditions = [];
        $params = [];
        
        // Add conditions based on which properties are set
        if($this->bookid) {
            $conditions[] = 'b.bookid = ?';
            $params[] = $this->bookid;
        }
        
        if($this->authorid) {
            $conditions[] = 'ba.authorid = ?';
            $params[] = $this->authorid;
        }
        
        // Add author name search
        if($this->name) {
            $conditions[] = 'a.name LIKE ?';
            $params[] = '%' . $this->name . '%';
        }
        
        if($this->title) {
            $conditions[] = 'b.title LIKE ?';
            $params[] = '%' . $this->title . '%';
        }
        
        if($this->bookgenre) {
            $conditions[] = 'b.bookgenre LIKE ?';
            $params[] = '%' . $this->bookgenre . '%';
        }
        
        if($this->isbn) {
            $conditions[] = 'b.isbn = ?';
            $params[] = $this->isbn;
        }
        
        if($this->publicationyear) {
            $conditions[] = 'b.publicationyear = ?';
            $params[] = $this->publicationyear;
        }
        
        if($this->availability !== null) {
            $conditions[] = 'b.availability = ?';
            $params[] = $this->availability;
        }
        
        // Add WHERE clause if we have conditions
        if(!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }
        
        // Complete the query with GROUP BY and ORDER BY
        $query .= ' GROUP BY b.bookid, b.title, b.bookgenre, b.isbn, b.publicationyear, b.availability ORDER BY b.title ASC';
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters if we have any
        for($i = 0; $i < count($params); $i++) {
            $stmt->bindParam($i + 1, $params[$i]);
        }
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }

    // Create Book
    public function create()
    {
        // First check if book with same ISBN already exists (unchanged)
        $query = 'SELECT bookid FROM ' . $this->table . ' WHERE isbn = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->isbn);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            echo json_encode(array('message' => 'Book with this ISBN already exists'));
            return false;
        }
        
        // Create book (unchanged)
        $query = 'INSERT INTO ' . $this->table . ' 
                  (title, bookgenre, isbn, publicationyear, availability)
                  VALUES (:title, :bookgenre, :isbn, :publicationyear, :availability)';
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->bookgenre = htmlspecialchars(strip_tags($this->bookgenre));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->publicationyear = htmlspecialchars(strip_tags($this->publicationyear));
        $this->availability = htmlspecialchars(strip_tags($this->availability));
        
        // Bind data
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':bookgenre', $this->bookgenre);
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->bindParam(':publicationyear', $this->publicationyear);
        $stmt->bindParam(':availability', $this->availability);
        
        // Execute query
        if($stmt->execute()) {
            // Get the inserted book's ID
            $this->bookid = $this->conn->lastInsertId();
            
            // If author IDs are provided, use them directly
            if(!empty($this->authors)) {
                $success = $this->linkAuthors($this->authors);
                if(!$success) {
                    return false;
                }
            }
            // If author names are provided, look up or create authors
            else if(!empty($this->authorNames)) {
                $authorIds = $this->getOrCreateAuthors($this->authorNames);
                if(!empty($authorIds)) {
                    $success = $this->linkAuthors($authorIds);
                    if(!$success) {
                        return false;
                    }
                }
            }
            
            // Get the complete book data with authors to return (unchanged)
            $query = 'SELECT b.bookid, b.title, b.bookgenre, b.isbn, b.publicationyear, b.availability,
                     string_agg(a.authorID::text, \',\') as author_ids, string_agg(a.name, \',\') as author_names
                     FROM ' . $this->table . ' b
                     LEFT JOIN booksauthors ba ON b.bookid = ba.bookid
                     LEFT JOIN authors a ON ba.authorid = a.authorID
                     WHERE b.bookid = ?
                     GROUP BY b.bookid, b.title, b.bookgenre, b.isbn, b.publicationyear, b.availability';
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->bookid);
            $stmt->execute();
            
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($book);
            
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
    
    // New method to look up authors by name or create them if they don't exist
    private function getOrCreateAuthors($authorNames) {
        $authorIds = [];
        
        foreach($authorNames as $authorName) {
            // Clean the name
            $authorName = htmlspecialchars(strip_tags($authorName));
            
            // First, check if author already exists
            $query = 'SELECT authorID FROM authors WHERE name = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $authorName);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                // Author exists, get the ID
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $authorIds[] = $row['authorid'];
            } else {
                // Author doesn't exist, create new author
                $query = 'INSERT INTO authors (name) VALUES (?)';
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $authorName);
                
                if($stmt->execute()) {
                    // Get the new author ID
                    $authorIds[] = $this->conn->lastInsertId();
                } else {
                    echo json_encode(array('message' => 'Failed to create author: ' . $authorName));
                    return [];
                }
            }
        }
        
        return $authorIds;
    }

    // Update Book
    public function update()
    {
        // First check if book exists
        $query = 'SELECT bookid FROM ' . $this->table . ' WHERE bookid = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->bookid);
        $stmt->execute();
        
        if($stmt->rowCount() == 0) {
            echo json_encode(array('message' => 'Book ID not found'));
            return false;
        }
        
        // Start building update query - only update fields that are provided
        $query = 'UPDATE ' . $this->table . ' SET ';
        $updates = [];
        
        if($this->title) {
            $updates[] = 'title = :title';
            $this->title = htmlspecialchars(strip_tags($this->title));
        }
        
        if($this->bookgenre) {
            $updates[] = 'bookgenre = :bookgenre';
            $this->bookgenre = htmlspecialchars(strip_tags($this->bookgenre));
        }
        
        if($this->isbn) {
            $updates[] = 'isbn = :isbn';
            $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        }
        
        if($this->publicationyear) {
            $updates[] = 'publicationyear = :publicationyear';
            $this->publicationyear = htmlspecialchars(strip_tags($this->publicationyear));
        }
        
        if($this->availability !== null) {
            $updates[] = 'availability = :availability';
            if (is_bool($this->availability)) {
                // Convert boolean to string acceptable for PostgreSQL (e.g., 'true' or 'false')
                $this->availability = $this->availability ? 'true' : 'false';
            } else {
                $this->availability = htmlspecialchars(strip_tags($this->availability));
            }
        }
        
        // If no updates were provided
        if(empty($updates)) {
            echo json_encode(array('message' => 'No fields to update'));
            return false;
        }
        
        // Complete the query
        $query .= implode(', ', $updates) . ' WHERE bookid = :bookid';
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind bookid
        $stmt->bindParam(':bookid', $this->bookid);
        
        // Bind other parameters if they were set
        if($this->title) {
            $stmt->bindParam(':title', $this->title);
        }
        
        if($this->bookgenre) {
            $stmt->bindParam(':bookgenre', $this->bookgenre);
        }
        
        if($this->isbn) {
            $stmt->bindParam(':isbn', $this->isbn);
        }
        
        if($this->publicationyear) {
            $stmt->bindParam(':publicationyear', $this->publicationyear);
        }
        
        if($this->availability !== null) {
            $stmt->bindParam(':availability', $this->availability);
        }
        
        // Execute query
        if($stmt->execute()) {
            // First delete existing relationships
            $query = 'DELETE FROM booksauthors WHERE bookid = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->bookid);
            $stmt->execute();
            
            // If author IDs are provided, use them directly
            if(!empty($this->authors)) {
                $success = $this->linkAuthors($this->authors);
                if(!$success) {
                    return false;
                }
            }
            // If author names are provided, look up or create authors
            else if(!empty($this->authorNames)) {
                $authorIds = $this->getOrCreateAuthors($this->authorNames);
                if(!empty($authorIds)) {
                    $success = $this->linkAuthors($authorIds);
                    if(!$success) {
                        return false;
                    }
                }
            }
            
            // Get the updated book data with authors to return
            $query = 'SELECT b.bookid, b.title, b.bookgenre, b.isbn, b.publicationyear, b.availability,
                     string_agg(a.authorID::text, \',\') as author_ids, string_agg(a.name, \',\') as author_names
                     FROM ' . $this->table . ' b
                     LEFT JOIN booksauthors ba ON b.bookid = ba.bookid
                     LEFT JOIN authors a ON ba.authorid = a.authorID
                     WHERE b.bookid = ?
                     GROUP BY b.bookid, b.title, b.bookgenre, b.isbn, b.publicationyear, b.availability';
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->bookid);
            $stmt->execute();
            
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($book);
            
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Delete Book
    public function delete()
    {
        // Check if book exists
        $query = 'SELECT bookid FROM ' . $this->table . ' WHERE bookid = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->bookid);
        $stmt->execute();
        
        if($stmt->rowCount() == 0) {
            echo json_encode(array('message' => 'Book ID not found'));
            return false;
        }
        
        // First delete related entries in junction table
        $query = 'DELETE FROM booksauthors WHERE bookid = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->bookid);
        
        if(!$stmt->execute()) {
            printf("Error deleting book-author relationships: %s.\n", $stmt->error);
            return false;
        }
        
        // Now delete the book
        $query = 'DELETE FROM ' . $this->table . ' WHERE bookid = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->bookid);
        
        if($stmt->execute()) {
            echo json_encode(array(
                'id' => $this->bookid,
                'message' => 'Book Deleted'
            ));
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
    
    // Helper method to link book with authors
    private function linkAuthors($authorIds)
    {
        foreach($authorIds as $authorId) {
            // First verify the author exists
            $query = 'SELECT authorID FROM authors WHERE authorID = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $authorId);
            $stmt->execute();
            
            if($stmt->rowCount() == 0) {
                echo json_encode(array('message' => 'Author ID ' . $authorId . ' not found'));
                return false;
            }
            
            // Create relationship in junction table
            $query = 'INSERT INTO booksauthors (bookid, authorid) VALUES (?, ?)';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->bookid);
            $stmt->bindParam(2, $authorId);
            
            if(!$stmt->execute()) {
                printf("Error linking book to author: %s.\n", $stmt->error);
                return false;
            }
        }
        
        return true;
    }
}