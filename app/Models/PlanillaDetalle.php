<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanillaDetalle extends Model
{
    use HasFactory;

    const BONIFICACION_INCENTIVO = 250.00; // Decreto 37-2001
    const PORCENTAJE_IGSS = 0.0483; // Cuota laboral IGSS Guatemala

    protected $fillable = [
        'planilla_periodo_id',
        'empleado_id',
        'sueldo_ordinario',
        'horas_extra',
        'bonificacion_incentivo',
        'anticipos',
        'igss',
        'otras_deducciones',
        'total_pagar',
    ];

    protected static function booted(): void
    {
        static::saving(function (PlanillaDetalle $detalle) {
            $detalle->bonificacion_incentivo = self::BONIFICACION_INCENTIVO;
            $detalle->igss = round(($detalle->sueldo_ordinario + $detalle->horas_extra) * self::PORCENTAJE_IGSS, 2);

            $detalle->total_pagar = round(
                $detalle->sueldo_ordinario
                    + $detalle->horas_extra
                    + $detalle->bonificacion_incentivo
                    - $detalle->anticipos
                    - $detalle->igss
                    - $detalle->otras_deducciones,
                2
            );
        });
    }

    public function planillaPeriodo()
    {
        return $this->belongsTo(PlanillaPeriodo::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
