<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mesures')) return;

        Schema::create('mesures', function (Blueprint $table) {
            $table->id();
            $table->float('temperature')->nullable();
            $table->float('humidite')->nullable();
            $table->float('gaz')->nullable();
            $table->float('courant')->nullable();
            $table->float('puissance')->nullable();
            $table->float('tension')->nullable();
            $table->boolean('pir')->default(false);
            $table->integer('rssi')->nullable();
            $table->unsignedBigInteger('salle_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesures');
    }
};
