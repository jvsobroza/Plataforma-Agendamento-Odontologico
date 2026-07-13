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
        Schema::create('servico_tratamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_planos')->constrained('plano_tratamentos')->onDelete('cascade');
            $table->foreignId('id_servico')->constrained('servicos')->onDelete('cascade');
            $table->foreignId('id_agendamento')->constrained('agendamentos')->onDelete('cascade');
            $table->integer('tempo');
            $table->decimal('preco', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_tratamentos');
    }
};
