<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (isset($_GET['action']) && $_GET['action'] === 'search') {
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    $clientes = [];
    if ($q !== '') {
        require_once '../config/database.php';
        $db = new Database();
        $stmt = $db->query("SELECT id, nome, cpf_cnpj FROM clientes WHERE nome LIKE ? OR cpf_cnpj LIKE ? LIMIT 20", ["%$q%", "%$q%"]);
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    echo json_encode(['success' => true, 'data' => $clientes]);
    exit;
}

try {
    $db = new Database();
    
    switch ($method) {
        case 'GET':
            if ($action === 'list') {
                $search = $_GET['search'] ?? '';
                $type = $_GET['type'] ?? '';
                $page = (int)($_GET['page'] ?? 1);
                $limit = (int)($_GET['limit'] ?? 10);
                $offset = ($page - 1) * $limit;
                
                $where = [];
                $params = [];
                
                if (!empty($search)) {
                    $where[] = "(nome LIKE ? OR telefone LIKE ? OR celular LIKE ? OR email LIKE ? OR cpf_cnpj LIKE ? OR endereco LIKE ? OR cidade LIKE ?)";
                    $searchParam = "%$search%";
                    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
                }
                
                if (!empty($type)) {
                    if ($type === 'cpf') {
                        $where[] = "LENGTH(REPLACE(REPLACE(REPLACE(cpf_cnpj, '.', ''), '-', ''), '/', '')) <= 11";
                    } else {
                        $where[] = "LENGTH(REPLACE(REPLACE(REPLACE(cpf_cnpj, '.', ''), '-', ''), '/', '')) > 11";
                    }
                }
                
                $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
                
                // Count total
                $countStmt = $db->query("SELECT COUNT(*) as total FROM clientes $whereClause", $params);
                $total = $countStmt->fetch()['total'];
                
                // Get data
                $sql = "SELECT * FROM clientes $whereClause ORDER BY nome LIMIT $limit OFFSET $offset";
                $stmt = $db->query($sql, $params);
                $clientes = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'data' => $clientes,
                    'total' => $total,
                    'page' => $page,
                    'totalPages' => ceil($total / $limit)
                ]);
                
            } elseif ($action === 'get' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $stmt = $db->query("SELECT * FROM clientes WHERE id = ?", [$id]);
                $cliente = $stmt->fetch();
                
                if (!$cliente) {
                    throw new Exception('Cliente não encontrado');
                }
                
                echo json_encode(['success' => true, 'data' => $cliente]);
            } elseif ($action === 'cep' && isset($_GET['cep'])) {
                $cep = preg_replace('/\D/', '', $_GET['cep']);
                $viaCepUrl = "https://viacep.com.br/ws/{$cep}/json/";
                $cepData = @file_get_contents($viaCepUrl);
                if ($cepData === false) {
                    echo json_encode(['success' => false, 'error' => 'Erro ao consultar o ViaCEP']);
                } else {
                    echo $cepData;
                }
                return;
            } elseif ($action === 'cnpj' && isset($_GET['cnpj'])) {
                $cnpj = preg_replace('/\D/', '', $_GET['cnpj']);
                $url = "https://receitaws.com.br/v1/cnpj/{$cnpj}";
                $opts = [
                    "http" => ["header" => "User-Agent: PHP"]
                ];
                $context = stream_context_create($opts);
                $cnpjData = @file_get_contents($url, false, $context);
                if ($cnpjData === false) {
                    echo json_encode(['success' => false, 'error' => 'Erro ao consultar ReceitaWS']);
                } else {
                    echo $cnpjData;
                }
                return;
            }
            break;
            
        case 'POST':
            if (!hasPermission('write')) {
                throw new Exception('Sem permissão para criar clientes');
            }
            $nome = $_POST['nome'] ?? '';
            $telefone = $_POST['telefone'] ?? '';
            $celular = $_POST['celular'] ?? '';
            $email = $_POST['email'] ?? '';
            $cpf_cnpj = $_POST['cpf_cnpj'] ?? '';
            $ie = $_POST['ie'] ?? '';
            $endereco = $_POST['endereco'] ?? '';
            $numero = $_POST['numero'] ?? '';
            $complemento = $_POST['complemento'] ?? '';
            $bairro = $_POST['bairro'] ?? '';
            $cidade = $_POST['cidade'] ?? '';
            $estado = $_POST['estado'] ?? '';
            $cep = $_POST['cep'] ?? '';
            $observacoes = $_POST['observacoes'] ?? '';
            if (empty($nome) || empty($celular) || empty($cpf_cnpj)) {
                throw new Exception('Nome, celular e CPF/CNPJ são obrigatórios');
            }
            $sql = "INSERT INTO clientes (nome, telefone, celular, email, cpf_cnpj, ie, endereco, numero, complemento, bairro, cidade, estado, cep, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $db->query($sql, [$nome, $telefone, $celular, $email, $cpf_cnpj, $ie, $endereco, $numero, $complemento, $bairro, $cidade, $estado, $cep, $observacoes]);
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            break;
            
        case 'PUT':
            if (!hasPermission('write')) {
                throw new Exception('Sem permissão para editar clientes');
            }
            parse_str(file_get_contents("php://input"), $_PUT);
            $id = (int)($_PUT['id'] ?? 0);
            $nome = $_PUT['nome'] ?? '';
            $telefone = $_PUT['telefone'] ?? '';
            $celular = $_PUT['celular'] ?? '';
            $email = $_PUT['email'] ?? '';
            $cpf_cnpj = $_PUT['cpf_cnpj'] ?? '';
            $ie = $_PUT['ie'] ?? '';
            $endereco = $_PUT['endereco'] ?? '';
            $numero = $_PUT['numero'] ?? '';
            $complemento = $_PUT['complemento'] ?? '';
            $bairro = $_PUT['bairro'] ?? '';
            $cidade = $_PUT['cidade'] ?? '';
            $estado = $_PUT['estado'] ?? '';
            $cep = $_PUT['cep'] ?? '';
            $observacoes = $_PUT['observacoes'] ?? '';
            if (empty($id) || empty($nome) || empty($celular) || empty($cpf_cnpj)) {
                throw new Exception('ID, nome, celular e CPF/CNPJ são obrigatórios');
            }
            $sql = "UPDATE clientes SET nome = ?, telefone = ?, celular = ?, email = ?, cpf_cnpj = ?, ie = ?, endereco = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, estado = ?, cep = ?, observacoes = ? WHERE id = ?";
            $db->query($sql, [$nome, $telefone, $celular, $email, $cpf_cnpj, $ie, $endereco, $numero, $complemento, $bairro, $cidade, $estado, $cep, $observacoes, $id]);
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            if (!hasPermission('delete')) {
                throw new Exception('Sem permissão para excluir clientes');
            }
            
            $id = (int)($_GET['id'] ?? 0);
            
            if (empty($id)) {
                throw new Exception('ID é obrigatório');
            }
            
            // Check if client has equipment or service orders
            $stmt = $db->query("SELECT COUNT(*) as count FROM equipamentos WHERE cliente_id = ?", [$id]);
            if ($stmt->fetch()['count'] > 0) {
                throw new Exception('Não é possível excluir cliente com equipamentos cadastrados');
            }
            
            $stmt = $db->query("SELECT COUNT(*) as count FROM ordens_servico WHERE cliente_id = ?", [$id]);
            if ($stmt->fetch()['count'] > 0) {
                throw new Exception('Não é possível excluir cliente com ordens de serviço');
            }
            
            $db->query("DELETE FROM clientes WHERE id = ?", [$id]);
            
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