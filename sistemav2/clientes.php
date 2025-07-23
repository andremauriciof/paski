<?php
require_once 'includes/auth.php';
requireLogin();
$currentPage = 'clientes';
$mainHeader = '<h1>Clientes</h1>';
ob_start();
?>
<!-- Conteúdo específico da página de clientes -->
<div id="clientes-content"></div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script src="assets/js/pages/clientes-production.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Clientes !== 'undefined') {
            Clientes.render();
        }
    });
</script>
