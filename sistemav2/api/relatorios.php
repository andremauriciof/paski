<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

requireLogin();

$action = $_GET['action'] ?? '';

try {
    $db = new Database();
    
    // Função auxiliar para montar WHERE corretamente
    function appendWhere($base, $extra) {
        if (strpos($base, 'WHERE') !== false) {
            return $base . ' AND ' . $extra;
        } else {
            return $base . ' WHERE ' . $extra;
        }
    }
    
    switch ($action) {
        case 'summary':
            $data_inicio = $_GET['data_inicio'] ?? '';
            $data_fim = $_GET['data_fim'] ?? '';
            $tecnico_id = $_GET['tecnico_id'] ?? '';
            
            $where = [];
            $params = [];
            
            if (!empty($data_inicio)) {
                $where[] = "DATE(data_entrada) >= ?";
                $params[] = $data_inicio;
            }
            
            if (!empty($data_fim)) {
                $where[] = "DATE(data_entrada) <= ?";
                $params[] = $data_fim;
            }
            
            if (!empty($tecnico_id)) {
                $where[] = "tecnico_id = ?";
                $params[] = $tecnico_id;
            }
            
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            // Summary stats
            $stmt = $db->query("SELECT COUNT(*) as total FROM ordens_servico $whereClause", $params);
            $total_os = $stmt->fetch()['total'];
            
            // Corrigir montagem do WHERE para status
            $whereStatus = $whereClause;
            if (!empty($whereStatus)) {
                $whereStatus .= " AND status IN ('Finalizada', 'Entregue')";
            } else {
                $whereStatus = "WHERE status IN ('Finalizada', 'Entregue')";
            }
            $stmt = $db->query("SELECT COUNT(*) as total FROM ordens_servico $whereStatus", $params);
            $os_concluidas = $stmt->fetch()['total'];
            
            // Corrigir montagem do WHERE para valor_final
            if (!hasPermission('financeiro')) {
                $faturamento = null;
            } else {
                $whereValor = $whereClause;
                if (!empty($whereValor)) {
                    $whereValor .= " AND valor_final IS NOT NULL";
                } else {
                    $whereValor = "WHERE valor_final IS NOT NULL";
                }
                $stmt = $db->query("SELECT COALESCE(SUM(valor_final), 0) as total FROM ordens_servico $whereValor", $params);
                $faturamento = $stmt->fetch()['total'];
            }
            
            // Corrigir montagem do WHERE para data_saida
            $whereSaida = $whereClause;
            if (!empty($whereSaida)) {
                $whereSaida .= " AND data_saida IS NOT NULL";
            } else {
                $whereSaida = "WHERE data_saida IS NOT NULL";
            }
            $stmt = $db->query("
                SELECT AVG(DATEDIFF(data_saida, data_entrada)) as tempo_medio 
                FROM ordens_servico $whereSaida
            ", $params);
            $tempo_medio = $stmt->fetch()['tempo_medio'] ?? 0;
            
            // OS by technician
            $stmt = $db->query("
                SELECT u.nome, COUNT(*) as count
                FROM ordens_servico os
                LEFT JOIN usuarios u ON os.tecnico_id = u.id
                $whereClause
                GROUP BY os.tecnico_id, u.nome
                ORDER BY count DESC
            ", $params);
            $os_por_tecnico = $stmt->fetchAll();
            
            // Status distribution
            $stmt = $db->query("
                SELECT status, COUNT(*) as count 
                FROM ordens_servico $whereClause
                GROUP BY status
            ", $params);
            $status_distribution = $stmt->fetchAll();
            
            // Top clients
            $stmt = $db->query("
                SELECT c.nome, COUNT(*) as count
                FROM ordens_servico os
                LEFT JOIN clientes c ON os.cliente_id = c.id
                $whereClause
                GROUP BY os.cliente_id, c.nome
                ORDER BY count DESC
                LIMIT 5
            ", $params);
            $top_clientes = $stmt->fetchAll();
            
            // Detailed data
            $stmt = $db->query("
                SELECT os.*, c.nome as cliente_nome, 
                       CONCAT(e.marca, ' ', e.modelo) as equipamento,
                       u.nome as tecnico_nome
                FROM ordens_servico os
                LEFT JOIN clientes c ON os.cliente_id = c.id
                LEFT JOIN equipamentos e ON os.equipamento_id = e.id
                LEFT JOIN usuarios u ON os.tecnico_id = u.id
                $whereClause
                ORDER BY os.criado_em DESC
            ", $params);
            $detailed_data = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'summary' => [
                        'total_os' => $total_os,
                        'os_concluidas' => $os_concluidas,
                        'faturamento' => $faturamento,
                        'tempo_medio' => round($tempo_medio)
                    ],
                    'os_por_tecnico' => $os_por_tecnico,
                    'status_distribution' => $status_distribution,
                    'top_clientes' => $top_clientes,
                    'detailed_data' => $detailed_data
                ]
            ]);
            break;
            
        case 'export':
            $type = $_GET['type'] ?? '';
            $data_inicio = $_GET['data_inicio'] ?? '';
            $data_fim = $_GET['data_fim'] ?? '';
            
            $where = [];
            $params = [];
            
            if (!empty($data_inicio)) {
                $where[] = "DATE(data_entrada) >= ?";
                $params[] = $data_inicio;
            }
            
            if (!empty($data_fim)) {
                $where[] = "DATE(data_entrada) <= ?";
                $params[] = $data_fim;
            }
            
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            if ($type === 'ordens') {
                $stmt = $db->query("
                    SELECT os.id, c.nome as cliente, 
                           CONCAT(e.marca, ' ', e.modelo) as equipamento,
                           u.nome as tecnico, os.status, 
                           DATE(os.data_entrada) as data_entrada,
                           DATE(os.data_saida) as data_saida,
                           COALESCE(os.valor_final, os.valor_orcado, 0) as valor
                    FROM ordens_servico os
                    LEFT JOIN clientes c ON os.cliente_id = c.id
                    LEFT JOIN equipamentos e ON os.equipamento_id = e.id
                    LEFT JOIN usuarios u ON os.tecnico_id = u.id
                    $whereClause
                    ORDER BY os.criado_em DESC
                ", $params);
                
            } elseif ($type === 'clientes') {
                $stmt = $db->query("
                    SELECT nome, telefone, email, cpf_cnpj, endereco, 
                           DATE(criado_em) as data_cadastro
                    FROM clientes
                    ORDER BY nome
                ");
            }
            
            $data = $stmt->fetchAll();
            
            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="relatorio_' . $type . '_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            if (!empty($data)) {
                // Write headers
                fputcsv($output, array_keys($data[0]));
                
                // Write data
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
            }
            
            fclose($output);
            exit;
            
        case 'financeiro_pdf':
            // Geração de PDF do relatório financeiro
            $data_inicio = $_GET['data_inicio'] ?? '';
            $data_fim = $_GET['data_fim'] ?? '';
            $tecnico_id = $_GET['tecnico_id'] ?? '';
            $where = [];
            $params = [];
            if (!empty($data_inicio)) {
                $where[] = "DATE(data_entrada) >= ?";
                $params[] = $data_inicio;
            }
            if (!empty($data_fim)) {
                $where[] = "DATE(data_entrada) <= ?";
                $params[] = $data_fim;
            }
            if (!empty($tecnico_id)) {
                $where[] = "tecnico_id = ?";
                $params[] = $tecnico_id;
            }
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            // Dados resumo
            $stmt = $db->query("SELECT COUNT(*) as total FROM ordens_servico $whereClause", $params);
            $total_os = $stmt->fetch()['total'];
            $whereStatus = $whereClause;
            if (!empty($whereStatus)) {
                $whereStatus .= " AND status IN ('Finalizada', 'Entregue')";
            } else {
                $whereStatus = "WHERE status IN ('Finalizada', 'Entregue')";
            }
            $stmt = $db->query("SELECT COUNT(*) as total FROM ordens_servico $whereStatus", $params);
            $os_concluidas = $stmt->fetch()['total'];
            if (!hasPermission('financeiro')) {
                $faturamento = null;
            } else {
                $whereValor = $whereClause;
                if (!empty($whereValor)) {
                    $whereValor .= " AND valor_final IS NOT NULL";
                } else {
                    $whereValor = "WHERE valor_final IS NOT NULL";
                }
                $stmt = $db->query("SELECT COALESCE(SUM(valor_final), 0) as total FROM ordens_servico $whereValor", $params);
                $faturamento = $stmt->fetch()['total'];
            }
            // Técnico
            $tecnico_nome = '';
            if (!empty($tecnico_id)) {
                $stmt = $db->query("SELECT nome FROM usuarios WHERE id = ?", [$tecnico_id]);
                $tecnico_nome = $stmt->fetch()['nome'] ?? '';
            }
            // Dados detalhados
            $stmt = $db->query("
                SELECT os.id, c.nome as cliente, CONCAT(e.marca, ' ', e.modelo) as equipamento,
                       u.nome as tecnico, os.status, DATE(os.data_entrada) as data_entrada,
                       DATE(os.data_saida) as data_saida, COALESCE(os.valor_final, os.valor_orcado, 0) as valor
                FROM ordens_servico os
                LEFT JOIN clientes c ON os.cliente_id = c.id
                LEFT JOIN equipamentos e ON os.equipamento_id = e.id
                LEFT JOIN usuarios u ON os.tecnico_id = u.id
                $whereClause
                ORDER BY os.criado_em DESC
            ", $params);
            $dados = $stmt->fetchAll();
            // Montar HTML do relatório
            $periodo = (!empty($data_inicio) && !empty($data_fim)) ? "Período: $data_inicio a $data_fim" :
                (!empty($data_inicio) ? "A partir de $data_inicio" : (!empty($data_fim) ? "Até $data_fim" : "Todos os períodos"));
            $html = '<h2 style="text-align:center;">Relatório Financeiro</h2>';
            $html .= '<p>' . $periodo . '</p>';
            if ($tecnico_nome) $html .= '<p><strong>Técnico:</strong> ' . htmlspecialchars($tecnico_nome) . '</p>';
            $html .= '<p><strong>Faturamento:</strong> R$ ' . number_format($faturamento, 2, ',', '.') .
                ' | <strong>Total de OS:</strong> ' . $total_os .
                ' | <strong>OS Concluídas:</strong> ' . $os_concluidas . '</p>';
            $html .= '<br><table border="1" cellpadding="4" cellspacing="0" width="100%">';
            $html .= '<thead><tr style="background:#f0f0f0;"><th>OS #</th><th>Cliente</th><th>Equipamento</th><th>Técnico</th><th>Status</th><th>Entrada</th><th>Saída</th><th>Valor</th></tr></thead><tbody>';
            foreach ($dados as $row) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($row['id']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['cliente']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['equipamento']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['tecnico']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['status']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['data_entrada']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['data_saida']) . '</td>';
                $html .= '<td>R$ ' . number_format($row['valor'], 2, ',', '.') . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
            // Gerar PDF com mPDF
            try {
                if (!class_exists('\Mpdf\Mpdf')) {
                    throw new Exception('Biblioteca mPDF não instalada. Instale via Composer: composer require mpdf/mpdf');
                }
                $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);
                $mpdf->WriteHTML($html);
                $mpdf->Output('relatorio_financeiro.pdf', 'I');
                exit;
            } catch (Exception $e) {
                header('Content-Type: text/html; charset=utf-8');
                echo '<h3>Erro ao gerar PDF</h3><p>' . htmlspecialchars($e->getMessage()) . '</p>';
                exit;
            }
            
        default:
            throw new Exception('Ação não reconhecida');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>