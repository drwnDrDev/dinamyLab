<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Jobs\TestJob;
use Illuminate\Support\Facades\Cache;

class AdministracionController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
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
 $procedimientos = array(
  array('tID' => 'CC','numero_doc' => '1012321353','sexo' => 'F','fecha_nacimiento' => '1986-04-28','fecha_procedimiento' => '2025-07-22','factura' => '6712','created_at' => '2025-07-22 16:36:54','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000734067','sexo' => 'M','fecha_nacimiento' => '2001-07-17','fecha_procedimiento' => '2025-07-02','factura' => '6552','created_at' => '2025-07-11 11:03:42','CUP' => '901305','valor_examen' => '6000.00'),
  array('tID' => 'CC','numero_doc' => '1013605748','sexo' => 'F','fecha_nacimiento' => '1989-06-14','fecha_procedimiento' => '2025-07-02','factura' => '6556','created_at' => '2025-07-02 16:31:59','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'RC','numero_doc' => '1012475653','sexo' => 'M','fecha_nacimiento' => '2022-04-21','fecha_procedimiento' => '2025-07-18','factura' => '6674','created_at' => '2025-07-18 17:10:00','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012372623','sexo' => 'F','fecha_nacimiento' => '1991-01-11','fecha_procedimiento' => '2025-07-24','factura' => '6736','created_at' => '2025-07-24 11:10:43','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000120696','sexo' => 'F','fecha_nacimiento' => '2001-02-27','fecha_procedimiento' => '2025-07-24','factura' => '6750','created_at' => '2025-07-24 18:54:04','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '39637355','sexo' => 'F','fecha_nacimiento' => '1963-09-25','fecha_procedimiento' => '2025-07-23','factura' => '6723','created_at' => '2025-07-23 18:26:13','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000118769','sexo' => 'F','fecha_nacimiento' => '2000-02-04','fecha_procedimiento' => '2025-07-22','factura' => '6714','created_at' => '2025-07-22 16:59:27','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000121704','sexo' => 'F','fecha_nacimiento' => '2001-08-22','fecha_procedimiento' => '2025-07-03','factura' => '6567','created_at' => '2025-07-03 16:52:39','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1026287820','sexo' => 'F','fecha_nacimiento' => '1994-03-14','fecha_procedimiento' => '2025-07-31','factura' => '6840','created_at' => '2025-07-31 08:40:05','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1007773387','sexo' => 'F','fecha_nacimiento' => '2001-01-05','fecha_procedimiento' => '2025-07-08','factura' => '6583','created_at' => '2025-07-08 14:33:43','CUP' => '901107','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1028881739','sexo' => 'F','fecha_nacimiento' => '2006-01-11','fecha_procedimiento' => '2025-07-26','factura' => '6783','created_at' => '2025-07-26 17:14:14','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1141715054','sexo' => 'F','fecha_nacimiento' => '2007-02-27','fecha_procedimiento' => '2025-07-21','factura' => '6696','created_at' => '2025-07-21 11:59:56','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012395805','sexo' => 'F','fecha_nacimiento' => '1993-01-06','fecha_procedimiento' => '2025-07-18','factura' => '6683','created_at' => '2025-07-18 16:51:22','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '37811741','sexo' => 'F','fecha_nacimiento' => '1947-02-12','fecha_procedimiento' => '2025-07-31','factura' => '6838','created_at' => '2025-07-31 15:46:39','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '52634025','sexo' => 'F','fecha_nacimiento' => '1972-09-20','fecha_procedimiento' => '2025-07-31','factura' => '6851','created_at' => '2025-07-31 17:52:58','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'RC','numero_doc' => '1240694377','sexo' => 'M','fecha_nacimiento' => '2021-03-12','fecha_procedimiento' => '2025-07-09','factura' => '2931','created_at' => '2025-07-09 12:10:37','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '28307267','sexo' => 'F','fecha_nacimiento' => '1953-04-15','fecha_procedimiento' => '2025-07-26','factura' => '34626','created_at' => '2025-07-27 22:57:34','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000134156','sexo' => 'F','fecha_nacimiento' => '1997-04-10','fecha_procedimiento' => '2025-07-01','factura' => '6539','created_at' => '2025-07-01 16:41:36','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '52860474','sexo' => 'F','fecha_nacimiento' => '1983-01-24','fecha_procedimiento' => '2025-07-12','factura' => '6622','created_at' => '2025-07-12 11:50:49','CUP' => '903841','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000858137','sexo' => 'F','fecha_nacimiento' => '2002-12-02','fecha_procedimiento' => '2025-07-26','factura' => '6787','created_at' => '2025-07-26 13:50:57','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012460922','sexo' => 'F','fecha_nacimiento' => '1999-03-22','fecha_procedimiento' => '2025-07-24','factura' => '6745','created_at' => '2025-07-24 15:45:32','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012421984','sexo' => 'F','fecha_nacimiento' => '1995-05-25','fecha_procedimiento' => '2025-07-01','factura' => '6535','created_at' => '2025-07-01 12:24:01','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '39289077','sexo' => 'F','fecha_nacimiento' => '1983-09-28','fecha_procedimiento' => '2025-07-01','factura' => '6537','created_at' => '2025-07-01 12:58:49','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1003764920','sexo' => 'F','fecha_nacimiento' => '2001-11-26','fecha_procedimiento' => '2025-07-10','factura' => '6612','created_at' => '2025-07-10 15:50:51','CUP' => '907106','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012450953','sexo' => 'F','fecha_nacimiento' => '1998-03-17','fecha_procedimiento' => '2025-07-16','factura' => '6654','created_at' => '2025-07-16 13:17:01','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1007642704','sexo' => 'M','fecha_nacimiento' => '2000-10-03','fecha_procedimiento' => '2025-07-30','factura' => '6821','created_at' => '2025-07-30 17:57:56','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012348580','sexo' => 'F','fecha_nacimiento' => '1988-10-09','fecha_procedimiento' => '2025-07-24','factura' => '6733','created_at' => '2025-07-24 16:23:25','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1007427928','sexo' => 'F','fecha_nacimiento' => '1987-12-14','fecha_procedimiento' => '2025-07-18','factura' => '6670','created_at' => '2025-07-18 18:47:45','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000352883','sexo' => 'F','fecha_nacimiento' => '2003-02-23','fecha_procedimiento' => '2025-07-31','factura' => '6850','created_at' => '2025-07-31 17:14:06','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1005853994','sexo' => 'F','fecha_nacimiento' => '2003-02-08','fecha_procedimiento' => '2025-07-29','factura' => '6814','created_at' => '2025-07-29 10:09:49','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012394896','sexo' => 'F','fecha_nacimiento' => '1991-09-22','fecha_procedimiento' => '2025-07-18','factura' => '6671','created_at' => '2025-07-18 08:19:07','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1028887628','sexo' => 'F','fecha_nacimiento' => '2009-06-04','fecha_procedimiento' => '2025-07-17','factura' => '6666','created_at' => '2025-07-17 14:55:48','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1116794399','sexo' => 'F','fecha_nacimiento' => '1992-09-20','fecha_procedimiento' => '2025-07-09','factura' => '6601','created_at' => '2025-07-09 11:22:41','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1005000356','sexo' => 'F','fecha_nacimiento' => '2003-05-03','fecha_procedimiento' => '2025-07-03','factura' => '6564','created_at' => '2025-07-03 13:07:48','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1193420206','sexo' => 'F','fecha_nacimiento' => '2001-04-30','fecha_procedimiento' => '2025-07-28','factura' => '6798','created_at' => '2025-07-28 12:12:50','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'RC','numero_doc' => '1074835016','sexo' => 'M','fecha_nacimiento' => '2024-12-19','fecha_procedimiento' => '2025-07-24','factura' => '6735','created_at' => '2025-07-25 10:15:36','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1013115649','sexo' => 'F','fecha_nacimiento' => '2007-04-26','fecha_procedimiento' => '2025-07-01','factura' => '6532','created_at' => '2025-07-01 10:25:54','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1193525718','sexo' => 'F','fecha_nacimiento' => '1997-12-15','fecha_procedimiento' => '2025-07-01','factura' => '6534','created_at' => '2025-07-01 10:38:48','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '100122484','sexo' => 'F','fecha_nacimiento' => '2001-03-19','fecha_procedimiento' => '2025-07-01','factura' => '6536','created_at' => '2025-07-01 12:55:59','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1018511743','sexo' => 'F','fecha_nacimiento' => '1999-06-12','fecha_procedimiento' => '2025-07-01','factura' => '6538','created_at' => '2025-07-01 14:03:48','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '10050722','sexo' => 'M','fecha_nacimiento' => '1983-07-02','fecha_procedimiento' => '2025-07-01','factura' => '6533','created_at' => '2025-07-01 15:00:08','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012408771','sexo' => 'F','fecha_nacimiento' => '1994-03-13','fecha_procedimiento' => '2025-07-01','factura' => '6541','created_at' => '2025-07-01 18:53:21','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '52956000','sexo' => 'F','fecha_nacimiento' => '1983-09-12','fecha_procedimiento' => '2025-07-02','factura' => '6542','created_at' => '2025-07-02 07:53:01','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1030635890','sexo' => 'M','fecha_nacimiento' => '1994-01-29','fecha_procedimiento' => '2025-07-02','factura' => '6543','created_at' => '2025-07-10 07:40:30','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1003203890','sexo' => 'M','fecha_nacimiento' => '1994-06-05','fecha_procedimiento' => '2025-07-02','factura' => '6544','created_at' => '2025-07-10 07:44:38','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1193272372','sexo' => 'M','fecha_nacimiento' => '1998-09-05','fecha_procedimiento' => '2025-07-02','factura' => '6545','created_at' => '2025-07-11 09:49:33','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '80144240','sexo' => 'M','fecha_nacimiento' => '1984-06-24','fecha_procedimiento' => '2025-07-02','factura' => '6547','created_at' => '2025-07-11 10:20:16','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '9815488','sexo' => 'M','fecha_nacimiento' => '1967-11-20','fecha_procedimiento' => '2025-07-02','factura' => '6548','created_at' => '2025-07-11 10:24:32','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1003122864','sexo' => 'F','fecha_nacimiento' => '2001-08-14','fecha_procedimiento' => '2025-07-02','factura' => '6549','created_at' => '2025-07-11 10:33:52','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1064119622','sexo' => 'F','fecha_nacimiento' => '1999-05-29','fecha_procedimiento' => '2025-07-02','factura' => '6550','created_at' => '2025-07-11 10:38:09','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1003821516','sexo' => 'M','fecha_nacimiento' => '2001-10-21','fecha_procedimiento' => '2025-07-02','factura' => '6551','created_at' => '2025-07-11 10:44:23','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012372903','sexo' => 'F','fecha_nacimiento' => '1990-12-06','fecha_procedimiento' => '2025-07-02','factura' => '6554','created_at' => '2025-07-02 12:13:42','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '35413374','sexo' => 'F','fecha_nacimiento' => '1968-01-26','fecha_procedimiento' => '2025-07-02','factura' => '6555','created_at' => '2025-07-02 12:17:31','CUP' => '911017','valor_examen' => '5000.00'),
  array('tID' => 'CC','numero_doc' => '1022408413','sexo' => 'F','fecha_nacimiento' => '1995-09-27','fecha_procedimiento' => '2025-07-02','factura' => '6557','created_at' => '2025-07-02 16:33:50','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '79274056','sexo' => 'M','fecha_nacimiento' => '1963-02-27','fecha_procedimiento' => '2025-07-02','factura' => '6558','created_at' => '2025-07-02 16:58:06','CUP' => '911017','valor_examen' => '5000.00'),
  array('tID' => 'CC','numero_doc' => '100754067','sexo' => 'F','fecha_nacimiento' => '2001-06-17','fecha_procedimiento' => '2025-07-02','factura' => '6552','created_at' => '2025-07-02 18:19:50','CUP' => '907106','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012344951','sexo' => 'F','fecha_nacimiento' => '2006-09-03','fecha_procedimiento' => '2025-07-02','factura' => '6559','created_at' => '2025-07-02 19:30:36','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1002953864','sexo' => 'F','fecha_nacimiento' => '2002-11-18','fecha_procedimiento' => '2025-07-03','factura' => '6566','created_at' => '2025-07-03 15:20:47','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012423916','sexo' => 'F','fecha_nacimiento' => '1995-08-05','fecha_procedimiento' => '2025-07-03','factura' => '6568','created_at' => '2025-07-03 17:18:51','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1007105510','sexo' => 'F','fecha_nacimiento' => '1991-01-01','fecha_procedimiento' => '2025-07-04','factura' => '34182','created_at' => '2025-07-04 12:18:14','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000128211','sexo' => 'F','fecha_nacimiento' => '2003-07-06','fecha_procedimiento' => '2025-07-05','factura' => '6572','created_at' => '2025-07-05 08:28:42','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'RC','numero_doc' => '1012475352','sexo' => 'M','fecha_nacimiento' => '2022-02-02','fecha_procedimiento' => '2025-07-07','factura' => '6577','created_at' => '2025-07-07 10:40:46','CUP' => '911017','valor_examen' => '5000.00'),
  array('tID' => 'TI','numero_doc' => '1013119025','sexo' => 'F','fecha_nacimiento' => '2007-12-02','fecha_procedimiento' => '2025-07-07','factura' => '6579','created_at' => '2025-07-07 13:10:07','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012316785','sexo' => 'F','fecha_nacimiento' => '2003-12-16','fecha_procedimiento' => '2025-07-07','factura' => '6580','created_at' => '2025-07-07 15:24:06','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012348943','sexo' => 'F','fecha_nacimiento' => '2007-01-27','fecha_procedimiento' => '2025-07-07','factura' => '6581','created_at' => '2025-07-07 17:05:29','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1007248750','sexo' => 'F','fecha_nacimiento' => '1987-03-09','fecha_procedimiento' => '2025-07-07','factura' => '6582','created_at' => '2025-07-07 17:56:27','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1024548935','sexo' => 'F','fecha_nacimiento' => '1994-01-07','fecha_procedimiento' => '2025-07-07','factura' => '6584','created_at' => '2025-07-07 18:20:25','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1016043754','sexo' => 'M','fecha_nacimiento' => '1992-04-03','fecha_procedimiento' => '2025-07-08','factura' => '6585','created_at' => '2025-07-11 11:56:33','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1006720370','sexo' => 'M','fecha_nacimiento' => '2003-11-04','fecha_procedimiento' => '2025-07-08','factura' => '6588','created_at' => '2025-07-11 12:00:33','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1026551631','sexo' => 'F','fecha_nacimiento' => '2004-04-28','fecha_procedimiento' => '2025-07-08','factura' => '6590','created_at' => '2025-07-08 13:57:22','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1033816338','sexo' => 'F','fecha_nacimiento' => '1999-05-13','fecha_procedimiento' => '2025-07-08','factura' => '6591','created_at' => '2025-07-08 14:25:56','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1007390395','sexo' => 'F','fecha_nacimiento' => '2002-06-22','fecha_procedimiento' => '2025-07-08','factura' => '6592','created_at' => '2025-07-08 15:40:33','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1007289035','sexo' => 'F','fecha_nacimiento' => '2000-06-26','fecha_procedimiento' => '2025-07-08','factura' => '6592','created_at' => '2025-07-08 17:41:06','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012339106','sexo' => 'F','fecha_nacimiento' => '2005-07-16','fecha_procedimiento' => '2025-07-08','factura' => '6594','created_at' => '2025-07-08 18:07:47','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1016074684','sexo' => 'F','fecha_nacimiento' => '1995-02-13','fecha_procedimiento' => '2025-07-08','factura' => '6595','created_at' => '2025-07-08 18:26:53','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'RC','numero_doc' => '1012475340','sexo' => 'M','fecha_nacimiento' => '2022-02-01','fecha_procedimiento' => '2025-07-09','factura' => '6598','created_at' => '2025-07-09 09:53:13','CUP' => '911017','valor_examen' => '5000.00'),
  array('tID' => 'CC','numero_doc' => '1019075688','sexo' => 'F','fecha_nacimiento' => '1992-04-24','fecha_procedimiento' => '2025-07-09','factura' => '6602','created_at' => '2025-07-09 11:35:14','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012436120','sexo' => 'F','fecha_nacimiento' => '1996-09-28','fecha_procedimiento' => '2025-07-09','factura' => '6603','created_at' => '2025-07-09 12:05:14','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000573370','sexo' => 'F','fecha_nacimiento' => '2002-03-18','fecha_procedimiento' => '2025-07-09','factura' => '6604','created_at' => '2025-07-09 12:30:56','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1010019276','sexo' => 'F','fecha_nacimiento' => '2000-11-01','fecha_procedimiento' => '2025-07-09','factura' => '6605','created_at' => '2025-07-09 16:05:19','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012459174','sexo' => 'F','fecha_nacimiento' => '1998-12-29','fecha_procedimiento' => '2025-07-09','factura' => '6606','created_at' => '2025-07-09 16:04:34','CUP' => '911017','valor_examen' => '5000.00'),
  array('tID' => 'CC','numero_doc' => '33116797','sexo' => 'F','fecha_nacimiento' => '1941-11-11','fecha_procedimiento' => '2025-07-09','factura' => '6599','created_at' => '2025-07-09 16:12:14','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1023035088','sexo' => 'F','fecha_nacimiento' => '1998-11-28','fecha_procedimiento' => '2025-07-09','factura' => '6607','created_at' => '2025-07-09 16:53:45','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1052414833','sexo' => 'F','fecha_nacimiento' => '1999-01-14','fecha_procedimiento' => '2025-07-09','factura' => '6608','created_at' => '2025-07-09 18:01:09','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000929185','sexo' => 'F','fecha_nacimiento' => '1999-07-29','fecha_procedimiento' => '2025-07-10','factura' => '6609','created_at' => '2025-07-10 07:30:05','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '2950644','sexo' => 'F','fecha_nacimiento' => '1946-07-27','fecha_procedimiento' => '2025-07-10','factura' => '6610','created_at' => '2025-07-10 21:30:01','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '79991390','sexo' => 'M','fecha_nacimiento' => '1979-05-21','fecha_procedimiento' => '2025-07-10','factura' => '34287','created_at' => '2025-07-21 10:28:33','CUP' => '906610','valor_examen' => '13000.00'),
  array('tID' => 'CC','numero_doc' => '1108766233','sexo' => 'F','fecha_nacimiento' => '1997-01-01','fecha_procedimiento' => '2025-07-03','factura' => '6570','created_at' => '2025-07-11 11:14:09','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1013661966','sexo' => 'M','fecha_nacimiento' => '1996-02-01','fecha_procedimiento' => '2025-07-05','factura' => '6571','created_at' => '2025-07-11 11:18:53','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1030688133','sexo' => 'F','fecha_nacimiento' => '1998-06-14','fecha_procedimiento' => '2025-07-14','factura' => '6613','created_at' => '2025-07-11 11:38:04','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1022326788','sexo' => 'M','fecha_nacimiento' => '2005-02-05','fecha_procedimiento' => '2025-07-05','factura' => '6573','created_at' => '2025-07-11 11:50:04','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1022930392','sexo' => 'M','fecha_nacimiento' => '1982-01-07','fecha_procedimiento' => '2025-07-09','factura' => '6600','created_at' => '2025-07-11 12:07:41','CUP' => '901305','valor_examen' => '6000.00'),
  array('tID' => 'CC','numero_doc' => '1002322974','sexo' => 'M','fecha_nacimiento' => '2001-11-06','fecha_procedimiento' => '2025-07-09','factura' => '6596','created_at' => '2025-07-11 12:29:58','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1049031914','sexo' => 'M','fecha_nacimiento' => '2017-12-30','fecha_procedimiento' => '2025-07-11','factura' => '6615','created_at' => '2025-07-11 12:53:03','CUP' => '911017','valor_examen' => '5000.00'),
  array('tID' => 'CC','numero_doc' => '20689263','sexo' => 'F','fecha_nacimiento' => '1985-06-25','fecha_procedimiento' => '2025-07-11','factura' => '6616','created_at' => '2025-07-11 15:55:46','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1010088219','sexo' => 'F','fecha_nacimiento' => '1997-12-05','fecha_procedimiento' => '2025-07-11','factura' => '6617','created_at' => '2025-07-11 17:02:11','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '39654956','sexo' => 'F','fecha_nacimiento' => '1970-10-23','fecha_procedimiento' => '2025-07-11','factura' => '34313','created_at' => '2025-07-11 17:17:24','CUP' => '907106','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012317224','sexo' => 'F','fecha_nacimiento' => '2004-01-29','fecha_procedimiento' => '2025-07-11','factura' => '6618','created_at' => '2025-07-11 19:26:22','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1073322269','sexo' => 'F','fecha_nacimiento' => '1989-01-18','fecha_procedimiento' => '2025-07-12','factura' => '6620','created_at' => '2025-07-12 09:02:31','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1072494784','sexo' => 'F','fecha_nacimiento' => '1990-08-26','fecha_procedimiento' => '2025-07-12','factura' => '6621','created_at' => '2025-07-12 09:36:19','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1193618289','sexo' => 'F','fecha_nacimiento' => '2001-01-05','fecha_procedimiento' => '2025-07-12','factura' => '6623','created_at' => '2025-07-12 12:25:02','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1011105025','sexo' => 'F','fecha_nacimiento' => '2010-07-24','fecha_procedimiento' => '2025-07-12','factura' => '6625','created_at' => '2025-07-12 13:46:47','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012326467','sexo' => 'F','fecha_nacimiento' => '2004-10-12','fecha_procedimiento' => '2025-07-12','factura' => '6619','created_at' => '2025-07-12 13:51:20','CUP' => '901107','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1001344346','sexo' => 'F','fecha_nacimiento' => '2002-01-07','fecha_procedimiento' => '2025-07-12','factura' => '6626','created_at' => '2025-07-12 14:43:06','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012407093','sexo' => 'F','fecha_nacimiento' => '1993-12-11','fecha_procedimiento' => '2025-07-12','factura' => '6627','created_at' => '2025-07-12 15:28:04','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1021663899','sexo' => 'F','fecha_nacimiento' => '2004-04-27','fecha_procedimiento' => '2025-07-14','factura' => '6229','created_at' => '2025-07-14 11:27:38','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1023387380','sexo' => 'F','fecha_nacimiento' => '2010-01-10','fecha_procedimiento' => '2025-07-14','factura' => '6231','created_at' => '2025-07-14 13:10:18','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1033821597','sexo' => 'F','fecha_nacimiento' => '1999-10-01','fecha_procedimiento' => '2025-07-14','factura' => '6232','created_at' => '2025-07-14 13:25:50','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1011510652','sexo' => 'F','fecha_nacimiento' => '2004-08-27','fecha_procedimiento' => '2025-07-14','factura' => '6233','created_at' => '2025-07-14 13:55:39','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '52901769','sexo' => 'F','fecha_nacimiento' => '1982-04-17','fecha_procedimiento' => '2025-07-14','factura' => '6234','created_at' => '2025-07-14 14:20:45','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000124293','sexo' => 'F','fecha_nacimiento' => '2002-02-02','fecha_procedimiento' => '2025-07-14','factura' => '6235','created_at' => '2025-07-14 18:21:22','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012400545','sexo' => 'F','fecha_nacimiento' => '1993-06-19','fecha_procedimiento' => '2025-07-15','factura' => '6238','created_at' => '2025-07-15 11:03:54','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1028868844','sexo' => 'F','fecha_nacimiento' => '2010-02-17','fecha_procedimiento' => '2025-07-15','factura' => '34361','created_at' => '2025-07-15 11:52:42','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000120596','sexo' => 'M','fecha_nacimiento' => '2001-06-06','fecha_procedimiento' => '2025-07-15','factura' => '6241','created_at' => '2025-07-15 17:11:28','CUP' => '901107','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1026599341','sexo' => 'F','fecha_nacimiento' => '1999-10-08','fecha_procedimiento' => '2025-07-16','factura' => '6247','created_at' => '2025-07-16 10:30:40','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '3009699','sexo' => 'M','fecha_nacimiento' => '1954-05-04','fecha_procedimiento' => '2025-07-16','factura' => '6249','created_at' => '2025-07-16 10:33:36','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000181634','sexo' => 'M','fecha_nacimiento' => '1998-01-01','fecha_procedimiento' => '2025-07-16','factura' => '6244','created_at' => '2025-07-16 16:58:06','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000788990','sexo' => 'F','fecha_nacimiento' => '2003-04-02','fecha_procedimiento' => '2025-07-16','factura' => '6655','created_at' => '2025-07-16 13:40:28','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1110635855','sexo' => 'F','fecha_nacimiento' => '2009-06-18','fecha_procedimiento' => '2025-07-16','factura' => '6556','created_at' => '2025-07-16 14:05:49','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1128544147','sexo' => 'F','fecha_nacimiento' => '2005-02-07','fecha_procedimiento' => '2025-07-16','factura' => '6557','created_at' => '2025-07-16 17:59:50','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1029283201','sexo' => 'F','fecha_nacimiento' => '2008-11-24','fecha_procedimiento' => '2025-07-16','factura' => '6658','created_at' => '2025-07-16 18:33:27','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1102375419','sexo' => 'F','fecha_nacimiento' => '1994-11-10','fecha_procedimiento' => '2025-07-17','factura' => '6660','created_at' => '2025-07-17 08:21:17','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1023655625','sexo' => 'M','fecha_nacimiento' => '2018-02-28','fecha_procedimiento' => '2025-07-17','factura' => '6661','created_at' => '2025-07-17 08:29:05','CUP' => '911017','valor_examen' => '5000.00'),
  array('tID' => 'CC','numero_doc' => '52466291','sexo' => 'F','fecha_nacimiento' => '1979-08-31','fecha_procedimiento' => '2025-07-17','factura' => '6664','created_at' => '2025-07-17 12:36:27','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'RC','numero_doc' => '1074833623','sexo' => 'M','fecha_nacimiento' => '2023-03-25','fecha_procedimiento' => '2025-07-17','factura' => '6663','created_at' => '2025-07-17 18:22:22','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'RC','numero_doc' => '1012481427','sexo' => 'M','fecha_nacimiento' => '2024-02-15','fecha_procedimiento' => '2025-07-17','factura' => '6667','created_at' => '2025-07-17 14:58:05','CUP' => '911017','valor_examen' => '5000.00'),
  array('tID' => 'CC','numero_doc' => '1033766616','sexo' => 'F','fecha_nacimiento' => '1994-07-26','fecha_procedimiento' => '2025-07-17','factura' => '6668','created_at' => '2025-07-17 15:32:56','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012352168','sexo' => 'F','fecha_nacimiento' => '2007-04-18','fecha_procedimiento' => '2025-07-17','factura' => '6669','created_at' => '2025-07-17 17:13:58','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012401891','sexo' => 'F','fecha_nacimiento' => '1993-08-01','fecha_procedimiento' => '2025-07-18','factura' => '6672','created_at' => '2025-07-18 08:51:38','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '52755297','sexo' => 'F','fecha_nacimiento' => '1981-05-12','fecha_procedimiento' => '2025-07-18','factura' => '6675','created_at' => '2025-07-18 10:12:45','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1001327453','sexo' => 'F','fecha_nacimiento' => '2001-11-18','fecha_procedimiento' => '2025-07-18','factura' => '6676','created_at' => '2025-07-18 10:47:44','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '52505767','sexo' => 'F','fecha_nacimiento' => '1979-03-20','fecha_procedimiento' => '2025-07-18','factura' => '34432','created_at' => '2025-07-18 17:55:51','CUP' => '903841','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1012360129','sexo' => 'F','fecha_nacimiento' => '2007-12-11','fecha_procedimiento' => '2025-07-18','factura' => '6673','created_at' => '2025-07-18 14:43:23','CUP' => '901107','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012423915','sexo' => 'F','fecha_nacimiento' => '1995-06-17','fecha_procedimiento' => '2025-07-18','factura' => '6679','created_at' => '2025-07-18 16:02:24','CUP' => '907106','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '52006956','sexo' => 'F','fecha_nacimiento' => '1968-09-22','fecha_procedimiento' => '2025-07-18','factura' => '6684','created_at' => '2025-07-18 19:06:02','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1007664760','sexo' => 'F','fecha_nacimiento' => '1999-12-24','fecha_procedimiento' => '2025-07-19','factura' => '6687','created_at' => '2025-07-19 10:09:05','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000115889','sexo' => 'F','fecha_nacimiento' => '1998-07-01','fecha_procedimiento' => '2025-07-19','factura' => '6688','created_at' => '2025-07-19 13:11:13','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012396470','sexo' => 'F','fecha_nacimiento' => '1993-01-11','fecha_procedimiento' => '2025-07-19','factura' => '6689','created_at' => '2025-07-19 14:47:05','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1016948218','sexo' => 'F','fecha_nacimiento' => '2006-01-05','fecha_procedimiento' => '2025-07-19','factura' => '6691','created_at' => '2025-07-19 17:21:12','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1012357435','sexo' => 'F','fecha_nacimiento' => '2007-09-20','fecha_procedimiento' => '2025-07-19','factura' => '6693','created_at' => '2025-07-19 18:44:37','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '63562152','sexo' => 'F','fecha_nacimiento' => '1985-07-12','fecha_procedimiento' => '2025-07-21','factura' => '6693','created_at' => '2025-07-21 09:08:08','CUP' => '911017','valor_examen' => '5000.00'),
  array('tID' => 'CC','numero_doc' => '1134180859','sexo' => 'F','fecha_nacimiento' => '2005-09-08','fecha_procedimiento' => '2025-07-21','factura' => '6695','created_at' => '2025-07-21 09:58:52','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1013099321','sexo' => 'F','fecha_nacimiento' => '2004-04-16','fecha_procedimiento' => '2025-07-21','factura' => '6697','created_at' => '2025-07-21 12:51:47','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000119900','sexo' => 'F','fecha_nacimiento' => '2001-05-16','fecha_procedimiento' => '2025-07-21','factura' => '6700','created_at' => '2025-07-21 15:03:34','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1106307007','sexo' => 'F','fecha_nacimiento' => '2006-12-01','fecha_procedimiento' => '2025-07-21','factura' => '6701','created_at' => '2025-07-21 15:39:51','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '20318364','sexo' => 'F','fecha_nacimiento' => '1937-08-29','fecha_procedimiento' => '2025-07-21','factura' => '6694','created_at' => '2025-07-21 15:51:35','CUP' => '907106','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1141314541','sexo' => 'F','fecha_nacimiento' => '2002-10-06','fecha_procedimiento' => '2025-07-21','factura' => '6702','created_at' => '2025-07-21 18:33:56','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012319931','sexo' => 'F','fecha_nacimiento' => '2004-04-01','fecha_procedimiento' => '2025-07-22','factura' => '6707','created_at' => '2025-07-22 11:54:38','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012394049','sexo' => 'F','fecha_nacimiento' => '1992-09-12','fecha_procedimiento' => '2025-07-22','factura' => '6708','created_at' => '2025-07-22 12:14:02','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1110465435','sexo' => 'F','fecha_nacimiento' => '1987-11-01','fecha_procedimiento' => '2025-07-22','factura' => '6709','created_at' => '2025-07-22 12:39:31','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'RC','numero_doc' => '1012475905','sexo' => 'F','fecha_nacimiento' => '2022-05-16','fecha_procedimiento' => '2025-07-22','factura' => '6710','created_at' => '2025-07-22 13:50:22','CUP' => '911017','valor_examen' => '5000.00'),
  array('tID' => 'CC','numero_doc' => '52752521','sexo' => 'F','fecha_nacimiento' => '1980-12-28','fecha_procedimiento' => '2025-07-22','factura' => '6705','created_at' => '2025-07-22 14:22:38','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1116808257','sexo' => 'M','fecha_nacimiento' => '2016-03-15','fecha_procedimiento' => '2025-07-23','factura' => '34542','created_at' => '2025-07-23 17:03:03','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012463730','sexo' => 'F','fecha_nacimiento' => '1999-07-31','fecha_procedimiento' => '2025-07-22','factura' => '6715','created_at' => '2025-07-22 18:53:06','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012415455','sexo' => 'F','fecha_nacimiento' => '1994-10-05','fecha_procedimiento' => '2025-07-22','factura' => '34521','created_at' => '2025-07-23 08:26:25','CUP' => '907106','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1022986264','sexo' => 'F','fecha_nacimiento' => '1993-05-29','fecha_procedimiento' => '2025-07-23','factura' => '6718','created_at' => '2025-07-23 10:33:40','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1102580859','sexo' => 'F','fecha_nacimiento' => '1994-11-05','fecha_procedimiento' => '2025-07-23','factura' => '6719','created_at' => '2025-07-23 11:29:29','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1016592598','sexo' => 'F','fecha_nacimiento' => '2004-02-29','fecha_procedimiento' => '2025-07-23','factura' => '6721','created_at' => '2025-07-23 12:51:30','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '11189796','sexo' => 'M','fecha_nacimiento' => '1973-01-12','fecha_procedimiento' => '2025-07-23','factura' => '6722','created_at' => '2025-07-23 12:57:16','CUP' => '911017','valor_examen' => '5000.00'),
  array('tID' => 'CC','numero_doc' => '1007767478','sexo' => 'F','fecha_nacimiento' => '1990-10-22','fecha_procedimiento' => '2025-07-23','factura' => '6725','created_at' => '2025-07-23 14:31:39','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '40022813','sexo' => 'F','fecha_nacimiento' => '1964-10-10','fecha_procedimiento' => '2025-07-23','factura' => '6717','created_at' => '2025-07-23 14:41:02','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000116773','sexo' => 'F','fecha_nacimiento' => '2000-10-07','fecha_procedimiento' => '2025-07-23','factura' => '6726','created_at' => '2025-07-23 16:22:19','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1019604231','sexo' => 'M','fecha_nacimiento' => '2002-09-06','fecha_procedimiento' => '2025-07-24','factura' => '6729','created_at' => '2025-07-24 18:04:21','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1120098276','sexo' => 'M','fecha_nacimiento' => '2006-11-24','fecha_procedimiento' => '2025-07-24','factura' => '6730','created_at' => '2025-07-24 18:12:08','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1007773116','sexo' => 'M','fecha_nacimiento' => '2000-11-29','fecha_procedimiento' => '2025-07-24','factura' => '6732','created_at' => '2025-07-24 19:21:34','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1047235083','sexo' => 'F','fecha_nacimiento' => '1995-11-27','fecha_procedimiento' => '2025-07-24','factura' => '6734','created_at' => '2025-07-24 19:58:02','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012380062','sexo' => 'M','fecha_nacimiento' => '1991-07-25','fecha_procedimiento' => '2025-07-24','factura' => '6737','created_at' => '2025-07-24 20:01:13','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1069177252','sexo' => 'F','fecha_nacimiento' => '1994-02-28','fecha_procedimiento' => '2025-07-24','factura' => '6739','created_at' => '2025-07-24 12:08:37','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '53131441','sexo' => 'F','fecha_nacimiento' => '1984-08-18','fecha_procedimiento' => '2025-07-24','factura' => '6740','created_at' => '2025-07-24 17:10:43','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1013619203','sexo' => 'F','fecha_nacimiento' => '1991-02-08','fecha_procedimiento' => '2025-07-24','factura' => '6741','created_at' => '2025-07-24 17:17:02','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1054571736','sexo' => 'F','fecha_nacimiento' => '1999-11-28','fecha_procedimiento' => '2025-07-24','factura' => '6742','created_at' => '2025-07-24 13:40:34','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012427476','sexo' => 'F','fecha_nacimiento' => '1995-09-29','fecha_procedimiento' => '2025-07-24','factura' => '6743','created_at' => '2025-07-24 15:31:34','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012418813','sexo' => 'F','fecha_nacimiento' => '1995-01-28','fecha_procedimiento' => '2025-07-24','factura' => '6746','created_at' => '2025-07-24 16:17:26','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '28865572','sexo' => 'F','fecha_nacimiento' => '1966-04-16','fecha_procedimiento' => '2025-07-24','factura' => '34555','created_at' => '2025-07-24 17:45:20','CUP' => '907106','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1011092992','sexo' => 'F','fecha_nacimiento' => '2006-03-26','fecha_procedimiento' => '2025-07-24','factura' => '6749','created_at' => '2025-07-24 18:54:53','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1063076498','sexo' => 'M','fecha_nacimiento' => '2006-05-21','fecha_procedimiento' => '2025-07-25','factura' => '6752','created_at' => '2025-07-28 19:01:33','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1022933746','sexo' => 'M','fecha_nacimiento' => '2005-05-17','fecha_procedimiento' => '2025-07-25','factura' => '6753','created_at' => '2025-07-28 19:04:11','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1075316737','sexo' => 'F','fecha_nacimiento' => '1999-02-21','fecha_procedimiento' => '2025-07-25','factura' => '6754','created_at' => '2025-07-28 19:05:59','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012353319','sexo' => 'F','fecha_nacimiento' => '1989-05-06','fecha_procedimiento' => '2025-07-25','factura' => '6755','created_at' => '2025-07-28 19:07:37','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1075270807','sexo' => 'M','fecha_nacimiento' => '1993-05-16','fecha_procedimiento' => '2025-07-25','factura' => '6756','created_at' => '2025-07-28 19:08:35','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000785200','sexo' => 'F','fecha_nacimiento' => '2002-02-03','fecha_procedimiento' => '2025-07-28','factura' => '6757','created_at' => '2025-07-28 19:10:28','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1065910675','sexo' => 'F','fecha_nacimiento' => '1997-05-07','fecha_procedimiento' => '2025-07-25','factura' => '6758','created_at' => '2025-07-28 19:11:47','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1057783533','sexo' => 'M','fecha_nacimiento' => '1988-07-11','fecha_procedimiento' => '2025-07-25','factura' => '6760','created_at' => '2025-07-28 19:12:34','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1010224885','sexo' => 'F','fecha_nacimiento' => '1995-11-04','fecha_procedimiento' => '2025-07-25','factura' => '6761','created_at' => '2025-07-28 19:14:23','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1022991254','sexo' => 'M','fecha_nacimiento' => '1993-11-16','fecha_procedimiento' => '2025-07-25','factura' => '6762','created_at' => '2025-07-28 19:16:06','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1019119980','sexo' => 'M','fecha_nacimiento' => '1996-03-11','fecha_procedimiento' => '2025-07-25','factura' => '6764','created_at' => '2025-07-28 19:22:19','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000131353','sexo' => 'M','fecha_nacimiento' => '2003-02-25','fecha_procedimiento' => '2025-07-25','factura' => '6765','created_at' => '2025-07-28 19:24:22','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1023006904','sexo' => 'M','fecha_nacimiento' => '1995-08-07','fecha_procedimiento' => '2025-07-25','factura' => '6766','created_at' => '2025-07-28 19:25:38','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1024498349','sexo' => 'F','fecha_nacimiento' => '1989-12-02','fecha_procedimiento' => '2025-07-25','factura' => '6767','created_at' => '2025-07-28 19:26:51','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1030546411','sexo' => 'F','fecha_nacimiento' => '1988-04-06','fecha_procedimiento' => '2025-07-25','factura' => '6778','created_at' => '2025-07-28 19:42:37','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1024573402','sexo' => 'M','fecha_nacimiento' => '1996-08-08','fecha_procedimiento' => '2025-07-25','factura' => '6769','created_at' => '2025-07-28 19:30:12','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1016592703','sexo' => 'F','fecha_nacimiento' => '2004-03-19','fecha_procedimiento' => '2025-07-25','factura' => '6770','created_at' => '2025-07-28 19:32:13','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '52971745','sexo' => 'F','fecha_nacimiento' => '1983-01-23','fecha_procedimiento' => '2025-07-25','factura' => '6771','created_at' => '2025-07-28 19:33:46','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1003934244','sexo' => 'M','fecha_nacimiento' => '2003-04-09','fecha_procedimiento' => '2025-07-28','factura' => '6772','created_at' => '2025-07-28 19:35:11','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1043444360','sexo' => 'M','fecha_nacimiento' => '2005-07-29','fecha_procedimiento' => '2025-07-25','factura' => '6773','created_at' => '2025-07-28 19:36:25','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000226662','sexo' => 'M','fecha_nacimiento' => '2001-10-02','fecha_procedimiento' => '2025-07-25','factura' => '6774','created_at' => '2025-07-28 19:37:51','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1014233073','sexo' => 'F','fecha_nacimiento' => '1992-05-20','fecha_procedimiento' => '2025-07-25','factura' => '6775','created_at' => '2025-07-28 19:38:53','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1151195335','sexo' => 'M','fecha_nacimiento' => '2003-02-14','fecha_procedimiento' => '2025-07-25','factura' => '6776','created_at' => '2025-07-28 19:40:20','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1030637117','sexo' => 'F','fecha_nacimiento' => '1994-02-28','fecha_procedimiento' => '2025-07-25','factura' => '6777','created_at' => '2025-07-28 19:41:18','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1192792585','sexo' => 'M','fecha_nacimiento' => '2000-11-14','fecha_procedimiento' => '2025-07-25','factura' => '6763','created_at' => '2025-07-28 19:21:12','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1000217687','sexo' => 'M','fecha_nacimiento' => '2001-09-10','fecha_procedimiento' => '2025-07-25','factura' => '6768','created_at' => '2025-07-28 19:28:11','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1010160096','sexo' => 'F','fecha_nacimiento' => '2003-03-27','fecha_procedimiento' => '2025-07-25','factura' => '6779','created_at' => '2025-07-25 13:23:27','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1010246174','sexo' => 'F','fecha_nacimiento' => '1999-05-12','fecha_procedimiento' => '2025-07-25','factura' => '6782','created_at' => '2025-07-25 16:38:05','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '52879523','sexo' => 'F','fecha_nacimiento' => '1983-09-21','fecha_procedimiento' => '2025-07-25','factura' => '6783','created_at' => '2025-07-25 16:40:48','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1006862943','sexo' => 'F','fecha_nacimiento' => '2002-02-01','fecha_procedimiento' => '2025-07-26','factura' => '6786','created_at' => '2025-07-26 13:20:03','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1030656660','sexo' => 'F','fecha_nacimiento' => '1995-07-07','fecha_procedimiento' => '2025-07-26','factura' => '6780','created_at' => '2025-07-26 16:14:28','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1003711209','sexo' => 'F','fecha_nacimiento' => '2002-02-14','fecha_procedimiento' => '2025-07-26','factura' => '6799','created_at' => '2025-07-26 16:23:10','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1034280616','sexo' => 'F','fecha_nacimiento' => '2005-03-07','fecha_procedimiento' => '2025-07-26','factura' => '6752','created_at' => '2025-07-26 16:39:02','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '53081340','sexo' => 'F','fecha_nacimiento' => '1984-11-06','fecha_procedimiento' => '2025-07-26','factura' => '34621','created_at' => '2025-07-27 22:29:25','CUP' => '906911','valor_examen' => '8000.00'),
  array('tID' => 'RC','numero_doc' => '10745377214','sexo' => 'M','fecha_nacimiento' => '2022-05-30','fecha_procedimiento' => '2025-07-26','factura' => '34634','created_at' => '2025-07-27 23:04:47','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1074815171','sexo' => 'F','fecha_nacimiento' => '2011-02-20','fecha_procedimiento' => '2025-07-28','factura' => '6786','created_at' => '2025-07-28 08:39:52','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '41696771','sexo' => 'F','fecha_nacimiento' => '1954-05-06','fecha_procedimiento' => '2025-07-26','factura' => '34633','created_at' => '2025-07-28 09:32:44','CUP' => '906911','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1105367514','sexo' => 'F','fecha_nacimiento' => '2006-05-12','fecha_procedimiento' => '2025-07-28','factura' => '6790','created_at' => '2025-07-28 12:50:24','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1003711118','sexo' => 'F','fecha_nacimiento' => '2002-01-17','fecha_procedimiento' => '2025-07-28','factura' => '6791','created_at' => '2025-07-28 13:06:39','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1013638572','sexo' => 'F','fecha_nacimiento' => '1992-09-18','fecha_procedimiento' => '2025-07-28','factura' => '6793','created_at' => '2025-07-28 14:13:29','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1005452583','sexo' => 'F','fecha_nacimiento' => '2000-08-22','fecha_procedimiento' => '2025-07-28','factura' => '6795','created_at' => '2025-07-28 16:25:11','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '13466984','sexo' => 'M','fecha_nacimiento' => '1963-01-01','fecha_procedimiento' => '2025-07-28','factura' => '34652','created_at' => '2025-07-28 18:34:03','CUP' => '906915','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012428757','sexo' => 'F','fecha_nacimiento' => '1996-01-01','fecha_procedimiento' => '2025-07-28','factura' => '6785','created_at' => '2025-07-29 09:26:53','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '65709602','sexo' => 'F','fecha_nacimiento' => '1983-01-18','fecha_procedimiento' => '2025-07-26','factura' => '6807','created_at' => '2025-07-30 17:13:58','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1006165134','sexo' => 'F','fecha_nacimiento' => '1999-03-25','fecha_procedimiento' => '2025-07-29','factura' => '6809','created_at' => '2025-07-29 06:35:10','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1024566487','sexo' => 'F','fecha_nacimiento' => '1995-11-19','fecha_procedimiento' => '2025-07-29','factura' => '6810','created_at' => '2025-07-29 07:15:16','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1124035944','sexo' => 'F','fecha_nacimiento' => '1990-10-14','fecha_procedimiento' => '2025-07-30','factura' => '6811','created_at' => '2025-07-30 17:34:04','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1043649706','sexo' => 'F','fecha_nacimiento' => '1999-08-20','fecha_procedimiento' => '2025-07-29','factura' => '6812','created_at' => '2025-07-30 17:36:06','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1023369671','sexo' => 'M','fecha_nacimiento' => '2005-09-12','fecha_procedimiento' => '2025-07-29','factura' => '6813','created_at' => '2025-07-30 17:54:45','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '52862821','sexo' => 'F','fecha_nacimiento' => '1979-01-20','fecha_procedimiento' => '2025-07-29','factura' => '6815','created_at' => '2025-07-29 17:53:24','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012429429','sexo' => 'F','fecha_nacimiento' => '1996-02-02','fecha_procedimiento' => '2025-07-29','factura' => '6817','created_at' => '2025-07-29 13:13:46','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012434000','sexo' => 'F','fecha_nacimiento' => '1996-07-18','fecha_procedimiento' => '2025-07-29','factura' => '6819','created_at' => '2025-07-29 14:02:25','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1082250580','sexo' => 'M','fecha_nacimiento' => '1994-11-22','fecha_procedimiento' => '2025-07-30','factura' => '6820','created_at' => '2025-07-30 17:56:40','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1024516164','sexo' => 'M','fecha_nacimiento' => '1990-12-10','fecha_procedimiento' => '2025-07-30','factura' => '6823','created_at' => '2025-07-30 17:59:09','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1081793542','sexo' => 'M','fecha_nacimiento' => '2006-01-18','fecha_procedimiento' => '2025-07-30','factura' => '6824','created_at' => '2025-07-30 18:01:10','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012406362','sexo' => 'F','fecha_nacimiento' => '1993-11-07','fecha_procedimiento' => '2025-07-30','factura' => '6827','created_at' => '2025-07-30 09:24:55','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1124013003','sexo' => 'F','fecha_nacimiento' => '1987-12-23','fecha_procedimiento' => '2025-07-30','factura' => '6829','created_at' => '2025-07-30 10:02:31','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012401129','sexo' => 'F','fecha_nacimiento' => '1992-12-20','fecha_procedimiento' => '2025-07-30','factura' => '6830','created_at' => '2025-07-30 14:10:47','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'TI','numero_doc' => '1122518372','sexo' => 'F','fecha_nacimiento' => '2008-04-13','fecha_procedimiento' => '2025-07-30','factura' => '6831','created_at' => '2025-07-30 14:13:56','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '19113851','sexo' => 'M','fecha_nacimiento' => '1950-07-30','fecha_procedimiento' => '2025-07-30','factura' => '34709','created_at' => '2025-07-30 22:22:16','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012345260','sexo' => 'F','fecha_nacimiento' => '2006-09-01','fecha_procedimiento' => '2025-07-30','factura' => '6833','created_at' => '2025-07-30 15:42:35','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012463685','sexo' => 'F','fecha_nacimiento' => '1999-08-14','fecha_procedimiento' => '2025-07-30','factura' => '6834','created_at' => '2025-07-30 16:03:17','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '73190124','sexo' => 'M','fecha_nacimiento' => '1982-05-23','fecha_procedimiento' => '2025-07-30','factura' => '6828','created_at' => '2025-07-30 18:01:56','CUP' => '901305','valor_examen' => '6000.00'),
  array('tID' => 'CC','numero_doc' => '1007701759','sexo' => 'F','fecha_nacimiento' => '2000-05-09','fecha_procedimiento' => '2025-07-30','factura' => '6835','created_at' => '2025-07-30 16:24:07','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1001276835','sexo' => 'M','fecha_nacimiento' => '2003-09-12','fecha_procedimiento' => '2025-07-26','factura' => '6808','created_at' => '2025-07-30 17:27:33','CUP' => '902207','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012458863','sexo' => 'F','fecha_nacimiento' => '1999-01-03','fecha_procedimiento' => '2025-07-30','factura' => '6836','created_at' => '2025-07-30 18:22:01','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1024480234','sexo' => 'F','fecha_nacimiento' => '2006-04-13','fecha_procedimiento' => '2025-07-31','factura' => '6842','created_at' => '2025-07-31 11:25:25','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012316831','sexo' => 'F','fecha_nacimiento' => '2003-12-18','fecha_procedimiento' => '2025-07-31','factura' => '6849','created_at' => '2025-07-31 16:21:12','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1012457797','sexo' => 'F','fecha_nacimiento' => '1998-11-27','fecha_procedimiento' => '2025-07-31','factura' => '6852','created_at' => '2025-07-31 17:54:39','CUP' => '904508','valor_examen' => '8000.00'),
  array('tID' => 'CC','numero_doc' => '1007247738','sexo' => 'F','fecha_nacimiento' => '1999-12-14','fecha_procedimiento' => '2025-07-31','factura' => '6751','created_at' => '2025-07-31 18:14:16','CUP' => '906911','valor_examen' => '8000.00')
);

// {
// "usuarios":
//    [
// 	{
// 		"tipoDocumentoIdentificacion": "CC",
// 		"numDocumentoIdentificacion": "1013115649",
// 		"tipoUsuario": "04",
// 		"fechaNacimiento": "2007-04-26",
// 		"codSexo": "F",
// 		"codPaisResidencia": "170",
// 		"codMunicipioResidencia": "11001",
// 		"codZonaTerritorialResidencia": "01",
// 		"incapacidad": "NO",
// 		"codPaisOrigen": "170",
// 		"consecutivo": 1,
// 		"servicios":
// 		{

// 			"procedimientos":
// 			[
// 				{
// 				"codPrestador": "110010822701",
// 				"fechaInicioAtencion": "2025-07-12 00:00",
// 				"idMIPRES": "",
// 				"numAutorizacion": "",
// 				"codProcedimiento": "904508",
// 				"viaIngresoServicioSalud": "03",
// 				"modalidadGrupoServicioTecSal": "01",
// 				"grupoServicios": "03",
// 				"codServicio": 328,
// 				"finalidadTecnologiaSalud": "15",
// 				"tipoDocumentoIdentificacion": "CC",
// 				"numDocumentoIdentificacion": "1013115649",
// 				"codDiagnosticoPrincipal": "Z017",
// 				"codDiagnosticoRelacionado": null,
// 				"codComplicacion": null,
// 				"vrServicio": 0,
// 				"conceptoRecaudo":"05",
// 				"valorPagoModerador": 0,
// 				"numFEVPagoModerador": "",
// 				"consecutivo": 1
// 				},
// 				{
// 				"codPrestador": "110010822701",
// 				"fechaInicioAtencion": "2025-07-12 00:00",
// 				"idMIPRES": "",
// 				"numAutorizacion": "",
// 				"codProcedimiento": "902207",
// 				"viaIngresoServicioSalud": "03",
// 				"modalidadGrupoServicioTecSal": "01",
// 				"grupoServicios": "03",
// 				"codServicio": 328,
// 				"finalidadTecnologiaSalud": "15",
// 				"tipoDocumentoIdentificacion": "CC",
// 				"numDocumentoIdentificacion": "1013115649",
// 				"codDiagnosticoPrincipal": "Z017",
// 				"codDiagnosticoRelacionado": null,
// 				"codComplicacion": null,
// 				"vrServicio":0,
// 				"conceptoRecaudo":"05",
// 				"valorPagoModerador": 0,
// 				"numFEVPagoModerador": "",
// 				"consecutivo": 2
// 				},
// 				{
// 				"codPrestador": "110010822701",
// 				"fechaInicioAtencion": "2025-07-12 00:00",
// 				"idMIPRES": "",
// 				"numAutorizacion": "",
// 				"codProcedimiento": "900511",
// 				"viaIngresoServicioSalud": "03",
// 				"modalidadGrupoServicioTecSal": "01",
// 				"grupoServicios": "03",
// 				"codServicio": 328,
// 				"finalidadTecnologiaSalud": "15",
// 				"tipoDocumentoIdentificacion": "CC",
// 				"numDocumentoIdentificacion": "1013115649",
// 				"codDiagnosticoPrincipal": "Z017",
// 				"codDiagnosticoRelacionado": null,
// 				"codComplicacion": null,
// 				"vrServicio": 0,
// 				"conceptoRecaudo":"05",
// 				"valorPagoModerador": 0,
// 				"numFEVPagoModerador": "",
// 				"consecutivo": 3
// 				}

//             ]
// 		}
// 		}
// 	]};


$lista ="CC	36562350	SANGUINETTI	ROMERO	MARY	LUZ	1968-06-12	F	2025-07-01	07:15			903895	7000
CC	11442216	SARMIENTO	DUARTE	CARLOS	ANDRES	1977-08-23	M	2025-07-01	07:25			903895	7000
CC	4090269	MARTINEZ	NIÑO	LUIS	REINALDO	1960-12-04	M	2025-07-01	07:35			903895	7000
CC	5756162	GONZALEZ	RIVAS	JOSE	ADOLFO	1959-08-29	M	2025-07-01	07:45			902207	7000
CC	5756162	GONZALEZ	RIVAS	JOSE	ADOLFO	1959-08-29	M	2025-07-01	07:45			903841	7000
CC	5756162	GONZALEZ	RIVAS	JOSE	ADOLFO	1959-08-29	M	2025-07-01	07:45			903818	7000
CC	5756162	GONZALEZ	RIVAS	JOSE	ADOLFO	1959-08-29	M	2025-07-01	07:45			903868	9000
CC	5756162	GONZALEZ	RIVAS	JOSE	ADOLFO	1959-08-29	M	2025-07-01	07:45			903801	7000
CC	5756162	GONZALEZ	RIVAS	JOSE	ADOLFO	1959-08-29	M	2025-07-01	07:45			903866	9000
CC	5756162	GONZALEZ	RIVAS	JOSE	ADOLFO	1959-08-29	M	2025-07-01	07:45			903867	9000
CC	5756162	GONZALEZ	RIVAS	JOSE	ADOLFO	1959-08-29	M	2025-07-01	07:45			903895	7000
CC	5756162	GONZALEZ	RIVAS	JOSE	ADOLFO	1959-08-29	M	2025-07-01	07:45			907106	7000
CC	5756162	GONZALEZ	RIVAS	JOSE	ADOLFO	1959-08-29	M	2025-07-01	07:45			907002	7000
CC	79623220	ARANZA	GARCIA	JUAN	RICARDO	1971-10-19	M	2025-07-01	08:05			903841	4500
CC	79623220	ARANZA	GARCIA	JUAN	RICARDO	1971-10-19	M	2025-07-01	08:05			903818	4500
CC	79623220	ARANZA	GARCIA	JUAN	RICARDO	1971-10-19	M	2025-07-01	08:05			903868	6000
AS	COL5156143	BLANCO	MATOS	ROISLIMAR		2002-02-26	F	2025-07-01	08:11			903841	4500
AS	COL5156143	BLANCO	MATOS	ROISLIMAR		2002-02-26	F	2025-07-01	08:11			903818	4500
AS	COL5156143	BLANCO	MATOS	ROISLIMAR		2002-02-26	F	2025-07-01	08:11			903868	6000
CC	1101386682	MOLINA	RODRIGUEZ	FABIO	DAVID	1991-11-06	M	2025-07-01	08:17			903841	4500
CC	1101386682	MOLINA	RODRIGUEZ	FABIO	DAVID	1991-11-06	M	2025-07-01	08:17			903818	4500
CC	1101386682	MOLINA	RODRIGUEZ	FABIO	DAVID	1991-11-06	M	2025-07-01	08:17			903868	6000
CC	1030579953	BASTO	REYES	CESAR	ORLANDO	1990-03-04	M	2025-07-01	08:23			903841	4500
CC	1030579953	BASTO	REYES	CESAR	ORLANDO	1990-03-04	M	2025-07-01	08:23			903818	4500
CC	1030579953	BASTO	REYES	CESAR	ORLANDO	1990-03-04	M	2025-07-01	08:23			903868	6000
CC	1012326840	MURCIA	PEÑA	DORA	JOSEFINA	1986-10-15	F	2025-07-02	07:15			907106	7000
CC	1012326840	MURCIA	PEÑA	DORA	JOSEFINA	1986-10-15	F	2025-07-02	07:15			907002	7000
CC	6775375	PATIÑO	IBAÑEZ	LUIS	FERNANDO	1967-08-02	M	2025-07-02	07:25			903868	9000
CC	1019136074	CABALLERO	HERNANDEZ	EVELYN	JOHANNA	1997-12-22	F	2025-07-02	07:30			904508	9000
CC	1024531906	BARRERO	TORRES	EDNA	ROCIO	1992-06-19	F	2025-07-02	07:35			904508	9000
CC	1121890780	CORREDOR	GONZALEZ	CARMEN	IVONNE	1971-10-19	F	2025-07-02	07:50			903841	4500
CC	1121890780	CORREDOR	GONZALEZ	CARMEN	IVONNE	1971-10-19	F	2025-07-02	07:50			903818	4500
CC	1121890780	CORREDOR	GONZALEZ	CARMEN	IVONNE	1971-10-19	F	2025-07-02	07:50			903868	6000
AS	COL5050427	GUZMAN	RIOS	DARWING		2002-02-26	M	2025-07-02	07:56			903841	4500
AS	COL5050427	GUZMAN	RIOS	DARWING		2002-02-26	M	2025-07-02	07:56			903818	4500
AS	COL5050427	GUZMAN	RIOS	DARWING		2002-02-26	M	2025-07-02	07:56			903868	6000
CC	1024565447	CAICEDO	DIAZ	JHOAN	SEBASTIAN	1991-11-06	M	2025-07-02	08:02			903841	4500
CC	1024565447	CAICEDO	DIAZ	JHOAN	SEBASTIAN	1991-11-06	M	2025-07-02	08:02			903818	4500
CC	1024565447	CAICEDO	DIAZ	JHOAN	SEBASTIAN	1991-11-06	M	2025-07-02	08:02			903868	6000
CC	1013679218	SALAZAR	CHAGUEZA	JOSE	HERNANDO	1998-01-25	M	2025-07-02	08:08			903841	4500
CC	1013679218	SALAZAR	CHAGUEZA	JOSE	HERNANDO	1998-01-25	M	2025-07-02	08:08			903818	4500
CC	1013679218	SALAZAR	CHAGUEZA	JOSE	HERNANDO	1998-01-25	M	2025-07-02	08:08			903868	6000
CC	1030694002	GARCIA	SALDAÑA	ALEJANDRO		1999-01-25	M	2025-07-02	08:14			902207	7000
CC	1030694002	GARCIA	SALDAÑA	ALEJANDRO		1999-01-25	M	2025-07-02	08:14			903841	4500
CC	1030694002	GARCIA	SALDAÑA	ALEJANDRO		1999-01-25	M	2025-07-02	08:14			903818	4500
CC	1030694002	GARCIA	SALDAÑA	ALEJANDRO		1999-01-25	M	2025-07-02	08:14			903868	6000
AS	COL4915343	VILLASMIL	RAMIREZ	DELVER	ALEXANDER	1990-09-15	M	2025-07-02	08:22			903841	4500
AS	COL4915343	VILLASMIL	RAMIREZ	DELVER	ALEXANDER	1990-09-15	M	2025-07-02	08:22			903818	4500
AS	COL4915343	VILLASMIL	RAMIREZ	DELVER	ALEXANDER	1990-09-15	M	2025-07-02	08:22			903868	6000
CC	5608757	ESTEBAN	NAUSA	HERIBERTO		1964-07-05	M	2025-07-02	08:28			903841	4500
CC	5608757	ESTEBAN	NAUSA	HERIBERTO		1964-07-05	M	2025-07-02	08:28			903818	4500
CC	5608757	ESTEBAN	NAUSA	HERIBERTO		1964-07-05	M	2025-07-02	08:28			903868	6000
CC	1066348810	GUEVARA	PALMERA	PABLO		1995-01-03	M	2025-07-02	08:34			903841	4500
CC	1066348810	GUEVARA	PALMERA	PABLO		1995-01-03	M	2025-07-02	08:34			903818	4500
CC	1066348810	GUEVARA	PALMERA	PABLO		1995-01-03	M	2025-07-02	08:34			903868	6000
CC	1030583962	DONCEL	LAITON	EDWARDS	IBRAHIN	1990-12-10	M	2025-07-02	08:40			903841	4500
CC	1030583962	DONCEL	LAITON	EDWARDS	IBRAHIN	1990-12-10	M	2025-07-02	08:40			903818	4500
CC	1030583962	DONCEL	LAITON	EDWARDS	IBRAHIN	1990-12-10	M	2025-07-02	08:40			903868	6000
CC	29912557	ACEVEDO	VILLEGAS	MARIA	GILMA	1962-04-20	F	2025-07-02	08:46			902207	7000
CC	29912557	ACEVEDO	VILLEGAS	MARIA	GILMA	1962-04-20	F	2025-07-02	08:46			903841	7000
CC	29912557	ACEVEDO	VILLEGAS	MARIA	GILMA	1962-04-20	F	2025-07-02	08:46			903818	7000
CC	29912557	ACEVEDO	VILLEGAS	MARIA	GILMA	1962-04-20	F	2025-07-02	08:46			903868	9000
CC	29912557	ACEVEDO	VILLEGAS	MARIA	GILMA	1962-04-20	F	2025-07-02	08:46			903895	7000
CC	29912557	ACEVEDO	VILLEGAS	MARIA	GILMA	1962-04-20	F	2025-07-02	08:46			906914	9000
CC	29912557	ACEVEDO	VILLEGAS	MARIA	GILMA	1962-04-20	F	2025-07-02	08:46			907106	7000
RC	1012481284	MALAGON	CAMARGO	SARA	VALENTINA	2024-01-18	F	2025-07-02	09:00			907002	7000
CC	30055123	RUIZ	GUTIERREZ	MARIA	BETSABE	1967-12-13	F	2025-07-02	09:10			902207	6000
CC	30055123	RUIZ	GUTIERREZ	MARIA	BETSABE	1967-12-13	F	2025-07-02	09:10			903841	6000
CC	30055123	RUIZ	GUTIERREZ	MARIA	BETSABE	1967-12-13	F	2025-07-02	09:10			903818	6000
CC	30055123	RUIZ	GUTIERREZ	MARIA	BETSABE	1967-12-13	F	2025-07-02	09:10			903815	6000
CC	30055123	RUIZ	GUTIERREZ	MARIA	BETSABE	1967-12-13	F	2025-07-02	09:10			903868	6000
CC	30055123	RUIZ	GUTIERREZ	MARIA	BETSABE	1967-12-13	F	2025-07-02	09:10			903856	6000
CC	30055123	RUIZ	GUTIERREZ	MARIA	BETSABE	1967-12-13	F	2025-07-02	09:10			903895	6000
CC	30055123	RUIZ	GUTIERREZ	MARIA	BETSABE	1967-12-13	F	2025-07-02	09:10			907106	6000
CC	53932453	MORENO		ISABEL	CRISTINA	1985-09-05	F	2025-07-02	09:18			902207	6000
CC	53932453	MORENO		ISABEL	CRISTINA	1985-09-05	F	2025-07-02	09:18			903841	6000
CC	53932453	MORENO		ISABEL	CRISTINA	1985-09-05	F	2025-07-02	09:18			903818	6000
CC	53932453	MORENO		ISABEL	CRISTINA	1985-09-05	F	2025-07-02	09:18			903815	6000
CC	53932453	MORENO		ISABEL	CRISTINA	1985-09-05	F	2025-07-02	09:18			903868	6000
CC	7306872	RODRIGUEZ	CORREDOR	JOSE	BUENAVENTURA	1962-09-11	M	2025-07-02	09:28			902207	6000
CC	7306872	RODRIGUEZ	CORREDOR	JOSE	BUENAVENTURA	1962-09-11	M	2025-07-02	09:28			903841	6000
CC	7306872	RODRIGUEZ	CORREDOR	JOSE	BUENAVENTURA	1962-09-11	M	2025-07-02	09:28			903818	6000
CC	7306872	RODRIGUEZ	CORREDOR	JOSE	BUENAVENTURA	1962-09-11	M	2025-07-02	09:28			903815	6000
CC	7306872	RODRIGUEZ	CORREDOR	JOSE	BUENAVENTURA	1962-09-11	M	2025-07-02	09:28			903868	6000
CC	80878362	BARRETO	SANCHEZ	GUILLERMO		1985-03-19	M	2025-07-03	07:15			903895	7000
CC	79751960	BERNAL	ALFONSO	CARLOS	ALBERTO	1974-08-02	M	2025-07-03	07:35			903841	4500
CC	79751960	BERNAL	ALFONSO	CARLOS	ALBERTO	1974-08-02	M	2025-07-03	07:35			903818	4500
CC	79751960	BERNAL	ALFONSO	CARLOS	ALBERTO	1974-08-02	M	2025-07-03	07:35			903868	6000
CC	1049930720	PEREZ	ESTREMOR	ELIECER		1991-01-07	M	2025-07-03	07:41			903841	4500
CC	1049930720	PEREZ	ESTREMOR	ELIECER		1991-01-07	M	2025-07-03	07:41			903818	4500
CC	1049930720	PEREZ	ESTREMOR	ELIECER		1991-01-07	M	2025-07-03	07:41			903868	6000
CC	33676682	PARADA	MORA	SANDRA	MILENA	1978-12-29	F	2025-07-03	07:47			902207	7000
CC	33676682	PARADA	MORA	SANDRA	MILENA	1978-12-29	F	2025-07-03	07:47			903841	5000
CC	33676682	PARADA	MORA	SANDRA	MILENA	1978-12-29	F	2025-07-03	07:47			903856	7000
CC	33676682	PARADA	MORA	SANDRA	MILENA	1978-12-29	F	2025-07-03	07:47			903895	7000
CC	41480061	TELLEZ	DE RINCON	MARIA	GLADYS	1950-07-05	F	2025-07-03	07:55			902207	7000
CC	41480061	TELLEZ	DE RINCON	MARIA	GLADYS	1950-07-05	F	2025-07-03	07:55			903841	7000
CC	41480061	TELLEZ	DE RINCON	MARIA	GLADYS	1950-07-05	F	2025-07-03	07:55			903818	7000
CC	41480061	TELLEZ	DE RINCON	MARIA	GLADYS	1950-07-05	F	2025-07-03	07:55			903868	9000
CC	41480061	TELLEZ	DE RINCON	MARIA	GLADYS	1950-07-05	F	2025-07-03	07:55			903866	9000
CC	41480061	TELLEZ	DE RINCON	MARIA	GLADYS	1950-07-05	F	2025-07-03	07:55			903867	9000
CC	41480061	TELLEZ	DE RINCON	MARIA	GLADYS	1950-07-05	F	2025-07-03	07:55			907106	7000
CC	51778184	ROMERO	GUERRERO	INES		1963-09-17	F	2025-07-03	08:09			907196	7000
TI	1029148788	ESCOBAR	CALLEJAS	ANDRES	FELIPE	2010-08-11	M	2025-07-04	07:15			907106	7000
TI	1029148788	ESCOBAR	CALLEJAS	ANDRES	FELIPE	2010-08-11	M	2025-07-04	07:15			907002	7000
CC	1000137532	VALDEZ	TORRES	JUAN	CAMILO	1999-07-16	M	2025-07-04	07:25			903841	4500
CC	1000137532	VALDEZ	TORRES	JUAN	CAMILO	1999-07-16	M	2025-07-04	07:25			903818	4500
CC	1000137532	VALDEZ	TORRES	JUAN	CAMILO	1999-07-16	M	2025-07-04	07:25			903868	6000
CC	1003087457	LEON	PEDROZO	JOSE	ALFREDO	2002-10-04	M	2025-07-04	07:31			903841	4500
CC	1003087457	LEON	PEDROZO	JOSE	ALFREDO	2002-10-04	M	2025-07-04	07:31			903818	4500
CC	1003087457	LEON	PEDROZO	JOSE	ALFREDO	2002-10-04	M	2025-07-04	07:31			903868	6000
CC	1050947898	MEZA	GOMEZ	AMAURY		1987-05-04	M	2025-07-04	07:37			903841	4500
CC	1050947898	MEZA	GOMEZ	AMAURY		1987-05-04	M	2025-07-04	07:37			903818	4500
CC	1050947898	MEZA	GOMEZ	AMAURY		1987-05-04	M	2025-07-04	07:37			903868	6000
CC	1071579449	LOPEZ	ROJAS	LUZ	MARIBEL	1990-01-21	F	2025-07-04	07:43			903841	4500
CC	1071579449	LOPEZ	ROJAS	LUZ	MARIBEL	1990-01-21	F	2025-07-04	07:43			903818	4500
CC	1071579449	LOPEZ	ROJAS	LUZ	MARIBEL	1990-01-21	F	2025-07-04	07:43			903868	6000
CC	1119949031	FLOR	MESTIZO	LUIYE		1996-04-09	M	2025-07-04	07:49			902207	7000
CC	1119949031	FLOR	MESTIZO	LUIYE		1996-04-09	M	2025-07-04	07:49			903841	4500
CC	1119949031	FLOR	MESTIZO	LUIYE		1996-04-09	M	2025-07-04	07:49			903818	4500
CC	1119949031	FLOR	MESTIZO	LUIYE		1996-04-09	M	2025-07-04	07:49			903868	6000
CC	1012425937	ORDOÑEZ	VILLABONA	BERTHA	DAYANNA	1995-08-17	F	2025-07-04	08:05			904508	6000
CC	4136654	GOMEZ	NAVAS	FELIX	ARGEMIRO	1971-12-15	M	2025-07-04	08:15			902207	6000
CC	4136654	GOMEZ	NAVAS	FELIX	ARGEMIRO	1971-12-15	M	2025-07-04	08:15			903841	6000
CC	4136654	GOMEZ	NAVAS	FELIX	ARGEMIRO	1971-12-15	M	2025-07-04	08:15			903818	6000
CC	4136654	GOMEZ	NAVAS	FELIX	ARGEMIRO	1971-12-15	M	2025-07-04	08:15			903815	6000
CC	4136654	GOMEZ	NAVAS	FELIX	ARGEMIRO	1971-12-15	M	2025-07-04	08:15			903868	6000
CC	4136654	GOMEZ	NAVAS	FELIX	ARGEMIRO	1971-12-15	M	2025-07-04	08:15			907106	6000
TI	1011238283	ROBLEDO	AGUILAR	ISABELLA		2015-05-14	F	2025-07-04	08:15			902207	6000
TI	1011238283	ROBLEDO	AGUILAR	ISABELLA		2015-05-14	F	2025-07-04	08:15			903841	6000
CC	1013105944	DUARTE	GONZALEZ	NIYIRETH	MARIANA	2005-09-09	F	2025-07-04	08:30			904508	9000
MS	COL7438180	GARRIDO	PIÑA	OLMER	GABRIEL	2015-06-03	M	2025-07-05	07:15			907106	7000
CC	1005185588	FLOREZ	PULIDO	FABIO	LUIS	1996-07-16	M	2025-07-05	07:25			903841	4500
CC	1005185588	FLOREZ	PULIDO	FABIO	LUIS	1996-07-16	M	2025-07-05	07:25			903818	4500
CC	1005185588	FLOREZ	PULIDO	FABIO	LUIS	1996-07-16	M	2025-07-05	07:25			903868	6000
CC	5874985	YATE	OTAVO	HERMINSON		1984-06-22	M	2025-07-05	07:31			903841	4500
CC	5874985	YATE	OTAVO	HERMINSON		1984-06-22	M	2025-07-05	07:31			903818	4500
CC	5874985	YATE	OTAVO	HERMINSON		1984-06-22	M	2025-07-05	07:31			903868	6000
CC	1118534912	VARGAS	ZORRO	SANDRA	MILENA	1986-03-28	F	2025-07-05	07:37			903841	4500
CC	1118534912	VARGAS	ZORRO	SANDRA	MILENA	1986-03-28	F	2025-07-05	07:37			903818	4500
CC	1118534912	VARGAS	ZORRO	SANDRA	MILENA	1986-03-28	F	2025-07-05	07:37			903868	6000
CC	1118534912	VARGAS	ZORRO	SANDRA	MILENA	1986-03-28	F	2025-07-05	07:37			904508	6000
CC	79390504	BAUTISTA	MENDOZA	ALFREDO		1966-06-12	M	2025-07-05	07:45			903841	4500
CC	79390504	BAUTISTA	MENDOZA	ALFREDO		1966-06-12	M	2025-07-05	07:45			903818	4500
CC	79390504	BAUTISTA	MENDOZA	ALFREDO		1966-06-12	M	2025-07-05	07:45			903868	6000
CC	1047440718	NUÑEZ	CARO	KELI	JOHANA	1991-10-28	F	2025-07-05	07:51			903841	4500
CC	1047440718	NUÑEZ	CARO	KELI	JOHANA	1991-10-28	F	2025-07-05	07:51			903818	4500
CC	1047440718	NUÑEZ	CARO	KELI	JOHANA	1991-10-28	F	2025-07-05	07:51			903868	6000
CC	93119206	URQUIZA	SABOGAL	CESAR	AUGUSTO	1961-11-29	M	2025-07-05	07:57			903841	4500
CC	93119206	URQUIZA	SABOGAL	CESAR	AUGUSTO	1961-11-29	M	2025-07-05	07:57			903818	4500
CC	93119206	URQUIZA	SABOGAL	CESAR	AUGUSTO	1961-11-29	M	2025-07-05	07:57			903868	6000
CC	1073172074	PEREZ	ORDOÑEZ	CRISTIAN	DAVID	1996-10-24	M	2025-07-05	08:03			903841	4500
CC	1073172074	PEREZ	ORDOÑEZ	CRISTIAN	DAVID	1996-10-24	M	2025-07-05	08:03			903818	4500
CC	1073172074	PEREZ	ORDOÑEZ	CRISTIAN	DAVID	1996-10-24	M	2025-07-05	08:03			903868	6000
CC	1104414177	ALVAREZ	BALDOVINO	JORGE	ALBERTO	1987-01-05	M	2025-07-05	08:09			903841	4500
CC	1104414177	ALVAREZ	BALDOVINO	JORGE	ALBERTO	1987-01-05	M	2025-07-05	08:09			903818	4500
CC	1104414177	ALVAREZ	BALDOVINO	JORGE	ALBERTO	1987-01-05	M	2025-07-05	08:09			903868	6000
CC	1104413585	ACOSTA	CHAVEZ	LUIS	ALBEIRO	1989-02-11	M	2025-07-05	08:15			903841	4500
CC	1104413585	ACOSTA	CHAVEZ	LUIS	ALBEIRO	1989-02-11	M	2025-07-05	08:15			903818	4500
CC	1104413585	ACOSTA	CHAVEZ	LUIS	ALBEIRO	1989-02-11	M	2025-07-05	08:15			903868	6000
CC	10887252	ACOSTA	CHAVEZ	HERNANDO	ANTONIO	1982-05-05	M	2025-07-05	08:21			903841	4500
CC	10887252	ACOSTA	CHAVEZ	HERNANDO	ANTONIO	1982-05-05	M	2025-07-05	08:21			903818	4500
CC	10887252	ACOSTA	CHAVEZ	HERNANDO	ANTONIO	1982-05-05	M	2025-07-05	08:21			903868	6000
CC	1024580088	BAUTISTA	GUIO	JOHN	MARIO	1997-04-09	M	2025-07-05	08:27			903841	4500
CC	1024580088	BAUTISTA	GUIO	JOHN	MARIO	1997-04-09	M	2025-07-05	08:27			903818	4500
CC	1024580088	BAUTISTA	GUIO	JOHN	MARIO	1997-04-09	M	2025-07-05	08:27			903868	6000
CC	79829892	LOPEZ	HERRERA	WILSON	HUMBERTO	1982-05-22	M	2025-07-05	08:33			902207	6000
CC	79829892	LOPEZ	HERRERA	WILSON	HUMBERTO	1982-05-22	M	2025-07-05	08:33			903841	6000
CC	79829892	LOPEZ	HERRERA	WILSON	HUMBERTO	1982-05-22	M	2025-07-05	08:33			903818	6000
CC	79829892	LOPEZ	HERRERA	WILSON	HUMBERTO	1982-05-22	M	2025-07-05	08:33			903815	6000
CC	79829892	LOPEZ	HERRERA	WILSON	HUMBERTO	1982-05-22	M	2025-07-05	08:33			903868	6000
CC	79829892	LOPEZ	HERRERA	WILSON	HUMBERTO	1982-05-22	M	2025-07-05	08:33			903801	6000
CC	20543161	VALBUENA	AREVALO	ROSALBINA		1944-09-27	F	2025-07-07	07:15			903818	7000
CC	20543161	VALBUENA	AREVALO	ROSALBINA		1944-09-27	F	2025-07-07	07:15			903868	9000
RC	1020850410	BOHORQUEZ	MARTINEZ	MARIEL	AITANA	2021-12-03	F	2025-07-07	07:25			907106	7000
CC	80250347	ROZO	RICO	JHONNY		1984-07-02	M	2025-07-07	07:35			902207	7000
CC	1033783234	CORREDOR	MARTINEZ	MARIA	FERNANDA	1995-10-24	F	2025-07-07	07:45			904508	9000
CC	1002321858	HERRERA	PEREZ	CARLOS	ANDRES	2000-08-27	M	2025-07-07	07:55			903841	4500
CC	1002321858	HERRERA	PEREZ	CARLOS	ANDRES	2000-08-27	M	2025-07-07	07:55			903818	4500
CC	1002321858	HERRERA	PEREZ	CARLOS	ANDRES	2000-08-27	M	2025-07-07	07:55			903868	6000
CC	1001340240	PEREZ	MAHECHA	LUIS	FERNEY	2000-04-14	M	2025-07-07	08:01			903841	4500
CC	1001340240	PEREZ	MAHECHA	LUIS	FERNEY	2000-04-14	M	2025-07-07	08:01			903818	4500
CC	1001340240	PEREZ	MAHECHA	LUIS	FERNEY	2000-04-14	M	2025-07-07	08:01			903868	6000
CC	85273182	LEON	RINCON	DEINER	MANUEL	1985-10-14	M	2025-07-07	08:07			903841	4500
CC	85273182	LEON	RINCON	DEINER	MANUEL	1985-10-14	M	2025-07-07	08:07			903818	4500
CC	85273182	LEON	RINCON	DEINER	MANUEL	1985-10-14	M	2025-07-07	08:07			903868	6000
CC	1124032982	VIZCAINO	CANTILLO	DAREET	JAVIER	1991-07-09	M	2025-07-07	08:13			903841	4500
CC	1124032982	VIZCAINO	CANTILLO	DAREET	JAVIER	1991-07-09	M	2025-07-07	08:13			903818	4500
CC	1124032982	VIZCAINO	CANTILLO	DAREET	JAVIER	1991-07-09	M	2025-07-07	08:13			903868	6000
CC	1031157969	JIMENEZ	BUITRAGO	YESSIKA	ANDREA	1995-02-10	F	2025-07-07	08:19			903841	4500
CC	1031157969	JIMENEZ	BUITRAGO	YESSIKA	ANDREA	1995-02-10	F	2025-07-07	08:19			903818	4500
CC	1031157969	JIMENEZ	BUITRAGO	YESSIKA	ANDREA	1995-02-10	F	2025-07-07	08:19			903868	6000
CC	52446546	SOLANO	VARGAS	SONIA	YADIT	1980-01-16	F	2025-07-07	08:25			903841	4500
CC	52446546	SOLANO	VARGAS	SONIA	YADIT	1980-01-16	F	2025-07-07	08:25			903818	4500
CC	52446546	SOLANO	VARGAS	SONIA	YADIT	1980-01-16	F	2025-07-07	08:25			903868	6000
CC	80801620	GARAVITO	CARO	GUSTAVO	ALEJANDRO	1989-01-19	M	2025-07-07	08:31			903841	4500
CC	80801620	GARAVITO	CARO	GUSTAVO	ALEJANDRO	1989-01-19	M	2025-07-07	08:31			903818	4500
CC	80801620	GARAVITO	CARO	GUSTAVO	ALEJANDRO	1989-01-19	M	2025-07-07	08:31			903868	6000
CC	1010070360	BELTRAN	ROJAS	JOHAN	SEBASTIAN	2000-10-16	M	2025-07-07	08:37			903841	4500
CC	1010070360	BELTRAN	ROJAS	JOHAN	SEBASTIAN	2000-10-16	M	2025-07-07	08:37			903818	4500
CC	1010070360	BELTRAN	ROJAS	JOHAN	SEBASTIAN	2000-10-16	M	2025-07-07	08:37			903868	6000
CC	1051240015	PARRA	ALBA	MARCO	ANTONIO	1985-06-01	M	2025-07-07	08:43			903841	4500
CC	1051240015	PARRA	ALBA	MARCO	ANTONIO	1985-06-01	M	2025-07-07	08:43			903818	4500
CC	1051240015	PARRA	ALBA	MARCO	ANTONIO	1985-06-01	M	2025-07-07	08:43			903868	6000
CC	80267523	AMORTEGUI	OSPINA	GILBERTO		1964-11-20	M	2025-07-08	07:15			903895	7000
CC	1003102008	AREVALO	PEDROZO	JUAN	CARLOS	1997-07-29	M	2025-07-08	07:25			903841	4500
CC	1003102008	AREVALO	PEDROZO	JUAN	CARLOS	1997-07-29	M	2025-07-08	07:25			903818	4500
CC	1003102008	AREVALO	PEDROZO	JUAN	CARLOS	1997-07-29	M	2025-07-08	07:25			903868	6000
CC	9274710	GOMEZ	SOSA	JAIME		1979-08-29	M	2025-07-08	07:31			903841	4500
CC	9274710	GOMEZ	SOSA	JAIME		1979-08-29	M	2025-07-08	07:31			903818	4500
CC	9274710	GOMEZ	SOSA	JAIME		1979-08-29	M	2025-07-08	07:31			903868	6000
CC	1001167042	GUERRERO	LARGO	YESICA	ANDREA	2000-03-25	F	2025-07-08	07:37			903841	4500
CC	1001167042	GUERRERO	LARGO	YESICA	ANDREA	2000-03-25	F	2025-07-08	07:37			903818	4500
CC	1001167042	GUERRERO	LARGO	YESICA	ANDREA	2000-03-25	F	2025-07-08	07:37			903868	6000
CC	1019100609	BARRAGAN	VILLANUEVA	FABIAN		1994-09-26	M	2025-07-08	07:43			903841	4500
CC	1019100609	BARRAGAN	VILLANUEVA	FABIAN		1994-09-26	M	2025-07-08	07:43			903818	4500
CC	1019100609	BARRAGAN	VILLANUEVA	FABIAN		1994-09-26	M	2025-07-08	07:43			903868	6000
CC	92671945	NARVAEZ	OROZCO	FREDYS	RAFAEL	1981-11-23	M	2025-07-08	07:49			903841	4500
CC	92671945	NARVAEZ	OROZCO	FREDYS	RAFAEL	1981-11-23	M	2025-07-08	07:49			903818	4500
CC	92671945	NARVAEZ	OROZCO	FREDYS	RAFAEL	1981-11-23	M	2025-07-08	07:49			903868	6000
CC	1072251959	PACHECO	REYES	JHONATAN	JOSE	1987-06-25	M	2025-07-08	07:55			903841	4500
CC	1072251959	PACHECO	REYES	JHONATAN	JOSE	1987-06-25	M	2025-07-08	07:55			903818	4500
CC	1072251959	PACHECO	REYES	JHONATAN	JOSE	1987-06-25	M	2025-07-08	07:55			903868	6000
CC	1013102302	SAZA	MORENO	SEBASTIAN		2004-07-09	M	2025-07-08	08:01			903841	4500
CC	1013102302	SAZA	MORENO	SEBASTIAN		2004-07-09	M	2025-07-08	08:01			903818	4500
CC	1013102302	SAZA	MORENO	SEBASTIAN		2004-07-09	M	2025-07-08	08:01			903868	6000
CC	79768930	CORNELIO		ANDERSON		1977-04-23	M	2025-07-08	08:07			903841	4500
CC	79768930	CORNELIO		ANDERSON		1977-04-23	M	2025-07-08	08:07			903818	4500
CC	79768930	CORNELIO		ANDERSON		1977-04-23	M	2025-07-08	08:07			903868	6000
CC	1007958789	URRUCHURTU	BALLESTERO	ANGEL	MANUEL	2001-12-31	M	2025-07-08	08:13			903841	4500
CC	1007958789	URRUCHURTU	BALLESTERO	ANGEL	MANUEL	2001-12-31	M	2025-07-08	08:13			903818	4500
CC	1007958789	URRUCHURTU	BALLESTERO	ANGEL	MANUEL	2001-12-31	M	2025-07-08	08:13			903868	6000
CC	1006443218	MARTINEZ	PULIDO	ANDERSON	FABIAN	2000-10-29	M	2025-07-08	08:19			903841	4500
CC	1006443218	MARTINEZ	PULIDO	ANDERSON	FABIAN	2000-10-29	M	2025-07-08	08:19			903818	4500
CC	1006443218	MARTINEZ	PULIDO	ANDERSON	FABIAN	2000-10-29	M	2025-07-08	08:19			903868	6000
CC	1015483029	ZAMBRANO	SANCHEZ	JULIAN		1999-08-25	M	2025-07-08	08:25			903841	4500
CC	1015483029	ZAMBRANO	SANCHEZ	JULIAN		1999-08-25	M	2025-07-08	08:25			903818	4500
CC	1015483029	ZAMBRANO	SANCHEZ	JULIAN		1999-08-25	M	2025-07-08	08:25			903868	6000
CC	1151477539	ARROYO	HERNANDEZ	WILSON	RAFAEL	1988-05-04	M	2025-07-08	08:31			903841	4500
CC	1151477539	ARROYO	HERNANDEZ	WILSON	RAFAEL	1988-05-04	M	2025-07-08	08:31			903818	4500
CC	1151477539	ARROYO	HERNANDEZ	WILSON	RAFAEL	1988-05-04	M	2025-07-08	08:31			903868	6000
CC	3007208	MARTINEZ		IVAN	RICARDO	1983-01-11	M	2025-07-08	08:37			903841	4500
CC	3007208	MARTINEZ		IVAN	RICARDO	1983-01-11	M	2025-07-08	08:37			903818	4500
CC	3007208	MARTINEZ		IVAN	RICARDO	1983-01-11	M	2025-07-08	08:37			903868	6000
CC	1002430486	ESCANDON	SOLANO	BRANDON	ENRIQUE	2001-02-01	M	2025-07-08	08:43			903841	4500
CC	1002430486	ESCANDON	SOLANO	BRANDON	ENRIQUE	2001-02-01	M	2025-07-08	08:43			903818	4500
CC	1002430486	ESCANDON	SOLANO	BRANDON	ENRIQUE	2001-02-01	M	2025-07-08	08:43			903868	6000
CC	1193387009	MACHADO	VEGA	NEIDER		1997-11-17	M	2025-07-08	08:49			903841	4500
CC	1193387009	MACHADO	VEGA	NEIDER		1997-11-17	M	2025-07-08	08:49			903818	4500
CC	1193387009	MACHADO	VEGA	NEIDER		1997-11-17	M	2025-07-08	08:49			903868	6000
CC	1116873381	DIAZ	MULATO	JAIME	LUIS	1999-05-09	M	2025-07-08	08:55			903841	4500
CC	1116873381	DIAZ	MULATO	JAIME	LUIS	1999-05-09	M	2025-07-08	08:55			903818	4500
CC	1116873381	DIAZ	MULATO	JAIME	LUIS	1999-05-09	M	2025-07-08	08:55			903868	6000
CC	1022952086	BEDOYA	SOGAMOSO	MARITZA		1989-06-23	F	2025-07-08	09:01			903841	4500
CC	1022952086	BEDOYA	SOGAMOSO	MARITZA		1989-06-23	F	2025-07-08	09:01			903818	4500
CC	1022952086	BEDOYA	SOGAMOSO	MARITZA		1989-06-23	F	2025-07-08	09:01			903868	6000
CC	1235242975	RIVERO	GONZALEZ	JHON	ERNESTO	2005-07-09	M	2025-07-08	09:07			903841	4500
CC	1235242975	RIVERO	GONZALEZ	JHON	ERNESTO	2005-07-09	M	2025-07-08	09:07			903818	4500
CC	1235242975	RIVERO	GONZALEZ	JHON	ERNESTO	2005-07-09	M	2025-07-08	09:07			903868	6000
CC	73507144	HERRERA	CARRERA	WILLIAM		1979-10-09	M	2025-07-08	09:13			903841	4500
CC	73507144	HERRERA	CARRERA	WILLIAM		1979-10-09	M	2025-07-08	09:13			903818	4500
CC	73507144	HERRERA	CARRERA	WILLIAM		1979-10-09	M	2025-07-08	09:13			903868	6000
CC	1012402179	GARCIA	SIERRA	YERLENY	DAYANA	1993-08-24	F	2025-07-08	09:19			904508	6000
CC	14315839	PEÑA	LOPEZ	ENRIQUE		1954-10-07	M	2025-07-08	09:25			902207	6000
CC	14315839	PEÑA	LOPEZ	ENRIQUE		1954-10-07	M	2025-07-08	09:25			903841	6000
CC	14315839	PEÑA	LOPEZ	ENRIQUE		1954-10-07	M	2025-07-08	09:25			903818	6000
CC	14315839	PEÑA	LOPEZ	ENRIQUE		1954-10-07	M	2025-07-08	09:25			903815	6000
CC	14315839	PEÑA	LOPEZ	ENRIQUE		1954-10-07	M	2025-07-08	09:25			903868	6000
CC	14315839	PEÑA	LOPEZ	ENRIQUE		1954-10-07	M	2025-07-08	09:25			903856	6000
CC	14315839	PEÑA	LOPEZ	ENRIQUE		1954-10-07	M	2025-07-08	09:25			903895	6000
CC	14315839	PEÑA	LOPEZ	ENRIQUE		1954-10-07	M	2025-07-08	09:25			907106	6000
MS	COL1629647	LAGUADO	HERNANDEZ	ELIETTE	NOEMI	2019-03-22	F	2025-07-08	09:37			907002	6000
CC	51712504	CHAVEZ	YEPES	JACQUELINE		1963-12-31	F	2025-07-08	09:50			902207	7000
CC	51712504	CHAVEZ	YEPES	JACQUELINE		1963-12-31	F	2025-07-08	09:50			903841	7000
CC	51712504	CHAVEZ	YEPES	JACQUELINE		1963-12-31	F	2025-07-08	09:50			907106	7000
CC	51712504	CHAVEZ	YEPES	JACQUELINE		1963-12-31	F	2025-07-08	09:50			907002	7000
CC	1012348313	GARCIA	MARIN	NIKOLL	MARIANA	2006-12-09	F	2025-07-08	09:59			904508	9000
CC	1120759000	DIAZ	ALMARIO	ALEXANDER	JOSE	1990-10-02	M	2025-07-09	07:15			903841	4500
CC	1120759000	DIAZ	ALMARIO	ALEXANDER	JOSE	1990-10-02	M	2025-07-09	07:15			903818	4500
CC	1120759000	DIAZ	ALMARIO	ALEXANDER	JOSE	1990-10-02	M	2025-07-09	07:15			903868	6000
CC	80743888	DAZA	REYES	JOSE	WILSON	1983-01-03	M	2025-07-09	07:21			903841	4500
CC	80743888	DAZA	REYES	JOSE	WILSON	1983-01-03	M	2025-07-09	07:21			903818	4500
CC	80743888	DAZA	REYES	JOSE	WILSON	1983-01-03	M	2025-07-09	07:21			903868	6000
CC	1062908070	CLAVIJO	GALVIS	CARLOS	ALFONSO	1990-09-02	M	2025-07-09	07:27			903841	4500
CC	1062908070	CLAVIJO	GALVIS	CARLOS	ALFONSO	1990-09-02	M	2025-07-09	07:27			903818	4500
CC	1062908070	CLAVIJO	GALVIS	CARLOS	ALFONSO	1990-09-02	M	2025-07-09	07:27			903868	6000
CC	7692449	GUTIERREZ	VANEGAS	LEONIDAS		1972-10-15	M	2025-07-09	07:33			903841	4500
CC	7692449	GUTIERREZ	VANEGAS	LEONIDAS		1972-10-15	M	2025-07-09	07:33			903818	4500
CC	7692449	GUTIERREZ	VANEGAS	LEONIDAS		1972-10-15	M	2025-07-09	07:33			903868	6000
CC	1003236554	ORTIZ	PERTUZ	JERSON	JOSE	2002-06-09	M	2025-07-09	07:39			903841	4500
CC	1003236554	ORTIZ	PERTUZ	JERSON	JOSE	2002-06-09	M	2025-07-09	07:39			903818	4500
CC	1003236554	ORTIZ	PERTUZ	JERSON	JOSE	2002-06-09	M	2025-07-09	07:39			903868	6000
CC	1024533478	MORENO	RAMIREZ	MANUEL		1992-09-08	M	2025-07-09	07:45			903841	4500
CC	1024533478	MORENO	RAMIREZ	MANUEL		1992-09-08	M	2025-07-09	07:45			903818	4500
CC	1024533478	MORENO	RAMIREZ	MANUEL		1992-09-08	M	2025-07-09	07:45			903868	6000
CC	1073164628	BERNAL	PINTO	HERSON	ANDRES	1993-06-02	M	2025-07-09	07:51			903841	4500
CC	1073164628	BERNAL	PINTO	HERSON	ANDRES	1993-06-02	M	2025-07-09	07:51			903818	4500
CC	1073164628	BERNAL	PINTO	HERSON	ANDRES	1993-06-02	M	2025-07-09	07:51			903868	6000
CC	1004120801	FONSECA 	GUTIERREZ	LUIS	EDUARDO	2002-04-11	M	2025-07-09	07:57			903841	4500
CC	1004120801	FONSECA 	GUTIERREZ	LUIS	EDUARDO	2002-04-11	M	2025-07-09	07:57			903818	4500
CC	1004120801	FONSECA 	GUTIERREZ	LUIS	EDUARDO	2002-04-11	M	2025-07-09	07:57			903868	6000
CC	1007013769	UNDA	RUIZ	JEINER	ANDRES	2001-09-30	M	2025-07-09	08:03			903841	4500
CC	1007013769	UNDA	RUIZ	JEINER	ANDRES	2001-09-30	M	2025-07-09	08:03			903818	4500
CC	1007013769	UNDA	RUIZ	JEINER	ANDRES	2001-09-30	M	2025-07-09	08:03			903868	6000
CC	1050456240	PAVA	ARRIETA	CELSO		2003-11-28	M	2025-07-09	08:09			903841	4500
CC	1050456240	PAVA	ARRIETA	CELSO		2003-11-28	M	2025-07-09	08:09			903818	4500
CC	1050456240	PAVA	ARRIETA	CELSO		2003-11-28	M	2025-07-09	08:09			903868	6000
CC	1012316811	PERDOMO	QUIJANO	JEFFERSON	JAVIER	2003-12-17	M	2025-07-09	08:15			903841	4500
CC	1012316811	PERDOMO	QUIJANO	JEFFERSON	JAVIER	2003-12-17	M	2025-07-09	08:15			903818	4500
CC	1012316811	PERDOMO	QUIJANO	JEFFERSON	JAVIER	2003-12-17	M	2025-07-09	08:15			903868	6000
CC	1073525641	POVEDA	ORTIZ	NICOLAS	SANTIAGO	1998-09-18	M	2025-07-09	08:21			903841	4500
CC	1073525641	POVEDA	ORTIZ	NICOLAS	SANTIAGO	1998-09-18	M	2025-07-09	08:21			903818	4500
CC	1073525641	POVEDA	ORTIZ	NICOLAS	SANTIAGO	1998-09-18	M	2025-07-09	08:21			903868	6000
CC	1063355170	MORENO	MAZO	OSCAR	GERARDO	1986-10-01	M	2025-07-09	08:27			903841	4500
CC	1063355170	MORENO	MAZO	OSCAR	GERARDO	1986-10-01	M	2025-07-09	08:27			903818	4500
CC	1063355170	MORENO	MAZO	OSCAR	GERARDO	1986-10-01	M	2025-07-09	08:27			903868	6000
CC	1120562362	LARRAHONDO	VALENCIA	SANTIAGO		2004-10-04	M	2025-07-09	08:33			903841	4500
CC	1120562362	LARRAHONDO	VALENCIA	SANTIAGO		2004-10-04	M	2025-07-09	08:33			903818	4500
CC	1120562362	LARRAHONDO	VALENCIA	SANTIAGO		2004-10-04	M	2025-07-09	08:33			903868	6000
CC	1233908847	JAIMES	PINZON	JULIAN	DAVID	1999-07-03	M	2025-07-09	08:39			903841	4500
CC	1233908847	JAIMES	PINZON	JULIAN	DAVID	1999-07-03	M	2025-07-09	08:39			903818	4500
CC	1233908847	JAIMES	PINZON	JULIAN	DAVID	1999-07-03	M	2025-07-09	08:39			903868	6000
CC	1024583473	COLMENARES	GARCIA	BRAYAN	YESID	1997-08-23	M	2025-07-09	08:45			903841	4500
CC	1024583473	COLMENARES	GARCIA	BRAYAN	YESID	1997-08-23	M	2025-07-09	08:45			903818	4500
CC	1024583473	COLMENARES	GARCIA	BRAYAN	YESID	1997-08-23	M	2025-07-09	08:45			903868	6000
CC	80733557	CAICEDO	CORRALES	FREDY	ORLEY	1982-07-30	M	2025-07-09	08:51			903841	4500
CC	80733557	CAICEDO	CORRALES	FREDY	ORLEY	1982-07-30	M	2025-07-09	08:51			903818	4500
CC	80733557	CAICEDO	CORRALES	FREDY	ORLEY	1982-07-30	M	2025-07-09	08:51			903868	6000
CC	1058324375	PEREZ	TORRES	RONAL	LEONARDO	2000-09-14	M	2025-07-09	08:57			903841	4500
CC	1058324375	PEREZ	TORRES	RONAL	LEONARDO	2000-09-14	M	2025-07-09	08:57			903818	4500
CC	1058324375	PEREZ	TORRES	RONAL	LEONARDO	2000-09-14	M	2025-07-09	08:57			903868	6000
CC	1037485048	COGOLLO	JIMENEZ	JERSON		1995-10-23	M	2025-07-09	09:03			903841	4500
CC	1037485048	COGOLLO	JIMENEZ	JERSON		1995-10-23	M	2025-07-09	09:03			903818	4500
CC	1037485048	COGOLLO	JIMENEZ	JERSON		1995-10-23	M	2025-07-09	09:03			903868	6000
CC	1050976390	ACUÑA	MORENO	SERGIO	MIGUEL	1999-07-18	M	2025-07-09	09:09			903841	4500
CC	1050976390	ACUÑA	MORENO	SERGIO	MIGUEL	1999-07-18	M	2025-07-09	09:09			903818	4500
CC	1050976390	ACUÑA	MORENO	SERGIO	MIGUEL	1999-07-18	M	2025-07-09	09:09			903868	6000
CC	1030553436	BURITICA	MORENO	JUAN	DAVID	1989-02-10	M	2025-07-09	09:15			903841	4500
CC	1030553436	BURITICA	MORENO	JUAN	DAVID	1989-02-10	M	2025-07-09	09:15			903818	4500
CC	1030553436	BURITICA	MORENO	JUAN	DAVID	1989-02-10	M	2025-07-09	09:15			903868	6000
CC	1006008904	MUÑOZ	HURTADO	JHON	JAIRO	1995-02-28	M	2025-07-09	09:21			903841	4500
CC	1006008904	MUÑOZ	HURTADO	JHON	JAIRO	1995-02-28	M	2025-07-09	09:21			903818	4500
CC	1006008904	MUÑOZ	HURTADO	JHON	JAIRO	1995-02-28	M	2025-07-09	09:21			903868	6000
CC	1007578349	BARON 	MESA	JOVANNY	DE JESUS	1991-11-29	M	2025-07-10	07:15			903841	4500
CC	1007578349	BARON 	MESA	JOVANNY	DE JESUS	1991-11-29	M	2025-07-10	07:15			903818	4500
CC	1007578349	BARON 	MESA	JOVANNY	DE JESUS	1991-11-29	M	2025-07-10	07:15			903868	6000
CC	1026301823	CASTAÑEDA	DIAZ	CHRISTIAN	WILHELM	1998-05-02	M	2025-07-10	07:21			903841	4500
CC	1026301823	CASTAÑEDA	DIAZ	CHRISTIAN	WILHELM	1998-05-02	M	2025-07-10	07:21			903818	4500
CC	1026301823	CASTAÑEDA	DIAZ	CHRISTIAN	WILHELM	1998-05-02	M	2025-07-10	07:21			903868	6000
CC	1101445823	SALGADO	GALAN	EVER	LUIS	2006-09-08	M	2025-07-10	07:27			903841	4500
CC	1101445823	SALGADO	GALAN	EVER	LUIS	2006-09-08	M	2025-07-10	07:27			903818	4500
CC	1101445823	SALGADO	GALAN	EVER	LUIS	2006-09-08	M	2025-07-10	07:27			903868	6000
CC	1006887589	OSORIO	MOJICA	KALLETH 	JOHANA	1990-04-08	F	2025-07-10	07:33			903841	4500
CC	1006887589	OSORIO	MOJICA	KALLETH 	JOHANA	1990-04-08	F	2025-07-10	07:33			903818	4500
CC	1006887589	OSORIO	MOJICA	KALLETH 	JOHANA	1990-04-08	F	2025-07-10	07:33			903868	6000
CC	1032462408	PAJARITO	DIAZ	JESSICA	PAOLA	1993-11-30	F	2025-07-10	07:39			903841	4500
CC	1032462408	PAJARITO	DIAZ	JESSICA	PAOLA	1993-11-30	F	2025-07-10	07:39			903818	4500
CC	1032462408	PAJARITO	DIAZ	JESSICA	PAOLA	1993-11-30	F	2025-07-10	07:39			903868	6000
CC	1012359351	PINEDA	CRUZ	JAIBER	ALEXIS	1989-11-30	M	2025-07-10	07:45			903841	4500
CC	1012359351	PINEDA	CRUZ	JAIBER	ALEXIS	1989-11-30	M	2025-07-10	07:45			903818	4500
CC	1012359351	PINEDA	CRUZ	JAIBER	ALEXIS	1989-11-30	M	2025-07-10	07:45			903868	6000
AS	COL6237008	ESCALONA	GARCIA	ISRAEL	SALOMON	1978-04-30	M	2025-07-10	07:51			903841	4500
AS	COL6237008	ESCALONA	GARCIA	ISRAEL	SALOMON	1978-04-30	M	2025-07-10	07:51			903818	4500
AS	COL6237008	ESCALONA	GARCIA	ISRAEL	SALOMON	1978-04-30	M	2025-07-10	07:51			903868	6000
CC	1019061483	MENDOZA	CARDENAS	JULIO	CESAR	1992-07-29	M	2025-07-10	07:57			903841	4500
CC	1019061483	MENDOZA	CARDENAS	JULIO	CESAR	1992-07-29	M	2025-07-10	07:57			903818	4500
CC	1019061483	MENDOZA	CARDENAS	JULIO	CESAR	1992-07-29	M	2025-07-10	07:57			903868	6000
CC	1001054459	GOMEZ	QUIÑONES	JUAN	CAMILO	2001-08-17	M	2025-07-10	08:03			903841	4500
CC	1001054459	GOMEZ	QUIÑONES	JUAN	CAMILO	2001-08-17	M	2025-07-10	08:03			903818	4500
CC	1001054459	GOMEZ	QUIÑONES	JUAN	CAMILO	2001-08-17	M	2025-07-10	08:03			903868	6000
CC	1003658671	GUERRERO	MOICA	OSCAR	ELIECER	2001-12-13	M	2025-07-10	08:09			903841	4500
CC	1003658671	GUERRERO	MOICA	OSCAR	ELIECER	2001-12-13	M	2025-07-10	08:09			903818	4500
CC	1003658671	GUERRERO	MOICA	OSCAR	ELIECER	2001-12-13	M	2025-07-10	08:09			903868	6000
CC	1001047375	LILOY	RODRIGUEZ	BRAYAN	SNEIDER	2002-06-06	M	2025-07-10	08:15			903841	4500
CC	1001047375	LILOY	RODRIGUEZ	BRAYAN	SNEIDER	2002-06-06	M	2025-07-10	08:15			903818	4500
CC	1001047375	LILOY	RODRIGUEZ	BRAYAN	SNEIDER	2002-06-06	M	2025-07-10	08:15			903868	6000
CC	1003504851	TUIRAN	VELASQUEZ	FERNANDO	JOSE	1998-02-19	M	2025-07-10	08:21			903841	4500
CC	1003504851	TUIRAN	VELASQUEZ	FERNANDO	JOSE	1998-02-19	M	2025-07-10	08:21			903818	4500
CC	1003504851	TUIRAN	VELASQUEZ	FERNANDO	JOSE	1998-02-19	M	2025-07-10	08:21			903868	6000
CC	1073160681	MEDINA	OSORIO	CAMILO	ANDRES	1991-05-30	M	2025-07-10	08:27			903841	4500
CC	1073160681	MEDINA	OSORIO	CAMILO	ANDRES	1991-05-30	M	2025-07-10	08:27			903818	4500
CC	1073160681	MEDINA	OSORIO	CAMILO	ANDRES	1991-05-30	M	2025-07-10	08:27			903868	6000
CC	1073169310	ESPINOSA	MONTALVO	EDUARD	DANIEL	1995-08-13	M	2025-07-10	08:33			903841	4500
CC	1073169310	ESPINOSA	MONTALVO	EDUARD	DANIEL	1995-08-13	M	2025-07-10	08:33			903818	4500
CC	1073169310	ESPINOSA	MONTALVO	EDUARD	DANIEL	1995-08-13	M	2025-07-10	08:33			903868	6000
CC	1050839256	PAVA	ARRIETA	DANIEL		2005-10-30	M	2025-07-10	08:39			903841	4500
CC	1050839256	PAVA	ARRIETA	DANIEL		2005-10-30	M	2025-07-10	08:39			903818	4500
CC	1050839256	PAVA	ARRIETA	DANIEL		2005-10-30	M	2025-07-10	08:39			903868	6000
CC	1116258308	OROZCO	LOAIZA	JENNIFER	ANDREA	1993-04-07	F	2025-07-10	08:45			903841	4500
CC	1116258308	OROZCO	LOAIZA	JENNIFER	ANDREA	1993-04-07	F	2025-07-10	08:45			903818	4500
CC	1116258308	OROZCO	LOAIZA	JENNIFER	ANDREA	1993-04-07	F	2025-07-10	08:45			903868	6000
CC	1104415604	ALVAREZ	REQUENA	EMILSON		2000-06-24	M	2025-07-10	08:51			903841	4500
CC	1104415604	ALVAREZ	REQUENA	EMILSON		2000-06-24	M	2025-07-10	08:51			903818	4500
CC	1104415604	ALVAREZ	REQUENA	EMILSON		2000-06-24	M	2025-07-10	08:51			903868	6000
CC	51759755	DIAZ	ORTIZ	MERY		1956-01-29	F	2025-07-10	08:57			902207	6000
CC	51759755	DIAZ	ORTIZ	MERY		1956-01-29	F	2025-07-10	08:57			903841	6000
CC	51759755	DIAZ	ORTIZ	MERY		1956-01-29	F	2025-07-10	08:57			903818	6000
CC	51759755	DIAZ	ORTIZ	MERY		1956-01-29	F	2025-07-10	08:57			903815	6000
CC	51759755	DIAZ	ORTIZ	MERY		1956-01-29	F	2025-07-10	08:57			903868	6000
CC	51759755	DIAZ	ORTIZ	MERY		1956-01-29	F	2025-07-10	08:57			903856	6000
CC	51759755	DIAZ	ORTIZ	MERY		1956-01-29	F	2025-07-10	08:57			903895	6000
CC	51759755	DIAZ	ORTIZ	MERY		1956-01-29	F	2025-07-10	08:57			907106	6000
CC	1070949072	COLORADO	RIVERA	CARLOS	ANDRES	1988-01-21	M	2025-07-11	07:15			903841	4500
CC	1070949072	COLORADO	RIVERA	CARLOS	ANDRES	1988-01-21	M	2025-07-11	07:15			903818	4500
CC	1070949072	COLORADO	RIVERA	CARLOS	ANDRES	1988-01-21	M	2025-07-11	07:15			903868	6000
CC	91432091	VASQUEZ	CAMARGO	ELFREN		1968-02-25	M	2025-07-11	07:21			903841	4500
CC	91432091	VASQUEZ	CAMARGO	ELFREN		1968-02-25	M	2025-07-11	07:21			903818	4500
CC	91432091	VASQUEZ	CAMARGO	ELFREN		1968-02-25	M	2025-07-11	07:21			903868	6000
CC	1024536617	CUPRITA	VIUCHE	FABIAN		1993-01-18	M	2025-07-11	07:27			903841	4500
CC	1024536617	CUPRITA	VIUCHE	FABIAN		1993-01-18	M	2025-07-11	07:27			903818	4500
CC	1024536617	CUPRITA	VIUCHE	FABIAN		1993-01-18	M	2025-07-11	07:27			903868	6000
CC	1105059566	VIUCHE	CARRILLO	BERLEY		1991-12-29	M	2025-07-11	07:33			903841	4500
CC	1105059566	VIUCHE	CARRILLO	BERLEY		1991-12-29	M	2025-07-11	07:33			903818	4500
CC	1105059566	VIUCHE	CARRILLO	BERLEY		1991-12-29	M	2025-07-11	07:33			903868	6000
CC	7708644	TOVAR	SANCHEZ	EDISON		1977-06-14	M	2025-07-11	07:39			903841	4500
CC	7708644	TOVAR	SANCHEZ	EDISON		1977-06-14	M	2025-07-11	07:39			903818	4500
CC	7708644	TOVAR	SANCHEZ	EDISON		1977-06-14	M	2025-07-11	07:39			903868	6000
CC	80253241	RODAS	MONCADA	CARLOS	ALBERTO	1983-05-23	M	2025-07-11	07:45			903841	4500
CC	80253241	RODAS	MONCADA	CARLOS	ALBERTO	1983-05-23	M	2025-07-11	07:45			903818	4500
CC	80253241	RODAS	MONCADA	CARLOS	ALBERTO	1983-05-23	M	2025-07-11	07:45			903868	6000
CC	16843435	DIAZ	MULATO	JHON	JAIRO	1979-11-18	M	2025-07-11	07:51			903841	4500
CC	16843435	DIAZ	MULATO	JHON	JAIRO	1979-11-18	M	2025-07-11	07:51			903818	4500
CC	16843435	DIAZ	MULATO	JHON	JAIRO	1979-11-18	M	2025-07-11	07:51			903868	6000
CC	1193604948	CORREA	TAPIAS	BRAYAN	ALFONSO	1999-03-11	M	2025-07-11	07:57			903841	4500
CC	1193604948	CORREA	TAPIAS	BRAYAN	ALFONSO	1999-03-11	M	2025-07-11	07:57			903818	4500
CC	1193604948	CORREA	TAPIAS	BRAYAN	ALFONSO	1999-03-11	M	2025-07-11	07:57			903868	6000
CC	1033779711	GALINDO	LOPEZ	CARLOS	JAVIER	1995-08-30	M	2025-07-11	08:03			903841	4500
CC	1033779711	GALINDO	LOPEZ	CARLOS	JAVIER	1995-08-30	M	2025-07-11	08:03			903818	4500
CC	1033779711	GALINDO	LOPEZ	CARLOS	JAVIER	1995-08-30	M	2025-07-11	08:03			903868	6000
CC	1015997925	FERNANDEZ	DUARTE	MIGUEL	ANGEL	2004-09-24	M	2025-07-11	08:09			903841	4500
CC	1015997925	FERNANDEZ	DUARTE	MIGUEL	ANGEL	2004-09-24	M	2025-07-11	08:09			903818	4500
CC	1015997925	FERNANDEZ	DUARTE	MIGUEL	ANGEL	2004-09-24	M	2025-07-11	08:09			903868	6000
CC	1128150638	MIER	MOLINA	CARLOS	ANDRES	1989-12-15	M	2025-07-11	08:15			903841	4500
CC	1128150638	MIER	MOLINA	CARLOS	ANDRES	1989-12-15	M	2025-07-11	08:15			903818	4500
CC	1128150638	MIER	MOLINA	CARLOS	ANDRES	1989-12-15	M	2025-07-11	08:15			903868	6000
CC	1002242234	URBINA	MOLINA	EDEL	LUIS	2000-02-14	M	2025-07-11	08:21			903841	4500
CC	1002242234	URBINA	MOLINA	EDEL	LUIS	2000-02-14	M	2025-07-11	08:21			903818	4500
CC	1002242234	URBINA	MOLINA	EDEL	LUIS	2000-02-14	M	2025-07-11	08:21			903868	6000
CC	1000003299	OSSA	PEÑA	YEINER	ANDRES	2002-07-21	M	2025-07-11	08:27			903841	4500
CC	1000003299	OSSA	PEÑA	YEINER	ANDRES	2002-07-21	M	2025-07-11	08:27			903818	4500
CC	1000003299	OSSA	PEÑA	YEINER	ANDRES	2002-07-21	M	2025-07-11	08:27			903868	6000
CC	1118811511	GOMEZ	GOMEZ	YONAIKER	ANDRES	2005-05-01	M	2025-07-11	08:33			903841	4500
CC	1118811511	GOMEZ	GOMEZ	YONAIKER	ANDRES	2005-05-01	M	2025-07-11	08:33			903818	4500
CC	1118811511	GOMEZ	GOMEZ	YONAIKER	ANDRES	2005-05-01	M	2025-07-11	08:33			903868	6000
CC	1069501693	DIAZ	FLOREZ	DIEGO	ARMANDO	1996-06-08	M	2025-07-11	08:39			903841	4500
CC	1069501693	DIAZ	FLOREZ	DIEGO	ARMANDO	1996-06-08	M	2025-07-11	08:39			903818	4500
CC	1069501693	DIAZ	FLOREZ	DIEGO	ARMANDO	1996-06-08	M	2025-07-11	08:39			903868	6000
CC	1065614504	SARMIENTO	HERNANDEZ	WILLIAM	RICARDO	1989-11-27	M	2025-07-11	08:45			903841	4500
CC	1065614504	SARMIENTO	HERNANDEZ	WILLIAM	RICARDO	1989-11-27	M	2025-07-11	08:45			903818	4500
CC	1065614504	SARMIENTO	HERNANDEZ	WILLIAM	RICARDO	1989-11-27	M	2025-07-11	08:45			903868	6000
CC	39778763	ALZA	PARDO	MARISOL		1967-12-31	F	2025-07-11	08:51			903841	7000
CC	1120353713	ROMERO	NIETO	VALENTINA		2004-08-07	F	2025-07-11	08:59			904508	9000
RC	1012482407	ARIAS	MARIN	MARIA	PAULA	2024-06-10	F	2025-07-11	09:05			902207	7000
CC	53116464	PACHON	CORDOBA	XIOMARA		1985-02-25	F	2025-07-12	07:15			903841	4500
CC	53116464	PACHON	CORDOBA	XIOMARA		1985-02-25	F	2025-07-12	07:15			903818	4500
CC	53116464	PACHON	CORDOBA	XIOMARA		1985-02-25	F	2025-07-12	07:15			903868	6000
CC	92450832	JULIO	MEDINA	FELIX	ENRIQUE	1993-08-20	M	2025-07-12	07:21			903841	4500
CC	92450832	JULIO	MEDINA	FELIX	ENRIQUE	1993-08-20	M	2025-07-12	07:21			903818	4500
CC	92450832	JULIO	MEDINA	FELIX	ENRIQUE	1993-08-20	M	2025-07-12	07:21			903868	6000
CC	79610338	HERRERA	CORREA	BENJAMIN		1973-02-15	M	2025-07-12	07:27			903841	4500
CC	79610338	HERRERA	CORREA	BENJAMIN		1973-02-15	M	2025-07-12	07:27			903818	4500
CC	79610338	HERRERA	CORREA	BENJAMIN		1973-02-15	M	2025-07-12	07:27			903868	6000
CC	79856911	AMAZO	VELASQUEZ	OSCAR	EDUARDO	1974-10-20	M	2025-07-12	07:33			903841	4500
CC	79856911	AMAZO	VELASQUEZ	OSCAR	EDUARDO	1974-10-20	M	2025-07-12	07:33			903818	4500
CC	79856911	AMAZO	VELASQUEZ	OSCAR	EDUARDO	1974-10-20	M	2025-07-12	07:33			903868	6000
CC	1070918282	REY	NIVIA	JHONATAN		1989-06-23	M	2025-07-12	07:39			903841	4500
CC	1070918282	REY	NIVIA	JHONATAN		1989-06-23	M	2025-07-12	07:39			903818	4500
CC	1070918282	REY	NIVIA	JHONATAN		1989-06-23	M	2025-07-12	07:39			903868	6000
CC	1074808708	YAÑEZ	IMBACHI	JUAN	DAVID	2004-05-16	M	2025-07-12	07:45			902207	7000
CC	1074808708	YAÑEZ	IMBACHI	JUAN	DAVID	2004-05-16	M	2025-07-12	07:45			903841	7000
CC	1074808708	YAÑEZ	IMBACHI	JUAN	DAVID	2004-05-16	M	2025-07-12	07:45			903866	9000
CC	1074808708	YAÑEZ	IMBACHI	JUAN	DAVID	2004-05-16	M	2025-07-12	07:45			903867	9000
CC	1074808708	YAÑEZ	IMBACHI	JUAN	DAVID	2004-05-16	M	2025-07-12	07:45			907106	7000
CC	1074808708	YAÑEZ	IMBACHI	JUAN	DAVID	2004-05-16	M	2025-07-12	07:45			907002	7000
CC	1049825478	BERRIO	MELGAREJO	REMBERTO		1987-07-26	M	2025-07-14	07:15			903841	4500
CC	1049825478	BERRIO	MELGAREJO	REMBERTO		1987-07-26	M	2025-07-14	07:15			903818	4500
CC	1049825478	BERRIO	MELGAREJO	REMBERTO		1987-07-26	M	2025-07-14	07:15			903868	6000
CC	7384888	HERNANDEZ	MONTES	YERSON	YAIR	1983-04-06	M	2025-07-14	07:21			903841	4500
CC	7384888	HERNANDEZ	MONTES	YERSON	YAIR	1983-04-06	M	2025-07-14	07:21			903818	4500
CC	7384888	HERNANDEZ	MONTES	YERSON	YAIR	1983-04-06	M	2025-07-14	07:21			903868	6000
CC	79730876	NIÑO	RODRIGUEZ	JOAQUIN	ELIAS	1978-02-15	M	2025-07-14	07:27			903841	4500
CC	79730876	NIÑO	RODRIGUEZ	JOAQUIN	ELIAS	1978-02-15	M	2025-07-14	07:27			903818	4500
CC	79730876	NIÑO	RODRIGUEZ	JOAQUIN	ELIAS	1978-02-15	M	2025-07-14	07:27			903868	6000
CC	80808317	GALINDO	URUEÑA	DIEGO	LEONEL	1984-06-16	M	2025-07-14	07:33			903841	4500
CC	80808317	GALINDO	URUEÑA	DIEGO	LEONEL	1984-06-16	M	2025-07-14	07:33			903818	4500
CC	80808317	GALINDO	URUEÑA	DIEGO	LEONEL	1984-06-16	M	2025-07-14	07:33			903868	6000
CC	19955495	ACOSTA	PEREZ	GIOVANI	RAFAEL	1985-08-06	M	2025-07-14	07:39			903841	4500
CC	19955495	ACOSTA	PEREZ	GIOVANI	RAFAEL	1985-08-06	M	2025-07-14	07:39			903818	4500
CC	19955495	ACOSTA	PEREZ	GIOVANI	RAFAEL	1985-08-06	M	2025-07-14	07:39			903868	6000
CC	9157240	ACOSTA	PEREZ	ALEXIS		1978-05-22	M	2025-07-14	07:45			903841	4500
CC	9157240	ACOSTA	PEREZ	ALEXIS		1978-05-22	M	2025-07-14	07:45			903818	4500
CC	9157240	ACOSTA	PEREZ	ALEXIS		1978-05-22	M	2025-07-14	07:45			903868	6000
CC	9149346	ZUÑIGA	ANGULO	JESUS		1978-03-06	M	2025-07-14	07:51			903841	4500
CC	9149346	ZUÑIGA	ANGULO	JESUS		1978-03-06	M	2025-07-14	07:51			903818	4500
CC	9149346	ZUÑIGA	ANGULO	JESUS		1978-03-06	M	2025-07-14	07:51			903868	6000
CC	24016215	BETANCOURT	BETANCOURT	MARIA	ELSA	1956-01-04	F	2025-07-14	07:57			902207	7000
CC	24016215	BETANCOURT	BETANCOURT	MARIA	ELSA	1956-01-04	F	2025-07-14	07:57			903841	7000
CC	24016215	BETANCOURT	BETANCOURT	MARIA	ELSA	1956-01-04	F	2025-07-14	07:57			902818	7000
CC	24016215	BETANCOURT	BETANCOURT	MARIA	ELSA	1956-01-04	F	2025-07-14	07:57			903815	9000
CC	24016215	BETANCOURT	BETANCOURT	MARIA	ELSA	1956-01-04	F	2025-07-14	07:57			903868	9000
CC	24016215	BETANCOURT	BETANCOURT	MARIA	ELSA	1956-01-04	F	2025-07-14	07:57			903895	7000
CC	24016215	BETANCOURT	BETANCOURT	MARIA	ELSA	1956-01-04	F	2025-07-14	07:57			907106	7000
TI	1025543864	CASTILLO	PEREZ	VALERIA	ALEXANDRA	2010-02-02	F	2025-07-14	08:11			904508	9000
CC	52300889	ROJAS	CASTAÑEDA	OLGA	LUCIA	1975-08-24	F	2025-07-14	08:13			904508	9000
CC	79618410	PEREZ	ALARCON	HECTOR	JULIO	1973-07-20	M	2025-07-15	07:15			903841	4500
CC	79618410	PEREZ	ALARCON	HECTOR	JULIO	1973-07-20	M	2025-07-15	07:15			903818	4500
CC	79618410	PEREZ	ALARCON	HECTOR	JULIO	1973-07-20	M	2025-07-15	07:15			903868	6000
CC	1121527561	CASSERES	TORRES	JUAN	DAVID	2004-06-16	M	2025-07-15	07:21			903841	4500
CC	1121527561	CASSERES	TORRES	JUAN	DAVID	2004-06-16	M	2025-07-15	07:21			903818	4500
CC	1121527561	CASSERES	TORRES	JUAN	DAVID	2004-06-16	M	2025-07-15	07:21			903868	6000
CC	11810033	RENTERIA	ROBLEDO	WILBERT		1979-05-04	M	2025-07-15	07:27			903841	4500
CC	11810033	RENTERIA	ROBLEDO	WILBERT		1979-05-04	M	2025-07-15	07:27			903818	4500
CC	11810033	RENTERIA	ROBLEDO	WILBERT		1979-05-04	M	2025-07-15	07:27			903868	6000
CC	9154673	ZUÑIGA	PALOMINO	VALDIMIRO		1972-10-15	M	2025-07-15	07:33			903841	4500
CC	9154673	ZUÑIGA	PALOMINO	VALDIMIRO		1972-10-15	M	2025-07-15	07:33			903818	4500
CC	9154673	ZUÑIGA	PALOMINO	VALDIMIRO		1972-10-15	M	2025-07-15	07:33			903868	6000
CC	5991410	MACIAS	LOZANO	MARCO	ANTONIO	1965-04-28	M	2025-07-15	07:39			903841	4500
CC	5991410	MACIAS	LOZANO	MARCO	ANTONIO	1965-04-28	M	2025-07-15	07:39			903818	4500
CC	5991410	MACIAS	LOZANO	MARCO	ANTONIO	1965-04-28	M	2025-07-15	07:39			903868	6000
AS	COL965171	GARCIA		LUIS	ANGEL	1975-08-04	M	2025-07-15	07:45			903841	4500
AS	COL965171	GARCIA		LUIS	ANGEL	1975-08-04	M	2025-07-15	07:45			903818	4500
AS	COL965171	GARCIA		LUIS	ANGEL	1975-08-04	M	2025-07-15	07:45			903868	6000
CC	10941364	HUMANES	JULIO	HECTOR	DAVID	1972-01-27	M	2025-07-15	07:51			903841	4500
CC	10941364	HUMANES	JULIO	HECTOR	DAVID	1972-01-27	M	2025-07-15	07:51			903818	4500
CC	10941364	HUMANES	JULIO	HECTOR	DAVID	1972-01-27	M	2025-07-15	07:51			903868	6000
CC	8798374	JIMENEZ	JUNIRELES	LUIS	ALBERTO	1977-12-22	M	2025-07-15	07:57			903841	4500
CC	8798374	JIMENEZ	JUNIRELES	LUIS	ALBERTO	1977-12-22	M	2025-07-15	07:57			903818	4500
CC	8798374	JIMENEZ	JUNIRELES	LUIS	ALBERTO	1977-12-22	M	2025-07-15	07:57			903868	6000
CC	1071143804	URREGO	ALVAREZ	ANGELA	PATRICIA	1995-06-15	F	2025-07-15	08:03			904508	6000
CC	1000379815	SUAREZ	GONZALEZ	PAOLA	ANDREA	2001-08-22	F	2025-07-15	08:09			904508	6000
CC	24872063	GUTIERREZ	DE MUÑOZ	MARIA 	DEL ROSARIO	1942-09-02	F	2025-07-15	08:14			902207	6000
CC	24872063	GUTIERREZ	DE MUÑOZ	MARIA 	DEL ROSARIO	1942-09-02	F	2025-07-15	08:14			903841	6000
CC	24872063	GUTIERREZ	DE MUÑOZ	MARIA 	DEL ROSARIO	1942-09-02	F	2025-07-15	08:14			903818	6000
CC	24872063	GUTIERREZ	DE MUÑOZ	MARIA 	DEL ROSARIO	1942-09-02	F	2025-07-15	08:14			903815	6000
CC	24872063	GUTIERREZ	DE MUÑOZ	MARIA 	DEL ROSARIO	1942-09-02	F	2025-07-15	08:14			903868	6000
CC	24872063	GUTIERREZ	DE MUÑOZ	MARIA 	DEL ROSARIO	1942-09-02	F	2025-07-15	08:14			903856	6000
CC	24872063	GUTIERREZ	DE MUÑOZ	MARIA 	DEL ROSARIO	1942-09-02	F	2025-07-15	08:14			903895	6000
CC	24872063	GUTIERREZ	DE MUÑOZ	MARIA 	DEL ROSARIO	1942-09-02	F	2025-07-15	08:14			907106	6000
CC	79996004	BELTRAN	CHALA	JAIDER	ALEXER	1981-03-17	M	2025-07-15	08:23			902207	6000
CC	79996004	BELTRAN	CHALA	JAIDER	ALEXER	1981-03-17	M	2025-07-15	08:23			903841	6000
CC	79996004	BELTRAN	CHALA	JAIDER	ALEXER	1981-03-17	M	2025-07-15	08:23			903818	6000
CC	79996004	BELTRAN	CHALA	JAIDER	ALEXER	1981-03-17	M	2025-07-15	08:23			903815	6000
CC	79996004	BELTRAN	CHALA	JAIDER	ALEXER	1981-03-17	M	2025-07-15	08:23			903868	6000
CC	79996004	BELTRAN	CHALA	JAIDER	ALEXER	1981-03-17	M	2025-07-15	08:23			907106	6000
CC	52373028	FANDIÑO	AVILA	CLAUDIA	YANETH	1978-04-02	F	2025-07-15	08:35			902207	7000
CC	52373028	FANDIÑO	AVILA	CLAUDIA	YANETH	1978-04-02	F	2025-07-15	08:35			903841	7000
CC	52373028	FANDIÑO	AVILA	CLAUDIA	YANETH	1978-04-02	F	2025-07-15	08:35			903818	7000
CC	52373028	FANDIÑO	AVILA	CLAUDIA	YANETH	1978-04-02	F	2025-07-15	08:35			903815	9000
CC	52373028	FANDIÑO	AVILA	CLAUDIA	YANETH	1978-04-02	F	2025-07-15	08:35			903868	9000
CC	78305038	SUAREZ	ARRIETA	CARLOS	JOSE	1981-01-08	M	2025-07-15	08:45			903815	7000
CC	78305038	SUAREZ	ARRIETA	CARLOS	JOSE	1981-01-08	M	2025-07-15	08:45			903868	9000
CC	1010227403	GOMEZ	ESPINOSA	INGRID	VANESSA	1996-04-13	F	2025-07-16	07:15			903818	5000
CC	1010227403	GOMEZ	ESPINOSA	INGRID	VANESSA	1996-04-13	F	2025-07-16	07:15			903868	6000
CC	1129493910	RUIZ	PEREZ	LUIS	FERNANDO	1993-06-05	M	2025-07-16	07:23			903841	4500
CC	1129493910	RUIZ	PEREZ	LUIS	FERNANDO	1993-06-05	M	2025-07-16	07:23			903818	4500
CC	1129493910	RUIZ	PEREZ	LUIS	FERNANDO	1993-06-05	M	2025-07-16	07:23			903868	6000
CC	1066743797	BUSTAMANTE	FLOREZ	JOSE	CARLOS	1994-03-18	M	2025-07-16	07:29			903841	4500
CC	1066743797	BUSTAMANTE	FLOREZ	JOSE	CARLOS	1994-03-18	M	2025-07-16	07:29			903818	4500
CC	1066743797	BUSTAMANTE	FLOREZ	JOSE	CARLOS	1994-03-18	M	2025-07-16	07:29			903868	6000
CC	77104641	RAMIREZ	VILLA	JOSE	ALVEIRO	1977-01-18	M	2025-07-16	07:35			903841	4500
CC	77104641	RAMIREZ	VILLA	JOSE	ALVEIRO	1977-01-18	M	2025-07-16	07:35			903818	4500
CC	77104641	RAMIREZ	VILLA	JOSE	ALVEIRO	1977-01-18	M	2025-07-16	07:35			903868	6000
CC	1007360193	DE ARCO	PEREA	YOIBER		1999-02-13	M	2025-07-16	07:41			903841	4500
CC	1007360193	DE ARCO	PEREA	YOIBER		1999-02-13	M	2025-07-16	07:41			903818	4500
CC	1007360193	DE ARCO	PEREA	YOIBER		1999-02-13	M	2025-07-16	07:41			903868	6000
CC	1002230933	ROSALES	RAMOS	PEDRO	LUIS	1992-04-18	M	2025-07-16	07:47			903841	4500
CC	1002230933	ROSALES	RAMOS	PEDRO	LUIS	1992-04-18	M	2025-07-16	07:47			903818	4500
CC	1002230933	ROSALES	RAMOS	PEDRO	LUIS	1992-04-18	M	2025-07-16	07:47			903868	6000
CC	1030592588	OSORIO	SUAREZ	LUZ	MARCELA	1991-01-14	F	2025-07-16	07:53			903841	4500
CC	1030592588	OSORIO	SUAREZ	LUZ	MARCELA	1991-01-14	F	2025-07-16	07:53			903818	4500
CC	1030592588	OSORIO	SUAREZ	LUZ	MARCELA	1991-01-14	F	2025-07-16	07:53			903868	6000
AS	COL5759943	FERREIRA	FERNANDEZ	ANTHONY	ENRIQUE	1992-10-10	M	2025-07-16	07:59			903841	4500
AS	COL5759943	FERREIRA	FERNANDEZ	ANTHONY	ENRIQUE	1992-10-10	M	2025-07-16	07:59			903818	4500
AS	COL5759943	FERREIRA	FERNANDEZ	ANTHONY	ENRIQUE	1992-10-10	M	2025-07-16	07:59			903868	6000
CC	1051568661	RODRIGUEZ	JIMENEZ	YUBER	CAMILO	1995-08-20	M	2025-07-16	08:05			903841	4500
CC	1051568661	RODRIGUEZ	JIMENEZ	YUBER	CAMILO	1995-08-20	M	2025-07-16	08:05			903818	4500
CC	1051568661	RODRIGUEZ	JIMENEZ	YUBER	CAMILO	1995-08-20	M	2025-07-16	08:05			903868	6000
CC	16489077	RAMIREZ	VALENCIA	EDISON		1964-12-15	M	2025-07-16	08:11			903841	4500
CC	16489077	RAMIREZ	VALENCIA	EDISON		1964-12-15	M	2025-07-16	08:11			903818	4500
CC	16489077	RAMIREZ	VALENCIA	EDISON		1964-12-15	M	2025-07-16	08:11			903868	6000
CC	1072592476	VELASCO	SANCHEZ	YULY	PAOLA	1989-07-05	F	2025-07-16	08:17			904508	6000
CC	1014268112	MONTAÑA	AMAYA	ESTEFANY		1995-07-31	F	2025-07-16	08:25			904508	6000
CC	1082410440	OROZCO	HERNANDEZ	FRAIN	DAVID	1994-04-30	M	2025-07-16	08:35			903841	4500
CC	1082410440	OROZCO	HERNANDEZ	FRAIN	DAVID	1994-04-30	M	2025-07-16	08:35			903818	4500
CC	1082410440	OROZCO	HERNANDEZ	FRAIN	DAVID	1994-04-30	M	2025-07-16	08:35			903868	6000
CC	80657373	QUEVEDO	BARBOSA	VICENCIO		1983-05-02	M	2025-07-16	08:41			903841	4500
CC	80657373	QUEVEDO	BARBOSA	VICENCIO		1983-05-02	M	2025-07-16	08:41			903818	4500
CC	80657373	QUEVEDO	BARBOSA	VICENCIO		1983-05-02	M	2025-07-16	08:41			903868	6000
AS	COL1027464	SUAREZ	MANZANO	MARCO	ANTONIO	1979-12-22	M	2025-07-16	08:47			903841	4500
AS	COL1027464	SUAREZ	MANZANO	MARCO	ANTONIO	1979-12-22	M	2025-07-16	08:47			903818	4500
AS	COL1027464	SUAREZ	MANZANO	MARCO	ANTONIO	1979-12-22	M	2025-07-16	08:47			903868	6000
CC	1233496178	CEDEÑO	LIZCANO	YINA	YOMAIRA	1998-04-24	F	2025-07-16	08:53			904508	6000
CC	1032317	ORTIZ	TELLEZ	ALVARO		1935-06-19	M	2025-07-17	07:15			903895	7000
CC	1100542900	RAMIREZ	ALVAREZ	DANIEL	ENRIQUE	2004-09-21	M	2025-07-17	07:25			903841	4500
CC	1100542900	RAMIREZ	ALVAREZ	DANIEL	ENRIQUE	2004-09-21	M	2025-07-17	07:25			903818	4500
CC	1100542900	RAMIREZ	ALVAREZ	DANIEL	ENRIQUE	2004-09-21	M	2025-07-17	07:25			903868	6000
CC	1066084236	SANCHEZ	QUINTERO	ANDERSON	STEVEN	2005-10-01	M	2025-07-17	07:31			903841	4500
CC	1066084236	SANCHEZ	QUINTERO	ANDERSON	STEVEN	2005-10-01	M	2025-07-17	07:31			903818	4500
CC	1066084236	SANCHEZ	QUINTERO	ANDERSON	STEVEN	2005-10-01	M	2025-07-17	07:31			903868	6000
CC	12448803	PAREJO	RODRIGUEZ	ARAMIS	ALBEIRO	1980-10-24	M	2025-07-17	07:37			903841	4500
CC	12448803	PAREJO	RODRIGUEZ	ARAMIS	ALBEIRO	1980-10-24	M	2025-07-17	07:37			903818	4500
CC	12448803	PAREJO	RODRIGUEZ	ARAMIS	ALBEIRO	1980-10-24	M	2025-07-17	07:37			903868	6000
CC	1089515215	AVILA	RODRIGUEZ	ELKIN	YOAN	1999-03-10	M	2025-07-17	07:43			903841	4500
CC	1089515215	AVILA	RODRIGUEZ	ELKIN	YOAN	1999-03-10	M	2025-07-17	07:43			903818	4500
CC	1089515215	AVILA	RODRIGUEZ	ELKIN	YOAN	1999-03-10	M	2025-07-17	07:43			903868	6000
CC	1014293601	MOSQUERA	MOSQUERA	WILBER	ALEXANDER	1997-11-24	M	2025-07-17	07:49			903841	4500
CC	1014293601	MOSQUERA	MOSQUERA	WILBER	ALEXANDER	1997-11-24	M	2025-07-17	07:49			903818	4500
CC	1014293601	MOSQUERA	MOSQUERA	WILBER	ALEXANDER	1997-11-24	M	2025-07-17	07:49			903868	6000
CC	51982652	OBANDO	VERA	AYDA	ISABELA	1969-04-04	M	2025-07-17	07:55			901325	6000
CC	51982652	OBANDO	VERA	AYDA	ISABELA	1969-04-04	M	2025-07-17	07:55			901305	5000
CC	51982652	OBANDO	VERA	AYDA	ISABELA	1969-04-04	M	2025-07-17	07:55			907002	6000
CC	1030555222	ESCOBAR	CRUZ	DAYSI	JOHANNA	1988-11-02	F	2025-07-17	08:01			904508	6000
CC	51990740	CASTILLO	MUÑOZ	LILIANA	MARGOTH	1969-08-22	F	2025-07-17	08:10			901325	6000
CC	51990740	CASTILLO	MUÑOZ	LILIANA	MARGOTH	1969-08-22	F	2025-07-17	08:10			901305	5000
CC	51990740	CASTILLO	MUÑOZ	LILIANA	MARGOTH	1969-08-22	F	2025-07-17	08:10			907002	6000
CC	52912200	ORDOÑEZ	DIAZ	LEIDI	LISBETH	1982-09-01	F	2025-07-17	08:16			904508	6000
CC	80439681	GUATAQUIRA		AQUILEO		1971-10-21	M	2025-07-17	08:25			902207	7000
CC	80439681	GUATAQUIRA		AQUILEO		1971-10-21	M	2025-07-17	08:25			903841	7000
CC	80439681	GUATAQUIRA		AQUILEO		1971-10-21	M	2025-07-17	08:25			903818	7000
CC	80439681	GUATAQUIRA		AQUILEO		1971-10-21	M	2025-07-17	08:25			903868	9000
CC	80439681	GUATAQUIRA		AQUILEO		1971-10-21	M	2025-07-17	08:25			903801	7000
CC	80439681	GUATAQUIRA		AQUILEO		1971-10-21	M	2025-07-17	08:25			903866	9000
CC	80439681	GUATAQUIRA		AQUILEO		1971-10-21	M	2025-07-17	08:25			903867	9000
CC	80439681	GUATAQUIRA		AQUILEO		1971-10-21	M	2025-07-17	08:25			907106	7000
CC	20440391	AMORTEGUI	DE RIOS	ROSA	ESTER	1953-12-21	F	2025-07-17	08:41			902207	7000
CC	20440391	AMORTEGUI	DE RIOS	ROSA	ESTER	1953-12-21	F	2025-07-17	08:41			907106	7000
CC	80390528	HORTUA	RINCON	PEDRO	ALIRIO	1968-12-26	M	2025-07-17	08:45			902207	6000
CC	80390528	HORTUA	RINCON	PEDRO	ALIRIO	1968-12-26	M	2025-07-17	08:45			903841	6000
CC	80390528	HORTUA	RINCON	PEDRO	ALIRIO	1968-12-26	M	2025-07-17	08:45			903818	6000
CC	80390528	HORTUA	RINCON	PEDRO	ALIRIO	1968-12-26	M	2025-07-17	08:45			903815	6000
CC	80390528	HORTUA	RINCON	PEDRO	ALIRIO	1968-12-26	M	2025-07-17	08:45			903868	6000
CC	80390528	HORTUA	RINCON	PEDRO	ALIRIO	1968-12-26	M	2025-07-17	08:45			907106	6000
AS	COL6827148	MONTIEL	OLIVAR	MAIRA	NATIVIDAD	1968-02-17	F	2025-07-17	08:57			904508	9000
RC	1011332238	RAMIREZ	ARIAS	ANTONIA		2024-07-15	F	2025-07-17	09:02			907002	7000
CC	80437084	GUAMAN	CRUZ	JUAN	JOSE	1970-08-25	M	2025-07-18	07:15			903868	9000
CC	53012343	PEREZ	CORREA	CLAUDIA	XIMENA	1984-06-25	F	2025-07-18	07:20			904508	9000
CC	1032373526	PALOMINO	RODRIGUEZ	ANDREA	VIVIANA	1986-09-19	F	2025-07-18	07:25			904508	9000
CC	2502205	MEDINA	GOMEZ	HENRY		1971-08-31	M	2025-07-18	07:30			903841	4500
CC	2502205	MEDINA	GOMEZ	HENRY		1971-08-31	M	2025-07-18	07:30			903818	4500
CC	2502205	MEDINA	GOMEZ	HENRY		1971-08-31	M	2025-07-18	07:30			903868	6000
CC	1067038053	GODOY	FLORIAN	JULIO	CESAR	1986-12-14	M	2025-07-18	07:36			903841	4500
CC	1067038053	GODOY	FLORIAN	JULIO	CESAR	1986-12-14	M	2025-07-18	07:36			903818	4500
CC	1067038053	GODOY	FLORIAN	JULIO	CESAR	1986-12-14	M	2025-07-18	07:36			903868	6000
CC	1100550588	DIAZ	MARTINEZ	RAFAEL	EDUARDO	1997-10-09	M	2025-07-18	07:42			903841	4500
CC	1100550588	DIAZ	MARTINEZ	RAFAEL	EDUARDO	1997-10-09	M	2025-07-18	07:42			903818	4500
CC	1100550588	DIAZ	MARTINEZ	RAFAEL	EDUARDO	1997-10-09	M	2025-07-18	07:42			903868	6000
CC	1087209761	QUIÑONES	PORTOCARRERO	LUIS	FELIPE	1993-05-29	M	2025-07-18	07:48			903841	4500
CC	1087209761	QUIÑONES	PORTOCARRERO	LUIS	FELIPE	1993-05-29	M	2025-07-18	07:48			903818	4500
CC	1087209761	QUIÑONES	PORTOCARRERO	LUIS	FELIPE	1993-05-29	M	2025-07-18	07:48			903868	6000
CC	1063289841	BEDOYA	DE LA OSSA	RAUL	ALFONSO	1991-04-29	M	2025-07-18	07:54			903841	4500
CC	1063289841	BEDOYA	DE LA OSSA	RAUL	ALFONSO	1991-04-29	M	2025-07-18	07:54			903818	4500
CC	1063289841	BEDOYA	DE LA OSSA	RAUL	ALFONSO	1991-04-29	M	2025-07-18	07:54			903868	6000
CC	72216010	CABRERA	DE MOYA	DANILSON	ALBERTO	1975-12-01	M	2025-07-18	08:00			903841	4500
CC	72216010	CABRERA	DE MOYA	DANILSON	ALBERTO	1975-12-01	M	2025-07-18	08:00			903818	4500
CC	72216010	CABRERA	DE MOYA	DANILSON	ALBERTO	1975-12-01	M	2025-07-18	08:00			903868	6000
CC	1046340942	CORTES	CARREÑO	YOEL	ENRIQUE	1991-03-26	M	2025-07-18	08:06			903841	4500
CC	1046340942	CORTES	CARREÑO	YOEL	ENRIQUE	1991-03-26	M	2025-07-18	08:06			903818	4500
CC	1046340942	CORTES	CARREÑO	YOEL	ENRIQUE	1991-03-26	M	2025-07-18	08:06			903868	6000
CC	1067283684	PINEDA	FLOREZ	RAFAEL	JOSE	1987-08-24	M	2025-07-18	08:12			903841	4500
CC	1067283684	PINEDA	FLOREZ	RAFAEL	JOSE	1987-08-24	M	2025-07-18	08:12			903818	4500
CC	1067283684	PINEDA	FLOREZ	RAFAEL	JOSE	1987-08-24	M	2025-07-18	08:12			903868	6000
CC	1064186421	MARTINEZ	MARTINEZ	JAIME	JESUS	1993-05-31	M	2025-07-18	08:18			903841	4500
CC	1064186421	MARTINEZ	MARTINEZ	JAIME	JESUS	1993-05-31	M	2025-07-18	08:18			903818	4500
CC	1064186421	MARTINEZ	MARTINEZ	JAIME	JESUS	1993-05-31	M	2025-07-18	08:18			903868	6000
CC	3230678	GONZALEZ	DUARTE	JAIME	ANDRES	1981-12-31	M	2025-07-18	08:24			903841	4500
CC	3230678	GONZALEZ	DUARTE	JAIME	ANDRES	1981-12-31	M	2025-07-18	08:24			903818	4500
CC	3230678	GONZALEZ	DUARTE	JAIME	ANDRES	1981-12-31	M	2025-07-18	08:24			903868	6000
CC	1010840618	MORA	MORALES	NIUMAN	ESTIBEN	2005-05-22	M	2025-07-18	08:30			903841	4500
CC	1010840618	MORA	MORALES	NIUMAN	ESTIBEN	2005-05-22	M	2025-07-18	08:30			903818	4500
CC	1010840618	MORA	MORALES	NIUMAN	ESTIBEN	2005-05-22	M	2025-07-18	08:30			903868	6000
CC	1007195177	ESCUDERO	THERAN	VICTOR	ALFONSO	1987-09-10	M	2025-07-18	08:36			903841	4500
CC	1007195177	ESCUDERO	THERAN	VICTOR	ALFONSO	1987-09-10	M	2025-07-18	08:36			903818	4500
CC	1007195177	ESCUDERO	THERAN	VICTOR	ALFONSO	1987-09-10	M	2025-07-18	08:36			903868	6000
RC	1012480778	MADRID	AREVALO	MATHIAS	JOSE	2023-11-19	M	2025-07-18	08:42			907002	7000
CC	79836802	MALAGON	SUAREZ	JAVIER	IGNACIO	1976-03-22	M	2025-07-18	08:50			902207	7000
CC	79836802	MALAGON	SUAREZ	JAVIER	IGNACIO	1976-03-22	M	2025-07-18	08:50			903841	7000
CC	79836802	MALAGON	SUAREZ	JAVIER	IGNACIO	1976-03-22	M	2025-07-18	08:50			903818	7000
CC	79836802	MALAGON	SUAREZ	JAVIER	IGNACIO	1976-03-22	M	2025-07-18	08:50			903868	9000
CC	79836802	MALAGON	SUAREZ	JAVIER	IGNACIO	1976-03-22	M	2025-07-18	08:50			907106	7000
CC	79836802	MALAGON	SUAREZ	JAVIER	IGNACIO	1976-03-22	M	2025-07-18	08:50			907002	7000
CC	19182142	FRANCO		LUIS	ALBERTO	1951-09-27	M	2025-07-19	07:15			903895	7000
CC	1054680050	SAENZ	CASTILLO	MARIA	CONSUELO	1990-11-26	F	2025-07-19	07:20			903841	4500
CC	1054680050	SAENZ	CASTILLO	MARIA	CONSUELO	1990-11-26	F	2025-07-19	07:20			903818	4500
CC	1054680050	SAENZ	CASTILLO	MARIA	CONSUELO	1990-11-26	F	2025-07-19	07:20			903868	6000
CC	8499328	BORJA	CAMARGO	JEINER	RAFAEL	1985-02-08	M	2025-07-19	07:26			903841	4500
CC	8499328	BORJA	CAMARGO	JEINER	RAFAEL	1985-02-08	M	2025-07-19	07:26			903818	4500
CC	8499328	BORJA	CAMARGO	JEINER	RAFAEL	1985-02-08	M	2025-07-19	07:26			903868	6000
CC	1040508199	ARCIA	BUELVAS	JUAN	ESTEBAN	1993-01-22	M	2025-07-19	07:32			903841	4500
CC	1040508199	ARCIA	BUELVAS	JUAN	ESTEBAN	1993-01-22	M	2025-07-19	07:32			903818	4500
CC	1040508199	ARCIA	BUELVAS	JUAN	ESTEBAN	1993-01-22	M	2025-07-19	07:32			903868	6000
CC	1002353983	TORRECILLA	RANGEL	STEVEN		2003-10-09	M	2025-07-19	07:38			903841	4500
CC	1002353983	TORRECILLA	RANGEL	STEVEN		2003-10-09	M	2025-07-19	07:38			903818	4500
CC	1002353983	TORRECILLA	RANGEL	STEVEN		2003-10-09	M	2025-07-19	07:38			903868	6000
CC	1140916962	RUIZ	CARDENAS	JEIDER	SANTIAGO	2006-10-17	M	2025-07-19	07:44			903841	4500
CC	1140916962	RUIZ	CARDENAS	JEIDER	SANTIAGO	2006-10-17	M	2025-07-19	07:44			903818	4500
CC	1140916962	RUIZ	CARDENAS	JEIDER	SANTIAGO	2006-10-17	M	2025-07-19	07:44			903868	6000
CC	80758842	FERREIRA	LUNA	LUIS	EDUARDO	1982-07-23	M	2025-07-19	07:50			903841	4500
CC	80758842	FERREIRA	LUNA	LUIS	EDUARDO	1982-07-23	M	2025-07-19	07:50			903818	4500
CC	80758842	FERREIRA	LUNA	LUIS	EDUARDO	1982-07-23	M	2025-07-19	07:50			903868	6000
CC	87944996	DAJOME		JESUS	ROBER	1983-03-16	M	2025-07-19	07:56			903841	4500
CC	87944996	DAJOME		JESUS	ROBER	1983-03-16	M	2025-07-19	07:56			903818	4500
CC	87944996	DAJOME		JESUS	ROBER	1983-03-16	M	2025-07-19	07:56			903868	6000
CC	1066752712	SERPA	MORALES	MARIA	SOFIA	1999-02-09	F	2025-07-19	08:02			903841	4500
CC	1066752712	SERPA	MORALES	MARIA	SOFIA	1999-02-09	F	2025-07-19	08:02			903818	4500
CC	1066752712	SERPA	MORALES	MARIA	SOFIA	1999-02-09	F	2025-07-19	08:02			903868	6000
CC	4591937	GONZALEZ	RAMIREZ	NESTOR		1971-07-03	M	2025-07-19	08:08			903841	4500
CC	4591937	GONZALEZ	RAMIREZ	NESTOR		1971-07-03	M	2025-07-19	08:08			903818	4500
CC	4591937	GONZALEZ	RAMIREZ	NESTOR		1971-07-03	M	2025-07-19	08:08			903868	6000
CC	1012434114	HERRERA	COLMENARES	OSCAR	VICENTE	1996-07-18	M	2025-07-19	08:14			903841	4500
CC	1012434114	HERRERA	COLMENARES	OSCAR	VICENTE	1996-07-18	M	2025-07-19	08:14			903818	4500
CC	1012434114	HERRERA	COLMENARES	OSCAR	VICENTE	1996-07-18	M	2025-07-19	08:14			903868	6000
CC	1148700592	RODRIGUEZ	TAPIA	CIRO	RAUL	1995-03-09	M	2025-07-19	08:20			903841	4500
CC	1148700592	RODRIGUEZ	TAPIA	CIRO	RAUL	1995-03-09	M	2025-07-19	08:20			903818	4500
CC	1148700592	RODRIGUEZ	TAPIA	CIRO	RAUL	1995-03-09	M	2025-07-19	08:20			903868	6000
CC	1065828017	RADA	FONTALVO	JOSE	ALFREDO	1996-09-11	M	2025-07-19	08:26			903841	4500
CC	1065828017	RADA	FONTALVO	JOSE	ALFREDO	1996-09-11	M	2025-07-19	08:26			903818	4500
CC	1065828017	RADA	FONTALVO	JOSE	ALFREDO	1996-09-11	M	2025-07-19	08:26			903868	6000
CC	41601075	ALARCON	DE SOTO	ELIODORA		1953-02-13	F	2025-07-19	08:32			902207	7000
CC	41601075	ALARCON	DE SOTO	ELIODORA		1953-02-13	F	2025-07-19	08:32			903841	7000
CC	41601075	ALARCON	DE SOTO	ELIODORA		1953-02-13	F	2025-07-19	08:32			903818	7000
CC	41601075	ALARCON	DE SOTO	ELIODORA		1953-02-13	F	2025-07-19	08:32			903868	9000
CC	41601075	ALARCON	DE SOTO	ELIODORA		1953-02-13	F	2025-07-19	08:32			907106	7000
CC	80456800	GARZON	ARIAS	WILLIAM		1983-07-02	M	2025-07-19	08:42			902207	6000
CC	80456800	GARZON	ARIAS	WILLIAM		1983-07-02	M	2025-07-19	08:42			903841	6000
CC	80456800	GARZON	ARIAS	WILLIAM		1983-07-02	M	2025-07-19	08:42			903818	6000
CC	80456800	GARZON	ARIAS	WILLIAM		1983-07-02	M	2025-07-19	08:42			903815	6000
CC	80456800	GARZON	ARIAS	WILLIAM		1983-07-02	M	2025-07-19	08:42			903868	6000
CC	80456800	GARZON	ARIAS	WILLIAM		1983-07-02	M	2025-07-19	08:42			907106	6000
CC	25218816	OSPINA	CARDONA	MARIA	CELIA	1928-08-04	F	2025-07-21	07:15			907106	7000
CC	1007473013	ABRIL	OSUNA	SOFIA		2003-04-08	F	2025-07-21	07:20			904508	9000
CC	4113260	TORRES	BERNAL	LUCAS		1965-10-04	M	2025-07-21	07:25			903841	4500
CC	4113260	TORRES	BERNAL	LUCAS		1965-10-04	M	2025-07-21	07:25			903818	4500
CC	4113260	TORRES	BERNAL	LUCAS		1965-10-04	M	2025-07-21	07:25			903868	6000
CC	39644213	SEGURA	TORRES	MARIA	ADELA	1966-06-19	F	2025-07-21	07:31			903841	4500
CC	39644213	SEGURA	TORRES	MARIA	ADELA	1966-06-19	F	2025-07-21	07:31			903818	4500
CC	39644213	SEGURA	TORRES	MARIA	ADELA	1966-06-19	F	2025-07-21	07:31			903868	6000
CC	51633219	DOMINGUEZ	AVENDAÑO	MARIA	CERLINA	1961-10-12	F	2025-07-21	07:36			903841	4500
CC	51633219	DOMINGUEZ	AVENDAÑO	MARIA	CERLINA	1961-10-12	F	2025-07-21	07:36			903818	4500
CC	51633219	DOMINGUEZ	AVENDAÑO	MARIA	CERLINA	1961-10-12	F	2025-07-21	07:36			903868	6000
CC	1120744784	BRITO	NIEVES	JOSY	ROSELIN	1991-01-14	F	2025-07-21	07:43			903841	4500
CC	1120744784	BRITO	NIEVES	JOSY	ROSELIN	1991-01-14	F	2025-07-21	07:43			903818	4500
CC	1120744784	BRITO	NIEVES	JOSY	ROSELIN	1991-01-14	F	2025-07-21	07:43			903868	6000
CC	1193235639	MARTINEZ	PINEDA	LUIS	CARMELO	1994-07-16	M	2025-07-21	07:49			903841	4500
CC	1193235639	MARTINEZ	PINEDA	LUIS	CARMELO	1994-07-16	M	2025-07-21	07:49			903818	4500
CC	1193235639	MARTINEZ	PINEDA	LUIS	CARMELO	1994-07-16	M	2025-07-21	07:49			903868	6000
CC	1193456625	ARIAS	GULLOSO	JOSE	DAVID	1991-11-04	M	2025-07-21	07:55			903841	4500
CC	1193456625	ARIAS	GULLOSO	JOSE	DAVID	1991-11-04	M	2025-07-21	07:55			903818	4500
CC	1193456625	ARIAS	GULLOSO	JOSE	DAVID	1991-11-04	M	2025-07-21	07:55			903868	6000
CC	82140682	ANDRADES	RIOS	JHON	DABRIN	1982-03-19	M	2025-07-21	08:01			903841	4500
CC	82140682	ANDRADES	RIOS	JHON	DABRIN	1982-03-19	M	2025-07-21	08:01			903818	4500
CC	82140682	ANDRADES	RIOS	JHON	DABRIN	1982-03-19	M	2025-07-21	08:01			903868	6000
CC	1044910042	CARMONA	SERRANO	JUAN	CARLOS	1986-02-23	M	2025-07-21	08:07			903841	4500
CC	1044910042	CARMONA	SERRANO	JUAN	CARLOS	1986-02-23	M	2025-07-21	08:07			903818	4500
CC	1044910042	CARMONA	SERRANO	JUAN	CARLOS	1986-02-23	M	2025-07-21	08:07			903868	6000
CC	10888552	OSORIO	SAGRAS	ALEXANDER		1994-09-01	M	2025-07-21	08:13			903841	4500
CC	10888552	OSORIO	SAGRAS	ALEXANDER		1994-09-01	M	2025-07-21	08:13			903818	4500
CC	10888552	OSORIO	SAGRAS	ALEXANDER		1994-09-01	M	2025-07-21	08:13			903868	6000
CC	73243501	ARRIETA	ESPINOZA	BLADIMIR	DE JESUS	1978-01-13	M	2025-07-21	08:19			903841	4500
CC	73243501	ARRIETA	ESPINOZA	BLADIMIR	DE JESUS	1978-01-13	M	2025-07-21	08:19			903818	4500
CC	73243501	ARRIETA	ESPINOZA	BLADIMIR	DE JESUS	1978-01-13	M	2025-07-21	08:19			903868	6000
CC	1105679452	TOVAR	VARGAS	JORGE	LUIS	1989-11-15	M	2025-07-21	08:25			903841	4500
CC	1105679452	TOVAR	VARGAS	JORGE	LUIS	1989-11-15	M	2025-07-21	08:25			903818	4500
CC	1105679452	TOVAR	VARGAS	JORGE	LUIS	1989-11-15	M	2025-07-21	08:25			903868	6000
CC	1003409279	ALEAN	PAUTT	JEISON	ALBERTO	1994-11-21	M	2025-07-21	08:31			903841	4500
CC	1003409279	ALEAN	PAUTT	JEISON	ALBERTO	1994-11-21	M	2025-07-21	08:31			903818	4500
CC	1003409279	ALEAN	PAUTT	JEISON	ALBERTO	1994-11-21	M	2025-07-21	08:31			903868	6000
CC	19485273	VALDERRAMA	TRUJILLO	JULIO	CESAR	1959-04-10	M	2025-07-21	08:37			902207	7000
CC	19485273	VALDERRAMA	TRUJILLO	JULIO	CESAR	1959-04-10	M	2025-07-21	08:37			906914	9000
CC	79763740	SIERRA	DUQUE	MICHEL	OSWALDO	1979-09-29	M	2025-07-21	08:42			903895	7000
CC	1030671459	DUARTE	MORALES	JULIETH	SORANY	1996-10-12	F	2025-07-21	08:47			904508	9000
CC	1012317037	MOSQUERA	MORALES	JULIETH	VALENTINA	2003-12-04	F	2025-07-21	08:52			904508	9000
CC	80267366	DIAZ	ACOSTA	JOSE	MAURICIO	1963-05-30	M	2025-07-22	07:15			903899	7000
CC	51857237	MARTINEZ	SERRATO	BERTA	ENILCE	1967-09-23	F	2025-07-22	07:20			902207	6000
CC	51857237	MARTINEZ	SERRATO	BERTA	ENILCE	1967-09-23	F	2025-07-22	07:20			903841	6000
CC	51857237	MARTINEZ	SERRATO	BERTA	ENILCE	1967-09-23	F	2025-07-22	07:20			903818	6000
CC	51857237	MARTINEZ	SERRATO	BERTA	ENILCE	1967-09-23	F	2025-07-22	07:20			903815	6000
CC	51857237	MARTINEZ	SERRATO	BERTA	ENILCE	1967-09-23	F	2025-07-22	07:20			903868	6000
CC	1012448178	VIRGUEZ	PEREZ	TANIA	DAYANNE	1997-12-10	F	2025-07-22	07:30			902207	6000
CC	1012448178	VIRGUEZ	PEREZ	TANIA	DAYANNE	1997-12-10	F	2025-07-22	07:30			903841	6000
CC	1012448178	VIRGUEZ	PEREZ	TANIA	DAYANNE	1997-12-10	F	2025-07-22	07:30			903818	6000
CC	1012448178	VIRGUEZ	PEREZ	TANIA	DAYANNE	1997-12-10	F	2025-07-22	07:30			903815	6000
CC	1012448178	VIRGUEZ	PEREZ	TANIA	DAYANNE	1997-12-10	F	2025-07-22	07:30			903868	6000
RC	1023989768	ZAMUDIO	SANCHEZ	JOEL	JOSE	2022-04-17	M	2025-07-22	07:40			902207	6000
RC	1023989768	ZAMUDIO	SANCHEZ	JOEL	JOSE	2022-04-17	M	2025-07-22	07:40			903841	6000
TI	1013686625	BRICEÑO	GUAYAMBUCO	MARIA	FERNANDA	2017-03-20	F	2025-07-22	07:50			907002	7000
CC	1100548703	NUÑEZ	RAMIREZ	JORGE	ISAAC	1995-02-05	M	2025-07-22	07:55			903841	4500
CC	1100548703	NUÑEZ	RAMIREZ	JORGE	ISAAC	1995-02-05	M	2025-07-22	07:55			903818	4500
CC	1100548703	NUÑEZ	RAMIREZ	JORGE	ISAAC	1995-02-05	M	2025-07-22	07:55			903868	6000
CC	1110461187	BARRIOS	GUERRA	JAIRO	ENRIQUE	1987-03-10	M	2025-07-22	08:01			903841	4500
CC	1110461187	BARRIOS	GUERRA	JAIRO	ENRIQUE	1987-03-10	M	2025-07-22	08:01			903818	4500
CC	1110461187	BARRIOS	GUERRA	JAIRO	ENRIQUE	1987-03-10	M	2025-07-22	08:01			903868	6000
CC	17788429	ANDRADE	APACHE	ABEL		1984-02-24	M	2025-07-22	08:07			903841	4500
CC	17788429	ANDRADE	APACHE	ABEL		1984-02-24	M	2025-07-22	08:07			903818	4500
CC	17788429	ANDRADE	APACHE	ABEL		1984-02-24	M	2025-07-22	08:07			903868	6000
CC	1049895022	PALENCIA	SALAZAR	RANDY		2005-10-29	M	2025-07-22	08:13			903841	4500
CC	1049895022	PALENCIA	SALAZAR	RANDY		2005-10-29	M	2025-07-22	08:13			903818	4500
CC	1049895022	PALENCIA	SALAZAR	RANDY		2005-10-29	M	2025-07-22	08:13			903868	6000
AS	COL5308008	FERNANDEZ	PEDROZO	YOHANDRYS	JOSE	2003-10-14	M	2025-07-22	08:19			903841	4500
AS	COL5308008	FERNANDEZ	PEDROZO	YOHANDRYS	JOSE	2003-10-14	M	2025-07-22	08:19			903818	4500
AS	COL5308008	FERNANDEZ	PEDROZO	YOHANDRYS	JOSE	2003-10-14	M	2025-07-22	08:19			903868	6000
CC	1080010752	BORJA	PEREZ	CARLOS	MANUEL	2004-12-31	M	2025-07-22	08:25			903841	4500
CC	1080010752	BORJA	PEREZ	CARLOS	MANUEL	2004-12-31	M	2025-07-22	08:25			903818	4500
CC	1080010752	BORJA	PEREZ	CARLOS	MANUEL	2004-12-31	M	2025-07-22	08:25			903868	6000
CC	1023957116	AYA	BRICEÑO	MIGUEL	ANGEL	1996-11-29	M	2025-07-22	08:31			903841	4500
CC	1023957116	AYA	BRICEÑO	MIGUEL	ANGEL	1996-11-29	M	2025-07-22	08:31			903818	4500
CC	1023957116	AYA	BRICEÑO	MIGUEL	ANGEL	1996-11-29	M	2025-07-22	08:31			903868	6000
CC	1014312032	ESCALANTE	LEON	FRANKLIN	LEONEL	1994-02-26	M	2025-07-22	08:37			903841	4500
CC	1014312032	ESCALANTE	LEON	FRANKLIN	LEONEL	1994-02-26	M	2025-07-22	08:37			903818	4500
CC	1014312032	ESCALANTE	LEON	FRANKLIN	LEONEL	1994-02-26	M	2025-07-22	08:37			903868	6000
CC	19767890	GOMEZ 	SOSA	ETALIDES		1981-06-23	M	2025-07-22	08:43			903841	4500
CC	19767890	GOMEZ 	SOSA	ETALIDES		1981-06-23	M	2025-07-22	08:43			903818	4500
CC	19767890	GOMEZ 	SOSA	ETALIDES		1981-06-23	M	2025-07-22	08:43			903868	6000
CC	52465704	OSORIO	CORONADO	BLANCA	ISABEL	1979-08-01	F	2025-07-23	07:15			903895	7000
CC	39654521	MANCIPE	PRIETO	NHORA	PATRICIA	1970-01-25	F	2025-07-23	07:25			902207	7000
CC	1033676126	RENDON	FONSECA	HAROLD 	DANIEL	2003-11-08	M	2025-07-23	07:35			903841	4500
CC	1033676126	RENDON	FONSECA	HAROLD 	DANIEL	2003-11-08	M	2025-07-23	07:35			903818	4500
CC	1033676126	RENDON	FONSECA	HAROLD 	DANIEL	2003-11-08	M	2025-07-23	07:35			903868	6000
CC	1063175111	REYES	SOLANO	ARMANDO	JOSE	1996-02-20	M	2025-07-23	07:41			903841	4500
CC	1063175111	REYES	SOLANO	ARMANDO	JOSE	1996-02-20	M	2025-07-23	07:41			903818	4500
CC	1063175111	REYES	SOLANO	ARMANDO	JOSE	1996-02-20	M	2025-07-23	07:41			903868	6000
CC	1010162327	RODRIGUEZ	NIEVES	LEIDY	JUDITH	1985-04-22	F	2025-07-23	07:47			903841	4500
CC	1010162327	RODRIGUEZ	NIEVES	LEIDY	JUDITH	1985-04-22	F	2025-07-23	07:47			903818	4500
CC	1010162327	RODRIGUEZ	NIEVES	LEIDY	JUDITH	1985-04-22	F	2025-07-23	07:47			903868	6000
CC	1018512109	GUAYACUNDO	BARRAGAN	JENNIFER	ANDREA	1999-07-01	F	2025-07-23	07:53			903841	4500
CC	1018512109	GUAYACUNDO	BARRAGAN	JENNIFER	ANDREA	1999-07-01	F	2025-07-23	07:53			903818	4500
CC	1018512109	GUAYACUNDO	BARRAGAN	JENNIFER	ANDREA	1999-07-01	F	2025-07-23	07:53			903868	6000
CC	80426765	CAÑON	MENDIVELSO	MIGUEL	ANTONIO	1973-04-25	M	2025-07-23	07:59			903841	4500
CC	80426765	CAÑON	MENDIVELSO	MIGUEL	ANTONIO	1973-04-25	M	2025-07-23	07:59			903818	4500
CC	80426765	CAÑON	MENDIVELSO	MIGUEL	ANTONIO	1973-04-25	M	2025-07-23	07:59			903868	6000
CC	1000728505	ROJAS	PEREZ	KATERIN	CAMILA	2002-12-31	F	2025-07-24	07:15			904508	9000
CC	1000614057	CASTILLO	ROZO	DUVAN	JULIAN	1998-11-22	M	2025-07-24	07:25			903841	4500
CC	1000614057	CASTILLO	ROZO	DUVAN	JULIAN	1998-11-22	M	2025-07-24	07:25			903818	4500
CC	1000614057	CASTILLO	ROZO	DUVAN	JULIAN	1998-11-22	M	2025-07-24	07:25			903868	6000
CC	1077471091	PALACIOS	BEJARANO	VICTOR	RANGEL	1995-11-25	M	2025-07-24	07:31			903841	4500
CC	1077471091	PALACIOS	BEJARANO	VICTOR	RANGEL	1995-11-25	M	2025-07-24	07:31			903818	4500
CC	1077471091	PALACIOS	BEJARANO	VICTOR	RANGEL	1995-11-25	M	2025-07-24	07:31			903868	6000
AS	COL4861494	SANCHEZ	PIMENTEL	GUILMES 	DAVIS	1990-09-16	M	2025-07-24	07:37			903841	4500
AS	COL4861494	SANCHEZ	PIMENTEL	GUILMES 	DAVIS	1990-09-16	M	2025-07-24	07:37			903818	4500
AS	COL4861494	SANCHEZ	PIMENTEL	GUILMES 	DAVIS	1990-09-16	M	2025-07-24	07:37			903868	6000
CC	1024590003	IBARGUEN	BEJARANO	ISAIAS		1998-05-08	M	2025-07-24	07:43			903841	4500
CC	1024590003	IBARGUEN	BEJARANO	ISAIAS		1998-05-08	M	2025-07-24	07:43			903818	4500
CC	1024590003	IBARGUEN	BEJARANO	ISAIAS		1998-05-08	M	2025-07-24	07:43			903868	6000
CC	1100401455	GAMARRA	DIAZ	CARLOS	EDUARDO	1994-12-15	M	2025-07-24	07:49			903841	4500
CC	1100401455	GAMARRA	DIAZ	CARLOS	EDUARDO	1994-12-15	M	2025-07-24	07:49			903818	4500
CC	1100401455	GAMARRA	DIAZ	CARLOS	EDUARDO	1994-12-15	M	2025-07-24	07:49			903868	6000
CC	1137179076	CUETO	ORTEGA	JUAN	DAVID	1999-07-13	M	2025-07-24	07:55			903841	4500
CC	1137179076	CUETO	ORTEGA	JUAN	DAVID	1999-07-13	M	2025-07-24	07:55			903818	4500
CC	1137179076	CUETO	ORTEGA	JUAN	DAVID	1999-07-13	M	2025-07-24	07:55			903868	6000
CC	1148198820	LOPEZ	VIDES	XAVIER		1986-11-27	M	2025-07-24	08:01			903841	4500
CC	1148198820	LOPEZ	VIDES	XAVIER		1986-11-27	M	2025-07-24	08:01			903818	4500
CC	1148198820	LOPEZ	VIDES	XAVIER		1986-11-27	M	2025-07-24	08:01			903868	6000
CC	1063165387	SUAREZ	DIAZ	MIGUEL	ANGEL	1993-10-31	M	2025-07-24	08:07			903841	4500
CC	1063165387	SUAREZ	DIAZ	MIGUEL	ANGEL	1993-10-31	M	2025-07-24	08:07			903818	4500
CC	1063165387	SUAREZ	DIAZ	MIGUEL	ANGEL	1993-10-31	M	2025-07-24	08:07			903868	6000
CC	1030666765	BROCHERO	PAYOME	YOSI	ESTEBAN	1996-06-15	M	2025-07-24	08:13			903841	4500
CC	1030666765	BROCHERO	PAYOME	YOSI	ESTEBAN	1996-06-15	M	2025-07-24	08:13			903818	4500
CC	1030666765	BROCHERO	PAYOME	YOSI	ESTEBAN	1996-06-15	M	2025-07-24	08:13			903868	6000
CC	1057919663	VARGAS	LEON	ANDRES	BENIGNO	1997-12-03	M	2025-07-24	08:19			903841	4500
CC	1057919663	VARGAS	LEON	ANDRES	BENIGNO	1997-12-03	M	2025-07-24	08:19			903818	4500
CC	1057919663	VARGAS	LEON	ANDRES	BENIGNO	1997-12-03	M	2025-07-24	08:19			903868	6000
CC	1007296721	FLOREZ	GUTIERREZ	DUBERNEY		2000-08-02	M	2025-07-24	08:25			903841	4500
CC	1007296721	FLOREZ	GUTIERREZ	DUBERNEY		2000-08-02	M	2025-07-24	08:25			903818	4500
CC	1007296721	FLOREZ	GUTIERREZ	DUBERNEY		2000-08-02	M	2025-07-24	08:25			903868	6000
CC	1011200278	VASQUEZ	DUQUE	ANDRES	FELIPE	2000-08-02	M	2025-07-24	08:31			903841	4500
CC	1011200278	VASQUEZ	DUQUE	ANDRES	FELIPE	2000-08-02	M	2025-07-24	08:31			903818	4500
CC	1011200278	VASQUEZ	DUQUE	ANDRES	FELIPE	2000-08-02	M	2025-07-24	08:31			903868	6000
CC	1003097253	PADILLA	DELGADO	HERNAN	DARIO	2002-04-06	M	2025-07-24	08:37			903841	4500
CC	1003097253	PADILLA	DELGADO	HERNAN	DARIO	2002-04-06	M	2025-07-24	08:37			903818	4500
CC	1003097253	PADILLA	DELGADO	HERNAN	DARIO	2002-04-06	M	2025-07-24	08:37			903868	6000
CC	1001117887	TORRES	RODRIGUEZ	JOB	DANIEL	2002-09-19	M	2025-07-24	08:43			903841	4500
CC	1001117887	TORRES	RODRIGUEZ	JOB	DANIEL	2002-09-19	M	2025-07-24	08:43			903818	4500
CC	1001117887	TORRES	RODRIGUEZ	JOB	DANIEL	2002-09-19	M	2025-07-24	08:43			903868	6000
CC	1072073346	URREGO	BARRETO	NATALY	ANDREA	1993-12-18	F	2025-07-24	08:49			903841	4500
CC	1072073346	URREGO	BARRETO	NATALY	ANDREA	1993-12-18	F	2025-07-24	08:49			903818	4500
CC	1072073346	URREGO	BARRETO	NATALY	ANDREA	1993-12-18	F	2025-07-24	08:49			903868	6000
CC	1124030235	ARMESTO	SUAREZ	ARTURO		1991-06-10	M	2025-07-24	08:55			903841	4500
CC	1124030235	ARMESTO	SUAREZ	ARTURO		1991-06-10	M	2025-07-24	08:55			903818	4500
CC	1124030235	ARMESTO	SUAREZ	ARTURO		1991-06-10	M	2025-07-24	08:55			903868	6000
AS	COL4865164	BAEZ	PIRONA	CARLOS	ANTONIO	1987-08-13	M	2025-07-24	09:01			903841	4500
AS	COL4865164	BAEZ	PIRONA	CARLOS	ANTONIO	1987-08-13	M	2025-07-24	09:01			903818	4500
AS	COL4865164	BAEZ	PIRONA	CARLOS	ANTONIO	1987-08-13	M	2025-07-24	09:01			903868	6000
CC	74346378	CABALLERO	VEGA	JOSE	ESTEBAN	1972-04-30	M	2025-07-24	09:07			903841	4500
CC	74346378	CABALLERO	VEGA	JOSE	ESTEBAN	1972-04-30	M	2025-07-24	09:07			903818	4500
CC	74346378	CABALLERO	VEGA	JOSE	ESTEBAN	1972-04-30	M	2025-07-24	09:07			903868	6000
CC	93478741	SANCHEZ	SANCHEZ	MANUEL	ISIDRO	1984-07-28	M	2025-07-24	09:13			902207	7000
CC	93478741	SANCHEZ	SANCHEZ	MANUEL	ISIDRO	1984-07-28	M	2025-07-24	09:13			903841	7000
CC	93478741	SANCHEZ	SANCHEZ	MANUEL	ISIDRO	1984-07-28	M	2025-07-24	09:13			903818	7000
CC	93478741	SANCHEZ	SANCHEZ	MANUEL	ISIDRO	1984-07-28	M	2025-07-24	09:13			903868	9000
CC	93478741	SANCHEZ	SANCHEZ	MANUEL	ISIDRO	1984-07-28	M	2025-07-24	09:13			907106	7000
CC	19439031	URIBE	CARDONA	LUIS	FERNANDO	1961-06-09	M	2025-07-24	09:23			903895	7000
CC	79748374	ANGULO	CAMACHO	JOSELITO		1977-11-05	M	2025-07-25	07:15			902207	7000
CC	79748374	ANGULO	CAMACHO	JOSELITO		1977-11-05	M	2025-07-25	07:15			903841	7000
CC	79748374	ANGULO	CAMACHO	JOSELITO		1977-11-05	M	2025-07-25	07:15			903818	7000
CC	79748374	ANGULO	CAMACHO	JOSELITO		1977-11-05	M	2025-07-25	07:15			903815	9000
CC	79748374	ANGULO	CAMACHO	JOSELITO		1977-11-05	M	2025-07-25	07:15			903868	9000
CC	79748374	ANGULO	CAMACHO	JOSELITO		1977-11-05	M	2025-07-25	07:15			903895	7000
CC	79748374	ANGULO	CAMACHO	JOSELITO		1977-11-05	M	2025-07-25	07:15			907106	7000
CC	79738675	PARRA	RUBIANO	ANTONIO		1975-09-02	M	2025-07-25	07:29			903841	4500
CC	79738675	PARRA	RUBIANO	ANTONIO		1975-09-02	M	2025-07-25	07:29			903818	4500
CC	79738675	PARRA	RUBIANO	ANTONIO		1975-09-02	M	2025-07-25	07:29			903868	6000
CC	1023021948	RAMIREZ	PRIETO	BRANDON	STEVEN	1997-06-12	M	2025-07-25	07:35			903841	4500
CC	1023021948	RAMIREZ	PRIETO	BRANDON	STEVEN	1997-06-12	M	2025-07-25	07:35			903818	4500
CC	1023021948	RAMIREZ	PRIETO	BRANDON	STEVEN	1997-06-12	M	2025-07-25	07:35			903868	6000
CC	80203367	CRUZ		FERNEY	ALEXANDER	1982-12-10	M	2025-07-25	07:41			903841	4500
CC	80203367	CRUZ		FERNEY	ALEXANDER	1982-12-10	M	2025-07-25	07:41			903818	4500
CC	80203367	CRUZ		FERNEY	ALEXANDER	1982-12-10	M	2025-07-25	07:41			903868	6000
CC	1000464984	MARTIN	ULTENGO	ANDRES	CAMILO	2001-02-16	M	2025-07-25	07:47			903841	4500
CC	1000464984	MARTIN	ULTENGO	ANDRES	CAMILO	2001-02-16	M	2025-07-25	07:47			903818	4500
CC	1000464984	MARTIN	ULTENGO	ANDRES	CAMILO	2001-02-16	M	2025-07-25	07:47			903868	6000
CC	1193481176	CORREA	TAPIAS	DEIVIS	ANDRES	2001-10-15	M	2025-07-25	07:53			903841	4500
CC	1193481176	CORREA	TAPIAS	DEIVIS	ANDRES	2001-10-15	M	2025-07-25	07:53			903818	4500
CC	1193481176	CORREA	TAPIAS	DEIVIS	ANDRES	2001-10-15	M	2025-07-25	07:53			903868	6000
CC	80833187	MEDINA	RINCON	JUAN	PABLO	1985-12-13	M	2025-07-25	07:59			903841	4500
CC	80833187	MEDINA	RINCON	JUAN	PABLO	1985-12-13	M	2025-07-25	07:59			903818	4500
CC	80833187	MEDINA	RINCON	JUAN	PABLO	1985-12-13	M	2025-07-25	07:59			903868	6000
CC	4145497	MARTIN	ROA	ROBERTO		1970-12-13	M	2025-07-25	08:05			903841	4500
CC	4145497	MARTIN	ROA	ROBERTO		1970-12-13	M	2025-07-25	08:05			903818	4500
CC	4145497	MARTIN	ROA	ROBERTO		1970-12-13	M	2025-07-25	08:05			903868	6000
CC	41403054	ORTIZ	VERA	ROSALBA		1945-11-24	F	2025-07-25	08:11			903815	5000
CC	1000940210	LOVERA	FONSECA	MAUREEN	DANIELA	2001-05-15	F	2025-07-25	08:18			904508	6000
CC	1024575838	GIL	GIRALDO	CLAUDIA	MARCELA	1996-11-04	F	2025-07-25	08:25			904508	6000
AS	COL6013558	ASENCIO	RIVAS	PATRICIA	CAROLINA	1990-08-25	F	2025-07-25	08:32			902207	7000
AS	COL6013558	ASENCIO	RIVAS	PATRICIA	CAROLINA	1990-08-25	F	2025-07-25	08:32			903841	7000
AS	COL6013558	ASENCIO	RIVAS	PATRICIA	CAROLINA	1990-08-25	F	2025-07-25	08:32			903818	7000
AS	COL6013558	ASENCIO	RIVAS	PATRICIA	CAROLINA	1990-08-25	F	2025-07-25	08:32			903868	9000
AS	COL6013558	ASENCIO	RIVAS	PATRICIA	CAROLINA	1990-08-25	F	2025-07-25	08:32			903895	7000
AS	COL6013558	ASENCIO	RIVAS	PATRICIA	CAROLINA	1990-08-25	F	2025-07-25	08:32			907106	7000
CC	80139557	JIMENEZ	SANCHEZ	OLIVERIO		1980-04-29	M	2025-07-25	08:44			902207	7000
CC	80139557	JIMENEZ	SANCHEZ	OLIVERIO		1980-04-29	M	2025-07-25	08:44			903841	7000
CC	80139557	JIMENEZ	SANCHEZ	OLIVERIO		1980-04-29	M	2025-07-25	08:44			903818	7000
CC	80139557	JIMENEZ	SANCHEZ	OLIVERIO		1980-04-29	M	2025-07-25	08:44			903868	9000
CC	80139557	JIMENEZ	SANCHEZ	OLIVERIO		1980-04-29	M	2025-07-25	08:44			907106	7000
CC	1007695169	PLAZA	HERNANDEZ	LAURA	ALEJANDRA	2000-06-14	F	2025-07-25	08:54			902207	6000
CC	1007695169	PLAZA	HERNANDEZ	LAURA	ALEJANDRA	2000-06-14	F	2025-07-25	08:54			903841	6000
CC	1007695169	PLAZA	HERNANDEZ	LAURA	ALEJANDRA	2000-06-14	F	2025-07-25	08:54			903818	6000
CC	1007695169	PLAZA	HERNANDEZ	LAURA	ALEJANDRA	2000-06-14	F	2025-07-25	08:54			903815	6000
CC	1007695169	PLAZA	HERNANDEZ	LAURA	ALEJANDRA	2000-06-14	F	2025-07-25	08:54			903868	6000
CC	52760268	PACHECO	TORO	DIANA	MARCELA	1983-09-12	F	2025-07-25	09:04			904508	9000
CC	19179234	BALLESTEROS	SANCHEZ	HERNANDO		1951-08-30	M	2025-07-26	07:15			903895	7000
CC	1012441553	TRUJILLO	APONTE	STEFANY	ALEJANDRA	1997-02-16	F	2025-07-26	07:20			904508	9000
CC	1101383281	VILLARREAL	VILLADIEGO	WILBER		1985-02-03	M	2025-07-26	07:30			903841	4500
CC	1101383281	VILLARREAL	VILLADIEGO	WILBER		1985-02-03	M	2025-07-26	07:30			903818	4500
CC	1101383281	VILLARREAL	VILLADIEGO	WILBER		1985-02-03	M	2025-07-26	07:30			903868	6000
CC	1082250052	BUELVAS	GAMEZ	JOSE	JAVIER	1993-07-17	M	2025-07-26	07:36			903841	4500
CC	1082250052	BUELVAS	GAMEZ	JOSE	JAVIER	1993-07-17	M	2025-07-26	07:36			903818	4500
CC	1082250052	BUELVAS	GAMEZ	JOSE	JAVIER	1993-07-17	M	2025-07-26	07:36			903868	6000
CC	1057608984	BARRERA	MEDINA	DAVID	SANTIAGO	1999-05-28	M	2025-07-26	07:42			903841	4500
CC	1057608984	BARRERA	MEDINA	DAVID	SANTIAGO	1999-05-28	M	2025-07-26	07:42			903818	4500
CC	1057608984	BARRERA	MEDINA	DAVID	SANTIAGO	1999-05-28	M	2025-07-26	07:42			903868	6000
CC	1022333396	CUERVO	HERNANDEZ	HERVIN	ANTONIO	1986-11-05	M	2025-07-26	07:48			903841	4500
CC	1022333396	CUERVO	HERNANDEZ	HERVIN	ANTONIO	1986-11-05	M	2025-07-26	07:48			903818	4500
CC	1022333396	CUERVO	HERNANDEZ	HERVIN	ANTONIO	1986-11-05	M	2025-07-26	07:48			903868	6000
CC	80902613	PULIDO	CORONADO	JIMMY	ALEXANDER	1985-07-16	M	2025-07-26	07:54			903841	4500
CC	80902613	PULIDO	CORONADO	JIMMY	ALEXANDER	1985-07-16	M	2025-07-26	07:54			903818	4500
CC	80902613	PULIDO	CORONADO	JIMMY	ALEXANDER	1985-07-16	M	2025-07-26	07:54			903868	6000
CC	79544840	SANTAFE	CHAPARRO	WILMER		1970-09-05	M	2025-07-26	08:00			903841	4500
CC	79544840	SANTAFE	CHAPARRO	WILMER		1970-09-05	M	2025-07-26	08:00			903818	4500
CC	79544840	SANTAFE	CHAPARRO	WILMER		1970-09-05	M	2025-07-26	08:00			903868	6000
CC	1104010177	PEREZ	GALVAN	NAICIR	ANTONIO	1987-10-26	M	2025-07-26	08:06			903841	4500
CC	1104010177	PEREZ	GALVAN	NAICIR	ANTONIO	1987-10-26	M	2025-07-26	08:06			903818	4500
CC	1104010177	PEREZ	GALVAN	NAICIR	ANTONIO	1987-10-26	M	2025-07-26	08:06			903868	6000
CC	1034660898	TORRES	OVIEDO	NICOLAS	ANDRES	2007-02-23	M	2025-07-26	08:12			903841	4500
CC	1034660898	TORRES	OVIEDO	NICOLAS	ANDRES	2007-02-23	M	2025-07-26	08:12			903818	4500
CC	1034660898	TORRES	OVIEDO	NICOLAS	ANDRES	2007-02-23	M	2025-07-26	08:12			903868	6000
CC	1078116154	PALACIOS	ANDRADE	CRISTIAN		1998-10-20	M	2025-07-26	08:18			903841	4500
CC	1078116154	PALACIOS	ANDRADE	CRISTIAN		1998-10-20	M	2025-07-26	08:18			903818	4500
CC	1078116154	PALACIOS	ANDRADE	CRISTIAN		1998-10-20	M	2025-07-26	08:18			903868	6000
CC	80185878	BELTRAN	HERNANDEZ	ANDRES 	FELIPE	1982-05-24	M	2025-07-26	08:24			903841	4500
CC	80185878	BELTRAN	HERNANDEZ	ANDRES 	FELIPE	1982-05-24	M	2025-07-26	08:24			903818	4500
CC	80185878	BELTRAN	HERNANDEZ	ANDRES 	FELIPE	1982-05-24	M	2025-07-26	08:24			903868	6000
CC	1000688140	QUINTERO	BOBADILLA	LAURA	NATALIA	2003-02-05	F	2025-07-28	07:15			902207	7000
CC	1000688140	QUINTERO	BOBADILLA	LAURA	NATALIA	2003-02-05	F	2025-07-28	07:15			903841	7000
CC	1000688140	QUINTERO	BOBADILLA	LAURA	NATALIA	2003-02-05	F	2025-07-28	07:15			907106	7000
CC	1032456005	REY	TIQUE	ANGIE	LORENA	1993-04-02	F	2025-07-28	07:21			902207	7000
CC	1032456005	REY	TIQUE	ANGIE	LORENA	1993-04-02	F	2025-07-28	07:21			903841	7000
CC	1032456005	REY	TIQUE	ANGIE	LORENA	1993-04-02	F	2025-07-28	07:21			903818	7000
CC	1032456005	REY	TIQUE	ANGIE	LORENA	1993-04-02	F	2025-07-28	07:21			903868	9000
CC	1032456005	REY	TIQUE	ANGIE	LORENA	1993-04-02	F	2025-07-28	07:21			907106	7000
CC	1032456005	REY	TIQUE	ANGIE	LORENA	1993-04-02	F	2025-07-28	07:21			907002	7000
CC	1012441045	ANAYA	DELGADO	ALEJANDRO		1997-02-18	M	2025-07-28	07:33			902207	7000
CC	1012441045	ANAYA	DELGADO	ALEJANDRO		1997-02-18	M	2025-07-28	07:33			903841	7000
CC	1012441045	ANAYA	DELGADO	ALEJANDRO		1997-02-18	M	2025-07-28	07:33			903818	7000
CC	1012441045	ANAYA	DELGADO	ALEJANDRO		1997-02-18	M	2025-07-28	07:33			903868	9000
CC	1012441045	ANAYA	DELGADO	ALEJANDRO		1997-02-18	M	2025-07-28	07:33			907106	7000
CC	1012441045	ANAYA	DELGADO	ALEJANDRO		1997-02-18	M	2025-07-28	07:33			907002	7000
CC	1000774461	DE DIOS	CLAVIJO	ANDRES	FELIPE	2001-03-15	M	2025-07-28	07:45			903841	4500
CC	1000774461	DE DIOS	CLAVIJO	ANDRES	FELIPE	2001-03-15	M	2025-07-28	07:45			903818	4500
CC	1000774461	DE DIOS	CLAVIJO	ANDRES	FELIPE	2001-03-15	M	2025-07-28	07:45			903868	6000
CC	1000005507	PEÑA	MARTIN	YURI	NATALI	2000-08-15	F	2025-07-28	07:51			903841	4500
CC	1000005507	PEÑA	MARTIN	YURI	NATALI	2000-08-15	F	2025-07-28	07:51			903818	4500
CC	1000005507	PEÑA	MARTIN	YURI	NATALI	2000-08-15	F	2025-07-28	07:51			903868	6000
CC	1001269494	DE DIOS	CLAVIJO	MIGUEL	ANGEL	1999-07-19	M	2025-07-28	07:57			903841	4500
CC	1001269494	DE DIOS	CLAVIJO	MIGUEL	ANGEL	1999-07-19	M	2025-07-28	07:57			903818	4500
CC	1001269494	DE DIOS	CLAVIJO	MIGUEL	ANGEL	1999-07-19	M	2025-07-28	07:57			903868	6000
CC	79433696	PEÑA	GONZALEZ	CARLOS	MARTIN	1966-11-02	M	2025-07-28	08:03			902207	7000
CC	79902659	PIÑEROS	CARRANZA	JUAN	MANUEL	1978-05-27	M	2025-07-28	08:10			902207	7000
CC	1030598275	BASTIDAS	PIÑEROS	JUAN	SEBASTIAN	1991-09-11	M	2025-07-28	08:17			902207	7000
CC	1007438716	SUAREZ	ALAPE	LUIS	ANGEL	1996-01-08	M	2025-07-28	08:25			903841	4500
CC	1007438716	SUAREZ	ALAPE	LUIS	ANGEL	1996-01-08	M	2025-07-28	08:25			903818	4500
CC	1007438716	SUAREZ	ALAPE	LUIS	ANGEL	1996-01-08	M	2025-07-28	08:25			903868	6000
CC	79615029	GALINDO	VARGAS	WILSON		1971-08-02	M	2025-07-28	08:31			902207	7000
CC	79615029	GALINDO	VARGAS	WILSON		1971-08-02	M	2025-07-28	08:31			903841	7000
CC	79615029	GALINDO	VARGAS	WILSON		1971-08-02	M	2025-07-28	08:31			903818	7000
CC	79615029	GALINDO	VARGAS	WILSON		1971-08-02	M	2025-07-28	08:31			903815	7000
CC	79615029	GALINDO	VARGAS	WILSON		1971-08-02	M	2025-07-28	08:31			903868	7000
CC	79615029	GALINDO	VARGAS	WILSON		1971-08-02	M	2025-07-28	08:31			907106	7000
CC	1012396176	REYES	PARRA	AURA	MARIA	1992-11-19	F	2025-07-29	07:15			903895	7000
CC	20409245	GALINDO	DE CAICEDO	LUZ	DARY	1957-04-24	F	2025-07-29	07:20			903841	7000
CC	77103501	PARRA	REYES	JOSE	EDUARDO	1973-12-01	M	2025-07-29	07:25			903841	4500
CC	77103501	PARRA	REYES	JOSE	EDUARDO	1973-12-01	M	2025-07-29	07:25			903818	4500
CC	77103501	PARRA	REYES	JOSE	EDUARDO	1973-12-01	M	2025-07-29	07:25			903868	6000
CC	80720678	GONZALEZ	DUQUE	OSWALDO		1976-02-20	M	2025-07-29	07:31			903841	4500
CC	80720678	GONZALEZ	DUQUE	OSWALDO		1976-02-20	M	2025-07-29	07:31			903818	4500
CC	80720678	GONZALEZ	DUQUE	OSWALDO		1976-02-20	M	2025-07-29	07:31			903868	6000
CC	1045506350	CUESTA	MORENO	WASLIN		1990-04-09	M	2025-07-29	07:37			903841	4500
CC	1045506350	CUESTA	MORENO	WASLIN		1990-04-09	M	2025-07-29	07:37			903818	4500
CC	1045506350	CUESTA	MORENO	WASLIN		1990-04-09	M	2025-07-29	07:37			903868	6000
CC	1106900154	PULIDO	ARIAS	CRISTIAN	FABIAN	1998-04-06	M	2025-07-29	07:43			903841	4500
CC	1106900154	PULIDO	ARIAS	CRISTIAN	FABIAN	1998-04-06	M	2025-07-29	07:43			903818	4500
CC	1106900154	PULIDO	ARIAS	CRISTIAN	FABIAN	1998-04-06	M	2025-07-29	07:43			903868	6000
CC	1000788113	VANEGAS	CANTILLO	JOHAN	STEVEN	2001-08-27	M	2025-07-29	07:49			903841	4500
CC	1000788113	VANEGAS	CANTILLO	JOHAN	STEVEN	2001-08-27	M	2025-07-29	07:49			903818	4500
CC	1000788113	VANEGAS	CANTILLO	JOHAN	STEVEN	2001-08-27	M	2025-07-29	07:49			903868	6000
CC	1019149100	DAVILA	ROMERO	MARIA	ANGELA	1999-06-01	F	2025-07-29	07:55			903841	4500
CC	1019149100	DAVILA	ROMERO	MARIA	ANGELA	1999-06-01	F	2025-07-29	07:55			903818	4500
CC	1019149100	DAVILA	ROMERO	MARIA	ANGELA	1999-06-01	F	2025-07-29	07:55			903868	6000
CC	1065617073	BELEÑO	IMBRECHT	LUIS	FERNEY	1987-06-09	M	2025-07-29	08:01			903841	4500
CC	1065617073	BELEÑO	IMBRECHT	LUIS	FERNEY	1987-06-09	M	2025-07-29	08:01			903818	4500
CC	1065617073	BELEÑO	IMBRECHT	LUIS	FERNEY	1987-06-09	M	2025-07-29	08:01			903868	6000
CC	7572664	SUAREZ	BELEÑO	UBALDO	MANUEL	1983-02-07	M	2025-07-29	08:07			903841	4500
CC	7572664	SUAREZ	BELEÑO	UBALDO	MANUEL	1983-02-07	M	2025-07-29	08:07			903818	4500
CC	7572664	SUAREZ	BELEÑO	UBALDO	MANUEL	1983-02-07	M	2025-07-29	08:07			903868	6000
CC	1003231091	MENESES	TAPIAS	DUVIER	YESITH	1999-03-25	M	2025-07-29	08:13			903841	4500
CC	1003231091	MENESES	TAPIAS	DUVIER	YESITH	1999-03-25	M	2025-07-29	08:13			903818	4500
CC	1003231091	MENESES	TAPIAS	DUVIER	YESITH	1999-03-25	M	2025-07-29	08:13			903868	6000
CC	1073714549	HERNANDEZ	LAGUNAS	ANA	MARIA	1998-04-06	F	2025-07-29	08:19			904508	9000
CC	1028820231	SABOGAL	CRUZ	KAROL	MICHELL	2007-03-11	F	2025-07-29	08:25			904508	9000
CC	1012367593	DUARTE	RAMIREZ	PAHULA	MICHELLT	2007-04-04	F	2025-07-30	07:15			902207	6000
CC	1012367593	DUARTE	RAMIREZ	PAHULA	MICHELLT	2007-04-04	F	2025-07-30	07:15			903841	6000
CC	1012367593	DUARTE	RAMIREZ	PAHULA	MICHELLT	2007-04-04	F	2025-07-30	07:15			906002	6000
CC	93080974	ROMERO	PORTILLO	ISRAEL		1957-08-13	M	2025-07-30	07:21			907002	6000
CC	1108151845	GUTIERRES	ZOTO	NICOLAS		1996-02-09	M	2025-07-30	07:30			903841	4500
CC	1108151845	GUTIERRES	ZOTO	NICOLAS		1996-02-09	M	2025-07-30	07:30			903818	4500
CC	1108151845	GUTIERRES	ZOTO	NICOLAS		1996-02-09	M	2025-07-30	07:30			903868	6000
CC	15324392	BARRIOS	CHARRY	JORGE	EDUARDO	1967-12-01	M	2025-07-30	07:36			903841	4500
CC	15324392	BARRIOS	CHARRY	JORGE	EDUARDO	1967-12-01	M	2025-07-30	07:36			903818	4500
CC	15324392	BARRIOS	CHARRY	JORGE	EDUARDO	1967-12-01	M	2025-07-30	07:36			903868	6000
CC	39746314	BAUTISTA	MATEUS	MARTHA	LEONOR	1966-11-04	F	2025-07-30	07:42			903841	4500
CC	39746314	BAUTISTA	MATEUS	MARTHA	LEONOR	1966-11-04	F	2025-07-30	07:42			903818	4500
CC	39746314	BAUTISTA	MATEUS	MARTHA	LEONOR	1966-11-04	F	2025-07-30	07:42			903868	6000
CC	79989374	RODRIGUEZ	BAQUERO	JEFERSSON		1982-10-02	M	2025-07-30	07:48			903841	4500
CC	79989374	RODRIGUEZ	BAQUERO	JEFERSSON		1982-10-02	M	2025-07-30	07:48			903818	4500
CC	79989374	RODRIGUEZ	BAQUERO	JEFERSSON		1982-10-02	M	2025-07-30	07:48			903868	6000
CC	80161888	BASTO	PATARROYO	EDWARD	ANDRES	1982-12-24	M	2025-07-30	07:54			903841	4500
CC	80161888	BASTO	PATARROYO	EDWARD	ANDRES	1982-12-24	M	2025-07-30	07:54			903818	4500
CC	80161888	BASTO	PATARROYO	EDWARD	ANDRES	1982-12-24	M	2025-07-30	07:54			903868	6000
AS	COL5232696	PINEDA	MARIN	EDWARD	JOSE	1995-12-20	M	2025-07-30	08:00			903841	4500
AS	COL5232696	PINEDA	MARIN	EDWARD	JOSE	1995-12-20	M	2025-07-30	08:00			903818	4500
AS	COL5232696	PINEDA	MARIN	EDWARD	JOSE	1995-12-20	M	2025-07-30	08:00			903868	6000
CC	17221413	ULLOA	FONTECHA	ARNULFO		1978-07-11	M	2025-07-30	08:06			903841	4500
CC	17221413	ULLOA	FONTECHA	ARNULFO		1978-07-11	M	2025-07-30	08:06			903818	4500
CC	17221413	ULLOA	FONTECHA	ARNULFO		1978-07-11	M	2025-07-30	08:06			903868	6000
CC	1049924259	TRESPALACIOS	SANDOVAL	JHON	JAIRO	2001-04-25	M	2025-07-30	08:12			903841	4500
CC	1049924259	TRESPALACIOS	SANDOVAL	JHON	JAIRO	2001-04-25	M	2025-07-30	08:12			903818	4500
CC	1049924259	TRESPALACIOS	SANDOVAL	JHON	JAIRO	2001-04-25	M	2025-07-30	08:12			903868	6000
AS	COL5434383	FAJARDO	GONZALEZ	WILLIAM		2006-02-19	M	2025-07-30	08:18			903841	4500
AS	COL5434383	FAJARDO	GONZALEZ	WILLIAM		2006-02-19	M	2025-07-30	08:18			903818	4500
AS	COL5434383	FAJARDO	GONZALEZ	WILLIAM		2006-02-19	M	2025-07-30	08:18			903868	6000
CC	1007591297	ARRIETA	RAMIREZ	LUIS	JAVIER	1982-07-07	M	2025-07-30	08:24			903841	4500
CC	1007591297	ARRIETA	RAMIREZ	LUIS	JAVIER	1982-07-07	M	2025-07-30	08:24			903818	4500
CC	1007591297	ARRIETA	RAMIREZ	LUIS	JAVIER	1982-07-07	M	2025-07-30	08:24			903868	6000
CC	1033747225	ORJUELA	ROJAS	JOSE	EDWARD	1992-08-18	M	2025-07-30	08:30			903841	4500
CC	1033747225	ORJUELA	ROJAS	JOSE	EDWARD	1992-08-18	M	2025-07-30	08:30			903818	4500
CC	1033747225	ORJUELA	ROJAS	JOSE	EDWARD	1992-08-18	M	2025-07-30	08:30			903868	6000
CC	1123804209	VALENCIA	VEGA	JOHAN	DUVAN	2006-12-26	M	2025-07-30	08:36			903841	4500
CC	1123804209	VALENCIA	VEGA	JOHAN	DUVAN	2006-12-26	M	2025-07-30	08:36			903818	4500
CC	1123804209	VALENCIA	VEGA	JOHAN	DUVAN	2006-12-26	M	2025-07-30	08:36			903868	6000
CC	79675559	BOSSIO	QUINTANA	MIGUEL	IGNACIO	1970-12-03	M	2025-07-30	08:42			903841	4500
CC	79675559	BOSSIO	QUINTANA	MIGUEL	IGNACIO	1970-12-03	M	2025-07-30	08:42			903818	4500
CC	79675559	BOSSIO	QUINTANA	MIGUEL	IGNACIO	1970-12-03	M	2025-07-30	08:42			903868	6000
CC	1063949125	BORRERO	VIDES	SANTIAGO		1987-07-06	M	2025-07-30	08:48			903841	4500
CC	1063949125	BORRERO	VIDES	SANTIAGO		1987-07-06	M	2025-07-30	08:48			903818	4500
CC	1063949125	BORRERO	VIDES	SANTIAGO		1987-07-06	M	2025-07-30	08:48			903868	6000
CC	1023366625	ARENAS	MARTINEZ	JEISON	STEVEN	2004-10-25	M	2025-07-30	08:54			903841	4500
CC	1023366625	ARENAS	MARTINEZ	JEISON	STEVEN	2004-10-25	M	2025-07-30	08:54			903818	4500
CC	1023366625	ARENAS	MARTINEZ	JEISON	STEVEN	2004-10-25	M	2025-07-30	08:54			903868	6000
CC	79924657	NAVARRETE	ROMERO	RONALD	RAUL	1981-07-09	M	2025-07-30	09:00			903841	4500
CC	79924657	NAVARRETE	ROMERO	RONALD	RAUL	1981-07-09	M	2025-07-30	09:00			903818	4500
CC	79924657	NAVARRETE	ROMERO	RONALD	RAUL	1981-07-09	M	2025-07-30	09:00			903868	6000
CC	1023017899	ORTIZ	POVEDA	ANGIE	PAOLA	1999-06-08	F	2025-07-30	09:06			903841	4500
CC	1023017899	ORTIZ	POVEDA	ANGIE	PAOLA	1999-06-08	F	2025-07-30	09:06			903818	4500
CC	1023017899	ORTIZ	POVEDA	ANGIE	PAOLA	1999-06-08	F	2025-07-30	09:06			903868	6000
AS	COL6720022	LINAREZ	SANCHEZ	RUDY	JESUS	2004-01-16	M	2025-07-30	09:12			903841	4500
AS	COL6720022	LINAREZ	SANCHEZ	RUDY	JESUS	2004-01-16	M	2025-07-30	09:12			903818	4500
AS	COL6720022	LINAREZ	SANCHEZ	RUDY	JESUS	2004-01-16	M	2025-07-30	09:12			903868	6000
CC	76044224	CAICEDO	BALANTA	JHON	CARLOS	1976-05-09	M	2025-07-30	09:18			903841	4500
CC	76044224	CAICEDO	BALANTA	JHON	CARLOS	1976-05-09	M	2025-07-30	09:18			903818	4500
CC	76044224	CAICEDO	BALANTA	JHON	CARLOS	1976-05-09	M	2025-07-30	09:18			903868	6000
CC	1004280198	MIER	MOLINA	JOSE	ANTONIO	2000-09-24	M	2025-07-31	07:15			903841	4500
CC	1004280198	MIER	MOLINA	JOSE	ANTONIO	2000-09-24	M	2025-07-31	07:15			903818	4500
CC	1004280198	MIER	MOLINA	JOSE	ANTONIO	2000-09-24	M	2025-07-31	07:15			903868	6000
CC	98427310	QUIÑONES	MONTAÑO	JAMES	BERNABE	1975-06-26	M	2025-07-31	07:21			903841	4500
CC	98427310	QUIÑONES	MONTAÑO	JAMES	BERNABE	1975-06-26	M	2025-07-31	07:21			903818	4500
CC	98427310	QUIÑONES	MONTAÑO	JAMES	BERNABE	1975-06-26	M	2025-07-31	07:21			903868	6000
CC	86004337	APONTE	ZABALA	ARCADIO		1968-09-24	M	2025-07-31	07:27			903841	4500
CC	86004337	APONTE	ZABALA	ARCADIO		1968-09-24	M	2025-07-31	07:27			903818	4500
CC	86004337	APONTE	ZABALA	ARCADIO		1968-09-24	M	2025-07-31	07:27			903868	6000
CC	1005424941	NUÑEZ	RAMIREZ	OSNAIDER	JAVIER	2001-05-05	M	2025-07-31	07:33			903841	4500
CC	1005424941	NUÑEZ	RAMIREZ	OSNAIDER	JAVIER	2001-05-05	M	2025-07-31	07:33			903818	4500
CC	1005424941	NUÑEZ	RAMIREZ	OSNAIDER	JAVIER	2001-05-05	M	2025-07-31	07:33			903868	6000
CC	1079940448	LOPEZ	LIDUEÑAS	LEONARDO	LUIS	1997-11-25	M	2025-07-31	07:39			903841	4500
CC	1079940448	LOPEZ	LIDUEÑAS	LEONARDO	LUIS	1997-11-25	M	2025-07-31	07:39			903818	4500
CC	1079940448	LOPEZ	LIDUEÑAS	LEONARDO	LUIS	1997-11-25	M	2025-07-31	07:39			903868	6000
CC	79899171	CARDENAS		JUAN	MANUEL	1979-03-05	M	2025-07-31	07:45			903841	4500
CC	79899171	CARDENAS		JUAN	MANUEL	1979-03-05	M	2025-07-31	07:45			903818	4500
CC	79899171	CARDENAS		JUAN	MANUEL	1979-03-05	M	2025-07-31	07:45			903868	6000
CC	8049273	SALCEDO	SALCEDO	LUIS	ALFONSO	1968-02-05	M	2025-07-31	07:51			903841	4500
CC	8049273	SALCEDO	SALCEDO	LUIS	ALFONSO	1968-02-05	M	2025-07-31	07:51			903818	4500
CC	8049273	SALCEDO	SALCEDO	LUIS	ALFONSO	1968-02-05	M	2025-07-31	07:51			903868	6000
CC	1057517289	ACUÑA	PINZON	JUAN	CAMILO	1999-12-07	M	2025-07-31	07:57			903841	4500
CC	1057517289	ACUÑA	PINZON	JUAN	CAMILO	1999-12-07	M	2025-07-31	07:57			903818	4500
CC	1057517289	ACUÑA	PINZON	JUAN	CAMILO	1999-12-07	M	2025-07-31	07:57			903868	6000
CC	1193569051	JIMENEZ	LOPEZ	DUVAN		1997-09-13	M	2025-07-31	08:03			903841	4500
CC	1193569051	JIMENEZ	LOPEZ	DUVAN		1997-09-13	M	2025-07-31	08:03			903818	4500
CC	1193569051	JIMENEZ	LOPEZ	DUVAN		1997-09-13	M	2025-07-31	08:03			903868	6000
CC	1121306562	PEREA	IPUANA	LUIS	MIGUEL	2001-10-29	M	2025-07-31	08:09			903841	4500
CC	1121306562	PEREA	IPUANA	LUIS	MIGUEL	2001-10-29	M	2025-07-31	08:09			903818	4500
CC	1121306562	PEREA	IPUANA	LUIS	MIGUEL	2001-10-29	M	2025-07-31	08:09			903868	6000
CC	1001890668	VARGAS	RENTERIA	NESTOR	MIGUEL	1998-06-21	M	2025-07-31	08:15			903841	4500
CC	1001890668	VARGAS	RENTERIA	NESTOR	MIGUEL	1998-06-21	M	2025-07-31	08:15			903818	4500
CC	1001890668	VARGAS	RENTERIA	NESTOR	MIGUEL	1998-06-21	M	2025-07-31	08:15			903868	6000
CC	1121927377	GASCA	SUAREZ	GUSTAVO	ADOLFO	1994-11-18	M	2025-07-31	08:21			903841	4500
CC	1121927377	GASCA	SUAREZ	GUSTAVO	ADOLFO	1994-11-18	M	2025-07-31	08:21			903818	4500
CC	1121927377	GASCA	SUAREZ	GUSTAVO	ADOLFO	1994-11-18	M	2025-07-31	08:21			903868	6000
CC	52188927	MORA	PINILLA	HEIDY	YOLANDA	1975-08-29	F	2025-07-31	11:45			903841	7000";
$listaProcedimientos = array_map(function($line) {
    $data = explode("\t", $line);
    return array(
        "tipoDocumentoIdentificacion" => $data[0],
        "numDocumentoIdentificacion" => $data[1],
        "nombres" => $data[2],
        "apellido1" => $data[3],
        "apellido2" => $data[4],
        "fechaNacimiento" => $data[6],
        "sexo" => $data[7],
        "fechaProcedimiento" => $data[8],
        "horaProcedimiento" => $data[9],
        "factura" => $data[10],
        "CUP" => $data[12],
        "valorProcedimiento" => $data[13]
    );
}, explode("\n", $lista));



foreach ($listaProcedimientos as $procedimiento) {

    // Buscar si el usuario ya existe
    $key = $procedimiento['tipoDocumentoIdentificacion'] . '-' . $procedimiento['numDocumentoIdentificacion'];
    if (!isset($usuariosMap)) $usuariosMap = [];
    if (!isset($usuariosMap[$key])) {
        $usuariosMap[$key] = array(
            'tipoDocumentoIdentificacion' => $procedimiento['tipoDocumentoIdentificacion'],
            'numDocumentoIdentificacion' => $procedimiento['numDocumentoIdentificacion'],
            "tipoUsuario" => "04",
            "fechaNacimiento" => $procedimiento['fechaNacimiento'] ,
            "codSexo" => $procedimiento['sexo'],
            "codPaisResidencia" => "170",
            "codMunicipioResidencia" => "11001",
            "codZonaTerritorialResidencia" => "01",
            "incapacidad" => "NO",
            "codPaisOrigen" => "170",
            "consecutivo" => 1,
            "servicios" => array(
                "procedimientos" => array()
            ),
        );
    }
    $usuariosMap[$key]['servicios']['procedimientos'][] = array(
        "codPrestador" => "110010822701",
        "fechaInicioAtencion" => $procedimiento['fechaProcedimiento'] . " " . $procedimiento['horaProcedimiento'],
        "idMIPRES" => "",
        "numAutorizacion" => $procedimiento['factura'],
        "codProcedimiento" => $procedimiento['CUP'],
        "viaIngresoServicioSalud" => "03",
        "modalidadGrupoServicioTecSal" => "01",
        "grupoServicios" => "03",
        "codServicio" => 328,
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
    );
    $usuarios = array_values($usuariosMap);


}

return $usuarios;

$usuarios = array_map(function($procedimiento) {
    return array(
        "tipoDocumentoIdentificacion" => $procedimiento['tID'],
        "numDocumentoIdentificacion" => $procedimiento['numero_doc'],
        "tipoUsuario" => "04",
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
                    "codPrestador" => "110010822701",
                    "fechaInicioAtencion" => $procedimiento['fecha_procedimiento'] . " 00:00",
                    "idMIPRES" => "",
                    "numAutorizacion" => $procedimiento['factura'],
                    "codProcedimiento" => $procedimiento['CUP'],
                    "viaIngresoServicioSalud" => "03",
                    "modalidadGrupoServicioTecSal" => "01",
                    "grupoServicios" => "03",
                    "codServicio" => 328,
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
                )
            )
        )
    );
}, $procedimientos);

//descargar el archivo JSON
if (!empty($usuarios)) {
    $json = json_encode(array(
           "numDocumentoIdObligado"=> "51934571",
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
}
