<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'iso_pays'))         $table->string('iso_pays', 5)->nullable();
            if (!Schema::hasColumn('users', 'indicatif_tel'))    $table->string('indicatif_tel', 15)->nullable();
            if (!Schema::hasColumn('users', 'ville_residence'))  $table->string('ville_residence')->nullable();
            if (!Schema::hasColumn('users', 'lieu_naissance'))   $table->string('lieu_naissance', 200)->nullable();
            if (!Schema::hasColumn('users', 'admin_token'))      $table->string('admin_token', 64)->nullable()->index();
            if (!Schema::hasColumn('users', 'token_expires_at')) $table->timestamp('token_expires_at')->nullable();
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['iso_pays','indicatif_tel','ville_residence','lieu_naissance','admin_token','token_expires_at'];
            foreach ($cols as $col)
                if (Schema::hasColumn('users', $col)) $table->dropColumn($col);
        });
    }
};
