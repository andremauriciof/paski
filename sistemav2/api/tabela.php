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
                $marca = $_GET['marca'] ?? '';
                $modelo = $_GET['modelo'] ?? '';
                $fornecedor = $_GET['fornecedor'] ?? '';
                $page = (int)($_GET['page'] ?? 0); // 0 = sem paginação
                $limit = (int)($_GET['limit'] ?? 0); // 0 = sem limite
                
                $where = [];
                $params = [];
                
                if (!empty($search)) {
                    $where[] = "(Fornecedor LIKE ? OR Marca LIKE ? OR Modelo LIKE ? OR Data LIKE ? OR Custo LIKE ? OR MaoDeObra LIKE ? OR ValorTotal LIKE ?)";
                    $searchParam = "%$search%";
                    $params = array_merge($params, [
                        $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam
                    ]);
                }
                if (!empty($marca)) {
                    $where[] = "Marca = ?";
                    $params[] = $marca;
                }
                if (!empty($modelo)) {
                    $where[] = "Modelo = ?";
                    $params[] = $modelo;
                }
                if (!empty($fornecedor)) {
                    $where[] = "Fornecedor LIKE ?";
                    $params[] = "%$fornecedor%";
                }
                $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
                
                // Se não especificou paginação, retornar todos os dados
                if ($page === 0) {
                    $sql = "SELECT * FROM tabelapreco $whereClause ORDER BY Data DESC, Marca, Modelo";
                    if ($limit > 0) {
                        $sql .= " LIMIT $limit";
                    }
                    $stmt = $db->query($sql, $params);
                    $telas = $stmt->fetchAll();
                    echo json_encode([
                        'success' => true,
                        'data' => $telas,
                        'total' => count($telas)
                    ]);
                } else {
                    // Paginação no servidor
                    $offset = ($page - 1) * $limit;
                    // Count total
                    $countStmt = $db->query("SELECT COUNT(*) as total FROM tabelapreco $whereClause", $params);
                    $total = $countStmt->fetch()['total'];
                    // Get data
                    $sql = "SELECT * FROM tabelapreco $whereClause ORDER BY Data DESC, Marca, Modelo LIMIT $limit OFFSET $offset";
                    $stmt = $db->query($sql, $params);
                    $telas = $stmt->fetchAll();
                    echo json_encode([
                        'success' => true,
                        'data' => $telas,
                        'total' => $total,
                        'page' => $page,
                        'totalPages' => ceil($total / $limit)
                    ]);
                }
            } elseif ($action === 'get' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $stmt = $db->query("SELECT * FROM tabelapreco WHERE ID = ?", [$id]);
                $tela = $stmt->fetch();
                if (!$tela) {
                    throw new Exception('Tela não encontrada');
                }
                echo json_encode(['success' => true, 'data' => $tela]);
            } elseif ($action === 'marcas') {
                $stmt = $db->query("SELECT DISTINCT Marca FROM tabelapreco ORDER BY Marca");
                $marcas = array_map(function($row) { return $row['Marca']; }, $stmt->fetchAll());
                echo json_encode(['success' => true, 'data' => $marcas]);
            } elseif ($action === 'modelos') {
                $marca = $_GET['marca'] ?? '';
                if (!empty($marca)) {
                    $stmt = $db->query("SELECT DISTINCT Modelo FROM tabelapreco WHERE Marca = ? ORDER BY Modelo", [$marca]);
                } else {
                    $stmt = $db->query("SELECT DISTINCT Modelo FROM tabelapreco ORDER BY Modelo");
                }
                $modelos = array_map(function($row) { return $row['Modelo']; }, $stmt->fetchAll());
                echo json_encode(['success' => true, 'data' => $modelos]);
            }
            break;
        case 'POST':
            if (!hasPermission('write')) {
                throw new Exception('Sem permissão para criar telas');
            }
            $data = $_POST;
            // Suporte a JSON
            if (empty($data)) {
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
            }
            $campos = ['data', 'fornecedor', 'marca', 'modelo', 'custo', 'maodeobra', 'valortotal'];
            foreach ($campos as $campo) {
                if (empty($data[$campo])) {
                    throw new Exception('Campo obrigatório: ' . $campo);
                }
            }
            $sql = "INSERT INTO tabelapreco (Data, Fornecedor, Marca, Modelo, Custo, MaoDeObra, ValorTotal) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $db->query($sql, [
                $data['data'],
                $data['fornecedor'],
                $data['marca'],
                $data['modelo'],
                $data['custo'],
                $data['maodeobra'],
                $data['valortotal']
            ]);
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            break;
        case 'PUT':
            if (!hasPermission('write')) {
                throw new Exception('Sem permissão para editar telas');
            }
            parse_str(file_get_contents("php://input"), $_PUT);
            $id = (int)($_PUT['id'] ?? 0);
            $campos = ['data', 'fornecedor', 'marca', 'modelo', 'custo', 'maodeobra', 'valortotal'];
            foreach ($campos as $campo) {
                if (empty($_PUT[$campo])) {
                    throw new Exception('Campo obrigatório: ' . $campo);
                }
            }
            if (empty($id)) {
                throw new Exception('ID é obrigatório');
            }
            $sql = "UPDATE tabelapreco SET Data = ?, Fornecedor = ?, Marca = ?, Modelo = ?, Custo = ?, MaoDeObra = ?, ValorTotal = ? WHERE ID = ?";
            $db->query($sql, [
                $_PUT['data'],
                $_PUT['fornecedor'],
                $_PUT['marca'],
                $_PUT['modelo'],
                $_PUT['custo'],
                $_PUT['maodeobra'],
                $_PUT['valortotal'],
                $id
            ]);
            echo json_encode(['success' => true]);
            break;
        case 'DELETE':
            if (!hasPermission('delete')) {
                throw new Exception('Sem permissão para excluir telas');
            }
            $id = (int)($_GET['id'] ?? 0);
            if (empty($id)) {
                throw new Exception('ID é obrigatório');
            }
            $db->query("DELETE FROM tabelapreco WHERE ID = ?", [$id]);
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