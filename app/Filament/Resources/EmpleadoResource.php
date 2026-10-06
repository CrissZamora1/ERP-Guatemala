<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpleadoResource\Pages;
use App\Models\Empleado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class EmpleadoResource extends Resource
{
    protected static ?string $model = Empleado::class;
    protected static ?string $navigationLabel = 'Empleados';
    protected static ?string $pluralModelLabel = 'Empleados';
    protected static ?string $modelLabel = 'Empleado';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Planilla';

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'encargado']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre')
                    ->default(fn() => Auth::user()?->sucursal_id)
                    ->required(),

                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('dpi')
                    ->label('DPI')
                    ->maxLength(20),

                Forms\Components\TextInput::make('nit')
                    ->label('NIT')
                    ->maxLength(20),

                Forms\Components\TextInput::make('puesto')
                    ->maxLength(255),

                Forms\Components\DatePicker::make('fecha_ingreso')
                    ->label('Fecha de ingreso'),

                Forms\Components\TextInput::make('sueldo_base')
                    ->label('Sueldo base mensual')
                    ->numeric()
                    ->prefix('Q')
                    ->required()
                    ->default(0),

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

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal'),

                Tables\Columns\TextColumn::make('puesto'),

                Tables\Columns\TextColumn::make('sueldo_base')
                    ->label('Sueldo base')
                    ->money('GTQ'),

                Tables\Columns\TextColumn::make('fecha_ingreso')
                    ->date('d/m/Y'),

                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre'),
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
            'index' => Pages\ListEmpleados::route('/'),
            'create' => Pages\CreateEmpleado::route('/create'),
            'edit' => Pages\EditEmpleado::route('/{record}/edit'),
        ];
    }
}
