<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mob extends Model
{
    protected $fillable = [
        'name', 'identifier', 'creator_identifier', 'health', 'speed', 'behavior_type', 'attack_damage',
        'is_spawnable', 'is_summonable', 'collision_width', 'collision_height',
        'scale', 'model_type', 'texture_path', 'geometry_json_path',
        'spawn_egg_primary', 'spawn_egg_secondary',
    ];

    protected $casts = [
        'is_spawnable'  => 'boolean',
        'is_summonable' => 'boolean',
        'health'        => 'integer',
        'attack_damage' => 'integer',
        'speed'         => 'float',
        'collision_width'  => 'float',
        'collision_height' => 'float',
        'scale'         => 'float',
    ];
}
