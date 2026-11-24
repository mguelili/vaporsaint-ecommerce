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
$products = $pdo->query('SELECT id, name, category, sku, price, stock, reorder_level FROM products ORDER BY id DESC')->fetchAll();
function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Inventory | VaporSaint.clo</title><link rel="stylesheet" href="css/styles.css?v=12"></head>
<body class="admin-page">
  <main class="admin-shell">
    <aside class="admin-sidebar">
      <a class="brand" href="admin.php">VAPOR<span>SAINT</span><small>ADMIN</small></a>
      <a href="admin.php">Dashboard</a><a href="admin-products.php">Products</a><a href="admin.php#orders">Orders</a><a href="admin-customers.php">Customers</a><a class="selected" href="admin-inventory.php">Inventory</a><a href="index.html">View store</a><a href="auth/logout.php">Logout</a>
    </aside>
    <section class="admin-content">
      <p class="eyebrow">VAPORSAINT.CLO / ADMIN</p><h1>Inventory</h1>
      <section class="admin-panel"><div class="admin-table"><div class="admin-row inventory-row admin-heading"><span>Product</span><span>Category</span><span>SKU</span><span>Price</span><span>Stock</span><span>Status</span></div>
        <?php foreach ($products as $product): $status = (int)$product['stock'] === 0 ? 'OUT OF STOCK' : ((int)$product['stock'] <= (int)$product['reorder_level'] ? 'LOW STOCK' : 'IN STOCK'); ?><div class="admin-row inventory-row"><span><?= h($product['name']) ?></span><span><?= h($product['category']) ?></span><span><?= h($product['sku']) ?></span><span>?<?= number_format((float)$product['price'], 2) ?></span><span><?= (int)$product['stock'] ?></span><b class="<?= strtolower(str_replace(' ', '-', $status)) ?>"><?= $status ?></b></div><?php endforeach; ?>
        <?php if (!$products): ?><p>No products added to inventory yet. Add products to the `products` table first.</p><?php endif; ?>
      </div></section>
    </section>
  </main>
</body>
</html>
