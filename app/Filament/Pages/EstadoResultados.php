<?php

namespace App\Filament\Pages;

use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Gasto;
use App\Models\PlanillaDetalle;
use Filament\Pages\Page;

class EstadoResultados extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Financiero';
    protected static ?string $navigationLabel = 'Estado de Resultados';
    protected static string $view = 'filament.pages.estado-resultados';
    protected static ?string $title = 'Estado de Resultados (P&L)';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public string $desde;
    public string $hasta;

    public function mount(): void
    {
        $this->desde = now()->startOfMonth()->toDateString();
        $this->hasta = now()->toDateString();
    }

    public function getDatosPorSucursal()
    {
        return Sucursal::all()->map(function ($sucursal) {
            $ingresos = Venta::withoutGlobalScopes()
                ->where('sucursal_id', $sucursal->id)
                ->where('estado', 'completada')
                ->whereBetween('created_at', [$this->desde, $this->hasta . ' 23:59:59'])
                ->sum('total');

            $costoVentas = VentaDetalle::withoutGlobalScopes()
                ->whereHas('venta', function ($q) use ($sucursal) {
                    $q->where('sucursal_id', $sucursal->id)
                        ->where('estado', 'completada')
                        ->whereBetween('created_at', [$this->desde, $this->hasta . ' 23:59:59']);
                })
                ->join('productos', 'productos.id', '=', 'venta_detalle.producto_id')
                ->selectRaw('SUM(venta_detalle.cantidad * productos.costo) as total_costo')
                ->value('total_costo') ?? 0;

            $gastos = Gasto::where('sucursal_id', $sucursal->id)
                ->whereBetween('fecha', [$this->desde, $this->hasta])
                ->sum('monto');

            $planilla = PlanillaDetalle::withoutGlobalScopes()
                ->whereHas('planillaPeriodo', function ($q) use ($sucursal) {
                    $q->where('sucursal_id', $sucursal->id)
                        ->whereBetween('fecha_inicio', [$this->desde, $this->hasta]);
                })
                ->sum('total_pagar');

            $utilidadBruta = $ingresos - $costoVentas;
            $utilidadNeta = $utilidadBruta - $gastos - $planilla;

            return [
                'sucursal' => $sucursal->nombre,
                'ingresos' => $ingresos,
                'costo_ventas' => $costoVentas,
                'utilidad_bruta' => $utilidadBruta,
                'gastos' => $gastos,
                'planilla' => $planilla,
                'utilidad_neta' => $utilidadNeta,
            ];
        });
    }

    public function getTotales()
    {
        $datos = $this->getDatosPorSucursal();

        return [
            'ingresos' => $datos->sum('ingresos'),
            'costo_ventas' => $datos->sum('costo_ventas'),
            'utilidad_bruta' => $datos->sum('utilidad_bruta'),
            'gastos' => $datos->sum('gastos'),
            'planilla' => $datos->sum('planilla'),
            'utilidad_neta' => $datos->sum('utilidad_neta'),
        ];
    }
}
