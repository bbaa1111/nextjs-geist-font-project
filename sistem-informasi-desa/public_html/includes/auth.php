<?php
require_once 'config.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

// Check if user is admin
function isAdmin() {
    return isLoggedIn() && $_SESSION['user_role'] === 'admin';
}

// Check if user is operator or admin
function isOperator() {
    return isLoggedIn() && ($_SESSION['user_role'] === 'operator' || $_SESSION['user_role'] === 'admin');
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . 'login.php');
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . 'dashboard.php');
        exit();
    }
}

// Redirect if not operator (admin or operator)
function requireOperator() {
    requireLogin();
    if (!isOperator()) {
        header('Location: ' . SITE_URL . 'index.php');
        exit();
    }
}

// Login user
function loginUser($email, $password) {
    $pdo = getConnection();
    
    $stmt = $pdo->prepare("SELECT id, email, password, role, nama FROM admin WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_nama'] = $user['nama'];
        return true;
    }
    
    return false;
}

// Logout user
function logoutUser() {
    session_destroy();
    header('Location: ' . SITE_URL . 'login.php');
    exit();
}

// Get current user info
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role'],
        'nama' => $_SESSION['user_nama']
    ];
}
?>
