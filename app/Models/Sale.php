<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_type',
        'quantity',
        'price',
        'total',
        'customer_name',
        'notes'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function getProductNameAttribute()
    {
        $names = [
            'eggs' => 'Quail Eggs',
            'live_quail' => 'Live Quail',
            'dressed_quail' => 'Dressed Quail'
        ];
        
        return $names[$this->product_type] ?? $this->product_type;
    }

    public function getUnitAttribute()
    {
        return $this->product_type === 'eggs' ? 'trays' : 'pcs';
    }
}
