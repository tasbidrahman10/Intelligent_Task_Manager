<?php
require __DIR__.'/auth.php';
require_role('student');
$u = current_user();

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$deadline = $_POST['deadline'] ?? null;
$priority = $_POST['priority'] ?? 'Medium';

if ($title==='') {
  flash('error','Title is required.');
  header('Location: tasks.php'); exit;
}

$allowed = ['Low','Medium','High'];
if (!in_array($priority,$allowed,true)) $priority='Medium';

if ($deadline !== null && $deadline !== '') {
  $d = DateTime::createFromFormat('Y-m-d', $deadline);
  if (!$d || $d->format('Y-m-d') !== $deadline) { $deadline = null; }
} else { $deadline = null; }

$stmt = $mysqli->prepare("INSERT INTO tasks (user_id, title, description, deadline, priority) VALUES (?,?,?,?,?)");
$stmt->bind_param('issss', $u['id'], $title, $description, $deadline, $priority);
$ok = $stmt->execute();

flash($ok ? 'success':'error', $ok ? 'Task added.':'Failed to add task.');
header('Location: tasks.php');
