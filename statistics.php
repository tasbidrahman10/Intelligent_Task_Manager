<?php
require __DIR__.'/auth.php';
require_role('student');

$u = current_user();

// Fetch daily stats: Tasks created in the last 7 days
$daily_query = "
  SELECT DATE(created_at) as date, COUNT(*) as task_count
  FROM tasks
  WHERE user_id = ?
  AND created_at >= CURDATE() - INTERVAL 7 DAY
  GROUP BY DATE(created_at)
  ORDER BY DATE(created_at) DESC
";
$stmt = $mysqli->prepare($daily_query);
$stmt->bind_param('i', $u['id']);
$stmt->execute();
$daily_tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch weekly stats: Tasks created in the last 4 weeks
$weekly_query = "
  SELECT CONCAT(YEAR(created_at), '-', LPAD(WEEK(created_at, 1), 2, '0')) as week, COUNT(*) as task_count
  FROM tasks
  WHERE user_id = ?
  AND created_at >= CURDATE() - INTERVAL 28 DAY
  GROUP BY YEAR(created_at), WEEK(created_at, 1)
  ORDER BY YEAR(created_at) DESC, WEEK(created_at, 1) DESC
";
$stmt = $mysqli->prepare($weekly_query);
$stmt->bind_param('i', $u['id']);
$stmt->execute();
$weekly_tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Task Statistics - ITP</title>
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
      <span class="text-muted small">Hello, <?= e($u['name']) ?></span>
      <a class="btn btn-sm btn-outline-primary" href="dashboard.php">← Back to Dashboard</a>
      <a href="tasks.php" class="btn btn-sm btn-outline-primary">My Tasks</a>
      <a class="btn btn-sm btn-outline-primary" href="schedule.php">Smart Schedule</a>
      <a class="btn btn-sm btn btn-outline-danger" href="logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">
  <h2>Task Statistics</h2>

  <!-- Daily Stats Chart -->
  <div class="card mb-4">
    <div class="card-header">Daily Stats (Last 7 Days)</div>
    <div class="card-body">
      <canvas id="dailyChart"></canvas>
    </div>
  </div>

  <!-- Weekly Stats Chart -->
  <div class="card mb-4">
    <div class="card-header">Weekly Stats (Last 4 Weeks)</div>
    <div class="card-body">
      <canvas id="weeklyChart"></canvas>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Daily Stats Chart
    var dailyCtx = document.getElementById('dailyChart').getContext('2d');
    var dailyData = <?php echo json_encode($daily_tasks); ?>;
    var dailyLabels = dailyData.map(function(task) { return task.date; });
    var dailyCounts = dailyData.map(function(task) { return task.task_count; });

    var dailyChart = new Chart(dailyCtx, {
      type: 'bar',
      data: {
        labels: dailyLabels,
        datasets: [{
          label: 'Tasks per Day',
          data: dailyCounts,
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          tooltip: {
            callbacks: {
              label: function(tooltipItem) {
                return 'Tasks: ' + tooltipItem.raw;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Weekly Stats Chart
    var weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    var weeklyData = <?php echo json_encode($weekly_tasks); ?>;
    var weeklyLabels = weeklyData.map(function(task) { return task.week; });
    var weeklyCounts = weeklyData.map(function(task) { return task.task_count; });

    var weeklyChart = new Chart(weeklyCtx, {
      type: 'bar',
      data: {
        labels: weeklyLabels,
        datasets: [{
          label: 'Tasks per Week',
          data: weeklyCounts,
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          tooltip: {
            callbacks: {
              label: function(tooltipItem) {
                return 'Tasks: ' + tooltipItem.raw;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
