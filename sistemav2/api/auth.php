<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    $db = new Database();
    
    switch ($action) {
        case 'login':
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            
            if (empty($email) || empty($senha)) {
                throw new Exception('Email e senha são obrigatórios');
            }
            
            $stmt = $db->query("SELECT * FROM usuarios WHERE email = ?", [$email]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($senha, $user['senha'])) {
                throw new Exception('Email ou senha incorretos');
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_type'] = $user['tipo'];
            
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'nome' => $user['nome'],
                    'email' => $user['email'],
                    'tipo' => $user['tipo']
                ]
            ]);
            break;
            
        case 'logout':
            logout();
            break;
            
        case 'check':
            if (isLoggedIn()) {
                echo json_encode([
                    'success' => true,
                    'user' => getCurrentUser()
                ]);
            } else {
                echo json_encode(['success' => false]);
            }
            break;
            
        default:
            throw new Exception('Ação não reconhecida');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>