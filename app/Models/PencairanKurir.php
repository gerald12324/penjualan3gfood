<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencairanKurir extends Model
{
    protected $table = 'pencairan_kurir';

    protected $fillable = [
        'courier_id',
        'jumlah_antaran',
        'tarif_per_antaran',
        'total_pencairan',
        'periode',
        'status',
        'catatan',
    ];

    protected $casts = [
        'jumlah_antaran' => 'integer',
        'tarif_per_antaran' => 'integer',
        'total_pencairan' => 'integer',
    ];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }
}
