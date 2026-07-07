<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reprocesar archivo JSON</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; color: #1f2937; }
        .card { border: 1px solid #d1d5db; border-radius: 10px; padding: 1.25rem; margin-bottom: 1rem; max-width: 1100px; }
        .alert { padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        form { display: flex; gap: .75rem; align-items: center; flex-wrap: wrap; }
        input[type="file"] { padding: .3rem 0; }
        button { background: #2563eb; color: white; border: 0; padding: .6rem 1rem; border-radius: 6px; cursor: pointer; }
        button.secondary { background: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: .75rem; }
        th, td { border: 1px solid #e5e7eb; padding: .5rem; font-size: .9rem; text-align: left; }
        th { background: #f3f4f6; }
        .summary { display: flex; gap: 1rem; flex-wrap: wrap; }
        .summary span { background: #eff6ff; padding: .4rem .6rem; border-radius: 999px; font-size: .9rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Procesar, visualizar y reprocesar archivo</h1>
        <p>Sube un archivo JSON, visualiza los usuarios detectados y descarga una versión en CSV.</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('reprocesar.upload.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="archivo" accept=".json,.txt" required>
            <button type="submit">Procesar archivo</button>
            <a href="{{ route('reprocesar.exportar', $filters) }}"><button type="button" class="secondary">Exportar CSV</button></a>
        </form>

        <form method="GET" action="{{ route('reprocesar.upload') }}" style="margin-top: 1rem; display:flex; gap:.75rem; flex-wrap:wrap;">
            <input type="text" name="usuario" value="{{ $filters['usuario'] ?? '' }}" placeholder="Filtrar por usuario o documento">
            <input type="text" name="procedimiento" value="{{ $filters['procedimiento'] ?? '' }}" placeholder="Filtrar por procedimiento">
            <input type="date" name="fecha" value="{{ $filters['fecha'] ?? '' }}">
            <button type="submit">Aplicar filtros</button>
            <a href="{{ route('reprocesar.upload') }}"><button type="button" class="secondary">Limpiar</button></a>
        </form>

        @if(!empty($summary))
            <div class="summary" style="margin-top:1rem;">
                <span>Total usuarios: {{ $summary['total_usuarios'] ?? 0 }}</span>
                <span>Con servicios: {{ $summary['usuarios_con_servicios'] ?? 0 }}</span>
                <span>Archivo: {{ $filename }}</span>
            </div>
        @endif
    </div>

    @if(!empty($rows))
        <div class="card">
            <h2>Vista previa</h2>
            <table>
                <thead>
                    <tr>
                        <th>Consecutivo</th>
                        <th>Tipo doc.</th>
                        <th>No. documento</th>
                        <th>Fecha</th>
                        <th>Sexo</th>
                        <th>Municipio</th>
                        <th>Tipo usuario</th>
                        <th>Servicios</th>
                        <th>Código procedimiento</th>
                        <th>Procedimiento</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            <td>{{ $row['consecutivo'] ?? '' }}</td>
                            <td>{{ $row['tipoDocumentoIdentificacion'] ?? '' }}</td>
                            <td>{{ $row['numDocumentoIdentificacion'] ?? '' }}</td>
                            <td>{{ $row['fechaNacimiento'] ?? '' }}</td>
                            <td>{{ $row['codSexo'] ?? '' }}</td>
                            <td>{{ $row['codMunicipioResidencia'] ?? '' }}</td>
                            <td>{{ $row['tipoUsuario'] ?? '' }}</td>
                            <td>{{ $row['servicios_count'] ?? 0 }}</td>
                            <td>{{ $row['codigo_procedimiento'] ?? '' }}</td>
                            <td>{{ $row['procedimiento'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>
