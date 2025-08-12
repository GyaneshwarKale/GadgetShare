<?php
require 'db.php';

$today = date('Y-m-d');

// If logged in, exclude own items
if (is_logged_in()) {
    $uid = current_user_id();
    $stmt = $pdo->prepare("SELECT items.*, users.name AS owner_name, categories.name AS category_name
        FROM items
        JOIN users ON users.id = items.user_id
        LEFT JOIN categories ON categories.id = items.category_id
        WHERE items.user_id != ?
        ORDER BY items.created_at DESC");
    $stmt->execute([$uid]);
} else {
    $stmt = $pdo->query("SELECT items.*, users.name AS owner_name, categories.name AS category_name
        FROM items
        JOIN users ON users.id = items.user_id
        LEFT JOIN categories ON categories.id = items.category_id
        ORDER BY items.created_at DESC");
}
$items = $stmt->fetchAll();

function availability($pdo, $item_id) {
    $today = date('Y-m-d');
    $q = $pdo->prepare("SELECT * FROM bookings WHERE item_id=? AND status='accepted' AND end_date >= ? ORDER BY end_date DESC");
    $q->execute([$item_id, $today]);
    $rows = $q->fetchAll();
    if (!$rows) return ['available'=>true];
    $latest = null;
    foreach ($rows as $r) {
        if (!$latest || $r['end_date'] > $latest) $latest = $r['end_date'];
    }
    return ['available'=>false, 'available_from'=>date('Y-m-d', strtotime($latest . ' +1 day'))];
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>GadgetShare - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Card image container: fixed height, center image without cropping */
    .card-img-container {
      height: 200px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f5f5f5;
      overflow: hidden;
    }
    .card-img-container img {
      max-height: 100%;
      max-width: 100%;
      width: auto;
      height: auto;
      display: block;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="index.php">GadgetShare</a>
    <div>
      <?php if(is_logged_in()): ?>
        <a class="btn btn-sm btn-outline-primary" href="dashboard.php">Dashboard</a>
        <a class="btn btn-sm btn-outline-secondary" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-sm btn-outline-primary" href="login.php">Login</a>
        <a class="btn btn-sm btn-outline-success" href="register.php">Sign Up</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="row"><div class="col-12"><h3>All items</h3></div></div>
  <div class="row">
    <?php foreach($items as $it):
        $av = availability($pdo, $it['id']);
    ?>
    <div class="col-md-4">
      <div class="card mb-3">
        <?php
          $q = $pdo->prepare("SELECT filename FROM item_images WHERE item_id=? LIMIT 1");
          $q->execute([$it['id']]);
          $img = $q->fetchColumn();
        ?>
        <?php if($img): ?>
          <div class="card-img-container">
            <img src="uploads/<?php echo htmlspecialchars($img); ?>" alt="item image">
          </div>
        <?php else: ?>
          <div class="card-img-container">No Image</div>
        <?php endif; ?>

        <div class="card-body">
          <h5 class="card-title"><?php echo htmlspecialchars($it['title']); ?></h5>
          <p class="card-text">Category: <?php echo htmlspecialchars($it['category_name']?:''); ?></p>
          <p class="card-text">Price: ₹<?php echo number_format($it['price_per_month'],2); ?> / month</p>
          <p class="card-text small">Owner: <?php echo htmlspecialchars($it['owner_name']); ?></p>
          <?php if($av['available']): ?>
            <span class="badge bg-success">Available</span>
          <?php else: ?>
            <span class="badge bg-danger">Not available</span>
            <div class="small text-muted">Available from: <?php echo htmlspecialchars($av['available_from']); ?></div>
          <?php endif; ?>
          <a href="item.php?id=<?php echo $it['id']; ?>" class="stretched-link"></a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
