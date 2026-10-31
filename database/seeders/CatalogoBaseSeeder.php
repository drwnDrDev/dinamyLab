<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Catálogo común: idéntico en todas las instancias.
 *
 * No contiene datos de ningún cliente (empresa, sede, usuarios, tarifas).
 */
class CatalogoBaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesYPermisosSeeder::class,
            // MunicipioSeeder inserta también el catálogo de países; no llamar
            // a PaisSeeder por separado (viola paises.codigo_iso_2 único).
            MunicipioSeeder::class,
            TipoDocumentoSeeder::class,
            EPSsedeer::class,
            FinalidadSeeder::class,
            CUPSeeder::class,
            RelacionesCupSeeder::class,
            // AplicabilidadSexoCupSeeder NO se incluye: llama DB::rollBack() sin
            // beginTransaction() y, al fallar, revierte la transacción completa del
            // test (vacía paises y todo lo anterior). Nunca estuvo en DatabaseSeeder.
            // Bug preexistente, pendiente de arreglo propio.
            ModalidadAtencionSeeder::class,
            ServicioHabilitadoSeeder::class,
            ViaIngresoSeeder::class,
            CausaExternaSeeder::class,
            MediosDePagoSeeder::class,
            DefaultOptionSeeder::class,
            SetupSeeder::class,
            ExamenSeeder::class,
        ]);
    }
}
