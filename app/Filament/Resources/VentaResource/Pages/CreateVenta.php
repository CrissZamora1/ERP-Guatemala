<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {
            $venta = static::getModel()::create([
                'sucursal_id' => $data['sucursal_id'],
                'cliente_nombre' => $data['cliente_nombre'] ?? null,
                'cliente_nit' => $data['cliente_nit'] ?? null,
                'metodo_pago' => $data['metodo_pago'],
                'estado' => 'completada',
                'total' => 0,
            ]);

            $total = 0;

            foreach ($data['detalles'] as $detalle) {
                $inventario = Inventario::withoutGlobalScopes()
                    ->where('producto_id', $detalle['producto_id'])
                    ->where('sucursal_id', $data['sucursal_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$inventario || $inventario->stock_actual < $detalle['cantidad']) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'detalles' => "Stock insuficiente para el producto seleccionado.",
                    ]);
                }

                $venta->detalles()->create([
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $detalle['subtotal'],
                ]);

                $inventario->decrement('stock_actual', $detalle['cantidad']);
                $inventario->refresh();

                if ($inventario->stock_actual <= $inventario->stock_minimo) {
                    $inventario->generarOrdenAutomatica();
                }

                MovimientoInventario::create([
                    'producto_id' => $detalle['producto_id'],
                    'sucursal_origen_id' => $data['sucursal_id'],
                    'sucursal_destino_id' => null,
                    'tipo' => 'salida',
                    'cantidad' => $detalle['cantidad'],
                    'user_id' => auth()->id(),
                    'nota' => "Venta #{$venta->id}",
                ]);

                $total += $detalle['subtotal'];
            }

            $venta->update(['total' => $total]);

            return $venta;
        });
    }
}
