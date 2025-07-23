<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Verificar autenticação
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autorizado']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $db = new Database();
    
    switch ($action) {
        case 'get_items':
            getChecklistItems($db);
            break;
        case 'add_item':
            requireAdmin();
            addChecklistItem($db);
            break;
        case 'edit_item':
            requireAdmin();
            editChecklistItem($db);
            break;
        case 'delete_item':
            requireAdmin();
            deleteChecklistItem($db);
            break;
        case 'get_marks':
            getChecklistMarks($db);
            break;
        case 'save_marks':
            saveChecklistMarks($db);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Ação não especificada']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno do servidor: ' . $e->getMessage()]);
}

function requireAdmin() {
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Acesso restrito a administradores']);
        exit;
    }
}

function getChecklistItems($db) {
    try {
        $stmt = $db->query("SELECT id, nome, categoria FROM checklist_itens ORDER BY categoria, id");
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $items]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao buscar itens do checklist: ' . $e->getMessage()]);
    }
}

function addChecklistItem($db) {
    $nome = trim($_POST['nome'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    if (!$nome || !$categoria) {
        echo json_encode(['success' => false, 'error' => 'Descrição e categoria são obrigatórias']);
        return;
    }
    try {
        $db->query("INSERT INTO checklist_itens (nome, categoria) VALUES (?, ?)", [$nome, $categoria]);
        echo json_encode(['success' => true, 'message' => 'Item adicionado com sucesso']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao adicionar item: ' . $e->getMessage()]);
    }
}

function editChecklistItem($db) {
    $id = (int)($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    if (!$id || !$nome || !$categoria) {
        echo json_encode(['success' => false, 'error' => 'ID, descrição e categoria são obrigatórios']);
        return;
    }
    try {
        $db->query("UPDATE checklist_itens SET nome = ?, categoria = ? WHERE id = ?", [$nome, $categoria, $id]);
        echo json_encode(['success' => true, 'message' => 'Item atualizado com sucesso']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar item: ' . $e->getMessage()]);
    }
}

function deleteChecklistItem($db) {
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'ID é obrigatório']);
        return;
    }
    try {
        $db->query("DELETE FROM checklist_itens WHERE id = ?", [$id]);
        echo json_encode(['success' => true, 'message' => 'Item removido com sucesso']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao remover item: ' . $e->getMessage()]);
    }
}

function getChecklistMarks($db) {
    $ordemId = $_GET['ordem_id'] ?? null;
    
    if (!$ordemId) {
        echo json_encode(['success' => false, 'error' => 'ID da ordem não especificado']);
        return;
    }
    
    try {
        $stmt = $db->query("SELECT checklist_item_id FROM os_checklist_marks WHERE ordem_id = ?", [$ordemId]);
        $marks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $marks]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao buscar marcas do checklist: ' . $e->getMessage()]);
    }
}

function saveChecklistMarks($db) {
    $ordemId = $_POST['ordem_id'] ?? null;
    $marks = $_POST['checklist_marks'] ?? [];
    
    if (!$ordemId) {
        echo json_encode(['success' => false, 'error' => 'ID da ordem não especificado']);
        return;
    }
    
    try {
        // Remover marcas existentes
        $db->query("DELETE FROM os_checklist_marks WHERE ordem_id = ?", [$ordemId]);
        
        // Inserir novas marcas
        if (!empty($marks)) {
            foreach ($marks as $itemId) {
                $db->query("INSERT INTO os_checklist_marks (ordem_id, checklist_item_id) VALUES (?, ?)", [$ordemId, $itemId]);
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Marcas do checklist salvas com sucesso']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao salvar marcas do checklist: ' . $e->getMessage()]);
    }
}
?> 