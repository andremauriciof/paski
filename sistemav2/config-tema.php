<?php
require_once 'includes/auth.php';
requireLogin();
$currentPage = 'config-tema';
$mainHeader = '<h1>Configuração de Tema</h1>';
ob_start();
?>
<div class="container mt-4">
    <h2><i class="fas fa-palette"></i> Configuração de Tema</h2>
    <div class="card mt-3">
      <div class="card-body">
        <form id="formTema">
          <div class="mb-3">
            <label class="form-label">Escolha o tema:</label>
            <div>
              <input type="radio" name="tema" id="temaClaro" value="light">
              <label for="temaClaro">Claro</label>
              <input type="radio" name="tema" id="temaEscuro" value="dark" class="ms-3">
              <label for="temaEscuro">Escuro</label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Salvar Tema</button>
        </form>
        <div id="temaMsg" class="mt-3"></div>
      </div>
    </div>
</div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script>
  window.usuarioId = <?php echo json_encode($_SESSION['usuario_id']); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 