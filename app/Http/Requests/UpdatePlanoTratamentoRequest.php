<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanoTratamentoRequest extends FormRequest
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
            'id_paciente' => 'required|integer|exists:pacientes,id',
            'status' => 'required|string|in:Em andamento,Concluído,Cancelado',
            'servicos_planejados' => 'required|array|min:1',
            'servicos_planejados.*' => 'integer|distinct|exists:servicos,id',
            'servicos_concluidos' => 'nullable|array',
            'servicos_concluidos.*' => 'integer|distinct|exists:servicos,id',
        ];
    }
}
