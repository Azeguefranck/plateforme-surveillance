<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('pays')->nullable();
            $table->string('region')->nullable();
            $table->string('departement')->nullable();
            $table->string('statut_matrimonial')->nullable();
            $table->string('telephone')->nullable();
            $table->string('profession')->nullable();

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
