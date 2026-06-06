<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->route() && $this->route()->getName() === 'item.update';

        return [
            'name'       => ['required', 'string', 'min:1', 'max:50', 'regex:/^[a-zA-Z0-9 ]+$/'],
            'identifier' => ['required', 'string', 'regex:/^[a-z0-9_]+$/', $isUpdate ? 'unique:items,identifier,' . $this->route('item')->id : 'unique:items'],
            'texture'    => [$isUpdate ? 'nullable' : 'required', 'file', 'mimes:png', 'max:512'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Le nom de l\'item est obligatoire.',
            'name.max'            => 'Le nom ne doit pas dépasser 50 caractères.',
            'name.regex'          => 'Le nom ne peut contenir que des lettres, chiffres et espaces.',
            'identifier.required' => 'L\'identifiant technique est obligatoire.',
            'identifier.regex'    => 'L\'identifiant ne doit contenir que des minuscules et underscores (ex: my_item).',
            'identifier.unique'   => 'Cet identifiant existe déjà. Veuillez en choisir un autre.',
            'texture.required'    => 'La texture est obligatoire pour créer un nouvel item.',
            'texture.mimes'       => 'La texture doit être un fichier PNG.',
            'texture.max'         => 'La texture ne doit pas dépasser 512 Ko.',
        ];
    }
}
