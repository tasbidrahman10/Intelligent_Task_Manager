<?php
require __DIR__.'/auth.php';
require_role('student');
$u = current_user();

$id = (int)($_GET['id'] ?? 0);
if ($id<=0){ header('Location: tasks.php'); exit; }

$stmt = $mysqli->prepare("SELECT * FROM tasks WHERE id=? AND user_id=?");
$stmt->bind_param('ii', $id, $u['id']);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();
if (!$task){ flash('error','Task not found.'); header('Location: tasks.php'); exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Task - ITM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{background:#f8f9fb}</style>
</head>
<body>
<nav class="navbar navbar-light bg-white border-bottom">
  <div class="container d-flex justify-content-between">
    <a class="navbar-brand" href="tasks.php">ITM — Student</a>
    <a class="btn btn-sm btn-outline-secondary" href="logout.php">Logout</a>
  </div>
</nav>
<div class="container my-4" style="max-width: 820px;">
  <?php flashes(); ?>
  <div class="card">
    <div class="card-header fw-semibold">Edit Task</div>
    <div class="card-body">
      <form action="update_task.php" method="post" class="row g-3">
        <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
        <div class="col-md-6">
          <label class="form-label">Title</label>
          <input type="text" class="form-control" name="title" value="<?= e($task['title']) ?>" required maxlength="255">
        </div>
        <div class="col-md-6">
          <label class="form-label">Deadline</label>
          <input type="date" class="form-control" name="deadline" value="<?= e($task['deadline']) ?>">
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"><?= e($task['description']) ?></textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label">Priority</label>
          <select name="priority" class="form-select">
            <option value="High"   <?= $task['priority']==='High'?'selected':'' ?>>High</option>
            <option value="Medium" <?= $task['priority']==='Medium'?'selected':'' ?>>Medium</option>
            <option value="Low"    <?= $task['priority']==='Low'?'selected':'' ?>>Low</option>
          </select>
        </div>
        <div class="col-md-8 d-flex align-items-end justify-content-end">
          <a class="btn btn-outline-secondary me-2" href="tasks.php">Cancel</a>
          <button class="btn btn-primary">Update Task</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
