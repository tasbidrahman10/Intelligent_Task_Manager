<?php
require __DIR__.'/auth.php';
require_role('admin');

// Fetch all users
$users = $mysqli->query("SELECT id, role, name, email, created_at FROM users ORDER BY role DESC, created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Fetch all tasks
$tasks = $mysqli->query("
  SELECT t.*, u.name AS owner_name, u.email AS owner_email
  FROM tasks t
  JOIN users u ON u.id = t.user_id
  ORDER BY t.created_at DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - ITP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-light bg-white border-bottom">
  <div class="container d-flex justify-content-between">
    <a class="navbar-brand" href="admin.php">ITP — Admin</a>
    <a class="btn btn-sm btn-outline-secondary" href="logout.php">Logout</a>
  </div>
</nav>
<div class="container my-4">
  <?php flashes(); ?>
  <div class="row g-4">
    <div class="col-lg-5">
      <div class="card">
        <div class="card-header fw-semibold">Users</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
              <thead>
                <tr>
                  <th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $i=>$u): ?>
                <tr>
                  <td><?= $i+1 ?></td>
                  <td><?= e($u['name']) ?></td>
                  <td><?= e($u['email']) ?></td>
                  <td><span class="badge <?= $u['role']==='admin'?'bg-dark':'bg-primary' ?>"><?= e($u['role']) ?></span></td>
                  <td class="small text-muted"><?= e($u['created_at']) ?></td>
                  <td>
                    <?php if ($u['role'] !== 'admin'): // Prevent deleting admins ?>
                      <a class="btn btn-sm btn-outline-danger"
                         href="delete_user.php?id=<?= (int)$u['id'] ?>"
                         onclick="return confirm('Are you sure you want to delete this user and all their tasks?')">
                         Delete
                      </a>
                    <?php else: ?>
                      <span class="text-muted small">Protected</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; if(!$users): ?>
                <tr><td colspan="6" class="text-center p-3 text-muted">No users.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card">
        <div class="card-header fw-semibold">Recent Tasks</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
              <thead><tr>
                <th>#</th><th>Title</th><th>Owner</th><th>Deadline</th><th>Priority</th><th>Status</th><th>Created</th>
              </tr></thead>
              <tbody>
              <?php foreach ($tasks as $i=>$t): ?>
                <tr>
                  <td><?= $i+1 ?></td>
                  <td><?= e($t['title']) ?></td>
                  <td>
                    <div class="small fw-semibold"><?= e($t['owner_name']) ?></div>
                    <div class="small text-muted"><?= e($t['owner_email']) ?></div>
                  </td>
                  <td><?= $t['deadline'] ?: '—' ?></td>
                  <td><?= e($t['priority']) ?></td>
                  <td><?= e($t['status']) ?></td>
                  <td class="small text-muted"><?= e($t['created_at']) ?></td>
                </tr>
              <?php endforeach; if(!$tasks): ?>
                <tr><td colspan="7" class="text-center p-3 text-muted">No tasks yet.</td></tr>
              <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
