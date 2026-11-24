<?php
// auth/logout.php — PHP 5.6+ compatible

ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', '1');
session_start();

$_SESSION = array();
session_destroy();

header('Location: ../login.php');
exit;
