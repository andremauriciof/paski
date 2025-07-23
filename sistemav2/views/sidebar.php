<?php
// Buscar logo da empresa
$logoBase64Sidebar = null;
try {
    require_once __DIR__ . '/../config/database.php';
    $dbLogoSidebar = new Database();
    $stmtLogoSidebar = $dbLogoSidebar->query('SELECT logo FROM empresa LIMIT 1');
    $rowLogoSidebar = $stmtLogoSidebar->fetch();
    if ($rowLogoSidebar && !empty($rowLogoSidebar['logo'])) {
        $logoBase64Sidebar = 'data:image/png;base64,' . base64_encode($rowLogoSidebar['logo']);
    }
} catch (Exception $e) {}
?>
<aside class="sidebar-modern d-flex flex-column justify-content-between" id="sidebar">
    <div>
        <div class="sidebar-modern-header text-center py-4">
            <?php if ($logoBase64Sidebar): ?>
                <img src="<?php echo $logoBase64Sidebar; ?>" alt="Logo" class="sidebar-logo mb-2" style="max-width:80px;max-height:80px;border-radius:16px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
            <?php else: ?>
                <div class="sidebar-logo-placeholder mb-2"><i class="fas fa-tools fa-2x"></i></div>
            <?php endif; ?>
            <h4 class="fw-bold mb-0" style="font-size:1.1rem;">SISTEMA ASL</h4>
            <button class="btn btn-link text-white sidebar-modern-toggle mt-2" id="sidebar-toggle" title="Expandir/retrair menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <nav class="sidebar-modern-nav mt-4">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 <?php if (isset($currentPage) && $currentPage === 'dashboard') echo 'active'; ?>" href="index.php">
                        <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 <?php if (isset($currentPage) && $currentPage === 'clientes') echo 'active'; ?>" href="clientes.php">
                        <i class="fas fa-users"></i> <span>Clientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 collapsed" data-bs-toggle="collapse" href="#submenu-cadastros" role="button" aria-expanded="<?php echo (isset($currentPage) && in_array($currentPage, ['equipamentos','tipos','marcas','modelos','checklist-admin'])) ? 'true' : 'false'; ?>" aria-controls="submenu-cadastros">
                        <i class="fas fa-folder-open"></i> <span>Cadastros</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse <?php if (isset($currentPage) && in_array($currentPage, ['equipamentos','tipos','marcas','modelos','checklist-admin'])) echo 'show'; ?>" id="submenu-cadastros">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2 <?php if (isset($currentPage) && $currentPage === 'equipamentos') echo 'active'; ?>" href="equipamentos.php">
                                    <i class="fas fa-laptop"></i> <span>Equipamentos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2 <?php if (isset($currentPage) && $currentPage === 'tipos') echo 'active'; ?>" href="tipos.php">
                                    <i class="fas fa-tags"></i> <span>Tipos de Equipamento</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2 <?php if (isset($currentPage) && $currentPage === 'marcas') echo 'active'; ?>" href="marcas.php">
                                    <i class="fas fa-industry"></i> <span>Marcas</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2 <?php if (isset($currentPage) && $currentPage === 'modelos') echo 'active'; ?>" href="modelos.php">
                                    <i class="fas fa-cubes"></i> <span>Modelos</span>
                                </a>
                            </li>
                            <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2 <?php if (isset($currentPage) && $currentPage === 'checklist-admin') echo 'active'; ?>" href="checklist-admin.php">
                                    <i class="fas fa-clipboard-check"></i> <span>Checklist</span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 <?php if (isset($currentPage) && $currentPage === 'ordens') echo 'active'; ?>" href="ordens.php">
                        <i class="fas fa-clipboard-list"></i> <span>Ordens de Serviço</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 <?php if (isset($currentPage) && $currentPage === 'tabela') echo 'active'; ?>" href="tabela.php">
                        <i class="fas fa-table"></i> <span>Tabela de Telas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 <?php if (isset($currentPage) && $currentPage === 'empresa') echo 'active'; ?>" href="empresa.php">
                        <i class="fas fa-building"></i> <span>Empresa</span>
                    </a>
                </li>
                <?php if (hasPermission('manage')): ?>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 <?php if (isset($currentPage) && $currentPage === 'usuarios') echo 'active'; ?>" href="usuarios.php">
                        <i class="fas fa-user-cog"></i> <span>Usuários</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if ($_SESSION['user_type'] === 'admin' || $_SESSION['user_type'] === 'consulta'): ?>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 <?php if (isset($currentPage) && $currentPage === 'relatorios') echo 'active'; ?>" href="relatorios.php">
                        <i class="fas fa-chart-bar"></i> <span>Relatórios</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <div class="sidebar-modern-footer text-center py-3">
        <small class="text-white-50">&copy; <?php echo date('Y'); ?> Sistema ASL</small>
    </div>
</aside>
<button class="btn btn-primary sidebar-modern-open" id="sidebar-open" style="display:none;position:fixed;top:20px;left:20px;z-index:1100;">
    <i class="fas fa-bars"></i>
</button>
<style>
.sidebar-modern {
    width: 250px;
    background: linear-gradient(180deg, #0055c7 80%, #003a8c 100%);
    color: #fff;
    min-height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1050;
    box-shadow: 2px 0 12px rgba(30,58,138,0.08);
    transition: transform 0.3s cubic-bezier(.4,2,.6,1), width 0.3s;
    overflow-y: auto;
}
.sidebar-modern.collapsed {
    transform: translateX(-100%);
}
.sidebar-modern-header {
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.sidebar-modern-toggle {
    color: #fff;
    font-size: 1.2rem;
    background: none;
    border: none;
    outline: none;
    transition: color 0.2s;
}
.sidebar-modern-toggle:hover {
    color: #a5b4fc;
}
.sidebar-modern-nav .nav-link {
    color: rgba(255,255,255,0.85);
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 0.25rem;
    font-weight: 500;
    transition: background 0.2s, color 0.2s;
    display: flex;
    align-items: center;
}
.sidebar-modern-nav .nav-link.active, .sidebar-modern-nav .nav-link:hover {
    background: #eaf1fb;
    color: #0055c7;
}
/* Submenu hover/active: cor mais clara */
.sidebar-modern-nav .nav .nav-link.active,
.sidebar-modern-nav .nav .nav-link:hover {
    background: #f5f8fd;
    color: #0055c7;
}
.sidebar-modern-nav .nav-link i {
    min-width: 22px;
    text-align: center;
    font-size: 1.1rem;
}
.sidebar-modern-footer {
    border-top: 1px solid rgba(255,255,255,0.08);
}
@media (max-width: 900px) {
    .sidebar-modern {
        width: 200px;
    }
}
@media (max-width: 768px) {
    .sidebar-modern {
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        z-index: 1200;
        transform: translateX(-100%);
    }
    .sidebar-modern.show {
        transform: translateX(0);
    }
    .sidebar-modern-open {
        display: block !important;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarOpen = document.getElementById('sidebar-open');
    // Toggle sidebar (desktop)
    sidebarToggle?.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
    });
    // Toggle sidebar (mobile)
    sidebarOpen?.addEventListener('click', function() {
        sidebar.classList.add('show');
        sidebarOpen.style.display = 'none';
    });
    // Fechar sidebar ao clicar fora (mobile)
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && sidebar.classList.contains('show')) {
            if (!sidebar.contains(e.target) && !sidebarOpen.contains(e.target)) {
                sidebar.classList.remove('show');
                sidebarOpen.style.display = 'block';
            }
        }
    });
    // Mostrar botão de abrir no mobile
    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
        sidebarOpen.style.display = 'block';
    }
});
</script> 