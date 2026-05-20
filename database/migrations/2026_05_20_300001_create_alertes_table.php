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
            $table->string('type_alerte')->default('info');
            $table->text('message');
            $table->string('niveau')->default('warning'); // warning | critique
            $table->string('valeur')->nullable();
            $table->unsignedBigInteger('salle_id')->nullable();
            $table->boolean('lu')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};
