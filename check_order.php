<?php
$host = 'localhost';
$db = 'quail_feeder';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT id, product, quantity, name FROM orders WHERE id = 10");
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Order #10 Data:\n";
    echo "================\n";
    echo "ID: " . $order['id'] . "\n";
    echo "Product: '" . $order['product'] . "'\n";
    echo "Quantity: '" . $order['quantity'] . "'\n";
    echo "Name: " . $order['name'] . "\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>