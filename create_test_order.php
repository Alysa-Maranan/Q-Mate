<?php
/**
 * Create test order for notification system testing
 */
$pdo = new PDO('mysql:host=127.0.0.1;dbname=quail_feeder;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// Use existing customer (Juan Dela Cruz, ID: 3)
$customerId = 3;
$testOrderId = $customerId . '_' . time();  // unique notes identifier

$stmt = $pdo->prepare("
    INSERT INTO orders 
    (customer_id, name, email, phone, address, product, quantity, notes, status, created_at, updated_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
");

$stmt->execute([
    $customerId,                        // customer_id (must exist in customers table)
    'Test Notif User ' . rand(1000, 9999),  // name
    'notif@test.com',                   // email
    '09123456789',                      // phone
    'Test Address, Metro Manila',       // address
    'quail_eggs',                       // product
    12,                                 // quantity
    'NOTIF_TEST_' . $testOrderId,       // notes
    'pending',                          // status
]);

$orderId = $pdo->lastInsertId();

echo "TEST ORDER CREATED\n";
echo "==================\n";
echo "Order ID:    $orderId\n";
echo "Customer ID: $customerId (Juan Dela Cruz)\n";
echo "Product:     quail_eggs (x12)\n";
echo "Status:      pending\n";
echo "\n";
echo "NEXT STEPS:\n";
echo "1. Open Dashboard in browser (logged in as admin)\n";
echo "2. Wait ~10 seconds for notification polling\n";
echo "3. You should see notification\n";
echo "4. Sound should play + popup should appear\n";
echo "\n";
echo "To clean up later, run:\n";
echo "  DELETE FROM orders WHERE id = $orderId\n";
