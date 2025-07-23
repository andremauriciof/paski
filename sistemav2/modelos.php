<?php
require_once 'includes/auth.php';
requireLogin();
$currentPage = 'modelos';
$mainHeader = '<h1>Modelos</h1>';
ob_start();
?>
<!-- Conteúdo específico da página de modelos -->
<div id="page-content"></div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script src="assets/js/pages/modelos.js"></script>
<script>
    window.addEventListener('load', function() {
        if (typeof Modelos !== 'undefined') {
            Modelos.render();
        }
    });
</script> 