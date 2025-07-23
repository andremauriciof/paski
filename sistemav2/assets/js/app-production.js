// Global Application JavaScript - Production Version
class App {
    constructor() {
        this.init();
    }

    init() {
        this.initEventListeners();
    }

    initEventListeners() {
        // Sidebar toggle
        document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
            this.toggleSidebar();
        });

        document.getElementById('mobile-sidebar-toggle')?.addEventListener('click', () => {
            this.toggleMobileSidebar();
        });

        // Window resize handler
        window.addEventListener('resize', () => {
            this.handleResize();
        });
    }

    toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content');
        
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('sidebar-collapsed');
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
}

// Utility functions
async function apiRequest(endpoint, options = {}) {
    try {
        const baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/');
        const url = baseUrl + window.API_BASE + endpoint;
        
        const response = await fetch(window.API_BASE + endpoint, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        if (!data.success && data.error) {
            throw new Error(data.error);
        }
        
        return data;
    } catch (error) {
        console.error('API Request Error:', error);
        throw error;
    }
}

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
    Swal.fire({
        title: 'Confirmar logout',
        text: 'Tem certeza que deseja sair?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sim, sair',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'login.php?logout=1';
        }
    });
}

function generateId() {
    return Date.now() + Math.random().toString(36).substr(2, 9);
}

function phoneMask(value) {
    return value.replace(/\D/g, '').replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
}

function cpfCnpjMask(value) {
    const numbers = value.replace(/\D/g, '');
    if (numbers.length <= 11) {
        return numbers.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    } else {
        return numbers.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }
}

// Definir o caminho base da API de acordo com o diretório atual
window.API_BASE = (window.location.pathname.match(/\/([^\/]+)\//) ? '/' + window.location.pathname.split('/')[1] + '/' : '/') + 'api/';

// Initialize app
document.addEventListener('DOMContentLoaded', function() {
    window.app = new App();

    // Remover todo o trecho que cria e insere o botão 'sidebar-float-toggle' no DOM e seus event listeners.
});