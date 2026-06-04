<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = ['name', 'identifier', 'creator_identifier', 'solid', 'destructible', 'resistance', 'texture_path', 'geometry', 'geometry_json_path'];

    protected $casts = [
        'solid'        => 'boolean',
        'destructible' => 'boolean',
        'resistance'   => 'float',
    ];
}
