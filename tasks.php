<?php
require __DIR__.'/auth.php';
require_role('student');

$u = current_user();

// Fetch tasks for this student
$stmt = $mysqli->prepare("
  SELECT * FROM tasks
  WHERE user_id=?
  ORDER BY (status='completed'), deadline IS NULL, deadline ASC, FIELD(priority,'High','Medium','Low')
");
$stmt->bind_param('i', $u['id']);
$stmt->execute();
$tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$today = date('Y-m-d'); // for deadline comparison
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Your Tasks - ITP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fb; }
    .priority-High { background:#dc3545; }
    .priority-Medium { background:#ffc107; }
    .priority-Low { background:#198754; }
    .task-completed { text-decoration: line-through; color:#6c757d; }
  </style>
</head>
<body>
<nav class="navbar navbar-light bg-white border-bottom">
  <div class="container d-flex justify-content-between">
    <a class="navbar-brand" href="dashboard.php">
      <img src="logo/logo2.png" alt="ITP Logo" style="width: 50px; height: auto; object-fit: contain;">
      <span>Intelligent Task Planner</span>
    </a>
    <div class="d-flex align-items-center gap-3">
      <!-- ✅ Added Back to Dashboard button -->
      <span class="text-muted small">Hello, <?= e($u['name']) ?></span>
      <a class="btn btn-sm btn-outline-primary" href="dashboard.php">← Back to Dashboard</a>
      <a class="btn btn-sm btn-outline-primary" href="schedule.php">Smart Schedule</a>
      <a href="statistics.php" class="btn btn-sm btn-outline-primary">View Task Statistics</a>
      <a class="btn btn-sm btn btn-outline-danger" href="logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">
  <?php flashes(); ?>

  <div class="card mb-4">
    <div class="card-header fw-semibold">Add a New Task</div>
    <div class="card-body">
      <form action="create_task.php" method="post" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" required maxlength="255">
        </div>
        <div class="col-md-6">
          <label class="form-label">Deadline</label>
          <input type="date" name="deadline" class="form-control">
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label">Priority</label>
          <select name="priority" class="form-select">
            <option value="High">High</option>
            <option value="Medium" selected>Medium</option>
            <option value="Low">Low</option>
          </select>
        </div>
        <div class="col-md-8 d-flex align-items-end justify-content-end">
          <button class="btn btn-primary">Add Task</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <span>All Tasks</span><span class="small text-muted"><!--Pending first → Completed later--></span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>Task No</th>
              <th>Title</th>
              <th>Deadline</th>
              <th>Priority</th>
              <th>Status</th>
              <th style="width: 240px;">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!$tasks): ?>
            <tr><td colspan="6" class="text-center p-4 text-muted">No tasks yet.</td></tr>
          <?php else: foreach ($tasks as $i=>$t): ?>
            <?php
              // reminder badge
              $reminder = '';
              if ($t['status'] !== 'completed' && !empty($t['deadline'])) { // Check if not completed
                if ($t['deadline'] < $today) {
                  $reminder = "<span class='badge bg-danger ms-2'>Overdue</span>";
                } elseif ($t['deadline'] == $today) {
                  $reminder = "<span class='badge bg-warning text-dark ms-2'>Due Today</span>";
                }
              }
            ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td class="<?= $t['status']==='completed' ? 'task-completed':'' ?>">
                <div class="fw-semibold">
                  <?= e($t['title']) ?> <?= $reminder ?>
                </div>
                <?php if ($t['description']): ?>
                <div class="small text-muted"><?= nl2br(e($t['description'])) ?></div>
                <?php endif; ?>
              </td>
              <td><?= $t['deadline'] ? e($t['deadline']) : '<span class="text-muted">—</span>' ?></td>
              <td><span class="badge priority-<?= e($t['priority']) ?>"><?= e($t['priority']) ?></span></td>
              <td>
                <?php if ($t['status']==='pending'): ?>
                  <span class="badge bg-secondary">Pending</span>
                <?php else: ?>
                  <span class="badge bg-success">Completed</span>
                <?php endif; ?>
              </td>
              <td>
                <a class="btn btn-sm btn-outline-success" href="toggle_status.php?id=<?= (int)$t['id'] ?>">
                  <?= $t['status']==='pending' ? 'Mark Done' : 'Undo' ?>
                </a>
                <a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?= (int)$t['id'] ?>">Edit</a>
                <a class="btn btn-sm btn-outline-danger" href="delete_task.php?id=<?= (int)$t['id'] ?>" onclick="return confirm('Delete this task?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
