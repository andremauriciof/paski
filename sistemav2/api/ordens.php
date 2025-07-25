<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (isset($_GET['action']) && $_GET['action'] === 'recent') {
    $db = new Database();
    $stmt = $db->query("
        SELECT os.id, c.nome as cliente_nome, CONCAT(e.marca, ' ', e.modelo) as equipamento, os.status, os.data_entrada
        FROM ordens_servico os
        LEFT JOIN clientes c ON os.cliente_id = c.id
        LEFT JOIN equipamentos e ON os.equipamento_id = e.id
        ORDER BY os.criado_em DESC
        LIMIT 5
    ");
    $ordens = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $ordens]);
    exit;
}

try {
    $db = new Database();
    
    switch ($method) {
        case 'GET':
            if ($action === 'list') {
                $search = $_GET['search'] ?? '';
                $status = $_GET['status'] ?? '';
                $tecnico_id = $_GET['tecnico_id'] ?? '';
                $data_inicio = $_GET['data_inicio'] ?? '';
                $data_fim = $_GET['data_fim'] ?? '';
                $page = (int)($_GET['page'] ?? 1);
                $limit = (int)($_GET['limit'] ?? 10);
                $offset = ($page - 1) * $limit;
                
                $where = [];
                $params = [];
                
                if (!empty($search)) {
                    $where[] = "(os.id LIKE ? OR c.nome LIKE ?)";
                    $searchParam = "%$search%";
                    $params = array_merge($params, [$searchParam, $searchParam]);
                }
                
                if (!empty($status)) {
                    $where[] = "os.status = ?";
                    $params[] = $status;
                }
                
                if (!empty($tecnico_id)) {
                    $where[] = "os.tecnico_id = ?";
                    $params[] = $tecnico_id;
                }
                
                if (!empty($data_inicio)) {
                    $where[] = "DATE(os.data_entrada) >= ?";
                    $params[] = $data_inicio;
                }
                
                if (!empty($data_fim)) {
                    $where[] = "DATE(os.data_entrada) <= ?";
                    $params[] = $data_fim;
                }
                
                $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
                
                // Count total
                $countSql = "SELECT COUNT(*) as total FROM ordens_servico os 
                           LEFT JOIN clientes c ON os.cliente_id = c.id $whereClause";
                $countStmt = $db->query($countSql, $params);
                $total = $countStmt->fetch()['total'];
                
                // Get data
                $sql = "SELECT os.*, c.nome as cliente_nome, e.marca, e.modelo, e.tipo as equipamento_tipo,
                              u.nome as tecnico_nome
                       FROM ordens_servico os 
                       LEFT JOIN clientes c ON os.cliente_id = c.id 
                       LEFT JOIN equipamentos e ON os.equipamento_id = e.id
                       LEFT JOIN usuarios u ON os.tecnico_id = u.id
                       $whereClause 
                       ORDER BY os.criado_em DESC 
                       LIMIT $limit OFFSET $offset";
                $stmt = $db->query($sql, $params);
                $ordens = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'data' => $ordens,
                    'total' => $total,
                    'page' => $page,
                    'totalPages' => ceil($total / $limit)
                ]);
                
            } elseif ($action === 'get' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $sql = "SELECT os.*, c.nome as cliente_nome, c.telefone as cliente_telefone, 
                              c.email as cliente_email, c.cpf_cnpj as cliente_cpf_cnpj,
                              e.marca, e.modelo, e.tipo as equipamento_tipo, e.numero_serie,
                              u.nome as tecnico_nome
                       FROM ordens_servico os 
                       LEFT JOIN clientes c ON os.cliente_id = c.id 
                       LEFT JOIN equipamentos e ON os.equipamento_id = e.id
                       LEFT JOIN usuarios u ON os.tecnico_id = u.id
                       WHERE os.id = ?";
                $stmt = $db->query($sql, [$id]);
                $ordem = $stmt->fetch();
                
                if (!$ordem) {
                    throw new Exception('Ordem de serviço não encontrada');
                }
                
                echo json_encode(['success' => true, 'data' => $ordem]);
            }
            break;
            
        case 'POST':
            if (!hasPermission('write')) {
                throw new Exception('Sem permissão para criar ordens de serviço');
            }
            
            // Debug: log dos dados recebidos
            file_put_contents('debug_post.txt', print_r($_POST, true) . "\n---\n", FILE_APPEND);
            
            $cliente_id = (int)($_POST['cliente_id'] ?? 0);
            $equipamento_id = (int)($_POST['equipamento_id'] ?? 0);
            $descricao_problema = $_POST['descricao_problema'] ?? '';
            $observacoes = $_POST['observacoes'] ?? '';
            $status = $_POST['status'] ?? 'Orçamento';
            $tecnico_id = (int)($_POST['tecnico_id'] ?? 0);
            $valor_orcado = !empty($_POST['valor_orcado']) ? (float)$_POST['valor_orcado'] : null;
            $valor_final = !empty($_POST['valor_final']) ? (float)$_POST['valor_final'] : null;
            $data_entrada = $_POST['data_entrada'] ?? date('Y-m-d H:i:s');
            
            if (empty($cliente_id) || empty($equipamento_id) || empty($descricao_problema) || empty($tecnico_id)) {
                throw new Exception('Cliente, equipamento, descrição do problema e técnico são obrigatórios');
            }
            
            $sql = "INSERT INTO ordens_servico (cliente_id, equipamento_id, descricao_problema, observacoes, 
                   status, tecnico_id, valor_orcado, valor_final, data_entrada) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $db->query($sql, [$cliente_id, $equipamento_id, $descricao_problema, $observacoes, 
                             $status, $tecnico_id, $valor_orcado, $valor_final, $data_entrada]);
            
            $ordem_id = $db->lastInsertId();
            
            // Salvar marcas do checklist se fornecidas
            $checklist_marks = [];
            if (isset($_POST['checklist_marks'])) {
                if (is_array($_POST['checklist_marks'])) {
                    $checklist_marks = $_POST['checklist_marks'];
                } elseif (is_string($_POST['checklist_marks'])) {
                    // Se for string, tentar decodificar JSON
                    $decoded = json_decode($_POST['checklist_marks'], true);
                    if (is_array($decoded)) {
                        $checklist_marks = $decoded;
                    }
                }
            }
            
            if (!empty($checklist_marks)) {
                foreach ($checklist_marks as $itemId) {
                    if (is_numeric($itemId)) {
                        $db->query("INSERT INTO os_checklist_marks (ordem_id, checklist_item_id) VALUES (?, ?)", [$ordem_id, (int)$itemId]);
                    }
                }
            }
            
            // Log the creation
            $user = getCurrentUser();
            $db->query("INSERT INTO logs_os (ordem_servico_id, usuario_id, status_antigo, status_novo, observacao) 
                       VALUES (?, ?, ?, ?, ?)", 
                      [$ordem_id, $user['id'], null, $status, 'OS criada']);
            
            echo json_encode(['success' => true, 'id' => $ordem_id]);
            break;
            
        case 'PUT':
            if (!hasPermission('write')) {
                throw new Exception('Sem permissão para editar ordens de serviço');
            }
            
            parse_str(file_get_contents("php://input"), $_PUT);
            
            $id = (int)($_PUT['id'] ?? 0);
            $cliente_id = (int)($_PUT['cliente_id'] ?? 0);
            $equipamento_id = (int)($_PUT['equipamento_id'] ?? 0);
            $descricao_problema = $_PUT['descricao_problema'] ?? '';
            $observacoes = $_PUT['observacoes'] ?? '';
            $status = $_PUT['status'] ?? '';
            $tecnico_id = (int)($_PUT['tecnico_id'] ?? 0);
            $valor_orcado = !empty($_PUT['valor_orcado']) ? (float)$_PUT['valor_orcado'] : null;
            $valor_final = !empty($_PUT['valor_final']) ? (float)$_PUT['valor_final'] : null;
            
            if (empty($id) || empty($cliente_id) || empty($equipamento_id) || empty($descricao_problema) || empty($tecnico_id)) {
                throw new Exception('ID, cliente, equipamento, descrição do problema e técnico são obrigatórios');
            }
            
            // Get current status for logging
            $stmt = $db->query("SELECT status FROM ordens_servico WHERE id = ?", [$id]);
            $currentOrder = $stmt->fetch();
            $oldStatus = $currentOrder['status'];
            
            // Set data_saida if status is 'Entregue'
            $data_saida = null;
            if ($status === 'Entregue' && $oldStatus !== 'Entregue') {
                $data_saida = date('Y-m-d H:i:s');
            }
            
            $sql = "UPDATE ordens_servico SET cliente_id = ?, equipamento_id = ?, descricao_problema = ?, 
                   observacoes = ?, status = ?, tecnico_id = ?, valor_orcado = ?, valor_final = ?";
            $params = [$cliente_id, $equipamento_id, $descricao_problema, $observacoes, 
                      $status, $tecnico_id, $valor_orcado, $valor_final];
            
            if ($data_saida) {
                $sql .= ", data_saida = ?";
                $params[] = $data_saida;
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $db->query($sql, $params);
            
            // Salvar marcas do checklist se fornecidas
            $checklist_marks = [];
            if (isset($_PUT['checklist_marks'])) {
                if (is_array($_PUT['checklist_marks'])) {
                    $checklist_marks = $_PUT['checklist_marks'];
                } elseif (is_string($_PUT['checklist_marks'])) {
                    // Se for string, tentar decodificar JSON
                    $decoded = json_decode($_PUT['checklist_marks'], true);
                    if (is_array($decoded)) {
                        $checklist_marks = $decoded;
                    }
                }
            }
            
            if (!empty($checklist_marks)) {
                // Remover marcas existentes
                $db->query("DELETE FROM os_checklist_marks WHERE ordem_id = ?", [$id]);
                
                // Inserir novas marcas
                foreach ($checklist_marks as $itemId) {
                    if (is_numeric($itemId)) {
                        $db->query("INSERT INTO os_checklist_marks (ordem_id, checklist_item_id) VALUES (?, ?)", [$id, (int)$itemId]);
                    }
                }
            } else {
                // Se não há marcas, remover todas as existentes
                $db->query("DELETE FROM os_checklist_marks WHERE ordem_id = ?", [$id]);
            }
            
            // Log the status change if different
            if ($oldStatus !== $status) {
                $user = getCurrentUser();
                $db->query("INSERT INTO logs_os (ordem_servico_id, usuario_id, status_antigo, status_novo, observacao) 
                           VALUES (?, ?, ?, ?, ?)", 
                          [$id, $user['id'], $oldStatus, $status, 'Status alterado']);
            }
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            if (!hasPermission('delete')) {
                throw new Exception('Sem permissão para excluir ordens de serviço');
            }
            
            $id = (int)($_GET['id'] ?? 0);
            
            if (empty($id)) {
                throw new Exception('ID é obrigatório');
            }
            
            // Delete logs first
            $db->query("DELETE FROM logs_os WHERE ordem_servico_id = ?", [$id]);
            
            // Delete order
            $db->query("DELETE FROM ordens_servico WHERE id = ?", [$id]);
            
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