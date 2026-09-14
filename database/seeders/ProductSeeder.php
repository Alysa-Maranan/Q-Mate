<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
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
                'image_url' => 'https://media.istockphoto.com/id/1289671737/photo/young-quail-isolated-on-white-background.jpg?s=170667a&w=0&k=20&c=o2qMiqHdhcA74EuXQv1XKm1yfVnZiKzjGE6vtIx45ME=',
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

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}