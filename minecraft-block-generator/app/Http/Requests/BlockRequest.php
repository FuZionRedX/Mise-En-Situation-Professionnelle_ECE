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
        $isUpdate = $this->route() && $this->route()->getName() === 'block.update';

        return [
            'name'        => ['required', 'string', 'min:1', 'max:50', 'regex:/^[a-zA-Z0-9 ]+$/'],
            'identifier'  => ['required', 'string', 'regex:/^[a-z0-9_]+$/', $isUpdate ? 'unique:blocks,identifier,' . $this->route('block')->id : 'unique:blocks'],
            'texture'     => [$isUpdate ? 'nullable' : 'required', 'file', 'mimes:png', 'max:512'],
            'geometry_file'  => ['nullable', 'file', 'mimes:json', 'max:256'],
            'light_emission' => ['required', 'integer', 'min:0', 'max:15'],
            'solid'          => ['required', 'in:0,1'],
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
            'identifier.unique'    => "Cet identifiant existe déjà. Veuillez en choisir un autre.",
            'texture.required'     => 'La texture est obligatoire pour créer un nouveau bloc.',
            'texture.mimes'        => 'La texture doit être un fichier PNG.',
            'texture.max'          => 'La texture ne doit pas dépasser 512 Ko.',
            'texture.ratio'        => 'La texture doit avoir des dimensions carrées (16×16, 32×32, 64×64, 128×128, etc).',
            'geometry_file.mimes'  => 'La géométrie doit être un fichier JSON.',
            'geometry_file.max'    => 'Le fichier de géométrie ne doit pas dépasser 256 Ko.',
            'solid.required'       => 'La solidité est obligatoire.',
            'destructible.required'=> 'La destructibilité est obligatoire.',
            'resistance.required'  => 'La résistance est obligatoire.',
            'resistance.min'       => 'La résistance doit être entre 0 et 100.',
            'resistance.max'       => 'La résistance doit être entre 0 et 100.',
        ];
    }
}
