<?php
// auth/login.php — PHP 5.6+ compatible

// Set cookie path BEFORE session_start so the cookie works site-wide
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', '1');
session_start();

// Load DB
try {
    require_once dirname(__FILE__) . '/../config/database.php';
} catch (Exception $e) {
    header('Location: ../login.php?error=Database+unavailable.+Try+again+later.');
    exit;
}

$email    = strtolower(trim(isset($_POST['email'])    ? $_POST['email']    : ''));
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $password === '') {
    header('Location: ../login.php?error=Please+enter+your+email+and+password.');
    exit;
}

try {
    $q = $pdo->prepare('SELECT id, password_hash, role FROM users WHERE email = ? LIMIT 1');
    $q->execute(array($email));
    $user = $q->fetch();
} catch (Exception $e) {
    header('Location: ../login.php?error=Login+error.+Please+try+again.');
    exit;
}

if (!$user || !password_verify($password, $user['password_hash'])) {
    header('Location: ../login.php?error=Invalid+email+or+password.');
    exit;
}

// Store session data
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['role']    = $user['role'];

// Force session write before redirect
session_write_close();

$dest = ($user['role'] === 'admin') ? '../admin.php' : '../account.php';
header('Location: ' . $dest);
exit;
