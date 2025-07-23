<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    echo "<h2>Teste de Login - Resultado</h2>";
    echo "<p><strong>Email informado:</strong> {$email}</p>";
    echo "<p><strong>Senha informada:</strong> " . str_repeat('*', strlen($senha)) . "</p>";
    
    try {
        $db = new Database();
        
        // Passo 1: Verificar se o usuário existe
        echo "<h3>Passo 1: Verificando se o usuário existe</h3>";
        $stmt = $db->query("SELECT * FROM usuarios WHERE email = ?", [$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            echo "<p style='color: red;'>✗ Usuário não encontrado com o email: {$email}</p>";
        } else {
            echo "<p style='color: green;'>✓ Usuário encontrado</p>";
            echo "<p><strong>Dados do usuário:</strong></p>";
            echo "<ul>";
            echo "<li>ID: {$user['id']}</li>";
            echo "<li>Nome: {$user['nome']}</li>";
            echo "<li>Email: {$user['email']}</li>";
            echo "<li>Tipo: {$user['tipo']}</li>";
            echo "<li>Tamanho da senha no banco: " . strlen($user['senha']) . " caracteres</li>";
            echo "</ul>";
            
            // Passo 2: Verificar a senha
            echo "<h3>Passo 2: Verificando a senha</h3>";
            
            // Verificar se a senha no banco parece ser um hash
            $isHash = password_get_info($user['senha']);
            echo "<p><strong>Informações do hash:</strong></p>";
            echo "<ul>";
            echo "<li>É um hash válido: " . ($isHash['algoName'] !== 'unknown' ? 'Sim' : 'Não') . "</li>";
            if ($isHash['algoName'] !== 'unknown') {
                echo "<li>Algoritmo: {$isHash['algoName']}</li>";
                echo "<li>Custo: {$isHash['options']['cost']}</li>";
            }
            echo "</ul>";
            
            // Tentar verificar a senha
            $passwordValid = password_verify($senha, $user['senha']);
            echo "<p><strong>Resultado da verificação:</strong> " . ($passwordValid ? "✓ Senha correta" : "✗ Senha incorreta") . "</p>";
            
            if ($passwordValid) {
                echo "<h3>Passo 3: Configurando sessão</h3>";
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['tipo'];
                
                echo "<p style='color: green;'>✓ Sessão configurada com sucesso</p>";
                echo "<p><strong>Dados da sessão:</strong></p>";
                echo "<ul>";
                echo "<li>user_id: {$_SESSION['user_id']}</li>";
                echo "<li>user_name: {$_SESSION['user_name']}</li>";
                echo "<li>user_email: {$_SESSION['user_email']}</li>";
                echo "<li>user_type: {$_SESSION['user_type']}</li>";
                echo "</ul>";
                
                echo "<p style='color: green; font-weight: bold;'>🎉 LOGIN REALIZADO COM SUCESSO!</p>";
            } else {
                echo "<p style='color: red; font-weight: bold;'>❌ FALHA NA AUTENTICAÇÃO</p>";
                
                // Mostrar os primeiros caracteres do hash para debug
                echo "<p><strong>Debug - Primeiros 20 caracteres do hash no banco:</strong> " . substr($user['senha'], 0, 20) . "...</p>";
                
                // Se a senha não parece ser um hash, pode ser texto puro
                if ($isHash['algoName'] === 'unknown') {
                    echo "<p style='color: orange;'><strong>ATENÇÃO:</strong> A senha no banco não parece ser um hash. Pode estar em texto puro.</p>";
                    echo "<p>Comparação direta: " . ($senha === $user['senha'] ? "✓ Senha correta (texto puro)" : "✗ Senha incorreta") . "</p>";
                }
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Erro: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
    echo "<p><a href='debug_login.php'>← Voltar ao debug</a></p>";
} else {
    header('Location: debug_login.php');
    exit;
}
?> 