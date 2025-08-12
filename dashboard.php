<?php
require 'db.php';
if (!is_logged_in()) { header('Location: login.php'); exit; }
$uid = current_user_id();
$msg = '';

// Get logged-in owner's name
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$uid]);
$owner = $stmt->fetch();
$owner_name = $owner ? $owner['name'] : 'Owner';

// Handle delete item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item_id'])) {
    $item_id = intval($_POST['delete_item_id']);
    $q = $pdo->prepare('SELECT * FROM items WHERE id=? AND user_id=?');
    $q->execute([$item_id, $uid]);
    $it = $q->fetch();
    if (!$it) { $msg = 'Item not found or not owned by you.'; }
    else {
        $today = date('Y-m-d');
        $qc = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE item_id=? AND status='accepted' AND end_date >= ?");
        $qc->execute([$item_id, $today]);
        $cnt = $qc->fetchColumn();
        if ($cnt > 0) $msg = 'Cannot delete: item has accepted/current/future bookings.';
        else {
            $qimgs = $pdo->prepare('SELECT filename FROM item_images WHERE item_id=?');
            $qimgs->execute([$item_id]);
            foreach ($qimgs->fetchAll() as $r) {
                @unlink(__DIR__ . '/uploads/' . $r['filename']);
            }
            $del = $pdo->prepare('DELETE FROM items WHERE id=?');
            $del->execute([$item_id]);
            $msg = 'Item deleted.';
        }
    }
}

// Handle close booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['close_booking_id'])) {
    $bid = intval($_POST['close_booking_id']);
    $qb = $pdo->prepare('SELECT bookings.*, items.price_per_month, items.title, items.id as item_id FROM bookings JOIN items ON items.id = bookings.item_id WHERE bookings.id=? AND bookings.renter_id=? AND bookings.status="accepted"');
    $qb->execute([$bid, $uid]);
    $b = $qb->fetch();
    if (!$b) {
        $msg = 'Booking not found or cannot be closed.';
    } else {
        $today = new DateTime();
        $start = new DateTime($b['start_date']);
        $end = new DateTime($b['end_date']);
        $chargeEnd = $today;
        if ($chargeEnd < $start) $chargeEnd = $start;
        $charged_days = (int)$start->diff($chargeEnd)->format('%a') + 1;
        if ($charged_days < 1) $charged_days = 1;

        $per_day = floatval($b['price_per_month']) / 30.0;
        $base_amount = $per_day * $charged_days;

        $late_days = 0;
        if ($today > $end) {
            $late_days = (int)$end->diff($today)->format('%a');
        }
        $penalty = ($late_days > 0) ? 0.20 * $base_amount : 0.0;
        $total_amount = round($base_amount + $penalty, 2);

        $up = $pdo->prepare('UPDATE bookings SET status = "completed", actual_return_date = ? WHERE id = ?');
        $up->execute([$today->format('Y-m-d'), $bid]);

        if ($late_days > 0) {
            $msg = "Closed booking. Returned {$late_days} day(s) late. Base: ₹".number_format($base_amount,2).". Penalty (20%): ₹".number_format($penalty,2).". Total: ₹".number_format($total_amount,2)." .";
        } else {
            $msg = "Closed booking. Charge: ₹".number_format($base_amount,2).". Total: ₹".number_format($total_amount,2).".";
        }
    }
}

// Load data
$cats = $pdo->query('SELECT * FROM categories')->fetchAll();
$items = $pdo->prepare('SELECT items.*, categories.name as category_name FROM items LEFT JOIN categories ON categories.id = items.category_id WHERE items.user_id=? ORDER BY items.created_at DESC');
$items->execute([$uid]);
$items = $items->fetchAll();

$requests = $pdo->prepare('SELECT bookings.*, users.name as renter_name, items.title FROM bookings JOIN users ON users.id = bookings.renter_id JOIN items ON items.id = bookings.item_id WHERE bookings.owner_id = ? AND bookings.status = "pending" ORDER BY bookings.created_at DESC');
$requests->execute([$uid]);
$requests = $requests->fetchAll();

$rentals = $pdo->prepare('SELECT bookings.*, items.title, items.price_per_month, users.name as owner_name
    FROM bookings
    JOIN items ON items.id = bookings.item_id
    JOIN users ON users.id = bookings.owner_id
    WHERE bookings.renter_id = ? AND bookings.status = "accepted"
    ORDER BY bookings.created_at DESC');
$rentals->execute([$uid]);
$rentals = $rentals->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Dashboard</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center">
    <h3>Dashboard - Welcome, <?php echo htmlspecialchars($owner_name); ?></h3>
    <div>
      <a class="btn btn-secondary" href="index.php">Home</a>
      <a class="btn btn-danger" href="logout.php">Logout</a>
    </div>
  </div>

  <?php if($msg): ?><div class="alert alert-info"><?php echo $msg; ?></div><?php endif; ?>

  <div class="row">
    <div class="col-md-6">
      <h5>Add Item for Rent</h5>
      <form method="post" action="add_item.php" enctype="multipart/form-data">
        <div class="mb-2"><label>Title</label><input class="form-control" name="title" required></div>
        <div class="mb-2"><label>Category</label>
          <select name="category_id" class="form-control" required>
            <?php foreach($cats as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="mb-2"><label>Price per month (INR)</label><input class="form-control" name="price_per_month" type="number" step="0.01" required></div>
        <div class="mb-2"><label>Specs / Description</label><textarea name="specs" class="form-control"></textarea></div>
        <div class="mb-2"><label>Images (multiple allowed)</label><input class="form-control" type="file" name="images[]" multiple accept="image/*"></div>
        <button class="btn btn-primary">Add Item</button>
      </form>
    </div>

    <div class="col-md-6">
      <h5>Your Items</h5>
      <?php if(!$items): ?><div>No items yet.</div><?php endif; ?>
      <?php foreach($items as $it): ?>
        <div class="card mb-2">
          <div class="card-body">
            <h6><?php echo htmlspecialchars($it['title']); ?></h6>
            <div>Category: <?php echo htmlspecialchars($it['category_name']); ?></div>
            <div>Price: ₹<?php echo number_format($it['price_per_month'],2); ?></div>
            <form method="post" onsubmit="return confirm('Delete this item?');">
              <input type="hidden" name="delete_item_id" value="<?php echo $it['id']; ?>">
              <button class="btn btn-sm btn-danger mt-2">Delete Item</button>
            </form>
            <a class="btn btn-sm btn-outline-primary mt-2" href="item.php?id=<?php echo $it['id']; ?>">View</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <hr>
  <h5>Incoming Requests</h5>
  <?php if(!$requests): ?><div>No incoming requests.</div><?php endif; ?>
  <?php foreach($requests as $r): ?>
    <div class="card mb-2">
      <div class="card-body">
        <div><strong><?php echo htmlspecialchars($r['renter_name']); ?></strong> wants <strong><?php echo htmlspecialchars($r['title']); ?></strong></div>
        <div>From: <?php echo htmlspecialchars($r['start_date']); ?> To: <?php echo htmlspecialchars($r['end_date']); ?></div>
        <div class="mt-2">
          <a class="btn btn-sm btn-success" href="accept_request.php?id=<?php echo $r['id']; ?>">Accept</a>
          <a class="btn btn-sm btn-danger" href="reject_request.php?id=<?php echo $r['id']; ?>">Reject</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <hr>
  <h5>Your Rentals (Active)</h5>
  <?php if(!$rentals): ?><div>No active rentals.</div><?php endif; ?>
  <?php foreach($rentals as $rt):
      $today = new DateTime();
      $end = new DateTime($rt['end_date']);
      $start = new DateTime($rt['start_date']);
      if ($today <= $end) {
          $days_left = (int)$today->diff($end)->format('%a');
      } else {
          $late_days = (int)$end->diff($today)->format('%a');
      }
  ?>
    <div class="card mb-2">
      <div class="card-body">
        <h6><?php echo htmlspecialchars($rt['title']); ?></h6>
        <div>Owner: <?php echo htmlspecialchars($rt['owner_name']); ?></div>
        <div>From: <?php echo htmlspecialchars($rt['start_date']); ?> To: <?php echo htmlspecialchars($rt['end_date']); ?></div>
        <?php if ($today <= $end): ?>
          <div class="text-success">Days left to return: <?php echo $days_left; ?></div>
        <?php else: ?>
          <div class="text-danger">Overdue by <?php echo $late_days; ?> day(s)</div>
        <?php endif; ?>

        <form method="post" onsubmit="return confirm('Confirm you received/are returning this item? This will close the booking and calculate charges.');">
          <input type="hidden" name="close_booking_id" value="<?php echo $rt['id']; ?>">
          <button class="btn btn-sm btn-primary mt-2">Close / Return Item</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>

</div>
</body></html>
