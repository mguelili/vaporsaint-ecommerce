<?php
// account.php — PHP 5.6+ compatible

ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', '1');
session_start();

// Not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=Please+log+in+first.');
    exit;
}

// Load DB
try {
    require_once dirname(__FILE__) . '/config/database.php';
} catch (Exception $e) {
    header('Location: login.php?error=Database+unavailable.+Please+try+again+later.');
    exit;
}

// Fetch user
try {
    $query = $pdo->prepare('SELECT first_name, last_name, email, created_at FROM users WHERE id = ? LIMIT 1');
    $query->execute(array((int) $_SESSION['user_id']));
    $user = $query->fetch();
} catch (Exception $e) {
    $user = null;
}

if (!$user) {
    $_SESSION = array();
    session_destroy();
    header('Location: login.php?error=Session+expired.+Please+log+in+again.');
    exit;
}

$isAdmin  = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$fullName = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
$email    = htmlspecialchars($user['email']);
$since    = htmlspecialchars(date('F j, Y', strtotime($user['created_at'])));
$initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Account | VaporSaint.clo</title>
  <link rel="stylesheet" href="css/styles.css?v=12">
  <style>
    /* ── Account page styles ── */
    .account-wrap { max-width: 960px; margin: 0 auto; padding: 30px 20px; }
    .account-header-bar { display: flex; align-items: center; gap: 18px; margin-bottom: 32px; flex-wrap: wrap; }
    .avatar-lg {
      width: 72px; height: 72px; border-radius: 50%;
      background: var(--lime); color: #111;
      display: flex; align-items: center; justify-content: center;
      font-size: 26px; font-weight: 900; flex-shrink: 0;
    }
    .account-name { font-size: 22px; font-weight: 800; color: #fff; }
    .account-email { font-size: 13px; color: #888; margin-top: 3px; }
    .account-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .account-card {
      background: #151515; border: 1px solid #2a2a2a;
      border-radius: 10px; padding: 24px;
    }
    .account-card h3 { font-size: 13px; text-transform: uppercase; letter-spacing: .1em; color: var(--lime); margin: 0 0 18px; }
    .info-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #222; font-size: 14px; }
    .info-row:last-child { border-bottom: none; }
    .info-row span { color: #777; font-size: 12px; text-transform: uppercase; letter-spacing: .06em; }
    .info-row b { color: #fff; }
    .order-empty { text-align: center; padding: 40px 20px; color: #555; }
    .order-empty p { margin: 10px 0 0; font-size: 14px; }
    .nav-links { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 28px; }
    .nav-link-btn {
      padding: 11px 20px; border-radius: 5px; font-size: 12px;
      text-transform: uppercase; letter-spacing: .08em; font-weight: bold;
      border: 1px solid #333; color: #ccc; background: transparent; cursor: pointer;
      text-decoration: none; display: inline-block;
    }
    .nav-link-btn:hover { border-color: var(--lime); color: var(--lime); }
    .nav-link-btn.danger { border-color: #c33; color: #c33; }
    .nav-link-btn.danger:hover { background: #2a0000; }
    @media(max-width: 640px) {
      .account-cards { grid-template-columns: 1fr; }
      .account-name { font-size: 18px; }
      .nav-links { flex-direction: column; }
      .nav-link-btn { text-align: center; }
    }
  </style>
</head>
<body>
<header>
  <a class="brand" href="index.html">VAPOR<span>SAINT</span>.CLO</a>
  <button class="menu-btn">☰</button>
  <nav>
    <a href="index.html">Home</a>
    <a href="products.html">Shop</a>
    <a href="cart.html">Cart <b class="cart-count">0</b></a>
    <?php if ($isAdmin): ?><a href="admin.php">Admin</a><?php endif; ?>
    <a href="auth/logout.php">Logout</a>
  </nav>
</header>

<main>
  <div class="account-wrap">

    <!-- User header -->
    <div class="account-header-bar">
      <div class="avatar-lg"><?= $initials ?></div>
      <div>
        <div class="account-name"><?= $fullName ?></div>
        <div class="account-email"><?= $email ?></div>
      </div>
    </div>

    <!-- Info cards -->
    <div class="account-cards">

      <!-- Profile card -->
      <div class="account-card">
        <h3>Profile</h3>
        <div class="info-row"><span>Name</span><b><?= $fullName ?></b></div>
        <div class="info-row"><span>Email</span><b><?= $email ?></b></div>
        <div class="info-row"><span>Member since</span><b><?= $since ?></b></div>
        <div class="info-row"><span>Role</span><b><?= $isAdmin ? 'Admin' : 'Customer' ?></b></div>
      </div>

      <!-- Orders card -->
      <div class="account-card">
        <h3>Recent Orders</h3>
        <div class="order-empty">
          <div style="font-size:36px">🛍️</div>
          <p>No orders yet.</p>
          <a href="products.html" class="btn" style="margin-top:16px;display:inline-block;padding:12px 22px;font-size:12px;">Shop Now</a>
        </div>
      </div>

    </div><!-- /account-cards -->

    <!-- Nav buttons -->
    <div class="nav-links">
      <a href="products.html" class="nav-link-btn">🛍 Shop</a>
      <a href="cart.html" class="nav-link-btn">🛒 Cart</a>
      <?php if ($isAdmin): ?>
      <a href="admin.php" class="nav-link-btn">⚙️ Admin Dashboard</a>
      <?php endif; ?>
      <a href="auth/logout.php" class="nav-link-btn danger">↩ Logout</a>
    </div>

  </div>
</main>

<footer>VAPORSAINT.CLO <span>Streetwear for the uncommon.</span></footer>
<script src="js/app.js"></script>
<script>updateCount();</script>
</body>
</html>
