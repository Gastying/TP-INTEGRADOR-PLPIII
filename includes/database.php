<?php
class Database {
    private $host = "localhost";
    private $db_name = "essence_shop";
    private $username = "root";
    private $password = "";  // Por defecto en XAMPP está vacío
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", 
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
            // En producción, deberías loggear este error en lugar de mostrarlo
        }
        
        return $this->conn;
    }
}
?>