<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierPayout extends Model
{
    protected $fillable = ['courier_id', 'amount', 'period', 'status', 'notes'];

    protected $casts = ['amount' => 'integer'];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }
}
