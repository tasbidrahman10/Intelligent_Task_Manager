<?php
require __DIR__.'/config.php';
if (isset($_SESSION['user'])) {
  header('Location: ' . ($_SESSION['user']['role']==='admin' ? 'admin.php' : 'tasks.php'));
} else {
  header('Location: login.php');
}
