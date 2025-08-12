<?php
require 'db.php';
if (!is_logged_in()) { header('Location: login.php'); exit; }
$uid = current_user_id();
$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: dashboard.php'); exit; }
// fetch booking
$q = $pdo->prepare('SELECT * FROM bookings WHERE id=? AND owner_id=? AND status="pending"');
$q->execute([$id, $uid]);
$b = $q->fetch();
if (!$b) { $_SESSION['msg']='Request not found or not allowed.'; header('Location: dashboard.php'); exit; }
// check for conflicts with existing accepted bookings
$conf = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE item_id=? AND status='accepted' AND NOT (end_date < ? OR start_date > ?)");
$conf->execute([$b['item_id'], $b['start_date'], $b['end_date']]);
if ($conf->fetchColumn() > 0) {
    $_SESSION['msg'] = 'Cannot accept: dates conflict with existing accepted booking.';
    header('Location: dashboard.php'); exit;
}
// accept
$up = $pdo->prepare('UPDATE bookings SET status="accepted" WHERE id=?');
$up->execute([$id]);
$_SESSION['msg']='Request accepted.';
header('Location: dashboard.php');
exit;
?>
