<?php
require_once 'includes/auth.php';
requireLogin();
$currentPage = 'ordens';
$mainHeader = '<h1>Ordens de Serviço</h1>';
ob_start();
?>
<!-- Conteúdo específico da página de ordens de serviço -->
<div id="ordens-content"></div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script src="assets/js/pages/ordens-production.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Ordens !== 'undefined') {
            Ordens.render();
        }
    });
</script> 