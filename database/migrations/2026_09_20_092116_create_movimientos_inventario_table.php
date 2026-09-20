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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('sucursal_origen_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('sucursal_destino_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->enum('tipo', ['entrada', 'salida', 'traslado', 'ajuste']);
            $table->integer('cantidad');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('nota')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
