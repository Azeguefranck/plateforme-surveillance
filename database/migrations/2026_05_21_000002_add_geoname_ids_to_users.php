<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// [NOUVEAU] Ajoute les colonnes geonameId et les nouveaux champs géo
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pays_code'))
                $table->string('pays_code', 3)->nullable()->after('pays');
            if (!Schema::hasColumn('users', 'pays_nom'))
                $table->string('pays_nom')->nullable()->after('pays_code');
            if (!Schema::hasColumn('users', 'pays_geoname_id'))
                $table->unsignedBigInteger('pays_geoname_id')->nullable()->after('pays_nom');
            if (!Schema::hasColumn('users', 'region_geoname_id'))
                $table->unsignedBigInteger('region_geoname_id')->nullable()->after('region');
            if (!Schema::hasColumn('users', 'dept_geoname_id'))
                $table->unsignedBigInteger('dept_geoname_id')->nullable()->after('departement');
            if (!Schema::hasColumn('users', 'arr_geoname_id'))
                $table->unsignedBigInteger('arr_geoname_id')->nullable()->after('arrondissement');
            if (!Schema::hasColumn('users', 'ville'))
                $table->string('ville')->nullable()->after('ville_residence');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['pays_code','pays_nom','pays_geoname_id','region_geoname_id','dept_geoname_id','arr_geoname_id','ville'];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('users', $c));
            if ($existing) $table->dropColumn(array_values($existing));
        });
    }
};
