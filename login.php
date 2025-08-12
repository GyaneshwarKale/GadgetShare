<?php
require 'db.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (!$email || !$pass) $err = 'Fill both fields.';
    else {
        $q = $pdo->prepare('SELECT * FROM users WHERE email=? LIMIT 1');
        $q->execute([$email]);
        $u = $q->fetch();
        if ($u && password_verify($pass, $u['password'])) {
            $_SESSION['user_id'] = $u['id'];
            header('Location: index.php');
            exit;
        } else $err = 'Invalid credentials.';
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4">
<div class="container">
  <h3>Login</h3>
  <?php if($err): ?><div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3"><label>Email</label><input class="form-control" name="email" type="email"></div>
    <div class="mb-3"><label>Password</label><input class="form-control" name="password" type="password"></div>
    <button class="btn btn-primary">Login</button>
    <a href="register.php" class="btn btn-link">Register</a>
  </form>
</div>
</body></html>
