<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema ASL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-page d-flex align-items-center justify-content-center" style="min-height:100vh;">
        <div class="login-form-container text-center p-4 shadow rounded" style="max-width: 400px; width: 100%; background: #fff;">
            <div id="login-logo" style="margin-bottom: 24px;"></div>
            <div class="login-form">
                <h3 class="mb-4">Acesso ao Sistema</h3>
                <form id="login-form">
                    <div class="mb-3 text-start">
                        <label for="email" class="form-label">E-mail</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password" class="form-label">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                </form>
            </div>
            <!-- Rodapé -->
            <div class="text-center mt-4 mb-2 text-muted" style="font-size:0.95em;">
              Desenvolvido por André Maurício Ferreira<br>ASL Sistemas
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
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

        document.addEventListener('DOMContentLoaded', function() {
            fetch('api/api.php?action=empresa')
                .then(resp => resp.json())
                .then(data => {
                    if (data.success && data.data && data.data.logo) {
                        document.getElementById('login-logo').innerHTML = '<img src="data:image/png;base64,'+data.data.logo+'" alt="Logo" style="max-height:140px;max-width:350px;" />';
                    }
                });
        });

        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            try {
                const formData = new FormData();
                formData.append('action', 'login');
                formData.append('email', email);
                formData.append('senha', password);
                
                const response = await fetch('api/auth.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        title: 'Login realizado com sucesso!',
                        text: `Bem-vindo, ${data.user.nome}`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                } else {
                    Swal.fire({
                        title: 'Erro no login',
                        text: data.error || 'Email ou senha incorretos',
                        icon: 'error',
                        confirmButtonColor: '#1e3a8a'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Erro',
                    text: 'Erro de conexão. Tente novamente.',
                    icon: 'error',
                    confirmButtonColor: '#1e3a8a'
                });
            }
        });
    </script>
</body>
</html>