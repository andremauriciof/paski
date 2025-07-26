<?php
require_once 'includes/auth.php';
$user = getCurrentUser();
$nomeUsuario = $user ? $user['nome'] : 'Usuário';
$nivelAcesso = $user ? ($user['tipo'] === 'admin' ? 2 : 1) : 1;
$currentPage = 'tabela';
$mainHeader = '<h1>Tabela de Telas</h1>';
ob_start();
?>
<div class="mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Tabela de Preços de Telas</h2>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" id="btnNovaEntrada">
                <i class="fas fa-plus me-1"></i>Nova Tabela
            </button>
        </div>
    </div>
    <!-- Filtros -->
    <div class="search-filter-container mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <div class="input-group h-100">
                    <span class="input-group-text h-100"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control h-100" id="filtroBuscaGeral" placeholder="Buscar por marca, modelo, fornecedor...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select h-100" id="filtroMarca">
                    <option value="">Todas as marcas</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select h-100" id="filtroModelo">
                    <option value="">Todos os modelos</option>
                </select>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-secondary me-2" id="btnLimparFiltros">Limpar</button>
                <button type="button" class="btn btn-primary" id="btnFiltrar">Filtrar</button>
            </div>
        </div>
    </div>
    <!-- Tabela de dados -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Fornecedor</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Custo</th>
                            <th>Mão de Obra</th>
                            <th>Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaDados">
                        <!-- Dados serão carregados via JavaScript -->
                    </tbody>
                </table>
            </div>
            <div id="mensagemSemDados" class="text-center py-3 d-none">
                <p class="text-muted">Nenhum registro encontrado.</p>
            </div>
            <!-- Paginação -->
            <nav aria-label="Paginação">
                <ul class="pagination" id="tabela-pagination">
                    <!-- Pagination will be rendered here -->
                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- Modais -->
<div class="modal fade" id="modalTela" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTituloTela">Nova Entrada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="formTela">
                    <input type="hidden" id="idTela">
                    <div class="mb-3">
                        <label for="data" class="form-label">Data</label>
                        <input type="date" class="form-control" id="data" required placeholder=" ">
                    </div>
                    <div class="mb-3">
                        <label for="fornecedor" class="form-label">Fornecedor</label>
                        <input type="text" class="form-control" id="fornecedor" required placeholder=" ">
                    </div>
                    <div class="mb-3">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca" required placeholder=" ">
                    </div>
                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" required placeholder=" ">
                    </div>
                    <div class="mb-3">
                        <label for="custo" class="form-label">Custo (R$)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="custo" required placeholder=" ">
                    </div>
                    <div class="mb-3">
                        <label for="maodeobra" class="form-label">Mão de Obra (R$)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="maodeobra" required placeholder=" ">
                    </div>
                    <div class="mb-3">
                        <label for="valortotal" class="form-label">Valor Total (R$)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="valortotal" required placeholder=" ">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvar">Salvar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalConfiguracoes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configurações do Sistema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <!-- Conteúdo do modal de configurações -->
            </div>
        </div>
    </div>
</div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script src="assets/js/app-production.js"></script>
<script src="assets/js/pages/tabela-production.js"></script>
<script>
// Scripts específicos da tabela podem ser incluídos aqui
</script>