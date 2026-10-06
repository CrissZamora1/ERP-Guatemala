<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProveedorResource\Pages;
use App\Models\Proveedor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;
    protected static ?string $navigationLabel = 'Proveedores';
    protected static ?string $pluralModelLabel = 'Proveedores';
    protected static ?string $modelLabel = 'Proveedor';
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'encargado']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('nit')
                    ->label('NIT')
                    ->maxLength(20),

                Forms\Components\TextInput::make('direccion')
                    ->maxLength(255),

                Forms\Components\TextInput::make('telefono')
                    ->tel()
                    ->maxLength(20),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),

                Forms\Components\TextInput::make('contacto')
                    ->label('Persona de contacto')
                    ->maxLength(255),

                Forms\Components\TextInput::make('tiempo_entrega_dias')
                    ->label('Tiempo de entrega (días)')
                    ->numeric(),

                Forms\Components\Toggle::make('activo')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nit')
                    ->label('NIT'),

                Tables\Columns\TextColumn::make('telefono'),

                Tables\Columns\TextColumn::make('tiempo_entrega_dias')
                    ->label('Entrega (días)'),

                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }
}
