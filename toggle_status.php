<?php
require __DIR__.'/auth.php';
require_role('student');
$u = current_user();

$id = (int)($_GET['id'] ?? 0);
if ($id<=0){ header('Location: tasks.php'); exit; }

$stmt = $mysqli->prepare("UPDATE tasks SET status = IF(status='pending','completed','pending') WHERE id=? AND user_id=?");
$stmt->bind_param('ii', $id, $u['id']);
$ok = $stmt->execute();

flash($ok ? 'success':'error', $ok ? 'Status updated.' : 'Unable to update status.');
header('Location: tasks.php');
