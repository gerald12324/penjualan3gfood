<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code', 'customer_name', 'phone', 'address',
        'payment_method', 'total', 'status', 'courier_id',
        'accepted_at', 'cooking_at', 'cooked_at', 'delivering_at', 'completed_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'cooking_at' => 'datetime',
        'cooked_at' => 'datetime',
        'delivering_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function whatsappLogs()
    {
        return $this->hasMany(WhatsappLog::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function latestWhatsappLog()
    {
        return $this->hasOne(WhatsappLog::class)->latestOfMany();
    }
}