<?php
// Script de debug para verificar configurações
echo "<h2>Debug do Sistema</h2>";

echo "<h3>Configurações do Banco:</h3>";
echo "Host: paski_db.mysql.dbaas.com.br<br>";
echo "Porta: 3306<br>";
echo "Database: paski_db<br>";
echo "Usuário: paski_db<br>";

echo "<h3>Teste de Conexão:</h3>";
try {
    require_once 'config/database.php';
    $db = new Database();
    echo "✅ Conexão estabelecida com sucesso!<br>";
    
    // Testar consulta simples
    $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    echo "✅ Total de usuários: " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<h3>Informações do PHP:</h3>";
echo "Versão PHP: " . phpversion() . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Disponível' : '❌ Não disponível') . "<br>";

echo "<h3>Estrutura de Arquivos:</h3>";
$files = [
    'config/database.php',
    'includes/auth.php',
    'api/auth.php',
    'api/clientes.php',
    'login.php',
    'index.php'
];

foreach ($files as $file) {
    echo $file . ": " . (file_exists($file) ? '✅' : '❌') . "<br>";
}
?>