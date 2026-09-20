<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanillaDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'planilla_periodo_id', 'empleado_id', 'sueldo_ordinario', 'horas_extra',
        'bonificacion_incentivo', 'anticipos', 'igss', 'otras_deducciones', 'total_pagar',
    ];

    public function planillaPeriodo()
    {
        return $this->belongsTo(PlanillaPeriodo::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
