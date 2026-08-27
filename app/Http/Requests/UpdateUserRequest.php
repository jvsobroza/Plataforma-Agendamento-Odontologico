<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            "nome" => "required|string",
            "email" => [
                "required",
                "email",
                Rule::unique('usuarios', 'email')->ignore($this->route('secretaria')), //usado para ignorar o email atual
            ],
            "senha" => "nullable|string|min:6",
            "tipo" => "required|integer|in:1,2",
            "id_filial" => "required|integer|exists:filials,id",
        ];
    }
}
