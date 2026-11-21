<?php

return [
    /*
    | Clave con la que se calcula el código de verificación que se imprime en el
    | reporte. Si no se define, se usa APP_KEY. Definirla permite rotar APP_KEY
    | sin invalidar los códigos de reportes ya emitidos.
    */
    'clave_verificacion' => env('REPORTE_VERIFICACION_KEY') ?: env('APP_KEY'),
];
