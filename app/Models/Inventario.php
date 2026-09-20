<?php

namespace App\Models;

use App\Models\Scopes\SucursalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id', 'sucursal_id', 'stock_actual', 'stock_minimo', 'stock_maximo',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new SucursalScope);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function getBajoStockMinimoAttribute(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }
}
