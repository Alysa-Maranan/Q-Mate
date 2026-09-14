<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'customer_id',
        'product_slug',
        'rating',
        'comment',
        'admin_reply',
        'admin_reply_at',
    ];

    protected $casts = [
        'admin_reply_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
