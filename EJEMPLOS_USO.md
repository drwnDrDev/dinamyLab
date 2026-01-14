# 💡 Ejemplos Prácticos - Sistema de Pre-registro de Citas

Este archivo contiene ejemplos reales de cómo usar el sistema en diferentes escenarios.

## 📱 Escenario 1: Usuario registra una cita para él mismo

### Frontend

```jsx
import { FormPreRegistroCita } from './components/citas';

function PaginaPreRegistro() {
    return (
        <div>
            <h1>Pre-registra tu cita</h1>
            <FormPreRegistroCita 
                onSuccess={(data) => {
                    // Usuario completó el registro
                    const codigo = data.data.codigo_confirmacion;
                    alert(`¡Listo! Tu código es: ${codigo}`);
                    
                    // Opcional: redirigir a página de confirmación
                    window.location.href = `/cita-confirmada?codigo=${codigo}`;
                }}
            />
        </div>
    );
}
```

### Flujo:
1. Usuario abre la página
2. Llena el formulario (solo nombre es obligatorio)
3. Click en "Registrar"
4. Recibe código: `ABC12345`
5. Guarda captura o copia el código

---

## 👨‍👩‍👧‍👦 Escenario 2: Usuario registra toda su familia

### Frontend

```jsx
import { PreRegistroListaCitas } from './components/citas';

function PaginaRegistroFamiliar() {
    return (
        <div>
            <h1>Registra varias personas</h1>
            <PreRegistroListaCitas 
                onSuccess={(data) => {
                    // data.data es un array con todos los registros
                    const cantidad = data.data.length;
                    const codigos = data.data.map(r => r.codigo_confirmacion);
                    
                    console.log(`${cantidad} personas registradas`);
                    console.log('Códigos:', codigos);
                    
                    // Opcional: enviar códigos por email
                    enviarCodigosPorEmail(data.data);
                }}
            />
        </div>
    );
}

function enviarCodigosPorEmail(registros) {
    const texto = registros.map(r => 
        `${r.nombres_completos}: ${r.codigo_confirmacion}`
    ).join('\n');
    
    // Aquí integrarías con tu servicio de email
    console.log('Enviar email con:', texto);
}
```

### Usuario ingresa:
```
Carlos Ramirez, 1012555321
Zonia Fierro, 10101010
Juan Perez
Maria Lopez, 5555555
```

### Sistema genera:
```
Carlos Ramirez: ABC12345
Zonia Fierro: DEF67890
Juan Perez: GHI13579
Maria Lopez: JKL24680
```

---

## 🔍 Escenario 3: Usuario consulta el estado de su cita

### Frontend

```jsx
import { ConsultarCita } from './components/citas';

function PaginaConsulta() {
    return (
        <div>
            <h1>Consulta tu cita</h1>
            <p>Ingresa tu código de confirmación o documento</p>
            <ConsultarCita />
        </div>
    );
}
```

### Flujo:
1. Usuario abre la página de consulta
2. Ingresa su código `ABC12345` o documento `1012555321`
3. Sistema muestra:
   - Nombre: Carlos Ramirez
   - Estado: Pendiente
   - Fecha deseada: 15 de febrero de 2024
   - Instrucciones: "Acude a recepción con tu código"

---

## 🏥 Escenario 4: Recepción confirma un pre-registro

### Frontend

```jsx
import { RecepcionCitas } from './components/citas';
import FormPersona from './components/FormPersona';

function PaginaRecepcion() {
    return (
        <div>
            <RecepcionCitas 
                FormPersona={FormPersona}
            />
        </div>
    );
}
```

### Flujo en recepción:

1. **Paciente llega y presenta código**: `ABC12345`

2. **Recepcionista busca** en el sistema:
   - Ingresa `ABC12345` en búsqueda
   - Sistema muestra pre-registro de "Carlos Ramirez"

3. **Verifica datos** con el paciente:
   - "¿Su nombre es Carlos Ramirez?"
   - "¿Su documento es 1012555321?"
   - "¿Su teléfono es 3001234567?"

4. **Completa el formulario** con ayuda del paciente:
   - Tipo de documento: CC
   - Fecha de nacimiento: 15/05/1990
   - Sexo: M
   - EPS: Sanitas
   - Etc.

5. **Confirma** el registro:
   - Click en "Confirmar y guardar"
   - Sistema crea persona formal
   - Estado cambia a "confirmado"
   - Se genera ID de persona: `123`

---

## 🎨 Escenario 5: Integración personalizada

### Componente wrapper personalizado

```jsx
import { FormPreRegistroCita } from './components/citas';

function MiFormularioPersonalizado() {
    const handleSuccess = (data) => {
        // Extraer información
        const preRegistro = data.data;
        
        // Guardar en localStorage
        localStorage.setItem('ultimo_codigo', preRegistro.codigo_confirmacion);
        
        // Enviar notificación push
        enviarNotificacionPush(preRegistro);
        
        // Analytics
        gtag('event', 'pre_registro_exitoso', {
            codigo: preRegistro.codigo_confirmacion
        });
        
        // Mostrar modal personalizado
        mostrarModalConfirmacion(preRegistro);
    };
    
    return (
        <div className="mi-container-personalizado">
            <div className="mi-header">
                <img src="/logo.png" alt="Logo" />
                <h1>Bienvenido a nuestra clínica</h1>
            </div>
            
            <FormPreRegistroCita onSuccess={handleSuccess} />
            
            <div className="mi-footer">
                <p>Horarios de atención: 7am - 7pm</p>
            </div>
        </div>
    );
}
```

---

## 📊 Escenario 6: Dashboard de estadísticas

### Crear componente de stats

```jsx
import { useState, useEffect } from 'react';
import axios from 'axios';

function DashboardPreRegistros() {
    const [stats, setStats] = useState(null);
    
    useEffect(() => {
        cargarEstadisticas();
    }, []);
    
    const cargarEstadisticas = async () => {
        try {
            // Endpoint personalizado que agregarías
            const response = await axios.get('/api/recepcion/pre-registros/estadisticas');
            setStats(response.data);
        } catch (error) {
            console.error(error);
        }
    };
    
    if (!stats) return <div>Cargando...</div>;
    
    return (
        <div className="grid grid-cols-4 gap-4 p-6">
            <div className="bg-yellow-100 p-4 rounded">
                <h3 className="font-bold">Pendientes</h3>
                <p className="text-3xl">{stats.pendientes}</p>
            </div>
            
            <div className="bg-blue-100 p-4 rounded">
                <h3 className="font-bold">Confirmados hoy</h3>
                <p className="text-3xl">{stats.confirmados_hoy}</p>
            </div>
            
            <div className="bg-green-100 p-4 rounded">
                <h3 className="font-bold">Total mes</h3>
                <p className="text-3xl">{stats.total_mes}</p>
            </div>
            
            <div className="bg-purple-100 p-4 rounded">
                <h3 className="font-bold">Promedio diario</h3>
                <p className="text-3xl">{stats.promedio_diario}</p>
            </div>
        </div>
    );
}
```

### Backend para estadísticas

```php
// En PreRegistroCitaController.php
public function estadisticas()
{
    return response()->json([
        'pendientes' => PreRegistroCita::where('estado', 'pendiente')->count(),
        'confirmados_hoy' => PreRegistroCita::where('estado', 'confirmado')
            ->whereDate('updated_at', today())->count(),
        'total_mes' => PreRegistroCita::whereMonth('created_at', now()->month)->count(),
        'promedio_diario' => PreRegistroCita::whereMonth('created_at', now()->month)
            ->count() / now()->day,
    ]);
}
```

### Ruta

```php
// En routes/api.php
Route::get('/recepcion/pre-registros/estadisticas', [PreRegistroCitaController::class, 'estadisticas'])
    ->middleware('auth:sanctum');
```

---

## 📧 Escenario 7: Envío de código por email

### Backend - Agregar notificación

```php
// Crear notificación
php artisan make:notification PreRegistroCreado
```

```php
// app/Notifications/PreRegistroCreado.php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PreRegistroCreado extends Notification
{
    protected $preRegistro;
    
    public function __construct($preRegistro)
    {
        $this->preRegistro = $preRegistro;
    }
    
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Código de confirmación - Cita médica')
            ->greeting('¡Hola ' . $this->preRegistro->nombres_completos . '!')
            ->line('Tu pre-registro ha sido exitoso.')
            ->line('Tu código de confirmación es:')
            ->line('**' . $this->preRegistro->codigo_confirmacion . '**')
            ->line('Por favor preséntalo al llegar a nuestra clínica.')
            ->action('Consultar estado', url('/consultar-cita?codigo=' . $this->preRegistro->codigo_confirmacion))
            ->line('¡Gracias por confiar en nosotros!');
    }
}
```

### Usar en el controlador

```php
// En PreRegistroCitaController::preRegistrar()
use App\Notifications\PreRegistroCreado;
use Illuminate\Support\Facades\Notification;

public function preRegistrar(Request $request)
{
    // ... validación y creación
    
    $preRegistro = PreRegistroCita::create($validated);
    
    // Enviar notificación si hay email
    if ($preRegistro->email) {
        Notification::route('mail', $preRegistro->email)
            ->notify(new PreRegistroCreado($preRegistro));
    }
    
    return response()->json([...]);
}
```

---

## 📱 Escenario 8: SMS con Twilio

### Instalación

```bash
composer require twilio/sdk
```

### Configuración

```php
// config/services.php
'twilio' => [
    'sid' => env('TWILIO_SID'),
    'token' => env('TWILIO_TOKEN'),
    'from' => env('TWILIO_FROM'),
],
```

```env
# .env
TWILIO_SID=tu_sid
TWILIO_TOKEN=tu_token
TWILIO_FROM=+1234567890
```

### Servicio de SMS

```php
// app/Services/EnviarSMS.php
namespace App\Services;

use Twilio\Rest\Client;

class EnviarSMS
{
    protected $twilio;
    
    public function __construct()
    {
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }
    
    public function enviarCodigoConfirmacion($telefono, $codigo, $nombre)
    {
        $mensaje = "Hola {$nombre}! Tu código de confirmación es: {$codigo}. Preséntalo al llegar a la clínica.";
        
        try {
            $this->twilio->messages->create(
                $telefono,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $mensaje
                ]
            );
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error al enviar SMS: ' . $e->getMessage());
            return false;
        }
    }
}
```

### Usar en el controlador

```php
// En PreRegistroCitaController
use App\Services\EnviarSMS;

public function preRegistrar(Request $request)
{
    // ... creación del pre-registro
    
    // Enviar SMS si hay teléfono
    if ($preRegistro->telefono_contacto) {
        (new EnviarSMS())->enviarCodigoConfirmacion(
            $preRegistro->telefono_contacto,
            $preRegistro->codigo_confirmacion,
            $preRegistro->nombres_completos
        );
    }
    
    return response()->json([...]);
}
```

---

## 🔔 Escenario 9: Recordatorios automáticos

### Crear comando artisan

```bash
php artisan make:command EnviarRecordatorios
```

```php
// app/Console/Commands/EnviarRecordatorios.php
namespace App\Console\Commands;

use App\Models\PreRegistroCita;
use App\Services\EnviarSMS;
use Illuminate\Console\Command;

class EnviarRecordatorios extends Command
{
    protected $signature = 'citas:recordatorios';
    protected $description = 'Envía recordatorios de citas para mañana';
    
    public function handle()
    {
        // Buscar citas confirmadas para mañana
        $citas = PreRegistroCita::where('estado', 'confirmado')
            ->whereDate('fecha_deseada', now()->addDay())
            ->get();
        
        $enviados = 0;
        
        foreach ($citas as $cita) {
            if ($cita->telefono_contacto) {
                $mensaje = "Recordatorio: Tienes cita mañana " . 
                          ($cita->hora_deseada ? "a las {$cita->hora_deseada}" : "") . 
                          ". Código: {$cita->codigo_confirmacion}";
                
                (new EnviarSMS())->enviarCodigoConfirmacion(
                    $cita->telefono_contacto,
                    $cita->codigo_confirmacion,
                    $cita->nombres_completos
                );
                
                $enviados++;
            }
        }
        
        $this->info("Se enviaron {$enviados} recordatorios");
    }
}
```

### Programar en cron

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Enviar recordatorios todos los días a las 6 PM
    $schedule->command('citas:recordatorios')
        ->dailyAt('18:00');
}
```

---

## 🎯 Escenario 10: Integración con React Router

```jsx
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { 
    FormPreRegistroCita,
    PreRegistroListaCitas,
    ConsultarCita,
    RecepcionCitas 
} from './components/citas';
import FormPersona from './components/FormPersona';

function App() {
    const [user, setUser] = useState(null);
    
    return (
        <BrowserRouter>
            <Routes>
                {/* Rutas públicas */}
                <Route path="/" element={<HomePage />} />
                <Route path="/pre-registro" element={<FormPreRegistroCita />} />
                <Route path="/pre-registro-multiple" element={<PreRegistroListaCitas />} />
                <Route path="/consultar" element={<ConsultarCita />} />
                
                {/* Rutas protegidas */}
                <Route 
                    path="/recepcion" 
                    element={
                        user ? 
                        <RecepcionCitas FormPersona={FormPersona} /> :
                        <Navigate to="/login" />
                    }
                />
                
                <Route path="/login" element={<LoginPage />} />
                <Route path="*" element={<NotFound />} />
            </Routes>
        </BrowserRouter>
    );
}

function HomePage() {
    return (
        <div className="container mx-auto p-8">
            <h1 className="text-4xl font-bold mb-8">Sistema de Citas</h1>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="/pre-registro" className="p-6 bg-blue-100 rounded-lg hover:bg-blue-200">
                    <h2 className="text-xl font-bold mb-2">📝 Registrar una cita</h2>
                    <p>Pre-registra tu cita en menos de 1 minuto</p>
                </a>
                
                <a href="/pre-registro-multiple" className="p-6 bg-purple-100 rounded-lg hover:bg-purple-200">
                    <h2 className="text-xl font-bold mb-2">👨‍👩‍👧 Registro familiar</h2>
                    <p>Registra varias personas a la vez</p>
                </a>
                
                <a href="/consultar" className="p-6 bg-teal-100 rounded-lg hover:bg-teal-200">
                    <h2 className="text-xl font-bold mb-2">🔍 Consultar cita</h2>
                    <p>Verifica el estado de tu pre-registro</p>
                </a>
            </div>
        </div>
    );
}
```

---

## 🧪 Escenario 11: Testing

### Test backend (PHPUnit)

```php
// tests/Feature/PreRegistroCitaTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PreRegistroCita;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PreRegistroCitaTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function puede_crear_pre_registro()
    {
        $response = $this->postJson('/api/citas/pre-registrar', [
            'nombres_completos' => 'Juan Test',
            'numero_documento' => '123456789'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'codigo_confirmacion',
                    'nombres_completos'
                ]
            ]);
        
        $this->assertDatabaseHas('pre_registros_citas', [
            'nombres_completos' => 'Juan Test',
            'numero_documento' => '123456789',
            'estado' => 'pendiente'
        ]);
    }
    
    /** @test */
    public function codigo_confirmacion_es_unico()
    {
        $codigo1 = PreRegistroCita::generarCodigoConfirmacion();
        $codigo2 = PreRegistroCita::generarCodigoConfirmacion();
        
        $this->assertNotEquals($codigo1, $codigo2);
        $this->assertEquals(8, strlen($codigo1));
    }
}
```

### Test frontend (Jest + React Testing Library)

```jsx
// __tests__/FormPreRegistroCita.test.jsx
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import FormPreRegistroCita from '../FormPreRegistroCita';
import axios from 'axios';

jest.mock('axios');

describe('FormPreRegistroCita', () => {
    test('renderiza el formulario correctamente', () => {
        render(<FormPreRegistroCita />);
        
        expect(screen.getByLabelText(/nombre completo/i)).toBeInTheDocument();
        expect(screen.getByRole('button', { name: /registrar/i })).toBeInTheDocument();
    });
    
    test('muestra código después de registro exitoso', async () => {
        axios.post.mockResolvedValue({
            data: {
                success: true,
                data: {
                    codigo_confirmacion: 'TEST1234',
                    nombres_completos: 'Juan Test'
                }
            }
        });
        
        render(<FormPreRegistroCita />);
        
        const input = screen.getByLabelText(/nombre completo/i);
        const button = screen.getByRole('button', { name: /registrar/i });
        
        fireEvent.change(input, { target: { value: 'Juan Test' } });
        fireEvent.click(button);
        
        await waitFor(() => {
            expect(screen.getByText(/TEST1234/i)).toBeInTheDocument();
        });
    });
});
```

---

## 🎨 Escenario 12: Personalización avanzada

### Tema personalizado

```jsx
// themes/citasTheme.js
export const citasTheme = {
    colors: {
        primary: 'bg-indigo-600 hover:bg-indigo-700',
        secondary: 'bg-pink-600 hover:bg-pink-700',
        success: 'bg-emerald-600 hover:bg-emerald-700',
        danger: 'bg-rose-600 hover:bg-rose-700',
        warning: 'bg-amber-100 border-amber-300 text-amber-800',
        info: 'bg-sky-100 border-sky-300 text-sky-800',
    },
    buttons: {
        primary: 'px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-semibold shadow-lg transition-all',
        secondary: 'px-6 py-3 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 font-semibold transition-all'
    }
};

// Componente wrapper
import { FormPreRegistroCita } from './components/citas';

function FormPreRegistroTematizado() {
    return (
        <div className="bg-gradient-to-br from-indigo-50 to-pink-50 min-h-screen p-8">
            <div className="max-w-2xl mx-auto">
                <div className="bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <div className="bg-gradient-to-r from-indigo-600 to-pink-600 p-6">
                        <h1 className="text-3xl font-bold text-white">
                            ✨ Pre-registra tu cita
                        </h1>
                        <p className="text-indigo-100 mt-2">
                            Rápido, fácil y seguro
                        </p>
                    </div>
                    <div className="p-8">
                        <FormPreRegistroCita />
                    </div>
                </div>
            </div>
        </div>
    );
}
```

---

**Estos ejemplos cubren la mayoría de casos de uso reales del sistema.**  
Para más información, consulta `SISTEMA_CITAS.md` y `QUICK_START_CITAS.md`.
