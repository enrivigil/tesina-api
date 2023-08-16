<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direcciones_destinatarios';
    protected $fillable = [
        'nombre', 'telefono', 'direccion', 'municipio_id', 'destinatario_id',
    ];

    public function destinatario()
    {
        return $this->belongsTo(Destinatario::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}
