<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EggCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'cage_pen',
        'total_eggs',
        'good_eggs',
        'cracked_eggs',
        'collection_time',
        'notes',
        'egg_size',
        'feed_brand'
    ];

    protected $casts = [
        'collection_time' => 'datetime:H:i',
    ];

    public function getQualityRateAttribute()
    {
        if ($this->total_eggs == 0) return 0;
        return round(($this->good_eggs / $this->total_eggs) * 100, 1);
    }
}
