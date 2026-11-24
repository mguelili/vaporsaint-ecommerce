<?php
// config/database.php — InfinityFree compatible, NO strict_types

$host     = 'sql204.infinityfree.com';
$database = 'if0_43474621_vaporsaint';
$username = 'if0_43474621';
$password = 'Acepogi9104';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8",
        $username,
        $password,
        array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        )
    );
} catch (PDOException $e) {
    throw new RuntimeException('DB connection failed: ' . $e->getMessage());
}

// Seed products only once (skip silently if already seeded)
try {
    $productCount = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    if ($productCount === 0) {
        $seed = $pdo->prepare(
            'INSERT INTO products (id,name,category,sku,price,stock,reorder_level,image)
             VALUES (?,?,?,?,?,?,?,?)'
        );
        $products = array(
            array(1,'Static Saint Tee',   'Tops',        'VS-TEE-001',  1290,20,5,'images/static-bloom.jpg'),
            array(2,'Signal Hoodie',      'Tops',        'VS-HOOD-001', 2490,12,5,'images/signal-hoodie.jpg'),
            array(3,'Night Shift Shell',  'Outerwear',   'VS-SHELL-001',3290, 8,3,'images/night-shell.jpg'),
            array(4,'Saint Transit Cap',  'Accessories', 'VS-CAP-001',   890,15,5,'images/transit-cap.jpg'),
            array(5,'Saint Bomber',       'Outerwear',   'VS-BOMB-001', 3990, 7,3,'images/afterglow-bomber.jpg'),
            array(6,'Saint Cargo Pant',   'Bottoms',     'VS-CARGO-001',2190,10,4,'images/void-cargo.jpg'),
        );
        foreach ($products as $p) {
            try { $seed->execute($p); } catch (PDOException $e) { /* skip duplicates */ }
        }
    }
} catch (Exception $e) {
    // If seed fails, continue — products may already exist
}
