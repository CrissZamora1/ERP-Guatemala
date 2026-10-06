<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarioResource\Pages;
use App\Models\Inventario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventarioResource extends Resource
{
    protected static ?string $model = Inventario::class;
    protected static ?string $navigationLabel = 'Inventario';
    protected static ?string $pluralModelLabel = 'Inventarios';
    protected static ?string $modelLabel = 'Inventario';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function canCreate(): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('producto_id')
                    ->label('Producto')
                    ->relationship('producto', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn () => auth()->user()?->role === 'cajero')
                    ->default(fn () => auth()->user()?->role === 'cajero' ? auth()->user()?->sucursal_id : null)
                    ->dehydrated(),

                Forms\Components\TextInput::make('stock_actual')
                    ->numeric()
                    ->required()
                    ->default(0),

                Forms\Components\TextInput::make('stock_minimo')
                    ->numeric()
                    ->required()
                    ->default(0),

                Forms\Components\TextInput::make('stock_maximo')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_actual')
                    ->label('Stock')
                    ->sortable()
                    ->color(fn($record) => $record->stock_actual <= $record->stock_minimo ? 'danger' : 'success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('stock_minimo')
                    ->label('Mínimo'),

                Tables\Columns\TextColumn::make('stock_maximo')
                    ->label('Máximo'),
            ])
            ->recordUrl(function ($record) {
                $puedeEditar = in_array(auth()->user()?->role, ['admin', 'encargado'])
                    || $record->sucursal_id === auth()->user()?->sucursal_id;

                return $puedeEditar
                    ? \App\Filament\Resources\InventarioResource::getUrl('edit', ['record' => $record])
                    : null;
            })
            ->filters([
                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) =>
                        in_array(auth()->user()?->role, ['admin', 'encargado'])
                        || $record->sucursal_id === auth()->user()?->sucursal_id
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventarios::route('/'),
            'create' => Pages\CreateInventario::route('/create'),
            'edit' => Pages\EditInventario::route('/{record}/edit'),
        ];
    }
}
