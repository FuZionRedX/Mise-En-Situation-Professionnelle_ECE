<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    protected $fillable = ['name', 'identifier', 'creator_identifier', 'solid', 'destructible', 'resistance', 'texture_path', 'geometry', 'geometry_json_path'];

    protected $casts = [
        'solid'        => 'boolean',
        'destructible' => 'boolean',
        'resistance'   => 'float',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_identifier', 'identifier');
    }
}
