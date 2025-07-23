<?php
require_once 'includes/auth.php';
requireLogin();
// Verificar permissão de administrador
if (!hasPermission('manage')) {
    header('Location: index.php');
    exit;
}
$currentPage = 'usuarios';
$mainHeader = '<h1>Usuários</h1>';
ob_start();
?>
<!-- Conteúdo específico da página de usuários -->
<div id="usuarios-content"></div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script src="assets/js/pages/usuarios-production.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Usuarios !== 'undefined') {
            Usuarios.render();
        }
    });
</script> 