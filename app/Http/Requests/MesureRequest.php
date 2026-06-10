<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MesureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'temperature'   => 'sometimes|numeric|between:-50,100',
            'humidite'      => 'sometimes|numeric|between:0,100',
            'gaz'           => 'sometimes|numeric|min:0',
            'pir'           => 'sometimes|boolean',
            'salle_id'      => 'sometimes|integer|min:1',
            'equipement_id' => 'sometimes|nullable|integer|min:1',
        ];
    }
}
