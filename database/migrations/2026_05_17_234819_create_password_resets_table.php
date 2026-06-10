<?php

use Illuminate\Database\Migrations\Migration;

// Table password_resets déjà créée sous le nom password_reset_tokens
// dans 0001_01_01_000000_create_users_table.php — no-op pour éviter le doublon.
return new class extends Migration
{
    public function up(): void {}
    public function down(): void {}
};
