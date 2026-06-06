<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
