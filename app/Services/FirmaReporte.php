<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FirmaReporte
{
    private const MAX_ANCHO = 600;

    private const MAX_ALTO = 240;

    private const MAX_PIXELES_ORIGEN = 4_000_000;

    private const MARGEN = 6;

    /**
     * Cache por petición: la firma compuesta no cambia mientras no cambien los bytes ni la marca.
     *
     * @var array<string, string>
     */
    private array $cache = [];

    /**
     * Compone la firma sobre un fondo blanco y le sobrepone la marca del reporte.
     */
    public function componer(?string $ruta, string $marca): ?string
    {
        $origen = null;
        $lienzo = null;

        try {
            $contenido = $this->leer($ruta);

            if ($contenido === null) {
                Log::warning('No se encontró la firma del reporte.', ['ruta' => $ruta]);

                return null;
            }

            $clave = sha1($contenido).sha1($marca);

            if (isset($this->cache[$clave])) {
                return $this->cache[$clave];
            }

            $info = @getimagesizefromstring($contenido);
            $mime = $info['mime'] ?? null;

            if (! in_array($mime, ['image/png', 'image/jpeg'], true)) {
                Log::warning('La firma del reporte no es PNG/JPEG.', ['ruta' => $ruta, 'mime' => $mime]);

                return null;
            }

            if (($info[0] ?? 0) * ($info[1] ?? 0) > self::MAX_PIXELES_ORIGEN) {
                Log::warning('La firma del reporte excede el tamaño permitido.', [
                    'ruta' => $ruta,
                    'ancho' => $info[0] ?? null,
                    'alto' => $info[1] ?? null,
                ]);

                return null;
            }

            $origen = @imagecreatefromstring($contenido);

            if (! $origen) {
                return null;
            }

            [$ancho, $alto] = $this->dimensiones(imagesx($origen), imagesy($origen));

            $lienzo = imagecreatetruecolor($ancho + self::MARGEN * 2, $alto + self::MARGEN * 2);

            if (! $lienzo) {
                return null;
            }

            imagealphablending($lienzo, true);
            imagefill($lienzo, 0, 0, imagecolorallocate($lienzo, 255, 255, 255));
            imagecopyresampled(
                $lienzo,
                $origen,
                self::MARGEN,
                self::MARGEN,
                0,
                0,
                $ancho,
                $alto,
                imagesx($origen),
                imagesy($origen)
            );

            if (! $this->marcar($lienzo, $marca)) {
                return null;
            }

            ob_start();

            try {
                imagepng($lienzo);
                $png = ob_get_clean();
            } catch (\Throwable $e) {
                ob_end_clean();

                throw $e;
            }

            if (! is_string($png) || $png === '') {
                return null;
            }

            return $this->cache[$clave] = 'data:image/png;base64,'.base64_encode($png);
        } catch (\Throwable $e) {
            Log::warning('No se pudo componer la firma del reporte.', [
                'error' => $e->getMessage(),
            ]);

            return null;
        } finally {
            if ($origen instanceof \GdImage) {
                imagedestroy($origen);
            }

            if ($lienzo instanceof \GdImage) {
                imagedestroy($lienzo);
            }
        }
    }

    private function leer(?string $ruta): ?string
    {
        if (! filled($ruta)) {
            return null;
        }

        $ruta = str_replace('\\', '/', $ruta);

        if (Str::startsWith($ruta, '/') || in_array('..', explode('/', $ruta), true)) {
            return null;
        }

        $disco = Storage::disk('local');

        if (! $disco->exists($ruta)) {
            $ruta = 'firmas/'.$ruta;

            if (! $disco->exists($ruta)) {
                return null;
            }
        }

        return $disco->get($ruta);
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function dimensiones(int $ancho, int $alto): array
    {
        $escala = min(
            1,
            self::MAX_ANCHO / max(1, $ancho),
            self::MAX_ALTO / max(1, $alto),
        );

        return [
            max(1, (int) round($ancho * $escala)),
            max(1, (int) round($alto * $escala)),
        ];
    }

    private function marcar(\GdImage $lienzo, string $marca): bool
    {
        $fuente = resource_path('fonts/DejaVuSans.ttf');

        if (! is_readable($fuente)) {
            Log::warning('No se encontró la fuente para la marca de agua de la firma.', [
                'fuente' => $fuente,
            ]);

            return false;
        }

        $color = imagecolorallocatealpha($lienzo, 90, 90, 90, 105);
        $ancho = imagesx($lienzo);
        $alto = imagesy($lienzo);

        for ($y = 18; $y < $alto + 90; $y += 70) {
            for ($x = -80; $x < $ancho + 120; $x += 260) {
                if (imagettftext($lienzo, 9, -30, $x, $y, $color, $fuente, $marca) === false) {
                    Log::warning('No se pudo escribir la marca de agua de la firma.', [
                        'fuente' => $fuente,
                    ]);

                    return false;
                }
            }
        }

        return true;
    }
}
