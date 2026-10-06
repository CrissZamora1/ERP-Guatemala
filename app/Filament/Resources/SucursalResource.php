<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SucursalResource\Pages;
use App\Models\Sucursal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SucursalResource extends Resource
{
    protected static ?string $model = Sucursal::class;
    protected static ?string $navigationLabel = 'Sucursales';
    protected static ?string $pluralModelLabel = 'Sucursales';
    protected static ?string $modelLabel = 'Sucursal';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return auth()->user()?->role !== 'cajero';
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->role !== 'cajero';
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->role !== 'cajero';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('codigo')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Ej: SUC01'),

                Forms\Components\TextInput::make('direccion')
                    ->maxLength(255),

                Forms\Components\TextInput::make('telefono')
                    ->tel()
                    ->maxLength(20),

                Forms\Components\Toggle::make('activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('telefono'),

                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->label('Creado')
                    ->sortable(),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSucursals::route('/'),
            'create' => Pages\CreateSucursal::route('/create'),
            'edit' => Pages\EditSucursal::route('/{record}/edit'),
        ];
    }
}
