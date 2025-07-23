<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $db = new Database();
    
    switch ($method) {
        case 'GET':
            if ($action === 'list') {
                $search = $_GET['search'] ?? '';
                $cliente_id = $_GET['cliente_id'] ?? '';
                $tipo = $_GET['tipo'] ?? '';
                $page = (int)($_GET['page'] ?? 1);
                $limit = (int)($_GET['limit'] ?? 10);
                $offset = ($page - 1) * $limit;
                
                $where = [];
                $params = [];
                
                if (!empty($search)) {
                    $where[] = "(e.marca LIKE ? OR e.modelo LIKE ? OR e.numero_serie LIKE ?)";
                    $searchParam = "%$search%";
                    $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
                }
                
                if (!empty($cliente_id)) {
                    $where[] = "e.cliente_id = ?";
                    $params[] = $cliente_id;
                }
                
                if (!empty($tipo)) {
                    $where[] = "e.tipo = ?";
                    $params[] = $tipo;
                }
                
                $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
                
                // Count total
                $countSql = "SELECT COUNT(*) as total FROM equipamentos e 
                           LEFT JOIN clientes c ON e.cliente_id = c.id $whereClause";
                $countStmt = $db->query($countSql, $params);
                $total = $countStmt->fetch()['total'];
                
                // Get data
                $sql = "SELECT e.*, c.nome as cliente_nome 
                       FROM equipamentos e 
                       LEFT JOIN clientes c ON e.cliente_id = c.id 
                       $whereClause 
                       ORDER BY e.criado_em DESC 
                       LIMIT $limit OFFSET $offset";
                $stmt = $db->query($sql, $params);
                $equipamentos = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'data' => $equipamentos,
                    'total' => $total,
                    'page' => $page,
                    'totalPages' => ceil($total / $limit)
                ]);
                
            } elseif ($action === 'get' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $sql = "SELECT e.*, c.nome as cliente_nome 
                       FROM equipamentos e 
                       LEFT JOIN clientes c ON e.cliente_id = c.id 
                       WHERE e.id = ?";
                $stmt = $db->query($sql, [$id]);
                $equipamento = $stmt->fetch();
                
                if (!$equipamento) {
                    throw new Exception('Equipamento não encontrado');
                }
                
                echo json_encode(['success' => true, 'data' => $equipamento]);
            }
            break;
            
        case 'POST':
            if (!hasPermission('write')) {
                throw new Exception('Sem permissão para criar equipamentos');
            }
            // Suporte a JSON
            $data = $_POST;
            if (empty($data)) $data = json_decode(file_get_contents('php://input'), true) ?? [];
            $cliente_id = (int)($data['cliente_id'] ?? 0);
            $tipo_id = (int)($data['tipo_id'] ?? 0);
            $marca_id = (int)($data['marca_id'] ?? 0);
            $modelo_id = (int)($data['modelo_id'] ?? 0);
            $numero_serie = $data['numero_serie'] ?? '';
            $observacoes = $data['observacoes'] ?? '';
            // Buscar nomes
            $tipo = '';
            $marca = '';
            $modelo = '';
            if ($tipo_id) {
                $stmt = $db->query('SELECT nome FROM tipos_equipamento WHERE id = ?', [$tipo_id]);
                $tipo = $stmt->fetch()['nome'] ?? '';
            }
            if ($marca_id) {
                $stmt = $db->query('SELECT nome FROM marcas WHERE id = ?', [$marca_id]);
                $marca = $stmt->fetch()['nome'] ?? '';
            }
            if ($modelo_id) {
                $stmt = $db->query('SELECT nome FROM modelos WHERE id = ?', [$modelo_id]);
                $modelo = $stmt->fetch()['nome'] ?? '';
            }
            if (empty($cliente_id) || empty($tipo) || empty($marca) || empty($modelo)) {
                throw new Exception('Cliente, tipo, marca e modelo são obrigatórios');
            }
            $sql = "INSERT INTO equipamentos (cliente_id, tipo, marca, modelo, numero_serie, observacoes) 
                   VALUES (?, ?, ?, ?, ?, ?)";
            $db->query($sql, [$cliente_id, $tipo, $marca, $modelo, $numero_serie, $observacoes]);
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            break;
            
        case 'PUT':
            if (!hasPermission('write')) {
                throw new Exception('Sem permissão para editar equipamentos');
            }
            // Suporte a JSON e FormData
            $data = [];
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (stripos($contentType, 'application/json') !== false) {
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
            } else {
                parse_str(file_get_contents('php://input'), $data);
            }
            $id = (int)($data['id'] ?? 0);
            $cliente_id = (int)($data['cliente_id'] ?? 0);
            $tipo_id = (int)($data['tipo_id'] ?? 0);
            $marca_id = (int)($data['marca_id'] ?? 0);
            $modelo_id = (int)($data['modelo_id'] ?? 0);
            $numero_serie = $data['numero_serie'] ?? '';
            $observacoes = $data['observacoes'] ?? '';
            // Buscar nomes
            $tipo = '';
            $marca = '';
            $modelo = '';
            if ($tipo_id) {
                $stmt = $db->query('SELECT nome FROM tipos_equipamento WHERE id = ?', [$tipo_id]);
                $tipo = $stmt->fetch()['nome'] ?? '';
            }
            if ($marca_id) {
                $stmt = $db->query('SELECT nome FROM marcas WHERE id = ?', [$marca_id]);
                $marca = $stmt->fetch()['nome'] ?? '';
            }
            if ($modelo_id) {
                $stmt = $db->query('SELECT nome FROM modelos WHERE id = ?', [$modelo_id]);
                $modelo = $stmt->fetch()['nome'] ?? '';
            }
            if (empty($id) || empty($cliente_id) || empty($tipo) || empty($marca) || empty($modelo)) {
                throw new Exception('ID, cliente, tipo, marca e modelo são obrigatórios');
            }
            $sql = "UPDATE equipamentos SET cliente_id = ?, tipo = ?, marca = ?, modelo = ?, 
                   numero_serie = ?, observacoes = ? WHERE id = ?";
            $db->query($sql, [$cliente_id, $tipo, $marca, $modelo, $numero_serie, $observacoes, $id]);
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            if (!hasPermission('delete')) {
                throw new Exception('Sem permissão para excluir equipamentos');
            }
            
            $id = (int)($_GET['id'] ?? 0);
            
            if (empty($id)) {
                throw new Exception('ID é obrigatório');
            }
            
            // Check if equipment has service orders
            $stmt = $db->query("SELECT COUNT(*) as count FROM ordens_servico WHERE equipamento_id = ?", [$id]);
            if ($stmt->fetch()['count'] > 0) {
                throw new Exception('Não é possível excluir equipamento com ordens de serviço');
            }
            
            $db->query("DELETE FROM equipamentos WHERE id = ?", [$id]);
            
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