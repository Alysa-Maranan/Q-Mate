<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotificationAck extends Model
{
    protected $table = 'admin_notification_acks';

    protected $fillable = [
        'user_id',
        'ack_key',
    ];
}