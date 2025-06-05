<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Procedimiento;
use App\Models\Orden;

class ResultadosController extends Controller
{
    
    public function index()
    {

        return view('resultados.index');
    }
    public function show($id)
    {
        
        return view('resultados.show', ['id' => $id]);
    }
    public function create(Procedimiento $procedimiento)
    {
        return view('resultados.create', ['procedimiento' => $procedimiento]);   
    }
}
