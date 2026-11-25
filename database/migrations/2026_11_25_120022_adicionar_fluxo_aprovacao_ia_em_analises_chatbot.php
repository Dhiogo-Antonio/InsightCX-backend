
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analises_chatbot', function (Blueprint $table) {

            // Situação da resposta gerada pela IA.
            $table->enum('status_aprovacao', [
                'aguardando',
                'aprovada',
                'recusada',
                'nao_gerada'
            ])->default('aguardando');

            // Administrador responsável pela decisão.
            $table->foreignId('avaliado_por')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();

            // Data da aprovação ou recusa.
            $table->timestamp('avaliado_em')->nullable();

            // Justificativa opcional da recusa.
            $table->text('motivo_recusa')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('analises_chatbot', function (Blueprint $table) {

            $table->dropForeign(['avaliado_por']);

            $table->dropColumn([
                'status_aprovacao',
                'avaliado_por',
                'avaliado_em',
                'motivo_recusa'
            ]);

        });
    }
};
