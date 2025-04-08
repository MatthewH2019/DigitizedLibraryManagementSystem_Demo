<?php
require_once __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->load();

class Database {
    private $conn;
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;

    public function __construct(){
        $this->username = trim($_ENV['USERNAME'] ?? '');
        $this->password = trim($_ENV['PASSWORD'] ?? '');
        $this->dbname   = trim($_ENV['DBNAME']   ?? '');
        $this->host     = trim($_ENV['HOST']     ?? '');
        $this->port     = trim($_ENV['PORT']     ?? '5432');
    }

    public function connect(){
        if ($this->conn) {
            return $this->conn;
        }
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
        try {
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            // Only output errors if you really need to debug
            // echo 'Connection Error: ' . $e->getMessage();
            return null;
        }
    }
}