<?php
require_once '../includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Empresa - Mobile</title>
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
    .empresa-loading {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 30px 0 20px 0;
    }
    .empresa-spinner {
      width: 38px; height: 38px;
      border: 4px solid #3385ff;
      border-top: 4px solid #fff;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
    .empresa-erro, .empresa-sucesso {
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
    .empresa-sucesso {
      background: #43aa8b;
      box-shadow: 0 2px 8px #43aa8b22;
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
    .mobile-client-card input {
      width: 100%;
      padding: 10px 8px;
      border: 1.5px solid #0055c7;
      border-radius: 8px;
      font-size: 1rem;
      margin-top: 2px;
      margin-bottom: 8px;
      background: #f7f7f7;
      color: #222;
    }
    .mobile-client-card input:focus {
      border-color: #3385ff;
      background: #fff;
      outline: none;
    }
    #empresa-modal input, #empresa-modal textarea {
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
    <h1 class="mobile-title">Empresa</h1>
    <div id="empresa-loading" class="empresa-loading" style="display:none;"><div class="empresa-spinner"></div></div>
    <div id="empresa-erro" class="empresa-erro" style="display:none;"></div>
    <div id="empresa-sucesso" class="empresa-sucesso" style="display:none;"></div>
    <section class="mobile-client-list">
      <form class="mobile-client-card" id="empresa-form" autocomplete="off">
        <div class="client-name">Dados da Empresa</div>
        <div class="client-info"><i class="fas fa-building"></i> <input type="text" id="empresa-nome" name="nome" placeholder="Nome da Empresa"></div>
        <div class="client-info"><i class="fas fa-envelope"></i> <input type="email" id="empresa-email" name="email" placeholder="E-mail"></div>
        <div class="client-info"><i class="fas fa-phone"></i> <input type="text" id="empresa-telefone" name="telefone" placeholder="Telefone"></div>
        <div class="client-info"><i class="fas fa-id-card"></i> <input type="text" id="empresa-cnpj" name="cnpj" placeholder="CNPJ"></div>
        <div class="client-info"><i class="fas fa-map-marker-alt"></i> <input type="text" id="empresa-endereco" name="endereco" placeholder="Endereço"></div>
        <div class="client-info"><i class="fas fa-city"></i> <input type="text" id="empresa-cidade" name="cidade" placeholder="Cidade"></div>
        <div class="client-info"><i class="fas fa-flag"></i> <input type="text" id="empresa-uf" name="uf" placeholder="UF" maxlength="2"></div>
      </form>
    </section>
    <button class="fab-btn" id="salvarEmpresaBtn" title="Salvar Empresa"><i class="fas fa-save"></i></button>
  </main>
  <!-- Modal de empresa -->
  <div id="empresa-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);z-index:300;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;box-shadow:0 4px 18px #3385ff33;padding:16px 12px 12px 12px;max-width:480px;width:96vw;min-width:0;position:relative;box-sizing:border-box;overflow-x:auto;">
      <button id="fecharEmpresaModal" style="position:absolute;top:8px;right:8px;background:none;border:none;font-size:1.5rem;color:#f44336;cursor:pointer;"><i class="fas fa-times"></i></button>
      <h2 id="empresa-modal-title" style="font-size:1.2rem;color:#0055c7;text-align:center;margin-bottom:12px;">Editar Empresa</h2>
      <form id="empresa-form-modal" autocomplete="off">
        <input type="hidden" id="empresa-id">
        <div class="form-group"><input type="text" id="empresa-nome-modal" placeholder="Nome da Empresa *" required></div>
        <div class="form-group"><input type="email" id="empresa-email-modal" placeholder="E-mail"></div>
        <div class="form-group"><input type="text" id="empresa-telefone-modal" placeholder="Telefone"></div>
        <div class="form-group"><input type="text" id="empresa-cnpj-modal" placeholder="CNPJ"></div>
        <div class="form-group"><input type="text" id="empresa-endereco-modal" placeholder="Endereço"></div>
        <div class="form-group"><input type="text" id="empresa-numero-modal" placeholder="Número"></div>
        <div class="form-group"><input type="text" id="empresa-complemento-modal" placeholder="Complemento"></div>
        <div class="form-group"><input type="text" id="empresa-bairro-modal" placeholder="Bairro"></div>
        <div class="form-group"><input type="text" id="empresa-cidade-modal" placeholder="Cidade"></div>
        <div class="form-group"><input type="text" id="empresa-uf-modal" maxlength="2" placeholder="UF"></div>
        <div class="form-group"><input type="text" id="empresa-cep-modal" maxlength="9" placeholder="CEP"></div>
        <div class="form-group"><textarea id="empresa-observacoes-modal" placeholder="Observações" style="height:54px;"></textarea></div>
        <div style="display:flex;gap:8px;justify-content:center;">
          <button type="submit" id="salvarEmpresaBtnModal" style="background:#0055c7;color:#fff;border:none;border-radius:8px;padding:10px 18px;font-weight:600;">Salvar</button>
        </div>
      </form>
      <div id="empresa-modal-erro" style="color:#f44336;text-align:center;margin-top:8px;display:none;"></div>
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
    // Loading, erro, sucesso
    const loading = document.getElementById('empresa-loading');
    const erro = document.getElementById('empresa-erro');
    const sucesso = document.getElementById('empresa-sucesso');
    const form = document.getElementById('empresa-form');
    // Carregar dados da empresa
    async function carregarEmpresa() {
      erro.style.display = 'none';
      sucesso.style.display = 'none';
      loading.style.display = 'flex';
      try {
        const resp = await fetch('../api/empresa.php?action=get');
        const data = await resp.json();
        if (data.success && data.data) {
          form['empresa-nome'].value = data.data.nome || '';
          form['empresa-email'].value = data.data.email || '';
          form['empresa-telefone'].value = data.data.telefone || '';
          form['empresa-cnpj'].value = data.data.cnpj || '';
          form['empresa-endereco'].value = data.data.endereco || '';
          form['empresa-cidade'].value = data.data.cidade || '';
          form['empresa-uf'].value = data.data.uf || '';
        } else {
          throw new Error(data.error || 'Erro ao carregar dados da empresa');
        }
      } catch (err) {
        erro.textContent = err.message;
        erro.style.display = 'block';
      } finally {
        loading.style.display = 'none';
      }
    }
    // Salvar dados da empresa
    document.getElementById('salvarEmpresaBtn').onclick = async function() {
      erro.style.display = 'none';
      sucesso.style.display = 'none';
      loading.style.display = 'flex';
      try {
        const fd = new FormData(form);
        fd.append('action', 'update');
        const resp = await fetch('../api/empresa.php', {
          method: 'POST',
          body: fd
        });
        const data = await resp.json();
        if (data.success) {
          sucesso.textContent = 'Dados salvos com sucesso!';
          sucesso.style.display = 'block';
        } else {
          throw new Error(data.error || 'Erro ao salvar dados');
        }
      } catch (err) {
        erro.textContent = err.message;
        erro.style.display = 'block';
      } finally {
        loading.style.display = 'none';
      }
    };
    // Inicial
    carregarEmpresa();
  </script>
</body>
</html> 