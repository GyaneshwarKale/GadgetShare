<?php
require 'db.php';
if (!is_logged_in()) { header('Location: login.php'); exit; }
$uid = current_user_id();
$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: dashboard.php'); exit; }
$q = $pdo->prepare('SELECT * FROM bookings WHERE id=? AND owner_id=? AND status="pending"');
$q->execute([$id, $uid]);
$b = $q->fetch();
if (!$b) { $_SESSION['msg']='Request not found or not allowed.'; header('Location: dashboard.php'); exit; }
$up = $pdo->prepare('UPDATE bookings SET status="rejected" WHERE id=?');
$up->execute([$id]);
$_SESSION['msg']='Request rejected.';
header('Location: dashboard.php');
exit;
?>
