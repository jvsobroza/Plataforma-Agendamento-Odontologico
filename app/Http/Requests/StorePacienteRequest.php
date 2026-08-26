<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePacienteRequest extends FormRequest
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
            "cpf"=> "required|string|unique:pacientes,id|max:11",
            "data_nascimento"=> "required|date",
            "telefone"=> "required|string",
            "observacoes_medicas"=> "nullable|string",
            "ativo" => "boolean",
        ];
    }
}
