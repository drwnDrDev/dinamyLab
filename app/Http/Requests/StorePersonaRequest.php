<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\TipoDocumento;

class StorePersonaRequest extends FormRequest
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
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'tipo_documento' => ['required', 'string','max:2',
            function ($attribute, $value, $fail) {
                    if (TipoDocumento::tryFrom($value) === null) {
                        $fail('El tipo de documento no es válido.');
                    }
                },],
            'numero_documento' => ['required', 'string','min:3', 'max:16',
            function ($attribute, $value, $fail) {
                    if ($this->tipo_documento == 'CC'|| $this->tipo_documento == 'RC' || $this->tipo_documento == 'TI' && strlen($value) > 10) {
                        $fail('El número de documento supera la longitud permitida.');
                    }
                }],
        
        ];
       
    }
}
