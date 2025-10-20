<?php
require __DIR__.'/auth.php';
require_role('student');

$u = current_user();
$today = date('Y-m-d');

// Fetch stats
$total = $mysqli->query("SELECT COUNT(*) FROM tasks WHERE user_id={$u['id']}")->fetch_row()[0];
$completed = $mysqli->query("SELECT COUNT(*) FROM tasks WHERE user_id={$u['id']} AND status='completed'")->fetch_row()[0];
$pending = $mysqli->query("SELECT COUNT(*) FROM tasks WHERE user_id={$u['id']} AND status='pending'")->fetch_row()[0];
$overdue = $mysqli->query("SELECT COUNT(*) FROM tasks WHERE user_id={$u['id']} AND status='pending' AND deadline < '$today'")->fetch_row()[0];

// Fetch upcoming deadlines (next 3)
$upcoming = $mysqli->query("
  SELECT * FROM tasks 
  WHERE user_id={$u['id']} AND status='pending' AND deadline >= '$today' 
  ORDER BY deadline ASC LIMIT 3
")->fetch_all(MYSQLI_ASSOC);

// Fetch recent tasks (last 5)
$recent = $mysqli->query("
  SELECT * FROM tasks 
  WHERE user_id={$u['id']} 
  ORDER BY created_at DESC LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard - ITP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-light bg-white border-bottom">
  <div class="container d-flex justify-content-between">
    <a class="navbar-brand" href="dashboard.php">
      <img src="logo/logo2.png" alt="ITP Logo" style="width: 50px; height: auto; object-fit: contain;">
      <span>Intelligent Task Planner</span>
    </a>
    
    <div class="d-flex gap-3 align-items-center">
      <span class="text-muted small">Hello, <?= e($u['name']) ?></span>
      <a class="btn btn-sm btn-outline-primary" href="tasks.php">My Tasks</a>
      <a class="btn btn-sm btn-outline-primary" href="schedule.php">Smart Schedule</a>
      <a href="statistics.php" class="btn btn-sm btn-outline-primary">View Task Statistics</a>
      <a class="btn btn-sm btn btn-outline-danger" href="logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">
  <h2 class="mb-4"> Dashboard</h2>
  
  <!-- Stats -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5>Total Tasks</h5>
          <p class="display-6"><?= $total ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5>Completed</h5>
          <p class="display-6 text-success"><?= $completed ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5>Pending</h5>
          <p class="display-6 text-warning"><?= $pending ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5>Overdue</h5>
          <p class="display-6 text-danger"><?= $overdue ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Upcoming deadlines -->
  <div class="card mb-4">
    <div class="card-header fw-semibold"> Upcoming Deadlines</div>
    <div class="card-body">
      <?php if (!$upcoming): ?>
        <p class="text-muted">No upcoming tasks.</p>
      <?php else: ?>
        <ul class="list-group">
          <?php foreach ($upcoming as $t): ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><?= e($t['title']) ?></span>
              <span class="badge bg-warning text-dark"><?= e($t['deadline']) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <!-- Recent tasks -->
  <div class="card">
    <div class="card-header fw-semibold">Recent Tasks</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-sm table-hover mb-0 align-middle">
          <thead><tr><th>Task No</th><th>Title</th><th>Deadline</th><th>Status</th></tr></thead>
          <tbody>
            <?php if (!$recent): ?>
              <tr><td colspan="4" class="text-center p-3 text-muted">No tasks yet.</td></tr>
            <?php else: foreach ($recent as $i=>$t): ?>
              <tr>
                <td><?= $i+1 ?></td>
                <td><?= e($t['title']) ?></td>
                <td><?= $t['deadline'] ?: '—' ?></td>
                <td>
                  <?php if ($t['status']==='completed'): ?>
                    <span class="badge bg-success">Completed</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Pending</span>
                  <?php endif; ?>
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
