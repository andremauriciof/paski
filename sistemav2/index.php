<?php
require_once 'includes/auth.php';
requireLogin();
$currentPage = 'dashboard';
$mainHeader = '<h1>Dashboard</h1>';
ob_start();
?>
<!-- Conteúdo específico do dashboard -->
<div id="dashboard-content"></div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script src="assets/js/pages/dashboard-production.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Dashboard !== 'undefined') {
            Dashboard.render();
        }
    });
</script>