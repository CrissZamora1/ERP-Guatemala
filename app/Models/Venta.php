<?php

namespace App\Models;

use App\Models\Scopes\SucursalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id',
        'user_id',
        'cliente_nombre',
        'cliente_nit',
        'metodo_pago',
        'estado',
        'total',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new SucursalScope);

        static::creating(function (Venta $venta) {
            $venta->user_id = $venta->user_id ?? auth()->id();
            $venta->sucursal_id = $venta->sucursal_id ?? auth()->user()?->sucursal_id;
        });
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function recalcularTotal(): void
    {
        $this->total = $this->detalles()->sum('subtotal');
        $this->saveQuietly();
    }
}
