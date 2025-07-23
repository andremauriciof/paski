<?php
require_once 'config/database.php';

// Iniciar sessão
session_start();

echo "<h2>Debug do Sistema de Login</h2>";

try {
    $db = new Database();
    echo "<p style='color: green;'>✓ Conexão com banco de dados estabelecida</p>";
    
    // Testar se a tabela usuarios existe
    $stmt = $db->query("SHOW TABLES LIKE 'usuarios'");
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "<p style='color: green;'>✓ Tabela 'usuarios' existe</p>";
        
        // Verificar estrutura da tabela
        $stmt = $db->query("DESCRIBE usuarios");
        $columns = $stmt->fetchAll();
        
        echo "<h3>Estrutura da tabela usuarios:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Verificar usuários existentes (sem mostrar senhas)
        $stmt = $db->query("SELECT id, nome, email, tipo, LENGTH(senha) as senha_length FROM usuarios");
        $users = $stmt->fetchAll();
        
        echo "<h3>Usuários cadastrados:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th><th>Tamanho da Senha</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['nome']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['tipo']}</td>";
            echo "<td>{$user['senha_length']} caracteres</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p style='color: red;'>✗ Tabela 'usuarios' não existe</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro: " . $e->getMessage() . "</p>";
}

// Testar função password_verify
echo "<h3>Teste da função password_verify:</h3>";
$testPassword = "teste123";
$hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
$verifyResult = password_verify($testPassword, $hashedPassword);

echo "<p>Senha de teste: '{$testPassword}'</p>";
echo "<p>Hash gerado: " . substr($hashedPassword, 0, 20) . "...</p>";
echo "<p>Verificação: " . ($verifyResult ? "✓ Sucesso" : "✗ Falha") . "</p>";

// Verificar se session está funcionando
echo "<h3>Teste de Sessão:</h3>";
$_SESSION['test'] = 'teste_sessao';
echo "<p>Valor da sessão: " . ($_SESSION['test'] ?? 'não definido') . "</p>";

echo "<hr>";
echo "<p><strong>Para testar o login, use o formulário abaixo:</strong></p>";
?>

<form method="POST" action="test_login.php">
    <h3>Teste de Login</h3>
    <p>
        <label>Email: <input type="email" name="email" required></label>
    </p>
    <p>
        <label>Senha: <input type="password" name="senha" required></label>
    </p>
    <p>
        <button type="submit">Testar Login</button>
    </p>
</form> 