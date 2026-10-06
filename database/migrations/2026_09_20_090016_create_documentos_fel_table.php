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
        Schema::create('documentos_fel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->enum('tipo', ['factura', 'nota_credito', 'nota_debito']);
            $table->string('numero_dte')->nullable();
            $table->string('serie')->nullable();
            $table->string('uuid_fel')->nullable();
            $table->enum('estado', ['pendiente', 'certificado', 'anulado', 'error'])->default('pendiente');
            $table->decimal('total', 12, 2)->default(0);
            $table->string('pdf_path')->nullable();
            $table->string('xml_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_fel');
    }
};
