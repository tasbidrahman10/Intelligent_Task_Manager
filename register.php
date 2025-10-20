<?php
require __DIR__.'/config.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($name==='' || $email==='' || $password==='') {
        flash('error', 'All fields are required.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Invalid email.');
    } elseif ($password !== $confirm) {
        flash('error', 'Passwords do not match.');
    } else {
        // check duplicate email
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            flash('error', 'Email already registered.');
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'student';
            $stmt = $mysqli->prepare("INSERT INTO users (role,name,email,password_hash) VALUES (?,?,?,?)");
            $stmt->bind_param('ssss', $role, $name, $email, $hash);
            if ($stmt->execute()) {
                // auto login
                $_SESSION['user'] = ['id'=>$stmt->insert_id,'role'=>'student','name'=>$name,'email'=>$email];
                flash('success', 'Registration successful. Welcome!');
                header('Location: tasks.php');
                exit;
            } else {
                flash('error', 'Registration failed.');
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register - ITM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-light bg-white border-bottom">
  <div class="container"><a class="navbar-brand" href="index.php">Intelligent Task Manager</a></div>
</nav>
<div class="container py-5" style="max-width: 560px;">
  <?php flashes(); ?>
  <div class="card">
    <div class="card-header fw-semibold">Student Registration</div>
    <div class="card-body">
      <form method="post" novalidate>
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" class="form-control" name="name" required value="<?= e($_POST['name'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" name="password" required minlength="6">
        </div>
        <div class="mb-4">
          <label class="form-label">Confirm Password</label>
          <input type="password" class="form-control" name="confirm" required minlength="6">
        </div>
        <button class="btn btn-primary w-100">Create Account</button>
      </form>
      <div class="text-center mt-3">
        Already have an account? <a href="login.php">Login</a>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
