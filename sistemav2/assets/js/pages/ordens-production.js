// Ordens Module
class Ordens {
    static currentOrdens = [];
    static filteredOrdens = [];
    static currentPage = 1;
    static itemsPerPage = 10;
    static totalPages = 1;
    static totalItems = 0;

    static async render() {
        const content = document.getElementById('page-content');
        
        content.innerHTML = `
            <div class="fade-in">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-clipboard-list text-primary"></i> Ordens de Serviço</h2>
                    <button class="btn btn-primary" onclick="Ordens.showAddModal()">
                        <i class="fas fa-plus"></i> Nova OS
                    </button>
                </div>

                <!-- Search and Filter -->
                <div class="search-filter-container">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="search-ordens" placeholder="Buscar por OS, cliente...">
                                <label for="search-ordens">Buscar por OS, cliente...</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="filter-data-inicio" placeholder="Data Início">
                                <label for="filter-data-inicio">Data Início</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="filter-data-fim" placeholder="Data Fim">
                                <label for="filter-data-fim">Data Fim</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating h-100">
                                <select class="form-select h-100" id="filter-tecnico" style="min-height: 58px;">
                                    <option value="">Todos os técnicos</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                                <label for="filter-tecnico">Técnico</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating h-100">
                                <select class="form-select h-100" id="filter-status" style="min-height: 58px;">
                                    <option value="">Todos os status</option>
                                    <option value="Orçamento">Orçamento</option>
                                    <option value="Executando">Executando</option>
                                    <option value="Aguardando Peça">Aguardando Peça</option>
                                    <option value="Finalizada">Finalizada</option>
                                    <option value="Entregue">Entregue</option>
                                </select>
                                <label for="filter-status">Status</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-secondary me-2" id="btnLimparFiltrosOrdens">Limpar</button>
                        </div>
                    </div>
                </div>

                <!-- Ordens Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Ordens de Serviço</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>OS #</th>
                                        <th>Cliente</th>
                                        <th>Equipamento</th>
                                        <th>Status</th>
                                        <th>Técnico</th>
                                        <th>Valor</th>
                                        <th>Data Entrada</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="ordens-table-body">
                                    <!-- Orders will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <nav aria-label="Paginação">
                            <ul class="pagination" id="ordens-pagination">
                                <!-- Pagination will be rendered here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Ordem Modal -->
            <div class="modal fade" id="ordemModal" tabindex="-1">
                <div class="modal-dialog modal-lg" style="max-width: 900px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ordemModalTitle">Nova Ordem de Serviço</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="ordemForm">
                            <div class="modal-body">
                                <input type="hidden" id="ordem-id">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating d-flex align-items-center" style="gap: 8px; position: relative;">
                                            <input type="text" class="form-control" id="ordem-cliente-nome" placeholder="Buscar cliente por nome ou CPF/CNPJ" autocomplete="off" required>
                                            <input type="hidden" id="ordem-cliente" name="ordem-cliente">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="btnNovoCliente" title="Novo Cliente" tabindex="-1" style="height:38px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-user-plus"></i></button>
                                            <label for="ordem-cliente-nome">Cliente *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating d-flex align-items-center" style="gap: 8px;">
                                            <select class="form-select" id="ordem-equipamento" required>
                                                <option value="">Selecione o equipamento</option>
                                                <!-- Options will be loaded dynamically -->
                                            </select>
                                            <button type="button" class="btn btn-outline-success btn-sm" id="btnNovoEquipamento" title="Novo Equipamento" tabindex="-1" style="height:38px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-plus"></i></button>
                                            <label for="ordem-equipamento">Equipamento *</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="ordem-problema" style="height: 100px" required></textarea>
                                            <label for="ordem-problema">Descrição do Problema *</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <select class="form-select" id="ordem-status" required>
                                                <option value="Orçamento">Orçamento</option>
                                                <option value="Executando">Executando</option>
                                                <option value="Aguardando Peça">Aguardando Peça</option>
                                                <option value="Finalizada">Finalizada</option>
                                                <option value="Entregue">Entregue</option>
                                            </select>
                                            <label for="ordem-status">Status *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <select class="form-select" id="ordem-tecnico" required>
                                                <option value="">Selecione o técnico</option>
                                                <!-- Options will be loaded dynamically -->
                                            </select>
                                            <label for="ordem-tecnico">Técnico Responsável *</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="ordem-valor-orcado" step="0.01" min="0">
                                            <label for="ordem-valor-orcado">Valor Orçado (R$)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="ordem-valor-final" step="0.01" min="0">
                                            <label for="ordem-valor-final">Valor Final (R$)</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="ordem-observacoes" style="height: 80px"></textarea>
                                            <label for="ordem-observacoes">Observações</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Checklist Section -->
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <h6 class="mb-3"><i class="fas fa-clipboard-check text-primary"></i> Checklist de Verificação</h6>
                                        <div class="card">
                                            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 class="text-primary">Celular</h6>
                                                        <div id="checklist-celular" class="checklist-container">
                                                            <!-- Checklist items will be loaded here -->
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="text-primary">Computador</h6>
                                                        <div id="checklist-computador" class="checklist-container">
                                                            <!-- Checklist items will be loaded here -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Novo Cliente -->
            <div class="modal fade" id="modalNovoCliente" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalNovoClienteLabel">Novo Cliente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="formNovoCliente">
                            <div class="modal-body">
                                <input type="hidden" id="novoClienteId">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="novoClienteNome" required>
                                            <label for="novoClienteNome">Nome / Razão Social *</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="novoClienteIE">
                                                <label for="novoClienteIE">Inscrição Estadual</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="novoClienteCpfCnpj" required placeholder=" ">
                                                <label for="novoClienteCpfCnpj">CPF/CNPJ *</label>
                                            </div>
                                            <button type="button" class="btn btn-outline-secondary align-self-stretch" id="buscarNovoCpfCnpj" style="min-width:90px;">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="novoClienteTelefone">
                                            <label for="novoClienteTelefone">Telefone</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="novoClienteCelular" required>
                                            <label for="novoClienteCelular">Celular *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="novoClienteEmail">
                                            <label for="novoClienteEmail">Email</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="novoClienteEndereco">
                                            <label for="novoClienteEndereco">Endereço</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="novoClienteNumero">
                                            <label for="novoClienteNumero">Número</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="novoClienteComplemento">
                                            <label for="novoClienteComplemento">Complemento</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="novoClienteBairro">
                                            <label for="novoClienteBairro">Bairro</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="novoClienteCidade">
                                            <label for="novoClienteCidade">Cidade</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="novoClienteEstado" maxlength="2">
                                            <label for="novoClienteEstado">UF</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="input-group">
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="novoClienteCep" maxlength="9" placeholder=" ">
                                                <label for="novoClienteCep">CEP</label>
                                            </div>
                                            <button type="button" class="btn btn-outline-secondary align-self-stretch" id="buscarNovoCep" style="min-width:90px;">Buscar</button>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="novoClienteObservacoes" style="height: 60px"></textarea>
                                            <label for="novoClienteObservacoes">Observações</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Novo Equipamento -->
            <div class="modal fade" id="modalNovoEquipamento" tabindex="-1" aria-labelledby="modalNovoEquipamentoLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <form id="formNovoEquipamento">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modalNovoEquipamentoLabel">Novo Equipamento</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" id="novoEquipamentoCliente">
                      <div class="row">
                        <div class="col-md-4 mb-3">
                          <div class="form-floating">
                            <select class="form-select" id="novoEquipamentoTipo" required>
                              <option value="">Selecione o tipo</option>
                            </select>
                            <label for="novoEquipamentoTipo">Tipo *</label>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <div class="form-floating">
                            <select class="form-select" id="novoEquipamentoMarca" required>
                              <option value="">Selecione a marca</option>
                            </select>
                            <label for="novoEquipamentoMarca">Marca *</label>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <div class="form-floating">
                            <select class="form-select" id="novoEquipamentoModelo" required>
                              <option value="">Selecione o modelo</option>
                            </select>
                            <label for="novoEquipamentoModelo">Modelo *</label>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <div class="form-floating">
                            <input type="text" class="form-control" id="novoEquipamentoNumeroSerie">
                            <label for="novoEquipamentoNumeroSerie">Número de Série</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="form-floating">
                            <textarea class="form-control" id="novoEquipamentoObservacoes" style="height: 100px"></textarea>
                            <label for="novoEquipamentoObservacoes">Observações</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                      <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        `;

        await this.loadDependencies();
        // Definir data do filtro para hoje e filtrar automaticamente
        setTimeout(async () => {
            const filterDataInicio = document.getElementById('filter-data-inicio');
            const filterDataFim = document.getElementById('filter-data-fim');
            const filterStatus = document.getElementById('filter-status');
            const ignorarData = localStorage.getItem('ordensFiltroIgnorarData');
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayStr = `${yyyy}-${mm}-${dd}`;
            if (filterDataInicio && !filterDataInicio.value) filterDataInicio.value = todayStr;
            if (filterDataFim && !filterDataFim.value) filterDataFim.value = todayStr;
            // Verificar filtro vindo do dashboard
            const filtroStatus = localStorage.getItem('ordensFiltroStatus');
            if (filtroStatus && filterStatus) {
                filterStatus.value = filtroStatus;
                localStorage.removeItem('ordensFiltroStatus');
            }
            // Se for para ignorar data, passe vazio na primeira filtragem
            if (ignorarData) {
                await this.filterOrdens({ ignorarData: true });
                localStorage.removeItem('ordensFiltroIgnorarData');
            } else {
                await this.filterOrdens();
            }
        }, 100);
        this.initEventListeners();
        
        // Check for URL parameters
        this.checkUrlParameters();

        // Adicionar eventos para abrir os modais de cadastro rápido
        setTimeout(() => {
            const btnNovoCliente = document.getElementById('btnNovoCliente');
            if (btnNovoCliente) {
                btnNovoCliente.addEventListener('click', function() {
                    const modal = new bootstrap.Modal(document.getElementById('modalNovoCliente'));
                    modal.show();
                    setTimeout(() => { // Garante que o DOM do modal está pronto
                        // Máscara para telefone/celular
                        var SPMaskBehavior = function (val) {
                            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
                        };
                        if (window.jQuery && $.fn.mask) {
                            $('#novoClienteTelefone').mask(SPMaskBehavior, {
                                onKeyPress: function(val, e, field, options) {
                                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                                }
                            });
                            $('#novoClienteCelular').mask(SPMaskBehavior, {
                                onKeyPress: function(val, e, field, options) {
                                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                                }
                            });
                        }
                        // Máscara para CPF/CNPJ
                        function cpfCnpjMask(value) {
                            value = value.replace(/\D/g, '');
                            if (value.length <= 11) {
                                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                            } else {
                                value = value.replace(/(\d{2})(\d)/, '$1.$2');
                                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                                value = value.replace(/(\d{3})(\d)/, '$1/$2');
                                value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
                            }
                            return value;
                        }
                        const novoClienteNome = document.getElementById('novoClienteNome');
                        if (novoClienteNome) novoClienteNome.addEventListener('input', e => { e.target.value = e.target.value.toUpperCase(); });
                        const novoClienteEndereco = document.getElementById('novoClienteEndereco');
                        if (novoClienteEndereco) novoClienteEndereco.addEventListener('input', e => { e.target.value = e.target.value.toUpperCase(); });
                        const novoClienteEmail = document.getElementById('novoClienteEmail');
                        if (novoClienteEmail) novoClienteEmail.addEventListener('input', e => { e.target.value = e.target.value.toLowerCase(); });
                        const novoClienteCpfCnpj = document.getElementById('novoClienteCpfCnpj');
                        if (novoClienteCpfCnpj) novoClienteCpfCnpj.addEventListener('input', e => { e.target.value = cpfCnpjMask(e.target.value); });
                        // Busca CEP
                        const btnBuscarCep = document.getElementById('buscarNovoCep');
                        if (btnBuscarCep) {
                            btnBuscarCep.onclick = async function() {
                                const cep = document.getElementById('novoClienteCep').value.replace(/\D/g, '');
                                if (cep.length !== 8) {
                                    Swal.fire('CEP inválido!', '', 'warning');
                                    return;
                                }
                                const resp = await fetch('api/clientes.php?action=cep&cep=' + cep);
                                const data = await resp.json();
                                if (data.erro || data.success === false) {
                                    Swal.fire('CEP não encontrado!', '', 'warning');
                                    return;
                                }
                                document.getElementById('novoClienteEndereco').value = data.logradouro || '';
                                document.getElementById('novoClienteBairro').value = data.bairro || '';
                                document.getElementById('novoClienteCidade').value = data.localidade || '';
                                document.getElementById('novoClienteEstado').value = data.uf || '';
                            };
                        }
                        // Busca CNPJ
                        const btnBuscarCnpj = document.getElementById('buscarNovoCpfCnpj');
                        if (btnBuscarCnpj) {
                            btnBuscarCnpj.onclick = async function() {
                                let cnpj = document.getElementById('novoClienteCpfCnpj').value.replace(/\D/g, '');
                                if (cnpj.length !== 14) {
                                    Swal.fire('CNPJ inválido!', '', 'warning');
                                    return;
                                }
                                const resp = await fetch('api/clientes.php?action=cnpj&cnpj=' + cnpj);
                                const data = await resp.json();
                                if (data.status === 'ERROR' || data.success === false) {
                                    Swal.fire('CNPJ não encontrado!', '', 'warning');
                                    return;
                                }
                                document.getElementById('novoClienteNome').value = data.nome || '';
                                document.getElementById('novoClienteEndereco').value = data.logradouro || '';
                                document.getElementById('novoClienteNumero').value = (data.numero && data.numero !== 'S/N') ? data.numero : '';
                                document.getElementById('novoClienteComplemento').value = '';
                                document.getElementById('novoClienteBairro').value = data.bairro || '';
                                document.getElementById('novoClienteCidade').value = data.municipio || '';
                                document.getElementById('novoClienteEstado').value = data.uf || '';
                                document.getElementById('novoClienteCep').value = data.cep || '';
                                let ie = '';
                                if (typeof data.inscricao_estadual === 'string') {
                                    ie = data.inscricao_estadual;
                                } else if (typeof data.inscricao_estadual === 'object' && data.inscricao_estadual !== null) {
                                    ie = Object.values(data.inscricao_estadual).join(', ');
                                } else if (typeof data.atividade_principal === 'string') {
                                    ie = data.atividade_principal;
                                }
                                document.getElementById('novoClienteIE').value = ie;
                                document.getElementById('novoClienteTelefone').value = (data.telefone || '').split('/')[0] || '';
                            };
                        }
                    }, 300);
                });
            }
            const btnNovoEquipamento = document.getElementById('btnNovoEquipamento');
            if (btnNovoEquipamento) {
                btnNovoEquipamento.addEventListener('click', function() {
                    // Abrir o modal primeiro
                    const modal = new bootstrap.Modal(document.getElementById('modalNovoEquipamento'));
                    modal.show();
                    // Depois popular os selects
                    setTimeout(async () => {
                      await popularTiposMarcasModelos();
                      document.getElementById('novoEquipamentoTipo').addEventListener('change', popularModelosPorTipoMarca);
                      document.getElementById('novoEquipamentoMarca').addEventListener('change', popularModelosPorTipoMarca);
                      // Preencher cliente oculto
                      const clienteId = document.getElementById('ordem-cliente').value;
                      document.getElementById('novoEquipamentoCliente').value = clienteId;
                    }, 300);
                });
            }

            // Cadastro rápido de cliente
            const formNovoCliente = document.getElementById('formNovoCliente');
            if (formNovoCliente) {
                formNovoCliente.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const nome = document.getElementById('novoClienteNome').value;
                    const telefone = document.getElementById('novoClienteTelefone').value;
                    const celular = document.getElementById('novoClienteCelular').value;
                    const email = document.getElementById('novoClienteEmail').value;
                    const cpf_cnpj = document.getElementById('novoClienteCpfCnpj').value;
                    const ie = document.getElementById('novoClienteIE').value;
                    const endereco = document.getElementById('novoClienteEndereco').value;
                    const numero = document.getElementById('novoClienteNumero').value;
                    const complemento = document.getElementById('novoClienteComplemento').value;
                    const bairro = document.getElementById('novoClienteBairro').value;
                    const cidade = document.getElementById('novoClienteCidade').value;
                    const estado = document.getElementById('novoClienteEstado').value;
                    const cep = document.getElementById('novoClienteCep').value;
                    const observacoes = document.getElementById('novoClienteObservacoes').value;
                    
                    // Validação dos campos obrigatórios
                    if (!nome.trim()) {
                        alert('Nome é obrigatório!');
                        document.getElementById('novoClienteNome').focus();
                        return;
                    }
                    if (!celular.trim()) {
                        alert('Celular é obrigatório!');
                        document.getElementById('novoClienteCelular').focus();
                        return;
                    }
                    if (!cpf_cnpj.trim()) {
                        alert('CPF/CNPJ é obrigatório!');
                        document.getElementById('novoClienteCpfCnpj').focus();
                        return;
                    }
                    
                    const formData = new FormData();
                    formData.append('nome', nome);
                    formData.append('telefone', telefone);
                    formData.append('celular', celular);
                    formData.append('email', email);
                    formData.append('cpf_cnpj', cpf_cnpj);
                    formData.append('ie', ie);
                    formData.append('endereco', endereco);
                    formData.append('numero', numero);
                    formData.append('complemento', complemento);
                    formData.append('bairro', bairro);
                    formData.append('cidade', cidade);
                    formData.append('estado', estado);
                    formData.append('cep', cep);
                    formData.append('observacoes', observacoes);
                    
                    try {
                        const response = await fetch('api/clientes.php', {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();
                        if (result.success && result.id) {
                            bootstrap.Modal.getInstance(document.getElementById('modalNovoCliente')).hide();
                            await Ordens.loadDependencies();
                            document.getElementById('ordem-cliente').value = result.id;
                            // Limpar formulário
                            formNovoCliente.reset();
                            // Atualizar equipamentos do novo cliente
                            await Ordens.loadEquipamentosByCliente(result.id);
                        } else {
                            alert('Erro ao cadastrar cliente: ' + (result.error || ''));
                        }
                    } catch (err) {
                        alert('Erro de conexão ao cadastrar cliente.');
                    }
                });
            }

            // Cadastro rápido de equipamento
            const formNovoEquipamento = document.getElementById('formNovoEquipamento');
            if (formNovoEquipamento) {
                formNovoEquipamento.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const cliente_id = document.getElementById('novoEquipamentoCliente').value;
                    const tipo_id = document.getElementById('novoEquipamentoTipo').value;
                    const marca_id = document.getElementById('novoEquipamentoMarca').value;
                    const modelo_id = document.getElementById('novoEquipamentoModelo').value;
                    const numero_serie = document.getElementById('novoEquipamentoNumeroSerie').value;
                    const observacoes = document.getElementById('novoEquipamentoObservacoes').value;
                    if (!cliente_id || !tipo_id || !marca_id || !modelo_id) {
                      alert('Cliente, tipo, marca e modelo são obrigatórios!');
                      return;
                    }
                    const body = JSON.stringify({ cliente_id, tipo_id, marca_id, modelo_id, numero_serie, observacoes });
                    try {
                      const response = await fetch('api/equipamentos.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body
                      });
                      const result = await response.json();
                      if (result.success && result.id) {
                        bootstrap.Modal.getInstance(document.getElementById('modalNovoEquipamento')).hide();
                        await Ordens.loadEquipamentosByCliente(cliente_id);
                        setTimeout(() => {
                          document.getElementById('ordem-equipamento').value = result.id;
                        }, 200);
                        formNovoEquipamento.reset();
                      } else {
                        alert('Erro ao cadastrar equipamento: ' + (result.error || ''));
                      }
                    } catch (err) {
                      alert('Erro de conexão ao cadastrar equipamento.');
                    }
                });
            }
        }, 500);

        // Adicionar evento para select de itens por página
        setTimeout(() => {
            const selectItemsPerPage = document.getElementById('ordens-items-per-page');
            if (selectItemsPerPage) {
                selectItemsPerPage.value = String(Ordens.itemsPerPage);
                selectItemsPerPage.addEventListener('change', function() {
                    const val = parseInt(this.value);
                    Ordens.itemsPerPage = val === -1 ? Ordens.filteredOrdens.length || 99999 : val;
                    Ordens.currentPage = 1;
                    Ordens.renderTable();
                    Ordens.renderPagination();
                });
            }
        }, 500);
    }

    static checkUrlParameters() {
        const urlParams = new URLSearchParams(window.location.search);
        const equipamentoId = urlParams.get('equipamento');
        const clienteId = urlParams.get('cliente');
        if (equipamentoId) {
            // Clear the parameter from URL
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
            // Open modal with equipment
            setTimeout(() => {
                this.showAddModalWithEquipment(parseInt(equipamentoId));
            }, 500);
        } else if (clienteId) {
            // Clear the parameter from URL
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
            // Open modal with client selected
            setTimeout(async () => {
                await this.showAddModal();
                document.getElementById('ordem-cliente').value = clienteId;
                this.loadEquipamentosByCliente(clienteId);
            }, 500);
        }
    }

    static initEventListeners() {
        // Search functionality
        document.getElementById('search-ordens').addEventListener('input', async () => {
            await this.filterOrdens();
        });

        // Filter functionality
        document.getElementById('filter-status').addEventListener('change', async () => {
            await this.filterOrdens();
        });

        document.getElementById('filter-tecnico').addEventListener('change', async () => {
            await this.filterOrdens();
        });

        document.getElementById('filter-data-inicio').addEventListener('change', async () => {
            await this.filterOrdens();
        });

        document.getElementById('filter-data-fim').addEventListener('change', async () => {
            await this.filterOrdens();
        });

        // Botão Limpar
        document.getElementById('btnLimparFiltrosOrdens').addEventListener('click', async () => {
            document.getElementById('search-ordens').value = '';
            document.getElementById('filter-status').value = '';
            document.getElementById('filter-tecnico').value = '';
            // Voltar datas para o dia atual
            const filterDataInicio = document.getElementById('filter-data-inicio');
            const filterDataFim = document.getElementById('filter-data-fim');
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayStr = `${yyyy}-${mm}-${dd}`;
            if (filterDataInicio) filterDataInicio.value = todayStr;
            if (filterDataFim) filterDataFim.value = todayStr;
            await this.filterOrdens();
        });

        // Form submission
        document.getElementById('ordemForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveOrdem();
        });

        // Cliente change event to load equipamentos
        document.getElementById('ordem-cliente').addEventListener('change', (e) => {
            this.loadEquipamentosByCliente(e.target.value);
        });
    }

    static async loadDependencies() {
        try {
            // Carregar clientes
            const clientesResponse = await fetch('api/clientes.php?action=list');
            const clientesResult = await clientesResponse.json();
            
            // Carregar usuários
            const usuariosResponse = await fetch('api/usuarios.php?action=list');
            const usuariosResult = await usuariosResponse.json();
            
            if (clientesResult.success && usuariosResult.success) {
                const clientes = clientesResult.data;
                const usuarios = usuariosResult.data;
                const tecnicos = usuarios.filter(u => u.tipo === 'tecnico' || u.tipo === 'admin');

                // Load clients
                const clienteSelects = ['ordem-cliente'];
                clienteSelects.forEach(selectId => {
                    const select = document.getElementById(selectId);
                    select.innerHTML = '<option value="">Selecione o cliente</option>';
                    clientes.forEach(cliente => {
                        select.innerHTML += `<option value="${cliente.id}">${cliente.nome}</option>`;
                    });
                });

                // Load technicians
                const tecnicoSelects = ['ordem-tecnico', 'filter-tecnico'];
                tecnicoSelects.forEach(selectId => {
                    const select = document.getElementById(selectId);
                    const defaultText = selectId.includes('filter') ? 'Todos os técnicos' : 'Selecione o técnico';
                    select.innerHTML = `<option value="">${defaultText}</option>`;
                    tecnicos.forEach(tecnico => {
                        select.innerHTML += `<option value="${tecnico.id}">${tecnico.nome}</option>`;
                    });
                });
                
                // Atualizar localStorage para compatibilidade
                localStorage.setItem('clientes', JSON.stringify(clientes));
                localStorage.setItem('usuarios', JSON.stringify(usuarios));
            } else {
                showAlert('Erro', 'Erro ao carregar dados de dependências', 'error');
            }
        } catch (error) {
            showAlert('Erro', 'Erro de conexão ao carregar dados de dependências', 'error');
        }
    }

    static async loadEquipamentosByCliente(clienteId) {
        try {
            const response = await fetch(`api/equipamentos.php?action=list&cliente_id=${clienteId}`);
            const result = await response.json();
            
            const select = document.getElementById('ordem-equipamento');
            select.innerHTML = '<option value="">Selecione o equipamento</option>';

            if (result.success && result.data) {
                result.data.forEach(equipamento => {
                    select.innerHTML += `<option value="${equipamento.id}">${equipamento.marca} ${equipamento.modelo} (${equipamento.tipo})</option>`;
                });
            }
        } catch (error) {
            console.error('Erro ao carregar equipamentos:', error);
            const select = document.getElementById('ordem-equipamento');
            select.innerHTML = '<option value="">Erro ao carregar equipamentos</option>';
        }
    }

    static async loadOrdens(options = {}) {
        try {
            const search = document.getElementById('search-ordens')?.value || '';
            const status = document.getElementById('filter-status')?.value || '';
            const tecnico_id = document.getElementById('filter-tecnico')?.value || '';
            let data_inicio = document.getElementById('filter-data-inicio')?.value || '';
            let data_fim = document.getElementById('filter-data-fim')?.value || '';
            if (options.ignorarData) {
                data_inicio = '';
                data_fim = '';
            }
            const params = new URLSearchParams({
                action: 'list',
                page: this.currentPage,
                limit: this.itemsPerPage,
                search,
                status,
                tecnico_id,
                data_inicio,
                data_fim
            });
            const response = await fetch('api/ordens.php?' + params.toString());
            const result = await response.json();
            if (result.success) {
                this.currentOrdens = result.data;
                this.filteredOrdens = [...this.currentOrdens];
                this.totalPages = result.totalPages || 1;
                this.totalItems = result.total || 0;
                localStorage.setItem('ordens', JSON.stringify(this.currentOrdens));
                await this.renderTable();
                this.renderPagination();
            } else {
                showAlert('Erro', result.error || 'Erro ao carregar ordens de serviço', 'error');
            }
        } catch (error) {
            showAlert('Erro', 'Erro de conexão ao carregar ordens de serviço', 'error');
        }
    }

    static async filterOrdens(options = {}) {
        this.currentPage = 1;
        await this.loadOrdens(options);
    }

    static async renderTable() {
        const tbody = document.getElementById('ordens-table-body');
        tbody.innerHTML = '';
        if (this.currentOrdens.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Nenhuma ordem de serviço encontrada</td></tr>';
            return;
        }
        const clientes = JSON.parse(localStorage.getItem('clientes') || '[]');
        const usuarios = JSON.parse(localStorage.getItem('usuarios') || '[]');
        // Buscar equipamentos do banco de dados
        let equipamentos = [];
        try {
            const response = await fetch('api/equipamentos.php?action=list');
            const result = await response.json();
            if (result.success) {
                equipamentos = result.data;
            }
        } catch (error) {
            console.error('Erro ao carregar equipamentos:', error);
        }
        this.currentOrdens.forEach(ordem => {
            const cliente = clientes.find(c => c.id === ordem.cliente_id);
            const equipamento = equipamentos.find(e => e.id === ordem.equipamento_id);
            const tecnico = usuarios.find(u => u.id === ordem.tecnico_id);
            const valor = ordem.valor_final || ordem.valor_orcado || 0;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <strong>#${ordem.id}</strong>
                </td>
                <td>
                    ${cliente ? cliente.nome : 'N/A'}
                </td>
                <td>
                    ${equipamento ? `${equipamento.marca} ${equipamento.modelo}` : 'N/A'}<br>
                    <small class="text-muted">${equipamento ? equipamento.tipo : ''}</small>
                </td>
                <td>
                    <span class="status-badge status-${ordem.status.toLowerCase().replace(' ', '-')}">${ordem.status}</span>
                </td>
                <td>
                    ${tecnico ? tecnico.nome : 'N/A'}
                </td>
                <td>
                    ${formatCurrency(valor)}
                </td>
                <td>
                    ${formatDate(ordem.data_entrada)}
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info" onclick="Ordens.showDetails(${ordem.id})" title="Detalhes">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-primary" onclick="Ordens.showEditModal(${ordem.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-success" onclick="Ordens.printOS(${ordem.id})" title="Imprimir">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="Ordens.finalizarOrdem(${ordem.id})" title="Alterar Status">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="Ordens.deleteOrdem(${ordem.id})" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    static renderPagination() {
        const pagination = document.getElementById('ordens-pagination');
        const totalPages = this.totalPages;
        if (totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }
        let paginationHTML = '';
        paginationHTML += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Ordens.goToPage(${this.currentPage - 1})">Anterior</a>
            </li>
        `;
        let startPage = Math.max(1, this.currentPage - 4);
        let endPage = Math.min(totalPages, startPage + 9);
        if (endPage - startPage < 9) {
            startPage = Math.max(1, endPage - 9);
        }
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="Ordens.goToPage(${i})">${i}</a>
                </li>
            `;
        }
        paginationHTML += `
            <li class="page-item ${this.currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Ordens.goToPage(${this.currentPage + 1})">Próximo</a>
            </li>
        `;
        pagination.innerHTML = paginationHTML;
    }

    static async goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
            this.currentPage = page;
            await this.loadOrdens();
        }
    }

    static async showAddModal() {
        document.getElementById('ordemModalTitle').textContent = 'Nova Ordem de Serviço';
        document.getElementById('ordemForm').reset();
        document.getElementById('ordem-id').value = '';
        document.getElementById('ordem-equipamento').innerHTML = '<option value="">Selecione o equipamento</option>';
        document.getElementById('ordem-cliente-nome').value = '';
        document.getElementById('ordem-cliente').value = '';
        await this.loadChecklist();
        this.clearChecklist();
        this.initClienteAutocomplete();
        const modal = new bootstrap.Modal(document.getElementById('ordemModal'));
        modal.show();
    }

    static async showAddModalWithEquipment(equipamentoId) {
        try {
            const response = await fetch(`api/equipamentos.php?action=get&id=${equipamentoId}`);
            const result = await response.json();
            
            if (!result.success || !result.data) {
                showAlert('Erro', 'Equipamento não encontrado', 'error');
                return;
            }

            const equipamento = result.data;
            this.showAddModal();
            
            // Set client and equipment
            document.getElementById('ordem-cliente').value = equipamento.cliente_id;
            await this.loadEquipamentosByCliente(equipamento.cliente_id);
            
            setTimeout(() => {
                document.getElementById('ordem-equipamento').value = equipamentoId;
            }, 100);
        } catch (error) {
            showAlert('Erro', 'Erro ao carregar equipamento', 'error');
        }
    }

    static async showEditModal(ordemId) {
        const ordem = this.currentOrdens.find(o => o.id === ordemId);
        if (!ordem) return;
        document.getElementById('ordemModalTitle').textContent = 'Editar Ordem de Serviço';
        document.getElementById('ordem-id').value = ordem.id;
        document.getElementById('ordem-cliente-nome').value = '';
        document.getElementById('ordem-cliente').value = ordem.cliente_id;
        // Buscar nome do cliente para preencher o campo de texto
        try {
            const resp = await fetch('api/clientes.php?action=search&q=' + ordem.cliente_id);
            const data = await resp.json();
            if (data.success && data.data && data.data.length > 0) {
                document.getElementById('ordem-cliente-nome').value = data.data[0].nome + (data.data[0].cpf_cnpj ? ' (' + data.data[0].cpf_cnpj + ')' : '');
            }
        } catch (e) {}
        document.getElementById('ordem-problema').value = ordem.descricao_problema;
        document.getElementById('ordem-status').value = ordem.status;
        document.getElementById('ordem-tecnico').value = ordem.tecnico_id;
        document.getElementById('ordem-valor-orcado').value = ordem.valor_orcado || '';
        document.getElementById('ordem-valor-final').value = ordem.valor_final || '';
        document.getElementById('ordem-observacoes').value = ordem.observacoes || '';
        await this.loadEquipamentosByCliente(ordem.cliente_id);
        document.getElementById('ordem-equipamento').value = ordem.equipamento_id;
        document.getElementById('ordem-tecnico').value = ordem.tecnico_id;
        await this.loadChecklist();
        await this.loadChecklistMarks(ordemId);
        this.initClienteAutocomplete();
        const modal = new bootstrap.Modal(document.getElementById('ordemModal'));
        modal.show();
    }

    static async saveOrdem() {
        const id = document.getElementById('ordem-id').value;
        const cliente_id = document.getElementById('ordem-cliente').value;
        const equipamento_id = document.getElementById('ordem-equipamento').value;
        const descricao_problema = document.getElementById('ordem-problema').value;
        const status = document.getElementById('ordem-status').value;
        const tecnico_id = document.getElementById('ordem-tecnico').value;

        // Validação
        if ((id && (!cliente_id || !equipamento_id || !descricao_problema || !tecnico_id)) ||
            (!id && (!cliente_id || !equipamento_id || !descricao_problema || !tecnico_id))) {
            const msg = id ? 'ID, cliente, equipamento, descrição do problema e técnico são obrigatórios' : 'Cliente, equipamento, descrição do problema e técnico são obrigatórios';
            showAlert('Erro', msg, 'error');
            return;
        }

        const ordem = {
            id,
            cliente_id,
            equipamento_id,
            descricao_problema,
            status,
            tecnico_id,
            valor_orcado: document.getElementById('ordem-valor-orcado').value || null,
            valor_final: document.getElementById('ordem-valor-final').value || null,
            observacoes: document.getElementById('ordem-observacoes').value,
            checklist_marks: this.getChecklistMarks()
        };

        console.log('Enviando ordem:', ordem);

        try {
            let url = 'api/ordens.php';
            let method = 'POST';
            let body;
            let headers = {};
            if (id) {
                method = 'PUT';
                const params = new URLSearchParams();
                Object.keys(ordem).forEach(key => {
                    if (ordem[key] !== null && ordem[key] !== undefined) {
                        if (key === 'checklist_marks') {
                            params.append(key, JSON.stringify(ordem[key]));
                        } else {
                            params.append(key, ordem[key]);
                        }
                    }
                });
                body = params.toString();
                headers = { 'Content-Type': 'application/x-www-form-urlencoded' };
            } else {
                body = new FormData();
                Object.keys(ordem).forEach(key => {
                    if (ordem[key] !== null && ordem[key] !== undefined) {
                        if (key === 'checklist_marks') {
                            body.append(key, JSON.stringify(ordem[key]));
                        } else {
                            body.append(key, ordem[key]);
                        }
                    }
                });
            }
            const response = await fetch(url, {
                method: method,
                body: body,
                headers: headers
            });
            const result = await response.json();
            if (result.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('ordemModal'));
                modal.hide();
                await this.loadOrdens();
                showAlert('Sucesso!', `Ordem de serviço ${id ? 'atualizada' : 'criada'} com sucesso!`);
            } else {
                showAlert('Erro', result.error || 'Erro ao salvar ordem de serviço', 'error');
            }
        } catch (error) {
            showAlert('Erro', 'Erro de conexão ao salvar ordem de serviço', 'error');
        }
    }

    static async deleteOrdem(ordemId) {
        showConfirm(
            'Confirmar exclusão',
            'Tem certeza que deseja excluir esta ordem de serviço? Esta ação não pode ser desfeita.',
            async () => {
                try {
                    const response = await fetch(`api/ordens.php?id=${ordemId}`, {
                        method: 'DELETE'
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        await this.loadOrdens();
                        showAlert('Sucesso!', 'Ordem de serviço excluída com sucesso!');
                    } else {
                        showAlert('Erro', result.error || 'Erro ao excluir ordem de serviço', 'error');
                    }
                } catch (error) {
                    showAlert('Erro', 'Erro de conexão ao excluir ordem de serviço', 'error');
                }
            }
        );
    }

    static async finalizarOrdem(ordemId) {
        // Lista de status disponíveis em ordem
        const statusOptions = [
            'Orçamento',
            'Executando',
            'Aguardando Peça',
            'Finalizada',
            'Entregue'
        ];
        // Buscar a ordem completa para saber o status atual
        const ordem = this.currentOrdens.find(o => o.id === ordemId);
        if (!ordem) {
            showAlert('Erro', 'Ordem não encontrada', 'error');
            return;
        }
        const statusAtual = ordem.status;
        // Permitir alterar para qualquer status, exceto o atual
        const opcoesSelect = statusOptions.filter(s => s !== statusAtual);
        if (opcoesSelect.length === 0) {
            showAlert('Atenção', 'Não há outros status disponíveis para alteração.', 'info');
            return;
        }
        // Prompt para escolher o status
        const { value: statusSelecionado } = await Swal.fire({
            title: 'Alterar Status da OS',
            input: 'select',
            inputOptions: opcoesSelect.reduce((acc, cur) => { acc[cur] = cur; return acc; }, {}),
            inputPlaceholder: 'Selecione o novo status',
            showCancelButton: true,
            confirmButtonText: 'Alterar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value) {
                    return 'Selecione um status!';
                }
            }
        });
        if (!statusSelecionado) return;
        showConfirm(
            'Alterar Status',
            `Tem certeza que deseja alterar o status desta ordem para "${statusSelecionado}"?`,
            async () => {
                try {
                    // Montar dados como URL-encoded para PUT
                    const params = new URLSearchParams();
                    params.append('id', ordemId);
                    params.append('cliente_id', ordem.cliente_id);
                    params.append('equipamento_id', ordem.equipamento_id);
                    params.append('descricao_problema', ordem.descricao_problema);
                    params.append('tecnico_id', ordem.tecnico_id);
                    params.append('status', statusSelecionado);
                    if (ordem.valor_orcado !== undefined && ordem.valor_orcado !== null) params.append('valor_orcado', ordem.valor_orcado);
                    if (ordem.valor_final !== undefined && ordem.valor_final !== null) params.append('valor_final', ordem.valor_final);
                    if (ordem.observacoes !== undefined && ordem.observacoes !== null) params.append('observacoes', ordem.observacoes);
                    // Enviar como PUT
                    const response = await fetch('api/ordens.php', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: params.toString()
                    });
                    const result = await response.json();
                    if (result.success) {
                        await this.loadOrdens();
                        showAlert('Sucesso!', `Status alterado para "${statusSelecionado}" com sucesso!`);
                    } else {
                        showAlert('Erro', result.error || 'Erro ao alterar status da ordem de serviço', 'error');
                    }
                } catch (error) {
                    showAlert('Erro', 'Erro de conexão ao alterar status da ordem de serviço', 'error');
                }
            },
            {
                confirmButtonText: 'Sim, alterar!',
                confirmButtonColor: '#2563eb',
                icon: 'info'
            }
        );
    }

    static showDetails(ordemId) {
        const ordem = this.currentOrdens.find(o => o.id === ordemId);
        const clientes = JSON.parse(localStorage.getItem('clientes') || '[]');
        const equipamentos = JSON.parse(localStorage.getItem('equipamentos') || '[]');
        const usuarios = JSON.parse(localStorage.getItem('usuarios') || '[]');
        
        const cliente = clientes.find(c => c.id === ordem.cliente_id);
        const equipamento = equipamentos.find(e => e.id === ordem.equipamento_id);
        const tecnico = usuarios.find(u => u.id === ordem.tecnico_id);

        if (!ordem) return;

        Swal.fire({
            title: `Ordem de Serviço #${ordem.id}`,
            html: `
                <div class="text-left">
                    <h6><strong>Dados do Cliente:</strong></h6>
                    <p><strong>Nome:</strong> ${cliente ? cliente.nome : 'N/A'}</p>
                    <p><strong>Telefone:</strong> ${cliente ? cliente.telefone : 'N/A'}</p>
                    <p><strong>Email:</strong> ${cliente ? cliente.email : 'N/A'}</p>
                    
                    <h6 class="mt-3"><strong>Equipamento:</strong></h6>
                    <p><strong>Tipo:</strong> ${equipamento ? equipamento.tipo : 'N/A'}</p>
                    <p><strong>Marca/Modelo:</strong> ${equipamento ? `${equipamento.marca} ${equipamento.modelo}` : 'N/A'}</p>
                    <p><strong>Número de Série:</strong> ${equipamento ? equipamento.numero_serie : 'N/A'}</p>
                    
                    <h6 class="mt-3"><strong>Serviço:</strong></h6>
                    <p><strong>Problema:</strong> ${ordem.descricao_problema}</p>
                    <p><strong>Status:</strong> <span class="status-badge status-${ordem.status.toLowerCase().replace(' ', '-')}">${ordem.status}</span></p>
                    <p><strong>Técnico:</strong> ${tecnico ? tecnico.nome : 'N/A'}</p>
                    <p><strong>Valor Orçado:</strong> ${ordem.valor_orcado ? formatCurrency(ordem.valor_orcado) : 'N/A'}</p>
                    <p><strong>Valor Final:</strong> ${ordem.valor_final ? formatCurrency(ordem.valor_final) : 'N/A'}</p>
                    <p><strong>Observações:</strong> ${ordem.observacoes || 'Nenhuma'}</p>
                    
                    <h6 class="mt-3"><strong>Datas:</strong></h6>
                    <p><strong>Entrada:</strong> ${formatDateTime(ordem.data_entrada)}</p>
                    <p><strong>Saída:</strong> ${ordem.data_saida ? formatDateTime(ordem.data_saida) : 'N/A'}</p>
                </div>
            `,
            width: '600px',
            confirmButtonColor: '#1e3a8a',
            confirmButtonText: 'Fechar'
        });
    }

    static async printOS(ordemId) {
        const ordem = this.currentOrdens.find(o => o.id === ordemId);
        const clientes = JSON.parse(localStorage.getItem('clientes') || '[]');
        const equipamentos = JSON.parse(localStorage.getItem('equipamentos') || '[]');
        const usuarios = JSON.parse(localStorage.getItem('usuarios') || '[]');
        const cliente = clientes.find(c => c.id === ordem.cliente_id);
        const equipamento = equipamentos.find(e => e.id === ordem.equipamento_id);
        const tecnico = usuarios.find(u => u.id === ordem.tecnico_id);
        if (!ordem) return;

        // Carregar marcas do checklist
        let checklistMarks = [];
        try {
            const response = await fetch(`api/checklist.php?action=get_marks&ordem_id=${ordemId}`);
            const result = await response.json();
            if (result.success) {
                checklistMarks = result.data.map(mark => mark.checklist_item_id);
            }
        } catch (error) {
            console.error('Erro ao carregar marcas do checklist:', error);
        }
        // Buscar dados da empresa
        let empresa = {};
        let logoBase64 = '';
        try {
            const res = await fetch('api/api.php?action=empresa');
            const json = await res.json();
            if (json.success && json.data) {
                empresa = json.data;
                logoBase64 = empresa.logo ? 'data:image/png;base64,' + empresa.logo : '';
            }
        } catch (e) {}
        // 1. Perguntar o tipo de OS
        const { value: tipoOS } = await Swal.fire({
            title: 'Escolha o tipo de OS para impressão',
            input: 'radio',
            inputOptions: {
                'celular': 'Celular',
                'computador': 'Computador/Notebook'
            },
            inputValidator: (value) => {
                if (!value) return 'Selecione o tipo de OS!';
            },
            confirmButtonText: 'Imprimir',
            cancelButtonText: 'Cancelar',
            showCancelButton: true
        });
        if (!tipoOS) return;

        // Função para gerar checklist com marcas
        async function generateChecklistHTML(categoria, marks) {
            try {
                const response = await fetch('api/checklist.php?action=get_items');
                const result = await response.json();
                let checklistItems;
                if (result.success) {
                    const items = result.data;
                    // LOG para depuração
                    console.log('Itens checklist:', items, 'Categoria:', categoria);
                    checklistItems = {
                        Celular: items.filter(item => item.categoria === 'Celular'),
                        Computador: items.filter(item => item.categoria === 'Computador')
                    };
                } else {
                    checklistItems = { Celular: [], Computador: [] };
                }
                const items = checklistItems[categoria] || [];
                let html = '<table style="width:100%; font-size:10px; border-collapse:collapse;">';
                const markedItems = items.filter(item => marks.includes(item.id));
                if (markedItems.length === 0) {
                    html += '<tr><td colspan="2">Nenhum item marcado no checklist.</td></tr>';
                }
                markedItems.forEach(item => {
                    html += '<tr>';
                    html += `<td style="width:24px;"><span style="font-size:12px; vertical-align:middle; margin-right:4px;">☑</span></td><td style="padding-left:1px;"><b>${item.nome}</b></td>`;
                    html += '</tr>';
                });
                html += '</table>';
                return html;
            } catch (error) {
                console.error('Erro ao carregar checklist para impressão:', error);
                return '<p>Erro ao carregar checklist</p>';
            }
        }

        // 2. Função para gerar via CELULAR
        function gerarViaCelular(tipoVia, logoBase64, checklistHTML) {
            return `
            <div class="os-via-celular" style="width: 100%; max-width: 800px; margin: 0 auto; font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #222; background: #fff; border: 1.5px solid #222; padding: 8px; box-sizing: border-box;">
                <!-- Cabeçalho -->
                <div style="display: flex; align-items: center; gap: 8px;">
                    ${logoBase64 ? `<img src='${logoBase64}' alt='Logo' style='height: 38px; margin-right: 8px;'>` : ''}
                    <div style="font-size: 11px; line-height: 1.2;">
                        <div style="font-weight: bold; font-size: 13px; color: #222;">${empresa.nome || ''}</div>
                        <div>${[
                            empresa.cnpj ? 'CNPJ: ' + empresa.cnpj : '',
                            empresa.ie ? 'IE: ' + empresa.ie : ''                          
                        ].filter(Boolean).join(' | ')}</div>                        
                        <div>${[
                            empresa.endereco || '',
                            empresa.numero || '',
                            empresa.bairro || '',
                            empresa.cidade || '',
                            empresa.estado || '',
                            empresa.cep ? 'CEP: ' + empresa.cep : ''
                        ].filter(Boolean).join(' - ')}</div>
                        <div>${[
                            empresa.telefone || '',
                            empresa.email ? 'E-mail: ' + empresa.email : ''
                        ].filter(Boolean).join(' | ')}</div>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; border-bottom: 1.5px solid #222; padding-bottom: 2px;">
                    <div style="font-size: 13px; font-weight: bold;">ORDEM DE SERVIÇO</div>
                    <div style="font-size: 12px; font-weight: bold;">Nº: ${ordem.id}</div>
                </div>
                <!-- Dados Cliente -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                    <tr>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 8%; font-weight: bold;">Código</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 22%; font-weight: bold;">Nome</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Contato</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Telefone</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">CPF/CNPJ</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 16%; font-weight: bold;">UF</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${cliente ? cliente.id || '' : ''}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${cliente ? cliente.nome || '' : ''}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;"></td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${cliente ? cliente.telefone || '' : ''}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${cliente ? cliente.cpf_cnpj || '' : ''}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">SP</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid #222; padding: 2px 4px;"><b>Endereço:</b> ${cliente ? cliente.endereco || '' : ''}</td>
                        <td colspan="2" style="border: 1px solid #222; padding: 2px 4px;"><b>Cidade:</b> ${cliente ? cliente.cidade || '' : ''}</td>
                        <td colspan="2" style="border: 1px solid #222; padding: 2px 4px;"></td>
                    </tr>
                </table>
                <!-- Dados Equipamento -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                    <tr>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Equipamento</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Marca</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Modelo</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Nº Série</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${equipamento ? equipamento.tipo || 'celular' : 'celular'}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${equipamento ? equipamento.marca || '' : ''}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${equipamento ? equipamento.modelo || '' : ''}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${equipamento ? equipamento.numero_serie || '' : ''}</td>
                    </tr>
                </table>
                <!-- Detalhes e Defeito Relatado lado a lado -->
                <div style="display: flex; gap: 10px; margin-bottom: 6px;">
                    <div style="flex: 1; border: 1px solid #222; min-height: 48px; padding: 4px 6px; display: flex; flex-direction: column; justify-content: flex-start;">
                        <b>Detalhes do Equipamento:</b>
                        <div style="min-height: 36px; white-space: pre-line;">${ordem.observacoes || '&nbsp;'}<br><br></div>
                    </div>
                    <div style="flex: 1; border: 1px solid #222; min-height: 48px; padding: 4px 6px; display: flex; flex-direction: column; justify-content: flex-start;">
                        <b>Defeito Relatado:</b>
                        <div style="min-height: 36px; white-space: pre-line;">${ordem.descricao_problema || '&nbsp;'}<br><br></div>
                    </div>
                </div>
                <!-- Checklist personalizado para celular -->
                <div style="border: 1px solid #222; margin-bottom: 6px; padding: 4px 6px;">
                    <div style="font-weight: bold; text-align: center; margin-bottom: 2px;">Check List</div>
                    ${checklistHTML}
                </div>
                <!-- Diagnóstico/Solução abaixo -->
                <div style="border: 1px solid #222; min-height: 48px; padding: 4px 6px; margin-bottom: 6px;">
                    <b>Diagnóstico/Solução:</b>
                    <div style="min-height: 36px; white-space: pre-line;">${ordem.status === 'Orçamento' ? 'Orçamento' : 'Diagnóstico'}<br><br></div>
                </div>
                <!-- Fechamento -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                    <tr>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 20%; font-weight: bold;">FECHAMENTO</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 20%;">Valor das Peças</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 20%;">Valor dos Serviços</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 20%;">Valor Frete</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 20%; font-weight: bold; background: #eee;">TOTAL</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #222; padding: 2px 4px;"></td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">R$ 0,00</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">R$ 0,00</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">R$ 0,00</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; font-weight: bold; background: #eee;">R$ 0,00</td>
                    </tr>
                </table>
                <!-- Termos -->
                <div style="font-size: 8px; margin-bottom: 2px;">
                    <div>01 - O equipamento não retirado no prazo de até 90 dias após o parecer ou aprovação do serviço, só será entregue mediante pagamento de taxa de R$ 15,00 (quinze reais) ao mês a título de seguro.</div>
                    <div>02 - A Assistência Técnica oferece garantia de 90 dias após a entrega do equipamento na peça trocada sem a violação do lacre de segurança, com exceção de: Limpezas, desoxidações, atualizações de software e desbloqueios.</div>
                    <div>03 - O Cliente é o total responsável pela procedência do equipamento, estando assim a Assistência Técnica isenta de qualquer responsabilidade.</div>
                    <div>04 - A Assistência Técnica não é responsável por arquivos contidos no equipamento como: fotos, músicas, vídeos, agenda de contatos, programas, aplicativos e jogos. Estas arquivos podem ser removidos em determinados serviços e o backup deverá ser efetuado pelo próprio cliente.</div>
                    <div>Declaro estar de acordo com os itens descritos acima e com os testes efetuados da lista de checagem do equipamento.</div>
                </div>
                <!-- Assinaturas -->
                <div style="display: flex; justify-content: space-between; font-size: 10px; margin-top: 8px; gap: 20px;">
                    <div style="flex: 1;">
                        <div>Data de Entrada na Assistência: _____/_____/________</div>
                        <div>Assinatura: ____________________________</div>
                    </div>
                    <div style="flex: 1;">
                        <div>Data de Entrega ao Cliente: _____/_____/________</div>
                        <div>Assinatura: ____________________________</div>
                    </div>
                </div>
            </div>
            `;
        }

        // 3. Função para gerar via COMPUTADOR/NOTEBOOK
        function gerarViaComputador(tipoVia, logoBase64, checklistHTML) {
            return `
            <div class="os-via-computador" style="width: 100%; max-width: 800px; margin: 0 auto; font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #222; background: #fff; border: 1.5px solid #222; padding: 8px; box-sizing: border-box;">
                <!-- Cabeçalho -->
                <div style="display: flex; align-items: center; gap: 8px;">
                    ${logoBase64 ? `<img src='${logoBase64}' alt='Logo' style='height: 38px; margin-right: 8px;'>` : ''}
                    <div style="font-size: 11px; line-height: 1.2;">
                        <div style="font-weight: bold; font-size: 13px; color: #222;">${empresa.nome || ''}</div>
                        <div>${[
                            empresa.cnpj ? 'CNPJ: ' + empresa.cnpj : '',
                            empresa.ie ? 'IE: ' + empresa.ie : ''                          
                        ].filter(Boolean).join(' | ')}</div>                        
                        <div>${[
                            empresa.endereco || '',
                            empresa.numero || '',
                            empresa.bairro || '',
                            empresa.cidade || '',
                            empresa.estado || '',
                            empresa.cep ? 'CEP: ' + empresa.cep : ''
                        ].filter(Boolean).join(' - ')}</div>
                        <div>${[
                            empresa.telefone || '',
                            empresa.email ? 'E-mail: ' + empresa.email : ''
                        ].filter(Boolean).join(' | ')}</div>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; border-bottom: 1.5px solid #222; padding-bottom: 2px;">
                    <div style="font-size: 13px; font-weight: bold;">ORDEM DE SERVIÇO</div>
                    <div style="font-size: 12px; font-weight: bold;">Nº: ${ordem.id}</div>
                </div>
                <!-- Dados Cliente -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                    <tr>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 8%; font-weight: bold;">Código</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 22%; font-weight: bold;">Nome</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Contato</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Telefone</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">CPF/CNPJ</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 16%; font-weight: bold;">UF</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${cliente ? cliente.id || '' : ''}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${cliente ? cliente.nome || '' : ''}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;"></td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${cliente ? cliente.telefone || '' : ''}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${cliente ? cliente.cpf_cnpj || '' : ''}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">SP</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid #222; padding: 2px 4px;"><b>Endereço:</b> ${cliente ? cliente.endereco || '' : ''}</td>
                        <td colspan="2" style="border: 1px solid #222; padding: 2px 4px;"><b>Cidade:</b> ${cliente ? cliente.cidade || '' : ''}</td>
                        <td colspan="2" style="border: 1px solid #222; padding: 2px 4px;"></td>
                    </tr>
                </table>
                <!-- Dados Equipamento -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                    <tr>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Equipamento</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Marca</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Modelo</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 18%; font-weight: bold;">Nº Série</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${equipamento ? equipamento.tipo || 'computador' : 'computador'}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${equipamento ? equipamento.marca || '' : ''}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${equipamento ? equipamento.modelo || '' : ''}</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">${equipamento ? equipamento.numero_serie || '' : ''}</td>
                    </tr>
                </table>
                <!-- Detalhes e Defeito Relatado lado a lado -->
                <div style="display: flex; gap: 10px; margin-bottom: 6px;">
                    <div style="flex: 1; border: 1px solid #222; min-height: 48px; padding: 4px 6px; display: flex; flex-direction: column; justify-content: flex-start;">
                        <b>Detalhes do Equipamento:</b>
                        <div style="min-height: 36px; white-space: pre-line;">${ordem.observacoes || '&nbsp;'}<br><br></div>
                    </div>
                    <div style="flex: 1; border: 1px solid #222; min-height: 48px; padding: 4px 6px; display: flex; flex-direction: column; justify-content: flex-start;">
                        <b>Defeito Relatado:</b>
                        <div style="min-height: 36px; white-space: pre-line;">${ordem.descricao_problema || '&nbsp;'}<br><br></div>
                    </div>
                </div>
                <!-- Checklist personalizado para computador/notebook -->
                <div style="border: 1px solid #222; margin-bottom: 6px; padding: 4px 6px;">
                    <div style="font-weight: bold; text-align: center; margin-bottom: 2px;">Check List</div>
                    ${checklistHTML}
                </div>
                <!-- Diagnóstico/Solução abaixo -->
                <div style="border: 1px solid #222; min-height: 48px; padding: 4px 6px; margin-bottom: 6px;">
                    <b>Diagnóstico/Solução:</b>
                    <div style="min-height: 36px; white-space: pre-line;">${ordem.status === 'Orçamento' ? 'Orçamento' : 'Diagnóstico'}<br><br></div>
                </div>
                <!-- Fechamento -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                    <tr>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 20%; font-weight: bold;">FECHAMENTO</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 20%;">Valor das Peças</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 20%;">Valor dos Serviços</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 20%;">Valor Frete</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; width: 20%; font-weight: bold; background: #eee;">TOTAL</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #222; padding: 2px 4px;"></td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">R$ 0,00</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">R$ 0,00</td>
                        <td style="border: 1px solid #222; padding: 2px 4px;">R$ 0,00</td>
                        <td style="border: 1px solid #222; padding: 2px 4px; font-weight: bold; background: #eee;">R$ 0,00</td>
                    </tr>
                </table>
                <!-- Termos -->
                <div style="font-size: 8px; margin-bottom: 2px;">
                    <div>01 - O equipamento não retirado no prazo de até 90 dias após o parecer ou aprovação do serviço, só será entregue mediante pagamento de taxa de R$ 15,00 (quinze reais) ao mês a título de seguro.</div>
                    <div>02 - A Assistência Técnica oferece garantia de 90 dias após a entrega do equipamento na peça trocada sem a violação do lacre de segurança, com exceção de: Limpezas, desoxidações, atualizações de software e desbloqueios.</div>
                    <div>03 - O Cliente é o total responsável pela procedência do equipamento, estando assim a Assistência Técnica isenta de qualquer responsabilidade.</div>
                    <div>04 - A Assistência Técnica não é responsável por arquivos contidos no equipamento como: fotos, músicas, vídeos, agenda de contatos, programas, aplicativos e jogos. Estas arquivos podem ser removidos em determinados serviços e o backup deverá ser efetuado pelo próprio cliente.</div>
                    <div>Declaro estar de acordo com os itens descritos acima e com os testes efetuados da lista de checagem do equipamento.</div>
                </div>
                <!-- Assinaturas -->
                <div style="display: flex; justify-content: space-between; font-size: 10px; margin-top: 8px; gap: 20px;">
                    <div style="flex: 1;">
                        <div>Data de Entrada na Assistência: _____/_____/________</div>
                        <div>Assinatura: ____________________________</div>
                    </div>
                    <div style="flex: 1;">
                        <div>Data de Entrega ao Cliente: _____/_____/________</div>
                        <div>Assinatura: ____________________________</div>
                    </div>
                </div>
            </div>
            `;
        }

        // 4. Gerar HTML das duas vias separadas por quebra de página
        const checklistCelularHTML = await generateChecklistHTML('Celular', checklistMarks);
        const checklistComputadorHTML = await generateChecklistHTML('Computador', checklistMarks);
        let html = '';
        if (tipoOS === 'celular') {
            html += gerarViaCelular('EMPRESA', logoBase64, checklistCelularHTML);
            html += gerarViaCelular('CLIENTE', logoBase64, checklistCelularHTML);
        } else {
            html += gerarViaComputador('EMPRESA', logoBase64, checklistComputadorHTML);
            html += gerarViaComputador('CLIENTE', logoBase64, checklistComputadorHTML);
        }
        // 5. Abrir janela de impressão
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>OS #${ordem.id}</title>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 5px; background: #f5f5f5; }
                    .os-via-celular, .os-via-computador {
                        background: white;
                        margin-bottom: 10px;
                        page-break-after: always;
                        width: 210mm;
                        min-height: 297mm;
                        max-width: 210mm;
                        max-height: 297mm;
                        box-sizing: border-box;
                        overflow: hidden;
                        padding: 16px;
                    }
                    .os-via-celular:last-child, .os-via-computador:last-child { page-break-after: avoid; }
                    @media print {
                        body { margin: 0; padding: 0; background: white; }
                        .os-via-celular, .os-via-computador {
                            page-break-after: always;
                            margin-bottom: 0;
                            width: 210mm;
                            min-height: 297mm;
                            max-width: 210mm;
                            max-height: 297mm;
                            box-sizing: border-box;
                            overflow: hidden;
                            padding: 16px;
                        }
                        .os-via-celular:last-child, .os-via-computador:last-child { page-break-after: avoid; }
                    }
                </style>
            </head>
            <body>${html}</body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => { printWindow.print(); }, 500);
    }

    // Checklist Functions
    static async loadChecklist() {
        try {
            const response = await fetch('api/checklist.php?action=get_items');
            const result = await response.json();
            
            if (result.success) {
                const items = result.data;
                
                // Organizar itens por categoria (ajustado para maiúscula)
                const checklistItems = {
                    celular: items.filter(item => item.categoria === 'Celular'),
                    computador: items.filter(item => item.categoria === 'Computador')
                };
                
                this.renderChecklist(checklistItems);
            } else {
                console.error('Erro ao carregar checklist:', result.error);
                // Fallback para itens hardcoded em caso de erro
                this.renderChecklist(this.getDefaultChecklistItems());
            }
        } catch (error) {
            console.error('Erro de conexão ao carregar checklist:', error);
            // Fallback para itens hardcoded em caso de erro
            this.renderChecklist(this.getDefaultChecklistItems());
        }
    }

    static getDefaultChecklistItems() {
        return {
            celular: [
                { id: 1, descricao: 'Tela / Display', options: ['OK', 'Trincada', 'Manchas', 'Sem imagem'] },
                { id: 2, descricao: 'Carcaça / Lateral', options: ['OK', 'Arranhada', 'Amassada', 'Solta'] },
                { id: 3, descricao: 'Traseira / Tampa', options: ['OK', 'Riscada', 'Trincada', 'Solta'] },
                { id: 4, descricao: 'Botões (Power, Volume)', options: ['OK', 'Duro', 'Sem resposta', ''] },
                { id: 5, descricao: 'Conector de carga', options: ['OK', 'Solto', 'Não carrega', ''] },
                { id: 6, descricao: 'Alto-falante / Fone', options: ['OK', 'Ruído', 'Sem som', ''] },
                { id: 7, descricao: 'Entrada P2 (fone)', options: ['OK', 'Com defeito', 'Não testado', ''] },
                { id: 8, descricao: 'Câmeras', options: ['OK', 'Com risco', 'Não funciona', ''] },
                { id: 9, descricao: 'Slot SIM / Cartão SD', options: ['OK', 'Sem gaveta', 'Com defeito', ''] },
                { id: 10, descricao: 'Touchscreen', options: ['OK', 'Com falhas', '', ''] },
                { id: 11, descricao: 'Sensor de digital / Face ID', options: ['OK', 'Não responde', '', ''] },
                { id: 12, descricao: 'Liga / Desliga', options: ['Sim', 'Não', '', ''] },
                { id: 13, descricao: 'Carregamento normal', options: ['Sim', 'Não', '', ''] },
                { id: 14, descricao: 'Reconhece chip (sinal)', options: ['Sim', 'Não', '', ''] },
                { id: 15, descricao: 'Conexão Wi-Fi', options: ['Sim', 'Não', '', ''] },
                { id: 16, descricao: 'Bluetooth', options: ['Sim', 'Não', '', ''] },
                { id: 17, descricao: 'Câmera frontal', options: ['Sim', 'Não', '', ''] },
                { id: 18, descricao: 'Câmera traseira', options: ['Sim', 'Não', '', ''] },
                { id: 19, descricao: 'Chamadas / Áudio', options: ['Sim', 'Não', '', ''] },
                { id: 20, descricao: 'Sensor de proximidade', options: ['Sim', 'Não', '', ''] }
            ],
            computador: [
                { id: 21, descricao: 'Gabinete / Carcaça / Tampa', options: ['OK', 'Quebrado', 'Riscado', 'Faltando peça'] },
                { id: 22, descricao: 'Tela / Monitor', options: ['OK', 'Trincada', 'Riscada', 'Pixels queimados'] },
                { id: 23, descricao: 'Teclado / Touchpad', options: ['OK', 'Teclas faltando', 'Não funciona', ''] },
                { id: 24, descricao: 'Portas USB / Rede / HDMI', options: ['OK', 'Danificadas', 'OXIDADAS', 'Faltando'] },
                { id: 25, descricao: 'Carregador / Fonte', options: ['OK', 'Não veio', 'Com defeito', 'Mal contato'] },
                { id: 26, descricao: 'Dobradiças (notebook)', options: ['OK', 'Quebradas', 'Travadas', ''] },
                { id: 27, descricao: 'Bateria (notebook)', options: ['OK', 'Não carrega', 'Inchada', 'Ausente'] },
                { id: 28, descricao: 'Periféricos extras (mouse, teclado, etc.)', options: ['OK', 'Não veio', 'Com defeito', ''] },
                { id: 29, descricao: 'Liga / Desliga corretamente', options: ['Sim', 'Não', '', ''] },
                { id: 30, descricao: 'Inicializa sistema operacional', options: ['Sim', 'Não', '', ''] },
                { id: 31, descricao: 'Conexão Wi-Fi / Rede', options: ['Sim', 'Não', '', ''] },
                { id: 32, descricao: 'Áudio / Som', options: ['Sim', 'Não', '', ''] },
                { id: 33, descricao: 'Vídeo / Resolução', options: ['Sim', 'Não', '', ''] },
                { id: 34, descricao: 'USBs / Leitor de cartão', options: ['Sim', 'Não', '', ''] },
                { id: 35, descricao: 'Webcam / Microfone', options: ['Sim', 'Não', '', ''] },
                { id: 36, descricao: 'Ventoinha / Ruído anormal', options: ['Sim', 'Não', '', ''] }
            ]
        };
    }

    static renderChecklist(checklistItems) {
        // Renderizar Celular
        const celularContainer = document.getElementById('checklist-celular');
        if (celularContainer) {
            celularContainer.innerHTML = checklistItems.celular && checklistItems.celular.length > 0 ? checklistItems.celular.map(item => `
                <div class="form-check mb-2">
                    <input class="form-check-input checklist-item" type="checkbox" 
                           id="check_${item.id}" value="${item.id}" data-item-id="${item.id}">
                    <label class="form-check-label" for="check_${item.id}">
                        ${item.nome}
                    </label>
                </div>
            `).join('') : '<p class="text-muted">Nenhum item cadastrado para celular.</p>';
        }
        // Renderizar Computador
        const computadorContainer = document.getElementById('checklist-computador');
        if (computadorContainer) {
            computadorContainer.innerHTML = checklistItems.computador && checklistItems.computador.length > 0 ? checklistItems.computador.map(item => `
                <div class="form-check mb-2">
                    <input class="form-check-input checklist-item" type="checkbox" 
                           id="check_${item.id}" value="${item.id}" data-item-id="${item.id}">
                    <label class="form-check-label" for="check_${item.id}">
                        ${item.nome}
                    </label>
                </div>
            `).join('') : '<p class="text-muted">Nenhum item cadastrado para computador.</p>';
        }
    }

    static renderChecklistItem(item) {
        const options = item.options || ['OK', 'Com defeito', 'Não testado', ''];
        let html = `<div class="checklist-item mb-2">
            <div class="d-flex align-items-center">
                <input type="checkbox" class="form-check-input me-2" id="checklist-${item.id}" data-item-id="${item.id}">
                <label for="checklist-${item.id}" class="form-check-label me-2" style="min-width: 120px; font-size:0.9rem;">${item.descricao}</label>
                <select class="form-select form-select-sm" style="width: auto; min-width:100px;" data-item-id="${item.id}">
                    <option value="">Selecione</option>`;
        
        options.forEach(option => {
            html += `<option value="${option}">${option}</option>`;
        });
        
        html += `</select>
            </div>
        </div>`;
        
        return html;
    }

    static getChecklistMarks() {
        const checkedItems = [];
        const checkboxes = document.querySelectorAll('.checklist-item:checked');
        
        checkboxes.forEach(checkbox => {
            checkedItems.push(parseInt(checkbox.getAttribute('data-item-id')));
        });
        
        return checkedItems;
    }

    static async loadChecklistMarks(ordemId) {
        try {
            const response = await fetch(`api/checklist.php?action=get_marks&ordem_id=${ordemId}`);
            const result = await response.json();
            
            if (result.success) {
                // Marcar checkboxes baseado nos dados salvos
                result.data.forEach(mark => {
                    const checkbox = document.getElementById(`check_${mark.checklist_item_id}`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
        } catch (error) {
            console.error('Erro ao carregar marcas do checklist:', error);
        }
    }

    static clearChecklist() {
        const checkboxes = document.querySelectorAll('.checklist-item');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    static initClienteAutocomplete() {
        const input = document.getElementById('ordem-cliente-nome');
        const hidden = document.getElementById('ordem-cliente');
        let dropdown = document.getElementById('autocomplete-clientes-dropdown');
        if (!dropdown) {
            dropdown = document.createElement('div');
            dropdown.id = 'autocomplete-clientes-dropdown';
            dropdown.style.position = 'absolute';
            dropdown.style.zIndex = '9999';
            dropdown.style.background = '#fff';
            dropdown.style.border = '1px solid #ccc';
            dropdown.style.width = input.offsetWidth + 'px';
            dropdown.style.maxHeight = '200px';
            dropdown.style.overflowY = 'auto';
            dropdown.style.display = 'none';
            input.parentNode.appendChild(dropdown);
        }
        let lastResults = [];
        input.oninput = async function() {
            const q = input.value.trim();
            hidden.value = '';
            if (q.length < 2) {
                dropdown.style.display = 'none';
                return;
            }
            console.log('Buscando clientes por:', q);
            const resp = await fetch('api/clientes.php?action=search&q=' + encodeURIComponent(q));
            const data = await resp.json();
            console.log('Resultado da busca:', data);
            dropdown.innerHTML = '';
            lastResults = [];
            if (data.success && data.data && data.data.length > 0) {
                lastResults = data.data;
                data.data.forEach((cliente, idx) => {
                    const item = document.createElement('div');
                    item.textContent = cliente.nome + (cliente.cpf_cnpj ? ' (' + cliente.cpf_cnpj + ')' : '');
                    item.style.padding = '4px 8px';
                    item.style.cursor = 'pointer';
                    item.onmousedown = function(e) { // usar onmousedown para garantir seleção antes do blur
                        input.value = item.textContent;
                        hidden.value = cliente.id;
                        dropdown.style.display = 'none';
                    };
                    dropdown.appendChild(item);
                });
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        };
        input.onblur = function() {
            setTimeout(() => { dropdown.style.display = 'none'; }, 200);
        };
        input.onfocus = function() {
            if (input.value.length >= 2) input.oninput();
        };
        input.onkeydown = function(e) {
            if (e.key === 'Enter' && dropdown.style.display === 'block') {
                e.preventDefault();
                const first = dropdown.querySelector('div');
                if (first) {
                    first.dispatchEvent(new MouseEvent('mousedown'));
                }
            } else {
                hidden.value = '';
            }
        };
    }
}

// Funções utilitárias para popular selects do modal de equipamento
async function popularTiposMarcasModelos() {
  // Buscar tipos
  const tiposResp = await fetch('api/equipamentos-cadastros.php?entidade=tipo');
  const tipos = (await tiposResp.json()).data || [];
  const tipoSelect = document.getElementById('novoEquipamentoTipo');
  tipoSelect.innerHTML = '<option value="">Selecione o tipo</option>';
  tipos.forEach(t => {
    tipoSelect.innerHTML += `<option value="${t.id}">${t.nome}</option>`;
  });

  // Buscar marcas
  const marcasResp = await fetch('api/equipamentos-cadastros.php?entidade=marca');
  const marcas = (await marcasResp.json()).data || [];
  const marcaSelect = document.getElementById('novoEquipamentoMarca');
  marcaSelect.innerHTML = '<option value="">Selecione a marca</option>';
  marcas.forEach(m => {
    marcaSelect.innerHTML += `<option value="${m.id}">${m.nome}</option>`;
  });

  // Limpar modelos
  const modeloSelect = document.getElementById('novoEquipamentoModelo');
  modeloSelect.innerHTML = '<option value="">Selecione o modelo</option>';
}

async function popularModelosPorTipoMarca() {
  const tipoId = document.getElementById('novoEquipamentoTipo').value;
  const marcaId = document.getElementById('novoEquipamentoMarca').value;
  const modeloSelect = document.getElementById('novoEquipamentoModelo');
  modeloSelect.innerHTML = '<option value="">Selecione o modelo</option>';
  if (tipoId && marcaId) {
    const modelosResp = await fetch(`api/equipamentos-cadastros.php?entidade=modelo&tipo_id=${tipoId}&marca_id=${marcaId}`);
    const modelos = (await modelosResp.json()).data || [];
    modelos.forEach(mo => {
      modeloSelect.innerHTML += `<option value="${mo.id}">${mo.nome}</option>`;
    });
  }
}

// Evento para abrir o modal e popular selects corretamente
const btnNovoEquipamento = document.getElementById('btnNovoEquipamento');
if (btnNovoEquipamento) {
  btnNovoEquipamento.addEventListener('click', function() {
    // Abrir o modal primeiro
    const modal = new bootstrap.Modal(document.getElementById('modalNovoEquipamento'));
    modal.show();
    // Depois popular os selects
    setTimeout(async () => {
      await popularTiposMarcasModelos();
      document.getElementById('novoEquipamentoTipo').addEventListener('change', popularModelosPorTipoMarca);
      document.getElementById('novoEquipamentoMarca').addEventListener('change', popularModelosPorTipoMarca);
      // Preencher cliente oculto
      const clienteId = document.getElementById('ordem-cliente').value;
      document.getElementById('novoEquipamentoCliente').value = clienteId;
    }, 300);
  });
}

// Submissão do formulário de novo equipamento
const formNovoEquipamento = document.getElementById('formNovoEquipamento');
if (formNovoEquipamento) {
  formNovoEquipamento.addEventListener('submit', async function(e) {
    e.preventDefault();
    const cliente_id = document.getElementById('novoEquipamentoCliente').value;
    const tipo_id = document.getElementById('novoEquipamentoTipo').value;
    const marca_id = document.getElementById('novoEquipamentoMarca').value;
    const modelo_id = document.getElementById('novoEquipamentoModelo').value;
    const numero_serie = document.getElementById('novoEquipamentoNumeroSerie').value;
    const observacoes = document.getElementById('novoEquipamentoObservacoes').value;
    if (!cliente_id || !tipo_id || !marca_id || !modelo_id) {
      alert('Cliente, tipo, marca e modelo são obrigatórios!');
      return;
    }
    const body = JSON.stringify({ cliente_id, tipo_id, marca_id, modelo_id, numero_serie, observacoes });
    try {
      const response = await fetch('api/equipamentos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body
      });
      const result = await response.json();
      if (result.success && result.id) {
        bootstrap.Modal.getInstance(document.getElementById('modalNovoEquipamento')).hide();
        await Ordens.loadEquipamentosByCliente(cliente_id);
        setTimeout(() => {
          document.getElementById('ordem-equipamento').value = result.id;
        }, 200);
        formNovoEquipamento.reset();
      } else {
        alert('Erro ao cadastrar equipamento: ' + (result.error || ''));
      }
    } catch (err) {
      alert('Erro de conexão ao cadastrar equipamento.');
    }
  });
}

// Forçar clique no label a marcar/desmarcar o checkbox correspondente (caso algum CSS atrapalhe)
document.addEventListener('click', function(e) {
  if (e.target.classList.contains('form-check-label')) {
    const forId = e.target.getAttribute('for');
    if (forId) {
      const checkbox = document.getElementById(forId);
      if (checkbox && checkbox.type === 'checkbox') {
        checkbox.checked = !checkbox.checked;
        e.preventDefault();
      }
    }
  }
});