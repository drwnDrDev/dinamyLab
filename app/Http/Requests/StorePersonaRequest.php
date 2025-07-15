<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TipoDocumento;

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
            'tipo_documento' => ['required', 'string','max:4',
            function ($attribute, $value, $fail) {
                    if (TipoDocumento::idPorCodigoRips($value) === null) {
                        $fail('El tipo de documento no es válido.');
                    }
                    if($this->calcularEdad($this->input('fecha_nacimiento')) < TipoDocumento::where('cod_rips', $value)->value('edad_minima')) {
                        $fail('La persona no cumple con la edad mínima requerida para este tipo de documento.');
                    }
                    if($this->calcularEdad($this->input('fecha_nacimiento')) > TipoDocumento::where('cod_rips', $value)->value('edad_maxima')) {
                        $fail('La persona no cumple con la edad máxima permitida para este tipo de documento.');
                    }
                },],
            'numero_documento' => ['required', 'string','min:3', 'max:16',TipoDocumento::regexPorCodigoRips($this->input('tipo_documento')),'unique:personas,numero_documento'],
            'fecha_nacimiento' => ['nullable','date',
            function ($attribute, $value, $fail) {
                if ($value && strtotime($value) > strtotime('today')) {
                    $fail('La fecha de nacimiento no puede ser una fecha futura.');
                }
                if ($value && strtotime($value) < strtotime('1900-01-01')) {
                    $fail('La fecha de nacimiento no puede ser anterior al 1 de enero de 1900.');
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
            'municipio' => ['nullable', 'string', 'max:255'],
        ];

    }
    /**
     * Calcula la edad a partir de una fecha de nacimiento.
     *
     * @param string|null $fechaNacimiento
     * @return int|null
     */
    public function calcularEdad(?string $fechaNacimiento): ?int
    {
        if (!$fechaNacimiento) {
            return null;
        }
        $fecha = date_create($fechaNacimiento);
        $hoy = date_create('today');
        if (!$fecha) {
            return null;
        }
        $edad = date_diff($fecha, $hoy)->y;
        return $edad;
    }
}
