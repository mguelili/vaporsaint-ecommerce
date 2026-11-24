<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$error = $_GET['error'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | VaporSaint.clo</title>
  <link rel="stylesheet" href="css/styles.css?v=12">
</head>
<body>
  <header>
    <a class="brand" href="index.html">VAPOR<span>SAINT</span>.CLO</a>
    <nav><a href="index.html">Home</a><a href="products.html">Shop</a><a href="register.php">Register</a><a href="cart.html">Cart</a></nav>
  </header>
  <main class="container auth-single">
    <p class="eyebrow">SECURITY AND USER ACCESS</p>
    <h1>Welcome back.</h1>
    <section class="auth-card">
      <h2>Login</h2>
      <?php if ($error !== ''): ?><p class="form-message"><?= htmlspecialchars($error) ?></p><?php endif; ?>
      <form method="post" action="auth/login.php">
        <label>Email address<input type="email" name="email" required></label>
        <label>Password<input type="password" name="password" required></label>
        <button class="btn" type="submit">Login</button>
      </form>
      <p>New customer? <a href="register.php">Create an account</a></p>
    </section>
  </main>
</body>
</html>
