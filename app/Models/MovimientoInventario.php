<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'producto_id', 'sucursal_origen_id', 'sucursal_destino_id',
        'tipo', 'cantidad', 'user_id', 'nota',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function sucursalOrigen()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_origen_id');
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_destino_id');
    }
}
