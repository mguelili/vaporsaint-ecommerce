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
  <title>Register | VaporSaint.clo</title>
  <link rel="stylesheet" href="css/styles.css?v=12">
</head>
<body>
  <header>
    <a class="brand" href="index.html">VAPOR<span>SAINT</span>.CLO</a>
    <nav><a href="index.html">Home</a><a href="products.html">Shop</a><a href="login.php">Login</a><a href="cart.html">Cart</a></nav>
  </header>
  <main class="container auth-single">
    <p class="eyebrow">CREATE ACCOUNT</p>
    <h1>Join VaporSaint.</h1>
    <section class="auth-card">
      <h2>Register</h2>
      <?php if ($error !== ''): ?><p class="form-message"><?= htmlspecialchars($error) ?></p><?php endif; ?>
      <form method="post" action="auth/register.php">
        <div class="form-grid two-columns"><label>First name<input name="first_name" required></label><label>Last name<input name="last_name" required></label></div>
        <label>Email<input type="email" name="email" required></label>
        <label>Password<input type="password" name="password" minlength="8" required></label>
        <button class="btn" type="submit">Register</button>
      </form>
      <p>Already registered? <a href="login.php">Login</a></p>
    </section>
  </main>
</body>
</html>
