<?php

namespace App\Filament\Resources\PlanillaPeriodoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';
    protected static ?string $title = 'Boletas de pago';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('empleado_id')
                    ->relationship('empleado', 'nombre')
                    ->disabled(),

                Forms\Components\TextInput::make('sueldo_ordinario')
                    ->numeric()
                    ->prefix('Q')
                    ->required(),

                Forms\Components\TextInput::make('horas_extra')
                    ->label('Horas extra (Q)')
                    ->numeric()
                    ->prefix('Q')
                    ->default(0),

                Forms\Components\TextInput::make('anticipos')
                    ->numeric()
                    ->prefix('Q')
                    ->default(0),

                Forms\Components\TextInput::make('otras_deducciones')
                    ->label('Otras deducciones')
                    ->numeric()
                    ->prefix('Q')
                    ->default(0),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('empleado.nombre')
            ->columns([
                Tables\Columns\TextColumn::make('empleado.nombre')
                    ->label('Empleado'),

                Tables\Columns\TextColumn::make('sueldo_ordinario')
                    ->label('Sueldo')
                    ->money('GTQ'),

                Tables\Columns\TextColumn::make('horas_extra')
                    ->label('Horas extra')
                    ->money('GTQ'),

                Tables\Columns\TextColumn::make('bonificacion_incentivo')
                    ->label('Bonif. (Decreto 37-2001)')
                    ->money('GTQ'),

                Tables\Columns\TextColumn::make('igss')
                    ->label('IGSS (4.83%)')
                    ->money('GTQ')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('anticipos')
                    ->money('GTQ')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('otras_deducciones')
                    ->label('Otras deducc.')
                    ->money('GTQ')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('total_pagar')
                    ->label('Total a pagar')
                    ->money('GTQ')
                    ->weight('bold')
                    ->color('success'),
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
