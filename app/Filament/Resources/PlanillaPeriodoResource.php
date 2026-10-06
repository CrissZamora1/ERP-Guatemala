<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanillaPeriodoResource\Pages;
use App\Filament\Resources\PlanillaPeriodoResource\RelationManagers;
use App\Models\Empleado;
use App\Models\PlanillaPeriodo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PlanillaPeriodoResource extends Resource
{
    protected static ?string $model = PlanillaPeriodo::class;
    protected static ?string $navigationLabel = 'Períodos de Planilla';
    protected static ?string $pluralModelLabel = 'Períodos de Planilla';
    protected static ?string $modelLabel = 'Período de Planilla';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
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

                Forms\Components\DatePicker::make('fecha_inicio')
                    ->required(),

                Forms\Components\DatePicker::make('fecha_fin')
                    ->required(),

                Forms\Components\Select::make('estado')
                    ->options([
                        'abierto' => 'Abierto',
                        'procesado' => 'Procesado',
                        'pagado' => 'Pagado',
                    ])
                    ->default('abierto')
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal'),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('fecha_fin')
                    ->date('d/m/Y'),

                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'gray' => 'abierto',
                        'warning' => 'procesado',
                        'success' => 'pagado',
                    ]),

                Tables\Columns\TextColumn::make('detalles_sum_total_pagar')
                    ->label('Total planilla')
                    ->state(fn($record) => 'Q' . number_format($record->detalles()->sum('total_pagar'), 2)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre'),
            ])
            ->actions([
                Tables\Actions\Action::make('generar')
                    ->label('Generar planilla')
                    ->icon('heroicon-o-bolt')
                    ->color('warning')
                    ->visible(fn($record) => $record->detalles()->count() === 0)
                    ->requiresConfirmation()
                    ->modalDescription('Esto crea un detalle de planilla por cada empleado activo de esta sucursal, con el sueldo base como punto de partida.')
                    ->action(function ($record) {
                        $empleados = Empleado::withoutGlobalScopes()
                            ->where('sucursal_id', $record->sucursal_id)
                            ->where('activo', true)
                            ->get();

                        foreach ($empleados as $empleado) {
                            $record->detalles()->create([
                                'empleado_id' => $empleado->id,
                                'sueldo_ordinario' => $empleado->sueldo_base,
                                'horas_extra' => 0,
                                'anticipos' => 0,
                                'otras_deducciones' => 0,
                            ]);
                        }
                    }),

                Tables\Actions\Action::make('marcar_pagado')
                    ->label('Marcar pagado')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->estado !== 'pagado' && $record->detalles()->count() > 0)
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['estado' => 'pagado'])),

                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetallesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanillaPeriodos::route('/'),
            'create' => Pages\CreatePlanillaPeriodo::route('/create'),
            'edit' => Pages\EditPlanillaPeriodo::route('/{record}/edit'),
        ];
    }
}
