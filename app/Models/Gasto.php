<?php

namespace App\Models;

use App\Models\Scopes\SucursalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;

    protected $fillable = ['sucursal_id', 'concepto', 'categoria', 'monto', 'fecha', 'user_id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new SucursalScope);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
