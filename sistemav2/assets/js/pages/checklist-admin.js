class ChecklistAdmin {
    static currentPage = 1;
    static itemsPerPage = 10;
    static totalPages = 1;
    static allItems = [];

    static async render() {
        const container = document.getElementById('checklist-admin-content');
        container.innerHTML = `
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Itens do Checklist</h5>
                    <button class="btn btn-primary btn-sm" id="btnAddChecklistItem"><i class="fas fa-plus"></i> Novo Item</button>
                </div>
                <div class="card-body">
                    <div id="checklist-items-table"></div>
                    <nav aria-label="Paginação">
                        <ul class="pagination" id="checklist-pagination"></ul>
                    </nav>
                </div>
            </div>
            <!-- Modal para adicionar/editar item -->
            <div class="modal fade" id="checklistItemModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="checklistItemModalTitle">Novo Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="checklistItemForm">
                            <div class="modal-body">
                                <input type="hidden" id="item-id">
                                <div class="mb-3">
                                    <label for="item-descricao" class="form-label">Descrição</label>
                                    <input type="text" class="form-control" id="item-descricao" required>
                                </div>
                                <div class="mb-3">
                                    <label for="item-categoria" class="form-label">Categoria</label>
                                    <select class="form-select" id="item-categoria" required>
                                        <option value="celular">Celular</option>
                                        <option value="computador">Computador</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary btn-sm">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        this.loadItems();
        this.setupEvents();
    }

    static async loadItems() {
        const tableDiv = document.getElementById('checklist-items-table');
        tableDiv.innerHTML = '<div class="text-center text-muted">Carregando...</div>';
        try {
            const res = await fetch('api/checklist.php?action=get_items');
            const json = await res.json();
            if (json.success) {
                this.allItems = json.data;
                this.totalPages = Math.ceil(this.allItems.length / this.itemsPerPage) || 1;
                const page = this.currentPage;
                const items = this.allItems.slice((page-1)*this.itemsPerPage, page*this.itemsPerPage);
                
                if (!items.length) {
                    tableDiv.innerHTML = '<div class="text-center text-muted">Nenhum item cadastrado.</div>';
                    this.renderPagination();
                    return;
                }
                
                let html = `
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descrição</th>
                                    <th>Categoria</th>
                                    <th style="width:120px"></th>
                                </tr>
                            </thead>
                            <tbody>`;
                for (const item of items) {
                    html += `<tr>
                        <td>${item.id}</td>
                        <td><strong>${item.nome}</strong></td>
                        <td>${item.categoria}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" data-edit="${item.id}" title="Editar"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-danger" data-del="${item.id}" title="Excluir"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>`;
                }
                html += `</tbody></table></div>`;
                tableDiv.innerHTML = html;
                this.setupTableEvents(items);
                this.renderPagination();
            } else {
                tableDiv.innerHTML = `<div class="text-danger">${json.error || 'Erro ao carregar itens.'}</div>`;
                this.renderPagination();
            }
        } catch (e) {
            tableDiv.innerHTML = '<div class="text-danger">Erro de conexão.</div>';
            this.renderPagination();
        }
    }

    static renderPagination() {
        const pagination = document.getElementById('checklist-pagination');
        if (this.totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }
        let paginationHTML = '';
        paginationHTML += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="ChecklistAdmin.goToPage(${this.currentPage - 1})">Anterior</a>
            </li>
        `;
        for (let i = 1; i <= this.totalPages; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="ChecklistAdmin.goToPage(${i})">${i}</a>
                </li>
            `;
        }
        paginationHTML += `
            <li class="page-item ${this.currentPage === this.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="ChecklistAdmin.goToPage(${this.currentPage + 1})">Próxima</a>
            </li>
        `;
        pagination.innerHTML = paginationHTML;
    }

    static goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
            this.currentPage = page;
            this.loadItems();
        }
    }

    static setupEvents() {
        document.getElementById('btnAddChecklistItem').onclick = () => {
            this.openModal();
        };
        const form = document.getElementById('checklistItemForm');
        form.onsubmit = async (e) => {
            e.preventDefault();
            const id = document.getElementById('item-id').value;
            const nome = document.getElementById('item-descricao').value.trim();
            const categoria = document.getElementById('item-categoria').value;
            if (!nome) return alert('Descrição obrigatória');
            const formData = new FormData();
            formData.append('nome', nome);
            formData.append('categoria', categoria);
            let action = 'add_item';
            if (id) {
                formData.append('id', id);
                action = 'edit_item';
            }
            try {
                const res = await fetch(`api/checklist.php?action=${action}`, { method: 'POST', body: formData });
                const json = await res.json();
                if (json.success) {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('checklistItemModal')).hide();
                    this.loadItems();
                } else {
                    alert(json.error || 'Erro ao salvar item.');
                }
            } catch (e) {
                alert('Erro de conexão.');
            }
        };
    }

    static setupTableEvents(items) {
        // Usar delegação de eventos no tbody, similar ao padrão de marcas.js
        const tbody = document.querySelector('#checklist-items-table tbody');
        if (!tbody) return;
        
        tbody.onclick = async (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;
            
            const id = btn.getAttribute('data-edit') || btn.getAttribute('data-del');
            if (!id) return;
            
            if (btn.hasAttribute('data-edit')) {                // Editar
                const item = items.find(i => i.id == id);
                if (item) {
                    this.openModal(item);
                }
            } else if (btn.hasAttribute('data-del')) {                // Excluir
                if (!confirm('Deseja realmente excluir este item?')) return;
                const formData = new FormData();
                formData.append('id', id);
                try {
                    const res = await fetch('api/checklist.php?action=delete_item', { method: 'POST', body: formData });
                    const json = await res.json();
                    if (json.success) {
                        this.loadItems();
                    } else {
                        alert(json.error || 'Erro ao excluir item.');
                    }
                } catch (e) {
                    alert('Erro de conexão.');
                }
            }
        };
    }

    static openModal(item = null) {
        document.getElementById('item-id').value = item ? item.id : '';
        document.getElementById('item-descricao').value = item ? item.nome : '';
        document.getElementById('item-categoria').value = item ? item.categoria : 'celular';
        document.getElementById('checklistItemModalTitle').innerText = item ? 'Editar Item' : 'Novo Item';
        const modal = new bootstrap.Modal(document.getElementById('checklistItemModal'));
        modal.show();
    }
} 