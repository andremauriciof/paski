<?php
// Arquivo para testar a conexão com o banco
require_once 'config/database.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    
    if ($db->testConnection()) {
        echo json_encode([
            'success' => true,
            'message' => 'Conexão com o banco de dados estabelecida com sucesso!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Falha ao testar a conexão com o banco de dados.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'details' => [
            'host' => DB_HOST,
            'port' => DB_PORT,
            'database' => DB_NAME,
            'user' => DB_USER
        ]
    ]);
}
?>