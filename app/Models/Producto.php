<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku', 'codigo_barras', 'nombre', 'descripcion',
        'categoria_id', 'costo', 'precio', 'activo',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    public function stockEnSucursal($sucursalId)
    {
        return $this->inventarios()->where('sucursal_id', $sucursalId)->first()?->stock_actual ?? 0;
    }
}
