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

    // Get Authors
    public function read_Authors(){
        // Create Query
        $query = 'SELECT a.authorID, a.name FROM ' . $this->table . ' a';

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Execute Query
        $stmt->execute();

        return $stmt;
    }

    // Get Single Author
    public function read_SingleAuthor(){
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
    }

    // Create Author
    public function create(){
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
	public function update()
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
        }
        else{
            // Place Holder Data
            $this->author = $temp;
            
            // Create Query
            $query = 'UPDATE ' . $this->table . ' SET author = :author WHERE id = :id';

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
                $query = 'SELECT authors.id, authors.name FROM authors WHERE authors.name = ?';

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
        }
    }

    // Delete Author
    public function delete()
    {
        // Place Holder Data
        $temp = $this->id;

        // Create Query
        $query = 'SELECT a.authorID FROM ' . $this->table . ' a WHERE a.authorID = ?';

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->id);

        // Execute Query
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row;
        if($this->id === false){ 
            echo json_encode(
                array('message' => 'author_id Not found'));
            exit();
        } else {
            // Place Holder Data
            $this->id = $temp;

            // Create Query
            $query = 'DELETE FROM quotes WHERE author_id = :id';

            // Prepare Statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind data
            $stmt->bindParam(':authorID', $this->id);
        
            if($stmt->execute()){
                // Place Holder Data
                $this->id = $temp;
            
                // Create Query
                $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

                // Prepare Statement
                $stmt = $this->conn->prepare($query);

                // Clean data
                $this->id = htmlspecialchars(strip_tags($this->id));

                // Bind data
                $stmt->bindParam(':authorID', $this->id);
                
                // Execute Query
                if($stmt->execute()){
                    $array = array('id' => $this->id);
                    echo(json_encode($array));
                    return true;
                } else {
                    printf("Error: %s.\n", $stmt->error);
                    return false;
                }
            } else {
                printf("Error: %s.\n", $stmt->error);
                return false;
            }	
        }
    }
}
