<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use App\Models\Pais;
use App\Models\Municipio;
use App\Models\Eps;

class TestJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $ForbidenGenderExamens = ['masculino'=>[
            'examenes' => ['examen1', 'examen2', 'examen3'],
            'descripcion' => 'Exámenes prohibidos para el género masculino'
        ], 'femenino'=>[
            'examenes' => ['examen4', 'examen5', 'examen6'],
            'descripcion' => 'Exámenes prohibidos para el género femenino'
        ]];
        $paises = Pais::select('nivel', 'nombre', 'codigo_iso')->orderBy('nivel','desc')->get()->toArray();
        $municipios = Municipio::select('id', 'nivel', 'municipio', 'departamento')->orderBy('nivel','desc')->get()->toArray();
        $eps = \App\Models\Eps::select('id', 'nombre')
            ->where('verificada', true) // Solo EPS verificadas
            ->orderBy('nombre')->get()->toArray();

        // Guardar los datos en caché
        Cache::put('ForbidenGenderExamens', $ForbidenGenderExamens, 60 * 24); // Guardar en caché por 24 horas
        Cache::put('Paises', $paises, 60 * 24);
        Cache::put('Municipios', $municipios, 60 * 24);
        Cache::put('EPS', $eps, 60 * 24);
        \Log::info('[TestJob] Datos de ForbidenGenderExamens, Paises, Municipios y EPS guardados en caché.');

        // Aquí podrías agregar más lógica si es necesario


    }
}
