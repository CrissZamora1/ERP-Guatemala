<x-filament-panels::page>
    <div class="flex gap-4 mb-6">
        <div>
            <label class="text-sm text-gray-500">Desde</label>
            <input type="date" wire:model.live="desde"
                class="fi-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 block">
        </div>
        <div>
            <label class="text-sm text-gray-500">Hasta</label>
            <input type="date" wire:model.live="hasta"
                class="fi-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 block">
        </div>
    </div>

    @php $totales = $this->getTotales(); @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="fi-card rounded-xl bg-white dark:bg-gray-800 p-4 shadow">
            <p class="text-sm text-gray-500">Ingresos</p>
            <p class="text-xl font-bold text-success-600">Q{{ number_format($totales['ingresos'], 2) }}</p>
        </div>
        <div class="fi-card rounded-xl bg-white dark:bg-gray-800 p-4 shadow">
            <p class="text-sm text-gray-500">Costo de ventas</p>
            <p class="text-xl font-bold text-danger-600">Q{{ number_format($totales['costo_ventas'], 2) }}</p>
        </div>
        <div class="fi-card rounded-xl bg-white dark:bg-gray-800 p-4 shadow">
            <p class="text-sm text-gray-500">Utilidad bruta</p>
            <p class="text-xl font-bold">Q{{ number_format($totales['utilidad_bruta'], 2) }}</p>
        </div>
        <div class="fi-card rounded-xl bg-white dark:bg-gray-800 p-4 shadow">
            <p class="text-sm text-gray-500">Gastos operativos</p>
            <p class="text-xl font-bold text-danger-600">Q{{ number_format($totales['gastos'], 2) }}</p>
        </div>
        <div class="fi-card rounded-xl bg-white dark:bg-gray-800 p-4 shadow">
            <p class="text-sm text-gray-500">Planilla</p>
            <p class="text-xl font-bold text-danger-600">Q{{ number_format($totales['planilla'], 2) }}</p>
        </div>
        <div class="fi-card rounded-xl bg-white dark:bg-gray-800 p-4 shadow border-2 border-primary-500">
            <p class="text-sm text-gray-500">Utilidad neta</p>
            <p class="text-xl font-bold {{ $totales['utilidad_neta'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                Q{{ number_format($totales['utilidad_neta'], 2) }}
            </p>
        </div>
    </div>

    <div class="fi-card rounded-xl bg-white dark:bg-gray-800 p-4 shadow">
        <p class="text-sm text-gray-500 mb-4">Comparativa por sucursal</p>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="text-left py-2">Sucursal</th>
                    <th class="text-right py-2">Ingresos</th>
                    <th class="text-right py-2">Costo ventas</th>
                    <th class="text-right py-2">Gastos</th>
                    <th class="text-right py-2">Planilla</th>
                    <th class="text-right py-2">Utilidad neta</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->getDatosPorSucursal() as $fila)
                    <tr class="border-b dark:border-gray-800">
                        <td class="py-2">{{ $fila['sucursal'] }}</td>
                        <td class="text-right">Q{{ number_format($fila['ingresos'], 2) }}</td>
                        <td class="text-right text-danger-600">Q{{ number_format($fila['costo_ventas'], 2) }}</td>
                        <td class="text-right text-danger-600">Q{{ number_format($fila['gastos'], 2) }}</td>
                        <td class="text-right text-danger-600">Q{{ number_format($fila['planilla'], 2) }}</td>
                        <td
                            class="text-right font-bold {{ $fila['utilidad_neta'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                            Q{{ number_format($fila['utilidad_neta'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>