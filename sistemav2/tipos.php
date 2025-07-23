<?php
require_once 'includes/auth.php';
requireLogin();
$currentPage = 'tipos';
$mainHeader = '<h1>Tipos de Equipamento</h1>';
ob_start();
?>
<!-- Conteúdo específico da página de tipos de equipamento -->
<div id="page-content"></div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script src="assets/js/pages/tipos.js"></script>
<script>
    window.addEventListener('load', function() {
        if (typeof Tipos !== 'undefined') {
            Tipos.render();
        }
    });
</script> 