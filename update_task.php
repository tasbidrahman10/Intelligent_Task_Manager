<?php
require __DIR__.'/auth.php';
require_role('student');
$u = current_user();

$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$deadline = $_POST['deadline'] ?? null;
$priority = $_POST['priority'] ?? 'Medium';

if ($id<=0 || $title===''){ flash('error','Invalid data.'); header('Location: tasks.php'); exit; }

$allowed=['Low','Medium','High'];
if (!in_array($priority,$allowed,true)) $priority='Medium';

if ($deadline !== null && $deadline !== '') {
  $d = DateTime::createFromFormat('Y-m-d', $deadline);
  if (!$d || $d->format('Y-m-d') !== $deadline) { $deadline = null; }
} else { $deadline = null; }

// update only if belongs to this user
$stmt = $mysqli->prepare("UPDATE tasks SET title=?, description=?, deadline=?, priority=? WHERE id=? AND user_id=?");
$stmt->bind_param('ssssii', $title, $description, $deadline, $priority, $id, $u['id']);
$ok = $stmt->execute();

flash($ok?'success':'error', $ok?'Task updated.':'Failed to update.');
header('Location: tasks.php');
