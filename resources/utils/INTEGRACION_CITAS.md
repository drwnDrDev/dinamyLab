# 🔌 Ejemplos de Integración - Sistema de Citas

## 1. Integrar botón en barra de navegación

### En el Layout principal (si usas Blade)

```blade
<!-- En tu layout o navbar -->
<nav>
    <!-- Otros items -->

    @auth
        <a href="{{ route('citas.index') }}" class="nav-link">
            📋 Gestionar Citas
        </a>
    @else
        <a href="{{ route('citas.create') }}" class="nav-link">
            📅 Agendar Cita
        </a>
    @endauth
</nav>
```

### En un componente React

```jsx
import { Link } from '@inertiajs/react';

export default function Navigation({ auth }) {
    return (
        <nav>
            {auth.user ? (
                <Link href={route('citas.index')}>
                    📋 Gestionar Citas
                </Link>
            ) : (
                <Link href={route('citas.create')}>
                    📅 Agendar Cita
                </Link>
            )}
        </nav>
    );
}
```

---

## 2. Llamar a las rutas desde JavaScript

```javascript
// Ir a registro de cita
window.location.href = '/citas/registrar';

// O usando Inertia
import { router } from '@inertiajs/react';
router.visit(route('citas.create'));

// Con parámetros
router.visit(route('citas.show', citaId));

// Con método POST
router.post(route('citas.store'), formData);
```

---

## 3. Verificar permisos en Blade

```blade
@can('view', $preRegistro)
    <a href="{{ route('citas.show', $preRegistro->id) }}">
        Ver detalles
    </a>
@endcan

@can('update', $preRegistro)
    <button onclick="cambiarEstado()">
        Cambiar estado
    </button>
@endcan
```

---

## 4. Obtener datos en el controlador

```php
// En tu controlador
use App\Models\PreRegistroCita;

public function miMetodo()
{
    // Obtener todas las citas pendientes
    $citasPendientes = PreRegistroCita::pendientes()->get();

    // Obtener citas de una fecha específica
    $citasHoy = PreRegistroCita::paraFecha(now()->date)->get();

    // Buscar por documento o código
    $cita = PreRegistroCita::buscarPorDocumentoOCodigo('12345678');

    // Con relaciones
    $cita = PreRegistroCita::with('persona', 'orden')->find($id);
}
```

---

## 5. Crear una cita desde el controlador

```php
use App\Models\PreRegistroCita;

// Manera manual
$cita = PreRegistroCita::create([
    'nombres_completos' => 'Juan Pérez',
    'tipo_documento' => 'CC',
    'numero_documento' => '123456789',
    'telefono_contacto' => '3001234567',
    'email' => 'juan@ejemplo.com',
    'fecha_deseada' => '2026-02-15',
    'hora_deseada' => '10:00',
    'motivo' => 'Chequeo general',
    'codigo_confirmacion' => PreRegistroCita::generarCodigoConfirmacion(),
    'estado' => 'pendiente',
]);

// O desde formulario validado
$validated = request()->validate([
    'nombres_completos' => 'required|string|max:255',
    'email' => 'required|email',
    // ... otros campos
]);

$cita = PreRegistroCita::create([
    ...$validated,
    'codigo_confirmacion' => PreRegistroCita::generarCodigoConfirmacion(),
]);
```

---

## 6. Escopes útiles

```php
// Buscar citas pendientes
$pendientes = PreRegistroCita::pendientes()->get();

// Buscar citas confirmadas
$confirmadas = PreRegistroCita::confirmados()->get();

// Citas para una fecha específica
$citasDelDia = PreRegistroCita::paraFecha(today())->get();

// Combinar scopes
$urgentes = PreRegistroCita::pendientes()
    ->paraFecha(today())
    ->get();

// Con paginación
$citas = PreRegistroCita::pendientes()
    ->with('persona', 'orden')
    ->paginate(15);
```

---

## 7. Eventos y listeners (Futuro)

```php
// Crear evento cuando se registra cita
// app/Events/CitaRegistrada.php
class CitaRegistrada
{
    public function __construct(public PreRegistroCita $cita) {}
}

// Listener para enviar email
// app/Listeners/EnviarEmailConfirmacion.php
class EnviarEmailConfirmacion
{
    public function handle(CitaRegistrada $event)
    {
        Mail::to($event->cita->email)->send(
            new ConfirmacionCitaMailable($event->cita)
        );
    }
}

// Usar en controlador
event(new CitaRegistrada($cita));
```

---

## 8. Exportar citas a Excel/PDF

```php
// En controlador
use Maatwebsite\Excel\Facades\Excel;

public function exportar()
{
    return Excel::download(
        new CitasExport(),
        'citas.xlsx'
    );
}

// Crear clase CitasExport
// app/Exports/CitasExport.php
class CitasExport implements FromQuery, WithHeadings
{
    public function query()
    {
        return PreRegistroCita::query();
    }

    public function headings(): array
    {
        return [
            'ID', 'Nombre', 'Email', 'Teléfono',
            'Fecha Deseada', 'Estado', 'Código'
        ];
    }
}
```

---

## 9. Dashboard de estadísticas

```php
// En controlador
public function dashboard()
{
    $estadisticas = [
        'total' => PreRegistroCita::count(),
        'pendientes' => PreRegistroCita::pendientes()->count(),
        'confirmadas' => PreRegistroCita::confirmados()->count(),
        'hoy' => PreRegistroCita::paraFecha(today())->count(),
        'semana' => PreRegistroCita::whereDate(
            'fecha_deseada',
            '>=',
            now()->startOfWeek()
        )->count(),
    ];

    return view('dashboard', $estadisticas);
}
```

---

## 10. Sincronizar con calendario

```php
// app/Services/CalendarService.php
class CalendarService
{
    public function sync(PreRegistroCita $cita)
    {
        $event = [
            'title' => 'Cita: ' . $cita->nombres_completos,
            'start' => $cita->fecha_deseada . 'T' . $cita->hora_deseada,
            'description' => $cita->motivo,
            'location' => $cita->datos_parseados['sede_id'] ?? null,
        ];

        // Enviar a Google Calendar API
        // return $this->googleCalendarClient->createEvent($event);
    }
}
```

---

## 11. Notificaciones por email

```php
// app/Mail/ConfirmacionCitaMailable.php
class ConfirmacionCitaMailable extends Mailable
{
    public function __construct(public PreRegistroCita $cita) {}

    public function build()
    {
        return $this->markdown('emails.confirmacion-cita')
            ->with([
                'nombre' => $this->cita->nombres_completos,
                'codigo' => $this->cita->codigo_confirmacion,
                'fecha' => $this->cita->fecha_deseada,
                'hora' => $this->cita->hora_deseada,
            ]);
    }
}

// Usar en controlador
Mail::to($cita->email)->send(new ConfirmacionCitaMailable($cita));
```

---

## 12. API REST para citas

```php
// app/Http/Controllers/Api/CitasController.php
class CitasController extends Controller
{
    // GET /api/citas
    public function index()
    {
        return PreRegistroCita::with('persona', 'orden')
            ->paginate(15);
    }

    // GET /api/citas/{id}
    public function show(PreRegistroCita $cita)
    {
        return $cita->load('persona', 'orden');
    }

    // POST /api/citas
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombres_completos' => 'required|string|max:255',
            // ... otros campos
        ]);

        return PreRegistroCita::create([
            ...$validated,
            'codigo_confirmacion' => PreRegistroCita::generarCodigoConfirmacion(),
        ]);
    }

    // PUT /api/citas/{id}
    public function update(Request $request, PreRegistroCita $cita)
    {
        $cita->update($request->validate([
            'estado' => 'in:pendiente,confirmada,procesada,cancelada',
        ]));

        return $cita;
    }
}

// En routes/api.php
Route::apiResource('citas', CitasController::class);
```

---

## 13. Búsqueda global de citas

```javascript
// En componente React
import { useState } from 'react';

export default function BuscadorCitas() {
    const [busqueda, setBusqueda] = useState('');
    const [resultados, setResultados] = useState([]);

    const handleBuscar = async (e) => {
        const query = e.target.value;
        setBusqueda(query);

        if (query.length > 2) {
            const res = await fetch(
                `/api/citas/buscar?q=${encodeURIComponent(query)}`
            );
            const data = await res.json();
            setResultados(data);
        }
    };

    return (
        <div>
            <input
                type="text"
                placeholder="Buscar por nombre, documento o código..."
                value={busqueda}
                onChange={handleBuscar}
            />
            <ul>
                {resultados.map(cita => (
                    <li key={cita.id}>
                        {cita.nombres_completos} - {cita.email}
                    </li>
                ))}
            </ul>
        </div>
    );
}
```

---

## 14. Notificaciones en tiempo real (WebSockets)

```php
// app/Events/CitaCreada.php
class CitaCreada
{
    public function __construct(public PreRegistroCita $cita) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('citas'),
        ];
    }
}

// En controlador
event(new CitaCreada($cita));

// En JavaScript (con Laravel Echo)
Echo.channel('citas')
    .listen('CitaCreada', (e) => {
        console.log('Nueva cita:', e.cita);
        // Actualizar UI en tiempo real
    });
```

---

## 15. Validaciones personalizadas

```php
// app/Rules/EmailNoRegistrado.php
class EmailNoRegistrado implements Rule
{
    public function passes($attribute, $value)
    {
        return !PreRegistroCita::where('email', $value)->exists();
    }

    public function message()
    {
        return 'Este email ya tiene un pre-registro.';
    }
}

// Usar en controlador
$request->validate([
    'email' => ['required', 'email', new EmailNoRegistrado()],
]);
```

---

## 16. Agendar tareas

```php
// app/Jobs/RecordarCita.php
class RecordarCita implements ShouldQueue
{
    public function __construct(public PreRegistroCita $cita) {}

    public function handle()
    {
        // Enviar recordatorio 24 horas antes
        Mail::to($this->cita->email)->send(
            new RecordatorioCitaMailable($this->cita)
        );
    }
}

// En kernel de comandos
class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(
            new RecordarCita(),
            'citas'
        )->dailyAt('09:00');
    }
}
```

---

## 17. Logging y auditoría

```php
// En modelo PreRegistroCita
protected static function booted()
{
    static::created(function ($cita) {
        Log::info('Nueva cita registrada', [
            'id' => $cita->id,
            'email' => $cita->email,
            'fecha' => $cita->fecha_deseada,
        ]);
    });

    static::updated(function ($cita) {
        Log::info('Cita actualizada', [
            'id' => $cita->id,
            'cambios' => $cita->getChanges(),
        ]);
    });
}
```

---

## 18. Testing

```php
// tests/Feature/CitasTest.php
class CitasTest extends TestCase
{
    public function test_registro_cita_anonimo()
    {
        $response = $this->post(route('citas.store'), [
            'nombres_completos' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'tipo_documento' => 'CC',
            'numero_documento' => '123456789',
            'telefono_contacto' => '3001234567',
            'fecha_deseada' => now()->addDay(),
            'hora_deseada' => '10:00',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('pre_registros_citas', [
            'email' => 'juan@test.com',
        ]);
    }

    public function test_solo_autenticados_pueden_ver_listado()
    {
        $response = $this->get(route('citas.index'));
        $response->assertRedirect(route('login'));
    }
}
```

---

## 19. Filtros avanzados

```php
// En controlador
public function filtroAvanzado(Request $request)
{
    $query = PreRegistroCita::query();

    if ($request->estado) {
        $query->where('estado', $request->estado);
    }

    if ($request->desde) {
        $query->whereDate('fecha_deseada', '>=', $request->desde);
    }

    if ($request->hasta) {
        $query->whereDate('fecha_deseada', '<=', $request->hasta);
    }

    if ($request->sede_id) {
        $query->whereJsonContains('datos_parseados->sede_id', $request->sede_id);
    }

    if ($request->busqueda) {
        $query->where('nombres_completos', 'LIKE', "%{$request->busqueda}%")
            ->orWhere('email', 'LIKE', "%{$request->busqueda}%")
            ->orWhere('numero_documento', 'LIKE', "%{$request->busqueda}%");
    }

    return $query->paginate(15);
}
```

---

## 20. Documentación de la API

```php
// app/Http/Controllers/Api/CitasController.php
/**
 * @OA\Get(
 *     path="/api/citas",
 *     summary="Listar citas",
 *     @OA\Response(response=200, description="Lista de citas")
 * )
 */
public function index()
{
    return PreRegistroCita::paginate(15);
}
```

Usa [Swagger/OpenAPI](https://swagger.io/) para documentar tu API.

---

**Nota**: Estos ejemplos son puntos de partida. Adapta según tus necesidades específicas.
