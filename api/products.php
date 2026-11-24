<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $query = $pdo->query(
        'SELECT id, name, category, price, image
         FROM products
         WHERE stock > 0
         ORDER BY id ASC'
    );
    echo json_encode($query->fetchAll(), JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to load products from the database.']);
}
