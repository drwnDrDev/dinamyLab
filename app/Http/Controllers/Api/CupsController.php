<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CodigoCup;

class CupsController extends Controller
{
    public function index()
    {
        return CodigoCup::all();
    }

    public function show($id)
    {
        return CodigoCup::find($id);
    }

    public function store(Request $request)
    {
        return CodigoCup::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $cup = CodigoCup::find($id);
        $cup->update($request->all());
        return $cup;
    }

    public function destroy($id)
    {
        return CodigoCup::destroy($id);
    }

    public function buscarPorCodigo($codigo)
    {
        return CodigoCup::where('codigo', $codigo)->get();
    }

    public function buscarPorNombre(Request $request)
    {
        $nombre = $request->query('nombre');
        return CodigoCup::where('nombre', 'like', "%$nombre%")->get();
    }

    public function activar($id)
    {
        $cup = CodigoCup::find($id);
        $cup->estado = 'activo';
        $cup->save();
        return $cup;
    }
}
