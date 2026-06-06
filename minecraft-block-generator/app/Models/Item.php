<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = [
        'name',
        'identifier',
        'creator_identifier',
        'texture_path',
        'max_stack_size',
        'max_durability',
        'item_tier',
        'item_multiplier',
        'damage',
        'hand_equipped',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_identifier', 'identifier');
    }
}
