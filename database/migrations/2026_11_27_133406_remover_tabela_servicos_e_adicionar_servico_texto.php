
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('sugestoes') &&
            Schema::hasColumn('sugestoes', 'id_servico')
        ) {
            Schema::table('sugestoes', function (Blueprint $table) {
                $table->dropForeign('sugestoes_id_servico_foreign');
                $table->dropColumn('id_servico');
            });
        }
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Não é possível restaurar automaticamente o relacionamento anterior.'
        );
    }
};
