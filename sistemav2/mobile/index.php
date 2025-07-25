<?php
require_once '../includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Mobile</title>
  <link rel="stylesheet" href="../assets/css/mobile-only.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .dashboard-loading {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 30px 0 20px 0;
    }
    .dashboard-spinner {
      width: 38px; height: 38px;
      border: 4px solid #3385ff;
      border-top: 4px solid #fff;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
    .dashboard-alert {
      background: #ffb300;
      color: #222;
      border-radius: 10px;
      padding: 14px 16px;
      margin-bottom: 16px;
      font-size: 1.08rem;
      font-weight: 600;
      box-shadow: 0 2px 8px #ffb30022;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .dashboard-cards {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 18px;
      justify-content: center;
    }
    .dashboard-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 8px #3385ff22;
      padding: 18px 14px;
      min-width: 120px;
      flex: 1 1 120px;
      text-align: center;
      font-size: 1.08rem;
      font-weight: 600;
      color: #0055c7;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: box-shadow 0.2s, transform 0.2s;
      text-decoration: none;
    }
    .dashboard-card:active {
      box-shadow: 0 6px 24px #3385ff33;
      transform: scale(0.98);
    }
    .dashboard-card i {
      font-size: 2rem;
      margin-bottom: 6px;
      color: #3385ff;
    }
    .dashboard-graph {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 8px #3385ff22;
      padding: 18px 10px 10px 10px;
      margin-bottom: 18px;
      min-height: 220px;
      height: 220px;
      max-height: 300px;
      position: relative;
    }
    #ordensStatusChart {
      width: 100% !important;
      height: 180px !important;
      max-height: 220px !important;
      display: block;
      margin: 0 auto;
    }
    .dashboard-graph-title {
      font-size: 1.08rem;
      font-weight: 600;
      color: #0055c7;
      margin-bottom: 8px;
      text-align: center;
    }
  </style>
</head>
<body>
  <header class="mobile-header">
    <button class="hamburger" id="openMenu"><i class="fas fa-bars"></i></button>
    <div class="mobile-logo">PASKi</div>
    <span style="width:40px;"></span>
  </header>
  <nav class="mobile-menu" id="mobileMenu">
    <button class="close-menu" id="closeMenu"><i class="fas fa-times"></i></button>
    <ul>
      <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="clientes.php"><i class="fas fa-users"></i> Clientes</a></li>
      <li><a href="equipamentos.php"><i class="fas fa-laptop"></i> Equipamentos</a></li>
      <li><a href="ordens.php"><i class="fas fa-clipboard-list"></i> Ordens</a></li>
      <li><a href="tabela.php"><i class="fas fa-table"></i> Tabela</a></li>
      <li><a href="empresa.php"><i class="fas fa-building"></i> Empresa</a></li>
      <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
    </ul>
  </nav>
  <main class="mobile-main">
    <h1 class="mobile-title">Dashboard</h1>
    <div id="dashboard-loading" class="dashboard-loading" style="display:none;"><div class="dashboard-spinner"></div></div>
    <div id="dashboard-alerts"></div>
    <div class="dashboard-indicadores" style="display:flex;gap:10px;justify-content:center;margin-bottom:18px;flex-wrap:wrap;">
      <div class="dashboard-indicador-card" style="background:#fff;border-radius:12px;box-shadow:0 2px 8px #3385ff22;padding:16px 12px;min-width:110px;flex:1 1 110px;text-align:center;">
        <div style="font-size:2rem;color:#3385ff;margin-bottom:4px;"><i class="fas fa-clipboard-list"></i></div>
        <div id="indicador-total-os" style="font-size:1.5rem;font-weight:700;color:#222;">-</div>
        <div style="font-size:1.02rem;color:#3385ff;">Total de OS</div>
      </div>
      <div class="dashboard-indicador-card" style="background:#fff;border-radius:12px;box-shadow:0 2px 8px #3385ff22;padding:16px 12px;min-width:110px;flex:1 1 110px;text-align:center;">
        <div style="font-size:2rem;color:#888;margin-bottom:4px;"><i class="fas fa-clock"></i></div>
        <div id="indicador-os-abertas" style="font-size:1.5rem;font-weight:700;color:#222;">-</div>
        <div style="font-size:1.02rem;color:#3385ff;">OS Abertas</div>
      </div>
      <div class="dashboard-indicador-card" style="background:#fff;border-radius:12px;box-shadow:0 2px 8px #3385ff22;padding:16px 12px;min-width:110px;flex:1 1 110px;text-align:center;">
        <div style="font-size:2rem;color:#888;margin-bottom:4px;"><i class="fas fa-check-circle"></i></div>
        <div id="indicador-os-finalizadas" style="font-size:1.5rem;font-weight:700;color:#222;">-</div>
        <div style="font-size:1.02rem;color:#3385ff;">OS Finalizadas</div>
      </div>
    </div>
    <div class="dashboard-graph">
      <div class="dashboard-graph-title">Ordens por Status</div>
      <canvas id="ordensStatusChart"></canvas>
    </div>
    <div class="dashboard-table" style="background:#fff;border-radius:12px;box-shadow:0 2px 8px #3385ff22;padding:10px 4px 4px 4px;margin-bottom:18px;">
      <div style="font-size:1.08rem;font-weight:600;color:#0055c7;margin-bottom:8px;text-align:center;">Últimas Ordens de Serviço</div>
      <table style="width:100%;border-collapse:collapse;font-size:0.98rem;">
        <thead>
          <tr style="color:#3385ff;text-align:left;">
            <th style="padding:4px 2px;">OS #</th>
            <th style="padding:4px 2px;">Cliente</th>
            <th style="padding:4px 2px;">Equipamento</th>
            <th style="padding:4px 2px;">Status</th>
            <th style="padding:4px 2px;">Data</th>
          </tr>
        </thead>
        <tbody id="dashboard-ultimas-os"></tbody>
      </table>
    </div>
  </main>
  <script>
    // Menu lateral
    document.getElementById('openMenu').onclick = function() {
      document.getElementById('mobileMenu').classList.add('show');
    };
    document.getElementById('closeMenu').onclick = function() {
      document.getElementById('mobileMenu').classList.remove('show');
    };
    // Dashboard loading e dados
    const loading = document.getElementById('dashboard-loading');
    const alerts = document.getElementById('dashboard-alerts');
    // Carregar dados do dashboard
    async function carregarDashboard() {
      loading.style.display = 'flex';
      alerts.innerHTML = '';
      try {
        const resp = await fetch('../api/dashboard.php?action=summary');
        const data = await resp.json();
        if (data.success) {
          // Indicadores
          document.getElementById('indicador-total-os').textContent = data.total_os ?? data.stats?.total_os ?? '-';
          document.getElementById('indicador-os-abertas').textContent = data.os_abertas ?? data.stats?.os_abertas ?? '-';
          document.getElementById('indicador-os-finalizadas').textContent = data.os_finalizadas ?? data.stats?.os_finalizadas ?? '-';
          // Gráfico de ordens por status
          if (data.ordensStatus) {
            renderOrdensStatusChart(data.ordensStatus);
          }
          // Últimas ordens de serviço (opcional: pode buscar em outro endpoint)
          carregarUltimasOrdens();
        } else {
          throw new Error(data.error || 'Erro ao carregar dashboard');
        }
      } catch (err) {
        alerts.innerHTML = `<div class='dashboard-alert' style='background:#f44336;color:#fff;'><i class='fas fa-exclamation-triangle'></i> ${err.message}</div>`;
      } finally {
        loading.style.display = 'none';
      }
    }
    // Gráfico Chart.js
    let ordensStatusChart = null;
    function renderOrdensStatusChart(statusData) {
      const canvas = document.getElementById('ordensStatusChart');
      if (!statusData || Object.keys(statusData).length === 0) {
        canvas.style.display = 'none';
        return;
      }
      canvas.style.display = 'block';
      const ctx = canvas.getContext('2d');
      if (ordensStatusChart) ordensStatusChart.destroy();
      ordensStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: Object.keys(statusData),
          datasets: [{
            data: Object.values(statusData),
            backgroundColor: ['#3385ff','#43aa8b','#ffb300','#f44336','#888'],
            borderWidth: 2
          }]
        },
        options: {
          plugins: { legend: { position: 'bottom', labels: { font: { size: 14 } } } },
          cutout: '60%',
          responsive: true,
          maintainAspectRatio: false
        }
      });
    }
    // Carregar últimas ordens de serviço
    async function carregarUltimasOrdens() {
      try {
        const resp = await fetch('../api/ordens.php?action=recent');
        const data = await resp.json();
        const tbody = document.getElementById('dashboard-ultimas-os');
        tbody.innerHTML = '';
        if (data.success && data.data && data.data.length) {
          for (const os of data.data) {
            tbody.innerHTML += `
              <tr>
                <td style='padding:4px 2px;'>#${os.id}</td>
                <td style='padding:4px 2px;'>${os.cliente_nome || '-'}</td>
                <td style='padding:4px 2px;'>${os.equipamento || '-'}</td>
                <td style='padding:4px 2px;'>${os.status || '-'}</td>
                <td style='padding:4px 2px;'>${os.data_entrada || '-'}</td>
              </tr>
            `;
          }
        } else {
          tbody.innerHTML = `<tr><td colspan='5' style='text-align:center;color:#888;'>Nenhuma ordem encontrada</td></tr>`;
        }
      } catch (err) {
        document.getElementById('dashboard-ultimas-os').innerHTML = `<tr><td colspan='5' style='text-align:center;color:#f44336;'>Erro ao carregar ordens</td></tr>`;
      }
    }
    // Inicial
    carregarDashboard();
  </script>
</body>
</html> 