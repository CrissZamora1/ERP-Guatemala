<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdenCompraResource\Pages;
use App\Models\OrdenCompra;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class OrdenCompraResource extends Resource
{
    protected static ?string $model = OrdenCompra::class;
    protected static ?string $navigationLabel = 'Órdenes de Compra';
    protected static ?string $pluralModelLabel = 'Órdenes de Compra';
    protected static ?string $modelLabel = 'Orden de Compra';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'encargado']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('proveedor_id')
                    ->label('Proveedor')
                    ->relationship('proveedor', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre')
                    ->default(fn() => Auth::user()?->sucursal_id)
                    ->required(),

                Forms\Components\Select::make('estado')
                    ->options([
                        'borrador' => 'Borrador',
                        'enviada' => 'Enviada',
                        'recibida' => 'Recibida',
                        'cancelada' => 'Cancelada',
                    ])
                    ->default('borrador')
                    ->required(),

                Forms\Components\Textarea::make('nota')
                    ->columnSpanFull(),

                Forms\Components\Repeater::make('detalles')
                    ->relationship()
                    ->label('Productos')
                    ->schema([
                        Forms\Components\Select::make('producto_id')
                            ->label('Producto')
                            ->options(Producto::where('activo', true)->pluck('nombre', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $producto = Producto::find($state);
                                $set('costo_unitario', $producto?->costo ?? 0);
                            }),

                        Forms\Components\TextInput::make('cantidad')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live()
                            ->afterStateUpdated(
                                fn($state, Forms\Get $get, Forms\Set $set) =>
                                $set('subtotal', round(($state ?? 0) * ($get('costo_unitario') ?? 0), 2))
                            ),

                        Forms\Components\TextInput::make('costo_unitario')
                            ->numeric()
                            ->prefix('Q')
                            ->required()
                            ->live()
                            ->afterStateUpdated(
                                fn($state, Forms\Get $get, Forms\Set $set) =>
                                $set('subtotal', round(($get('cantidad') ?? 0) * ($state ?? 0), 2))
                            ),

                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('Q')
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(4)
                    ->columnSpanFull()
                    ->addActionLabel('Agregar producto')
                    ->minItems(1)
                    ->required()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $total = collect($state)->sum('subtotal');
                        $set('total', $total);
                    }),

                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->prefix('Q')
                    ->disabled()
                    ->dehydrated()
                    ->default(0),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#'),

                Tables\Columns\TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal'),

                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'gray' => 'borrador',
                        'warning' => 'enviada',
                        'success' => 'recibida',
                        'danger' => 'cancelada',
                    ]),

                Tables\Columns\IconColumn::make('generada_automatica')
                    ->label('Auto')
                    ->boolean(),

                Tables\Columns\TextColumn::make('total')
                    ->money('GTQ'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'borrador' => 'Borrador',
                        'enviada' => 'Enviada',
                        'recibida' => 'Recibida',
                        'cancelada' => 'Cancelada',
                    ]),
                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('recibir')
                    ->label('Recibir mercadería')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->estado !== 'recibida' && $record->estado !== 'cancelada')
                    ->form(function ($record) {
                        return $record->detalles->map(function ($detalle) {
                            return Forms\Components\TextInput::make("recibido_{$detalle->id}")
                                ->label($detalle->producto->nombre . ' — pedido: ' . $detalle->cantidad)
                                ->numeric()
                                ->default($detalle->cantidad)
                                ->required()
                                ->minValue(0);
                        })->toArray();
                    })
                    ->action(function ($record, array $data) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
                            foreach ($record->detalles as $detalle) {
                                $cantidadRecibida = (int) ($data["recibido_{$detalle->id}"] ?? 0);

                                $detalle->update(['cantidad_recibida' => $cantidadRecibida]);

                                if ($cantidadRecibida <= 0) {
                                    continue;
                                }

                                $inventario = \App\Models\Inventario::withoutGlobalScopes()
                                    ->firstOrCreate(
                                        ['producto_id' => $detalle->producto_id, 'sucursal_id' => $record->sucursal_id],
                                        ['stock_actual' => 0, 'stock_minimo' => 0]
                                    );

                                $inventario->increment('stock_actual', $cantidadRecibida);

                                \App\Models\MovimientoInventario::create([
                                    'producto_id' => $detalle->producto_id,
                                    'sucursal_destino_id' => $record->sucursal_id,
                                    'tipo' => 'entrada',
                                    'cantidad' => $cantidadRecibida,
                                    'user_id' => auth()->id(),
                                    'nota' => "Recepción de orden de compra #{$record->id}" .
                                        ($cantidadRecibida < $detalle->cantidad ? ' (recepción parcial)' : ''),
                                ]);
                            }

                            $record->update(['estado' => 'recibida']);
                        });
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrdenCompras::route('/'),
            'create' => Pages\CreateOrdenCompra::route('/create'),
            'edit' => Pages\EditOrdenCompra::route('/{record}/edit'),
        ];
    }
}
