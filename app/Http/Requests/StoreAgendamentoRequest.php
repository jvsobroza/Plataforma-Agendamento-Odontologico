<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAgendamentoRequest extends FormRequest
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
            "id_filial"=> "required|integer|exists:filials,id",
            "data_hora"=> "required|date|after_or_equal:today",
            "status_pagamento"=> "required|string",
            "status_agendamento"=> "required|string",
            "parecer_clinico" => "required|string",
            "origem_agendamento"=> "required|string|in:1,2",
            "ativo" => "boolean",
            "observacoes"=> "required|string",
        ];
    }
}
