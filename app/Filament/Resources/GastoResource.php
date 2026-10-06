<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GastoResource\Pages;
use App\Models\Gasto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;
    protected static ?string $navigationLabel = 'Gastos';
    protected static ?string $pluralModelLabel = 'Gastos';
    protected static ?string $modelLabel = 'Gasto';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Financiero';

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

                Forms\Components\TextInput::make('concepto')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Select::make('categoria')
                    ->options([
                        'alquiler' => 'Alquiler',
                        'servicios' => 'Servicios (luz, agua, internet)',
                        'salarios' => 'Salarios adicionales',
                        'mantenimiento' => 'Mantenimiento',
                        'transporte' => 'Transporte',
                        'publicidad' => 'Publicidad',
                        'impuestos' => 'Impuestos',
                        'otros' => 'Otros',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('monto')
                    ->numeric()
                    ->prefix('Q')
                    ->required(),

                Forms\Components\DatePicker::make('fecha')
                    ->required()
                    ->default(now()),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('concepto')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal'),

                Tables\Columns\BadgeColumn::make('categoria'),

                Tables\Columns\TextColumn::make('monto')
                    ->money('GTQ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre'),

                Tables\Filters\SelectFilter::make('categoria')
                    ->options([
                        'alquiler' => 'Alquiler',
                        'servicios' => 'Servicios',
                        'salarios' => 'Salarios adicionales',
                        'mantenimiento' => 'Mantenimiento',
                        'transporte' => 'Transporte',
                        'publicidad' => 'Publicidad',
                        'impuestos' => 'Impuestos',
                        'otros' => 'Otros',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGastos::route('/'),
            'create' => Pages\CreateGasto::route('/create'),
            'edit' => Pages\EditGasto::route('/{record}/edit'),
        ];
    }
}
