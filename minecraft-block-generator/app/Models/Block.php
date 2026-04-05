<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = ['name', 'identifier', 'solid', 'destructible', 'resistance', 'texture_path'];

    protected $casts = [
        'solid'        => 'boolean',
        'destructible' => 'boolean',
        'resistance'   => 'float',
    ];
}
