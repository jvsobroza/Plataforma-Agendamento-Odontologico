<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreServicoTratamentoRequest extends FormRequest
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
            "id_planos"=> "required|integer|exists:plano_tratamentos,id",
            "id_servico"=> "required|integer|exists:servicos,id",
            "id_agendamento"=> "required|integer|exists:agendamentos,id",
            "tempo"=> "required|integer",
            "preco" => "required|numeric",
        ];
    }
}
