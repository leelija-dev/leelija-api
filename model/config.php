<?php
namespace db;

use mysqli; // Ensure mysqli is properly imported within the namespace

class DatabaseConnection {

    private $host;
    private $user;
    private $pass;
    private $db;
    public $conn;

    public function __construct() {
        $this->db_connect();
    }

    private function db_connect() {
        $this->host = 'localhost';
        $this->user = 'root';
        $this->pass = '';
        $this->db   = 'leelija_db';

        $this->conn = new \mysqli($this->host, $this->user, $this->pass, $this->db);

        // Check for connection errors
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        return $this->conn;
    }
}
?>