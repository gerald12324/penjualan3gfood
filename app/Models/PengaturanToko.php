<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanToko extends Model
{
    protected $table = 'pengaturan_toko';

    protected $fillable = [
        'status_toko',
        'jam_buka',
        'jam_tutup',
    ];
}
