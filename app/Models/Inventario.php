<?php

namespace App\Models;

use App\Models\Scopes\SucursalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'sucursal_id',
        'stock_actual',
        'stock_minimo',
        'stock_maximo',
    ];

    protected static function booted(): void
    {
        static::updated(function (Inventario $inventario) {
            if ($inventario->stock_actual <= $inventario->stock_minimo) {
                $inventario->generarOrdenAutomatica();
            }
        });
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

    public function generarOrdenAutomatica(): void
    {
        $producto = $this->producto;

        if (!$producto || !$producto->proveedor_id) {
            return;
        }

        $orden = \App\Models\OrdenCompra::withoutGlobalScopes()
            ->where('proveedor_id', $producto->proveedor_id)
            ->where('sucursal_id', $this->sucursal_id)
            ->where('estado', 'borrador')
            ->where('generada_automatica', true)
            ->first();

        if (!$orden) {
            $orden = \App\Models\OrdenCompra::create([
                'proveedor_id' => $producto->proveedor_id,
                'sucursal_id' => $this->sucursal_id,
                'estado' => 'borrador',
                'generada_automatica' => true,
                'total' => 0,
                'nota' => 'Generada automáticamente por stock mínimo',
            ]);
        }

        $yaExiste = $orden->detalles()->where('producto_id', $producto->id)->exists();

        if (!$yaExiste) {
            $cantidadSugerida = max(($this->stock_maximo ?? $this->stock_minimo * 2) - $this->stock_actual, 1);

            $orden->detalles()->create([
                'producto_id' => $producto->id,
                'cantidad' => $cantidadSugerida,
                'costo_unitario' => $producto->costo,
                'subtotal' => $cantidadSugerida * $producto->costo,
            ]);

            $orden->update(['total' => $orden->detalles()->sum('subtotal')]);
        }
    }
}
