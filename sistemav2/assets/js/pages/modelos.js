// Gestão de Modelos
class Modelos {
    static currentPage = 1;
    static itemsPerPage = 10;
    static totalPages = 1;
    static filtros = { search: '', tipo: '', marca: '' };

    static async render() {
        const content = document.getElementById('page-content');
        content.innerHTML = `
            <div class="fade-in">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-cubes text-primary"></i> Modelos</h2>
                    <button class="btn btn-primary" id="btnNovoModelo"><i class="fas fa-plus"></i> Novo Modelo</button>
                </div>
                <!-- Filtros -->
                <div class="search-filter-container mb-3">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search-modelos" placeholder="Buscar por nome do modelo...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filter-tipo">
                                <option value="">Todos os tipos</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filter-marca">
                                <option value="">Todas as marcas</option>
                            </select>
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-secondary w-100" id="btnLimparFiltrosModelos">Limpar</button>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Modelos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Marca</th>
                                        <th>Nome do Modelo</th>
                                        <th style="width:120px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="modelos-tbody"></tbody>
                            </table>
                        </div>
                        <nav aria-label="Paginação">
                            <ul class="pagination" id="modelos-pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!-- Modal Novo/Editar Modelo -->
            <div class="modal fade" id="modalModelo" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form class="modal-content" id="formModelo">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalModeloTitle">Novo Modelo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="modelo-id">
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
        await this.popularFiltros();
        await this.loadModelos();
        await this.popularSelects();
        this.initEventListeners();
    }

    static async popularFiltros() {
        // Tipos
        const tipoSelect = document.getElementById('filter-tipo');
        tipoSelect.innerHTML = '<option value="">Todos os tipos</option>';
        const tiposResp = await fetch('api/equipamentos-cadastros.php?entidade=tipo');
        const tipos = (await tiposResp.json()).data || [];
        tipos.forEach(t => {
            tipoSelect.innerHTML += `<option value="${t.id}">${t.nome}</option>`;
        });
        // Marcas
        const marcaSelect = document.getElementById('filter-marca');
        marcaSelect.innerHTML = '<option value="">Todas as marcas</option>';
        const marcasResp = await fetch('api/equipamentos-cadastros.php?entidade=marca');
        const marcas = (await marcasResp.json()).data || [];
        marcas.forEach(m => {
            marcaSelect.innerHTML += `<option value="${m.id}">${m.nome}</option>`;
        });
    }

    static async loadModelos() {
        const tbody = document.getElementById('modelos-tbody');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Carregando...</td></tr>';
        try {
            const resp = await fetch('api/equipamentos-cadastros.php?entidade=modelo');
            const result = await resp.json();
            if (result.success) {
                let modelos = result.data;
                // Filtros
                if (this.filtros.search) {
                    modelos = modelos.filter(m => m.nome.toLowerCase().includes(this.filtros.search.toLowerCase()));
                }
                if (this.filtros.tipo) {
                    modelos = modelos.filter(m => m.tipo_id == this.filtros.tipo);
                }
                if (this.filtros.marca) {
                    modelos = modelos.filter(m => m.marca_id == this.filtros.marca);
                }
                this.totalPages = Math.ceil(modelos.length / this.itemsPerPage) || 1;
                const page = this.currentPage;
                modelos = modelos.slice((page-1)*this.itemsPerPage, page*this.itemsPerPage);
                if (!modelos.length) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Nenhum modelo encontrado</td></tr>';
                    this.renderPagination();
                    return;
                }
                tbody.innerHTML = '';
                for (const modelo of modelos) {
                    const tipo = await Modelos.getTipoNome(modelo.tipo_id);
                    const marca = await Modelos.getMarcaNome(modelo.marca_id);
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${tipo}</td>
                        <td>${marca}</td>
                        <td><strong>${modelo.nome}</strong></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" data-edit="${modelo.id}" title="Editar"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-danger" data-del="${modelo.id}" title="Excluir"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                }
                this.renderPagination();
            } else {
                tbody.innerHTML = `<tr><td colspan="4" class="text-danger">${result.error || 'Erro ao carregar modelos'}</td></tr>`;
                this.renderPagination();
            }
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-danger">Erro de conexão</td></tr>';
            this.renderPagination();
        }
    }

    static renderPagination() {
        const pagination = document.getElementById('modelos-pagination');
        if (this.totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }
        let paginationHTML = '';
        paginationHTML += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Modelos.goToPage(${this.currentPage - 1})">Anterior</a>
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
                    <a class="page-link" href="#" onclick="Modelos.goToPage(${i})">${i}</a>
                </li>
            `;
        }
        paginationHTML += `
            <li class="page-item ${this.currentPage === this.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="Modelos.goToPage(${this.currentPage + 1})">Próximo</a>
            </li>
        `;
        pagination.innerHTML = paginationHTML;
    }

    static goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
            this.currentPage = page;
            this.loadModelos();
        }
    }

    static async popularSelects() {
        // Tipos
        const tipoSelect = document.getElementById('modelo-tipo');
        tipoSelect.innerHTML = '<option value="">Selecione o tipo</option>';
        const tiposResp = await fetch('api/equipamentos-cadastros.php?entidade=tipo');
        const tipos = (await tiposResp.json()).data || [];
        tipos.forEach(t => {
            tipoSelect.innerHTML += `<option value="${t.id}">${t.nome}</option>`;
        });
        // Marcas
        const marcaSelect = document.getElementById('modelo-marca');
        marcaSelect.innerHTML = '<option value="">Selecione a marca</option>';
        const marcasResp = await fetch('api/equipamentos-cadastros.php?entidade=marca');
        const marcas = (await marcasResp.json()).data || [];
        marcas.forEach(m => {
            marcaSelect.innerHTML += `<option value="${m.id}">${m.nome}</option>`;
        });
    }

    static async getTipoNome(tipo_id) {
        if (!tipo_id) return '-';
        const resp = await fetch('api/equipamentos-cadastros.php?entidade=tipo');
        const result = await resp.json();
        if (result.success) {
            const tipo = result.data.find(t => t.id == tipo_id);
            return tipo ? tipo.nome : '-';
        }
        return '-';
    }
    static async getMarcaNome(marca_id) {
        if (!marca_id) return '-';
        const resp = await fetch('api/equipamentos-cadastros.php?entidade=marca');
        const result = await resp.json();
        if (result.success) {
            const marca = result.data.find(m => m.id == marca_id);
            return marca ? marca.nome : '-';
        }
        return '-';
    }

    static initEventListeners() {
        // Filtros
        document.getElementById('search-modelos').addEventListener('input', (e) => {
            this.filtros.search = e.target.value;
            this.currentPage = 1;
            this.loadModelos();
        });
        document.getElementById('filter-tipo').addEventListener('change', (e) => {
            this.filtros.tipo = e.target.value;
            this.currentPage = 1;
            this.loadModelos();
        });
        document.getElementById('filter-marca').addEventListener('change', (e) => {
            this.filtros.marca = e.target.value;
            this.currentPage = 1;
            this.loadModelos();
        });
        document.getElementById('btnLimparFiltrosModelos').addEventListener('click', () => {
            this.filtros = { search: '', tipo: '', marca: '' };
            document.getElementById('search-modelos').value = '';
            document.getElementById('filter-tipo').value = '';
            document.getElementById('filter-marca').value = '';
            this.currentPage = 1;
            this.loadModelos();
        });
        // Novo modelo
        document.getElementById('btnNovoModelo').onclick = () => {
            document.getElementById('modalModeloTitle').textContent = 'Novo Modelo';
            document.getElementById('modelo-id').value = '';
            document.getElementById('modelo-nome').value = '';
            document.getElementById('modelo-tipo').value = '';
            document.getElementById('modelo-marca').value = '';
            new bootstrap.Modal(document.getElementById('modalModelo')).show();
        };
        // Salvar modelo
        document.getElementById('formModelo').onsubmit = async (e) => {
            e.preventDefault();
            const id = document.getElementById('modelo-id').value;
            const nome = document.getElementById('modelo-nome').value.trim();
            const tipo_id = document.getElementById('modelo-tipo').value;
            const marca_id = document.getElementById('modelo-marca').value;
            if (!nome || !tipo_id || !marca_id) return alert('Preencha todos os campos!');
            try {
                const body = id ? { entidade: 'modelo', id, nome, tipo_id, marca_id, action: 'edit' } : { entidade: 'modelo', nome, tipo_id, marca_id };
                const resp = await fetch('api/equipamentos-cadastros.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const result = await resp.json();
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalModelo')).hide();
                    await Modelos.loadModelos();
                } else {
                    alert(result.error || 'Erro ao salvar modelo');
                }
            } catch (e) {
                alert('Erro de conexão ao salvar modelo');
            }
        };
        // Editar/excluir
        document.getElementById('modelos-tbody').onclick = async (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;
            const id = btn.getAttribute('data-edit') || btn.getAttribute('data-del');
            if (btn.hasAttribute('data-edit')) {
                // Editar
                const row = btn.closest('tr');
                document.getElementById('modalModeloTitle').textContent = 'Editar Modelo';
                document.getElementById('modelo-id').value = id;
                document.getElementById('modelo-nome').value = row.querySelectorAll('td')[2].textContent.trim();
                // Selecionar tipo e marca
                const tipoNome = row.querySelectorAll('td')[0].textContent.trim();
                const marcaNome = row.querySelectorAll('td')[1].textContent.trim();
                // Buscar ids
                const tiposResp = await fetch('api/equipamentos-cadastros.php?entidade=tipo');
                const tipos = (await tiposResp.json()).data || [];
                const tipoObj = tipos.find(t => t.nome === tipoNome);
                document.getElementById('modelo-tipo').value = tipoObj ? tipoObj.id : '';
                const marcasResp = await fetch('api/equipamentos-cadastros.php?entidade=marca');
                const marcas = (await marcasResp.json()).data || [];
                const marcaObj = marcas.find(m => m.nome === marcaNome);
                document.getElementById('modelo-marca').value = marcaObj ? marcaObj.id : '';
                new bootstrap.Modal(document.getElementById('modalModelo')).show();
            } else if (btn.hasAttribute('data-del')) {
                if (confirm('Deseja realmente excluir este modelo?')) {
                    try {
                        const resp = await fetch(`api/equipamentos-cadastros.php?entidade=modelo&id=${id}`, { method: 'DELETE' });
                        const result = await resp.json();
                        if (result.success) {
                            await Modelos.loadModelos();
                        } else {
                            alert(result.error || 'Erro ao excluir modelo');
                        }
                    } catch (e) {
                        alert('Erro de conexão ao excluir modelo');
                    }
                }
            }
        };
    }
} 