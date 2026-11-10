<?php

namespace App\Http\Requests;

use App\Models\Persona;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ApiUpdatePersonaRequest extends FormRequest
{
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
            'numero_documento' => ['sometimes', 'required', 'string', 'max:16'],
            'nombres' => ['sometimes', 'required', 'string', 'max:255'],
            'apellidos' => ['sometimes', 'required', 'string', 'max:255'],
            'primer_nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'segundo_nombre' => ['sometimes', 'nullable', 'string', 'max:255'],
            'primer_apellido' => ['sometimes', 'required', 'string', 'max:255'],
            'segundo_apellido' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tipo_documento' => ['sometimes', 'required', 'string', 'exists:tipo_documentos,cod_rips'],
            'fecha_nacimiento' => ['sometimes', 'nullable', 'date', 'before_or_equal:today'],
            'sexo' => ['sometimes', 'nullable', 'exists:sexos,codigo'],
            'pais' => ['sometimes', 'required', 'string', 'exists:paises,codigo_iso'],
            'pais_origen' => ['sometimes', 'required', 'string', 'exists:paises,codigo_iso'],
            'telefono' => ['sometimes', 'nullable', 'string', 'size:10'],
            'correo' => ['sometimes', 'nullable', 'email', 'max:255'],
            'direccion' => ['sometimes', 'nullable', 'string', 'max:255'],
            'municipio' => ['sometimes', 'nullable', 'integer', 'exists:municipios,id'],
            'pais_residencia' => ['sometimes', 'nullable', 'string', 'exists:paises,codigo_iso'],
            'codigo_postal' => ['sometimes', 'nullable', 'string', 'max:10'],
            'zona' => ['sometimes', 'nullable', 'in:01,02'],
            'eps' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tipo_afiliacion' => ['sometimes', 'required', 'string', 'exists:tipos_afiliaciones,codigo'],
            'contacto_emergencia' => ['sometimes', 'nullable', 'array:acompanante_id,parentesco'],
            'contacto_emergencia.acompanante_id' => ['sometimes', 'required', 'integer', 'exists:personas,id'],
            'contacto_emergencia.parentesco' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $persona = Persona::find($this->route('id'));
            if (! $persona) {
                return;
            }

            $afiliacion = $persona->afiliacionSalud;
            if (
                $this->exists('tipo_afiliacion')
                && blank($this->input('eps'))
                && ! $afiliacion
            ) {
                $validator->errors()->add('eps', 'El EPS es obligatorio para crear la afiliación.');
            }

            $contacto = $this->input('contacto_emergencia');
            if (! is_array($contacto) || $persona->contactoEmergencia) {
                return;
            }

            foreach (['acompanante_id', 'parentesco'] as $field) {
                if (! array_key_exists($field, $contacto)) {
                    $validator->errors()->add(
                        "contacto_emergencia.{$field}",
                        'Este campo es obligatorio para crear el contacto de emergencia.'
                    );
                }
            }
        });
    }
}
