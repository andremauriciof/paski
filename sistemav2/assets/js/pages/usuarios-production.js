// Usuarios Module
class Usuarios {
    static currentUsuarios = [];
    static filteredUsuarios = [];
    static currentPage = 1;
    static itemsPerPage = 10;

    static render() {
        const content = document.getElementById('page-content');
        
        content.innerHTML = `
            <div class="fade-in">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-user-cog text-primary"></i> Usuários</h2>
                    <button class="btn btn-primary" onclick="Usuarios.showAddModal()">
                        <i class="fas fa-plus"></i> Novo Usuário
                    </button>
                </div>

                <!-- Search and Filter -->
                <div class="search-filter-container">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search-usuarios" 
                                       placeholder="Buscar por nome ou email...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filter-tipo">
                                <option value="">Todos os tipos</option>
                                <option value="admin">Administrador</option>
                                <option value="tecnico">Técnico</option>
                                <option value="consulta">Consulta</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-secondary me-2" id="btnLimparFiltrosUsuarios">Limpar</button>
                        </div>
                    </div>
                </div>

                <!-- Usuarios Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Usuários</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Tipo</th>
                                        <th>Data Cadastro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="usuarios-table-body">
                                    <!-- Users will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <nav aria-label="Paginação">
                            <ul class="pagination" id="usuarios-pagination">
                                <!-- Pagination will be rendered here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Usuario Modal -->
            <div class="modal fade" id="usuarioModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="usuarioModalTitle">Novo Usuário</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="usuarioForm">
                            <div class="modal-body">
                                <input type="hidden" id="usuario-id">
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="usuario-nome" required>
                                            <label for="usuario-nome">Nome completo *</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="usuario-email" required>
                                            <label for="usuario-email">Email *</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating">
                                            <select class="form-select" id="usuario-tipo" required>
                                                <option value="">Selecione o tipo</option>
                                                <option value="admin">Administrador</option>
                                                <option value="tecnico">Técnico</option>
                                                <option value="consulta">Consulta</option>
                                            </select>
                                            <label for="usuario-tipo">Tipo de Usuário *</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" id="usuario-senha" required>
                                            <label for="usuario-senha">Senha *</label>
                                        </div>
                                        <div class="form-text">
                                            <small>Deixe em branco para manter a senha atual (apenas na edição)</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <h6><strong>Permissões por tipo:</strong></h6>
                                    <ul class="mb-0">
                                        <li><strong>Administrador:</strong> Acesso total ao sistema</li>
                                        <li><strong>Técnico:</strong> Gerenciar OS, clientes e equipamentos</li>
                                        <li><strong>Consulta:</strong> Apenas visualizar informações</li>
                                    </ul>
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

        this.loadUsuarios();
        this.initEventListeners();
    }

    static initEventListeners() {
        // Search functionality
        document.getElementById('search-usuarios').addEventListener('input', (e) => {
            this.filterUsuarios();
        });

        // Filter functionality
        document.getElementById('filter-tipo').addEventListener('change', (e) => {
            this.filterUsuarios();
        });

        // Botão Limpar
        document.getElementById('btnLimparFiltrosUsuarios').addEventListener('click', () => {
            document.getElementById('search-usuarios').value = '';
            document.getElementById('filter-tipo').value = '';
            this.filterUsuarios();
        });

        // Form submission
        document.getElementById('usuarioForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveUsuario();
        });
    }

    static async loadUsuarios() {
        try {
            // Buscar dados do backend
            const response = await fetch('api/usuarios.php?action=list');
            const result = await response.json();
            if (result.success) {
                this.currentUsuarios = result.data;
                this.filteredUsuarios = [...this.currentUsuarios];
                // Atualizar localStorage para compatibilidade (opcional)
                localStorage.setItem('usuarios', JSON.stringify(this.currentUsuarios));
                this.renderTable();
                this.renderPagination();
            } else {
                showAlert('Erro', result.error || 'Erro ao carregar usuários', 'error');
            }
        } catch (error) {
            showAlert('Erro', 'Erro de conexão ao carregar usuários', 'error');
        }
    }

    static filterUsuarios() {
        const searchTerm = document.getElementById('search-usuarios').value.toLowerCase();
        const tipoFilter = document.getElementById('filter-tipo').value;

        this.filteredUsuarios = this.currentUsuarios.filter(usuario => {
            const matchesSearch = !searchTerm || 
                usuario.nome.toLowerCase().includes(searchTerm) ||
                usuario.email.toLowerCase().includes(searchTerm);

            const matchesTipo = !tipoFilter || usuario.tipo === tipoFilter;

            return matchesSearch && matchesTipo;
        });

        this.currentPage = 1;
        this.renderTable();
        this.renderPagination();
    }

    static renderTable() {
        const tbody = document.getElementById('usuarios-table-body');
        tbody.innerHTML = '';

        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const usuariosToShow = this.filteredUsuarios.slice(startIndex, endIndex);

        if (usuariosToShow.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Nenhum usuário encontrado</td></tr>';
            return;
        }

        usuariosToShow.forEach(usuario => {
            const tipoLabels = {
                admin: { text: 'Administrador', class: 'bg-danger' },
                tecnico: { text: 'Técnico', class: 'bg-primary' },
                consulta: { text: 'Consulta', class: 'bg-secondary' }
            };

            const tipoInfo = tipoLabels[usuario.tipo] || { text: usuario.tipo, class: 'bg-secondary' };

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <strong>${usuario.nome}</strong>
                </td>
                <td>
                    <i class="fas fa-envelope text-muted"></i> ${usuario.email}
                </td>
                <td>
                    <span class="badge ${tipoInfo.class}">${tipoInfo.text}</span>
                </td>
                <td>
                    ${formatDate(usuario.criado_em)}
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="Usuarios.showEditModal(${usuario.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="Usuarios.deleteUsuario(${usuario.id})" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    static renderPagination() {
        const pagination = document.getElementById('usuarios-pagination');
        const totalPages = Math.ceil(this.filteredUsuarios.length / this.itemsPerPage);

        if (totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let paginationHTML = '';

        // Previous button
        paginationHTML += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Usuarios.goToPage(${this.currentPage - 1})">Anterior</a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="Usuarios.goToPage(${i})">${i}</a>
                </li>
            `;
        }

        // Next button
        paginationHTML += `
            <li class="page-item ${this.currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Usuarios.goToPage(${this.currentPage + 1})">Próximo</a>
            </li>
        `;

        pagination.innerHTML = paginationHTML;
    }

    static goToPage(page) {
        const totalPages = Math.ceil(this.filteredUsuarios.length / this.itemsPerPage);
        if (page >= 1 && page <= totalPages) {
            this.currentPage = page;
            this.renderTable();
            this.renderPagination();
        }
    }

    static showAddModal() {
        document.getElementById('usuarioModalTitle').textContent = 'Novo Usuário';
        document.getElementById('usuarioForm').reset();
        document.getElementById('usuario-id').value = '';
        document.getElementById('usuario-senha').required = true;
        
        const modal = new bootstrap.Modal(document.getElementById('usuarioModal'));
        modal.show();
    }

    static showEditModal(usuarioId) {
        const usuario = this.currentUsuarios.find(u => u.id === usuarioId);
        if (!usuario) return;

        document.getElementById('usuarioModalTitle').textContent = 'Editar Usuário';
        document.getElementById('usuario-id').value = usuario.id;
        document.getElementById('usuario-nome').value = usuario.nome;
        document.getElementById('usuario-email').value = usuario.email;
        document.getElementById('usuario-tipo').value = usuario.tipo;
        document.getElementById('usuario-senha').value = '';
        document.getElementById('usuario-senha').required = false;

        const modal = new bootstrap.Modal(document.getElementById('usuarioModal'));
        modal.show();
    }

    static async saveUsuario() {
        const id = document.getElementById('usuario-id').value;
        const senha = document.getElementById('usuario-senha').value;
        
        // Validate required fields for new user
        if (!id && !senha) {
            showAlert('Erro', 'A senha é obrigatória para novos usuários.', 'error');
            return;
        }

        const usuario = {
            nome: document.getElementById('usuario-nome').value,
            email: document.getElementById('usuario-email').value,
            tipo: document.getElementById('usuario-tipo').value
        };

        // Only include password if provided
        if (senha) {
            usuario.senha = senha;
        }

        try {
            let response;
            const params = new URLSearchParams(usuario);
            if (id) {
                usuario.id = id;
                params.append('id', id);
                response = await fetch('api/usuarios.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: params.toString()
                });
            } else {
                response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: params.toString()
                });
            }
            let result;
            try {
                result = await response.json();
            } catch (e) {
                const text = await response.text();
                showAlert('Erro', 'Resposta inesperada do servidor: ' + text, 'error');
                return;
            }
            if (result.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('usuarioModal'));
                modal.hide();

                // Reload table
                await this.loadUsuarios();

                // Show success message
                showAlert('Sucesso!', `Usuário ${id ? 'atualizado' : 'cadastrado'} com sucesso!`);
            } else {
                showAlert('Erro', result.error || JSON.stringify(result), 'error');
            }
        } catch (error) {
            showAlert('Erro', 'Erro de conexão ao salvar usuário: ' + error.message, 'error');
        }
    }

    static deleteUsuario(usuarioId) {
        // Prevent deleting current user
        const currentUser = Auth.getCurrentUser();
        if (currentUser && currentUser.id === usuarioId) {
            showAlert('Erro', 'Você não pode excluir seu próprio usuário.', 'error');
            return;
        }

        showConfirm(
            'Confirmar exclusão',
            'Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.',
            () => {
                let usuarios = JSON.parse(localStorage.getItem('usuarios') || '[]');
                usuarios = usuarios.filter(u => u.id !== usuarioId);
                localStorage.setItem('usuarios', JSON.stringify(usuarios));
                
                this.loadUsuarios();
                showAlert('Sucesso!', 'Usuário excluído com sucesso!');
            }
        );
    }
}