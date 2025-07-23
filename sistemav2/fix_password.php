<?php
require_once 'config/database.php';

echo "<h2>Correção de Senhas - Sistema ASL</h2>";

try {
    $db = new Database();
    
    // Buscar usuários com senhas não criptografadas
    $stmt = $db->query("SELECT id, nome, email, senha FROM usuarios");
    $users = $stmt->fetchAll();
    
    echo "<h3>Usuários encontrados:</h3>";
    
    foreach ($users as $user) {
        echo "<p><strong>Usuário:</strong> {$user['nome']} ({$user['email']})</p>";
        echo "<p><strong>Tamanho da senha atual:</strong> " . strlen($user['senha']) . " caracteres</p>";
        
        // Verificar se a senha já está criptografada
        $isHash = password_get_info($user['senha']);
        
        if ($isHash['algoName'] === 'unknown') {
            echo "<p style='color: orange;'>⚠️ Senha não está criptografada</p>";
            
            // Criptografar a senha
            $hashedPassword = password_hash($user['senha'], PASSWORD_DEFAULT);
            
            // Atualizar no banco
            $updateStmt = $db->query("UPDATE usuarios SET senha = ? WHERE id = ?", [$hashedPassword, $user['id']]);
            
            echo "<p style='color: green;'>✅ Senha criptografada e atualizada no banco</p>";
            echo "<p><strong>Novo hash:</strong> " . substr($hashedPassword, 0, 20) . "...</p>";
        } else {
            echo "<p style='color: green;'>✅ Senha já está criptografada</p>";
        }
        
        echo "<hr>";
    }
    
    echo "<h3>Teste de verificação:</h3>";
    
    // Testar se a senha funciona agora
    $testUser = $users[0]; // Primeiro usuário
    $testPassword = $testUser['senha']; // Senha original (antes da criptografia)
    
    // Buscar a senha atualizada
    $stmt = $db->query("SELECT senha FROM usuarios WHERE id = ?", [$testUser['id']]);
    $currentPassword = $stmt->fetch()['senha'];
    
    $isValid = password_verify($testPassword, $currentPassword);
    
    echo "<p><strong>Teste com usuário:</strong> {$testUser['nome']}</p>";
    echo "<p><strong>Senha original:</strong> {$testPassword}</p>";
    echo "<p><strong>Verificação:</strong> " . ($isValid ? "✅ Sucesso" : "❌ Falha") . "</p>";
    
    if ($isValid) {
        echo "<p style='color: green; font-weight: bold;'>🎉 Correção realizada com sucesso! O login deve funcionar agora.</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Ainda há problemas com a verificação.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='login.php'>← Ir para o login</a></p>";
echo "<p><a href='debug_login.php'>← Voltar ao debug</a></p>";
?> 