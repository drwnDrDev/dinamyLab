<?php

/**
 * EJEMPLOS DE INTEGRACIÓN DEL COMPONENTE REACT DE CONVENIOS
 * 
 * Este archivo muestra cómo integrar el componente React de convenios
 * en tu aplicación Laravel.
 */

// ============================================================
// EJEMPLO 1: Modificar el controlador para usar la vista React
// ============================================================

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use Illuminate\View\View;

class ConvenioController extends Controller
{
    /**
     * Mostrar el formulario para crear un nuevo convenio (versión React)
     */
    public function create(): View
    {
        // Obtener los tipos de documento disponibles
        $documentos = TipoDocumento::all();
        
        // O si prefieres pasar documentos específicos
        // $documentos = [
        //     (object)['cod_dian' => '91', 'nombre' => 'NIT'],
        //     (object)['cod_dian' => '11', 'nombre' => 'Cédula de Ciudadanía'],
        // ];
        
        return view('convenios.create-react', [
            'documentos' => $documentos,
        ]);
    }

    /**
     * Guardar el convenio creado (funciona tanto para Blade como React)
     */
    public function store(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'tipo_documento' => 'required|string',
            'numero_documento' => 'required|string|unique:convenios',
            'razon_social' => 'required|string',
            'telefono' => 'nullable|string',
            'correo' => 'nullable|email',
            'municipio' => 'nullable|string',
            'direccion' => 'nullable|string',
            'pais' => 'nullable|string',
            'redes.*' => 'nullable|string',
        ]);

        // Crear convenio
        $convenio = Convenio::create($validated);

        // Guardar redes sociales si existen
        if ($request->has('redes')) {
            $convenio->redes()->createMany(
                collect($request->input('redes'))
                    ->map(fn($value, $key) => ['tipo' => $key, 'valor' => $value])
                    ->toArray()
            );
        }

        return redirect()
            ->route('convenios.show', $convenio)
            ->with('success', 'Convenio creado exitosamente');
    }
}

// ============================================================
// EJEMPLO 2: Crear un endpoint API para obtener países
// ============================================================

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PaisController extends Controller
{
    /**
     * Obtener lista de países para el autocomplete
     * 
     * GET /api/paises
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Opción 1: Desde una tabla en la base de datos
        // $paises = Pais::pluck('nombre')->toArray();
        
        // Opción 2: Desde una lista fija
        $paises = [
            'Colombia',
            'Argentina',
            'Australia',
            'Austria',
            'Bélgica',
            'Brasil',
            'Bulgaria',
            'Canadá',
            'Chile',
            'China',
            'Chipre',
            'Corea del Sur',
            'Costa Rica',
            'Croacia',
            'Dinamarca',
            'Eslovaquia',
            'Eslovenia',
            'España',
            'Estados Unidos',
            'Estonia',
            'Filipinas',
            'Finlandia',
            'Francia',
            'Grecia',
            'Hungría',
            'India',
            'Indonesia',
            'Irlanda',
            'Islandia',
            'Israel',
            'Italia',
            'Japón',
            'Letonia',
            'Lituania',
            'Luxemburgo',
            'Malta',
            'Marruecos',
            'México',
            'Noruega',
            'Nueva Zelanda',
            'Países Bajos',
            'Pakistán',
            'Panamá',
            'Paraguay',
            'Perú',
            'Polonia',
            'Portugal',
            'Reino Unido',
            'República Checa',
            'República Dominicana',
            'Rumania',
            'Rusia',
            'Samoa Americana',
            'Singapur',
            'Siria',
            'Sudáfrica',
            'Suecia',
            'Suiza',
            'Tailandia',
            'Taiwán',
            'Tanzania',
            'Túnez',
            'Turquía',
            'Ucrania',
            'Uganda',
            'Uruguay',
            'Uzbekistán',
            'Venezuela',
            'Vietnam',
        ];

        return response()->json($paises);
    }

    /**
     * Buscar países por término
     * 
     * GET /api/paises/buscar?q=colom
     */
    public function buscar(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        if (strlen($query) < 3) {
            return response()->json([]);
        }

        // Obtener todos los países
        // $paises = Pais::where('nombre', 'ilike', "%{$query}%")
        //     ->limit(10)
        //     ->pluck('nombre')
        //     ->toArray();

        // O con lista fija
        $todosLosPaises = /* tu lista de países */;
        $paises = array_filter(
            $todosLosPaises,
            fn($pais) => stripos($pais, $query) !== false
        );
        $paises = array_values(array_slice($paises, 0, 10));

        return response()->json($paises);
    }
}

// ============================================================
// EJEMPLO 3: Rutas API (routes/api.php)
// ============================================================

/*
use App\Http\Controllers\Api\PaisController;

Route::get('/paises', [PaisController::class, 'index']);
Route::get('/paises/buscar', [PaisController::class, 'buscar']);
*/

// ============================================================
// EJEMPLO 4: Cargar países desde la base de datos (Seeder)
// ============================================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaisSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run(): void
    {
        $paises = [
            ['nombre' => 'Colombia', 'codigo' => 'CO'],
            ['nombre' => 'Argentina', 'codigo' => 'AR'],
            ['nombre' => 'Brasil', 'codigo' => 'BR'],
            // ... más países
        ];

        DB::table('paises')->insert($paises);
    }
}

// ============================================================
// NOTAS IMPORTANTES
// ============================================================

/*
1. Si usas una tabla de países en la BD, recuerda crear la migración:
   php artisan make:migration create_paises_table
   php artisan make:seeder PaisSeeder
   php artisan migrate
   php artisan db:seed --class=PaisSeeder

2. El componente React busca en localStorage por defecto, pero se puede 
   modificar para cargar desde una API:
   
   En ConvenioForm.jsx, modifica el useEffect así:
   
   useEffect(() => {
     fetch('/api/paises')
       .then(res => res.json())
       .then(data => setPaises(data))
       .catch(() => {
         const paisesLocal = localStorage.getItem('paises');
         if (paisesLocal) setPaises(JSON.parse(paisesLocal));
       });
   }, []);

3. El componente maneja CSRF automáticamente buscando el token meta:
   <meta name="csrf-token" content="{{ csrf_token() }}">

4. Asegúrate de que Vite esté compilando el nuevo entry point:
   'resources/js/convenioCreate.jsx' debe estar en vite.config.js
*/
