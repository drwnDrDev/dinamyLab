<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Remision;
use Illuminate\Http\JsonResponse;

class RemisionesController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Remision::with(['valores' => fn ($query) => $query->orderBy('precio')->orderBy('id'), 'valores.ipsExterna'])
                ->orderBy('nombre')->get(),
        ]);
    }
}
