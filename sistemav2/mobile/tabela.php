<?php
require_once '../includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tabela - Mobile</title>
  <link rel="stylesheet" href="../assets/css/mobile-only.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .fab-btn {
      position: fixed;
      right: 22px;
      bottom: 28px;
      width: 58px;
      height: 58px;
      border-radius: 50%;
      background: linear-gradient(90deg, #0055c7 60%, #3385ff 100%);
      color: #fff;
      font-size: 2rem;
      box-shadow: 0 4px 18px #3385ff33;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 200;
      cursor: pointer;
      transition: background 0.2s, box-shadow 0.2s, opacity 0.2s;
    }
    .fab-btn:active { opacity: 0.85; }
    .tabela-loading {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 30px 0 20px 0;
    }
    .tabela-spinner {
      width: 38px; height: 38px;
      border: 4px solid #3385ff;
      border-top: 4px solid #fff;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
    .tabela-erro, .tabela-vazio {
      color: #fff;
      background: #f44336;
      border-radius: 8px;
      padding: 12px 14px;
      margin: 18px 0;
      text-align: center;
      font-size: 1.05rem;
      box-shadow: 0 2px 8px #f4433622;
      animation: shake 0.3s;
    }
    .tabela-vazio {
      background: #0055c7;
      color: #fff;
      box-shadow: 0 2px 8px #3385ff22;
      animation: none;
    }
    @keyframes shake {
      0% { transform: translateX(0); }
      25% { transform: translateX(-6px); }
      50% { transform: translateX(6px); }
      75% { transform: translateX(-4px); }
      100% { transform: translateX(0); }
    }
    .mobile-client-card {
      transition: box-shadow 0.2s, transform 0.2s;
      cursor: pointer;
    }
    .mobile-client-card:active {
      box-shadow: 0 6px 24px #3385ff33;
      transform: scale(0.98);
    }
    #tabela-modal input, #tabela-modal textarea {
      width: 100%;
      box-sizing: border-box;
      min-width: 0;
      padding: 10px 8px;
      margin-bottom: 10px;
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
    <h1 class="mobile-title">Tabela</h1>
    <form class="mobile-search" id="tabela-search-form" autocomplete="off">
      <input type="text" id="tabela-search" placeholder="Buscar por marca, modelo, fornecedor...">
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
    <div id="tabela-loading" class="tabela-loading" style="display:none;"><div class="tabela-spinner"></div></div>
    <div id="tabela-erro" class="tabela-erro" style="display:none;"></div>
    <section class="mobile-client-list" id="tabela-list"></section>
    <div id="tabela-vazio" class="tabela-vazio" style="display:none;">Nenhuma tabela encontrada.</div>
    <button class="fab-btn" id="novaTabelaBtn" title="Nova Tabela"><i class="fas fa-plus"></i></button>
  </main>
  <!-- Modal de tabela -->
  <div id="tabela-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);z-index:300;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;box-shadow:0 4px 18px #3385ff33;padding:16px 12px 12px 12px;max-width:480px;width:96vw;min-width:0;position:relative;box-sizing:border-box;overflow-x:auto;">
      <button id="fecharTabelaModal" style="position:absolute;top:8px;right:8px;background:none;border:none;font-size:1.5rem;color:#f44336;cursor:pointer;"><i class="fas fa-times"></i></button>
      <h2 id="tabela-modal-title" style="font-size:1.2rem;color:#0055c7;text-align:center;margin-bottom:12px;">Nova Tabela</h2>
      <form id="tabela-form" autocomplete="off">
        <input type="hidden" id="tabela-id">
        <input type="hidden" id="tabela-cliente-id">
        <input type="hidden" id="tabela-equipamento-id">
        <div class="form-group" style="position:relative;">
          <input type="text" id="tabela-cliente" placeholder="Cliente" style="width:100%;padding:10px 8px;margin-bottom:10px;">
          <div id="autocomplete-clientes" style="position:absolute;top:100%;left:0;width:100%;background:#fff;z-index:10;box-shadow:0 2px 8px #3385ff22;display:none;"></div>
        </div>
        <div class="form-group" style="position:relative;">
          <input type="text" id="tabela-equipamento" placeholder="Equipamento" style="width:100%;padding:10px 8px;margin-bottom:10px;">
          <div id="autocomplete-equipamentos" style="position:absolute;top:100%;left:0;width:100%;background:#fff;z-index:10;box-shadow:0 2px 8px #3385ff22;display:none;"></div>
        </div>
        <div class="form-group"><input type="text" id="tabela-marca" placeholder="Marca *" required style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
        <div class="form-group"><input type="text" id="tabela-modelo" placeholder="Modelo *" required style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
        <div class="form-group"><input type="text" id="tabela-fornecedor" placeholder="Fornecedor" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
        <div class="form-group"><input type="number" id="tabela-custo" placeholder="Custo" step="0.01" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
        <div class="form-group"><input type="number" id="tabela-mao_obra" placeholder="Mão de Obra" step="0.01" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
        <div class="form-group"><input type="number" id="tabela-total" placeholder="Total" step="0.01" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
        <div class="form-group"><textarea id="tabela-observacoes" placeholder="Observações" style="width:100%;padding:10px 8px;height:54px;"></textarea></div>
        <div style="display:flex;gap:8px;justify-content:center;">
          <button type="submit" id="salvarTabelaBtn" style="background:#0055c7;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-weight:600;">Salvar</button>
          <button type="button" id="excluirTabelaBtn" style="background:#f44336;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-weight:600;display:none;">Excluir</button>
        </div>
      </form>
      <div id="tabela-modal-erro" style="color:#f44336;text-align:center;margin-top:8px;display:none;"></div>
    </div>
  </div>
  <script>
    // Menu lateral
    document.getElementById('openMenu').onclick = function() {
      document.getElementById('mobileMenu').classList.add('show');
    };
    document.getElementById('closeMenu').onclick = function() {
      document.getElementById('mobileMenu').classList.remove('show');
    };
    // Botão nova tabela
    document.getElementById('novaTabelaBtn').onclick = function() {
      document.getElementById('tabela-modal').style.display = 'flex';
      document.getElementById('tabela-modal-title').textContent = 'Nova Tabela';
      document.getElementById('tabela-form').reset();
      document.getElementById('tabela-id').value = '';
      document.getElementById('excluirTabelaBtn').style.display = 'none';
      document.getElementById('tabela-modal-erro').style.display = 'none';
    };
    // Fechar modal
    document.getElementById('fecharTabelaModal').onclick = function() {
      document.getElementById('tabela-modal').style.display = 'none';
    };
    // Busca e listagem real
    const tabelaList = document.getElementById('tabela-list');
    const loading = document.getElementById('tabela-loading');
    const erro = document.getElementById('tabela-erro');
    const vazio = document.getElementById('tabela-vazio');
    const searchInput = document.getElementById('tabela-search');
    const searchForm = document.getElementById('tabela-search-form');
    let searchTimeout = null;
    function renderTabela(tabelas) {
      tabelaList.innerHTML = '';
      if (!tabelas.length) {
        vazio.style.display = 'block';
        return;
      }
      vazio.style.display = 'none';
      for (const t of tabelas) {
        tabelaList.innerHTML += `
        <div class="mobile-client-card">
          <div class="client-name">${t.Marca || ''} ${t.Modelo || ''}</div>
          <div class="client-info"><i class="fas fa-truck"></i> ${t.Fornecedor || '-'}</div>
          <div class="client-info"><i class="fas fa-money-bill"></i> ${t.Custo || '-'}</div>
          <div class="client-info"><i class="fas fa-calendar"></i> ${t.Data || ''}</div>
        </div>`;
      }
    }
    async function buscarTabela(q = '') {
      erro.style.display = 'none';
      loading.style.display = 'flex';
      vazio.style.display = 'none';
      tabelaList.innerHTML = '';
      try {
        const url = q ? `../api/tabela.php?action=list&search=${encodeURIComponent(q)}&limit=10` : `../api/tabela.php?action=list&limit=10`;
        const resp = await fetch(url);
        const data = await resp.json();
        if (data.success) {
          renderTabela(data.data);
        } else {
          throw new Error(data.error || 'Erro ao buscar tabela');
        }
      } catch (err) {
        erro.textContent = err.message;
        erro.style.display = 'block';
      } finally {
        loading.style.display = 'none';
      }
    }
    // Busca em tempo real
    searchInput.oninput = function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => buscarTabela(searchInput.value), 400);
    };
    searchForm.onsubmit = function(e) {
      e.preventDefault();
      buscarTabela(searchInput.value);
    };
    // Inicial
    buscarTabela();

    // Autocomplete clientes
    const clienteInput = document.getElementById('tabela-cliente');
    const clienteIdInput = document.getElementById('tabela-cliente-id');
    const autocompleteClientes = document.getElementById('autocomplete-clientes');
    let clienteTimeout = null;
    clienteInput.oninput = function() {
      clearTimeout(clienteTimeout);
      const q = clienteInput.value.trim();
      if (!q) { autocompleteClientes.style.display = 'none'; return; }
      clienteTimeout = setTimeout(async () => {
        const resp = await fetch(`../api/clientes.php?action=search&q=${encodeURIComponent(q)}`);
        const data = await resp.json();
        if (data.success && data.data.length) {
          autocompleteClientes.innerHTML = data.data.map(c => `<div class='autocomplete-item' data-id='${c.id}' data-nome='${c.nome}'>${c.nome} <small>${c.cpf_cnpj}</small></div>`).join('');
          autocompleteClientes.style.display = 'block';
          document.querySelectorAll('#autocomplete-clientes .autocomplete-item').forEach(item => {
            item.onclick = function() {
              clienteInput.value = this.getAttribute('data-nome');
              clienteIdInput.value = this.getAttribute('data-id');
              autocompleteClientes.style.display = 'none';
            };
          });
        } else {
          autocompleteClientes.style.display = 'none';
        }
      }, 300);
    };
    clienteInput.onblur = function() { setTimeout(()=>autocompleteClientes.style.display='none', 200); };
    // Autocomplete equipamentos
    const equipamentoInput = document.getElementById('tabela-equipamento');
    const equipamentoIdInput = document.getElementById('tabela-equipamento-id');
    const autocompleteEquipamentos = document.getElementById('autocomplete-equipamentos');
    let equipamentoTimeout = null;
    equipamentoInput.oninput = function() {
      clearTimeout(equipamentoTimeout);
      const q = equipamentoInput.value.trim();
      const clienteId = clienteIdInput.value;
      if (!q && !clienteId) { autocompleteEquipamentos.style.display = 'none'; return; }
      equipamentoTimeout = setTimeout(async () => {
        let url = `../api/equipamentos.php?action=list&limit=10`;
        if (q) url += `&search=${encodeURIComponent(q)}`;
        if (clienteId) url += `&cliente_id=${encodeURIComponent(clienteId)}`;
        const resp = await fetch(url);
        const data = await resp.json();
        if (data.success && data.data.length) {
          autocompleteEquipamentos.innerHTML = data.data.map(e => `<div class='autocomplete-item' data-id='${e.id}' data-nome='${e.marca} ${e.modelo}'>${e.marca} ${e.modelo} <small>${e.numero_serie||''}</small></div>`).join('');
          autocompleteEquipamentos.style.display = 'block';
          document.querySelectorAll('#autocomplete-equipamentos .autocomplete-item').forEach(item => {
            item.onclick = function() {
              equipamentoInput.value = this.getAttribute('data-nome');
              equipamentoIdInput.value = this.getAttribute('data-id');
              autocompleteEquipamentos.style.display = 'none';
            };
          });
        } else {
          autocompleteEquipamentos.style.display = 'none';
        }
      }, 300);
    };
    equipamentoInput.onblur = function() { setTimeout(()=>autocompleteEquipamentos.style.display='none', 200); };
  </script>
</body>
</html> 