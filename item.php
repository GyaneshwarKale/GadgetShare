<?php
require 'db.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$itq = $pdo->prepare('SELECT items.*, users.name as owner_name, users.id as owner_id, categories.name as category_name
    FROM items
    JOIN users ON users.id = items.user_id
    LEFT JOIN categories ON categories.id = items.category_id
    WHERE items.id = ?');
$itq->execute([$id]);
$it = $itq->fetch();
if (!$it) { echo 'Item not found'; exit; }

// images
$imgs = $pdo->prepare('SELECT filename FROM item_images WHERE item_id=?');
$imgs->execute([$id]);
$imgs = $imgs->fetchAll();

// availability helper
$today = date('Y-m-d');
$qa = $pdo->prepare("SELECT * FROM bookings WHERE item_id=? AND status='accepted' AND end_date >= ? ORDER BY end_date DESC");
$qa->execute([$id, $today]);
$rows = $qa->fetchAll();
$available = true;
$available_from = null;
if ($rows) {
    $latest = null;
    foreach ($rows as $r) {
        if (!$latest || $r['end_date'] > $latest) $latest = $r['end_date'];
    }
    $available = false;
    $available_from = date('Y-m-d', strtotime($latest . ' +1 day'));
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($it['title']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* carousel image wrapper : keep full image visible without cropping */
    .carousel-img-wrap {
      height: 360px;
      display:flex;
      align-items:center;
      justify-content:center;
      background:#f5f5f5;
      overflow:hidden;
    }
    .carousel-img-wrap img {
      max-height:100%;
      max-width:100%;
      width:auto;
      height:auto;
      display:block;
    }
  </style>
</head>
<body class="p-4">
<div class="container">
  <a href="index.php">&laquo; Back</a>
  <h3><?php echo htmlspecialchars($it['title']); ?></h3>
  <div>Category: <?php echo htmlspecialchars($it['category_name']); ?></div>
  <div>Owner: <?php echo htmlspecialchars($it['owner_name']); ?></div>
  <div>Price: ₹<?php echo number_format($it['price_per_month'],2); ?> / month</div>

  <div class="mt-3 row">
    <div class="col-md-6">
      <?php if($imgs): ?>
        <div id="itemCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <?php foreach($imgs as $i => $im): ?>
              <div class="carousel-item <?php echo $i==0 ? 'active' : ''; ?>">
                <div class="carousel-img-wrap">
                  <img src="uploads/<?php echo htmlspecialchars($im['filename']); ?>" alt="item image">
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <?php if(count($imgs) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#itemCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#itemCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div style="height:360px;background:#eee;display:flex;align-items:center;justify-content:center">No Image</div>
      <?php endif; ?>
    </div>

    <div class="col-md-6">
      <h5>Specs</h5>
      <p><?php echo nl2br(htmlspecialchars($it['specs'])); ?></p>

      <?php if($available): ?>
        <span class="badge bg-success">Available</span>
      <?php else: ?>
        <span class="badge bg-danger">Not available</span>
        <div class="small text-muted">Available from: <?php echo htmlspecialchars($available_from); ?></div>
      <?php endif; ?>

      <?php if(is_logged_in()): ?>
        <?php if(current_user_id() != $it['owner_id']): ?>
          <hr>
          <h6>Request to Rent</h6>
          <form method="post" action="request_rent.php">
            <input type="hidden" name="item_id" value="<?php echo $it['id']; ?>">
            <div class="mb-2"><label>Start date</label><input class="form-control" type="date" name="start_date" required></div>
            <div class="mb-2"><label>End date</label><input class="form-control" type="date" name="end_date" required></div>
            <button class="btn btn-primary">Send Request</button>
          </form>
        <?php else: ?>
          <div class="alert alert-info">This is your item.</div>
        <?php endif; ?>
      <?php else: ?>
        <div>Please <a href="login.php">login</a> to request booking.</div>
      <?php endif; ?>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
