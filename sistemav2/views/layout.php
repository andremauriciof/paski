<script>
  window.usuarioId = <?= $_SESSION['usuario_id'] ?? 1 ?>;
</script>
<script src="assets/js/config-tema.js"></script>
<?php
require_once __DIR__ . '/../config/database.php';
if (!isset($mainHeader)) $mainHeader = '';
if (!isset($pageContent)) $pageContent = '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema ASL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background-color: #181c23;">
<?php
$logoBase64Header = null;
try {
    $dbLogoHeader = new Database();
    $stmtLogoHeader = $dbLogoHeader->query('SELECT logo FROM empresa LIMIT 1');
    $rowLogoHeader = $stmtLogoHeader->fetch();
    if ($rowLogoHeader && !empty($rowLogoHeader['logo'])) {
        $logoBase64Header = 'data:image/png;base64,' . base64_encode($rowLogoHeader['logo']);
    }
} catch (Exception $e) {}
?>
<div class="background: #fff; width: 100vw; min-height: 64px; box-shadow: 0 2px 8px rgba(0,85,199,0.08); border-bottom: 1.5px solid #e0e7ef;"></div>
  <div class="container-fluid d-flex align-items-center justify-content-between px-3" style="height: 64px;">
    <div class="d-flex align-items-center gap-2">
      <a class="d-flex align-items-center text-decoration-none" href="index.php">
        <?php if ($logoBase64Header): ?>
          <img src="<?php echo $logoBase64Header; ?>" alt="Logo" style="max-height:54px;max-width:140px;border-radius:8px;box-shadow:none !important;background:transparent !important;padding:4px;">
        <?php else: ?>
          <i class="fas fa-tools fa-lg" style="color:#0055c7;"></i>
        <?php endif; ?>
      </a>
    </div>
    <nav class="menu-central rounded-3 px-3 py-1 d-none d-lg-flex align-items-center" style="background:transparent; border:none; box-shadow:none;">
      <ul class="nav gap-2">
        <li class="nav-item">
          <a class="nav-link menu-link <?php if (isset($currentPage) && $currentPage === 'dashboard') echo 'active'; ?>" href="index.php">
            <i class="fas fa-tachometer-alt"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link menu-link <?php if (isset($currentPage) && $currentPage === 'clientes') echo 'active'; ?>" href="clientes.php">
            <i class="fas fa-users"></i> Clientes
          </a>
        </li>
        <li class="nav-item dropdown" id="menu-cadastros-dropdown">
          <a class="nav-link menu-link dropdown-toggle <?php if (isset($currentPage) && in_array($currentPage, ['equipamentos','tipos','marcas','modelos','checklist-admin'])) echo 'active'; ?>" href="#" id="navbarDropdownCadastros" role="button" aria-expanded="false">
            <i class="fas fa-folder-open"></i> Cadastros
          </a>
          <ul class="dropdown-menu border-0 mt-2" aria-labelledby="navbarDropdownCadastros">
            <li><a class="dropdown-item text-primary <?php if (isset($currentPage) && $currentPage === 'equipamentos') echo 'active'; ?>" href="equipamentos.php"><i class="fas fa-laptop"></i> Equipamentos</a></li>
            <li><a class="dropdown-item text-primary <?php if (isset($currentPage) && $currentPage === 'tipos') echo 'active'; ?>" href="tipos.php"><i class="fas fa-tags"></i> Tipos de Equipamento</a></li>
            <li><a class="dropdown-item text-primary <?php if (isset($currentPage) && $currentPage === 'marcas') echo 'active'; ?>" href="marcas.php"><i class="fas fa-industry"></i> Marcas</a></li>
            <li><a class="dropdown-item text-primary <?php if (isset($currentPage) && $currentPage === 'modelos') echo 'active'; ?>" href="modelos.php"><i class="fas fa-cubes"></i> Modelos</a></li>
            <?php if (function_exists('isAdmin') && isAdmin()): ?>
            <li><a class="dropdown-item text-primary <?php if (isset($currentPage) && $currentPage === 'checklist-admin') echo 'active'; ?>" href="checklist-admin.php"><i class="fas fa-clipboard-check"></i> Checklist</a></li>
            <?php endif; ?>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link menu-link <?php if (isset($currentPage) && $currentPage === 'ordens') echo 'active'; ?>" href="ordens.php">
            <i class="fas fa-clipboard-list"></i> Ordens
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link menu-link <?php if (isset($currentPage) && $currentPage === 'tabela') echo 'active'; ?>" href="tabela.php">
            <i class="fas fa-table"></i> Tabela
          </a>
        </li>
        <li class="nav-item dropdown" id="menu-configuracoes-dropdown">
          <a class="nav-link menu-link dropdown-toggle <?php if (isset($currentPage) && in_array($currentPage, ['empresa','config-tema'])) echo 'active'; ?>" href="#" id="navbarDropdownConfiguracoes" role="button" aria-expanded="false">
            <i class="fas fa-cogs"></i> Configurações
          </a>
          <ul class="dropdown-menu border-0 mt-2" aria-labelledby="navbarDropdownConfiguracoes">
            <li><a class="dropdown-item text-primary <?php if (isset($currentPage) && $currentPage === 'empresa') echo 'active'; ?>" href="empresa.php"><i class="fas fa-building"></i> Empresa</a></li>
            <li><a class="dropdown-item text-primary <?php if (isset($currentPage) && $currentPage === 'config-tema') echo 'active'; ?>" href="config-tema.php"><i class="fas fa-palette"></i> Tema</a></li>
          </ul>
        </li>
        <?php if (function_exists('hasPermission') && hasPermission('manage')): ?>
        <li class="nav-item">
          <a class="nav-link menu-link <?php if (isset($currentPage) && $currentPage === 'usuarios') echo 'active'; ?>" href="usuarios.php">
            <i class="fas fa-user-cog"></i> Usuários
          </a>
        </li>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] === 'admin' || $_SESSION['user_type'] === 'consulta')): ?>
        <li class="nav-item">
          <a class="nav-link menu-link <?php if (isset($currentPage) && $currentPage === 'relatorios') echo 'active'; ?>" href="relatorios.php">
            <i class="fas fa-chart-bar"></i> Relatórios
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </nav>
    <div class="d-flex align-items-center gap-3">
      <a href="#" class="text-primary" title="Usuário"><i class="fas fa-user fa-lg"></i></a>
      <a href="#" class="text-info" title="Chat"><i class="fas fa-comments fa-lg"></i></a>
      <a href="#" class="text-info" id="btnSobre" title="Sobre">
        <i class="fas fa-info-circle fa-lg" style="color: #007bff;"></i>
      </a>
      <a href="logout.php" class="text-danger" title="Sair"><i class="fas fa-sign-out-alt fa-lg"></i></a>
    </div>
    <!-- Menu mobile -->
    <div class="d-lg-none">
      <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
        <i class="fas fa-bars"></i>
      </button>
      <div class="offcanvas offcanvas-end bg-white text-dark" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="nav flex-column gap-2">
            <li class="nav-item"><a class="nav-link text-primary" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-primary" href="clientes.php"><i class="fas fa-users"></i> Clientes</a></li>
            <li class="nav-item"><a class="nav-link text-primary" href="equipamentos.php"><i class="fas fa-laptop"></i> Equipamentos</a></li>
            <li class="nav-item"><a class="nav-link text-primary" href="tipos.php"><i class="fas fa-tags"></i> Tipos de Equipamento</a></li>
            <li class="nav-item"><a class="nav-link text-primary" href="marcas.php"><i class="fas fa-industry"></i> Marcas</a></li>
            <li class="nav-item"><a class="nav-link text-primary" href="modelos.php"><i class="fas fa-cubes"></i> Modelos</a></li>
            <?php if (function_exists('isAdmin') && isAdmin()): ?>
            <li class="nav-item"><a class="nav-link text-primary" href="checklist-admin.php"><i class="fas fa-clipboard-check"></i> Checklist</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link text-primary" href="ordens.php"><i class="fas fa-clipboard-list"></i> Ordens</a></li>
            <li class="nav-item"><a class="nav-link text-primary" href="tabela.php"><i class="fas fa-table"></i> Tabela</a></li>
            <li class="nav-item"><a class="nav-link text-primary" href="empresa.php"><i class="fas fa-building"></i> Empresa</a></li>
            <?php if (function_exists('hasPermission') && hasPermission('manage')): ?>
            <li class="nav-item"><a class="nav-link text-primary" href="usuarios.php"><i class="fas fa-user-cog"></i> Usuários</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] === 'admin' || $_SESSION['user_type'] === 'consulta')): ?>
            <li class="nav-item"><a class="nav-link text-primary" href="relatorios.php"><i class="fas fa-chart-bar"></i> Relatórios</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="#" id="btnSobreMobile" title="Sobre">
              <i class="fas fa-info-circle" style="color: #007bff;"></i>
            </a></li>
            <li class="nav-item"><a class="nav-link text-primary" href="#"><i class="fas fa-user"></i> Usuário</a></li>
            <li class="nav-item"><a class="nav-link text-info" href="#"><i class="fas fa-comments"></i> Chat</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
    .menu-central {
      background: transparent !important;
      box-shadow: none !important;
      min-width: 1100px !important;
      padding-left: 32px;
      padding-right: 32px;
      border: none !important;
    }
    .menu-central .nav {
      gap: 2.5rem !important;
    }
    .menu-link {
      color: #0055c7 !important;
      font-weight: 500;
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1.2rem !important;
      border-radius: 6px;
      transition: background 0.2s, color 0.2s;
      border: none !important;
      box-shadow: none !important;
    }
    .menu-link.active, .menu-link:hover {
      background: #0055c7 !important;
      color: #fff !important;
      border: none !important;
      box-shadow: none !important;
      background-image: none !important;
    }
    .menu-link i {
      color: #0055c7 !important;
      min-width: 22px;
      text-align: center;
      font-size: 1.1rem;
      border: none !important;
      box-shadow: none !important;
    }
    .menu-link.active i, .menu-link:hover i {
      color: #fff !important;
      border: none !important;
      box-shadow: none !important;
    }
    .menu-central .nav-item.dropdown:hover .dropdown-menu {
      display: block !important;
      margin-top: 0.5rem;
    }
    .menu-central .dropdown-menu {
      transition: none;
    }
    .menu-central .dropdown-menu.show {
      display: block !important;
      opacity: 1 !important;
      visibility: visible !important;
      z-index: 9999 !important;
      z-index: 1055 !important;
    }
    .container-fluid, .page-content {
      overflow: visible !important;
    }
    .d-flex.align-items-center.gap-3 {
      margin-right: 24px;
    }
    @media (max-width: 991px) {
      .menu-central { display: none !important; }
    }
</style>
<div class="container-fluid" style="padding-top: 30px;">
  <div class="page-content" id="page-content">
    <?php echo $pageContent; ?>
  </div>
</div>
<!-- Rodapé do sistema -->
<footer class="text-center" style="background: #181c23; color: #bbb; font-size: 0.90em; padding: 8px 0 4px 0;">
  Desenvolvido por André Maurício Ferreira - ASL Sistemas &copy; <?php echo date('Y'); ?>
</footer>
<?php
require_once __DIR__ . '/../includes/auth.php';
$user = getCurrentUser();
$perms = [
    'read' => hasPermission('read'),
    'write' => hasPermission('write'),
    'delete' => hasPermission('delete'),
    'manage' => hasPermission('manage'),
    'financeiro' => hasPermission('financeiro')
];
?>
<script>
    window.USER_PERMISSIONS = <?php echo json_encode($perms); ?>;
</script>
<script>
  window.usuarioId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;
</script>
<script src="assets/js/app-production.js"></script>
<script src="assets/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.menu-central .nav-item.dropdown').forEach(function(item) {
    const dropdown = item.querySelector('.dropdown-menu');
    const toggle = item.querySelector('.dropdown-toggle');
    let timeout;

    function openMenu() {
      if (dropdown) {
        dropdown.classList.add('show');
        toggle.setAttribute('aria-expanded', 'true');
      }
      clearTimeout(timeout);
    }

    function closeMenu() {
      timeout = setTimeout(function() {
        if (dropdown) {
          dropdown.classList.remove('show');
          toggle.setAttribute('aria-expanded', 'false');
        }
      }, 150); // Pequeno delay para permitir mover o mouse
    }

    item.addEventListener('mouseenter', openMenu);
    item.addEventListener('mouseleave', closeMenu);

    if (dropdown) {
      dropdown.addEventListener('mouseenter', openMenu);
      dropdown.addEventListener('mouseleave', closeMenu);
    }
  });
});
</script>
<!-- Modal Sobre -->
<div class="modal fade" id="modalSobre" tabindex="-1" aria-labelledby="modalSobreLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title w-100 text-center" id="modalSobreLabel">Sobre o Sistema</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body text-center">
        <img src="assets/img/logoasl.png" alt="Logo ASL Sistemas" style="max-width: 180px; margin-bottom: 18px;">
        <h5 class="mb-2" style="color: #4f8cff;">Bem-vindo ao Sistema ASL!</h5>
        <p class="mb-2">Este sistema foi desenvolvido com dedicação para facilitar a gestão de ordens de serviço, clientes e equipamentos, tornando o seu dia a dia mais prático e eficiente.</p>
        <div class="mb-2"><strong>Desenvolvedor:</strong> André Maurício Ferreira</div>
        <div class="mb-2"><strong>Empresa:</strong> ASL Sistemas</div>
        <div class="mb-2"><strong>Ano de desenvolvimento:</strong> 2025</div>
        <div class="mb-2"><strong>Contato:</strong> <a href="mailto:andremauricioferreira@outlook.com">andremauricioferreira@outlook.com</a></div>
        <p class="mt-3" style="font-size: 0.95em; color: #888;">Conte sempre conosco para inovar e transformar sua gestão!</p>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var sobreBtn = document.getElementById('btnSobre');
    var sobreBtnMobile = document.getElementById('btnSobreMobile');
    var sobreModal = new bootstrap.Modal(document.getElementById('modalSobre'));
    if (sobreBtn) sobreBtn.addEventListener('click', function(e) { e.preventDefault(); sobreModal.show(); });
    if (sobreBtnMobile) sobreBtnMobile.addEventListener('click', function(e) { e.preventDefault(); sobreModal.show(); });
  });
</script>
</body>
</html> 