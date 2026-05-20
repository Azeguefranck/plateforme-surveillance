<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nom'))            $table->string('nom')->nullable();
            if (!Schema::hasColumn('users', 'prenom'))         $table->string('prenom')->nullable();
            if (!Schema::hasColumn('users', 'sexe'))           $table->string('sexe')->nullable();
            if (!Schema::hasColumn('users', 'date_naissance')) $table->date('date_naissance')->nullable();
            if (!Schema::hasColumn('users', 'nationalite'))    $table->string('nationalite')->nullable();
            if (!Schema::hasColumn('users', 'iso_pays'))       $table->string('iso_pays', 5)->nullable();
            if (!Schema::hasColumn('users', 'indicatif_tel'))  $table->string('indicatif_tel', 15)->nullable();
            if (!Schema::hasColumn('users', 'ville_residence'))$table->string('ville_residence')->nullable();
            if (!Schema::hasColumn('users', 'arrondissement')) $table->string('arrondissement')->nullable();
            if (!Schema::hasColumn('users', 'quartier'))       $table->string('quartier')->nullable();
            if (!Schema::hasColumn('users', 'adresse'))        $table->string('adresse', 500)->nullable();
            if (!Schema::hasColumn('users', 'organisation'))   $table->string('organisation')->nullable();
            if (!Schema::hasColumn('users', 'photo_profil'))   $table->string('photo_profil')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['nom','prenom','sexe','date_naissance','nationalite','iso_pays',
                     'indicatif_tel','ville_residence','arrondissement','quartier',
                     'adresse','organisation','photo_profil'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) $table->dropColumn($col);
            }
        });
    }
};
