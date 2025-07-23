// Gestão de Tipos de Equipamento
class Tipos {
    static currentPage = 1;
    static itemsPerPage = 10;
    static totalPages = 1;

    static async render() {
        const content = document.getElementById('page-content');
        content.innerHTML = `
            <div class="fade-in">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-tags text-primary"></i> Tipos de Equipamento</h2>
                    <button class="btn btn-primary" id="btnNovoTipo"><i class="fas fa-plus"></i> Novo Tipo</button>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Tipos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th style="width:120px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tipos-tbody"></tbody>
                            </table>
                        </div>
                        <nav aria-label="Paginação">
                            <ul class="pagination" id="tipos-pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!-- Modal Novo/Editar Tipo -->
            <div class="modal fade" id="modalTipo" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form class="modal-content" id="formTipo">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTipoTitle">Novo Tipo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="tipo-id">
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
        `;
        await this.loadTipos();
        this.initEventListeners();
    }

    static async loadTipos() {
        const tbody = document.getElementById('tipos-tbody');
        tbody.innerHTML = '<tr><td colspan="2" class="text-center text-muted">Carregando...</td></tr>';
        try {
            const resp = await fetch('api/equipamentos-cadastros.php?entidade=tipo');
            const result = await resp.json();
            if (result.success) {
                let tipos = result.data;
                this.totalPages = Math.ceil(tipos.length / this.itemsPerPage) || 1;
                const page = this.currentPage;
                tipos = tipos.slice((page-1)*this.itemsPerPage, page*this.itemsPerPage);
                if (!tipos.length) {
                    tbody.innerHTML = '<tr><td colspan="2" class="text-center text-muted">Nenhum tipo cadastrado</td></tr>';
                    this.renderPagination();
                    return;
                }
                tbody.innerHTML = '';
                tipos.forEach(tipo => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${tipo.nome}</strong></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" data-edit="${tipo.id}" title="Editar"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-danger" data-del="${tipo.id}" title="Excluir"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                this.renderPagination();
            } else {
                tbody.innerHTML = `<tr><td colspan="2" class="text-danger">${result.error || 'Erro ao carregar tipos'}</td></tr>`;
                this.renderPagination();
            }
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="2" class="text-danger">Erro de conexão</td></tr>';
            this.renderPagination();
        }
    }

    static renderPagination() {
        const pagination = document.getElementById('tipos-pagination');
        if (this.totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }
        let paginationHTML = '';
        paginationHTML += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Tipos.goToPage(${this.currentPage - 1})">Anterior</a>
            </li>
        `;
        for (let i = 1; i <= this.totalPages; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="Tipos.goToPage(${i})">${i}</a>
                </li>
            `;
        }
        paginationHTML += `
            <li class="page-item ${this.currentPage === this.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Tipos.goToPage(${this.currentPage + 1})">Próximo</a>
            </li>
        `;
        pagination.innerHTML = paginationHTML;
    }

    static goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
            this.currentPage = page;
            this.loadTipos();
        }
    }

    static initEventListeners() {
        // Novo tipo
        document.getElementById('btnNovoTipo').onclick = () => {
            document.getElementById('modalTipoTitle').textContent = 'Novo Tipo';
            document.getElementById('tipo-id').value = '';
            document.getElementById('tipo-nome').value = '';
            new bootstrap.Modal(document.getElementById('modalTipo')).show();
        };
        // Salvar tipo
        document.getElementById('formTipo').onsubmit = async (e) => {
            e.preventDefault();
            const id = document.getElementById('tipo-id').value;
            const nome = document.getElementById('tipo-nome').value.trim();
            if (!nome) return alert('Informe o nome do tipo!');
            try {
                const body = id ? { entidade: 'tipo', id, nome, action: 'edit' } : { entidade: 'tipo', nome };
                const resp = await fetch('api/equipamentos-cadastros.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const result = await resp.json();
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalTipo')).hide();
                    await Tipos.loadTipos();
                } else {
                    alert(result.error || 'Erro ao salvar tipo');
                }
            } catch (e) {
                alert('Erro de conexão ao salvar tipo');
            }
        };
        // Editar/excluir
        document.getElementById('tipos-tbody').onclick = async (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;
            const id = btn.getAttribute('data-edit') || btn.getAttribute('data-del');
            if (btn.hasAttribute('data-edit')) {
                // Editar
                const row = btn.closest('tr');
                document.getElementById('modalTipoTitle').textContent = 'Editar Tipo';
                document.getElementById('tipo-id').value = id;
                document.getElementById('tipo-nome').value = row.querySelector('td').textContent.trim();
                new bootstrap.Modal(document.getElementById('modalTipo')).show();
            } else if (btn.hasAttribute('data-del')) {
                if (confirm('Deseja realmente excluir este tipo?')) {
                    try {
                        const resp = await fetch(`api/equipamentos-cadastros.php?entidade=tipo&id=${id}`, { method: 'DELETE' });
                        const result = await resp.json();
                        if (result.success) {
                            await Tipos.loadTipos();
                        } else {
                            alert(result.error || 'Erro ao excluir tipo');
                        }
                    } catch (e) {
                        alert('Erro de conexão ao excluir tipo');
                    }
                }
            }
        };
    }
} 