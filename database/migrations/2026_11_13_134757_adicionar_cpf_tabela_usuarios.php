
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        if (!Schema::hasColumn('usuarios', 'cpf')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->string('cpf', 11)->nullable()->after('email');
            });
        }
    }



    public function down(): void
    {
        if (Schema::hasColumn('usuarios', 'cpf')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('cpf');
            });
        }
    }
};
