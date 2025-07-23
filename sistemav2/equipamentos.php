<?php
require_once 'includes/auth.php';
requireLogin();
$currentPage = 'equipamentos';
$mainHeader = '<h1>Equipamentos</h1>';
ob_start();
?>
<!-- Conteúdo específico da página de equipamentos -->
<div id="page-content"></div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script src="assets/js/pages/equipamentos-production.js"></script>
<script>
    window.addEventListener('load', function() {
        if (typeof Equipamentos !== 'undefined') {
            Equipamentos.render();
        }
    });
</script> 