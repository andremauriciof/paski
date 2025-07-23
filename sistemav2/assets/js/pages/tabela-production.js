// Tabela de Telas - Production
class TabelaTelas {
    static currentPage = 1;
    static itemsPerPage = 10;
    static totalPages = 1;
    static allData = []; // Armazenar todos os dados

    static async carregarDados() {
        try {
            // Montar filtros
            const marca = document.getElementById('filtroMarca').value;
            const modelo = document.getElementById('filtroModelo').value;
            const search = document.getElementById('filtroBuscaGeral') ? document.getElementById('filtroBuscaGeral').value : '';
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (marca) params.append('marca', marca);
            if (modelo) params.append('modelo', modelo);
            params.append('action', 'list');
            // Buscar dados da API via GET
            const response = await fetch('api/tabela.php?' + params.toString(), {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (result && result.success && Array.isArray(result.data)) {
                this.allData = result.data; // Armazenar todos os dados
                this.totalPages = Math.ceil(this.allData.length / this.itemsPerPage) || 1;
                this.preencherTabela();
            } else {
                this.allData = [];
                this.totalPages = 1;
                this.preencherTabela();
            }
        } catch (e) {
            this.allData = [];
            this.totalPages = 1;
            this.preencherTabela();
        }
    }

    static preencherTabela() {
        const tbody = document.getElementById('tabelaDados');
        const msg = document.getElementById('mensagemSemDados');
        tbody.innerHTML = '';
        
        if (!this.allData || this.allData.length === 0) {
            msg.classList.remove('d-none');
            this.renderPagination();
            return;
        }
        
        msg.classList.add('d-none');
        
        // Aplicar paginação aos dados
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const dadosPaginados = this.allData.slice(startIndex, endIndex);
        
        dadosPaginados.forEach(item => {
            const id = item.ID || item.id;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${id}</td>
                <td>${item.Data || item.data || ''}</td>
                <td>${item.Fornecedor || item.fornecedor || ''}</td>
                <td>${item.Marca || item.marca || ''}</td>
                <td>${item.Modelo || item.modelo || ''}</td>
                <td>R$ ${item.Custo !== undefined ? Number(item.Custo).toFixed(2) : (item.custo !== undefined ? Number(item.custo).toFixed(2) : '0,00')}</td>
                <td>R$ ${item.MaoDeObra !== undefined ? Number(item.MaoDeObra).toFixed(2) : (item.maodeobra !== undefined ? Number(item.maodeobra).toFixed(2) : '0,00')}</td>
                <td>R$ ${item.ValorTotal !== undefined ? Number(item.ValorTotal).toFixed(2) : (item.valortotal !== undefined ? Number(item.valortotal).toFixed(2) : '0,00')}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" title="Editar" onclick="TabelaTelas.abrirModalEdicao(${id})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger" title="Excluir" onclick="TabelaTelas.excluirTela(${id})"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
        
        this.renderPagination();
    }

    static async abrirModalEdicao(id) {
        // Buscar dados da tela pelo ID e preencher o modal
        try {
            const response = await fetch(`api/tabela.php?action=get&id=${id}`);
            const result = await response.json();
            if (result && result.success && result.data) {
                const tela = result.data;
                document.getElementById('idTela').value = tela.ID || tela.id;
                document.getElementById('data').value = tela.Data || tela.data || '';
                document.getElementById('fornecedor').value = tela.Fornecedor || tela.fornecedor || '';
                document.getElementById('marca').value = tela.Marca || tela.marca || '';
                document.getElementById('modelo').value = tela.Modelo || tela.modelo || '';
                document.getElementById('custo').value = tela.Custo || tela.custo || '';
                document.getElementById('maodeobra').value = tela.MaoDeObra || tela.maodeobra || '';
                document.getElementById('valortotal').value = tela.ValorTotal || tela.valortotal || '';
                document.getElementById('modalTituloTela').innerText = 'Editar Tela';
                const modal = new bootstrap.Modal(document.getElementById('modalTela'));
                modal.show();
            } else {
                alert('Erro ao buscar dados da tela.');
            }
        } catch (e) {
            alert('Erro ao buscar dados da tela.');
        }
    }

    static async excluirTela(id) {
        if (!confirm('Tem certeza que deseja excluir esta tela?')) return;
        try {
            const response = await fetch(`api/tabela.php?id=${id}`, { method: 'DELETE' });
            const result = await response.json();
            if (result && result.success) {
                this.carregarDados();
            } else {
                alert(result.error || 'Erro ao excluir tela.');
            }
        } catch (e) {
            alert('Erro ao excluir tela.');
        }
    }

    static renderPagination() {
        const pagination = document.getElementById('tabela-pagination');
        if (!pagination) {
            return;
        }
        if (this.totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }
        let paginationHTML = '';
        paginationHTML += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="TabelaTelas.goToPage(${this.currentPage - 1})">Anterior</a>
            </li>
        `;
        let startPage = Math.max(1, this.currentPage - 4);
        let endPage = Math.min(this.totalPages, startPage + 9);
        if (endPage - startPage < 9) {
            startPage = Math.max(1, endPage - 9);
        }
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="TabelaTelas.goToPage(${i})">${i}</a>
                </li>
            `;
        }
        paginationHTML += `
            <li class="page-item ${this.currentPage === this.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="TabelaTelas.goToPage(${this.currentPage + 1})">Próximo</a>
            </li>
        `;
        pagination.innerHTML = paginationHTML;
    }

    static goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
            this.currentPage = page;
            this.preencherTabela();
        }
    }

    static async carregarMarcas() {
        try {
            const response = await fetch('api/tabela.php?action=marcas');
            const result = await response.json();
            const select = document.getElementById('filtroMarca');
            select.innerHTML = '<option value="">Todas as marcas</option>';
            if (result && result.success && Array.isArray(result.data)) {
                result.data.forEach(marca => {
                    select.innerHTML += `<option value="${marca}">${marca}</option>`;
                });
            }
        } catch {}
    }

    static async carregarModelos() {
        try {
            const marca = document.getElementById('filtroMarca').value;
            const response = await fetch('api/tabela.php?action=modelos&marca=' + encodeURIComponent(marca));
            const result = await response.json();
            const select = document.getElementById('filtroModelo');
            select.innerHTML = '<option value="">Todos os modelos</option>';
            if (result && result.success && Array.isArray(result.data)) {
                result.data.forEach(modelo => {
                    select.innerHTML += `<option value="${modelo}">${modelo}</option>`;
                });
            }
        } catch {}
    }

    static init() {
        this.carregarMarcas();
        this.carregarModelos();
        this.carregarDados();
        document.getElementById('btnFiltrar').onclick = () => this.carregarDados();
        document.getElementById('btnLimparFiltros').onclick = () => {
            if (document.getElementById('filtroBuscaGeral')) document.getElementById('filtroBuscaGeral').value = '';
            document.getElementById('filtroMarca').value = '';
            document.getElementById('filtroModelo').value = '';
            this.carregarDados();
        };
        document.getElementById('filtroMarca').onchange = () => this.carregarModelos();
        if (document.getElementById('filtroBuscaGeral')) {
            document.getElementById('filtroBuscaGeral').addEventListener('input', () => {
                this.carregarDados();
            });
        }
        // Evento para novo registro
        const btnNova = document.getElementById('btnNovaEntrada');
        if (btnNova) {
            btnNova.onclick = () => {
                document.getElementById('idTela').value = '';
                document.getElementById('data').value = '';
                document.getElementById('fornecedor').value = '';
                document.getElementById('marca').value = '';
                document.getElementById('modelo').value = '';
                document.getElementById('custo').value = '';
                document.getElementById('maodeobra').value = '';
                document.getElementById('valortotal').value = '';
                document.getElementById('modalTituloTela').innerText = 'Nova Entrada';
                const modal = new bootstrap.Modal(document.getElementById('modalTela'));
                modal.show();
            };
        }
    }
}

// Adicionar variáveis de controle de página
TabelaTelas.currentPage = 1;
TabelaTelas.itemsPerPage = 10;
TabelaTelas.totalPages = 1;

// Variáveis globais para os fatores da empresa
let empresaFatores = {
    fator_custo: 1.0,
    fator_mao_obra: 1.0,
    valor_adicional: 0.0
};

// Função para buscar os fatores da empresa
async function carregarFatoresEmpresa() {
    try {
        const resp = await fetch('api/empresa.php');
        const data = await resp.json();
        if (data.success && data.data) {
            empresaFatores.fator_custo = parseFloat(data.data.fator_custo) || 1.0;
            empresaFatores.fator_mao_obra = parseFloat(data.data.fator_mao_obra) || 1.0;
            empresaFatores.valor_adicional = parseFloat(data.data.valor_adicional) || 0.0;
        }
    } catch (e) {
        // Se falhar, mantém valores padrão
    }
}

// Função para calcular o valor total
function calcularValorTotal() {
    const custo = parseFloat(document.getElementById('custo').value) || 0;
    const maodeobra = parseFloat(document.getElementById('maodeobra').value) || 0;
    const total = ((custo * empresaFatores.fator_custo) + maodeobra) * empresaFatores.fator_mao_obra + empresaFatores.valor_adicional;
    document.getElementById('valortotal').value = total.toFixed(2);
}

// Adicionar eventos após DOM pronto
const oldDOMContentLoaded = document.addEventListener;
document.addEventListener = function(type, listener, options) {
    if (type === 'DOMContentLoaded') {
        oldDOMContentLoaded.call(document, type, async function(event) {
            await carregarFatoresEmpresa();
            // Adiciona eventos de cálculo automático
            const custoInput = document.getElementById('custo');
            const maoInput = document.getElementById('maodeobra');
            if (custoInput && maoInput) {
                custoInput.addEventListener('input', calcularValorTotal);
                maoInput.addEventListener('input', calcularValorTotal);
            }
            listener(event);
        }, options);
    } else {
        oldDOMContentLoaded.call(document, type, listener, options);
    }
};

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('tabelaDados')) {
        TabelaTelas.init();
    }
}); 