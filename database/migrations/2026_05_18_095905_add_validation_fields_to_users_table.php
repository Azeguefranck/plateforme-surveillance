<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'telephone'))
                $table->string('telephone')->nullable();
            if (!Schema::hasColumn('users', 'profession'))
                $table->string('profession')->nullable();
            if (!Schema::hasColumn('users', 'statut'))
                $table->string('statut')->default('en_attente');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['telephone', 'profession', 'statut'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) $table->dropColumn($col);
            }
        });
    }
};
