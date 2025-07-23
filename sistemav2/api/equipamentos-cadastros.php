<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
// Suporte a JSON no POST
$rawInput = file_get_contents('php://input');
$dataInput = json_decode($rawInput, true);
$entidade = $_GET['entidade'] ?? $_POST['entidade'] ?? ($dataInput['entidade'] ?? '');
$action = $_GET['action'] ?? $_POST['action'] ?? ($dataInput['action'] ?? '');

try {
    $db = new Database();
    switch ($method) {
        case 'GET':
            if ($entidade === 'tipo') {
                $stmt = $db->query('SELECT * FROM tipos_equipamento ORDER BY nome');
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            } elseif ($entidade === 'marca') {
                $stmt = $db->query('SELECT * FROM marcas ORDER BY nome');
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            } elseif ($entidade === 'modelo') {
                $tipo_id = $_GET['tipo_id'] ?? null;
                $marca_id = $_GET['marca_id'] ?? null;
                $where = [];
                $params = [];
                if ($tipo_id) { $where[] = 'tipo_id = ?'; $params[] = $tipo_id; }
                if ($marca_id) { $where[] = 'marca_id = ?'; $params[] = $marca_id; }
                $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
                $stmt = $db->query("SELECT * FROM modelos $whereClause ORDER BY nome", $params);
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            } else {
                throw new Exception('Entidade não reconhecida');
            }
            break;
        case 'POST':
            if (!hasPermission('write')) throw new Exception('Sem permissão');
            $data = $_POST;
            if (empty($data)) $data = json_decode(file_get_contents('php://input'), true) ?? [];
            // EDIÇÃO
            if ($action === 'edit') {
                $id = (int)($data['id'] ?? 0);
                if (!$id) throw new Exception('ID obrigatório');
                if ($entidade === 'tipo') {
                    $nome = trim($data['nome'] ?? '');
                    if (!$nome) throw new Exception('Nome obrigatório');
                    $db->query('UPDATE tipos_equipamento SET nome = ? WHERE id = ?', [$nome, $id]);
                    echo json_encode(['success' => true]);
                } elseif ($entidade === 'marca') {
                    $nome = trim($data['nome'] ?? '');
                    if (!$nome) throw new Exception('Nome obrigatório');
                    $db->query('UPDATE marcas SET nome = ? WHERE id = ?', [$nome, $id]);
                    echo json_encode(['success' => true]);
                } elseif ($entidade === 'modelo') {
                    $nome = trim($data['nome'] ?? '');
                    $tipo_id = (int)($data['tipo_id'] ?? 0);
                    $marca_id = (int)($data['marca_id'] ?? 0);
                    if (!$nome || !$tipo_id || !$marca_id) throw new Exception('Todos os campos são obrigatórios');
                    $db->query('UPDATE modelos SET nome = ?, tipo_id = ?, marca_id = ? WHERE id = ?', [$nome, $tipo_id, $marca_id, $id]);
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception('Entidade não reconhecida');
                }
                break;
            }
            // CADASTRO NOVO
            if ($entidade === 'tipo') {
                $nome = trim($data['nome'] ?? '');
                if (!$nome) throw new Exception('Nome obrigatório');
                $db->query('INSERT INTO tipos_equipamento (nome) VALUES (?)', [$nome]);
                echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            } elseif ($entidade === 'marca') {
                $nome = trim($data['nome'] ?? '');
                if (!$nome) throw new Exception('Nome obrigatório');
                $db->query('INSERT INTO marcas (nome) VALUES (?)', [$nome]);
                echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            } elseif ($entidade === 'modelo') {
                $nome = trim($data['nome'] ?? '');
                $tipo_id = (int)($data['tipo_id'] ?? 0);
                $marca_id = (int)($data['marca_id'] ?? 0);
                if (!$nome || !$tipo_id || !$marca_id) throw new Exception('Todos os campos são obrigatórios');
                $db->query('INSERT INTO modelos (nome, tipo_id, marca_id) VALUES (?, ?, ?)', [$nome, $tipo_id, $marca_id]);
                echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            } else {
                throw new Exception('Entidade não reconhecida');
            }
            break;
        case 'DELETE':
            if (!hasPermission('delete')) throw new Exception('Sem permissão');
            $id = (int)($_GET['id'] ?? 0);
            if (!$id) throw new Exception('ID obrigatório');
            if ($entidade === 'tipo') {
                $db->query('DELETE FROM tipos_equipamento WHERE id = ?', [$id]);
            } elseif ($entidade === 'marca') {
                $db->query('DELETE FROM marcas WHERE id = ?', [$id]);
            } elseif ($entidade === 'modelo') {
                $db->query('DELETE FROM modelos WHERE id = ?', [$id]);
            } else {
                throw new Exception('Entidade não reconhecida');
            }
            echo json_encode(['success' => true]);
            break;
        default:
            throw new Exception('Método não permitido');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} 