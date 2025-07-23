// Global Application JavaScript
class App {
    constructor() {
        this.currentUser = null;
        this.currentPage = 'dashboard';
        this.init();
    }

    init() {
        // Initialize the application
        this.checkAuth();
        this.initEventListeners();
        this.initializeData();
        
        // Hide loading screen after 1 second
        setTimeout(() => {
            document.getElementById('loading-screen').style.display = 'none';
        }, 1000);
    }

    initEventListeners() {
        // Sidebar navigation
        document.querySelectorAll('[data-page]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.navigateTo(e.target.closest('[data-page]').dataset.page);
            });
        });

        // Sidebar toggle
        document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
            this.toggleSidebar();
            // Esconde a sidebar e mostra o botão flutuante
            document.getElementById('sidebar').style.display = 'none';
            document.getElementById('sidebar-open').style.display = 'block';
        });

        // Botão flutuante para reabrir a sidebar
        document.getElementById('sidebar-open')?.addEventListener('click', () => {
            document.getElementById('sidebar').style.display = 'block';
            document.getElementById('sidebar-open').style.display = 'none';
        });

        document.getElementById('mobile-sidebar-toggle')?.addEventListener('click', () => {
            this.toggleMobileSidebar();
        });

        // Window resize handler
        window.addEventListener('resize', () => {
            this.handleResize();
        });
    }

    checkAuth() {
        const user = localStorage.getItem('currentUser');
        if (user) {
            this.currentUser = JSON.parse(user);
            this.showMainApp();
        } else {
            this.showLoginPage();
        }
    }

    showLoginPage() {
        document.getElementById('login-page').classList.remove('d-none');
        document.getElementById('main-app').classList.add('d-none');
    }

    showMainApp() {
        document.getElementById('login-page').classList.add('d-none');
        document.getElementById('main-app').classList.remove('d-none');
        
        if (this.currentUser) {
            document.getElementById('user-name').textContent = this.currentUser.nome;
        }
        
        this.navigateTo('dashboard');
    }

    navigateTo(page) {
        // Update active nav item
        document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
            link.classList.remove('active');
        });
        document.querySelector(`[data-page="${page}"]`).classList.add('active');

        // Update breadcrumb
        this.updateBreadcrumb(page);

        // Load page content
        this.loadPage(page);
        this.currentPage = page;
    }

    updateBreadcrumb(page) {
        const breadcrumb = document.getElementById('breadcrumb');
        const pageNames = {
            dashboard: 'Dashboard',
            clientes: 'Clientes',
            equipamentos: 'Equipamentos',
            ordens: 'Ordens de Serviço',
            usuarios: 'Usuários',
            relatorios: 'Relatórios'
        };

        breadcrumb.innerHTML = `<li class="breadcrumb-item active">${pageNames[page]}</li>`;
    }

    loadPage(page) {
        const content = document.getElementById('page-content');
        content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';

        // Simulate loading delay
        setTimeout(() => {
            switch (page) {
                case 'dashboard':
                    Dashboard.render();
                    break;
                case 'clientes':
                    Clientes.render();
                    break;
                case 'equipamentos':
                    Equipamentos.render();
                    break;
                case 'ordens':
                    Ordens.render();
                    break;
                case 'usuarios':
                    Usuarios.render();
                    break;
                case 'relatorios':
                    Relatorios.render();
                    break;
                default:
                    content.innerHTML = '<div class="alert alert-warning">Página não encontrada</div>';
            }
        }, 300);
    }

    toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content');
        
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('sidebar-collapsed');
        document.body.classList.toggle('sidebar-collapsed');
    }

    toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('show');
    }

    handleResize() {
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth > 768) {
            sidebar.classList.remove('show');
        }
    }

    initializeData() {
        // Initialize demo data if not exists
        if (!localStorage.getItem('usuarios')) {
            this.createDemoData();
        }
    }

    createDemoData() {
        // Demo users
        const usuarios = [
            {
                id: 1,
                nome: 'Admin Sistema',
                email: 'admin@sistema.com',
                senha: 'admin123', // In real app, this should be hashed
                tipo: 'admin',
                criado_em: new Date().toISOString()
            },
            {
                id: 2,
                nome: 'João Técnico',
                email: 'joao@sistema.com',
                senha: 'tecnico123',
                tipo: 'tecnico',
                criado_em: new Date().toISOString()
            }
        ];

        // Demo clients
        const clientes = [
            {
                id: 1,
                nome: 'Maria Silva',
                telefone: '(11) 99999-9999',
                email: 'maria@email.com',
                cpf_cnpj: '123.456.789-00',
                endereco: 'Rua das Flores, 123 - São Paulo/SP',
                criado_em: new Date().toISOString()
            },
            {
                id: 2,
                nome: 'José Santos',
                telefone: '(11) 88888-8888',
                email: 'jose@email.com',
                cpf_cnpj: '987.654.321-00',
                endereco: 'Av. Principal, 456 - São Paulo/SP',
                criado_em: new Date().toISOString()
            }
        ];

        // Demo equipment
        const equipamentos = [
            {
                id: 1,
                cliente_id: 1,
                tipo: 'Smartphone',
                marca: 'Samsung',
                modelo: 'Galaxy S21',
                numero_serie: 'SN123456789',
                observacoes: 'Tela trincada',
                criado_em: new Date().toISOString()
            },
            {
                id: 2,
                cliente_id: 2,
                tipo: 'Notebook',
                marca: 'Dell',
                modelo: 'Inspiron 15',
                numero_serie: 'NB987654321',
                observacoes: 'Não liga',
                criado_em: new Date().toISOString()
            }
        ];

        // Demo service orders
        const ordens = [
            {
                id: 1,
                cliente_id: 1,
                equipamento_id: 1,
                descricao_problema: 'Tela do celular está trincada após queda',
                observacoes: 'Cliente informou que derrubou o aparelho',
                status: 'Orçamento',
                tecnico_id: 2,
                valor_orcado: 150.00,
                valor_final: null,
                data_entrada: new Date().toISOString(),
                data_saida: null,
                criado_em: new Date().toISOString()
            },
            {
                id: 2,
                cliente_id: 2,
                equipamento_id: 2,
                descricao_problema: 'Notebook não liga, LED de energia não acende',
                observacoes: 'Possível problema na fonte ou placa mãe',
                status: 'Executando',
                tecnico_id: 2,
                valor_orcado: 200.00,
                valor_final: null,
                data_entrada: new Date(Date.now() - 86400000).toISOString(), // Yesterday
                data_saida: null,
                criado_em: new Date(Date.now() - 86400000).toISOString()
            }
        ];

        // Save to localStorage
        localStorage.setItem('usuarios', JSON.stringify(usuarios));
        localStorage.setItem('clientes', JSON.stringify(clientes));
        localStorage.setItem('equipamentos', JSON.stringify(equipamentos));
        localStorage.setItem('ordens_servico', JSON.stringify(ordens));
    }
}

// Utility functions
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR');
}

function generateId() {
    return Date.now() + Math.floor(Math.random() * 1000);
}

function showAlert(title, text, icon = 'success') {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonColor: '#1e3a8a'
    });
}

function showConfirm(title, text, callback, options = {}) {
    Swal.fire({
        title: title,
        text: text,
        icon: options.icon || 'warning',
        showCancelButton: true,
        confirmButtonColor: options.confirmButtonColor || '#ef4444',
        cancelButtonColor: options.cancelButtonColor || '#6b7280',
        confirmButtonText: options.confirmButtonText || 'Sim, deletar!',
        cancelButtonText: options.cancelButtonText || 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}

function logout() {
    localStorage.removeItem('currentUser');
    location.reload();
}

function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Função para aplicar as cores customizadas em todas as páginas
function applyCustomColors(cores) {
  if (!cores) return;
  for (const key in cores) {
    document.documentElement.style.setProperty('--' + key.replace(/_/g, '-'), cores[key]);
  }
}

async function carregarCoresUsuarioGlobal() {
  if (!window.usuarioId) return;
  try {
    const resp = await fetch('api/usuarios.php?action=get_cores&usuario_id=' + window.usuarioId);
    const data = await resp.json();
    if (data.success && data.cores) {
      applyCustomColors(data.cores);
    }
  } catch (e) {
    // Silencie erros para não travar a página
  }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.app = new App();
    carregarCoresUsuarioGlobal();
});