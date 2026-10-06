<?php

namespace App\Filament\Pages;

use App\Models\Venta;
use App\Models\Gasto;
use App\Models\Inventario;
use App\Models\Sucursal;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Dashboard';

    public ?int $sucursalFiltro = null;

    public function getVentasHoy()
    {
        return Venta::withoutGlobalScopes()
            ->where('estado', 'completada')
            ->whereDate('created_at', today())
            ->when($this->sucursalFiltro, fn($q) => $q->where('sucursal_id', $this->sucursalFiltro))
            ->sum('total');
    }

    public function getGastosMes()
    {
        return Gasto::where('fecha', '>=', now()->startOfMonth())
            ->when($this->sucursalFiltro, fn($q) => $q->where('sucursal_id', $this->sucursalFiltro))
            ->sum('monto');
    }

    public function getUtilidadNeta()
    {
        $ventasMes = Venta::withoutGlobalScopes()
            ->where('estado', 'completada')
            ->where('created_at', '>=', now()->startOfMonth())
            ->when($this->sucursalFiltro, fn($q) => $q->where('sucursal_id', $this->sucursalFiltro))
            ->sum('total');

        return $ventasMes - $this->getGastosMes();
    }

    public function getAlertasStock()
    {
        return Inventario::withoutGlobalScopes()
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->when($this->sucursalFiltro, fn($q) => $q->where('sucursal_id', $this->sucursalFiltro))
            ->count();
    }

    public function getVentasPorSucursal()
    {
        return Sucursal::query()
            ->withSum(['ventas' => function ($q) {
                $q->where('estado', 'completada')
                    ->where('created_at', '>=', now()->subDays(7));
            }], 'total')
            ->get()
            ->map(fn($s) => [
                'nombre' => $s->nombre,
                'total' => $s->ventas_sum_total ?? 0,
            ]);
    }

    public function getSucursales()
    {
        return Sucursal::pluck('nombre', 'id');
    }
}
