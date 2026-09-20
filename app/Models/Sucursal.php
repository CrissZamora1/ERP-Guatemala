<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';

    protected $fillable = ['nombre', 'codigo', 'direccion', 'telefono', 'activo'];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }

    public function gastos()
    {
        return $this->hasMany(Gasto::class);
    }
}
