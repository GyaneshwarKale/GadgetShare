<?php
require 'db.php';
if (!is_logged_in()) { header('Location: login.php'); exit; }
$uid = current_user_id();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
$item_id = intval($_POST['item_id'] ?? 0);
$start = $_POST['start_date'] ?? '';
$end = $_POST['end_date'] ?? '';
if (!$item_id || !$start || !$end) { $_SESSION['msg']='Invalid data'; header('Location: item.php?id='.$item_id); exit; }
if ($start > $end) { $_SESSION['msg']='Start date must be <= end date'; header('Location: item.php?id='.$item_id); exit; }
// get owner id
$qi = $pdo->prepare('SELECT user_id FROM items WHERE id=?');
$qi->execute([$item_id]);
$owner = $qi->fetchColumn();
if (!$owner) { $_SESSION['msg']='Item not found'; header('Location: index.php'); exit; }
if ($owner == $uid) { $_SESSION['msg']='Cannot rent your own item'; header('Location: item.php?id='.$item_id); exit; }
// insert booking as pending
$q = $pdo->prepare('INSERT INTO bookings (item_id, renter_id, owner_id, start_date, end_date, status) VALUES (?,?,?,?,?,"pending")');
$q->execute([$item_id, $uid, $owner, $start, $end]);
$_SESSION['msg']='Request sent to owner.';
header('Location: item.php?id='.$item_id);
exit;
?>
