<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre', 'nit', 'direccion', 'telefono', 'email', 'contacto',
        'tiempo_entrega_dias', 'activo',
    ];

    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class);
    }
}
