<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function hasPermission($requiredPermission) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userType = $_SESSION['user_type'];
    
    $permissions = [
        'admin' => ['read', 'write', 'delete', 'manage', 'financeiro'],
        'tecnico' => ['read', 'write'], // técnico NÃO tem acesso ao financeiro
        'consulta' => ['read'] // consulta só pode visualizar
    ];
    
    return in_array($requiredPermission, $permissions[$userType] ?? []);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'nome' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'tipo' => $_SESSION['user_type']
    ];
}

function logout() {
    session_destroy();
    header('Location: login.php');
    exit;
}

function isAdmin() {
    return isLoggedIn() && ($_SESSION['user_type'] === 'admin');
}
?>