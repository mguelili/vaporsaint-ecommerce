<?php
// auth/register.php — PHP 5.6+ compatible

ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', '1');
session_start();

try {
    require_once dirname(__FILE__) . '/../config/database.php';
} catch (Exception $e) {
    header('Location: ../register.php?error=Database+unavailable.+Try+again+later.');
    exit;
}

$firstName = trim(isset($_POST['first_name']) ? $_POST['first_name'] : '');
$lastName  = trim(isset($_POST['last_name'])  ? $_POST['last_name']  : '');
$email     = strtolower(trim(isset($_POST['email'])    ? $_POST['email']    : ''));
$password  = isset($_POST['password']) ? $_POST['password'] : '';

if ($firstName === '' || $lastName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
    header('Location: ../register.php?error=Please+complete+all+fields.+Password+must+be+8+or+more+characters.');
    exit;
}

try {
    $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $check->execute(array($email));
    if ($check->fetch()) {
        header('Location: ../register.php?error=That+email+is+already+registered.');
        exit;
    }

    $insert = $pdo->prepare(
        'INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)'
    );
    $insert->execute(array(
        $firstName,
        $lastName,
        $email,
        password_hash($password, PASSWORD_DEFAULT),
    ));
    $newId = (int) $pdo->lastInsertId();

} catch (Exception $e) {
    header('Location: ../register.php?error=Registration+failed.+Please+try+again.');
    exit;
}

$_SESSION['user_id'] = $newId;
$_SESSION['role']    = 'customer';

session_write_close();

header('Location: ../account.php');
exit;
