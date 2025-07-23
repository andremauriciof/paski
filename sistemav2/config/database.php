<?php
// Configuração do banco de dados
define('DB_HOST', 'paski_db.mysql.dbaas.com.br');
define('DB_PORT', '3306');
define('DB_NAME', 'paski_db');
define('DB_USER', 'paski_db');
define('DB_PASS', 'Paski@2018');

class Database {
    private $connection;
    
    public function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Erro na conexão com o banco de dados. Verifique as configurações.");
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Exibe o erro real para debug
            die("Erro SQL: " . $e->getMessage() . "<br>Query: " . $sql . "<br>Params: " . print_r($params, true));
            // error_log("Database query error: " . $e->getMessage());
            // throw new Exception("Erro na consulta ao banco de dados.");
        }
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    public function testConnection() {
        try {
            $stmt = $this->connection->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>