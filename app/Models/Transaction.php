<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
         'tag',
         'stage_id',
         'order_status',
         'production_status',
         'date_produced',
         'is_produced',

        ];

    protected $casts = [
        'is_produced' => 'boolean',
    ];

}
