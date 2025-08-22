<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AfiliacionSaludController extends Controller
{public function index()
    {
 
        return view('afiliacion_salud.index');
    }

    public function create()
    {
        // Logic to show the form for creating a new health affiliation
        return view('afiliacion_salud.create');
    }

    public function store(Request $request)
    {
        // Logic to store a new health affiliation
        // Validate and save the data
        return redirect()->route('afiliacion_salud.index')->with('success', 'Health affiliation created successfully.');
    }

    public function edit($id)
    {
        // Logic to show the form for editing an existing health affiliation
        return view('afiliacion_salud.edit', ['id' => $id]);
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing health affiliation
        // Validate and update the data
        return redirect()->route('afiliacion_salud.index')->with('success', 'Health affiliation updated successfully.');
    }

    public function destroy($id)
    {
        // Logic to delete a health affiliation
        return redirect()->route('afiliacion_salud.index')->with('success', 'Health affiliation deleted successfully.');
    }
}
