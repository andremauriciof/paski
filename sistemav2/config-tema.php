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
        <hr>
        <form id="formCores">
          <h5 class="mb-3"><i class="fas fa-sliders-h"></i> Personalização de Cores</h5>
          <div class="row g-3">
            <div class="col-md-4">
              <label>Cor Primária:</label>
              <input type="color" name="cor_primaria" id="cor_primaria" value="#0055c7" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor Secundária:</label>
              <input type="color" name="cor_secundaria" id="cor_secundaria" value="#4f8cff" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor de Fundo Principal:</label>
              <input type="color" name="cor_fundo" id="cor_fundo" value="#f5f5f5" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor do Menu:</label>
              <input type="color" name="cor_menu" id="cor_menu" value="#fff" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor do Card/Modal:</label>
              <input type="color" name="cor_card" id="cor_card" value="#fff" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor do Botão Primário:</label>
              <input type="color" name="cor_botao_primario" id="cor_botao_primario" value="#0055c7" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor do Botão Secundário:</label>
              <input type="color" name="cor_botao_secundario" id="cor_botao_secundario" value="#6c757d" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor do Footer:</label>
              <input type="color" name="cor_footer" id="cor_footer" value="#fff" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor do Header:</label>
              <input type="color" name="cor_header" id="cor_header" value="#fff" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor da Borda:</label>
              <input type="color" name="cor_borda" id="cor_borda" value="#e0e0e0" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor de Sucesso:</label>
              <input type="color" name="cor_sucesso" id="cor_sucesso" value="#43aa8b" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor de Erro:</label>
              <input type="color" name="cor_erro" id="cor_erro" value="#f44336" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor de Aviso:</label>
              <input type="color" name="cor_aviso" id="cor_aviso" value="#ffb300" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor de Hover do Menu:</label>
              <input type="color" name="cor_hover_menu" id="cor_hover_menu" value="#eaf1fb" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor do Sidebar:</label>
              <input type="color" name="cor_sidebar" id="cor_sidebar" value="#fff" class="form-control form-control-color">
            </div>
            <div class="col-md-4">
              <label>Cor do Texto Principal:</label>
              <input type="color" name="cor_texto" id="cor_texto" value="#222" class="form-control form-control-color">
            </div>
          </div>
          <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salvar Cores</button>
            <button type="button" class="btn btn-secondary" id="btnRestaurarCores">Restaurar Padrão</button>
          </div>
        </form>
        <div id="temaMsg" class="mt-3"></div>
        <div id="coresMsg" class="mt-3"></div>
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
<script src="assets/js/config-tema.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 