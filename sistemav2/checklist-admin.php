<?php
require_once 'includes/auth.php';
requireLogin();
if (!isAdmin()) {
    http_response_code(403);
    echo '<div class="alert alert-danger">Acesso restrito a administradores.</div>';
    exit;
}
$currentPage = 'checklist-admin';
$mainHeader = '<h1>Administração do Checklist</h1>';
ob_start();
?>
<!-- Conteúdo específico da página de administração do checklist -->
<div id="checklist-admin-content"></div>
<!-- Campo de cliente para autocomplete -->
<div style="position: relative;">
    <input type="text" class="form-control" id="ordem-cliente-nome" placeholder="Buscar cliente por nome ou CPF/CNPJ" autocomplete="off" required>
    <input type="hidden" id="ordem-cliente" name="ordem-cliente">
</div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script src="assets/js/pages/checklist-admin.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof ChecklistAdmin !== 'undefined') {
            ChecklistAdmin.render();
        }
    });
</script> 