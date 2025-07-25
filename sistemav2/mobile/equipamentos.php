<?php
require_once '../includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Equipamentos - Mobile</title>
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
    .equipamentos-loading {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 30px 0 20px 0;
    }
    .equipamentos-spinner {
      width: 38px; height: 38px;
      border: 4px solid #3385ff;
      border-top: 4px solid #fff;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
    .equipamentos-erro, .equipamentos-vazio {
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
    .equipamentos-vazio {
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
    #equipamento-modal input, #equipamento-modal textarea {
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
    <h1 class="mobile-title">Equipamentos</h1>
    <form class="mobile-search" id="equipamentos-search-form" autocomplete="off">
      <input type="text" id="equipamentos-search" placeholder="Buscar por tipo, marca, modelo, cliente...">
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
    <div id="equipamentos-loading" class="equipamentos-loading" style="display:none;"><div class="equipamentos-spinner"></div></div>
    <div id="equipamentos-erro" class="equipamentos-erro" style="display:none;"></div>
    <section class="mobile-client-list" id="equipamentos-list"></section>
    <div id="equipamentos-vazio" class="equipamentos-vazio" style="display:none;">Nenhum equipamento encontrado.</div>
    <button class="fab-btn" id="novoEquipamentoBtn" title="Novo Equipamento"><i class="fas fa-plus"></i></button>
  </main>
  <!-- Modal de equipamento -->
  <div id="equipamento-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);z-index:300;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;box-shadow:0 4px 18px #3385ff33;padding:16px 12px 12px 12px;max-width:480px;width:96vw;min-width:0;position:relative;box-sizing:border-box;overflow-x:auto;">
      <button id="fecharEquipamentoModal" style="position:absolute;top:8px;right:8px;background:none;border:none;font-size:1.5rem;color:#f44336;cursor:pointer;"><i class="fas fa-times"></i></button>
      <h2 id="equipamento-modal-title" style="font-size:1.2rem;color:#0055c7;text-align:center;margin-bottom:12px;">Novo Equipamento</h2>
      <form id="equipamento-form" autocomplete="off">
        <input type="hidden" id="equipamento-id">
        <input type="hidden" id="equipamento-cliente-id">
        <div class="form-group" style="position:relative;">
          <input type="text" id="equipamento-cliente" placeholder="Cliente *" required style="width:100%;padding:10px 8px;margin-bottom:10px;">
          <div id="autocomplete-clientes" style="position:absolute;top:100%;left:0;width:100%;background:#fff;z-index:10;box-shadow:0 2px 8px #3385ff22;display:none;"></div>
        </div>
        <div class="form-group"><input type="text" id="equipamento-tipo" placeholder="Tipo *" required style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
        <div class="form-group"><input type="text" id="equipamento-marca" placeholder="Marca *" required style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
        <div class="form-group"><input type="text" id="equipamento-modelo" placeholder="Modelo *" required style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
        <div class="form-group"><input type="text" id="equipamento-numero_serie" placeholder="Nº de Série" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
        <div class="form-group"><textarea id="equipamento-observacoes" placeholder="Observações" style="width:100%;padding:10px 8px;height:54px;"></textarea></div>
        <div style="display:flex;gap:8px;justify-content:center;">
          <button type="submit" id="salvarEquipamentoBtn" style="background:#0055c7;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-weight:600;">Salvar</button>
          <button type="button" id="excluirEquipamentoBtn" style="background:#f44336;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-weight:600;display:none;">Excluir</button>
        </div>
      </form>
      <div id="equipamento-modal-erro" style="color:#f44336;text-align:center;margin-top:8px;display:none;"></div>
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
    // Botão novo equipamento
    document.getElementById('novoEquipamentoBtn').onclick = function() {
      document.getElementById('equipamento-modal').style.display = 'flex';
      document.getElementById('equipamento-modal-title').textContent = 'Novo Equipamento';
      document.getElementById('equipamento-form')[0].reset();
      document.getElementById('equipamento-id').value = '';
      document.getElementById('salvarEquipamentoBtn').textContent = 'Salvar';
      document.getElementById('excluirEquipamentoBtn').style.display = 'none';
      document.getElementById('equipamento-modal-erro').style.display = 'none';
    };
    // Fechar modal
    document.getElementById('fecharEquipamentoModal').onclick = function() {
      document.getElementById('equipamento-modal').style.display = 'none';
    };
    // Busca e listagem real
    const equipamentosList = document.getElementById('equipamentos-list');
    const loading = document.getElementById('equipamentos-loading');
    const erro = document.getElementById('equipamentos-erro');
    const vazio = document.getElementById('equipamentos-vazio');
    const searchInput = document.getElementById('equipamentos-search');
    const searchForm = document.getElementById('equipamentos-search-form');
    let searchTimeout = null;
    function renderEquipamentos(equipamentos) {
      equipamentosList.innerHTML = '';
      if (!equipamentos.length) {
        vazio.style.display = 'block';
        return;
      }
      vazio.style.display = 'none';
      for (const e of equipamentos) {
        equipamentosList.innerHTML += `
        <div class="mobile-client-card">
          <div class="client-name">${e.tipo || ''} ${e.marca || ''} ${e.modelo || ''}</div>
          <div class="client-info"><i class="fas fa-user"></i> ${e.cliente_nome || '-'}</div>
          <div class="client-info"><i class="fas fa-barcode"></i> ${e.numero_serie || '-'}</div>
          <div class="client-info"><i class="fas fa-calendar"></i> ${e.data_cadastro || ''}</div>
        </div>`;
      }
    }
    async function buscarEquipamentos(q = '') {
      erro.style.display = 'none';
      loading.style.display = 'flex';
      vazio.style.display = 'none';
      equipamentosList.innerHTML = '';
      try {
        const resp = await fetch(`../api/equipamentos.php?action=list&search=${encodeURIComponent(q)}`);
        const data = await resp.json();
        if (data.success) {
          renderEquipamentos(data.data);
        } else {
          throw new Error(data.error || 'Erro ao buscar equipamentos');
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
      searchTimeout = setTimeout(() => buscarEquipamentos(searchInput.value), 400);
    };
    searchForm.onsubmit = function(e) {
      e.preventDefault();
      buscarEquipamentos(searchInput.value);
    };
    // Inicial
    buscarEquipamentos();

    // Autocomplete clientes
    const clienteInput = document.getElementById('equipamento-cliente');
    const clienteIdInput = document.getElementById('equipamento-cliente-id');
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
  </script>
</body>
</html> 