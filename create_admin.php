<?php
require __DIR__.'/config.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name==='' || $email==='' || $password==='') {
        flash('error','All fields are required.');
    } elseif (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        flash('error','Invalid email.');
    } else {
        // If admin with email exists, update password; else create
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        if ($row) {
            $stmt = $mysqli->prepare("UPDATE users SET role='admin', name=?, password_hash=? WHERE id=?");
            $stmt->bind_param('ssi', $name, $hash, $row['id']);
            $ok = $stmt->execute();
        } else {
            $role='admin';
            $stmt = $mysqli->prepare("INSERT INTO users (role,name,email,password_hash) VALUES (?,?,?,?)");
            $stmt->bind_param('ssss',$role,$name,$email,$hash);
            $ok = $stmt->execute();
        }

        if ($ok) {
            flash('success','Admin ready. Now delete create_admin.php.');
            header('Location: login.php');
            exit;
        } else {
            flash('error','Failed to create admin.');
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Create Admin - ITP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 520px;">
  <?php flashes(); ?>
  <div class="card">
    <div class="card-header fw-semibold">One-time Admin Creator</div>
    <div class="card-body">
      <form method="post">
        <div class="mb-3"><label class="form-label">Admin Name</label><input class="form-control" name="name" required></div>
        <div class="mb-3"><label class="form-label">Admin Email</label><input class="form-control" name="email" type="email" required></div>
        <div class="mb-4"><label class="form-label">Admin Password</label><input class="form-control" name="password" type="password" required></div>
        <button class="btn btn-dark w-100">Create Admin</button>
      </form>
      <!--<div class="small text-muted mt-3">After success, please <b>delete this file</b> from the server.</div>-->
    </div>
  </div>
</div>
</body>
</html>
