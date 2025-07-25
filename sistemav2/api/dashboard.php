<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

requireLogin();

if (isset($_GET['action']) && $_GET['action'] === 'summary') {
    $db = new Database();
    $clientes = $db->query('SELECT COUNT(*) as total FROM clientes')->fetch()['total'];
    $equipamentos = $db->query('SELECT COUNT(*) as total FROM equipamentos')->fetch()['total'];
    $total_os = $db->query('SELECT COUNT(*) as total FROM ordens_servico')->fetch()['total'];
    $os_abertas = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status IN ('Orçamento', 'Executando', 'Aguardando Peça')")->fetch()['total'];
    $os_finalizadas = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status IN ('Finalizada', 'Entregue')")->fetch()['total'];
    $tabela = $db->query('SELECT COUNT(*) as total FROM tabelapreco')->fetch()['total'];
    // Status das ordens
    $status = $db->query('SELECT status, COUNT(*) as total FROM ordens_servico GROUP BY status')->fetchAll();
    $ordensStatus = [];
    foreach ($status as $s) $ordensStatus[$s['status']] = (int)$s['total'];
    echo json_encode([
        'success' => true,
        'clientes' => (int)$clientes,
        'equipamentos' => (int)$equipamentos,
        'ordens' => (int)$total_os,
        'tabela' => (int)$tabela,
        'total_os' => (int)$total_os,
        'os_abertas' => (int)$os_abertas,
        'os_finalizadas' => (int)$os_finalizadas,
        'alertas' => [],
        'ordensStatus' => $ordensStatus
    ]);
    exit;
}

try {
    $db = new Database();
    
    // Get statistics
    $stats = [];
    
    // Total OS
    $stmt = $db->query("SELECT COUNT(*) as total FROM ordens_servico");
    $stats['total_os'] = $stmt->fetch()['total'];
    
    // Open OS
    $stmt = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status IN ('Orçamento', 'Executando', 'Aguardando Peça')");
    $stats['os_abertas'] = $stmt->fetch()['total'];
    
    // Finished OS
    $stmt = $db->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status IN ('Finalizada', 'Entregue')");
    $stats['os_finalizadas'] = $stmt->fetch()['total'];
    
    // Revenue
    if (!hasPermission('financeiro')) {
        $stats['faturamento'] = null;
    } else {
        $stmt = $db->query("SELECT COALESCE(SUM(valor_final), 0) as total FROM ordens_servico WHERE valor_final IS NOT NULL");
        $stats['faturamento'] = $stmt->fetch()['total'];
    }
    
    // Recent orders
    $stmt = $db->query("
        SELECT os.id, os.status, os.data_entrada, c.nome as cliente_nome, 
               CONCAT(e.marca, ' ', e.modelo) as equipamento
        FROM ordens_servico os
        LEFT JOIN clientes c ON os.cliente_id = c.id
        LEFT JOIN equipamentos e ON os.equipamento_id = e.id
        ORDER BY os.criado_em DESC
        LIMIT 5
    ");
    $recent_orders = $stmt->fetchAll();
    
    // Status distribution
    $stmt = $db->query("
        SELECT status, COUNT(*) as count 
        FROM ordens_servico 
        GROUP BY status
    ");
    $status_distribution = $stmt->fetchAll();
    
    // Monthly revenue (last 6 months)
    if (!hasPermission('financeiro')) {
        $monthly_revenue = [];
    } else {
        $stmt = $db->query("
            SELECT DATE_FORMAT(data_entrada, '%Y-%m') as mes, 
                   COALESCE(SUM(valor_final), 0) as total
            FROM ordens_servico 
            WHERE data_entrada >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            AND valor_final IS NOT NULL
            GROUP BY DATE_FORMAT(data_entrada, '%Y-%m')
            ORDER BY mes
        ");
        $monthly_revenue = $stmt->fetchAll();
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'stats' => $stats,
            'recent_orders' => $recent_orders,
            'status_distribution' => $status_distribution,
            'monthly_revenue' => $monthly_revenue
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>