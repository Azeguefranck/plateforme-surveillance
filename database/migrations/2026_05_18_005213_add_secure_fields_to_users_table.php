<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'pays'))              $table->string('pays')->nullable();
            if (!Schema::hasColumn('users', 'region'))            $table->string('region')->nullable();
            if (!Schema::hasColumn('users', 'departement'))       $table->string('departement')->nullable();
            if (!Schema::hasColumn('users', 'statut_matrimonial'))$table->string('statut_matrimonial')->nullable();
            if (!Schema::hasColumn('users', 'telephone'))         $table->string('telephone')->nullable();
            if (!Schema::hasColumn('users', 'profession'))        $table->string('profession')->nullable();

            if (!Schema::hasColumn('users', 'validation_status'))
                $table->enum('validation_status',
                ['en_attente','valide','refuse','bloque'])
                ->default('en_attente');

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'pays',
                'region',
                'departement',
                'statut_matrimonial',
                'telephone',
                'profession',
                'validation_status'
            ]);

        });
    }
};
