<?php
// File: includes/functions/auth.php

function handle_login($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => 'Invalid username or password.'];
    }
}

function is_logged_in() {
    return isset($_SESSION['admin_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
