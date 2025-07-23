<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\TestJob;
use Illuminate\Support\Facades\Cache;

class AdministracionController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }
    public function caja()
    {

        if (!Cache::has('ForbidenGenderExamens')) {
              TestJob::dispatch();
        }
        if (Cache::has('ForbidenGenderExamens')) {
            $ForbidenGenderExamens = Cache::get('ForbidenGenderExamens');
        } else {
            $ForbidenGenderExamens = [];
        }

        if (Cache::has('Paises')) {
            $paises = Cache::get('Paises');
        } else {
              TestJob::dispatch();
              $paises = Cache::get('Paises')?? ['no hay paises'];
        }
        if (Cache::has('Municipios')) {
            $municipios = Cache::get('Municipios');
        } else {
          TestJob::dispatch();
            $municipios = Cache::get('Municipios') ?? ['no hay municipios'];
        }
        if (Cache::has('Eps')) {
            $eps = Cache::get('EPS');
        } else {
             TestJob::dispatch();
            $eps = Cache::get('EPS') ?? ['no hay eps'];
        }

        $NoExamenes = $ForbidenGenderExamens ? $ForbidenGenderExamens : ['no hay examenes prohibidos'];



        return view('admin.caja', compact('NoExamenes', 'paises', 'municipios', 'eps'));
    }
}
