<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['name', 'description', 'category', 'price', 'image_url', 'is_available'];

    protected $casts = ['price' => 'integer', 'is_available' => 'boolean'];
}
