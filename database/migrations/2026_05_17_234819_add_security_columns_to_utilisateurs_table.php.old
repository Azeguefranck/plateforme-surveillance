<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

public function up(): void
{
Schema::table('utilisateurs', function (Blueprint $table) {

if (!Schema::hasColumn('utilisateurs','prenom'))
$table->string('prenom')->nullable();

if (!Schema::hasColumn('utilisateurs','telephone'))
$table->string('telephone')->nullable();

if (!Schema::hasColumn('utilisateurs','profession'))
$table->string('profession')->nullable();

if (!Schema::hasColumn('utilisateurs','statut'))
$table->string('statut')->default('EN_ATTENTE');

if (!Schema::hasColumn('utilisateurs','validation_code'))
$table->string('validation_code')->nullable();

});
}

public function down(): void
{
Schema::table('utilisateurs', function (Blueprint $table) {

$table->dropColumn([
'prenom',
'telephone',
'profession',
'statut',
'validation_code'
]);

});
}

};
