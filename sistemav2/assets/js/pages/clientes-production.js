// Clientes Module - Production Version
class Clientes {
    static currentPage = 1;
    static itemsPerPage = 10;
    static totalPages = 1;

    static async render() {
        // Alterar para renderizar dentro do #clientes-content, se existir, senão usar #page-content
        const content = document.getElementById('clientes-content') || document.getElementById('page-content');
        
        content.innerHTML = `
            <div class="fade-in">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-users text-primary"></i> Clientes</h2>
                    ${window.USER_PERMISSIONS && window.USER_PERMISSIONS.write ? `
                    <button class="btn btn-primary" id="btn-novo-cliente">
                        <i class="fas fa-plus"></i> Novo Cliente
                    </button>
                    ` : ''}
                </div>

                <!-- Search and Filter -->
                <div class="search-filter-container">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search-clientes" 
                                       placeholder="Buscar por nome, celular, email, CPF/CNPJ ou endereço...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filter-type">
                                <option value="">Todos os tipos</option>
                                <option value="cpf">Pessoa Física</option>
                                <option value="cnpj">Pessoa Jurídica</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-secondary me-2" id="btnLimparFiltrosClientes">Limpar</button>
                        </div>
                    </div>
                </div>

                <!-- Clientes Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Clientes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Celular</th>
                                        <th>Email</th>
                                        <th>CPF/CNPJ</th>
                                        <th>Data Cadastro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="clientes-table-body">
                                    <tr><td colspan="6" class="text-center">Carregando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <nav aria-label="Paginação">
                            <ul class="pagination" id="clientes-pagination">
                                <!-- Pagination will be rendered here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!-- Add/Edit Cliente Modal -->
            <div class="modal fade" id="clienteModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="clienteModalTitle">Novo Cliente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="clienteForm">
                            <div class="modal-body">
                                <input type="hidden" id="cliente-id">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="cliente-nome" required>
                                            <label for="cliente-nome">Nome / Razão Social *</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="cliente-ie">
                                            <label for="cliente-ie">Inscrição Estadual</label>                                          
                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="cliente-cpf-cnpj" required placeholder=" ">
                                                <label for="cliente-cpf-cnpj">CPF/CNPJ *</label>
                                            </div>
                                            <button type="button" class="btn btn-outline-secondary align-self-stretch" id="buscar-cpf-cnpj" style="min-width:90px;">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="cliente-telefone">
                                            <label for="cliente-telefone">Telefone</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="cliente-celular" required>
                                            <label for="cliente-celular">Celular *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="cliente-email">
                                            <label for="cliente-email">Email</label>
                                        </div>
                                    </div>
                                </div>                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="cliente-endereco">
                                            <label for="cliente-endereco">Endereço</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="cliente-numero">
                                            <label for="cliente-numero">Número</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="cliente-complemento">
                                            <label for="cliente-complemento">Complemento</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="cliente-bairro">
                                            <label for="cliente-bairro">Bairro</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="cliente-cidade">
                                            <label for="cliente-cidade">Cidade</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="cliente-estado" maxlength="2">
                                            <label for="cliente-estado">UF</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="input-group">
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="cliente-cep" maxlength="9" placeholder=" ">
                                                <label for="cliente-cep">CEP</label>
                                            </div>
                                            <button type="button" class="btn btn-outline-secondary align-self-stretch" id="buscar-cep" style="min-width:90px;">Buscar</button>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="cliente-observacoes" style="height: 60px"></textarea>
                                            <label for="cliente-observacoes">Observações</label>
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

        this.initEventListeners();
        // Máscara para o campo TELEFONE e CELULAR (dinâmica para fixo e celular)
        var SPMaskBehavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        };
        if (window.jQuery && $.fn.mask) {
            $('#cliente-telefone').mask(SPMaskBehavior, {
                onKeyPress: function(val, e, field, options) {
                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                }
            });
            $('#cliente-celular').mask(SPMaskBehavior, {
                onKeyPress: function(val, e, field, options) {
                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                }
            });
        }        
        // Busca CEP
        document.getElementById('buscar-cep').addEventListener('click', async function() {
            const cep = document.getElementById('cliente-cep').value.replace(/\D/g, '');
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
            document.getElementById('cliente-endereco').value = data.logradouro || '';
            document.getElementById('cliente-numero').value = '';
            document.getElementById('cliente-complemento').value = '';
            document.getElementById('cliente-bairro').value = data.bairro || '';
            document.getElementById('cliente-cidade').value = data.localidade || '';
            document.getElementById('cliente-estado').value = data.uf || '';
        });
        // Busca CNPJ
        document.getElementById('buscar-cpf-cnpj').addEventListener('click', async function() {
            let cnpj = document.getElementById('cliente-cpf-cnpj').value.replace(/\D/g, '');
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
            document.getElementById('cliente-nome').value = data.nome || '';
            // Endereço separado do número
            document.getElementById('cliente-endereco').value = data.logradouro || '';
            document.getElementById('cliente-numero').value = (data.numero && data.numero !== 'S/N') ? data.numero : '';
            document.getElementById('cliente-complemento').value = '';
            document.getElementById('cliente-bairro').value = data.bairro || '';
            document.getElementById('cliente-cidade').value = data.municipio || '';
            document.getElementById('cliente-estado').value = data.uf || '';
            document.getElementById('cliente-cep').value = data.cep || '';
            // IE pode vir como objeto ou string
            let ie = '';
            if (typeof data.inscricao_estadual === 'string') {
                ie = data.inscricao_estadual;
            } else if (typeof data.inscricao_estadual === 'object' && data.inscricao_estadual !== null) {
                ie = Object.values(data.inscricao_estadual).join(', ');
            } else if (typeof data.atividade_principal === 'string') {
                ie = data.atividade_principal;
            }
            document.getElementById('cliente-ie').value = ie;
            document.getElementById('cliente-telefone').value = (data.telefone || '').split('/')[0] || '';
        });
        await this.loadClientes();
    }

    static initEventListeners() {
        // Search functionality
        document.getElementById('search-clientes').addEventListener('input', () => {
            this.currentPage = 1;
            this.loadClientes();
        });

        // Filter functionality
        document.getElementById('filter-type').addEventListener('change', () => {
            this.currentPage = 1;
            this.loadClientes();
        });
        // Botão Limpar
        document.getElementById('btnLimparFiltrosClientes').addEventListener('click', () => {
            document.getElementById('search-clientes').value = '';
            document.getElementById('filter-type').value = '';
            this.currentPage = 1;
            this.loadClientes();
        });

        // Novo Cliente button
        document.getElementById('btn-novo-cliente').addEventListener('click', () => {
            this.openModal('Novo Cliente');
            document.getElementById('cliente-id').value = ''; // Clear ID for new client
            document.getElementById('cliente-nome').value = '';
            document.getElementById('cliente-telefone').value = '';
            document.getElementById('cliente-email').value = '';
            document.getElementById('cliente-cpf-cnpj').value = '';
            document.getElementById('cliente-endereco').value = '';
            document.getElementById('cliente-numero').value = '';
            document.getElementById('cliente-complemento').value = '';
            document.getElementById('cliente-bairro').value = '';
            document.getElementById('cliente-cidade').value = '';
            document.getElementById('cliente-estado').value = '';
            document.getElementById('cliente-cep').value = '';
            document.getElementById('cliente-observacoes').value = '';
            document.getElementById('cliente-ie').value = '';
            document.getElementById('clienteForm').reset(); // Reset form fields
        });

        // Handle form submission
        document.getElementById('clienteForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('cliente-id').value;
            const endereco = document.getElementById('cliente-endereco').value;
            const cliente = {
                nome: document.getElementById('cliente-nome').value.toUpperCase(),
                telefone: document.getElementById('cliente-telefone').value,
                celular: document.getElementById('cliente-celular').value,
                email: document.getElementById('cliente-email').value.toLowerCase(),
                cpf_cnpj: document.getElementById('cliente-cpf-cnpj').value,
                endereco: endereco.toUpperCase(),
                numero: document.getElementById('cliente-numero').value.toUpperCase(),
                complemento: document.getElementById('cliente-complemento').value.toUpperCase(),
                bairro: document.getElementById('cliente-bairro').value.toUpperCase(),
                cidade: document.getElementById('cliente-cidade').value.toUpperCase(),
                estado: document.getElementById('cliente-estado').value.toUpperCase(),
                cep: document.getElementById('cliente-cep').value,
                observacoes: document.getElementById('cliente-observacoes').value.toUpperCase(),
                ie: document.getElementById('cliente-ie').value.toUpperCase()
            };
            let response;
            try {
                if (id) {
                    // Edição: PUT, x-www-form-urlencoded
                    cliente.id = id;
                    const params = new URLSearchParams(cliente);
                    response = await fetch('api/clientes.php', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: params.toString()
                    });
                    response = await response.json();
                } else {
                    // Cadastro: POST, x-www-form-urlencoded
                    const params = new URLSearchParams(cliente);
                    response = await fetch('api/clientes.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: params.toString()
                    });
                    response = await response.json();
                }
                if (response.success) {
                    showAlert('Sucesso!', id ? 'Cliente atualizado com sucesso!' : 'Cliente cadastrado com sucesso!');
                    await this.loadClientes();
                    $('#clienteModal').modal('hide');
                } else {
                    showAlert('Erro', response.error || response.message || 'Erro ao salvar cliente', 'error');
                }
            } catch (error) {
                showAlert('Erro', error.message || 'Erro ao salvar cliente', 'error');
            }
        });

        // Máscara para CPF/CNPJ
        function cpfCnpjMask(value) {
            value = value.replace(/\D/g, '');
            if (value.length <= 11) {
                // CPF: 000.000.000-00
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else {
                // CNPJ: 00.000.000/0000-00
                value = value.replace(/(\d{2})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1/$2');
                value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
            }
            return value;
        }

        document.getElementById('cliente-nome').addEventListener('input', (e) => {
            e.target.value = e.target.value.toUpperCase();
        });
        document.getElementById('cliente-endereco').addEventListener('input', (e) => {
            e.target.value = e.target.value.toUpperCase();
        });
        document.getElementById('cliente-email').addEventListener('input', (e) => {
            e.target.value = e.target.value.toLowerCase();
        });
        document.getElementById('cliente-cpf-cnpj').addEventListener('input', (e) => {
            e.target.value = cpfCnpjMask(e.target.value);
        });
    }

    static async loadClientes() {
        try {
            const search = document.getElementById('search-clientes').value;
            const type = document.getElementById('filter-type').value;
            
            const params = new URLSearchParams({
                action: 'list',
                page: this.currentPage,
                limit: this.itemsPerPage,
                search: search,
                type: type
            });

            const response = await apiRequest(`clientes.php?${params}`);
            
            this.totalPages = response.totalPages;
            this.renderTable(response.data);
            this.renderPagination();
            
        } catch (error) {
            console.error('Error loading clientes:', error);
            showAlert('Erro', 'Erro ao carregar clientes', 'error');
        }
    }

    static renderTable(clientes) {
        const tbody = document.getElementById('clientes-table-body');
        if (!clientes || clientes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nenhum cliente encontrado</td></tr>';
            return;
        }
        tbody.innerHTML = '';
        clientes.forEach(cliente => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${cliente.nome}</strong></td>
                <td><i class="fas fa-mobile-alt text-muted"></i> ${cliente.celular || cliente.telefone || 'N/A'}</td>
                <td><i class="fas fa-envelope text-muted"></i> ${cliente.email || 'N/A'}</td>
                <td><code>${cliente.cpf_cnpj}</code></td>
                <td>${cliente.criado_em ? formatDate(cliente.criado_em) : ''}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        ${window.USER_PERMISSIONS && window.USER_PERMISSIONS.write ? `
                        <button class="btn btn-outline-primary" onclick="Clientes.showEditModal(${cliente.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        ` : ''}
                        <button class="btn btn-outline-success" onclick="Clientes.viewEquipamentos(${cliente.id})" title="Equipamentos">
                            <i class="fas fa-laptop"></i>
                        </button>
                        ${window.USER_PERMISSIONS && window.USER_PERMISSIONS.delete ? `
                        <button class="btn btn-outline-danger" onclick="Clientes.deleteCliente(${cliente.id})" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                        ` : ''}
                        <button class="btn btn-success" onclick="Clientes.novaOS(${cliente.id})" title="Nova OS">
                            <i class="fas fa-plus"></i> OS
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    static renderPagination() {
        const pagination = document.getElementById('clientes-pagination');

        if (this.totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let paginationHTML = '';

        // Previous button
        paginationHTML += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Clientes.goToPage(${this.currentPage - 1})">Anterior</a>
            </li>
        `;

        // Limitar a exibição de no máximo 10 seletores de página
        let startPage = Math.max(1, this.currentPage - 4);
        let endPage = Math.min(this.totalPages, startPage + 9);
        if (endPage - startPage < 9) {
            startPage = Math.max(1, endPage - 9);
        }
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="Clientes.goToPage(${i})">${i}</a>
                </li>
            `;
        }

        // Next button
        paginationHTML += `
            <li class="page-item ${this.currentPage === this.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Clientes.goToPage(${this.currentPage + 1})">Próximo</a>
            </li>
        `;

        pagination.innerHTML = paginationHTML;
    }

    static goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
            this.currentPage = page;
            this.loadClientes();
        }
    }

    static viewEquipamentos(clienteId) {
        // Redirect to equipamentos page with client filter
        window.location.href = `equipamentos.php?cliente=${clienteId}`;
    }

    static async deleteCliente(clienteId) {
        if (!window.USER_PERMISSIONS.delete) return;
        
        showConfirm(
            'Confirmar exclusão',
            'Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita.',
            async () => {
                try {
                    await apiRequest(`clientes.php?id=${clienteId}`, {
                        method: 'DELETE'
                    });
                    
                    await this.loadClientes();
                    showAlert('Sucesso!', 'Cliente excluído com sucesso!');
                } catch (error) {
                    showAlert('Erro', error.message || 'Erro ao excluir cliente', 'error');
                }
            }
        );
    }

    static async showEditModal(clienteId) {
        try {
            const response = await apiRequest(`clientes.php?action=get&id=${clienteId}`);
            
            if (response.success && response.data) {
                const cliente = response.data;
                
                document.getElementById('clienteModalTitle').textContent = 'Editar Cliente';
                document.getElementById('cliente-id').value = cliente.id;
                document.getElementById('cliente-nome').value = cliente.nome;
                document.getElementById('cliente-telefone').value = cliente.telefone;
                document.getElementById('cliente-celular').value = cliente.celular || '';
                document.getElementById('cliente-email').value = cliente.email || '';
                document.getElementById('cliente-cpf-cnpj').value = cliente.cpf_cnpj;
                document.getElementById('cliente-endereco').value = cliente.endereco || '';
                document.getElementById('cliente-numero').value = cliente.numero || '';
                document.getElementById('cliente-complemento').value = cliente.complemento || '';
                document.getElementById('cliente-bairro').value = cliente.bairro || '';
                document.getElementById('cliente-cidade').value = cliente.cidade || '';
                document.getElementById('cliente-estado').value = cliente.estado || '';
                document.getElementById('cliente-cep').value = cliente.cep || '';
                document.getElementById('cliente-observacoes').value = cliente.observacoes || '';
                document.getElementById('cliente-ie').value = cliente.ie || '';
                
                $('#clienteModal').modal('show');
            } else {
                showAlert('Erro', response.message || 'Erro ao carregar cliente', 'error');
            }
        } catch (error) {
            showAlert('Erro', error.message || 'Erro ao carregar cliente', 'error');
        }
    }

    static openModal(title) {
        document.getElementById('clienteModalTitle').textContent = title;
        $('#clienteModal').modal('show');
        // Aplicar máscara de telefone/celular ao abrir o modal
        if (window.jQuery && $.fn.mask) {
            var SPMaskBehavior = function (val) {
                return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
            };
            $('#cliente-telefone').mask(SPMaskBehavior, {
                onKeyPress: function(val, e, field, options) {
                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                }
            });
            $('#cliente-celular').mask(SPMaskBehavior, {
                onKeyPress: function(val, e, field, options) {
                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                }
            });
        }
    }

    // Adicionar método para criar nova OS a partir do cliente
    static novaOS(clienteId) {
        // Salvar clienteId em localStorage para ser usado na tela de OS
        localStorage.setItem('clienteSelecionadoNovaOS', clienteId);
        // Redirecionar para a tela de ordens
        window.location.href = 'ordens.php?cliente=' + clienteId;
    }
}