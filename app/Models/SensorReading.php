<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    use HasFactory;

    protected $table = 'sensor_readings';
    
    protected $fillable = [
        'temperature',
        'humidity',
        'recorded_at',
    ];

    protected $casts = [
        'temperature' => 'float',
        'humidity' => 'float',
        'recorded_at' => 'datetime',
    ];

    public function scopeLatestReading($query)
    {
        return $query->orderBy('recorded_at', 'desc')->limit(1);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('recorded_at', '>=', now()->subHours($hours));
    }

    public function scopeAverage($query, $minutes = 60)
    {
        return $query->where('recorded_at', '>=', now()->subMinutes($minutes))
                     ->selectRaw('AVG(temperature) as avg_temperature, AVG(humidity) as avg_humidity');
    }
}

