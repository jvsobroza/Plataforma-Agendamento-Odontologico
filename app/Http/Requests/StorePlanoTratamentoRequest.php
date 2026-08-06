<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePlanoTratamentoRequest extends FormRequest
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
            "id_paciente" => "required|integer|exists:pacientes,id",
            "status"=> "required|string",
            "servicos_planejados"=> "required|string",
            "servicos_realizados"=> "required|string",
            "ativo"=> "boolean",
        ];
    }
}
