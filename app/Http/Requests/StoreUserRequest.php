<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            "nome"=> "required|string",
            "email"=> "required|unique:usuarios|email",
            "senha"=> "required|min:6",
            "tipo"=> "required|integer|in:1,2",
            "id_filial"=> "required|integer|exists:filials,id",
            "ativo"=> "boolean",
        ];
    }
}
