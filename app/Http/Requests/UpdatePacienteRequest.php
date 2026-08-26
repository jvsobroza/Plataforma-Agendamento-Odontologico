<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePacienteRequest extends FormRequest
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
            "cpf" => [
                "required",
                "digits:11",
                Rule::unique('pacientes', 'cpf')->ignore($this->route('paciente')), //usado pra ignorar o cpf já existente
            ],
            "data_nascimento" => "required|date",
            "telefone" => "required|string",
            "observacoes_medicas" => "nullable|string",
        ];
    }
}
