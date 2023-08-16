<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destinatario extends Model
{
    use HasFactory;

    protected $tables = 'destinatarios';
    protected $fillables = [
        'nombre', 'apellido', 'dui', 'telefono', 'email', 'cliente_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function direcciones()
    {
        return $this->hasMany(Direccion::class);
    }
}
