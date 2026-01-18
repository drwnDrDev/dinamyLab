<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Jobs\TestJob;
use App\Models\Procedimiento;
use Illuminate\Foundation\Exceptions\Renderer\Exception;
use Illuminate\Support\Facades\Cache;
use SplFileObject;

class AdministracionController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function setup()
    {
        return view('admin.setup');
    }
    public function caja()
    {

        if (!Cache::has('ForbidenGenderExamens')) {
              TestJob::dispatch();
        }
        if (Cache::has('ForbidenGenderExamens')) {
            $ForbidenGenderExamens = Cache::get('ForbidenGenderExamens');
        } else {
            $ForbidenGenderExamens = [];
        }

        if (Cache::has('Paises')) {
            $paises = Cache::get('Paises');
        } else {
              TestJob::dispatch();
              $paises = Cache::get('Paises')?? ['no hay paises'];
        }
        if (Cache::has('Municipios')) {
            $municipios = Cache::get('Municipios');
        } else {
          TestJob::dispatch();
            $municipios = Cache::get('Municipios') ?? ['no hay municipios'];
        }
        if (Cache::has('Eps')) {
            $eps = Cache::get('EPS');
        } else {
             TestJob::dispatch();
            $eps = Cache::get('EPS') ?? ['no hay eps'];
        }

        $NoExamenes = $ForbidenGenderExamens ? $ForbidenGenderExamens : ['no hay examenes prohibidos'];



        return view('admin.caja', compact('NoExamenes', 'paises', 'municipios', 'eps'));
    }

    public function rips()
    {

    $filePath = base_path('resources/utils/tablas/diciembre/doctora_sandra/diciembre.csv');
    if (!file_exists($filePath)) {
        return response()->json(['error' => 'Archivo no encontrado.'], 404);
    }
    $file = new SplFileObject($filePath, 'r');
    $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);


if ($file === false) {
    return response()->json(['error' => 'No se pudo leer el archivo.'], 500);
}

$lista = $file->fread($file->getSize());

$recordsToInsert = [];
try {
    $file->rewind(); // Asegura que estamos al inicio del archivo
    while (!$file->eof()) {
        $row = $file->fgetcsv();
        if ($row === false) {
            continue; // Salta filas vacías o errores de lectura
        }
        // Procesa la fila (por ejemplo, valida o transforma los datos)
        $recordsToInsert[] = $row;
    }
} catch (Exception $e) {
    return response()->json(['error' => 'Error al procesar el archivo: '], 500);
}
$recordsToInsert = array_slice($recordsToInsert, 1);




$listaProcedimientos = array_map(function($line) {
    $data = explode(";", $line[0]);
    return array(
        "tipoDocumentoIdentificacion" => $data[0],
        "numDocumentoIdentificacion" => $data[1],
        "fechaNacimiento" => $data[5],
        "sexo" => $data[6],
        "fechaProcedimiento" => $data[15],
        "factura" =>null,
        "CUP" => null,
        "CIE10" => $data[13],
        "paisOrigen" => $data[2]=="VENEZUELA" ?"862":"170",
        "CupProcedimiento" => $data[12]
    );
}, $recordsToInsert);



foreach ($listaProcedimientos as $procedimiento) {

    // Buscar si el usuario ya existe
    $key = $procedimiento['tipoDocumentoIdentificacion'] . '-' . $procedimiento['numDocumentoIdentificacion'];
    if (!isset($usuariosMap)) $usuariosMap = [];
    if (!isset($usuariosMap[$key])) {
        $usuariosMap[$key] = array(
            'tipoDocumentoIdentificacion' => $procedimiento['tipoDocumentoIdentificacion'],
            'numDocumentoIdentificacion' => $procedimiento['numDocumentoIdentificacion'],
            "tipoUsuario" => "12",
            "fechaNacimiento" => $procedimiento['fechaNacimiento'] ,
            "codSexo" => $procedimiento['sexo'],
            "codPaisResidencia" => "170",
            "codMunicipioResidencia" => "11001",
            "codZonaTerritorialResidencia" => "02",
            "incapacidad" => "NO",
            "codPaisOrigen" => $procedimiento['paisOrigen'],
            "consecutivo" => count($usuariosMap) + 1,
            "servicios" => array(
               // "consultas" => array(),
                "procedimientos" => array()
            ),
        );
    }


    // $usuariosMap[$key]['servicios']['consultas'][] = array(
    //     "codPrestador" => "110010219801",
    //     "fechaInicioAtencion" => $procedimiento['fechaProcedimiento'],
    //     "numAutorizacion" => "",
    //     "codConsulta" => $procedimiento['CUP'],
    //     "viaIngresoServicioSalud" => "01",// Demanda espontánea
    //     "modalidadGrupoServicioTecSal" => "01",// Intramural
    //     "grupoServicios" => "01",//consulta externa
    //     "codServicio" => 334,//odontologia general
    //     "finalidadTecnologiaSalud" => "15",//16 tratamiento 15 diagnostico
    //     "causaMotivoAtencion" => "38",//38 enfermedad general
    //     "codDiagnosticoPrincipal" => $procedimiento['CIE10'],
    //     "codDiagnosticoRelacionado1" => null,
    //     "codDiagnosticoRelacionado2" => null,
    //     "codDiagnosticoRelacionado3" => null,
    //     "tipoDiagnosticoPrincipal" => "01",//01 impresion diagnostica   02 confirmado nuevo  03 confirmado repetido
    //     "tipoDocumentoIdentificacion" => "CC",
    //     "numDocumentoIdentificacion" => "63362234",
    //     "vrServicio" => 0,
    //     "conceptoRecaudo" => "05",
    //     "valorPagoModerador" => 0,
    //     "numFEVPagoModerador" => "",
    //     "consecutivo" => count($usuariosMap[$key]['servicios']['consultas']) + 1
    // );

    $usuariosMap[$key]['servicios']['procedimientos'][] = array(
                    "codPrestador" => "110010219801",
                    "fechaInicioAtencion" => $procedimiento['fechaProcedimiento'],
                    "idMIPRES" => "",
                    "numAutorizacion" => $procedimiento['factura'],
                    "codProcedimiento" => $procedimiento['CupProcedimiento'],
                    "viaIngresoServicioSalud" => "01",//demanda expontanea
                    "modalidadGrupoServicioTecSal" => "01", //Intramural
                    "grupoServicios" => "01",//01 consulta externa 02 APOYO DIAGNOSTICO Y COMPLEMENTACION TERAPEUTICA
                    "codServicio" => 334,//odontologia general
                    "finalidadTecnologiaSalud" => "16",
                    "tipoDocumentoIdentificacion" => "CC",
                    "numDocumentoIdentificacion" => "63362234",
                    "codDiagnosticoPrincipal" => $procedimiento['CIE10'],
                    "codDiagnosticoRelacionado" => null,
                    "codComplicacion" => null,
                    "vrServicio" => 0,
                    "conceptoRecaudo" => "05",
                    "valorPagoModerador" => 0,
                    "numFEVPagoModerador" => "",
                    "consecutivo" => count($usuariosMap[$key]['servicios']['procedimientos']) + 1
    );

    $usuarios = array_values($usuariosMap);



}

//descargar el archivo JSON
if (!empty($usuarios)) {
    $json = json_encode(array(
           "numDocumentoIdObligado"=> "63362234",
            "numFactura"=> null,
            "tipoNota"=> "RS",
            "numNota"=> "122025",
        "usuarios" => $usuarios
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $fileName = 'usuarios.json';

    return response($json, 200)
        ->header('Content-Type', 'application/json')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
} else {
    return response()->json(['error' => 'No se encontraron usuarios.'], 404);
}


$usuarios = array_map(function($procedimiento) {
    return array(
        "tipoDocumentoIdentificacion" => $procedimiento['tID'],
        "numDocumentoIdentificacion" => $procedimiento['numero_doc'],
        "tipoUsuario" => "12",
        "fechaNacimiento" => $procedimiento['fecha_nacimiento'],
        "codSexo" => $procedimiento['sexo'],
        "codPaisResidencia" => "170",
        "codMunicipioResidencia" => "11001",
        "codZonaTerritorialResidencia" => "01",
        "incapacidad" => "NO",
        "codPaisOrigen" => "170",
        "consecutivo" => 1,
        "servicios" => array(
            "procedimientos" => array(
                array(
                    "codPrestador" => "110010219801",
                    "fechaInicioAtencion" => $procedimiento['fecha_procedimiento'],
                    "idMIPRES" => null,
                    "numAutorizacion" => null,
                    "codProcedimiento" => $procedimiento['CUP'],
                    "viaIngresoServicioSalud" => "01",//demanda expontanea
                    "modalidadGrupoServicioTecSal" => "01", //Intramural
                    "grupoServicios" => "03",
                    "codServicio" => 328,
                    "finalidadTecnologiaSalud" => "15",
                    "tipoDocumentoIdentificacion" => "CC",
                    "numDocumentoIdentificacion" => "63362234",
                    "codDiagnosticoPrincipal" => "Z017",
                    "codDiagnosticoRelacionado" => null,
                    "codComplicacion" => null,
                    "vrServicio" => 0,
                    "conceptoRecaudo" => "05",
                    "valorPagoModerador" => 0,
                    "numFEVPagoModerador" => "",
                    "consecutivo" => 1
                )
            )
        )
    );
}, $procedimientos);

//descargar el archivo JSON
if (!empty($usuarios)) {
    $json = json_encode(array(
           "numDocumentoIdObligado"=> "63362234",
            "numFactura"=> null,
            "tipoNota"=> "RS",
            "numNota"=> "6532",
        "usuarios" => $usuarios
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $fileName = 'usuarios.json';

    return response($json, 200)
        ->header('Content-Type', 'application/json')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
} else {
    return response()->json(['error' => 'No se encontraron usuarios.'], 404);
}

    }


public function json_rips(Request $request)
{
    $procedimientos = Procedimiento::with(['persona', 'examen'])
        ->whereBetween('fecha_procedimiento', ['2025-07-01', '2025-07-31'])
        ->where('prestador_id', 2)
        ->get();

    $procedimientosPorPersona = $procedimientos->groupBy('persona_id');

    $usuarios = [];
    foreach ($procedimientosPorPersona as $usuario) {
        $usuarios[] = [
            "tipoDocumentoIdentificacion" =>  $usuario->first()->persona->tID,
            "numDocumentoIdentificacion" =>  $usuario->first()->persona->numero_doc,
            "tipoUsuario" => "12",
            "fechaNacimiento" => $usuario->first()->persona->fecha_nacimiento,
            "codSexo" => $usuario->first()->persona->sexo,
            "codPaisResidencia" => "170",
            "codMunicipioResidencia" => str_pad($usuario->first()->persona->direccion->municipio_id, 5, '0', STR_PAD_LEFT),
            "codZonaTerritorialResidencia" => "01",
            "incapacidad" => "NO",
            "codPaisOrigen" => "170",
            "consecutivo" => 1,
            "servicios" => [
                "procedimientos" => $usuario->map(function($procedimiento) {
                    return [
                        "codPrestador" => "110010822703",
                        "fechaInicioAtencion" => $procedimiento->fecha_procedimiento->format('Y-m-d H:i'),
                        "idMIPRES" => null,
                        "numAutorizacion" => $procedimiento->factura,
                        "codProcedimiento" => $procedimiento->examen->CUP,
                        "viaIngresoServicioSalud" => "01",
                        "modalidadGrupoServicioTecSal" => "01",
                        "grupoServicios" => "03",
                        "codServicio" => 334,
                        "finalidadTecnologiaSalud" => "15",
                        "tipoDocumentoIdentificacion" => "CC",
                        "numDocumentoIdentificacion" => "51934571",
                        "codDiagnosticoPrincipal" => "Z017",
                        "codDiagnosticoRelacionado" => null,
                        "codComplicacion" => null,
                        "vrServicio" => 0,
                        "conceptoRecaudo" => "05",
                        "valorPagoModerador" => 0,
                        "numFEVPagoModerador" => "",
                        "consecutivo" => 1
                    ];
                })->values()->toArray()
            ]
        ];
    }

    return response()->json([
        'usuarios' => $usuarios
    ]);
}

}
