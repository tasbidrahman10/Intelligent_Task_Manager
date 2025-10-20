<?php
// config.php
session_start();
date_default_timezone_set('Asia/Dhaka');

// XAMPP defaults
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'itm_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('Database Connection Failed: ' . $mysqli->connect_error);
}

// Escape HTML output
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

// Flash messages
function flash($type, $msg) { $_SESSION['flash'][] = ['type'=>$type,'msg'=>$msg]; }
function flashes() {
    if (!empty($_SESSION['flash'])) {
        foreach ($_SESSION['flash'] as $f) {
            $cls = $f['type']==='success' ? 'alert-success' : 'alert-danger';
            echo '<div class="alert '.$cls.' alert-dismissible fade show" role="alert">'
               . e($f['msg'])
               . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
        unset($_SESSION['flash']);
    }
}
