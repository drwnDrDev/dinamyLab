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
