<?php

namespace App\Http\Requests\Project;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:a_faire,en_cours,termine',
            'user_ids'    => 'nullable|array',
            'user_ids.*'  => 'exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Le nom du projet est obligatoire.',
            'name.max'        => 'Le nom du projet ne doit pas dépasser 255 caractères.',
            'status.in'       => 'Le statut doit être: a_faire, en_cours ou termine.',
            'user_ids.array'  => 'Les utilisateurs assignés doivent être un tableau d\'IDs.',
            'user_ids.*.exists' => 'Un ou plusieurs utilisateurs spécifiés n\'existent pas.',
        ];
    }
}
