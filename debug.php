<?php
// TEMPORARY DEBUG FILE — DELETE AFTER FIXING
// Visit: vaporsaintclo.site.je/debug.php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/plain; charset=utf-8');

echo "=== VaporSaint Debug ===\n\n";

// 1. PHP Version
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP Major: " . PHP_MAJOR_VERSION . "\n\n";

// 2. Session support
echo "Session support: " . (function_exists('session_start') ? 'YES' : 'NO') . "\n";

// 3. PDO support
echo "PDO loaded: " . (extension_loaded('pdo') ? 'YES' : 'NO') . "\n";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? 'YES' : 'NO') . "\n\n";

// 4. DB connection test
$host = 'sql204.infinityfree.com';
$db   = 'if0_43474621_vaporsaint';
$user = 'if0_43474621';
$pass = 'Acepogi9104';

echo "Connecting to DB...\n";
try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8",
        $user,
        $pass,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    echo "DB Connection: SUCCESS\n\n";

    // 5. Check tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . implode(', ', $tables) . "\n\n";

    // 6. Check users table structure
    if (in_array('users', $tables)) {
        $cols = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
        echo "users table columns:\n";
        foreach ($cols as $c) {
            echo "  - " . $c['Field'] . " (" . $c['Type'] . ")\n";
        }
        $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "users row count: " . $count . "\n\n";
    }

    // 7. Check products
    if (in_array('products', $tables)) {
        $count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
        echo "products row count: " . $count . "\n";
    }

} catch (Exception $e) {
    echo "DB Connection FAILED: " . $e->getMessage() . "\n";
}

// 8. Session test
echo "\n=== Session Test ===\n";
session_start();
$_SESSION['test_key'] = 'hello123';
echo "Session ID: " . session_id() . "\n";
echo "Session test_key: " . ($_SESSION['test_key'] ?? 'NOT SET') . "\n";
echo "Session save path: " . session_save_path() . "\n";

echo "\n=== Done ===\n";
echo "DELETE THIS FILE AFTER DEBUGGING\n";
