<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mesures') && !Schema::hasColumn('mesures', 'equipement_id')) {
            Schema::table('mesures', function (Blueprint $table) {
                $table->unsignedBigInteger('equipement_id')->nullable()->after('salle_id');
            });
        }

        if (Schema::hasTable('alertes') && !Schema::hasColumn('alertes', 'equipement_id')) {
            Schema::table('alertes', function (Blueprint $table) {
                $table->unsignedBigInteger('equipement_id')->nullable()->after('salle_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('mesures') && Schema::hasColumn('mesures', 'equipement_id')) {
            Schema::table('mesures', function (Blueprint $table) {
                $table->dropColumn('equipement_id');
            });
        }

        if (Schema::hasTable('alertes') && Schema::hasColumn('alertes', 'equipement_id')) {
            Schema::table('alertes', function (Blueprint $table) {
                $table->dropColumn('equipement_id');
            });
        }
    }
};
