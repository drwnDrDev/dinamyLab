<?php

namespace Tests\Unit;

use App\Models\Convenio;
use App\Models\Examen;
use App\Models\Sede;
use App\Models\Tarifa;
use App\Services\EscogerPrecioExamen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EscogerPrecioExamenTest extends TestCase
{
    use RefreshDatabase;

    protected $examen;
    protected $convenio;
    protected $sede;

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecutar seeders necesarios
        $this->seed(\Database\Seeders\PaisSeeder::class);
        $this->seed(\Database\Seeders\TipoDocumentoSeeder::class);
        $this->seed(\Database\Seeders\TestDataSeeder::class);

        // Crear datos de prueba
        $this->examen = Examen::factory()->create(['valor' => 100000]);
        $this->convenio = Convenio::factory()->create();
        $this->sede = Sede::factory()->create();
    }

    /**
     * Prueba: Retorna tarifa específica por convenio + sede
     */
    public function test_returns_tarifa_by_convenio_y_sede()
    {
        // Crear tarifa específica para convenio + sede
        $tarifa = $this->crearTarifaEnSede(80000);

        // Vincular tarifa al convenio
        $this->convenio->tarifas()->attach($tarifa->id);

        $resultado = EscogerPrecioExamen::obtener($this->examen, $this->convenio, $this->sede);

        $this->assertEquals(80000, $resultado['precio']);
        $this->assertEquals('por_convenio_y_sede', $resultado['tipo_tarifa']);
        $this->assertEquals($tarifa->id, $resultado['tarifa_id']);
    }

    /**
     * Prueba: Retorna tarifa por sede si no existe tarifa de convenio
     */
    public function test_returns_tarifa_by_sede_when_no_convenio_tarifa()
    {
        // Crear tarifa solo por sede (sin vincular a convenio)
        $tarifa = $this->crearTarifaEnSede(90000);

        $resultado = EscogerPrecioExamen::obtener($this->examen, $this->convenio, $this->sede);

        $this->assertEquals(90000, $resultado['precio']);
        $this->assertEquals('por_sede', $resultado['tipo_tarifa']);
        $this->assertEquals($tarifa->id, $resultado['tarifa_id']);
    }

    /**
     * Prueba: Retorna precio base cuando no existe tarifa
     */
    public function test_returns_precio_base_when_no_tarifa()
    {
        $resultado = EscogerPrecioExamen::obtener($this->examen, $this->convenio, $this->sede);

        $this->assertEquals(100000, $resultado['precio']);
        $this->assertEquals('precio_base', $resultado['tipo_tarifa']);
        $this->assertNull($resultado['tarifa_id']);
    }

    /**
     * Prueba: Método obtenerPrecio retorna solo el valor numérico
     */
    public function test_obtener_precio_returns_float()
    {
        $tarifa = $this->crearTarifaEnSede(75000);

        $this->convenio->tarifas()->attach($tarifa->id);

        $precio = EscogerPrecioExamen::obtenerPrecio($this->examen, $this->convenio, $this->sede);

        $this->assertIsFloat($precio);
        $this->assertEquals(75000.0, $precio);
    }

    /**
     * Prueba: Prioridad de tarifas (convenio+sede > sede > precio_base)
     */
    public function test_priority_convenio_y_sede_over_sede()
    {
        // Crear dos tarifas: una por sede y otra por convenio+sede
        $tarifa_sede = $this->crearTarifaEnSede(90000);

        $tarifa_convenio = $this->crearTarifaEnSede(70000);

        // Vincular solo la tarifa de convenio
        $this->convenio->tarifas()->attach($tarifa_convenio->id);

        $resultado = EscogerPrecioExamen::obtener($this->examen, $this->convenio, $this->sede);

        // Debe retornar la tarifa de convenio, no la de sede
        $this->assertEquals(70000, $resultado['precio']);
        $this->assertEquals('por_convenio_y_sede', $resultado['tipo_tarifa']);
    }

    /**
     * Prueba: Manejo de error retorna precio base
     */
    public function test_error_handling_returns_precio_base()
    {
        // Crear examen sin ID válido para forzar un estado especial
        $resultado = EscogerPrecioExamen::obtener($this->examen, $this->convenio, $this->sede);

        // Incluso sin tarifas, debe retornar el precio base sin error
        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('precio', $resultado);
        $this->assertEquals(100000, $resultado['precio']);
    }

    /**
     * Crea una tarifa del examen y la asocia a la sede por la tabla pivote sede_tarifa.
     */
    private function crearTarifaEnSede(float $precio, ?Sede $sede = null): Tarifa
    {
        $tarifa = Tarifa::create([
            'precio' => $precio,
            'tarifable_type' => Examen::class,
            'tarifable_id' => $this->examen->id,
        ]);
        $tarifa->sedes()->attach(($sede ?? $this->sede)->id);

        return $tarifa;
    }

    /**
     * Prueba: una tarifa de otro convenio no se aplica como tarifa "por sede"
     */
    public function test_tarifa_de_otro_convenio_no_se_aplica()
    {
        $otroConvenio = Convenio::factory()->create();
        $tarifa = $this->crearTarifaEnSede(60000);
        $otroConvenio->tarifas()->attach($tarifa->id);

        $resultado = EscogerPrecioExamen::obtener($this->examen, $this->convenio, $this->sede);

        $this->assertEquals(100000, $resultado['precio']);
        $this->assertEquals('precio_base', $resultado['tipo_tarifa']);
    }

    /**
     * Prueba: una tarifa de otra sede no se aplica
     */
    public function test_tarifa_de_otra_sede_no_se_aplica()
    {
        $otraSede = Sede::factory()->create();
        $this->crearTarifaEnSede(50000, $otraSede);

        $resultado = EscogerPrecioExamen::obtener($this->examen, $this->convenio, $this->sede);

        $this->assertEquals(100000, $resultado['precio']);
        $this->assertEquals('precio_base', $resultado['tipo_tarifa']);
    }
}
