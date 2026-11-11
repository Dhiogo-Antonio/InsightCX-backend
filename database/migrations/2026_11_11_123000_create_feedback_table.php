<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('id_servico')->constrained('servicos')->onDelete('cascade');
            $table->string('assunto');
            $table->string('comentario');
            $table->enum('categoria', ['elogio', 'reclamacao']);
            $table->enum('status', ['pendente', 'em analise', 'resolvido'])->default('pendente');
            $table->enum('prioridade', ['baixa', 'media', 'alta']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
