<?php

namespace App\Models;

use App\Models\Scopes\SucursalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'ordenes_compra';

    protected $fillable = [
        'proveedor_id', 'sucursal_id', 'user_id', 'estado',
        'generada_automatica', 'total', 'nota',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new SucursalScope);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function detalles()
    {
        return $this->hasMany(OrdenCompraDetalle::class);
    }
}
