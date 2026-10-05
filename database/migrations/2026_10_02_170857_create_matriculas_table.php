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
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('documento_tipo')->default('DNI');
            $table->string('numero_documento');
            $table->string('email');
            $table->string('telefono')->nullable();
            $table->string('carrera');
            $table->string('ciclo')->default('I');
            $table->decimal('monto', 10, 2)->default(0);
            $table->string('metodo_pago')->default('Transferencia');
            $table->string('estado')->default('confirmado');
            $table->date('fecha_matricula');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
