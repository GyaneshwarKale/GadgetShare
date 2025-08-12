<?php
require 'db.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (!$name || !$email || !$pass) $err = 'Please fill all fields.';
    else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $q = $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
        try {
            $q->execute([$name,$email,$hash]);
            header('Location: login.php');
            exit;
        } catch (Exception $e) {
            $err = 'Could not register (email may already be used).';
        }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Register</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4">
<div class="container">
  <h3>Register</h3>
  <?php if($err): ?><div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3"><label>Name</label><input class="form-control" name="name"></div>
    <div class="mb-3"><label>Email</label><input class="form-control" name="email" type="email"></div>
    <div class="mb-3"><label>Password</label><input class="form-control" name="password" type="password"></div>
    <button class="btn btn-primary">Register</button>
    <a href="login.php" class="btn btn-link">Login</a>
  </form>
</div>
</body></html>
