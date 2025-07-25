<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Mobile</title>
  <link rel="stylesheet" href="../assets/css/mobile-only.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .mobile-login-container {
      max-width: 350px;
      margin: 40px auto 0 auto;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 18px rgba(0,85,199,0.13);
      padding: 36px 18px 28px 18px;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }
    .mobile-login-container h2 {
      color: #0055c7;
      font-size: 1.5rem;
      margin-bottom: 18px;
      text-align: center;
      font-weight: 700;
      letter-spacing: 0.5px;
    }
    .mobile-login-container .form-group {
      width: 100%;
      margin-bottom: 18px;
    }
    .mobile-login-container input {
      width: 100%;
      padding: 13px 12px;
      border: 1.7px solid #0055c7;
      border-radius: 9px;
      font-size: 1.05rem;
      margin-top: 4px;
      transition: border-color 0.2s, box-shadow 0.2s;
      outline: none;
    }
    .mobile-login-container input:focus {
      border-color: #3385ff;
      box-shadow: 0 0 0 2px #3385ff33;
    }
    .mobile-login-container button {
      width: 100%;
      background: linear-gradient(90deg, #0055c7 60%, #3385ff 100%);
      color: #fff;
      border: none;
      border-radius: 9px;
      padding: 13px 0;
      font-size: 1.13rem;
      font-weight: 700;
      margin-top: 10px;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(0,85,199,0.10);
      transition: background 0.2s, box-shadow 0.2s, opacity 0.2s;
      position: relative;
      overflow: hidden;
    }
    .mobile-login-container button:active {
      opacity: 0.85;
    }
    .mobile-login-logo {
      font-weight: bold;
      font-size: 1.7rem;
      color: #0055c7;
      margin-bottom: 18px;
      letter-spacing: 1px;
      text-shadow: 0 2px 8px #3385ff22;
    }
    .login-error {
      color: #fff;
      background: #f44336;
      border-radius: 8px;
      padding: 10px 12px;
      margin-bottom: 12px;
      width: 100%;
      text-align: center;
      font-size: 1rem;
      box-shadow: 0 2px 8px #f4433622;
      animation: shake 0.3s;
    }
    @keyframes shake {
      0% { transform: translateX(0); }
      25% { transform: translateX(-6px); }
      50% { transform: translateX(6px); }
      75% { transform: translateX(-4px); }
      100% { transform: translateX(0); }
    }
    .login-loading {
      position: absolute;
      right: 18px;
      top: 18px;
      width: 28px;
      height: 28px;
      border: 3px solid #3385ff;
      border-top: 3px solid #fff;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
      z-index: 2;
      background: none;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body style="background:#f7f7f7;">
  <div class="mobile-login-container">
    <div class="mobile-login-logo">PASKi</div>
    <h2>Login</h2>
    <div id="login-error" style="display:none;"></div>
    <form id="login-form" autocomplete="on">
      <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required placeholder="Digite seu e-mail">
      </div>
      <div class="form-group">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required placeholder="Digite sua senha">
      </div>
      <button type="submit" id="login-btn">Entrar</button>
    </form>
    <div id="login-loading" class="login-loading" style="display:none;"></div>
  </div>
  <script>
    const form = document.getElementById('login-form');
    const btn = document.getElementById('login-btn');
    const errorDiv = document.getElementById('login-error');
    const loading = document.getElementById('login-loading');
    form.onsubmit = async function(e) {
      e.preventDefault();
      errorDiv.style.display = 'none';
      loading.style.display = 'block';
      btn.disabled = true;
      btn.style.opacity = 0.7;
      const email = form.email.value.trim();
      const senha = form.senha.value;
      try {
        const fd = new FormData();
        fd.append('action', 'login');
        fd.append('email', email);
        fd.append('senha', senha);
        const resp = await fetch('../api/auth.php', {
          method: 'POST',
          credentials: 'same-origin',
          body: fd
        });
        const data = await resp.json();
        if (data.success) {
          window.location.href = 'index.php';
        } else {
          throw new Error(data.error || 'Falha no login');
        }
      } catch (err) {
        errorDiv.textContent = err.message;
        errorDiv.style.display = 'block';
      } finally {
        loading.style.display = 'none';
        btn.disabled = false;
        btn.style.opacity = 1;
      }
    };
  </script>
</body>
</html> 