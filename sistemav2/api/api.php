<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $db = new Database();
    
    // --- INÍCIO INTEGRAÇÃO TABELA DE TELAS ---
    function tabela_handle_action($action) {
        require_once __DIR__ . '/../config/database.php';
        require_once __DIR__ . '/../includes/auth.php';
        $user = getCurrentUser();
        ob_start(); // Captura qualquer saída inesperada
        try {
            switch ($action) {
                case 'tabela_list':
                    $_GET['marca'] = $_POST['marca'] ?? null;
                    $_GET['modelo'] = $_POST['modelo'] ?? null;
                    $_GET['fornecedor'] = $_POST['fornecedor'] ?? null;
                    $_GET['action'] = '';
                    $_SERVER['REQUEST_METHOD'] = 'GET';
                    include __DIR__ . '/../api/api.php';
                    break;
                case 'tabela_get':
                    $_GET['id'] = $_POST['id'] ?? '';
                    $_SERVER['REQUEST_METHOD'] = 'GET';
                    include __DIR__ . '/../api/api.php';
                    break;
                case 'tabela_add':
                    $_POST['data'] = $_POST['data'] ?? '';
                    $_POST['fornecedor'] = $_POST['fornecedor'] ?? '';
                    $_POST['marca'] = $_POST['marca'] ?? '';
                    $_POST['modelo'] = $_POST['modelo'] ?? '';
                    $_POST['custo'] = $_POST['custo'] ?? 0;
                    $_POST['maodeobra'] = $_POST['maodeobra'] ?? 0;
                    $_POST['valortotal'] = $_POST['valortotal'] ?? 0;
                    $_SERVER['REQUEST_METHOD'] = 'POST';
                    include __DIR__ . '/../api/api.php';
                    break;
                case 'tabela_update':
                    $_POST['id'] = $_POST['id'] ?? '';
                    $_POST['data'] = $_POST['data'] ?? '';
                    $_POST['fornecedor'] = $_POST['fornecedor'] ?? '';
                    $_POST['marca'] = $_POST['marca'] ?? '';
                    $_POST['modelo'] = $_POST['modelo'] ?? '';
                    $_POST['custo'] = $_POST['custo'] ?? 0;
                    $_POST['maodeobra'] = $_POST['maodeobra'] ?? 0;
                    $_POST['valortotal'] = $_POST['valortotal'] ?? 0;
                    $_SERVER['REQUEST_METHOD'] = 'PUT';
                    include __DIR__ . '/../api/api.php';
                    break;
                case 'tabela_delete':
                    $_GET['id'] = $_POST['id'] ?? '';
                    $_SERVER['REQUEST_METHOD'] = 'DELETE';
                    include __DIR__ . '/../api/api.php';
                    break;
                case 'tabela_marcas':
                    $_SERVER['REQUEST_METHOD'] = 'GET';
                    $_GET['action'] = 'marcas';
                    include __DIR__ . '/../api/api.php';
                    break;
                case 'tabela_modelos':
                    $_SERVER['REQUEST_METHOD'] = 'GET';
                    $_GET['action'] = 'modelos';
                    $_GET['marca'] = $_POST['marca'] ?? '';
                    include __DIR__ . '/../api/api.php';
                    break;
                case 'tabela_configuracoes':
                    $_SERVER['REQUEST_METHOD'] = 'GET';
                    $_GET['action'] = 'configuracoes';
                    include __DIR__ . '/../api/api.php';
                    break;
                case 'tabela_salvar_configuracoes':
                    $_SERVER['REQUEST_METHOD'] = 'POST';
                    $_GET['action'] = 'configuracoes';
                    include __DIR__ . '/../api/api.php';
                    break;
            }
        } catch (Exception $e) {
            $output = ob_get_clean();
            echo json_encode(['error' => $e->getMessage(), 'output' => $output]);
            exit;
        }
        $output = ob_get_clean();
        // Se a saída não for JSON, retorna erro
        if (trim($output) && strpos(ltrim($output), '{') !== 0 && strpos(ltrim($output), '[') !== 0) {
            echo json_encode(['error' => 'Saída inesperada da API da tabela', 'output' => $output]);
            exit;
        }
        echo $output;
        exit;
    }
    // --- FIM INTEGRAÇÃO TABELA DE TELAS ---

    // Chamar integração se action começar com 'tabela_'
    if (strpos($action, 'tabela_') === 0) {
        tabela_handle_action($action);
    }

    if (isset($_GET['action']) && $_GET['action'] === 'empresa') {
        require_once '../config/database.php';
        $db = new Database();
        $method = $_SERVER['REQUEST_METHOD'];
        header('Content-Type: application/json');
        try {
            if ($method === 'GET') {
                $stmt = $db->query('SELECT id, nome, cnpj, ie, cep, endereco, bairro, cidade, estado, telefone, email, fator_custo, fator_mao_obra, valor_adicional, logo FROM empresa LIMIT 1');
                $empresa = $stmt->fetch();
                if ($empresa && $empresa['logo']) {
                    $empresa['logo'] = base64_encode($empresa['logo']);
                }
                echo json_encode(['success' => true, 'data' => $empresa]);
            } elseif ($method === 'POST') {
                $nome = $_POST['nome'] ?? '';
                $cnpj = $_POST['cnpj'] ?? '';
                $ie = $_POST['ie'] ?? '';
                $cep = $_POST['cep'] ?? '';
                $endereco = $_POST['endereco'] ?? '';
                $bairro = $_POST['bairro'] ?? '';
                $cidade = $_POST['cidade'] ?? '';
                $estado = $_POST['estado'] ?? '';
                $telefone = $_POST['telefone'] ?? '';
                $email = $_POST['email'] ?? '';
                $fator_custo = $_POST['fator_custo'] ?? 1.2;
                $fator_mao_obra = $_POST['fator_mao_obra'] ?? 1.1;
                $valor_adicional = $_POST['valor_adicional'] ?? 10.0;
                $logo = null;
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $logo = file_get_contents($_FILES['logo']['tmp_name']);
                }
                $stmt = $db->query('SELECT id FROM empresa LIMIT 1');
                $empresa = $stmt->fetch();
                if ($empresa) {
                    // Atualiza
                    if ($logo) {
                        $sql = 'UPDATE empresa SET nome=?, cnpj=?, ie=?, endereco=?, telefone=?, email=?, logo=?, fator_custo=?, fator_mao_obra=?, valor_adicional=?, cep=?, bairro=?, cidade=?, estado=? WHERE id=?';
                        $params = [$nome, $cnpj, $ie, $endereco, $telefone, $email, $logo, $fator_custo, $fator_mao_obra, $valor_adicional, $cep, $bairro, $cidade, $estado, $empresa['id']];
                    } else {
                        $sql = 'UPDATE empresa SET nome=?, cnpj=?, ie=?, endereco=?, telefone=?, email=?, fator_custo=?, fator_mao_obra=?, valor_adicional=?, cep=?, bairro=?, cidade=?, estado=? WHERE id=?';
                        $params = [$nome, $cnpj, $ie, $endereco, $telefone, $email, $fator_custo, $fator_mao_obra, $valor_adicional, $cep, $bairro, $cidade, $estado, $empresa['id']];
                    }
                    $db->query($sql, $params);
                } else {
                    // Insere
                    $sql = 'INSERT INTO empresa (nome, cnpj, ie, endereco, telefone, email, logo, fator_custo, fator_mao_obra, valor_adicional, cep, bairro, cidade, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                    $db->query($sql, [$nome, $cnpj, $ie, $endereco, $telefone, $email, $logo, $fator_custo, $fator_mao_obra, $valor_adicional, $cep, $bairro, $cidade, $estado]);
                }
                echo json_encode(['success' => true]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'buscar_cnpj' && isset($_GET['cnpj'])) {
        $cnpj = preg_replace('/\D/', '', $_GET['cnpj']);
        if (strlen($cnpj) !== 14) {
            echo json_encode(['success' => false, 'error' => 'CNPJ inválido']);
            exit;
        }
        $url = 'https://www.receitaws.com.br/v1/cnpj/' . $cnpj;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $result = curl_exec($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);
        $data = json_decode($result, true);
        if ($curl_error) {
            echo json_encode(['success' => false, 'error' => 'Erro CURL: ' . $curl_error, 'debug' => $result]);
            exit;
        }
        if (isset($data['status']) && $data['status'] === 'OK') {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'error' => $data['message'] ?? 'Erro ao buscar CNPJ', 'debug' => $result]);
        }
        exit;
    }

    switch ($action) {
        case 'login':
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            
            if (empty($email) || empty($senha)) {
                throw new Exception('Email e senha são obrigatórios');
            }
            
            $stmt = $db->query("SELECT * FROM usuarios WHERE email = ?", [$email]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($senha, $user['senha'])) {
                throw new Exception('Email ou senha incorretos');
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_type'] = $user['tipo'];
            
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'nome' => $user['nome'],
                    'email' => $user['email'],
                    'tipo' => $user['tipo']
                ]
            ]);
            break;
            
        case 'logout':
            logout();
            break;
            
        case 'check':
            if (isLoggedIn()) {
                echo json_encode([
                    'success' => true,
                    'user' => getCurrentUser()
                ]);
            } else {
                echo json_encode(['success' => false]);
            }
            break;
            
        default:
            throw new Exception('Ação não reconhecida');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>