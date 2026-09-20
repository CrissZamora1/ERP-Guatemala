<?php

namespace App\Models;

use App\Models\Scopes\SucursalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanillaPeriodo extends Model
{
    use HasFactory;

    protected $fillable = ['sucursal_id', 'fecha_inicio', 'fecha_fin', 'estado'];

    protected static function booted(): void
    {
        static::addGlobalScope(new SucursalScope);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function detalles()
    {
        return $this->hasMany(PlanillaDetalle::class);
    }
}
