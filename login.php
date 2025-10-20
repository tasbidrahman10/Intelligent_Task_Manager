<?php
require __DIR__.'/config.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email==='' || $password==='') {
        flash('error', 'Email and password are required.');
    } else {
        $stmt = $mysqli->prepare("SELECT id, role, name, email, password_hash FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        if ($u = $stmt->get_result()->fetch_assoc()) {
            if (password_verify($password, $u['password_hash'])) {
                $_SESSION['user'] = [
                    'id'=>$u['id'],
                    'role'=>$u['role'],
                    'name'=>$u['name'],
                    'email'=>$u['email']
                ];
                flash('success', 'Logged in successfully.');
                
                if ($u['role']==='admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            }
        }
        flash('error', 'Invalid credentials.');
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - ITP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    #loadingOverlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(255,255,255,0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      display: none;
    }
  </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="logo/logo2.png" alt="ITP Logo" style="width: 50px; height: auto; object-fit: contain;">
      <span>Intelligent Task Planner</span>
    </a>
  </div>
</nav>
<div class="container py-5" style="max-width: 460px;">
  <?php flashes(); ?>
  <div class="card">
    <div class="card-header fw-semibold">Login (Students & Admin)</div>
    <div class="card-body">
      <form method="post" novalidate id="loginForm">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-4">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" name="password" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Login</button>
      </form>
      <div class="text-center mt-3">
        New student? <a href="register.php">Create an account</a>
      </div>
    </div>
  </div>
</div>

<!-- Loading overlay -->
<div id="loadingOverlay">
  <div class="text-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
    <p class="mt-3 fw-semibold">Logging you in...</p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Stop immediate form submission
    document.getElementById('loadingOverlay').style.display = 'flex';

    // Delay submission for 1.5 seconds
    setTimeout(() => {
      this.submit();
    }, 300);
  });
</script>
</body>
</html>
