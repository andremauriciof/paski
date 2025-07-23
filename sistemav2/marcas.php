<?php
require_once 'includes/auth.php';
requireLogin();
$currentPage = 'marcas';
$mainHeader = '<h1>Marcas</h1>';
ob_start();
?>
<!-- Conteúdo específico da página de marcas -->
<div id="page-content"></div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script src="assets/js/pages/marcas.js"></script>
<script>
    window.addEventListener('load', function() {
        if (typeof Marcas !== 'undefined') {
            Marcas.render();
        }
    });
</script> 