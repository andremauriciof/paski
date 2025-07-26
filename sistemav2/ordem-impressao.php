<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
requireLogin();

$ordemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$ordemId) {
    die('ID da ordem não especificado.');
}

// Buscar dados da ordem, cliente, equipamento, técnico
$db = new Database();
$stmt = $db->query("SELECT os.*, c.nome as cliente_nome, c.telefone as cliente_telefone, c.email as cliente_email, c.cpf_cnpj as cliente_cpf_cnpj, c.endereco as cliente_endereco, c.cidade as cliente_cidade, c.estado as cliente_estado, e.marca, e.modelo, e.tipo as equipamento_tipo, e.numero_serie, u.nome as tecnico_nome FROM ordens_servico os LEFT JOIN clientes c ON os.cliente_id = c.id LEFT JOIN equipamentos e ON os.equipamento_id = e.id LEFT JOIN usuarios u ON os.tecnico_id = u.id WHERE os.id = ?", [$ordemId]);
$ordem = $stmt->fetch();
if (!$ordem) {
    die('Ordem de serviço não encontrada.');
}

// Buscar dados da empresa
$stmt = $db->query('SELECT id, nome, cnpj, ie, cep, endereco, bairro, cidade, estado, telefone, email, logo FROM empresa LIMIT 1');
$empresa = $stmt->fetch();
$logoBase64 = ($empresa && $empresa['logo']) ? 'data:image/png;base64,' . base64_encode($empresa['logo']) : '';

// Buscar marcas do checklist
$stmt = $db->query('SELECT checklist_item_id FROM os_checklist_marks WHERE ordem_id = ?', [$ordemId]);
$marks = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Buscar itens do checklist
$stmt = $db->query('SELECT id, nome, categoria FROM checklist_itens ORDER BY categoria, id');
$checklistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
$checklistByCategoria = ['Celular' => [], 'Computador' => []];
foreach ($checklistItems as $item) {
    $checklistByCategoria[$item['categoria']][] = $item;
}

function gerarChecklistHTML($categoria, $marks, $checklistByCategoria) {
    $items = $checklistByCategoria[$categoria] ?? [];
    $markedItems = array_filter($items, function($item) use ($marks) {
        return in_array($item['id'], $marks);
    });
    $html = '<table style="width:100%; font-size:10px; border-collapse:collapse;">';
    if (count($markedItems) === 0) {
        $html .= '<tr><td colspan="2">Nenhum item marcado no checklist.</td></tr>';
    }
    foreach ($markedItems as $item) {
        $html .= '<tr>';
        $html .= '<td style="width:24px;"><span style="font-size:12px; vertical-align:middle; margin-right:4px;">☑</span></td><td style="padding-left:1px;"><b>' . htmlspecialchars($item['nome']) . '</b></td>';
        $html .= '</tr>';
    }
    $html .= '</table>';
    return $html;
}

function gerarVia($tipo, $ordem, $empresa, $logoBase64, $cliente, $equipamento, $checklistHTML, $valorTotal) {
    ob_start();
    ?>
    <div class="os-via-<?= strtolower($ordem['equipamento_tipo']) ?>" style="width: 100%; max-width: 800px; margin: 0 auto; font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #222; background: #fff; border: 1.5px solid #222; padding: 8px; box-sizing: border-box;">
        <!-- Cabeçalho -->
        <div style="display: flex; align-items: center; gap: 8px;">
            <?php if ($logoBase64): ?><img src="<?= $logoBase64 ?>" alt="Logo" style="height: 38px; margin-right: 8px;"><?php endif; ?>
            <div style="font-size: 11px; line-height: 1.2;">
                <div style="font-weight: bold; font-size: 13px; color: #222;"><?= htmlspecialchars($empresa['nome'] ?? '') ?></div>
                <div><?= implode(' | ', array_filter([
                    $empresa['cnpj'] ? 'CNPJ: ' . $empresa['cnpj'] : '',
                    $empresa['ie'] ? 'IE: ' . $empresa['ie'] : ''
                ])) ?></div>
                <div><?= implode(' - ', array_filter([
                    $empresa['endereco'] ?? '',
                    $empresa['bairro'] ?? '',
                    $empresa['cidade'] ?? '',
                    $empresa['estado'] ?? '',
                    $empresa['cep'] ? 'CEP: ' . $empresa['cep'] : ''
                ])) ?></div>
                <div><?= implode(' | ', array_filter([
                    $empresa['telefone'] ?? '',
                    $empresa['email'] ? 'E-mail: ' . $empresa['email'] : ''
                ])) ?></div>
            </div>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; border-bottom: 1.5px solid #222; padding-bottom: 2px;">
            <div style="font-size: 13px; font-weight: bold;">ORDEM DE SERVIÇO</div>
            <div style="font-size: 12px; font-weight: bold;">Nº: <?= $ordem['id'] ?></div>
        </div>
        <!-- Dados Cliente -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
            <tr>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 8%; font-weight: bold;">Código</td>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 22%; font-weight: bold;">Nome</td>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Contato</td>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Telefone</td>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">CPF/CNPJ</td>
            </tr>
            <tr>
                <td style="border: 1px solid #222; padding: 2px 4px;"><?= $ordem['cliente_id'] ?></td>
                <td style="border: 1px solid #222; padding: 2px 4px;"><?= htmlspecialchars($ordem['cliente_nome']) ?></td>
                <td style="border: 1px solid #222; padding: 2px 4px;"></td>
                <td style="border: 1px solid #222; padding: 2px 4px;"><?= htmlspecialchars($ordem['cliente_telefone']) ?></td>
                <td style="border: 1px solid #222; padding: 2px 4px;"><?= htmlspecialchars($ordem['cliente_cpf_cnpj']) ?></td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid #222; padding: 2px 4px;"><b>Endereço:</b> <?= htmlspecialchars($ordem['cliente_endereco']) ?></td>
                <td style="border: 1px solid #222; padding: 2px 4px;"><b>Cidade:</b> <?= htmlspecialchars($ordem['cliente_cidade']) ?></td>
                <td style="border: 1px solid #222; padding: 2px 4px;"><b>UF:</b> <?= htmlspecialchars($ordem['cliente_estado']) ?></td>
                <td style="border: 1px solid #222; padding: 2px 4px;"></td>
            </tr>
        </table>
        <!-- Dados Equipamento -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
            <tr>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Equipamento</td>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Marca</td>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Modelo</td>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Nº Série</td>
            </tr>
            <tr>
                <td style="border: 1px solid #222; padding: 2px 4px;"><?= htmlspecialchars($ordem['equipamento_tipo']) ?></td>
                <td style="border: 1px solid #222; padding: 2px 4px;"><?= htmlspecialchars($ordem['marca']) ?></td>
                <td style="border: 1px solid #222; padding: 2px 4px;"><?= htmlspecialchars($ordem['modelo']) ?></td>
                <td style="border: 1px solid #222; padding: 2px 4px;"><?= htmlspecialchars($ordem['numero_serie']) ?></td>
            </tr>
        </table>
        <!-- Detalhes e Defeito Relatado lado a lado -->
        <div style="display: flex; gap: 10px; margin-bottom: 6px;">
            <div style="flex: 1; border: 1px solid #222; min-height: 48px; padding: 4px 6px; display: flex; flex-direction: column; justify-content: flex-start;">
                <b>Detalhes do Equipamento:</b>
                <div style="min-height: 36px; white-space: pre-line;"><?= nl2br(htmlspecialchars($ordem['observacoes'])) ?><br><br></div>
            </div>
            <div style="flex: 1; border: 1px solid #222; min-height: 48px; padding: 4px 6px; display: flex; flex-direction: column; justify-content: flex-start;">
                <b>Defeito Relatado:</b>
                <div style="min-height: 36px; white-space: pre-line;"><?= nl2br(htmlspecialchars($ordem['descricao_problema'])) ?><br><br></div>
            </div>
        </div>
        <!-- Checklist -->
        <div style="border: 1px solid #222; margin-bottom: 6px; padding: 4px 6px;">
            <div style="font-weight: bold; text-align: center; margin-bottom: 2px;">Check List</div>
            <?= $checklistHTML ?>
        </div>
        <!-- Diagnóstico/Solução -->
        <div style="border: 1px solid #222; min-height: 48px; padding: 4px 6px; margin-bottom: 6px;">
            <b>Diagnóstico/Solução:</b>
            <div style="min-height: 36px; white-space: pre-line;"><?= $ordem['status'] === 'Orçamento' ? 'Orçamento' : 'Diagnóstico' ?><br><br></div>
        </div>
        <!-- Fechamento -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
            <tr>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 20%; font-weight: bold;">FECHAMENTO</td>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 20%;">Valor das Peças</td>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 20%;">Valor dos Serviços</td>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 20%;">Valor Frete</td>
                <td style="border: 1px solid #222; padding: 2px 4px; width: 20%; font-weight: bold; background: #eee;">TOTAL</td>
            </tr>
            <tr>
                <td style="border: 1px solid #222; padding: 2px 4px;"></td>
                <td style="border: 1px solid #222; padding: 2px 4px;">R$ 0,00</td>
                <td style="border: 1px solid #222; padding: 2px 4px;">R$ 0,00</td>
                <td style="border: 1px solid #222; padding: 2px 4px;">R$ 0,00</td>
                <td style="border: 1px solid #222; padding: 2px 4px; font-weight: bold; background: #eee;"><?= formatCurrency($valorTotal) ?></td>
            </tr>
        </table>
        <!-- Termos -->
        <div style="font-size: 8px; margin-bottom: 2px;">
            <div>01 - O equipamento não retirado no prazo de até 90 dias após o parecer ou aprovação do serviço, só será entregue mediante pagamento de taxa de R$ 15,00 (quinze reais) ao mês a título de seguro.</div>
            <div>02 - A Assistência Técnica oferece garantia de 90 dias após a entrega do equipamento na peça trocada sem a violação do lacre de segurança, com exceção de: Limpezas, desoxidações, atualizações de software e desbloqueios.</div>
            <div>03 - O Cliente é o total responsável pela procedência do equipamento, estando assim a Assistência Técnica isenta de qualquer responsabilidade.</div>
            <div>04 - A Assistência Técnica não é responsável por arquivos contidos no equipamento como: fotos, músicas, vídeos, agenda de contatos, programas, aplicativos e jogos. Estas arquivos podem ser removidos em determinados serviços e o backup deverá ser efetuado pelo próprio cliente.</div>
            <div>Declaro estar de acordo com os itens descritos acima e com os testes efetuados da lista de checagem do equipamento.</div>
        </div>
        <!-- Assinaturas -->
        <div style="display: flex; justify-content: space-between; font-size: 10px; margin-top: 8px; gap: 20px;">
            <div style="flex: 1;">
                <div>Data de Entrada na Assistência: _____/_____/________</div>
                <div>Assinatura: ____________________________</div>
            </div>
            <div style="flex: 1;">
                <div>Data de Entrega ao Cliente: _____/_____/________</div>
                <div>Assinatura: ____________________________</div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function formatCurrency($value) {
    return 'R$ ' . number_format((float)$value, 2, ',', '.');
}
$valorTotal = isset($ordem['valor_final']) && $ordem['valor_final'] !== null ? $ordem['valor_final'] : (isset($ordem['valor_orcado']) ? $ordem['valor_orcado'] : 0);

$checklistCelularHTML = gerarChecklistHTML('Celular', $marks, $checklistByCategoria);
$checklistComputadorHTML = gerarChecklistHTML('Computador', $marks, $checklistByCategoria);

$html = '';
if (strtolower($ordem['equipamento_tipo']) === 'celular') {
    $html .= gerarVia('EMPRESA', $ordem, $empresa, $logoBase64, $ordem, $ordem, $checklistCelularHTML, $valorTotal);
    $html .= gerarVia('CLIENTE', $ordem, $empresa, $logoBase64, $ordem, $ordem, $checklistCelularHTML, $valorTotal);
} else {
    $html .= gerarVia('EMPRESA', $ordem, $empresa, $logoBase64, $ordem, $ordem, $checklistComputadorHTML, $valorTotal);
    $html .= gerarVia('CLIENTE', $ordem, $empresa, $logoBase64, $ordem, $ordem, $checklistComputadorHTML, $valorTotal);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Impressão OS #<?= $ordem['id'] ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background: #f5f5f5; }
        .os-via-celular, .os-via-computador {
            background: white;
            margin: 0 auto 10px auto;
            page-break-after: always;
            width: 210mm;
            min-height: 287mm;
            max-width: 210mm;
            max-height: 287mm;
            box-sizing: border-box;
            overflow: hidden;
            padding: 8mm 8mm 8mm 8mm;
            /* padding reduzido para caber melhor */
        }
        .os-via-celular:last-child, .os-via-computador:last-child { page-break-after: avoid; }
        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                background: white;
            }
            .os-via-celular, .os-via-computador {
                page-break-after: always;
                margin: 0 auto 0 auto;
                width: 210mm;
                min-height: 287mm;
                max-width: 210mm;
                max-height: 287mm;
                box-sizing: border-box;
                overflow: hidden;
                padding: 8mm 8mm 8mm 8mm;
            }
            .os-via-celular:last-child, .os-via-computador:last-child { page-break-after: avoid; }
        }
    </style>
</head>
<body onload="window.print()">
<?= $html ?>
</body>
</html> 