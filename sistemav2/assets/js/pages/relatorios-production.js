// Relatorios Module
class Relatorios {
    static async render() {
        const content = document.getElementById('page-content');
        // Buscar dados da empresa
        let empresaHtml = '';
        try {
            const res = await fetch('api/api.php?action=empresa');
            const json = await res.json();
            if (json.success && json.data) {
                const emp = json.data;
                console.log('EMPRESA:', emp);
                empresaHtml = `<div class="empresa-info mb-4 d-flex align-items-center" style="gap:24px;">
                    ${emp.logo ? `<img src='data:image/png;base64,${emp.logo}' alt='Logo' style='max-height:60px;max-width:120px;border-radius:8px;background:#fff;padding:4px;'>` : ''}
                    <div>
                        <div style='font-size:1.3rem;font-weight:bold;'>${emp.nome || ''}</div>
                        <div style='font-size:0.95rem;'>CNPJ: ${emp.cnpj || ''}</div>
                        <div style='font-size:0.95rem;'>${emp.endereco || ''}${emp.bairro ? ', ' + emp.bairro : ''}${emp.cidade ? ' - ' + emp.cidade : ''}${emp.estado ? ' / ' + emp.estado : ''}</div>
                        <div style='font-size:0.95rem;'>${emp.telefone || ''} ${emp.email ? ' | ' + emp.email : ''}</div>
                    </div>
                </div>`;
            }
        } catch (e) {}
        content.innerHTML = `
            <div class="fade-in">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-chart-bar text-primary"></i> Relatórios</h2>
                    <button class="btn btn-outline-primary" onclick="Relatorios.printFinanceiroPDF()">
                        <i class="fas fa-print"></i> Imprimir Relatório Financeiro
                    </button>
                </div>

                <!-- Filter Section -->
                <div class="search-filter-container">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="filter-data-inicio">
                                <label for="filter-data-inicio">Data Início</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="filter-data-fim">
                                <label for="filter-data-fim">Data Fim</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating h-100">
                                <select class="form-select h-100" id="filter-relatorio-tecnico" style="min-height: 58px;">
                                    <option value="">Todos os técnicos</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                                <label for="filter-relatorio-tecnico">Técnico</label>
                            </div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-secondary me-2" id="btnLimparFiltrosRelatorio">Limpar</button>
                            <button type="button" class="btn btn-primary" id="btnFiltrarRelatorio">Filtrar</button>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon text-primary">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h3 id="relatorio-total-os">0</h3>
                            <p>Total de OS</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3 id="relatorio-os-concluidas">0</h3>
                            <p>OS Concluídas</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon text-info">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h3 id="relatorio-faturamento">R$ 0,00</h3>
                            <p>Faturamento</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon text-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3 id="relatorio-tempo-medio">0</h3>
                            <p>Tempo Médio (dias)</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- OS por Técnico -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-user-cog"></i> OS por Técnico</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="height: 300px;">
                                    <canvas id="osPorTecnicoChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- OS por Status -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Distribuição por Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="height: 300px;">
                                    <canvas id="statusDistributionChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Faturamento Mensal -->
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Faturamento Mensal</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="faturamentoMensalChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Top Clientes -->
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-crown"></i> Top Clientes</h5>
                            </div>
                            <div class="card-body">
                                <div id="top-clientes-list">
                                    <!-- Top clients will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-table"></i> Relatório Detalhado</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>OS #</th>
                                        <th>Cliente</th>
                                        <th>Equipamento</th>
                                        <th>Técnico</th>
                                        <th>Status</th>
                                        <th>Data Entrada</th>
                                        <th>Data Saída</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody id="relatorio-table-body">
                                    <!-- Report data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;

        this.loadTecnicos();
        this.setDefaultDates();
        this.loadRelatorios();
        this.initEventListeners();
    }

    static loadTecnicos() {
        const usuarios = JSON.parse(localStorage.getItem('usuarios') || '[]');
        const tecnicos = usuarios.filter(u => u.tipo === 'tecnico' || u.tipo === 'admin');

        const select = document.getElementById('filter-relatorio-tecnico');
        select.innerHTML = '<option value="">Todos os técnicos</option>';
        
        tecnicos.forEach(tecnico => {
            select.innerHTML += `<option value="${tecnico.id}">${tecnico.nome}</option>`;
        });
    }

    static setDefaultDates() {
        const hoje = new Date();
        const inicioMes = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
        
        document.getElementById('filter-data-inicio').value = inicioMes.toISOString().split('T')[0];
        document.getElementById('filter-data-fim').value = hoje.toISOString().split('T')[0];
    }

    static applyFilters() {
        this.loadRelatorios();
    }

    static async loadRelatorios() {
        try {
            // Obter filtros
            const data_inicio = document.getElementById('filter-data-inicio').value;
            const data_fim = document.getElementById('filter-data-fim').value;
            const tecnico_id = document.getElementById('filter-relatorio-tecnico').value;
            const params = new URLSearchParams({
                action: 'summary',
                data_inicio,
                data_fim,
                tecnico_id
            });
            // Buscar dados do backend
            const response = await fetch('api/relatorios.php?' + params.toString());
            const result = await response.json();
            if (result.success) {
                const data = result.data;
                // Atualizar cards
                document.getElementById('relatorio-total-os').textContent = data.summary.total_os;
                document.getElementById('relatorio-os-concluidas').textContent = data.summary.os_concluidas;
                document.getElementById('relatorio-faturamento').textContent = 'R$ ' + (data.summary.faturamento || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                document.getElementById('relatorio-tempo-medio').textContent = data.summary.tempo_medio;
                // Atualizar tabela detalhada
                const clientes = JSON.parse(localStorage.getItem('clientes') || '[]');
                const equipamentos = JSON.parse(localStorage.getItem('equipamentos') || '[]');
                const usuarios = JSON.parse(localStorage.getItem('usuarios') || '[]');
                this.renderDetailedTable(data.detailed_data, clientes, equipamentos, usuarios);
                // Atualizar gráficos e top clientes
                this.updateOsPorTecnicoChart(data.os_por_tecnico);
                this.updateStatusDistributionChart(data.status_distribution);
                this.updateTopClientes(data.top_clientes);
                this.updateFaturamentoMensalChart(data.detailed_data);
                // (Opcional) Atualizar localStorage para compatibilidade
                localStorage.setItem('relatorio_detailed_data', JSON.stringify(data.detailed_data));
            } else {
                showAlert('Erro', result.error || 'Erro ao carregar relatórios', 'error');
            }
        } catch (error) {
            console.error('Erro detalhado:', error);
            showAlert('Erro', 'Erro de conexão ao carregar relatórios', 'error');
        }
    }

    static updateSummary(ordens) {
        const totalOS = ordens.length;
        const osConcluidas = ordens.filter(o => ['Finalizada', 'Entregue'].includes(o.status)).length;
        const faturamento = ordens
            .filter(o => o.valor_final)
            .reduce((sum, o) => sum + parseFloat(o.valor_final), 0);

        // Calculate average time
        const ordensComSaida = ordens.filter(o => o.data_saida);
        const tempoMedio = ordensComSaida.length > 0 
            ? ordensComSaida.reduce((sum, o) => {
                const entrada = new Date(o.data_entrada);
                const saida = new Date(o.data_saida);
                return sum + (saida - entrada) / (1000 * 60 * 60 * 24); // days
            }, 0) / ordensComSaida.length
            : 0;

        document.getElementById('relatorio-total-os').textContent = totalOS;
        document.getElementById('relatorio-os-concluidas').textContent = osConcluidas;
        document.getElementById('relatorio-faturamento').textContent = formatCurrency(faturamento);
        document.getElementById('relatorio-tempo-medio').textContent = Math.round(tempoMedio);
    }

    static renderDetailedTable(ordens, clientes, equipamentos, usuarios) {
        const tbody = document.getElementById('relatorio-table-body');
        tbody.innerHTML = '';

        if (ordens.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Nenhuma ordem encontrada no período</td></tr>';
            return;
        }

        ordens.forEach(ordem => {
            const cliente = clientes.find(c => c.id === ordem.cliente_id);
            const equipamento = equipamentos.find(e => e.id === ordem.equipamento_id);
            const tecnico = usuarios.find(u => u.id === ordem.tecnico_id);
            const valor = ordem.valor_final || ordem.valor_orcado || 0;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>#${ordem.id}</strong></td>
                <td>${cliente ? cliente.nome : 'N/A'}</td>
                <td>${equipamento ? `${equipamento.marca} ${equipamento.modelo}` : 'N/A'}</td>
                <td>${tecnico ? tecnico.nome : 'N/A'}</td>
                <td><span class="status-badge status-${ordem.status.toLowerCase().replace(' ', '-')}">${ordem.status}</span></td>
                <td>${formatDate(ordem.data_entrada)}</td>
                <td>${ordem.data_saida ? formatDate(ordem.data_saida) : 'N/A'}</td>
                <td>${formatCurrency(valor)}</td>
            `;
            tbody.appendChild(row);
        });
    }

    static updateCharts(ordens, usuarios, clientes) {
        this.updateOsPorTecnicoChart(ordens, usuarios);
        this.updateStatusDistributionChart(ordens);
        this.updateFaturamentoMensalChart(ordens);
        this.updateTopClientes(ordens, clientes);
    }

    static updateOsPorTecnicoChart(osPorTecnico) {
        const canvas = document.getElementById('osPorTecnicoChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (!osPorTecnico || osPorTecnico.length === 0) {
            // Renderizar gráfico vazio
            if (window.osPorTecnicoChart && typeof window.osPorTecnicoChart.destroy === 'function') window.osPorTecnicoChart.destroy();
            window.osPorTecnicoChart = new Chart(ctx, {
                type: 'bar',
                data: { labels: [], datasets: [{ label: 'Número de OS', data: [] }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
            });
            return;
        }
        const labels = osPorTecnico.map(item => item.nome);
        const data = osPorTecnico.map(item => item.count);
        if (window.osPorTecnicoChart && typeof window.osPorTecnicoChart.destroy === 'function') window.osPorTecnicoChart.destroy();
        window.osPorTecnicoChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Número de OS',
                    data: data,
                    backgroundColor: '#3b82f6',
                    borderColor: '#1e3a8a',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    static updateStatusDistributionChart(statusDistribution) {
        const canvas = document.getElementById('statusDistributionChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (!statusDistribution || statusDistribution.length === 0) {
            // Renderizar gráfico vazio
            if (window.statusDistributionChart && typeof window.statusDistributionChart.destroy === 'function') window.statusDistributionChart.destroy();
            window.statusDistributionChart = new Chart(ctx, {
                type: 'doughnut',
                data: { labels: [], datasets: [{ data: [], backgroundColor: [] }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });
            return;
        }
        const labels = statusDistribution.map(item => item.status);
        const data = statusDistribution.map(item => item.count);
        const colors = [
            '#f59e0b', // Orçamento
            '#3b82f6', // Executando
            '#64748b', // Aguardando Peça
            '#10b981', // Finalizada
            '#22c55e'  // Entregue
        ];
        if (window.statusDistributionChart && typeof window.statusDistributionChart.destroy === 'function') window.statusDistributionChart.destroy();
        window.statusDistributionChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    static updateFaturamentoMensalChart(ordens) {
        // Group by month
        const faturamentoPorMes = {};
        
        ordens.forEach(ordem => {
            if (ordem.valor_final || ordem.valor_orcado) {
                const data = new Date(ordem.data_entrada);
                const mesAno = data.toLocaleDateString('pt-BR', { month: 'short', year: 'numeric' });
                const valor = parseFloat(ordem.valor_final || ordem.valor_orcado);
                faturamentoPorMes[mesAno] = (faturamentoPorMes[mesAno] || 0) + valor;
            }
        });

        const ctx = document.getElementById('faturamentoMensalChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (window.faturamentoMensalChart && typeof window.faturamentoMensalChart.destroy === 'function') {
            window.faturamentoMensalChart.destroy();
        }

        window.faturamentoMensalChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: Object.keys(faturamentoPorMes),
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: Object.values(faturamentoPorMes),
                    borderColor: '#1e3a8a',
                    backgroundColor: 'rgba(30, 58, 138, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });
    }

    static updateTopClientes(topClientes) {
        // topClientes já vem agrupado do backend: [{ nome: 'ANDRÉ', count: 1 }, ...]
        const container = document.getElementById('top-clientes-list');
        container.innerHTML = '';
        if (!topClientes || topClientes.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">Nenhum cliente encontrado</p>';
            return;
        }
        topClientes.forEach((cliente, index) => {
            const item = document.createElement('div');
            item.className = 'd-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded';
            item.innerHTML = `
                <div>
                    <span class="badge bg-primary me-2">${index + 1}º</span>
                    <strong>${cliente.nome}</strong>
                </div>
                <span class="badge bg-secondary">${cliente.count} OS</span>
            `;
            container.appendChild(item);
        });
    }

    static exportData(type) {
        let data, filename, headers;

        if (type === 'ordens') {
            const ordens = JSON.parse(localStorage.getItem('ordens_servico') || '[]');
            const clientes = JSON.parse(localStorage.getItem('clientes') || '[]');
            const equipamentos = JSON.parse(localStorage.getItem('equipamentos') || '[]');
            const usuarios = JSON.parse(localStorage.getItem('usuarios') || '[]');

            headers = ['OS #', 'Cliente', 'Equipamento', 'Técnico', 'Status', 'Data Entrada', 'Data Saída', 'Valor'];
            data = ordens.map(ordem => {
                const cliente = clientes.find(c => c.id === ordem.cliente_id);
                const equipamento = equipamentos.find(e => e.id === ordem.equipamento_id);
                const tecnico = usuarios.find(u => u.id === ordem.tecnico_id);
                const valor = ordem.valor_final || ordem.valor_orcado || 0;

                return [
                    ordem.id,
                    cliente ? cliente.nome : 'N/A',
                    equipamento ? `${equipamento.marca} ${equipamento.modelo}` : 'N/A',
                    tecnico ? tecnico.nome : 'N/A',
                    ordem.status,
                    formatDate(ordem.data_entrada),
                    ordem.data_saida ? formatDate(ordem.data_saida) : 'N/A',
                    valor.toFixed(2)
                ];
            });
            filename = 'relatorio_ordens_servico.csv';
        } else if (type === 'clientes') {
            const clientes = JSON.parse(localStorage.getItem('clientes') || '[]');
            headers = ['Nome', 'Telefone', 'Email', 'CPF/CNPJ', 'Endereço', 'Data Cadastro'];
            data = clientes.map(cliente => [
                cliente.nome,
                cliente.telefone,
                cliente.email || '',
                cliente.cpf_cnpj,
                cliente.endereco || '',
                formatDate(cliente.criado_em)
            ]);
            filename = 'relatorio_clientes.csv';
        }

        this.downloadCSV(data, headers, filename);
    }

    static downloadCSV(data, headers, filename) {
        const csvContent = [
            headers.join(','),
            ...data.map(row => row.map(field => `"${field}"`).join(','))
        ].join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showAlert('Sucesso!', 'Relatório exportado com sucesso!');
    }

    static initEventListeners() {
        document.getElementById('btnLimparFiltrosRelatorio').addEventListener('click', () => {
            document.getElementById('filter-data-inicio').value = '';
            document.getElementById('filter-data-fim').value = '';
            document.getElementById('filter-relatorio-tecnico').value = '';
            this.loadRelatorios();
        });
        document.getElementById('btnFiltrarRelatorio').addEventListener('click', () => {
            this.loadRelatorios();
        });
    }

    static async printFinanceiroPDF() {
        // Pega os filtros atuais
        const data_inicio = document.getElementById('filter-data-inicio')?.value || '';
        const data_fim = document.getElementById('filter-data-fim')?.value || '';
        const tecnico_id = document.getElementById('filter-relatorio-tecnico')?.value || '';
        // Busca dados da empresa
        let empresa = {};
        try {
            const res = await fetch('api/api.php?action=empresa');
            const json = await res.json();
            if (json.success && json.data) empresa = json.data;
        } catch (e) {}
        // Monta a URL para buscar os dados do relatório
        const params = new URLSearchParams();
        if (data_inicio) params.append('data_inicio', data_inicio);
        if (data_fim) params.append('data_fim', data_fim);
        if (tecnico_id) params.append('tecnico_id', tecnico_id);
        params.append('action', 'summary');
        fetch('api/relatorios.php?' + params.toString())
            .then(resp => resp.json())
            .then(json => {
                if (!json.success) {
                    alert('Erro ao buscar dados do relatório financeiro.');
                    return;
                }
                const data = json.data;
                let periodo = (!data_inicio && !data_fim) ? 'Todos os períodos' :
                    (data_inicio && data_fim ? `Período: ${data_inicio} a ${data_fim}` :
                    (data_inicio ? `A partir de ${data_inicio}` : `Até ${data_fim}`));
                let tecnicoNome = '';
                if (tecnico_id && data.os_por_tecnico) {
                    const t = data.os_por_tecnico.find(t => t.id == tecnico_id || t.nome == tecnico_id);
                    if (t) tecnicoNome = t.nome;
                }
                let html = `<html><head><title>Relatório Financeiro</title>
                <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; margin: 32px; background: #fff; color: #222; }
                .empresa-header { display: flex; align-items: center; gap: 24px; margin-bottom: 12px; }
                .empresa-header img { max-height: 60px; max-width: 120px; border-radius: 8px; background: #fff; padding: 4px; }
                .empresa-dados { font-size: 0.8rem; }
                .empresa-nome { font-size: 1rem; font-weight: bold; }
                h2 { text-align: center; font-size: 1.4rem; margin: 12px 0 18px 0; letter-spacing: 1px; }
                .periodo { text-align: center; margin-bottom: 10px; font-size: 1.08rem; color: #555; }
                .resumo { text-align: center; margin-bottom: 10px; font-size: 1.08rem; }
                table { border-collapse: collapse; width: 100%; background: #fff; margin-top: 18px; font-size: 12px; }
                th, td { padding: 8px 6px; border: 1px solid #b3c0d1; }
                th { background: #2563eb; color: #fff; font-weight: 600; font-size: 13px; }
                tr:nth-child(even) { background: #f3f6fa; }
                tr:nth-child(odd) { background: #fff; }
                td.valor { text-align: right; font-weight: 500; color: #2563eb; }
                td.data { text-align: center; }
                @media print { body { background: #fff; } }
                </style></head><body onload="window.print()">
                <div class="empresa-header">
                    ${empresa.logo ? `<img src='data:image/png;base64,${empresa.logo}' alt='Logo'>` : ''}
                    <div class="empresa-dados">
                        <div class="empresa-nome">${empresa.nome || ''}</div>
                        <div>CNPJ: ${empresa.cnpj || ''}</div>
                        <div>${empresa.endereco || ''}${empresa.bairro ? ', ' + empresa.bairro : ''}${empresa.cidade ? ' - ' + empresa.cidade : ''}${empresa.estado ? ' / ' + empresa.estado : ''}</div>
                        <div>${empresa.telefone || ''} ${empresa.email ? ' | ' + empresa.email : ''}</div>
                    </div>
                </div>
                <h2>Relatório Financeiro</h2>
                <div class="periodo">${periodo}${tecnicoNome ? ` &nbsp;|&nbsp; <strong>Técnico:</strong> ${tecnicoNome}` : ''}</div>
                <div class="resumo">
                    <strong>Faturamento:</strong> R$ ${Number(data.summary.faturamento).toLocaleString('pt-BR', {minimumFractionDigits:2})} &nbsp; | &nbsp;
                    <strong>Total de OS:</strong> ${data.summary.total_os} &nbsp; | &nbsp;
                    <strong>OS Concluídas:</strong> ${data.summary.os_concluidas}
                </div>
                <table><thead><tr><th>OS #</th><th>Cliente</th><th>Equipamento</th><th>Técnico</th><th>Status</th><th>Entrada</th><th>Saída</th><th>Valor</th></tr></thead><tbody>`;
                for (const row of (data.detailed_data || [])) {
                    html += `<tr>
                        <td>${row.id}</td>
                        <td>${row.cliente_nome || ''}</td>
                        <td>${row.equipamento || ''}</td>
                        <td>${row.tecnico_nome || ''}</td>
                        <td>${row.status || ''}</td>
                        <td class='data'>${row.data_entrada ? row.data_entrada.substr(0,10) : ''}</td>
                        <td class='data'>${row.data_saida ? row.data_saida.substr(0,10) : ''}</td>
                        <td class='valor'>R$ ${Number(row.valor_final || row.valor_orcado || 0).toLocaleString('pt-BR', {minimumFractionDigits:2})}</td>
                    </tr>`;
                }
                html += '</tbody></table></body></html>';
                // Abre nova janela para impressão
                const printWindow = window.open('', '_blank');
                printWindow.document.write(html);
                printWindow.document.close();
            })
            .catch(() => alert('Erro ao buscar dados do relatório financeiro.'));
    }
}