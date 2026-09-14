<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->get();
        return view('products.index', compact('products'));
    }

    public function updatePrice(Request $request, Product $product)
    {
        $request->validate([
            'price' => 'required|numeric|min:0'
        ]);

        $product->update([
            'price' => $request->price
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Price updated successfully',
            'new_price' => $product->formatted_price
        ]);
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $product->update([
            'stock' => $request->stock
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'new_stock' => $product->stock
        ]);
    }

    public function getProducts(Request $request)
    {
        try {
            $search = $request->get('search', '');
            
            $defaultProducts = [
                [
                    'id' => 1,
                    'name' => 'Fresh Quail Eggs',
                    'slug' => 'quail_eggs',
                    'unit' => 'tray (24 pieces)',
                    'price' => 80,
                    'stock' => 50,
                    'image_url' => 'https://www.instacart.com/company/wp-content/uploads/2023/01/quail-eggs.jpg'
                ],
                [
                    'id' => 2,
                    'name' => 'Live Quail',
                    'slug' => 'live_quail',
                    'unit' => 'piece',
                    'price' => 180,
                    'stock' => 25,
                    'image_url' => 'https://media.istockphoto.com/id/1289671737/photo/young-quail-isolated-on-white-background.jpg?s=170667a&w=0&k=20&c=o2qMiqHdhcA74EuXQv1XKm1yfVnZiKzjGE6vtIx45ME='
                ],
                [
                    'id' => 3,
                    'name' => 'Dressed Quail',
                    'slug' => 'dressed_quail',
                    'unit' => 'piece (cleaned)',
                    'price' => 250,
                    'stock' => 15,
                    'image_url' => 'http://wbldc.in/wp-content/uploads/2021/03/quail.jpg'
                ]
            ];
            
            // Filter by search query if provided
            if (!empty($search)) {
                $searchLower = strtolower($search);
                $defaultProducts = array_filter($defaultProducts, function($product) use ($searchLower) {
                    return str_contains(strtolower($product['name']), $searchLower) ||
                           str_contains(strtolower($product['slug']), $searchLower);
                });
                $defaultProducts = array_values($defaultProducts);
            }
            
            // Try to get products from database
            $products = Product::where('is_active', true)->get();
            
            if ($products->isNotEmpty()) {
                $dbProducts = $products->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'unit' => $product->unit,
                        'price' => $product->price,
                        'stock' => $product->stock,
                        'image_url' => $product->image_url
                    ];
                })->toArray();
                
                if (!empty($search)) {
                    $searchLower = strtolower($search);
                    $dbProducts = array_filter($dbProducts, function($product) use ($searchLower) {
                        return str_contains(strtolower($product['name']), $searchLower) ||
                               str_contains(strtolower($product['slug']), $searchLower);
                    });
                    $dbProducts = array_values($dbProducts);
                }
                
                return response()->json($dbProducts);
            }
            
            return response()->json($defaultProducts);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}