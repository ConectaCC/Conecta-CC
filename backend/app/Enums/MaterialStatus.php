<?php

namespace App\Enums;

enum MaterialStatus: string
{
    case Pendente = 'PENDENTE';
    case Aprovado = 'APROVADO';
    case Rejeitado = 'REJEITADO';
    case Cancelado = 'CANCELADO';
}
