<?php
require __DIR__.'/auth.php';
require_role('student');
$u = current_user();

$id = (int)($_GET['id'] ?? 0);
if ($id<=0){ header('Location: tasks.php'); exit; }

$stmt = $mysqli->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
$stmt->bind_param('ii', $id, $u['id']);
$ok = $stmt->execute();

flash($ok ? 'success':'error', $ok?'Task deleted.':'Unable to delete.');
header('Location: tasks.php');
