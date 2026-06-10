<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('alertes')) return;

        Schema::create('alertes', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('info');       // capteur : temperature, humidite, gaz, pir
            $table->text('message');
            $table->string('niveau')->default('warning'); // warning | critique
            $table->string('valeur')->nullable();
            $table->unsignedBigInteger('salle_id')->nullable();
            $table->unsignedBigInteger('equipement_id')->nullable();
            $table->boolean('resolu')->default(false);
            $table->boolean('envoi_email')->default(false);
            $table->boolean('envoi_sms')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};
