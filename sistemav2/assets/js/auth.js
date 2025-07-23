// Authentication Module
class Auth {
    static init() {
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', this.handleLogin);
        }
    }

    static handleLogin(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        // Get users from localStorage
        const usuarios = JSON.parse(localStorage.getItem('usuarios') || '[]');
        
        // Find user
        const user = usuarios.find(u => u.email === email && u.senha === password);
        
        if (user) {
            // Login successful
            localStorage.setItem('currentUser', JSON.stringify(user));
            
            // Show success message
            Swal.fire({
                title: 'Login realizado com sucesso!',
                text: `Bem-vindo, ${user.nome}`,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.app.currentUser = user;
                window.app.showMainApp();
            });
        } else {
            // Login failed
            Swal.fire({
                title: 'Erro no login',
                text: 'E-mail ou senha incorretos',
                icon: 'error',
                confirmButtonColor: '#1e3a8a'
            });
        }
    }

    static logout() {
        localStorage.removeItem('currentUser');
        window.app.currentUser = null;
        window.app.showLoginPage();
    }

    static getCurrentUser() {
        const user = localStorage.getItem('currentUser');
        return user ? JSON.parse(user) : null;
    }

    static hasPermission(requiredPermission) {
        const user = this.getCurrentUser();
        if (!user) return false;
        
        const permissions = {
            admin: ['read', 'write', 'delete', 'manage'],
            tecnico: ['read', 'write'],
            consulta: ['read']
        };
        
        return permissions[user.tipo]?.includes(requiredPermission) || false;
    }
}

// Initialize auth when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    Auth.init();
});