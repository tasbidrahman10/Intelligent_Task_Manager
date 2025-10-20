<?php
require __DIR__.'/auth.php';
require_role('student');
require __DIR__.'/schedule_engine.php';

$u = current_user();

// Parameters (optional): window and capacity
$days = max(1, min(14, (int)($_GET['days'] ?? 7)));       // 1..14
$maxPerDay = max(1, min(10, (int)($_GET['max'] ?? 4)));   // 1..10

// Fetch pending tasks for this user
$stmt = $mysqli->prepare("
  SELECT id, title, description, priority, deadline, status, created_at
  FROM tasks
  WHERE user_id=? AND status='pending'
  ORDER BY created_at DESC
");
$stmt->bind_param('i', $u['id']);
$stmt->execute();
$tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$plan = itm_build_schedule($tasks, $days, $maxPerDay);
$today = new DateTimeImmutable('today');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Smart Schedule - ITP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8f9fb; }
    .task-badge { white-space: nowrap; }
  </style>
</head>
<body>
<nav class="navbar navbar-light bg-white border-bottom">
  <div class="container d-flex justify-content-between">
    <a class="navbar-brand" href="dashboard.php">
      <img src="logo/logo2.png" alt="ITP Logo" style="width: 50px; height: auto; object-fit: contain;">
      <span>Intelligent Task Planner</span>
    </a>
    <div class="d-flex align-items-center gap-2">
      <span class="text-muted small">Hello, <?= e($u['name']) ?></span>
      <a class="btn btn-sm btn-outline-primary" href="dashboard.php">← Back to Dashboard</a>
      <a href="tasks.php" class="btn btn-sm btn-outline-primary">My Tasks</a>
      <a href="statistics.php" class="btn btn-sm btn-outline-primary">View Task Statistics</a>
      <a class="btn btn-sm btn btn-outline-danger" href="logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">
  <?php flashes(); ?>

  <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
    <h3 class="me-auto mb-0">Task Plan</h3>
    <form class="row g-2" method="get" action="schedule.php">
      <div class="col-auto">
        <label class="form-label">Max days</label>
        <input type="number" class="form-control" name="days" value="<?= (int)$days ?>" min="1" max="14">
      </div>
      <div class="col-auto">
        <label class="form-label">Max task/day</label>
        <input type="number" class="form-control" name="max" value="<?= (int)$maxPerDay ?>" min="1" max="10">
      </div>
      <div class="col-auto d-flex align-items-end">
        <button class="btn btn-primary">Get Schedule</button>
      </div>
    </form>
  </div>

  <?php if (!$tasks): ?>
    <div class="alert alert-info">No pending tasks to schedule. Add tasks first.</div>
  <?php else: ?>
    <div class="row g-3">
      <?php $i=0; foreach ($plan as $date => $items): $i++; ?>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <strong>
              <?= htmlspecialchars($date) ?>
              <?php if ($date === $today->format('Y-m-d')): ?>
                <span class="badge bg-primary ms-2">Today</span>
              <?php elseif ($date === $today->modify('+1 day')->format('Y-m-d')): ?>
                <span class="badge bg-secondary ms-2">Tomorrow</span>
              <?php endif; ?>
            </strong>
            <span class="badge bg-light text-muted"><?= count($items) ?> task(s)</span>
          </div>
          <div class="card-body p-0">
            <?php if (!$items): ?>
              <div class="p-3 text-muted small">Nothing scheduled.</div>
            <?php else: ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($items as $t): ?>
                  <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="fw-semibold"><?= e($t['title']) ?></div>
                        <?php if (!empty($t['description'])): ?>
                          <div class="small text-muted"><?= nl2br(e($t['description'])) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($t['deadline'])): ?>
                          <span class="badge bg-warning text-dark task-badge">Deadline: <?= e($t['deadline']) ?></span>
                        <?php else: ?>
                          <span class="badge bg-light text-muted task-badge">No deadline</span>
                        <?php endif; ?>
                        <span class="badge <?= $t['priority']==='High'?'bg-danger':($t['priority']==='Low'?'bg-success':'bg-warning text-dark') ?> task-badge">
                          <?= e($t['priority']) ?>
                        </span>
                      </div>
                      <div class="d-flex gap-2">
                        <a class="btn btn-sm btn-outline-success" href="toggle_status.php?id=<?= (int)$t['id'] ?>">Mark Done</a>
                        <a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?= (int)$t['id'] ?>">Edit</a>
                      </div>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
