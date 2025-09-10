<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CambiarNombrePersonaRequest extends FormRequest
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
            'primer_nombre' => ['required', 'string', 'max:255',
                function ($attribute, $value, $fail) {
                    $personaId = $this->route('id');
                    $nombresActuales = $this->obtenerNombresActuales($personaId);

                    if ($nombresActuales && levenshtein($value, $nombresActuales['primer_nombre']) > 3 && $this->observaciones === '') {
                        $fail('El cambio de primer nombre es significativo, por favor proporcione una justificación en el campo de observaciones.');
                    }
                }

        ],
            'segundo_nombre' => ['nullable', 'string', 'max:255'],
            'primer_apellido' => ['required', 'string', 'max:255'],
            'segundo_apellido' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function obtenerNombresActuales($id): array
    {
        $persona = Persona::find($id);
        if (!$persona) {
            return [];
        }

        return [
            'primer_nombre' => $persona->primer_nombre,

            'primer_apellido' => $persona->primer_apellido,

        ];
    }
}
