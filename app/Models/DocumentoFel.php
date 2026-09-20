<?php

namespace App\Models;

use App\Models\Scopes\SucursalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoFel extends Model
{
    use HasFactory;

    protected $table = 'documentos_fel';

    protected $fillable = [
        'sucursal_id', 'tipo', 'numero_dte', 'serie', 'uuid_fel',
        'estado', 'total', 'pdf_path', 'xml_path',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new SucursalScope);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
