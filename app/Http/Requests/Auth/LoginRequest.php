<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Permet à tout le monde de faire une requête de connexion
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ];
    }
}
