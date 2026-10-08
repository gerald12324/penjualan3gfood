<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'voucher';

    protected $fillable = [
        'kode_voucher',
        'jenis_potongan',
        'nilai_potongan',
        'min_belanja',
        'tanggal_berakhir',
        'kuota',
        'status',
    ];

    protected $casts = [
        'min_belanja' => 'integer',
        'nilai_potongan' => 'integer',
        'kuota' => 'integer',
    ];
}
