<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;

class ViewVenta extends ViewRecord
{
    protected static string $resource = VentaResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Venta')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('sucursal.nombre')->label('Sucursal'),
                        TextEntry::make('cliente_nombre')->label('Cliente')->default('Consumidor final'),
                        TextEntry::make('metodo_pago')->label('Método de pago'),
                        TextEntry::make('total')->money('GTQ'),
                        TextEntry::make('estado')->badge(),
                        TextEntry::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i'),
                    ]),

                Section::make('Productos')
                    ->schema([
                        RepeatableEntry::make('detalles')
                            ->schema([
                                TextEntry::make('producto.nombre')->label('Producto'),
                                TextEntry::make('cantidad'),
                                TextEntry::make('precio_unitario')->money('GTQ'),
                                TextEntry::make('subtotal')->money('GTQ'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }
}
