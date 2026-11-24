<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$required = ['name', 'email', 'address', 'phone', 'payment', 'items'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(422);
        echo json_encode(['error' => 'Please complete all checkout fields.']);
        exit;
    }
}

if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL) || !is_array($input['items'])) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid checkout information.']);
    exit;
}

try {
    $pdo->beginTransaction();
    $total = 0;
    foreach ($input['items'] as $item) {
        $total += ((float)$item['price']) * ((int)$item['qty']);
    }
    $total += 150;

    $order = $pdo->prepare('INSERT INTO orders (user_id, customer_name, customer_email, address, phone, payment_method, total) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $order->execute([$_SESSION['user_id'] ?? null, $input['name'], $input['email'], $input['address'], $input['phone'], $input['payment'], $total]);
    $orderId = (int)$pdo->lastInsertId();

    $itemQuery = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity) VALUES (?, ?, ?, ?, ?)');
    foreach ($input['items'] as $item) {
        $itemQuery->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['qty']]);
    }
    $pdo->commit();
    echo json_encode(['success' => true, 'order_id' => $orderId, 'total' => $total]);
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'The order could not be saved.']);
}
