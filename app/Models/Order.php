<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'address',
        'product',
        'quantity',
        'notes',
        'status',
        'order_type',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_amount' => 'decimal:2'
    ];

    // Price mapping for products
    protected $productPrices = [
        'quail_eggs' => 160,
        'quail_chicks' => 150,
        'quail_meat' => 250,
        'live_quail' => 180,
        'dressed_quail' => 250,
    ];

    // Calculate total amount (avoid conflict with total_amount cast)
    public function getCalculatedTotalAttribute()
    {
        // Normalize product name to match keys (case-insensitive)
        $productKey = strtolower(str_replace(' ', '_', $this->product));

        // Also try direct match first
        $price = $this->productPrices[$this->product] ?? 0;

        // If price is 0, try normalized key
        if ($price === 0) {
            $price = $this->productPrices[$productKey] ?? 0;
        }

        // Handle quantity - extract number if stored as string like "5 trays"
        $quantity = $this->quantity;

        if (is_string($quantity)) {
            preg_match('/(\d+)/', $quantity, $matches);
            $quantity = isset($matches[1]) ? (int)$matches[1] : 1;
        }

        return $price * ($quantity ?? 0);
    }

    // Get formatted total
    public function getFormattedTotalAttribute()
    {
        return '₱' . number_format($this->calculated_total, 2);
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Generate unique order number
    public static function generateOrderNumber()
    {
        do {
            $number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    // Get formatted product name
    public function getProductNameAttribute()
    {
        $names = [
            'quail_eggs' => 'Quail Eggs',
            'quail_chicks' => 'Quail Chicks',
            'quail_meat' => 'Quail Meat',
            'live_quail' => 'Live Quail',
            'dressed_quail' => 'Dressed Quail',
            'mixed' => 'Mixed Order'
        ];

        $product = trim($this->product);

        return $names[$product] ?? $product;
    }

    // Get status badge color
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'to_ship' => 'success',
            'ready' => 'success',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // Get product image URL based on product type
    public function getProductImageAttribute()
    {
        $images = [
            'quail_eggs' => 'https://www.instacart.com/company/wp-content/uploads/2023/01/quail-eggs.jpg',

            'quail_chicks' => 'https://media.istockphoto.com/id/1289671737/photo/young-quail-isolated-on-white-background.jpg',

            'live_quail' => 'https://media.istockphoto.com/id/1289671737/photo/young-quail-isolated-on-white-background.jpg',

            'live quail' => 'https://media.istockphoto.com/id/1289671737/photo/young-quail-isolated-on-white-background.jpg',

            'dressed_quail' => 'http://wbldc.in/wp-content/uploads/2021/03/quail.jpg',

            'dressed quail' => 'http://wbldc.in/wp-content/uploads/2021/03/quail.jpg',

            'quail_meat' => 'http://wbldc.in/wp-content/uploads/2021/03/quail.jpg',

            'quail meat' => 'http://wbldc.in/wp-content/uploads/2021/03/quail.jpg',

            'Fresh Quail Eggs' => 'https://www.instacart.com/company/wp-content/uploads/2023/01/quail-eggs.jpg',

            'Live Quail' => 'https://media.istockphoto.com/id/1289671737/photo/young-quail-isolated-on-white-background.jpg',

            'Dressed Quail' => 'http://wbldc.in/wp-content/uploads/2021/03/quail.jpg',

            'Quail Chicks' => 'https://media.istockphoto.com/id/1289671737/photo/young-quail-isolated-on-white-background.jpg',
        ];

        // Trim product name
        $product = trim($this->product);

        // First try exact match
        if (isset($images[$product])) {
            return $images[$product];
        }

        // Try lowercase match
        $productLower = strtolower($product);

        if (isset($images[$productLower])) {
            return $images[$productLower];
        }

        // Try normalized key (replace spaces with underscores)
        $productNormalized = str_replace(' ', '_', $productLower);

        if (isset($images[$productNormalized])) {
            return $images[$productNormalized];
        }

        // Fallback to placeholder
        return 'https://via.placeholder.com/100?text=Quail+Product';
    }

    // Check if order can be updated
    public function canBeUpdated()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    // Mark as confirmed
    public function markAsConfirmed()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);
    }

    // Mark as completed
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Automatically create a sale record when order is completed
        $this->createSaleRecord();
    }

    // Create sale record from completed order
    private function createSaleRecord()
    {
        // Map product types to sale product types
        $productTypeMap = [
            'quail_eggs' => 'eggs',
            'live_quail' => 'live_quail',
            'dressed_quail' => 'dressed_quail',
        ];

        // Skip if product type is mixed or not mappable
        if ($this->product_type === 'mixed' || !isset($productTypeMap[$this->product_type])) {
            return;
        }

        // Extract numeric quantity from quantity string (e.g., "5 trays" -> 5)
        preg_match('/(\d+)/', $this->quantity, $matches);

        $numericQuantity = isset($matches[1]) ? (int)$matches[1] : 1;

        // Calculate price per unit
        $pricePerUnit = $this->total_amount
            ? ($this->total_amount / $numericQuantity)
            : 0;

        // Create sale record
        Sale::create([
            'product_type' => $productTypeMap[$this->product_type],
            'quantity' => $numericQuantity,
            'price' => $pricePerUnit,
            'total' => $this->total_amount ?? 0,
            'customer_name' => $this->customer_name,
            'notes' => 'From Order #' . $this->order_number,
            'created_at' => $this->completed_at ?? now(),
        ]);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}