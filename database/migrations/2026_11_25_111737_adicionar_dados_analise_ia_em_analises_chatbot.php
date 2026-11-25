<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analises_chatbot', function (Blueprint $table) {
            $table->enum('sentimento', [
                'positivo',
                'negativo',
                'misto',
                'neutro'
            ])->nullable();

            $table->json('aspectos_positivos')->nullable();
            $table->json('aspectos_negativos')->nullable();
            $table->json('problemas_identificados')->nullable();
            $table->json('recomendacoes')->nullable();

            $table->decimal('confianca_analise', 5, 2)->nullable();
            $table->text('resumo_gerencial')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('analises_chatbot', function (Blueprint $table) {
            $table->dropColumn([
                'sentimento',
                'aspectos_positivos',
                'aspectos_negativos',
                'problemas_identificados',
                'recomendacoes',
                'confianca_analise',
                'resumo_gerencial'
            ]);
        });
    }
};