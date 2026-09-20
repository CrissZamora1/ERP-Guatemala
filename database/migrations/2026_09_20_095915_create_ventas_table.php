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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_nit')->nullable();
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'otro'])->default('efectivo');
            $table->enum('estado', ['completada', 'anulada'])->default('completada');
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
