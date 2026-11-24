<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

try {
    require_once __DIR__ . '/config/database.php';
} catch (Throwable $e) {
    die('Database error: ' . htmlspecialchars($e->getMessage()));
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php?error=Administrator+access+required.');
    exit;

}
$totalProducts = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$ordersToday = (int)$pdo->query('SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()')->fetchColumn();
$pendingOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status IN ('PENDING','PROCESSING')")->fetchColumn();
$salesToday = (float)$pdo->query('SELECT COALESCE(SUM(total), 0) FROM orders WHERE DATE(created_at) = CURDATE()')->fetchColumn();
$recentOrders = $pdo->query('SELECT id, customer_name, total, payment_method, status FROM orders ORDER BY id DESC LIMIT 10')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard | VaporSaint.clo</title>
  <link rel="stylesheet" href="css/styles.css?v=12">
</head>
<body class="admin-page">
  <main class="admin-shell">
    <aside class="admin-sidebar">
      <a class="brand" href="admin.php">VAPOR<span>SAINT</span><small>ADMIN</small></a>
      <a class="selected" href="admin.php">Dashboard</a>
      <a href="admin-products.php">Products</a>
      <a href="admin.php#orders">Orders</a>
      <a href="admin-customers.php">Customers</a>
      <a href="admin-inventory.php">Inventory</a>
      <a href="index.html">View store</a>
      <a href="auth/logout.php">Logout</a>
    </aside>

    <section class="admin-content">
      <p class="eyebrow">VAPORSAINT.CLO / ADMIN</p>
      <h1>Admin dashboard</h1>
      <div class="admin-stats">
        <article><span>Total products</span><strong><?= $totalProducts ?></strong></article>
        <article><span>Orders today</span><strong><?= $ordersToday ?></strong></article>
        <article><span>Pending orders</span><strong><?= $pendingOrders ?></strong></article>
        <article><span>Today's sales</span><strong>?<?= number_format($salesToday, 2) ?></strong></article>
      </div>

      <section id="orders" class="admin-panel">
        <h2>Recent orders</h2>
        <div class="admin-table">
          <div class="admin-row admin-heading"><span>Order</span><span>Customer</span><span>Total</span><span>Payment</span><span>Status</span></div>
          <?php foreach ($recentOrders as $order): ?>
            <div class="admin-row"><span>#<?= (int)$order['id'] ?></span><span><?= htmlspecialchars($order['customer_name']) ?></span><span>?<?= number_format((float)$order['total'], 2) ?></span><span><?= htmlspecialchars($order['payment_method']) ?></span><b><?= htmlspecialchars($order['status']) ?></b></div>
          <?php endforeach; ?>
          <?php if (!$recentOrders): ?><p>No orders have been placed yet.</p><?php endif; ?>
        </div>
      </section>
    </section>
  </main>
</body>
</html>
