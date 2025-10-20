<?php
// auth.php
require_once __DIR__.'/config.php';

function is_logged_in() {
    return isset($_SESSION['user']);
}
function current_user() {
    return $_SESSION['user'] ?? null;
}
function require_login() {
    if (!is_logged_in()) {
        flash('error', 'Please log in first.');
        header('Location: login.php');
        exit;
    }
}
function require_role($role) {
    require_login();
    $u = current_user();
    if (!$u || $u['role'] !== $role) {
        flash('error', 'Unauthorized.');
        // send students to tasks, admins to admin
        header('Location: ' . ($u && $u['role']==='admin' ? 'admin.php' : 'tasks.php'));
        exit;
    }
}
