<?php
require_once 'includes/auth.php';
requireLogin();
$currentPage = 'relatorios';
$mainHeader = '<h1>Relatórios</h1>';
ob_start();
?>
<!-- Conteúdo específico da página de relatórios -->
<div id="relatorios-content"></div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script src="assets/js/pages/relatorios-production.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Relatorios !== 'undefined') {
            Relatorios.render();
        }
    });
</script> 