<?php
require_once 'config/database.php';

echo "<h2>Teste do Sistema de Checklist</h2>";

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    echo "<h3>1. Verificando se a tabela checklist_items existe:</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'checklist_items'");
    $tableExists = $stmt->rowCount() > 0;
    echo $tableExists ? "✅ Tabela checklist_items existe" : "❌ Tabela checklist_items NÃO existe";
    echo "<br><br>";
    
    if ($tableExists) {
        echo "<h3>2. Verificando dados na tabela checklist_items:</h3>";
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM checklist_items");
        $count = $stmt->fetch()['total'];
        echo "Total de itens: $count<br>";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT * FROM checklist_items LIMIT 5");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<h4>Primeiros 5 itens:</h4>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Descrição</th><th>Categoria</th></tr>";
            foreach ($items as $item) {
                echo "<tr>";
                echo "<td>{$item['id']}</td>";
                echo "<td>{$item['descricao']}</td>";
                echo "<td>{$item['categoria']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<h3>3. Verificando se a tabela os_checklist_marks existe:</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'os_checklist_marks'");
    $tableExists = $stmt->rowCount() > 0;
    echo $tableExists ? "✅ Tabela os_checklist_marks existe" : "❌ Tabela os_checklist_marks NÃO existe";
    echo "<br><br>";
    
    if ($tableExists) {
        echo "<h3>4. Verificando dados na tabela os_checklist_marks:</h3>";
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM os_checklist_marks");
        $count = $stmt->fetch()['total'];
        echo "Total de marcas: $count<br>";
    }
    
    echo "<h3>5. Testando API do checklist:</h3>";
    echo "<a href='api/checklist.php?action=get_items' target='_blank'>Testar API get_items</a><br>";
    
} catch (Exception $e) {
    echo "<h3>❌ Erro:</h3>";
    echo $e->getMessage();
}
?> 