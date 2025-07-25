<?php
require_once '../includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clientes - Mobile</title>
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
    .clientes-loading {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 30px 0 20px 0;
    }
    .clientes-spinner {
      width: 38px; height: 38px;
      border: 4px solid #3385ff;
      border-top: 4px solid #fff;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
    .clientes-erro, .clientes-vazio {
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
    .clientes-vazio {
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
    #cliente-modal input, #cliente-modal textarea {
      width: 100%;
      box-sizing: border-box;
      min-width: 0;
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
    <h1 class="mobile-title">Clientes</h1>
    <form class="mobile-search" id="clientes-search-form" autocomplete="off">
      <input type="text" id="clientes-search" placeholder="Buscar por nome, celular, email...">
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
    <div id="clientes-loading" class="clientes-loading" style="display:none;"><div class="clientes-spinner"></div></div>
    <div id="clientes-erro" class="clientes-erro" style="display:none;"></div>
    <section class="mobile-client-list" id="clientes-list"></section>
    <div id="clientes-vazio" class="clientes-vazio" style="display:none;">Nenhum cliente encontrado.</div>
    <button class="fab-btn" id="novoClienteBtn" title="Novo Cliente"><i class="fas fa-plus"></i></button>
    <!-- Modal de cliente -->
    <div id="cliente-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);z-index:300;align-items:center;justify-content:center;">
      <div style="background:#fff;border-radius:14px;box-shadow:0 4px 18px #3385ff33;padding:16px 12px 12px 12px;max-width:480px;width:96vw;min-width:0;position:relative;box-sizing:border-box;overflow-x:auto;">
        <button id="fecharClienteModal" style="position:absolute;top:8px;right:8px;background:none;border:none;font-size:1.5rem;color:#f44336;cursor:pointer;"><i class="fas fa-times"></i></button>
        <h2 id="cliente-modal-title" style="font-size:1.2rem;color:#0055c7;text-align:center;margin-bottom:12px;">Novo Cliente</h2>
        <form id="cliente-form" autocomplete="off">
          <input type="hidden" id="cliente-id">
          <div class="form-group"><input type="text" id="cliente-nome" placeholder="Nome / Razão Social *" required style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
          <div class="form-group"><input type="text" id="cliente-ie" placeholder="Inscrição Estadual" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
          <div class="form-group" style="display:flex;gap:8px;align-items:center;margin-bottom:10px;">
            <input type="text" id="cliente-cpf" placeholder="CPF/CNPJ *" required style="flex:1;padding:10px 8px;">
            <button type="button" id="buscar-cnpj" style="background:#3385ff;color:#fff;border:none;border-radius:8px;padding:8px 12px;font-weight:600;">Buscar CNPJ</button>
          </div>
          <div class="form-group"><input type="text" id="cliente-telefone" placeholder="Telefone" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
          <div class="form-group"><input type="text" id="cliente-celular" placeholder="Celular *" required style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
          <div class="form-group"><input type="email" id="cliente-email" placeholder="E-mail" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
          <div class="form-group"><input type="text" id="cliente-endereco" placeholder="Endereço" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
          <div class="form-group"><input type="text" id="cliente-numero" placeholder="Número" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
          <div class="form-group"><input type="text" id="cliente-complemento" placeholder="Complemento" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
          <div class="form-group"><input type="text" id="cliente-bairro" placeholder="Bairro" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
          <div class="form-group"><input type="text" id="cliente-cidade" placeholder="Cidade" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
          <div class="form-group"><input type="text" id="cliente-estado" maxlength="2" placeholder="UF" style="width:100%;padding:10px 8px;margin-bottom:10px;"></div>
          <div class="form-group" style="display:flex;gap:8px;align-items:center;margin-bottom:10px;">
            <input type="text" id="cliente-cep" maxlength="9" placeholder="CEP" style="flex:1;padding:10px 8px;">
            <button type="button" id="buscar-cep" style="background:#3385ff;color:#fff;border:none;border-radius:8px;padding:8px 12px;font-weight:600;">Buscar CEP</button>
          </div>
          <div class="form-group"><textarea id="cliente-observacoes" placeholder="Observações" style="width:100%;padding:10px 8px;height:54px;"></textarea></div>
          <div style="display:flex;gap:8px;justify-content:center;">
            <button type="submit" id="salvarClienteBtn" style="background:#0055c7;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-weight:600;">Salvar</button>
            <button type="button" id="excluirClienteBtn" style="background:#f44336;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-weight:600;display:none;">Excluir</button>
          </div>
        </form>
        <div id="cliente-modal-erro" style="color:#f44336;text-align:center;margin-top:8px;display:none;"></div>
      </div>
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
    // Botão novo cliente
    document.getElementById('novoClienteBtn').onclick = function() {
      abrirClienteModal();
    };
    // Busca e listagem real
    const clientesList = document.getElementById('clientes-list');
    const loading = document.getElementById('clientes-loading');
    const erro = document.getElementById('clientes-erro');
    const vazio = document.getElementById('clientes-vazio');
    const searchInput = document.getElementById('clientes-search');
    const searchForm = document.getElementById('clientes-search-form');
    let searchTimeout = null;
    // CRUD Cliente
    const clienteModal = document.getElementById('cliente-modal');
    const clienteForm = document.getElementById('cliente-form');
    const clienteModalErro = document.getElementById('cliente-modal-erro');
    const excluirClienteBtn = document.getElementById('excluirClienteBtn');
    // Abrir modal novo cliente
    function abrirClienteModal(id = null) {
      clienteModalErro.style.display = 'none';
      clienteForm.reset();
      excluirClienteBtn.style.display = 'none';
      document.getElementById('cliente-modal-title').textContent = id ? 'Editar Cliente' : 'Novo Cliente';
      if (id) {
        // Buscar dados do cliente
        fetch(`../api/clientes.php?action=get&id=${id}`)
          .then(resp => resp.json())
          .then(data => {
            if (data.success && data.data) {
              document.getElementById('cliente-id').value = data.data.id;
              document.getElementById('cliente-nome').value = data.data.nome || '';
              document.getElementById('cliente-ie').value = data.data.inscricao_estadual || '';
              document.getElementById('cliente-cpf').value = data.data.cpf_cnpj || '';
              document.getElementById('cliente-telefone').value = data.data.telefone || '';
              document.getElementById('cliente-celular').value = data.data.celular || '';
              document.getElementById('cliente-email').value = data.data.email || '';
              document.getElementById('cliente-endereco').value = data.data.endereco || '';
              document.getElementById('cliente-numero').value = data.data.numero || '';
              document.getElementById('cliente-complemento').value = data.data.complemento || '';
              document.getElementById('cliente-bairro').value = data.data.bairro || '';
              document.getElementById('cliente-cidade').value = data.data.cidade || '';
              document.getElementById('cliente-estado').value = data.data.uf || '';
              document.getElementById('cliente-cep').value = data.data.cep || '';
              document.getElementById('cliente-observacoes').value = data.data.observacoes || '';
              excluirClienteBtn.style.display = 'inline-block';
            } else {
              throw new Error(data.error || 'Erro ao buscar cliente');
            }
          })
          .catch(err => {
            clienteModalErro.textContent = err.message;
            clienteModalErro.style.display = 'block';
          });
      }
      clienteModal.style.display = 'flex';
    }
    // Fechar modal
    document.getElementById('fecharClienteModal').onclick = function() {
      clienteModal.style.display = 'none';
    };
    // Abrir modal para editar
    clientesList.onclick = function(e) {
      const card = e.target.closest('.mobile-client-card');
      if (!card) return;
      const id = card.dataset.id;
      if (!id) return;
      abrirClienteModal(id);
    };
    // Salvar cliente (criar/editar)
    clienteForm.onsubmit = async function(e) {
      e.preventDefault();
      clienteModalErro.style.display = 'none';
      const id = document.getElementById('cliente-id').value;
      const nome = document.getElementById('cliente-nome').value.trim();
      const ie = document.getElementById('cliente-ie').value.trim();
      const cpf = document.getElementById('cliente-cpf').value.trim();
      const telefone = document.getElementById('cliente-telefone').value.trim();
      const celular = document.getElementById('cliente-celular').value.trim();
      const email = document.getElementById('cliente-email').value.trim();
      const endereco = document.getElementById('cliente-endereco').value.trim();
      const numero = document.getElementById('cliente-numero').value.trim();
      const complemento = document.getElementById('cliente-complemento').value.trim();
      const bairro = document.getElementById('cliente-bairro').value.trim();
      const cidade = document.getElementById('cliente-cidade').value.trim();
      const estado = document.getElementById('cliente-estado').value.trim();
      const cep = document.getElementById('cliente-cep').value.trim();
      const observacoes = document.getElementById('cliente-observacoes').value.trim();

      const fd = new FormData();
      fd.append('nome', nome);
      fd.append('inscricao_estadual', ie);
      fd.append('cpf_cnpj', cpf);
      fd.append('telefone', telefone);
      fd.append('celular', celular);
      fd.append('email', email);
      fd.append('endereco', endereco);
      fd.append('numero', numero);
      fd.append('complemento', complemento);
      fd.append('bairro', bairro);
      fd.append('cidade', cidade);
      fd.append('uf', estado);
      fd.append('cep', cep);
      fd.append('observacoes', observacoes);
      fd.append('action', id ? 'update' : 'create');
      if (id) fd.append('id', id);
      try {
        const resp = await fetch('../api/clientes.php', { method: 'POST', body: fd });
        const data = await resp.json();
        if (data.success) {
          clienteModal.style.display = 'none';
          buscarClientes(searchInput.value);
        } else {
          throw new Error(data.error || 'Erro ao salvar cliente');
        }
      } catch (err) {
        clienteModalErro.textContent = err.message;
        clienteModalErro.style.display = 'block';
      }
    };
    // Excluir cliente
    excluirClienteBtn.onclick = async function() {
      if (!confirm('Tem certeza que deseja excluir este cliente?')) return;
      const id = document.getElementById('cliente-id').value;
      if (!id) return;
      const fd = new FormData();
      fd.append('action', 'delete');
      fd.append('id', id);
      try {
        const resp = await fetch('../api/clientes.php', { method: 'POST', body: fd });
        const data = await resp.json();
        if (data.success) {
          clienteModal.style.display = 'none';
          buscarClientes(searchInput.value);
        } else {
          throw new Error(data.error || 'Erro ao excluir cliente');
        }
      } catch (err) {
        clienteModalErro.textContent = err.message;
        clienteModalErro.style.display = 'block';
      }
    };
    function renderClientes(clientes) {
      clientesList.innerHTML = '';
      if (!clientes.length) {
        vazio.style.display = 'block';
        return;
      }
      vazio.style.display = 'none';
      for (const c of clientes) {
        clientesList.innerHTML += `
        <div class="mobile-client-card" data-id="${c.id}">
          <div class="client-name">${c.nome || ''}</div>
          <div class="client-info"><i class="fas fa-phone"></i> ${c.celular || c.telefone || '-'}</div>
          <div class="client-info"><i class="fas fa-envelope"></i> ${c.email || '-'}</div>
          <div class="client-info"><i class="fas fa-id-card"></i> ${c.cpf_cnpj || '-'}</div>
          <div class="client-info"><i class="fas fa-map-marker-alt"></i> ${c.cidade || ''}</div>
        </div>`;
      }
    }
    async function buscarClientes(q = '') {
      erro.style.display = 'none';
      loading.style.display = 'flex';
      vazio.style.display = 'none';
      clientesList.innerHTML = '';
      try {
        const resp = await fetch(`../api/clientes.php?action=list&search=${encodeURIComponent(q)}`);
        const data = await resp.json();
        if (data.success) {
          renderClientes(data.data);
        } else {
          throw new Error(data.error || 'Erro ao buscar clientes');
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
      searchTimeout = setTimeout(() => buscarClientes(searchInput.value), 400);
    };
    searchForm.onsubmit = function(e) {
      e.preventDefault();
      buscarClientes(searchInput.value);
    };
    // Busca automática de CEP
    document.getElementById('buscar-cep').onclick = async function() {
      const cep = document.getElementById('cliente-cep').value.replace(/\D/g, '');
      if (cep.length !== 8) {
        clienteModalErro.textContent = 'CEP inválido!';
        clienteModalErro.style.display = 'block';
        return;
      }
      try {
        const resp = await fetch(`../api/clientes.php?action=cep&cep=${cep}`);
        const data = await resp.json();
        if (data.erro || data.success === false) {
          clienteModalErro.textContent = 'CEP não encontrado!';
          clienteModalErro.style.display = 'block';
          return;
        }
        document.getElementById('cliente-endereco').value = data.logradouro || '';
        document.getElementById('cliente-numero').value = '';
        document.getElementById('cliente-complemento').value = '';
        document.getElementById('cliente-bairro').value = data.bairro || '';
        document.getElementById('cliente-cidade').value = data.localidade || '';
        document.getElementById('cliente-estado').value = data.uf || '';
      } catch (err) {
        clienteModalErro.textContent = 'Erro ao buscar CEP';
        clienteModalErro.style.display = 'block';
      }
    };
    // Busca automática de CNPJ
    document.getElementById('buscar-cnpj').onclick = async function() {
      const cnpj = document.getElementById('cliente-cpf').value.replace(/\D/g, '');
      if (cnpj.length !== 14) {
        clienteModalErro.textContent = 'CNPJ inválido!';
        clienteModalErro.style.display = 'block';
        return;
      }
      try {
        const resp = await fetch(`../api/clientes.php?action=cnpj&cnpj=${cnpj}`);
        const data = await resp.json();
        if (data.erro || data.success === false) {
          clienteModalErro.textContent = 'CNPJ não encontrado!';
          clienteModalErro.style.display = 'block';
          return;
        }
        document.getElementById('cliente-nome').value = data.nome || '';
        document.getElementById('cliente-ie').value = data.inscricao_estadual || '';
        document.getElementById('cliente-email').value = data.email || '';
        document.getElementById('cliente-endereco').value = data.logradouro || '';
        document.getElementById('cliente-numero').value = data.numero || '';
        document.getElementById('cliente-complemento').value = data.complemento || '';
        document.getElementById('cliente-bairro').value = data.bairro || '';
        document.getElementById('cliente-cidade').value = data.municipio || data.cidade || '';
        document.getElementById('cliente-estado').value = data.uf || '';
        document.getElementById('cliente-cep').value = data.cep || '';
      } catch (err) {
        clienteModalErro.textContent = 'Erro ao buscar CNPJ';
        clienteModalErro.style.display = 'block';
      }
    };
    // Inicial
    buscarClientes();
  </script>
</body>
</html> 