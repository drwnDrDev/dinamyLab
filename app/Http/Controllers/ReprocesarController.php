<?php

namespace App\Http\Controllers;

use App\Models\CodigoCup;
use Illuminate\Http\Request;

class ReprocesarController extends Controller
{
    public function showUploadForm(Request $request)
    {
        $rows = session('reprocesar.rows', []);
        $summary = session('reprocesar.summary', []);
        $filename = session('reprocesar.filename', 'bitacora.json');
        $filters = [
            'usuario' => trim((string) $request->query('usuario', '')),
            'procedimiento' => trim((string) $request->query('procedimiento', '')),
            'fecha' => trim((string) $request->query('fecha', '')),
        ];

        $rows = $this->aplicarFiltros($rows, $filters);

        return view('reprocesar.upload', compact('rows', 'summary', 'filename', 'filters'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'archivo' => ['required', 'file', 'mimes:json,txt'],
        ]);

        $archivo = $request->file('archivo');
        $contenido = file_get_contents($archivo->getRealPath());

        if ($contenido === false || trim($contenido) === '') {
            return redirect()->back()->with('error', 'El archivo está vacío.');
        }

        $datos = json_decode($contenido, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->with('error', 'El archivo no tiene un JSON válido.');
        }

        [$rows, $summary] = $this->normalizarDatos($datos);

        session([
            'reprocesar.rows' => $rows,
            'reprocesar.summary' => $summary,
            'reprocesar.filename' => $archivo->getClientOriginalName(),
        ]);

        return redirect()->route('reprocesar.upload')->with('success', 'Archivo procesado correctamente.');
    }

    public function reprocesar(Request $request)
    {
        return $this->upload($request);
    }

    public function exportar(Request $request)
    {
        $rows = session('reprocesar.rows', []);
        $filters = [
            'usuario' => trim((string) $request->query('usuario', '')),
            'procedimiento' => trim((string) $request->query('procedimiento', '')),
            'fecha' => trim((string) $request->query('fecha', '')),
        ];

        $rows = $this->aplicarFiltros($rows, $filters);

        if (empty($rows)) {
            return redirect()->route('reprocesar.upload')->with('error', 'No hay datos para exportar.');
        }

        $filename = session('reprocesar.filename', 'bitacora.json');
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $downloadName = $baseName . '_reprocesado.csv';
        $headers = [
            'consecutivo',
            'tipoDocumentoIdentificacion',
            'numDocumentoIdentificacion',
            'fechaNacimiento',
            'fecha',
            'codSexo',
            'codPaisResidencia',
            'codMunicipioResidencia',
            'codZonaTerritorialResidencia',
            'incapacidad',
            'codPaisOrigen',
            'tipoUsuario',
            'servicios_count',
            'codigo_procedimiento',
            'procedimiento',
        ];

        $callback = function () use ($rows, $headers): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers, ';');

            foreach ($rows as $row) {
                $linea = [];
                foreach ($headers as $header) {
                    $linea[] = $row[$header] ?? '';
                }

                fputcsv($handle, $linea, ';');
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $downloadName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    private function normalizarDatos(array $datos): array
    {
        $usuarios = [];

        if (isset($datos['usuarios']) && is_array($datos['usuarios'])) {
            $usuarios = $datos['usuarios'];
        } elseif (is_array($datos)) {
            $usuarios = $datos;
        } else {
            $usuarios = [$datos];
        }

        $rows = [];

        foreach ($usuarios as $index => $usuario) {
            if (!is_array($usuario)) {
                continue;
            }

            $servicios = $usuario['servicios'] ?? [];
            $serviciosCount = is_array($servicios) ? count($servicios) : 0;
            $procedimientosPorUsuario = $this->extraerProcedimientosPorUsuario($servicios);
            $fecha = $usuario['fechaNacimiento'] ?? $usuario['fecha'] ?? '';

            foreach ($procedimientosPorUsuario as $procedimiento) {
                $rows[] = [
                    'consecutivo' => $usuario['consecutivo'] ?? ($index + 1),
                    'tipoDocumentoIdentificacion' => $usuario['tipoDocumentoIdentificacion'] ?? 'CC',
                    'numDocumentoIdentificacion' => $usuario['numDocumentoIdentificacion'] ?? '',
                    'fechaNacimiento' => $fecha,
                    'fecha' => $fecha,
                    'codSexo' => $usuario['codSexo'] ?? '',
                    'codPaisResidencia' => $usuario['codPaisResidencia'] ?? '',
                    'codMunicipioResidencia' => $usuario['codMunicipioResidencia'] ?? '',
                    'codZonaTerritorialResidencia' => $usuario['codZonaTerritorialResidencia'] ?? '',
                    'incapacidad' => $usuario['incapacidad'] ?? '',
                    'codPaisOrigen' => $usuario['codPaisOrigen'] ?? '',
                    'tipoUsuario' => $usuario['tipoUsuario'] ?? '',
                    'servicios_count' => $serviciosCount,
                    'codigo_procedimiento' => $procedimiento['codigo'],
                    'procedimiento' => $procedimiento['nombre'],
                ];
            }
        }

        $summary = [
            'total_usuarios' => count(array_unique(array_column($rows, 'numDocumentoIdentificacion'))),
            'usuarios_con_servicios' => count(array_filter($rows, static fn ($row) => (int) $row['servicios_count'] > 0)),
            'total_procedimientos' => count($rows),
            'tipos_documento' => $this->agruparConteo($rows, 'tipoDocumentoIdentificacion'),
            'sexos' => $this->agruparConteo($rows, 'codSexo'),
        ];

        return [$rows, $summary];
    }

    private function extraerProcedimientosPorUsuario(array $servicios): array
    {
        $procedimientos = [];

        foreach ($servicios as $servicio) {
            if (!is_array($servicio)) {
                continue;
            }

            $procedimientos = array_merge($procedimientos, $this->extraerProcedimientos($servicio));
        }

        $procedimientosFiltrados = array_values(array_filter($procedimientos, static function ($item): bool {
            return is_array($item) && (!empty($item['codigo']) || !empty($item['nombre']));
        }));

        return $this->deduplicarProcedimientos($procedimientosFiltrados);
    }

    private function deduplicarProcedimientos(array $procedimientos): array
    {
        $resultado = [];
        $visitadas = [];

        foreach ($procedimientos as $procedimiento) {
            if (!is_array($procedimiento)) {
                continue;
            }

            $codigo = trim((string) ($procedimiento['codigo'] ?? ''));
            $nombre = CodigoCup::find($codigo)?->nombre ?? trim((string) ($procedimiento['nombre'] ?? ''));
            $clave = mb_strtolower($codigo . '|' . $nombre);

            if ($clave === '' || isset($visitadas[$clave])) {
                continue;
            }

            $visitadas[$clave] = true;
            $resultado[] = [
                'codigo' => $codigo,
                'nombre' => $nombre,
            ];
        }

        return $resultado;
    }

    private function extraerProcedimientos(array $item): array
    {
        $resultado = [];

        if ($this->pareceProcedimiento($item)) {
            $resultado[] = $this->normalizarProcedimiento($item);
        }

        $camposProcedimiento = ['procedimientos', 'procedimiento', 'procedures', 'procedure'];

        foreach ($camposProcedimiento as $campo) {
            if (!array_key_exists($campo, $item)) {
                continue;
            }

            $valor = $item[$campo];

            if (is_array($valor)) {
                foreach ($valor as $subvalor) {
                    if (is_array($subvalor)) {
                        $resultado = array_merge($resultado, $this->extraerProcedimientos($subvalor));
                    } elseif (is_string($subvalor) && trim($subvalor) !== '') {
                        $resultado[] = ['codigo' => '', 'nombre' => trim($subvalor)];
                    }
                }
            } elseif (is_string($valor) && trim($valor) !== '') {
                $resultado[] = ['codigo' => '', 'nombre' => trim($valor)];
            }
        }

        foreach ($item as $valor) {
            if (is_array($valor)) {
                $resultado = array_merge($resultado, $this->extraerProcedimientos($valor));
            }
        }

        return array_values(array_filter($resultado, static fn ($item) => is_array($item) && (!empty($item['codigo']) || !empty($item['nombre']))));
    }

    private function pareceProcedimiento(array $item): bool
    {
        $claves = array_map('strtolower', array_keys($item));
        $texto = implode('|', $claves);

        return preg_match('/codigo|codigoproced|procedimiento|descripcion|nombre|detalle/', $texto) === 1;
    }

    private function normalizarProcedimiento(array $item): array
    {
        $codigo = $item['codigo'] ?? $item['codigoProcedimiento'] ?? $item['codProcedimiento'] ?? $item['id'] ?? $item['idProcedimiento'] ?? $item['codigoProc'] ?? '';
        $nombre = $item['nombre'] ?? $item['descripcion'] ?? $item['procedimiento'] ?? $item['nombreProcedimiento'] ?? $item['detalle'] ?? '';

        if ($codigo !== '' && $nombre !== '') {
            return [
                'codigo' => trim((string) $codigo),
                'nombre' => trim((string) $nombre),
            ];
        }

        if ($codigo !== '') {
            return [
                'codigo' => trim((string) $codigo),
                'nombre' => trim((string) $codigo),
            ];
        }

        if ($nombre !== '') {
            return [
                'codigo' => '',
                'nombre' => trim((string) $nombre),
            ];
        }

        return [
            'codigo' => '',
            'nombre' => 'Procedimiento',
        ];
    }

    private function aplicarFiltros(array $rows, array $filters): array
    {
        if (empty($rows)) {
            return [];
        }

        $usuario = mb_strtolower(trim((string) ($filters['usuario'] ?? '')));
        $procedimiento = mb_strtolower(trim((string) ($filters['procedimiento'] ?? '')));
        $fecha = trim((string) ($filters['fecha'] ?? ''));

        return array_values(array_filter($rows, static function (array $row) use ($usuario, $procedimiento, $fecha): bool {
            $coincideUsuario = $usuario === '' || mb_stripos((string) ($row['numDocumentoIdentificacion'] ?? ''), $usuario) !== false || mb_stripos((string) ($row['tipoDocumentoIdentificacion'] ?? ''), $usuario) !== false;
            $coincideProcedimiento = $procedimiento === '' || mb_stripos((string) ($row['codigo_procedimiento'] ?? ''), $procedimiento) !== false || mb_stripos((string) ($row['procedimiento'] ?? ''), $procedimiento) !== false;
            $coincideFecha = $fecha === '' || mb_stripos((string) ($row['fecha'] ?? ''), $fecha) !== false;

            return $coincideUsuario && $coincideProcedimiento && $coincideFecha;
        }));
    }

    private function agruparConteo(array $rows, string $campo): array
    {
        $conteo = [];

        foreach ($rows as $row) {
            $valor = (string) ($row[$campo] ?? '');
            $conteo[$valor] = ($conteo[$valor] ?? 0) + 1;
        }

        ksort($conteo);

        return $conteo;
    }

    public function downloadTemplate()
    {
        $templatePath = storage_path('app/templates/template.json');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'El archivo de plantilla no existe.');
        }

        return response()->download($templatePath, 'template.json', [
            'Content-Type' => 'application/json',
        ]);
    }
}
