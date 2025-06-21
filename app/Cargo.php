<?php

namespace App;

enum Cargo : string
{
    case PRESTADOR = 'prestador de servicios';
    case ADMINISTRADOR = 'administrador';
    case AGENTE = 'agente operativo';
}
