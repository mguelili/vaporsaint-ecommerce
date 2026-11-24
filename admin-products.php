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
$adminProducts = $pdo->query('SELECT id, name, category, price, stock FROM products ORDER BY id ASC')->fetchAll();
function adminProductText(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Product Management | VaporSaint.clo</title>
  <link rel="stylesheet" href="css/styles.css?v=12">
</head>
<body class="admin-page">
  <main class="admin-shell">
    <aside class="admin-sidebar">
      <a class="brand" href="admin.php">VAPOR<span>SAINT</span><small>ADMIN</small></a>
      <a href="admin.php">Dashboard</a>
      <a class="selected" href="admin-products.php">Products</a>
      <a href="admin.php#orders">Orders</a>
      <a href="admin-customers.php">Customers</a>
      <a href="admin-inventory.php">Inventory</a>
      <a href="index.html">View store</a>
      <a href="auth/logout.php">Logout</a>
    </aside>

    <section class="admin-content">
      <div class="admin-title-row"><div><p class="eyebrow">VAPORSAINT.CLO / ADMIN</p><h1>Product management</h1></div><button class="btn" id="add-product">+ Add product</button></div>
      <label class="admin-search">Search<input id="product-search" type="search" placeholder="Search product name..."></label>
      <section class="admin-panel"><div id="product-table" class="admin-table"></div></section>
    </section>
  </main>
  <div class="admin-panel" style="margin: 20px 0; padding: 16px;">
    <strong>Connected to MySQL inventory.</strong> Changes made in the database appear in the shop automatically.
  </div>
  <script>
    const adminProducts = <?= json_encode($adminProducts, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const table = document.getElementById('product-table');
    function renderAdminProducts(items = adminProducts) {
      table.innerHTML = `<div class="admin-row admin-heading"><span>Product</span><span>Category</span><span>Price</span><span>Stock</span><span>Status</span></div>` + items.map(p => `<div class="admin-row"><span>${p.name}</span><span>${p.category}</span><span>?${Number(p.price).toLocaleString('en-PH')}</span><span>${p.stock}</span><span>${Number(p.stock) > 0 ? 'IN STOCK' : 'OUT OF STOCK'}</span></div>`).join('');
    }
    document.getElementById('product-search').oninput = event => {
      const term = event.target.value.toLowerCase();
      renderAdminProducts(adminProducts.filter(p => p.name.toLowerCase().includes(term)));
    };
    document.getElementById('add-product').onclick = () => alert('Add the product in phpMyAdmin first. It will then appear here and in the shop.');
    renderAdminProducts();
  </script>
</body>
</html>
