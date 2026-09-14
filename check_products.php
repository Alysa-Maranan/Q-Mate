<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "Checking products in database...\n";
$products = Product::all();
echo "Total products: " . $products->count() . "\n";

foreach ($products as $product) {
    echo "- {$product->name}: ₱{$product->price} (Stock: {$product->stock})\n";
}

if ($products->count() === 0) {
    echo "No products found. Creating default products...\n";
    
    $defaultProducts = [
        [
            'name' => 'Fresh Quail Eggs',
            'slug' => 'quail_eggs',
            'description' => 'Fresh quail eggs from our farm',
            'price' => 80.00,
            'stock' => 50,
            'unit' => 'tray (24 pieces)',
            'image_url' => 'https://www.instacart.com/company/wp-content/uploads/2023/01/quail-eggs.jpg',
            'is_active' => true
        ],
        [
            'name' => 'Live Quail',
            'slug' => 'live_quail',
            'description' => 'Healthy live quail for breeding or raising',
            'price' => 180.00,
            'stock' => 25,
            'unit' => 'piece',
            'image_url' => 'https://media.istockphoto.com/id/1289671737/photo/young-quail-isolated-on-white-background.jpg',
            'is_active' => true
        ],
        [
            'name' => 'Dressed Quail',
            'slug' => 'dressed_quail',
            'description' => 'Cleaned and dressed quail ready for cooking',
            'price' => 250.00,
            'stock' => 15,
            'unit' => 'piece (cleaned)',
            'image_url' => 'http://wbldc.in/wp-content/uploads/2021/03/quail.jpg',
            'is_active' => true
        ]
    ];
    
    foreach ($defaultProducts as $productData) {
        try {
            Product::create($productData);
            echo "Created: {$productData['name']}\n";
        } catch (Exception $e) {
            echo "Error creating {$productData['name']}: " . $e->getMessage() . "\n";
        }
    }
}

echo "Done!\n";