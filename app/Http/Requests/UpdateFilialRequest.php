<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFilialRequest extends FormRequest
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
            "cidade" => "string|required",
            "endereco" => "string|required",
            "datas_agenda" => "array|required",
            "datas_agenda.*" => "integer|between:0,6",
            "servicos" => "array|required|min:1",
            "servicos.*" => "integer|exists:servicos,id",
            "ativo" => "boolean",
        ];
    }
}
