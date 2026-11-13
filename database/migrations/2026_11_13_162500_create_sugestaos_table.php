
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sugestoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_empresa')
                ->constrained('empresas')
                ->onDelete('cascade');

            $table->foreignId('id_servico')
                ->constrained('servicos')
                ->onDelete('cascade');

            $table->string('titulo');
            $table->text('descricao');

            $table->enum('status', [
                'pendente',
                'em andamento',
                'concluida'
            ])->default('pendente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sugestoes');
    }
};
