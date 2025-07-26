<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Orden;

class OrdenStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'numero_orden' => 'required|string|max:255',
            'paciente_id' => 'required|exists:personas,id',
            'acompaniante_id' => 'nullable|exists:personas,id',
            'observaciones' => 'nullable|string|max:1000',

        ];
    }
}
