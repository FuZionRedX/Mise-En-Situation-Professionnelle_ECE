<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'min:1', 'max:50', 'regex:/^[a-zA-Z0-9 ]+$/'],
            'identifier'  => ['required', 'string', 'regex:/^[a-z0-9_]+$/'],
            'texture'     => ['required', 'file', 'mimes:png', 'max:512'],
            'solid'       => ['required', 'in:0,1'],
            'destructible'=> ['required', 'in:0,1'],
            'resistance'  => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Le nom du bloc est obligatoire.',
            'name.max'             => 'Le nom ne doit pas dépasser 50 caractères.',
            'name.regex'           => 'Le nom ne peut contenir que des lettres, chiffres et espaces.',
            'identifier.required'  => "L'identifiant technique est obligatoire.",
            'identifier.regex'     => "L'identifiant ne doit contenir que des minuscules et underscores (ex: my_block).",
            'texture.required'     => 'La texture est obligatoire.',
            'texture.mimes'        => 'La texture doit être un fichier PNG.',
            'texture.max'          => 'La texture ne doit pas dépasser 512 Ko.',
            'texture.max'          => 'La texture ne doit pas dépasser 512 Ko.',
            'solid.required'       => 'La solidité est obligatoire.',
            'destructible.required'=> 'La destructibilité est obligatoire.',
            'resistance.required'  => 'La résistance est obligatoire.',
            'resistance.min'       => 'La résistance doit être entre 0 et 100.',
            'resistance.max'       => 'La résistance doit être entre 0 et 100.',
        ];
    }
}
