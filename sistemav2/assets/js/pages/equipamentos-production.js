// Equipamentos Module
class Equipamentos {
    static currentEquipamentos = [];
    static filteredEquipamentos = [];
    static currentPage = 1;
    static itemsPerPage = 10;

    static async render() {
        const content = document.getElementById('page-content');
        
        content.innerHTML = `
            <div class="fade-in">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-laptop text-primary"></i> Equipamentos</h2>
                    <div>
                        <button class="btn btn-primary" onclick="Equipamentos.showAddModal()">
                            <i class="fas fa-plus"></i> Novo Equipamento
                        </button>
                        <div class="dropdown d-inline-block ms-2">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="submenuEquipamentos" data-bs-toggle="dropdown" aria-expanded="false">
                                Gerenciar
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="submenuEquipamentos">
                                <li><a class="dropdown-item" href="#" id="btnCadastrarTipo">Cadastrar Tipo</a></li>
                                <li><a class="dropdown-item" href="#" id="btnCadastrarMarca">Cadastrar Marca</a></li>
                                <li><a class="dropdown-item" href="#" id="btnCadastrarModelo">Cadastrar Modelo</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="search-filter-container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search-equipamentos" 
                                       placeholder="Buscar por marca, modelo ou número de série...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filter-cliente">
                                <option value="">Todos os clientes</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filter-tipo">
                                <option value="">Todos os tipos</option>
                                <option value="Smartphone">Smartphone</option>
                                <option value="Tablet">Tablet</option>
                                <option value="Notebook">Notebook</option>
                                <option value="Desktop">Desktop</option>
                                <option value="Televisão">Televisão</option>
                                <option value="Console">Console</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-secondary me-2" id="btnLimparFiltrosEquip">Limpar</button>
                        </div>
                    </div>
                </div>

                <!-- Equipamentos Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Equipamentos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Tipo</th>
                                        <th>Marca/Modelo</th>
                                        <th>Número de Série</th>
                                        <th>Data Cadastro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="equipamentos-table-body">
                                    <!-- Equipment will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <nav aria-label="Paginação">
                            <ul class="pagination" id="equipamentos-pagination">
                                <!-- Pagination will be rendered here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Equipamento Modal -->
            <div class="modal fade" id="equipamentoModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="equipamentoModalTitle">Novo Equipamento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="equipamentoForm">
                            <div class="modal-body">
                                <input type="hidden" id="equipamento-id">
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating">
                                            <select class="form-select" id="equipamento-cliente" required>
                                                <option value="">Selecione o cliente</option>
                                                <!-- Options will be loaded dynamically -->
                                            </select>
                                            <label for="equipamento-cliente">Cliente *</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <select class="form-select" id="equipamento-tipo" required>
                                                <option value="">Selecione o tipo</option>
                                            </select>
                                            <label for="equipamento-tipo">Tipo *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <select class="form-select" id="equipamento-marca" required>
                                                <option value="">Selecione a marca</option>
                                            </select>
                                            <label for="equipamento-marca">Marca *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <select class="form-select" id="equipamento-modelo" required>
                                                <option value="">Selecione o modelo</option>
                                            </select>
                                            <label for="equipamento-modelo">Modelo *</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="equipamento-serie">
                                            <label for="equipamento-serie">Número de Série</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="equipamento-observacoes" style="height: 100px"></textarea>
                                            <label for="equipamento-observacoes">Observações</label>
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

            <!-- Modais de Cadastro -->
            <div class="modal fade" id="modalCadastrarTipo" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form class="modal-content" id="formCadastrarTipo">
                  <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Tipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="tipo-nome" class="form-label">Nome do Tipo</label>
                      <input type="text" class="form-control" id="tipo-nome" required>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="modal fade" id="modalCadastrarMarca" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form class="modal-content" id="formCadastrarMarca">
                  <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="marca-nome" class="form-label">Nome da Marca</label>
                      <input type="text" class="form-control" id="marca-nome" required>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="modal fade" id="modalCadastrarModelo" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form class="modal-content" id="formCadastrarModelo">
                  <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Modelo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="modelo-tipo" class="form-label">Tipo</label>
                      <select class="form-select" id="modelo-tipo" required></select>
                    </div>
                    <div class="mb-3">
                      <label for="modelo-marca" class="form-label">Marca</label>
                      <select class="form-select" id="modelo-marca" required></select>
                    </div>
                    <div class="mb-3">
                      <label for="modelo-nome" class="form-label">Nome do Modelo</label>
                      <input type="text" class="form-control" id="modelo-nome" required>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                  </div>
                </form>
              </div>
            </div>
        `;

        await this.loadClientes();
        await this.loadEquipamentos();
        this.initEventListeners();
        // Adiciona listeners para os formulários de cadastro de tipo, marca e modelo
        this.initCadastroListeners();
        // Check for URL parameters
        this.checkUrlParameters();
        await this.popularSelectsEquipamento();
    }

    static checkUrlParameters() {
        const urlParams = new URLSearchParams(window.location.search);
        const clienteId = urlParams.get('cliente');
        
        if (clienteId) {
            // Limpa o parâmetro da URL
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
            
            // Apenas abrir modal de cadastro de equipamento já com o cliente selecionado
            setTimeout(() => {
                const btnNovoEquip = document.querySelector('button[onclick*="Equipamentos.showAddModal"]') || document.getElementById('btn-novo-equipamento');
                if (btnNovoEquip) {
                    btnNovoEquip.click();
                    setTimeout(() => {
                        const selectCliente = document.getElementById('equipamento-cliente');
                        if (selectCliente) selectCliente.value = clienteId;
                    }, 300);
                }
            }, 500);
        }
    }

    static initEventListeners() {
        // Verificações de existência dos elementos
        const requiredIds = [
            'search-equipamentos',
            'filter-cliente',
            'filter-tipo',
            'btnLimparFiltrosEquip',
            'equipamentoForm',
            'btnCadastrarTipo',
            'btnCadastrarMarca',
            'btnCadastrarModelo',
        ];
        for (const id of requiredIds) {
            if (!document.getElementById(id)) {
                console.warn(`Elemento com id '${id}' não encontrado no DOM!`);
            }
        }
        // Search functionality
        const searchInput = document.getElementById('search-equipamentos');
        if (searchInput) {
            searchInput.addEventListener('input', async (e) => {
                await this.filterEquipamentos();
            });
        }
        // Filter functionality
        const filterCliente = document.getElementById('filter-cliente');
        if (filterCliente) {
            filterCliente.addEventListener('change', async () => {
                await this.filterEquipamentos();
            });
        }
        const filterTipo = document.getElementById('filter-tipo');
        if (filterTipo) {
            filterTipo.addEventListener('change', async () => {
                await this.filterEquipamentos();
            });
        }
        // Botão Limpar
        const btnLimpar = document.getElementById('btnLimparFiltrosEquip');
        if (btnLimpar) {
            btnLimpar.addEventListener('click', async () => {
                if (searchInput) searchInput.value = '';
                if (filterCliente) filterCliente.value = '';
                if (filterTipo) filterTipo.value = '';
                await this.filterEquipamentos();
            });
        }
        // Botão Filtrar
        const btnFiltrar = document.getElementById('btnFiltrarEquip');
        if (btnFiltrar) {
            btnFiltrar.addEventListener('click', async () => {
                await this.filterEquipamentos();
            });
        }
        // Form submission
        const formEquipamento = document.getElementById('equipamentoForm');
        if (formEquipamento) {
            formEquipamento.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveEquipamento();
            });
        }
        // Abrir modal de cadastro de Tipo
        const btnTipo = document.getElementById('btnCadastrarTipo');
        if (btnTipo) {
            btnTipo.addEventListener('click', (e) => {
                e.preventDefault();
                document.getElementById('tipo-nome').value = '';
                const modal = new bootstrap.Modal(document.getElementById('modalCadastrarTipo'));
                modal.show();
            });
        }
        // Abrir modal de cadastro de Marca
        const btnMarca = document.getElementById('btnCadastrarMarca');
        if (btnMarca) {
            btnMarca.addEventListener('click', (e) => {
                e.preventDefault();
                document.getElementById('marca-nome').value = '';
                const modal = new bootstrap.Modal(document.getElementById('modalCadastrarMarca'));
                modal.show();
            });
        }
        // Abrir modal de cadastro de Modelo
        const btnModelo = document.getElementById('btnCadastrarModelo');
        if (btnModelo) {
            btnModelo.addEventListener('click', async (e) => {
                e.preventDefault();
                document.getElementById('modelo-nome').value = '';
                await Equipamentos.popularTiposMarcasModelo();
                const modal = new bootstrap.Modal(document.getElementById('modalCadastrarModelo'));
                modal.show();
            });
        }
    }

    static initCadastroListeners() {
        // Cadastro de Tipo
        const formTipo = document.getElementById('formCadastrarTipo');
        if (formTipo) {
            formTipo.addEventListener('submit', async (e) => {
                e.preventDefault();
                const nome = document.getElementById('tipo-nome').value.trim();
                if (!nome) return showAlert('Atenção', 'Informe o nome do tipo!', 'warning');
                try {
                    const response = await fetch('api/equipamentos-cadastros.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ entidade: 'tipo', nome })
                    });
                    const result = await response.json();
                    if (result.success) {
                        showAlert('Sucesso!', 'Tipo cadastrado com sucesso!');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalCadastrarTipo'));
                        modal.hide();
                        formTipo.reset();
                        await Equipamentos.popularTiposMarcasModelo();
                    } else {
                        showAlert('Erro', result.error || 'Erro ao cadastrar tipo', 'error');
                    }
                } catch (error) {
                    showAlert('Erro', 'Erro de conexão ao cadastrar tipo', 'error');
                }
            });
        }
        // Cadastro de Marca
        const formMarca = document.getElementById('formCadastrarMarca');
        if (formMarca) {
            formMarca.addEventListener('submit', async (e) => {
                e.preventDefault();
                const nome = document.getElementById('marca-nome').value.trim();
                if (!nome) return showAlert('Atenção', 'Informe o nome da marca!', 'warning');
                try {
                    const response = await fetch('api/equipamentos-cadastros.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ entidade: 'marca', nome })
                    });
                    const result = await response.json();
                    if (result.success) {
                        showAlert('Sucesso!', 'Marca cadastrada com sucesso!');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalCadastrarMarca'));
                        modal.hide();
                        formMarca.reset();
                        await Equipamentos.popularTiposMarcasModelo();
                    } else {
                        showAlert('Erro', result.error || 'Erro ao cadastrar marca', 'error');
                    }
                } catch (error) {
                    showAlert('Erro', 'Erro de conexão ao cadastrar marca', 'error');
                }
            });
        }
        // Cadastro de Modelo
        const formModelo = document.getElementById('formCadastrarModelo');
        if (formModelo) {
            formModelo.addEventListener('submit', async (e) => {
                e.preventDefault();
                const nome = document.getElementById('modelo-nome').value.trim();
                const tipo_id = document.getElementById('modelo-tipo').value;
                const marca_id = document.getElementById('modelo-marca').value;
                if (!nome || !tipo_id || !marca_id) return showAlert('Atenção', 'Preencha todos os campos!', 'warning');
                try {
                    const response = await fetch('api/equipamentos-cadastros.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ entidade: 'modelo', nome, tipo_id, marca_id })
                    });
                    const result = await response.json();
                    if (result.success) {
                        showAlert('Sucesso!', 'Modelo cadastrado com sucesso!');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalCadastrarModelo'));
                        modal.hide();
                        formModelo.reset();
                        await Equipamentos.popularTiposMarcasModelo();
                    } else {
                        showAlert('Erro', result.error || 'Erro ao cadastrar modelo', 'error');
                    }
                } catch (error) {
                    showAlert('Erro', 'Erro de conexão ao cadastrar modelo', 'error');
                }
            });
        }
    }

    static async loadClientes() {
        try {
            const response = await fetch('api/clientes.php?action=list');
            const result = await response.json();
            
            if (result.success) {
                const clientes = result.data;
                
                // Load clients in filter dropdown
                const filterSelect = document.getElementById('filter-cliente');
                filterSelect.innerHTML = '<option value="">Todos os clientes</option>';
                
                // Load clients in form dropdown
                const formSelect = document.getElementById('equipamento-cliente');
                formSelect.innerHTML = '<option value="">Selecione o cliente</option>';
                
                clientes.forEach(cliente => {
                    filterSelect.innerHTML += `<option value="${cliente.id}">${cliente.nome}</option>`;
                    formSelect.innerHTML += `<option value="${cliente.id}">${cliente.nome}</option>`;
                });
                
                // Atualizar localStorage para compatibilidade
                localStorage.setItem('clientes', JSON.stringify(clientes));
            } else {
                showAlert('Erro', result.error || 'Erro ao carregar clientes', 'error');
            }
        } catch (error) {
            showAlert('Erro', 'Erro de conexão ao carregar clientes', 'error');
        }
    }

    static async loadEquipamentos() {
        try {
            // Buscar dados do backend
            const response = await fetch('api/equipamentos.php?action=list');
            const result = await response.json();
            if (result.success) {
                this.currentEquipamentos = result.data;
                this.filteredEquipamentos = [...this.currentEquipamentos];
                // Atualizar localStorage para compatibilidade (opcional)
                localStorage.setItem('equipamentos', JSON.stringify(this.currentEquipamentos));
                await this.renderTable();
                this.renderPagination();
            } else {
                showAlert('Erro', result.error || 'Erro ao carregar equipamentos', 'error');
            }
        } catch (error) {
            showAlert('Erro', 'Erro de conexão ao carregar equipamentos', 'error');
        }
    }

    static async filterEquipamentos() {
        const searchTerm = document.getElementById('search-equipamentos').value.toLowerCase();
        const clienteFilter = document.getElementById('filter-cliente').value;
        const tipoFilter = document.getElementById('filter-tipo').value;

        this.filteredEquipamentos = this.currentEquipamentos.filter(equipamento => {
            const matchesSearch = !searchTerm || 
                equipamento.marca.toLowerCase().includes(searchTerm) ||
                equipamento.modelo.toLowerCase().includes(searchTerm) ||
                equipamento.numero_serie.toLowerCase().includes(searchTerm);

            const matchesCliente = !clienteFilter || 
                equipamento.cliente_id === parseInt(clienteFilter);

            const matchesTipo = !tipoFilter || 
                equipamento.tipo === tipoFilter;

            return matchesSearch && matchesCliente && matchesTipo;
        });

        this.currentPage = 1;
        await this.renderTable();
        this.renderPagination();
    }

    static async filterByClient(clienteId) {
        document.getElementById('filter-cliente').value = clienteId;
        await this.filterEquipamentos();
    }

    static async renderTable() {
        const tbody = document.getElementById('equipamentos-table-body');
        tbody.innerHTML = '';

        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const equipamentosToShow = this.filteredEquipamentos.slice(startIndex, endIndex);

        if (equipamentosToShow.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nenhum equipamento encontrado</td></tr>';
            return;
        }

        // Buscar clientes do banco de dados
        let clientes = [];
        try {
            const response = await fetch('api/clientes.php?action=list');
            const result = await response.json();
            if (result.success) {
                clientes = result.data;
            }
        } catch (error) {
            console.error('Erro ao carregar clientes:', error);
        }

        equipamentosToShow.forEach(equipamento => {
            const cliente = clientes.find(c => c.id === equipamento.cliente_id);
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <strong>${cliente ? cliente.nome : 'Cliente não encontrado'}</strong>
                </td>
                <td>
                    <span class="badge bg-primary">${equipamento.tipo}</span>
                </td>
                <td>
                    <strong>${equipamento.marca}</strong><br>
                    <small class="text-muted">${equipamento.modelo}</small>
                </td>
                <td>
                    <code>${equipamento.numero_serie || 'N/A'}</code>
                </td>
                <td>
                    ${formatDate(equipamento.criado_em)}
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info" onclick="Equipamentos.showDetails(${equipamento.id})" title="Detalhes">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-primary" onclick="Equipamentos.showEditModal(${equipamento.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-success" onclick="Equipamentos.createOS(${equipamento.id})" title="Nova OS">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="Equipamentos.deleteEquipamento(${equipamento.id})" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    static renderPagination() {
        const pagination = document.getElementById('equipamentos-pagination');
        const totalPages = Math.ceil(this.filteredEquipamentos.length / this.itemsPerPage);

        if (totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let paginationHTML = '';

        // Previous button
        paginationHTML += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Equipamentos.goToPage(${this.currentPage - 1})">Anterior</a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="Equipamentos.goToPage(${i})">${i}</a>
                </li>
            `;
        }

        // Next button
        paginationHTML += `
            <li class="page-item ${this.currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Equipamentos.goToPage(${this.currentPage + 1})">Próximo</a>
            </li>
        `;

        pagination.innerHTML = paginationHTML;
    }

    static async goToPage(page) {
        const totalPages = Math.ceil(this.filteredEquipamentos.length / this.itemsPerPage);
        if (page >= 1 && page <= totalPages) {
            this.currentPage = page;
            await this.renderTable();
            this.renderPagination();
        }
    }

    static showAddModal() {
        document.getElementById('equipamentoModalTitle').textContent = 'Novo Equipamento';
        document.getElementById('equipamentoForm').reset();
        document.getElementById('equipamento-id').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('equipamentoModal'));
        modal.show();
    }

    static async showEditModal(equipamentoId) {
        const equipamento = this.currentEquipamentos.find(e => e.id === equipamentoId);
        if (!equipamento) return;

        document.getElementById('equipamentoModalTitle').textContent = 'Editar Equipamento';
        document.getElementById('equipamento-id').value = equipamento.id;
        document.getElementById('equipamento-cliente').value = equipamento.cliente_id;
        // Popular selects dinâmicos e selecionar os valores corretos
        await this.popularSelectsEquipamento();
        // Buscar ids de tipo, marca e modelo pelos nomes
        let tipoId = '', marcaId = '', modeloId = '';
        // Buscar tipo
        const tiposResp = await fetch('api/equipamentos-cadastros.php?entidade=tipo');
        const tipos = (await tiposResp.json()).data || [];
        const tipoObj = tipos.find(t => t.nome === equipamento.tipo);
        if (tipoObj) tipoId = tipoObj.id;
        // Buscar marca
        const marcasResp = await fetch('api/equipamentos-cadastros.php?entidade=marca');
        const marcas = (await marcasResp.json()).data || [];
        const marcaObj = marcas.find(m => m.nome === equipamento.marca);
        if (marcaObj) marcaId = marcaObj.id;
        // Buscar modelo
        let modelos = [];
        if (tipoId && marcaId) {
            const modelosResp = await fetch(`api/equipamentos-cadastros.php?entidade=modelo&tipo_id=${tipoId}&marca_id=${marcaId}`);
            modelos = (await modelosResp.json()).data || [];
            const modeloObj = modelos.find(mo => mo.nome === equipamento.modelo);
            if (modeloObj) modeloId = modeloObj.id;
        }
        // Selecionar nos selects
        document.getElementById('equipamento-tipo').value = tipoId;
        await this.atualizarMarcasModelos();
        document.getElementById('equipamento-marca').value = marcaId;
        await this.atualizarModelos();
        document.getElementById('equipamento-modelo').value = modeloId;
        document.getElementById('equipamento-serie').value = equipamento.numero_serie || '';
        document.getElementById('equipamento-observacoes').value = equipamento.observacoes || '';

        const modal = new bootstrap.Modal(document.getElementById('equipamentoModal'));
        modal.show();
    }

    static async saveEquipamento() {
        const id = document.getElementById('equipamento-id').value;
        const equipamento = {
            cliente_id: parseInt(document.getElementById('equipamento-cliente').value),
            tipo_id: document.getElementById('equipamento-tipo').value,
            marca_id: document.getElementById('equipamento-marca').value,
            modelo_id: document.getElementById('equipamento-modelo').value,
            numero_serie: document.getElementById('equipamento-serie').value,
            observacoes: document.getElementById('equipamento-observacoes').value
        };

        try {
            let url = 'api/equipamentos.php';
            let method = 'POST';
            let body;
            let headers = { 'Content-Type': 'application/json' };
            if (id) {
                // Editar equipamento existente
                method = 'PUT';
                equipamento.id = parseInt(id);
                body = JSON.stringify(equipamento);
            } else {
                // Novo cadastro
                body = JSON.stringify(equipamento);
            }

            const response = await fetch(url, {
                method: method,
                body: body,
                headers: headers
            });

            const result = await response.json();
            
            if (result.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('equipamentoModal'));
                modal.hide();

                // Reload table
                await this.loadEquipamentos();

                // Show success message
                showAlert('Sucesso!', `Equipamento ${id ? 'atualizado' : 'cadastrado'} com sucesso!`);
            } else {
                showAlert('Erro', result.error || 'Erro ao salvar equipamento', 'error');
            }
        } catch (error) {
            showAlert('Erro', 'Erro de conexão ao salvar equipamento', 'error');
        }
    }

    static async deleteEquipamento(equipamentoId) {
        showConfirm(
            'Confirmar exclusão',
            'Tem certeza que deseja excluir este equipamento? Esta ação não pode ser desfeita.',
            async () => {
                try {
                    const response = await fetch(`api/equipamentos.php?id=${equipamentoId}`, {
                        method: 'DELETE'
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        await this.loadEquipamentos();
                        showAlert('Sucesso!', 'Equipamento excluído com sucesso!');
                    } else {
                        showAlert('Erro', result.error || 'Erro ao excluir equipamento', 'error');
                    }
                } catch (error) {
                    showAlert('Erro', 'Erro de conexão ao excluir equipamento', 'error');
                }
            }
        );
    }

    static showDetails(equipamentoId) {
        const equipamento = this.currentEquipamentos.find(e => e.id === equipamentoId);
        const clientes = JSON.parse(localStorage.getItem('clientes') || '[]');
        const cliente = clientes.find(c => c.id === equipamento.cliente_id);

        if (!equipamento) return;

        Swal.fire({
            title: 'Detalhes do Equipamento',
            html: `
                <div class="text-left">
                    <p><strong>Cliente:</strong> ${cliente ? cliente.nome : 'N/A'}</p>
                    <p><strong>Tipo:</strong> ${equipamento.tipo}</p>
                    <p><strong>Marca:</strong> ${equipamento.marca}</p>
                    <p><strong>Modelo:</strong> ${equipamento.modelo}</p>
                    <p><strong>Número de Série:</strong> ${equipamento.numero_serie || 'N/A'}</p>
                    <p><strong>Observações:</strong> ${equipamento.observacoes || 'Nenhuma'}</p>
                    <p><strong>Cadastrado em:</strong> ${formatDateTime(equipamento.criado_em)}</p>
                </div>
            `,
            confirmButtonColor: '#1e3a8a',
            confirmButtonText: 'Fechar'
        });
    }

    static createOS(equipamentoId) {
        // Redirect to orders page with equipment parameter
        window.location.href = `ordens.php?equipamento=${equipamentoId}`;
    }

    static async popularTiposMarcasModelo() {
        // Popular tipos
        const tiposResp = await fetch('api/equipamentos-cadastros.php?entidade=tipo');
        const tipos = (await tiposResp.json()).data || [];
        const selectTipo = document.getElementById('modelo-tipo');
        selectTipo.innerHTML = '<option value="">Selecione o tipo</option>';
        tipos.forEach(t => {
            selectTipo.innerHTML += `<option value="${t.id}">${t.nome}</option>`;
        });
        // Popular marcas
        const marcasResp = await fetch('api/equipamentos-cadastros.php?entidade=marca');
        const marcas = (await marcasResp.json()).data || [];
        const selectMarca = document.getElementById('modelo-marca');
        selectMarca.innerHTML = '<option value="">Selecione a marca</option>';
        marcas.forEach(m => {
            selectMarca.innerHTML += `<option value="${m.id}">${m.nome}</option>`;
        });
    }

    static async popularSelectsEquipamento() {
        // Carregar tipos
        const tipoSelect = document.getElementById('equipamento-tipo');
        const marcaSelect = document.getElementById('equipamento-marca');
        const modeloSelect = document.getElementById('equipamento-modelo');
        if (!tipoSelect || !marcaSelect || !modeloSelect) return;
        // Limpar
        tipoSelect.innerHTML = '<option value="">Selecione o tipo</option>';
        marcaSelect.innerHTML = '<option value="">Selecione a marca</option>';
        modeloSelect.innerHTML = '<option value="">Selecione o modelo</option>';
        // Buscar tipos
        const tiposResp = await fetch('api/equipamentos-cadastros.php?entidade=tipo');
        const tipos = (await tiposResp.json()).data || [];
        tipos.forEach(t => {
            tipoSelect.innerHTML += `<option value="${t.id}">${t.nome}</option>`;
        });
        // Buscar marcas
        const marcasResp = await fetch('api/equipamentos-cadastros.php?entidade=marca');
        const marcas = (await marcasResp.json()).data || [];
        marcas.forEach(m => {
            marcaSelect.innerHTML += `<option value="${m.id}">${m.nome}</option>`;
        });
        // Eventos para atualizar marcas/modelos
        tipoSelect.addEventListener('change', async () => {
            // Atualizar modelos e marcas
            await Equipamentos.atualizarMarcasModelos();
        });
        marcaSelect.addEventListener('change', async () => {
            // Atualizar modelos
            await Equipamentos.atualizarModelos();
        });
    }
    static async atualizarMarcasModelos() {
        const tipoId = document.getElementById('equipamento-tipo').value;
        const marcaSelect = document.getElementById('equipamento-marca');
        const modeloSelect = document.getElementById('equipamento-modelo');
        // Atualizar marcas (opcional: pode filtrar por tipo futuramente)
        const marcasResp = await fetch('api/equipamentos-cadastros.php?entidade=marca');
        const marcas = (await marcasResp.json()).data || [];
        marcaSelect.innerHTML = '<option value="">Selecione a marca</option>';
        marcas.forEach(m => {
            marcaSelect.innerHTML += `<option value="${m.id}">${m.nome}</option>`;
        });
        // Atualizar modelos
        await Equipamentos.atualizarModelos();
    }
    static async atualizarModelos() {
        const tipoId = document.getElementById('equipamento-tipo').value;
        const marcaId = document.getElementById('equipamento-marca').value;
        const modeloSelect = document.getElementById('equipamento-modelo');
        modeloSelect.innerHTML = '<option value="">Selecione o modelo</option>';
        if (!tipoId || !marcaId) return;
        const modelosResp = await fetch(`api/equipamentos-cadastros.php?entidade=modelo&tipo_id=${tipoId}&marca_id=${marcaId}`);
        const modelos = (await modelosResp.json()).data || [];
        modelos.forEach(mo => {
            modeloSelect.innerHTML += `<option value="${mo.id}">${mo.nome}</option>`;
        });
    }

    // Adicionar método para redirecionar para a tela de equipamentos filtrando pelo cliente
    static viewEquipamentos(clienteId) {
        window.location.href = 'equipamentos.php?cliente=' + clienteId;
    }
}