// Gestão de Marcas
class Marcas {
    static currentPage = 1;
    static itemsPerPage = 10;
    static totalPages = 1;

    static async render() {
        const content = document.getElementById('page-content');
        content.innerHTML = `
            <div class="fade-in">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-industry text-primary"></i> Marcas</h2>
                    <button class="btn btn-primary" id="btnNovaMarca"><i class="fas fa-plus"></i> Nova Marca</button>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Marcas</h5>
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
                                <tbody id="marcas-tbody"></tbody>
                            </table>
                        </div>
                        <nav aria-label="Paginação">
                            <ul class="pagination" id="marcas-pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!-- Modal Nova/Editar Marca -->
            <div class="modal fade" id="modalMarca" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form class="modal-content" id="formMarca">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalMarcaTitle">Nova Marca</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="marca-id">
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
        `;
        await this.loadMarcas();
        this.initEventListeners();
    }

    static async loadMarcas() {
        const tbody = document.getElementById('marcas-tbody');
        tbody.innerHTML = '<tr><td colspan="2" class="text-center text-muted">Carregando...</td></tr>';
        try {
            const resp = await fetch('api/equipamentos-cadastros.php?entidade=marca');
            const result = await resp.json();
            if (result.success) {
                let marcas = result.data;
                this.totalPages = Math.ceil(marcas.length / this.itemsPerPage) || 1;
                const page = this.currentPage;
                marcas = marcas.slice((page-1)*this.itemsPerPage, page*this.itemsPerPage);
                if (!marcas.length) {
                    tbody.innerHTML = '<tr><td colspan="2" class="text-center text-muted">Nenhuma marca cadastrada</td></tr>';
                    this.renderPagination();
                    return;
                }
                tbody.innerHTML = '';
                marcas.forEach(marca => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${marca.nome}</strong></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" data-edit="${marca.id}" title="Editar"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-danger" data-del="${marca.id}" title="Excluir"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                this.renderPagination();
            } else {
                tbody.innerHTML = `<tr><td colspan="2" class="text-danger">${result.error || 'Erro ao carregar marcas'}</td></tr>`;
                this.renderPagination();
            }
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="2" class="text-danger">Erro de conexão</td></tr>';
            this.renderPagination();
        }
    }

    static renderPagination() {
        const pagination = document.getElementById('marcas-pagination');
        if (this.totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }
        let paginationHTML = '';
        paginationHTML += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Marcas.goToPage(${this.currentPage - 1})">Anterior</a>
            </li>
        `;
        for (let i = 1; i <= this.totalPages; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="Marcas.goToPage(${i})">${i}</a>
                </li>
            `;
        }
        paginationHTML += `
            <li class="page-item ${this.currentPage === this.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Marcas.goToPage(${this.currentPage + 1})">Próximo</a>
            </li>
        `;
        pagination.innerHTML = paginationHTML;
    }

    static goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
            this.currentPage = page;
            this.loadMarcas();
        }
    }

    static initEventListeners() {
        // Nova marca
        document.getElementById('btnNovaMarca').onclick = () => {
            document.getElementById('modalMarcaTitle').textContent = 'Nova Marca';
            document.getElementById('marca-id').value = '';
            document.getElementById('marca-nome').value = '';
            new bootstrap.Modal(document.getElementById('modalMarca')).show();
        };
        // Salvar marca
        document.getElementById('formMarca').onsubmit = async (e) => {
            e.preventDefault();
            const id = document.getElementById('marca-id').value;
            const nome = document.getElementById('marca-nome').value.trim();
            if (!nome) return alert('Informe o nome da marca!');
            try {
                const body = id ? { entidade: 'marca', id, nome, action: 'edit' } : { entidade: 'marca', nome };
                const resp = await fetch('api/equipamentos-cadastros.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const result = await resp.json();
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalMarca')).hide();
                    await Marcas.loadMarcas();
                } else {
                    alert(result.error || 'Erro ao salvar marca');
                }
            } catch (e) {
                alert('Erro de conexão ao salvar marca');
            }
        };
        // Editar/excluir
        document.getElementById('marcas-tbody').onclick = async (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;
            const id = btn.getAttribute('data-edit') || btn.getAttribute('data-del');
            if (btn.hasAttribute('data-edit')) {
                // Editar
                const row = btn.closest('tr');
                document.getElementById('modalMarcaTitle').textContent = 'Editar Marca';
                document.getElementById('marca-id').value = id;
                document.getElementById('marca-nome').value = row.querySelector('td').textContent.trim();
                new bootstrap.Modal(document.getElementById('modalMarca')).show();
            } else if (btn.hasAttribute('data-del')) {
                if (confirm('Deseja realmente excluir esta marca?')) {
                    try {
                        const resp = await fetch(`api/equipamentos-cadastros.php?entidade=marca&id=${id}`, { method: 'DELETE' });
                        const result = await resp.json();
                        if (result.success) {
                            await Marcas.loadMarcas();
                        } else {
                            alert(result.error || 'Erro ao excluir marca');
                        }
                    } catch (e) {
                        alert('Erro de conexão ao excluir marca');
                    }
                }
            }
        };
    }
} 