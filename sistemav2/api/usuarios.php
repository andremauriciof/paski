<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $db = new Database();

    // ENDPOINTS DE PERSONALIZAÇÃO DE TEMA E CORES (devem vir antes dos blocos de usuário)
    if ($method === 'GET' && $action === 'get_tema' && isset($_GET['usuario_id'])) {
        $uid = intval($_GET['usuario_id']);
        $stmt = $db->query("SELECT tema FROM usuarios_tema WHERE usuario_id = ?", [$uid]);
        $tema = $stmt->fetchColumn();
        echo json_encode(['success' => true, 'tema' => $tema ?: 'light']);
        exit;
    }
    if ($method === 'POST' && $action === 'set_tema' && isset($_POST['usuario_id'], $_POST['tema'])) {
        $uid = intval($_POST['usuario_id']);
        $tema = $_POST['tema'];
        $stmt = $db->query("INSERT INTO usuarios_tema (usuario_id, tema) VALUES (?, ?) ON DUPLICATE KEY UPDATE tema = VALUES(tema)", [$uid, $tema]);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($method === 'GET' && $action === 'get_cores' && isset($_GET['usuario_id'])) {
        $uid = intval($_GET['usuario_id']);
        $stmt = $db->query("SELECT cor_primaria, cor_secundaria, cor_fundo, cor_menu, cor_card, cor_botao_primario, cor_botao_secundario, cor_footer, cor_header, cor_borda, cor_sucesso, cor_erro, cor_aviso, cor_hover_menu, cor_sidebar, cor_texto FROM usuarios_cores WHERE usuario_id = ?", [$uid]);
        $cores = $stmt->fetch();
        if (!$cores) {
            $cores = [
                'cor_primaria' => '#0055c7',
                'cor_secundaria' => '#4f8cff',
                'cor_fundo' => '#f5f5f5',
                'cor_menu' => '#fff',
                'cor_card' => '#fff',
                'cor_botao_primario' => '#0055c7',
                'cor_botao_secundario' => '#6c757d',
                'cor_footer' => '#fff',
                'cor_header' => '#fff',
                'cor_borda' => '#e0e0e0',
                'cor_sucesso' => '#43aa8b',
                'cor_erro' => '#f44336',
                'cor_aviso' => '#ffb300',
                'cor_hover_menu' => '#eaf1fb',
                'cor_sidebar' => '#fff',
                'cor_texto' => '#222'
            ];
        }
        echo json_encode(['success' => true, 'cores' => $cores]);
        exit;
    }
    if ($method === 'POST' && $action === 'set_cores' && isset($_POST['usuario_id'])) {
        $uid = intval($_POST['usuario_id']);
        $fields = [
            'cor_primaria', 'cor_secundaria', 'cor_fundo', 'cor_menu', 'cor_card', 'cor_botao_primario', 'cor_botao_secundario',
            'cor_footer', 'cor_header', 'cor_borda', 'cor_sucesso', 'cor_erro', 'cor_aviso', 'cor_hover_menu', 'cor_sidebar', 'cor_texto'
        ];
        $values = [];
        foreach ($fields as $f) {
            $values[$f] = $_POST[$f] ?? '';
        }
        $db->query(
            "INSERT INTO usuarios_cores (usuario_id, cor_primaria, cor_secundaria, cor_fundo, cor_menu, cor_card, cor_botao_primario, cor_botao_secundario, cor_footer, cor_header, cor_borda, cor_sucesso, cor_erro, cor_aviso, cor_hover_menu, cor_sidebar, cor_texto)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                cor_primaria=VALUES(cor_primaria), cor_secundaria=VALUES(cor_secundaria), cor_fundo=VALUES(cor_fundo), cor_menu=VALUES(cor_menu),
                cor_card=VALUES(cor_card), cor_botao_primario=VALUES(cor_botao_primario), cor_botao_secundario=VALUES(cor_botao_secundario),
                cor_footer=VALUES(cor_footer), cor_header=VALUES(cor_header), cor_borda=VALUES(cor_borda), cor_sucesso=VALUES(cor_sucesso),
                cor_erro=VALUES(cor_erro), cor_aviso=VALUES(cor_aviso), cor_hover_menu=VALUES(cor_hover_menu), cor_sidebar=VALUES(cor_sidebar), cor_texto=VALUES(cor_texto)",
            array_merge([$uid], array_values($values))
        );
        echo json_encode(['success' => true]);
        exit;
    }

    switch ($method) {
        case 'GET':
            if ($action === 'list') {
                $currentUser = getCurrentUser();
                if ($currentUser['tipo'] === 'admin') {
                    // Admin pode listar todos
                    $search = $_GET['search'] ?? '';
                    $tipo = $_GET['tipo'] ?? '';
                    $page = (int)($_GET['page'] ?? 1);
                    $limit = (int)($_GET['limit'] ?? 10);
                    $offset = ($page - 1) * $limit;
                    $where = [];
                    $params = [];
                    if (!empty($search)) {
                        $where[] = "(nome LIKE ? OR email LIKE ?)";
                        $searchParam = "%$search%";
                        $params = array_merge($params, [$searchParam, $searchParam]);
                    }
                    if (!empty($tipo)) {
                        $where[] = "tipo = ?";
                        $params[] = $tipo;
                    }
                    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
                    // Count total
                    $countStmt = $db->query("SELECT COUNT(*) as total FROM usuarios $whereClause", $params);
                    $total = $countStmt->fetch()['total'];
                    // Get data (exclude password)
                    $sql = "SELECT id, nome, email, tipo, criado_em FROM usuarios $whereClause ORDER BY nome LIMIT $limit OFFSET $offset";
                    $stmt = $db->query($sql, $params);
                    $usuarios = $stmt->fetchAll();
                    echo json_encode([
                        'success' => true,
                        'data' => $usuarios,
                        'total' => $total,
                        'page' => $page,
                        'totalPages' => ceil($total / $limit)
                    ]);
                } else if ($currentUser['tipo'] === 'tecnico') {
                    // Técnico pode listar apenas técnicos e admins
                    $stmt = $db->query("SELECT id, nome, email, tipo, criado_em FROM usuarios WHERE tipo IN ('admin', 'tecnico') ORDER BY nome");
                    $usuarios = $stmt->fetchAll();
                    echo json_encode([
                        'success' => true,
                        'data' => $usuarios
                    ]);
                } else {
                    // Consulta não pode listar
                    echo json_encode(['success' => false, 'error' => 'Sem permissão para listar usuários']);
                }
            } elseif ($action === 'get' && isset($_GET['id'])) {
                if (!hasPermission('manage')) {
                    throw new Exception('Sem permissão para visualizar usuários');
                }
                
                $id = (int)$_GET['id'];
                $stmt = $db->query("SELECT id, nome, email, tipo, criado_em FROM usuarios WHERE id = ?", [$id]);
                $usuario = $stmt->fetch();
                
                if (!$usuario) {
                    throw new Exception('Usuário não encontrado');
                }
                
                echo json_encode(['success' => true, 'data' => $usuario]);
                
            } elseif ($action === 'tecnicos') {
                // Get technicians for dropdowns
                $stmt = $db->query("SELECT id, nome FROM usuarios WHERE tipo IN ('admin', 'tecnico') ORDER BY nome");
                $tecnicos = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $tecnicos]);
            }
            break;
            
        case 'POST':
            if (!hasPermission('manage')) {
                throw new Exception('Sem permissão para criar usuários');
            }
            
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $tipo = $_POST['tipo'] ?? '';
            
            if (empty($nome) || empty($email) || empty($senha) || empty($tipo)) {
                throw new Exception('Todos os campos são obrigatórios');
            }
            
            if (!in_array($tipo, ['admin', 'tecnico', 'consulta'])) {
                throw new Exception('Tipo de usuário inválido');
            }
            
            // Check if email already exists
            $stmt = $db->query("SELECT COUNT(*) as count FROM usuarios WHERE email = ?", [$email]);
            if ($stmt->fetch()['count'] > 0) {
                throw new Exception('Este email já está em uso');
            }
            
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";
            $db->query($sql, [$nome, $email, $senhaHash, $tipo]);
            
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            break;
            
        case 'PUT':
            if (!hasPermission('manage')) {
                throw new Exception('Sem permissão para editar usuários');
            }
            
            parse_str(file_get_contents("php://input"), $_PUT);
            
            $id = (int)($_PUT['id'] ?? 0);
            $nome = $_PUT['nome'] ?? '';
            $email = $_PUT['email'] ?? '';
            $senha = $_PUT['senha'] ?? '';
            $tipo = $_PUT['tipo'] ?? '';
            
            if (empty($id) || empty($nome) || empty($email) || empty($tipo)) {
                throw new Exception('ID, nome, email e tipo são obrigatórios');
            }
            
            if (!in_array($tipo, ['admin', 'tecnico', 'consulta'])) {
                throw new Exception('Tipo de usuário inválido');
            }
            
            // Check if email already exists for other users
            $stmt = $db->query("SELECT COUNT(*) as count FROM usuarios WHERE email = ? AND id != ?", [$email, $id]);
            if ($stmt->fetch()['count'] > 0) {
                throw new Exception('Este email já está em uso por outro usuário');
            }
            
            if (!empty($senha)) {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nome = ?, email = ?, senha = ?, tipo = ? WHERE id = ?";
                $params = [$nome, $email, $senhaHash, $tipo, $id];
            } else {
                $sql = "UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id = ?";
                $params = [$nome, $email, $tipo, $id];
            }
            
            $db->query($sql, $params);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            if (!hasPermission('manage')) {
                throw new Exception('Sem permissão para excluir usuários');
            }
            
            $id = (int)($_GET['id'] ?? 0);
            $currentUser = getCurrentUser();
            
            if (empty($id)) {
                throw new Exception('ID é obrigatório');
            }
            
            if ($id == $currentUser['id']) {
                throw new Exception('Você não pode excluir seu próprio usuário');
            }
            
            // Check if user has service orders
            $stmt = $db->query("SELECT COUNT(*) as count FROM ordens_servico WHERE tecnico_id = ?", [$id]);
            if ($stmt->fetch()['count'] > 0) {
                throw new Exception('Não é possível excluir usuário com ordens de serviço vinculadas');
            }
            
            $db->query("DELETE FROM usuarios WHERE id = ?", [$id]);
            
            echo json_encode(['success' => true]);
            break;
            
        default:
            throw new Exception('Método não permitido');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>