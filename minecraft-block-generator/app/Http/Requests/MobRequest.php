<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'min:1', 'max:50'],
            'identifier'        => ['required', 'regex:/^[a-z0-9_]+$/', 'unique:mobs,identifier'],
            'texture'           => ['required', 'file', 'mimes:png', 'max:512'],
            'model_type'        => ['required', 'in:humanoid,quadruped,creeper'],
            'health'            => ['required', 'integer', 'min:1', 'max:2048'],
            'speed'             => ['required', 'numeric', 'min:0.1', 'max:2.0'],
            'behavior_type'     => ['required', 'in:passive,neutral,hostile'],
            'attack_damage'     => ['nullable', 'integer', 'min:1', 'max:50'],
            'is_spawnable'      => ['required', 'in:0,1'],
            'is_summonable'     => ['required', 'in:0,1'],
            'collision_width'   => ['required', 'numeric', 'min:0.1', 'max:4.0'],
            'collision_height'  => ['required', 'numeric', 'min:0.1', 'max:4.0'],
            'scale'             => ['required', 'numeric', 'min:0.1', 'max:4.0'],
            'spawn_egg_primary'  => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'spawn_egg_secondary'=> ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'geometry_file'      => ['nullable', 'file', 'mimes:json,txt', 'max:512'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.regex'  => 'L\'identifiant ne peut contenir que des minuscules, chiffres et underscores.',
            'identifier.unique' => 'Cet identifiant est déjà utilisé.',
            'texture.mimes'     => 'La texture doit être un fichier PNG.',
            'texture.max'       => 'La texture ne doit pas dépasser 512 Ko.',
        ];
    }
}
