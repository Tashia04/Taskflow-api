<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Permet à tout le monde de faire une requête d'inscription
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ];
    }
}
