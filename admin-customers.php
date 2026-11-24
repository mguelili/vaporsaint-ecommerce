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
$customers = $pdo->query('SELECT id, first_name, last_name, email, created_at, role FROM users ORDER BY id DESC')->fetchAll();
function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Customers | VaporSaint.clo</title><link rel="stylesheet" href="css/styles.css?v=12"></head>
<body class="admin-page">
  <main class="admin-shell">
    <aside class="admin-sidebar">
      <a class="brand" href="admin.php">VAPOR<span>SAINT</span><small>ADMIN</small></a>
      <a href="admin.php">Dashboard</a><a href="admin-products.php">Products</a><a href="admin.php#orders">Orders</a><a class="selected" href="admin-customers.php">Customers</a><a href="admin-inventory.php">Inventory</a><a href="index.html">View store</a><a href="auth/logout.php">Logout</a>
    </aside>
    <section class="admin-content">
      <p class="eyebrow">VAPORSAINT.CLO / ADMIN</p><h1>Customers</h1>
      <section class="admin-panel"><div class="admin-table"><div class="admin-row admin-heading"><span>ID</span><span>Customer</span><span>Email</span><span>Role</span><span>Registered</span></div>
        <?php foreach ($customers as $customer): ?><div class="admin-row"><span>#<?= (int)$customer['id'] ?></span><span><?= h($customer['first_name'].' '.$customer['last_name']) ?></span><span><?= h($customer['email']) ?></span><span><?= h($customer['role']) ?></span><span><?= h($customer['created_at']) ?></span></div><?php endforeach; ?>
        <?php if (!$customers): ?><p>No customers registered yet.</p><?php endif; ?>
      </div></section>
    </section>
  </main>
</body>
</html>
