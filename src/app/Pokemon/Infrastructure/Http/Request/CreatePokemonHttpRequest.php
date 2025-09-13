<?php

namespace App\Pokemon\Infrastructure\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class CreatePokemonHttpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                'unique:pokemon,name',
                'regex:/^[a-zA-Z\s\-\'\.]+$/'
            ],
            'type' => [
                'required',
                'string',
                'in:Electric,Fire,Water,Grass,Rock,Flying,Bug,Normal,Fighting,Poison,Ground,Psychic,Ice,Dragon,Dark,Steel,Fairy'
            ],
            'hp' => [
                'required',
                'integer',
                'min:1',
                'max:100'
            ],
            'status' => [
                'sometimes',
                'string',
                'in:wild,captured'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Pokemon name is required',
            'name.max' => 'Pokemon name cannot exceed 50 characters',
            'name.unique' => 'A Pokemon with this name already exists',
            'name.regex' => 'Pokemon name can only contain letters, spaces, hyphens, apostrophes and dots',
            'type.required' => 'Pokemon type is required',
            'type.in' => 'Pokemon type must be one of the valid types',
            'hp.required' => 'Pokemon HP is required',
            'hp.integer' => 'Pokemon HP must be an integer',
            'hp.min' => 'Pokemon HP must be at least 1',
            'hp.max' => 'Pokemon HP cannot exceed 100',
            'status.in' => 'Pokemon status must be either wild or captured'
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();
        
        // Set default status if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = 'wild';
        }
        
        return $validated;
    }
}