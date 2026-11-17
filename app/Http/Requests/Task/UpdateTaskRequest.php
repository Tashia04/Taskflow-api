<?php

namespace App\Http\Requests\Task;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status'      => 'sometimes|required|in:a_faire,en_cours,en_attente,termine',
            'priority'    => 'sometimes|required|in:low,medium,high,critical',
            'assigned_id' => 'sometimes|nullable|exists:users,id',
            'project_id'  => 'sometimes|required|exists:projects,id',
            'due_date'    => 'sometimes|nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'      => 'Le titre est obligatoire.',
            'status.in'           => 'Le statut doit être: a_faire, en_cours, en_attente, termine.',
            'priority.in'         => 'La priorité doit être: low, medium, high, critical.',
            'project_id.exists'   => 'Ce projet n\'existe pas.',
            'assigned_id.exists'  => 'Cet utilisateur n\'existe pas.',
            'due_date.date'       => 'La date d\'échéance doit être une date valide.',
        ];
    }
}
