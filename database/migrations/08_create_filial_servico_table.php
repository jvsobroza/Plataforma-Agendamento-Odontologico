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
        Schema::create('filial_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_filial')->constrained('filiais')->onDelete('cascade');
            $table->foreignId('id_servico')->constrained('servicos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filial_servico');
    }
};
