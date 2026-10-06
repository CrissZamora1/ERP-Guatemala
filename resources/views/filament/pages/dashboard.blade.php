<x-filament-panels::page>
    <div class="mb-4">
        <select wire:model.live="sucursalFiltro"
            class="fi-select-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            <option value="">Todas las sucursales</option>
            @foreach($this->getSucursales() as $id => $nombre)
                <option value="{{ $id }}">{{ $nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="fi-card rounded-xl bg-white dark:bg-gray-800 p-4 shadow">
            <p class="text-sm text-gray-500">Ventas de hoy</p>
            <p class="text-2xl font-bold text-success-600">Q{{ number_format($this->getVentasHoy(), 2) }}</p>
        </div>

        <div class="fi-card rounded-xl bg-white dark:bg-gray-800 p-4 shadow">
            <p class="text-sm text-gray-500">Gastos del mes</p>
            <p class="text-2xl font-bold text-danger-600">Q{{ number_format($this->getGastosMes(), 2) }}</p>
        </div>

        <div class="fi-card rounded-xl bg-white dark:bg-gray-800 p-4 shadow">
            <p class="text-sm text-gray-500">Utilidad neta (mes)</p>
            <p class="text-2xl font-bold {{ $this->getUtilidadNeta() >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                Q{{ number_format($this->getUtilidadNeta(), 2) }}
            </p>
        </div>

        <div class="fi-card rounded-xl bg-white dark:bg-gray-800 p-4 shadow">
            <p class="text-sm text-gray-500">Alertas de stock mínimo</p>
            <p class="text-2xl font-bold {{ $this->getAlertasStock() > 0 ? 'text-warning-600' : 'text-success-600' }}">
                {{ $this->getAlertasStock() }}
            </p>
        </div>
    </div>

    <div class="fi-card rounded-xl bg-white dark:bg-gray-800 p-4 shadow">
        <p class="text-sm text-gray-500 mb-2">Ventas por sucursal (últimos 7 días)</p>
        <div class="space-y-2">
            @foreach($this->getVentasPorSucursal() as $s)
                <div class="flex items-center gap-2">
                    <span class="w-24 text-sm">{{ $s['nombre'] }}</span>
                    <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded h-4 overflow-hidden">
                        <div class="bg-primary-500 h-4"
                            style="width: {{ $s['total'] > 0 ? min(100, $s['total'] / 10) : 0 }}%"></div>
                    </div>
                    <span class="w-24 text-sm text-right">Q{{ number_format($s['total'], 2) }}</span>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>