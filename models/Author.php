<?php
class Author{
    // Data Base Stuff
    private $conn;
    private $table = 'authors';

    // Author Properties
    public $id;
    public $author;

    // Constructor with Data Base
    public function __construct($db){
        $this->conn = $db;
    }

    // Get All Authors
    public function getAuthors(){
        // Create Query
        $query = 'SELECT a.authorID, a.name FROM ' . $this->table . ' a ORDER BY a.name ASC';

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Execute Query
        $stmt->execute();

        return $stmt;
    }

    // Get Single Author
    public function getSingleAuthor(){
        // Create Query
        $query = 'SELECT a.authorID, a.name FROM ' . $this->table . ' a WHERE a.name = ?';

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
		$stmt->bindParam(1, $this->id);

		// Execute Query
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->author = $row;
    }

    // Create Author
    public function createAuthor(){
        // Place Holder Data
        $temp = $this->author;

        // Create Query
        $query = 'SELECT a.authorID, a.name FROM ' . $this->table . ' a WHERE a.name = ?';

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
		$stmt->bindParam(1, $this->author);

		// Execute Query
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->author = $row;
       
        if($this->author === false){
            // Place Holder Data
            $this->author = $temp;

            // Create Query
            $query = 'INSERT INTO ' . $this->table . ' (name) VALUES (:name)';
            
            // Prepare Statement
            $stmt = $this->conn->prepare($query);

            // Clean Data
            $this->author = htmlspecialchars(strip_tags($this->author));

            // Bind Data
            $stmt->bindParam(':name', $this->author);

            if($stmt->execute()){
                // Place Holder Data
                $this->author = $temp;

                // Create Query
                $query = 'SELECT authors.authorID, authors.name FROM authors WHERE authors.name = ?';

                // Prepare Statement
                $stmt = $this->conn->prepare($query);

                // Bind ID
                $stmt->bindParam(1, $this->author);

                // Execute Query
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $this->author = $row;

                echo(json_encode($this->author));
                return true;
            } else {
                printf("Error: %s.\n", $stmt->error);
                return false;
            }
        } else {
            return false;
        }
    }

    // Update Autor
	public function updateAuthor()
	{
        // Place Holder Data
		$temp = $this->author;

        // Create Query
        $query = 'SELECT a.authorID FROM ' . $this->table . ' a WHERE a.authorID = ?';

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
		$stmt->bindParam(1, $this->id);

		// Execute Query
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->author = $row;
        if($this->author === false){
            echo json_encode(
                array('message' => 'author_id Not found'));
            exit();
        } else {
            // Restore the author name we want to update to
            $this->author = $temp;
            
            // Create Query
            $query = 'UPDATE ' . $this->table . ' SET name = :name WHERE authorID = :authorID';

            // Prepare Statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->author = htmlspecialchars(strip_tags($this->author));
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind data
            $stmt->bindParam(':name', $this->author);
            $stmt->bindParam(':authorID', $this->id);
            
            if($stmt->execute()){
                $this->author = $temp;

                // Create Query
                $query = 'SELECT a.authorID, a.name FROM ' . $this->table . ' a WHERE a.authorID = ?';

                // Prepare Statement
                $stmt = $this->conn->prepare($query);

                // Bind ID
                $stmt->bindParam(1, $this->id);
                
                // Execute Query
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $this->author = $row;

                echo(json_encode($this->author));
                return true;
            } else {
                printf("Error: %s.\n", $stmt->error);
                return false;
            }
        }
    }

    // Delete Author
    public function deleteAuthor()
    {
        // Check which identifier is being used
        if ($this->id) {
            $query = 'SELECT a.authorID FROM ' . $this->table . ' a WHERE a.authorID = ?';
            $param = $this->id;
        } else {
            $query = 'SELECT a.authorID FROM ' . $this->table . ' a WHERE a.name = ?';
            $param = $this->author;
        }

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(1, $param);

        // Execute Query
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no author found, return error
        if($row === false){ 
            echo json_encode(
                array('message' => 'Author not found')
            );
            exit();
        } 
        
        // Store the author ID for deletion
        $author_id = $row['authorid'];
        
        // Start a transaction to ensure all deletions happen or none do
        $this->conn->beginTransaction();
        
        try {
            // First, delete entries from the junction table
            $query = 'DELETE FROM booksauthors WHERE authorid = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $author_id);
            $stmt->execute();
            
            // Now delete the author
            $query = 'DELETE FROM ' . $this->table . ' WHERE authorid = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $author_id);
            $stmt->execute();
            
            // Commit the transaction
            $this->conn->commit();
            
            $array = array(
                'id' => $author_id,
                'message' => 'Author Deleted'
            );
            echo(json_encode($array));
            return true;
        } 
        catch (Exception $e) {
            // Roll back the transaction if something failed
            $this->conn->rollBack();
            echo json_encode(array(
                'message' => 'Error deleting author: ' . $e->getMessage()
            ));
            return false;
        }
    }
}
