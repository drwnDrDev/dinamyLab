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
            'numero_documento' => ['required', 'string','min:3', 'max:16','regex:/^[a-zA-Z0-9]+$/',
            function ($attribute, $value, $fail) {
                if (in_array($this->tipo_documento, ['CC', 'RC', 'TI'])) {
                    if (!ctype_digit($value)) {
                        $fail('El número de documento debe ser numérico para el tipo de documento seleccionado.');
                    }
                    if (strlen($value) > 10) {
                        $fail('El número de documento no puede superar los 10 caracteres para el tipo de documento seleccionado.');
                    }
                }
            },
            'unique:personas,numero_documento'],
            'fecha_nacimiento' => ['nullable','date',
            function ($attribute, $value, $fail) {
                if ($value && strtotime($value) > strtotime('today')) {
                    $fail('La fecha de nacimiento no puede ser una fecha futura.');
                }
            }],

            'perfil' => ['required',
                function ($attribute, $value, $fail) {
                    if ($value === 'Paciente'){
                        if (empty($this->input('sexo'))) {
                            $fail('El campo sexo es obligatorio para el perfil paciente.');
                        }
                        if (empty($this->input('fecha_nacimiento'))) {
                            $fail('El campo fecha de nacimiento es obligatorio para el perfil paciente.');
                        }
                    }
                },

            ],
            'telefono' => ['nullable', 'string', 'size:10'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:255'],
        ];

    }
}
