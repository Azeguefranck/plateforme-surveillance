<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mesures', function (Blueprint $table) {
            if (!Schema::hasColumn('mesures', 'courant'))
                $table->float('courant')->nullable()->after('pir_detecte');
            if (!Schema::hasColumn('mesures', 'puissance'))
                $table->float('puissance')->nullable()->after('courant');
        });
    }

    public function down(): void
    {
        Schema::table('mesures', function (Blueprint $table) {
            $table->dropColumn(['courant', 'puissance']);
        });
    }
};
