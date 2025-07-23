// assets/js/config-tema.js

function applyTheme(theme) {
  if (theme === 'dark') {
    document.body.classList.add('dark-theme');
  } else {
    document.body.classList.remove('dark-theme');
  }
}

async function carregarTemaUsuario() {
  const usuarioId = window.usuarioId;
  if (usuarioId) {
    const resp = await fetch('api/usuarios.php?action=get_tema&usuario_id=' + usuarioId);
    const data = await resp.json();
    if (data.success && data.tema) {
      document.getElementById('temaClaro').checked = data.tema === 'light';
      document.getElementById('temaEscuro').checked = data.tema === 'dark';
      applyTheme(data.tema);
      localStorage.setItem('theme', data.tema);
    }
  }
}

const CORES_PADRAO = {
  cor_primaria: '#0055c7',
  cor_secundaria: '#4f8cff',
  cor_fundo: '#f5f5f5',
  cor_menu: '#fff',
  cor_card: '#fff',
  cor_botao_primario: '#0055c7',
  cor_botao_secundario: '#6c757d',
  cor_footer: '#fff',
  cor_header: '#fff',
  cor_borda: '#e0e0e0',
  cor_sucesso: '#43aa8b',
  cor_erro: '#f44336',
  cor_aviso: '#ffb300',
  cor_hover_menu: '#eaf1fb',
  cor_sidebar: '#fff',
  cor_texto: '#222'
};

function applyCustomColors(cores) {
  if (!cores) return;
  for (const key in CORES_PADRAO) {
    document.documentElement.style.setProperty('--' + key.replace(/_/g, '-'), cores[key] || CORES_PADRAO[key]);
  }
}

async function carregarCoresUsuario() {
  const usuarioId = window.usuarioId;
  if (usuarioId) {
    const resp = await fetch('api/usuarios.php?action=get_cores&usuario_id=' + usuarioId);
    const data = await resp.json();
    if (data.success && data.cores) {
      for (const key in CORES_PADRAO) {
        if (document.getElementById(key)) {
          document.getElementById(key).value = data.cores[key] || CORES_PADRAO[key];
        }
      }
      applyCustomColors(data.cores);
    }
  }
}

document.addEventListener('DOMContentLoaded', function() {
  carregarTemaUsuario();
  carregarCoresUsuario();

  document.getElementById('formTema').addEventListener('submit', async function(e) {
    e.preventDefault();
    const tema = document.querySelector('input[name="tema"]:checked').value;
    applyTheme(tema);
    localStorage.setItem('theme', tema);

    // Salvar no banco
    const usuarioId = window.usuarioId;
    let msg = document.getElementById('temaMsg');
    if (usuarioId) {
      const resp = await fetch('api/usuarios.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=set_tema&usuario_id=${usuarioId}&tema=${tema}`
      });
      const data = await resp.json();
      if (data.success) {
        msg.innerHTML = '<div class="alert alert-success">Tema salvo com sucesso!</div>';
      } else {
        msg.innerHTML = '<div class="alert alert-danger">Erro ao salvar tema.</div>';
      }
    }
  });

  document.getElementById('formCores').addEventListener('input', function() {
    // Aplicar dinamicamente ao mudar
    const cores = {};
    for (const key in CORES_PADRAO) {
      if (this[key]) cores[key] = this[key].value;
    }
    applyCustomColors(cores);
  });

  document.getElementById('formCores').addEventListener('submit', async function(e) {
    e.preventDefault();
    const usuarioId = window.usuarioId;
    let msg = document.getElementById('coresMsg');
    const form = this;
    const body = new URLSearchParams({ action: 'set_cores', usuario_id: usuarioId });
    for (const key in CORES_PADRAO) {
      if (form[key]) body.append(key, form[key].value);
    }
    const resp = await fetch('api/usuarios.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    });
    const data = await resp.json();
    if (data.success) {
      msg.innerHTML = '<div class="alert alert-success">Cores salvas com sucesso!</div>';
    } else {
      msg.innerHTML = '<div class="alert alert-danger">Erro ao salvar cores.</div>';
    }
  });

  document.getElementById('btnRestaurarCores').addEventListener('click', function() {
    // Resetar inputs para padrão
    for (const key in CORES_PADRAO) {
      if (document.getElementById(key)) {
        document.getElementById(key).value = CORES_PADRAO[key];
      }
    }
    // Aplicar imediatamente
    applyCustomColors(CORES_PADRAO);
    // Mensagem opcional
    document.getElementById('coresMsg').innerHTML = '<div class="alert alert-info">Cores restauradas para o padrão. Clique em Salvar Cores para manter.</div>';
  });
}); 