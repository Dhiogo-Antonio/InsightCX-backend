<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analises_chatbot', function (Blueprint $table) {
            $table->text('resposta_administrador')->nullable();
            $table->timestamp('respondido_administrador_em')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('analises_chatbot', function (Blueprint $table) {
            $table->dropColumn([
                'resposta_administrador',
                'respondido_administrador_em'
            ]);
        });
    }
};
