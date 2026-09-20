<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('planilla_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planilla_periodo_id')->constrained('planilla_periodos')->cascadeOnDelete();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->decimal('sueldo_ordinario', 12, 2)->default(0);
            $table->decimal('horas_extra', 12, 2)->default(0);
            $table->decimal('bonificacion_incentivo', 12, 2)->default(0);
            $table->decimal('anticipos', 12, 2)->default(0);
            $table->decimal('igss', 12, 2)->default(0);
            $table->decimal('otras_deducciones', 12, 2)->default(0);
            $table->decimal('total_pagar', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planilla_detalles');
    }
};
