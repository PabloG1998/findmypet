<?php 
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "findmypet";
    private $conn;

    //Constructor
    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host 
            . ";dbname=" 
            . $this->dbname, 
            $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de Conexion: " . $e->getMessage());
        }
    }

    public function execute($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOExecption $e) {
            die("Error de ejecución: " . $e->getMessage());
        }
    }
    public function lastId() {
        return $this->conn->lastId();
    }

    public function getConnection() {
        return $this->conn;
    }
}

?>