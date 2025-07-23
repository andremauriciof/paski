<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

requireLogin();

try {
    $db = new Database();
    $stmt = $db->query('SELECT nome, cnpj, endereco, telefone, email, logo, fator_custo, fator_mao_obra, valor_adicional FROM empresa LIMIT 1');
    $empresa = $stmt->fetch();
    if ($empresa && !empty($empresa['logo'])) {
        $empresa['logo'] = 'data:image/png;base64,' . base64_encode($empresa['logo']);
    }
    echo json_encode(['success' => true, 'data' => $empresa]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 