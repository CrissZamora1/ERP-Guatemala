<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Venta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;
    protected static ?string $navigationLabel = 'Ventas';
    protected static ?string $pluralModelLabel = 'Ventas';
    protected static ?string $modelLabel = 'Venta';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre')
                    ->default(fn() => Auth::user()?->sucursal_id)
                    ->required(),

                Forms\Components\TextInput::make('cliente_nombre')
                    ->label('Cliente (opcional)')
                    ->maxLength(255),

                Forms\Components\TextInput::make('cliente_nit')
                    ->label('NIT (opcional)')
                    ->maxLength(20),

                Forms\Components\Select::make('metodo_pago')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'tarjeta' => 'Tarjeta',
                        'transferencia' => 'Transferencia',
                        'otro' => 'Otro',
                    ])
                    ->default('efectivo')
                    ->required(),

                Forms\Components\Repeater::make('detalles')
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
                                $set('precio_unitario', $producto?->precio ?? 0);
                            }),

                        Forms\Components\TextInput::make('cantidad')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live()
                            ->afterStateUpdated(
                                fn($state, Forms\Get $get, Forms\Set $set) =>
                                $set('subtotal', round(($state ?? 0) * ($get('precio_unitario') ?? 0), 2))
                            ),

                        Forms\Components\TextInput::make('precio_unitario')
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
                    ->required(),

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
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal'),

                Tables\Columns\TextColumn::make('cliente_nombre')
                    ->label('Cliente')
                    ->default('Consumidor final'),

                Tables\Columns\TextColumn::make('total')
                    ->money('GTQ')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'success' => 'completada',
                        'danger' => 'anulada',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Esto devolverá el stock de todos los productos de esta venta. ¿Continuar?')
                    ->visible(fn ($record) => $record->estado === 'completada')
                    ->action(function ($record) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                            foreach ($record->detalles as $detalle) {
                                $inventario = \App\Models\Inventario::withoutGlobalScopes()
                                    ->where('producto_id', $detalle->producto_id)
                                    ->where('sucursal_id', $record->sucursal_id)
                                    ->lockForUpdate()
                                    ->first();

                                if ($inventario) {
                                    $inventario->increment('stock_actual', $detalle->cantidad);
                                }

                                \App\Models\MovimientoInventario::create([
                                    'producto_id' => $detalle->producto_id,
                                    'sucursal_origen_id' => null,
                                    'sucursal_destino_id' => $record->sucursal_id,
                                    'tipo' => 'ajuste',
                                    'cantidad' => $detalle->cantidad,
                                    'user_id' => auth()->id(),
                                    'nota' => "Anulación de venta #{$record->id}",
                                ]);
                            }

                            $record->update(['estado' => 'anulada']);
                        });
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'view' => Pages\ViewVenta::route('/{record}'),
        ];
    }
}
