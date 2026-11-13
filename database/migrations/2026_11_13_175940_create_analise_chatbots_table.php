<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analises_chatbot', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_feedback')
                ->constrained('feedback')
                ->onDelete('cascade');

            $table->text('analise');

            $table->json('palavras_chave')->nullable();

            $table->enum('prioridade_identificada', [
                'baixa',
                'media',
                'alta'
            ]);

            $table->enum('tipo_atendimento', [
                'pendente',
                'automatico',
                'manual'
            ])->default('pendente');

            $table->text('resposta_gerada')->nullable();

            $table->enum('status_atendimento', [
                'aguardando_analise',
                'respondido_automaticamente',
                'aguardando_administrador',
                'em_atendimento',
                'finalizado'
            ])->default('aguardando_analise');

            $table->timestamp('resposta_enviada_em')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analises_chatbot');
    }
};
