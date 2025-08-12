<?php
require 'db.php';
if (!is_logged_in()) { header('Location: login.php'); exit; }
$uid = current_user_id();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: dashboard.php'); exit; }

$title = trim($_POST['title'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);
$price = floatval($_POST['price_per_month'] ?? 0);
$specs = trim($_POST['specs'] ?? '');

if (!$title || !$category_id || !$price) {
    $_SESSION['msg'] = 'Missing fields';
    header('Location: dashboard.php');
    exit;
}

// insert item
$q = $pdo->prepare('INSERT INTO items (user_id, category_id, title, specs, price_per_month) VALUES (?,?,?,?,?)');
$q->execute([$uid, $category_id, $title, $specs, $price]);
$item_id = $pdo->lastInsertId();

// handle multiple images
$uploaddir = __DIR__ . '/uploads/';
if (!is_dir($uploaddir)) mkdir($uploaddir, 0755, true);
if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
    for ($i=0;$i<count($_FILES['images']['name']);$i++) {
        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
        $tmp = $_FILES['images']['tmp_name'][$i];
        $orig = basename($_FILES['images']['name'][$i]);
        $ext = pathinfo($orig, PATHINFO_EXTENSION);
        $newname = 'item_'.$item_id.'_'.time().'_'.$i.'.'.($ext?:'jpg');
        $dest = $uploaddir . $newname;
        if (move_uploaded_file($tmp, $dest)) {
            $qi = $pdo->prepare('INSERT INTO item_images (item_id, filename) VALUES (?,?)');
            $qi->execute([$item_id, $newname]);
        }
    }
}
header('Location: dashboard.php');
exit;
?>
