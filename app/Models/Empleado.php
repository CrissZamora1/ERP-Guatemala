<?php

namespace App\Models;

use App\Models\Scopes\SucursalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id', 'user_id', 'nombre', 'dpi', 'nit',
        'puesto', 'fecha_ingreso', 'sueldo_base', 'activo',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new SucursalScope);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function planillaDetalles()
    {
        return $this->hasMany(PlanillaDetalle::class);
    }
}
