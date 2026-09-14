<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['fullname', 'email', 'location', 'subject', 'message', 'gcash_proof', 'is_read', 'status'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
