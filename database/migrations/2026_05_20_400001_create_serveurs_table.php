<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('serveurs')) return;

        Schema::create('serveurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('type');
            $table->string('adresse_ip', 45)->nullable();
            $table->unsignedBigInteger('salle_id')->nullable();
            $table->string('responsable')->nullable();
            $table->string('ram')->nullable();
            $table->string('cpu')->nullable();
            $table->string('stockage')->nullable();
            $table->enum('statut', ['en_ligne', 'hors_ligne', 'maintenance'])->default('en_ligne');
            $table->date('date_installation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serveurs');
    }
};
