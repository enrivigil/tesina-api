<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';
    protected $fillable = [
        'prefijo_codigo', 'num_codigo', 'sufijo_codigo', 'descripciones', 'observacion', 'costo', 'estado_id', 'sucursal_id', 'direccion_destinatario_id',
    ];
}
