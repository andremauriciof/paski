<?php
require_once '../includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ordens - Mobile</title>
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
    .ordens-loading {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 30px 0 20px 0;
    }
    .ordens-spinner {
      width: 38px; height: 38px;
      border: 4px solid #3385ff;
      border-top: 4px solid #fff;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
    .ordens-erro, .ordens-vazio {
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
    .ordens-vazio {
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
    #ordem-modal input, #ordem-modal textarea {
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
    <h1 class="mobile-title">Ordens</h1>
    <form class="mobile-search" id="ordens-search-form" autocomplete="off">
      <input type="text" id="ordens-search" placeholder="Buscar por número, cliente, equipamento...">
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
    <div id="ordens-loading" class="ordens-loading" style="display:none;"><div class="ordens-spinner"></div></div>
    <div id="ordens-erro" class="ordens-erro" style="display:none;"></div>
    <section class="mobile-client-list" id="ordens-list"></section>
    <div id="ordens-vazio" class="ordens-vazio" style="display:none;">Nenhuma ordem encontrada.</div>
    <button class="fab-btn" id="novaOrdemBtn" title="Nova Ordem"><i class="fas fa-plus"></i></button>
  </main>
  <!-- Modal de ordem -->
  <div id="ordem-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);z-index:300;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;box-shadow:0 4px 18px #3385ff33;padding:16px 12px 12px 12px;max-width:480px;width:96vw;min-width:0;position:relative;box-sizing:border-box;overflow-x:auto;">
      <button id="fecharOrdemModal" style="position:absolute;top:8px;right:8px;background:none;border:none;font-size:1.5rem;color:#f44336;cursor:pointer;"><i class="fas fa-times"></i></button>
      <h2 id="ordem-modal-title" style="font-size:1.2rem;color:#0055c7;text-align:center;margin-bottom:12px;">Nova Ordem</h2>
      <form id="ordem-form" autocomplete="off">
        <input type="hidden" id="ordem-id">
        <input type="hidden" id="ordem-cliente-id">
        <input type="hidden" id="ordem-equipamento-id">
        <input type="hidden" id="ordem-tecnico-id">
        <div class="form-group" style="position:relative;">
          <input type="text" id="ordem-cliente" placeholder="Cliente *" required style="width:100%;padding:10px 8px;margin-bottom:10px;">
          <div id="autocomplete-clientes" style="position:absolute;top:100%;left:0;width:100%;background:#fff;z-index:10;box-shadow:0 2px 8px #3385ff22;display:none;"></div>
        </div>
        <div class="form-group" style="position:relative;">
          <input type="text" id="ordem-equipamento" placeholder="Equipamento *" required style="width:100%;padding:10px 8px;margin-bottom:10px;">
          <div id="autocomplete-equipamentos" style="position:absolute;top:100%;left:0;width:100%;background:#fff;z-index:10;box-shadow:0 2px 8px #3385ff22;display:none;"></div>
        </div>
        <div class="form-group" style="position:relative;">
          <input type="text" id="ordem-tecnico" placeholder="Técnico *" required style="width:100%;padding:10px 8px;margin-bottom:10px;">
          <div id="autocomplete-tecnicos" style="position:absolute;top:100%;left:0;width:100%;background:#fff;z-index:10;box-shadow:0 2px 8px #3385ff22;display:none;"></div>
        </div>
        <div class="form-group"><!-- status será substituído por select JS --><input type="text" id="ordem-status" placeholder="Status *" required style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
        <div class="form-group"><input type="date" id="ordem-data_entrada" placeholder="Data de Entrada" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
        <div class="form-group"><textarea id="ordem-observacoes" placeholder="Observações" style="width:100%;padding:10px 8px;height:54px;"></textarea></div>
        <div style="display:flex;gap:8px;justify-content:center;">
          <button type="submit" id="salvarOrdemBtn" style="background:#0055c7;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-weight:600;">Salvar</button>
          <button type="button" id="excluirOrdemBtn" style="background:#f44336;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-weight:600;display:none;">Excluir</button>
        </div>
      </form>
      <div id="ordem-modal-erro" style="color:#f44336;text-align:center;margin-top:8px;display:none;"></div>
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
    // Botão nova ordem
    document.getElementById('novaOrdemBtn').onclick = function() {
      document.getElementById('ordem-modal').style.display = 'flex';
      document.getElementById('ordem-modal-title').textContent = 'Nova Ordem';
      document.getElementById('ordem-form').reset();
      document.getElementById('ordem-id').value = '';
      document.getElementById('excluirOrdemBtn').style.display = 'none';
      document.getElementById('ordem-modal-erro').style.display = 'none';
    };
    // Fechar modal
    document.getElementById('fecharOrdemModal').onclick = function() {
      document.getElementById('ordem-modal').style.display = 'none';
    };
    // Busca e listagem real
    const ordensList = document.getElementById('ordens-list');
    const loading = document.getElementById('ordens-loading');
    const erro = document.getElementById('ordens-erro');
    const vazio = document.getElementById('ordens-vazio');
    const searchInput = document.getElementById('ordens-search');
    const searchForm = document.getElementById('ordens-search-form');
    let searchTimeout = null;
    function renderOrdens(ordens) {
      ordensList.innerHTML = '';
      if (!ordens.length) {
        vazio.style.display = 'block';
        return;
      }
      vazio.style.display = 'none';
      for (const o of ordens) {
        ordensList.innerHTML += `
        <div class="mobile-client-card" data-id="${o.id}">
          <div class="client-name">${o.numero || o.id || ''} - ${o.status || ''}</div>
          <div class="client-info"><i class="fas fa-user"></i> ${o.cliente_nome || '-'}</div>
          <div class="client-info"><i class="fas fa-laptop"></i> ${o.equipamento_nome || o.equipamento || '-'}</div>
          <div class="client-info"><i class="fas fa-calendar"></i> ${o.data_abertura || o.data_entrada || ''}</div>
        </div>`;
      }
      // Adiciona evento de clique para visualizar/editar
      document.querySelectorAll('.mobile-client-card').forEach(card => {
        card.onclick = async function() {
          const id = this.getAttribute('data-id');
          if (!id) return;
          abrirOrdemModal(id);
        };
      });
    }
    async function buscarOrdens(q = '') {
      erro.style.display = 'none';
      loading.style.display = 'flex';
      vazio.style.display = 'none';
      ordensList.innerHTML = '';
      try {
        const resp = await fetch(`../api/ordens.php?action=list&search=${encodeURIComponent(q)}`);
        const data = await resp.json();
        if (data.success) {
          renderOrdens(data.data);
        } else {
          throw new Error(data.error || 'Erro ao buscar ordens');
        }
      } catch (err) {
        erro.textContent = err.message;
        erro.style.display = 'block';
      } finally {
        loading.style.display = 'none';
      }
    }
    async function abrirOrdemModal(id) {
      document.getElementById('ordem-modal').style.display = 'flex';
      document.getElementById('ordem-modal-title').textContent = 'Visualizar/Editar Ordem';
      document.getElementById('ordem-form').reset();
      document.getElementById('ordem-id').value = id;
      document.getElementById('excluirOrdemBtn').style.display = 'block';
      document.getElementById('ordem-modal-erro').style.display = 'none';
      // Carregar dados da ordem
      try {
        const resp = await fetch(`../api/ordens.php?action=get&id=${id}`);
        const data = await resp.json();
        if (data.success) {
          const o = data.data;
          document.getElementById('ordem-cliente').value = o.cliente_nome || '';
          document.getElementById('ordem-cliente-id').value = o.cliente_id || '';
          document.getElementById('ordem-equipamento').value = o.marca && o.modelo ? `${o.marca} ${o.modelo}` : (o.equipamento_nome || '');
          document.getElementById('ordem-equipamento-id').value = o.equipamento_id || '';
          document.getElementById('ordem-status').value = o.status || '';
          document.getElementById('ordem-data_entrada').value = (o.data_entrada || '').slice(0,10);
          document.getElementById('ordem-observacoes').value = o.observacoes || '';
          document.getElementById('ordem-tecnico').value = o.tecnico_nome || '';
          document.getElementById('ordem-tecnico-id').value = o.tecnico_id || '';
          // Preencher dropdown de status
          preencherStatusDropdown(o.status);
        } else {
          throw new Error(data.error || 'Erro ao buscar ordem');
        }
      } catch (err) {
        document.getElementById('ordem-modal-erro').textContent = err.message;
        document.getElementById('ordem-modal-erro').style.display = 'block';
      }
    }
    function preencherStatusDropdown(selected) {
      const statusInput = document.getElementById('ordem-status');
      if (statusInput.tagName === 'SELECT') {
        statusInput.value = selected || '';
        return;
      }
      // Substitui input por select
      const select = document.createElement('select');
      select.id = 'ordem-status';
      select.required = true;
      select.style.width = '100%';
      select.style.padding = '10px 8px';
      select.style.marginBottom = '10px';
      const statusList = ['Orçamento','Executando','Aguardando Peça','Finalizada','Entregue','Cancelada'];
      for (const s of statusList) {
        const opt = document.createElement('option');
        opt.value = s;
        opt.textContent = s;
        if (selected === s) opt.selected = true;
        select.appendChild(opt);
      }
      statusInput.parentNode.replaceChild(select, statusInput);
    }
    // Busca em tempo real
    searchInput.oninput = function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => buscarOrdens(searchInput.value), 400);
    };
    searchForm.onsubmit = function(e) {
      e.preventDefault();
      buscarOrdens(searchInput.value);
    };
    // Inicial
    buscarOrdens();
    document.getElementById('ordem-form').onsubmit = async function(e) {
      e.preventDefault();
      const id = document.getElementById('ordem-id').value;
      const statusEl = document.getElementById('ordem-status');
      const status = statusEl.value;
      const obs = document.getElementById('ordem-observacoes').value;
      const clienteId = document.getElementById('ordem-cliente-id').value;
      const equipamentoId = document.getElementById('ordem-equipamento-id').value;
      const tecnicoId = document.getElementById('ordem-tecnico-id').value;
      // Para edição, seria necessário também os IDs de cliente e equipamento, mas aqui só status e obs
      try {
        const resp = await fetch(`../api/ordens.php`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `id=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}&observacoes=${encodeURIComponent(obs)}&cliente_id=${encodeURIComponent(clienteId)}&equipamento_id=${encodeURIComponent(equipamentoId)}&tecnico_id=${encodeURIComponent(tecnicoId)}`
        });
        const data = await resp.json();
        if (data.success) {
          document.getElementById('ordem-modal').style.display = 'none';
          buscarOrdens();
        } else {
          throw new Error(data.error || 'Erro ao salvar ordem');
        }
      } catch (err) {
        document.getElementById('ordem-modal-erro').textContent = err.message;
        document.getElementById('ordem-modal-erro').style.display = 'block';
      }
    };
    document.getElementById('excluirOrdemBtn').onclick = async function() {
      const id = document.getElementById('ordem-id').value;
      if (!confirm('Tem certeza que deseja excluir esta ordem?')) return;
      try {
        const resp = await fetch(`../api/ordens.php?id=${id}`, { method: 'DELETE' });
        const data = await resp.json();
        if (data.success) {
          document.getElementById('ordem-modal').style.display = 'none';
          buscarOrdens();
        } else {
          throw new Error(data.error || 'Erro ao excluir ordem');
        }
      } catch (err) {
        document.getElementById('ordem-modal-erro').textContent = err.message;
        document.getElementById('ordem-modal-erro').style.display = 'block';
      }
    };
    // Autocomplete clientes
    const clienteInput = document.getElementById('ordem-cliente');
    const clienteIdInput = document.getElementById('ordem-cliente-id');
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
    const equipamentoInput = document.getElementById('ordem-equipamento');
    const equipamentoIdInput = document.getElementById('ordem-equipamento-id');
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
    // Autocomplete técnicos
    const tecnicoInput = document.getElementById('ordem-tecnico');
    const tecnicoIdInput = document.getElementById('ordem-tecnico-id');
    const autocompleteTecnicos = document.getElementById('autocomplete-tecnicos');
    let tecnicoTimeout = null;
    tecnicoInput.oninput = function() {
      clearTimeout(tecnicoTimeout);
      const q = tecnicoInput.value.trim();
      if (!q) { autocompleteTecnicos.style.display = 'none'; return; }
      tecnicoTimeout = setTimeout(async () => {
        const resp = await fetch(`../api/usuarios.php?action=list&search=${encodeURIComponent(q)}&tipo=tecnico&limit=10`);
        const data = await resp.json();
        if (data.success && data.data.length) {
          autocompleteTecnicos.innerHTML = data.data.map(t => `<div class='autocomplete-item' data-id='${t.id}' data-nome='${t.nome}'>${t.nome} <small>${t.email||''}</small></div>`).join('');
          autocompleteTecnicos.style.display = 'block';
          document.querySelectorAll('#autocomplete-tecnicos .autocomplete-item').forEach(item => {
            item.onclick = function() {
              tecnicoInput.value = this.getAttribute('data-nome');
              tecnicoIdInput.value = this.getAttribute('data-id');
              autocompleteTecnicos.style.display = 'none';
            };
          });
        } else {
          autocompleteTecnicos.style.display = 'none';
        }
      }, 300);
    };
    tecnicoInput.onblur = function() { setTimeout(()=>autocompleteTecnicos.style.display='none', 200); };
  </script>
</body>
</html> 