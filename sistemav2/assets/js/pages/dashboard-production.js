// Dashboard Module - Production Version
class Dashboard {
    static async render() {
        const content = document.getElementById('page-content');
        
        content.innerHTML = `
            <div class="fade-in">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-tachometer-alt text-primary"></i> Dashboard</h2>
                    <div class="text-muted">
                        <i class="fas fa-calendar-alt"></i> ${new Date().toLocaleDateString('pt-BR')}
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon text-primary">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h3 id="stats-total-os">0</h3>
                            <p>Total de OS</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card" id="card-os-abertas" style="cursor:pointer;">
                            <div class="stats-icon text-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3 id="stats-os-abertas">0</h3>
                            <p>OS Abertas</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3 id="stats-os-finalizadas">0</h3>
                            <p>OS Finalizadas</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon text-info">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h3 id="stats-faturamento">R$ 0,00</h3>
                            <p>Faturamento</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Recent Orders -->
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-history"></i> Últimas Ordens de Serviço</h5>
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
                                                <th>Data</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recent-orders">
                                            <tr><td colspan="5" class="text-center">Carregando...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Chart -->
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> OS por Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="height: 300px;">
                                    <canvas id="statusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Monthly Revenue Chart -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Faturamento dos Últimos 6 Meses</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        await this.loadDashboardData();
        // Evento para abrir OS Abertas filtradas
        setTimeout(() => {
            const cardAbertas = document.getElementById('card-os-abertas');
            if (cardAbertas) {
                cardAbertas.addEventListener('click', () => {
                    // Salvar filtro no localStorage
                    localStorage.setItem('ordensFiltroStatus', 'Executando');
                    localStorage.setItem('ordensFiltroIgnorarData', '1');
                    window.location.href = 'ordens.php';
                });
            }
        }, 200);
    }
    
    static async loadDashboardData() {
        try {
            const response = await apiRequest('dashboard.php');
            const data = response.data;
            
            // Update stats
            document.getElementById('stats-total-os').textContent = data.stats.total_os;
            document.getElementById('stats-os-abertas').textContent = data.stats.os_abertas;
            document.getElementById('stats-os-finalizadas').textContent = data.stats.os_finalizadas;
            document.getElementById('stats-faturamento').textContent = formatCurrency(data.stats.faturamento);
            
            // Load recent orders
            this.loadRecentOrders(data.recent_orders);
            
            // Initialize charts
            this.initStatusChart(data.status_distribution);
            this.initRevenueChart(data.monthly_revenue);
            
        } catch (error) {
            console.error('Error loading dashboard data:', error);
            showAlert('Erro', 'Erro ao carregar dados do dashboard', 'error');
        }
    }
    
    static loadRecentOrders(orders) {
        const tbody = document.getElementById('recent-orders');
        tbody.innerHTML = '';
        
        if (orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Nenhuma ordem de serviço encontrada</td></tr>';
            return;
        }
        
        orders.forEach(ordem => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>#${ordem.id}</strong></td>
                <td>${ordem.cliente_nome || 'N/A'}</td>
                <td>${ordem.equipamento || 'N/A'}</td>
                <td><span class="status-badge status-${ordem.status.toLowerCase().replace(' ', '-')}">${ordem.status}</span></td>
                <td>${formatDate(ordem.data_entrada)}</td>
            `;
            tbody.appendChild(row);
        });
    }
    
    static initStatusChart(statusData) {
        const ctx = document.getElementById('statusChart').getContext('2d');
        
        const labels = statusData.map(item => item.status);
        const data = statusData.map(item => item.count);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#f59e0b',
                        '#3b82f6',
                        '#64748b',
                        '#10b981',
                        '#22c55e'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    static initRevenueChart(revenueData) {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Process data for chart
        const labels = revenueData.map(item => {
            const date = new Date(item.mes + '-01');
            return date.toLocaleDateString('pt-BR', { month: 'short', year: 'numeric' });
        });
        const data = revenueData.map(item => parseFloat(item.total));
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: data,
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
}