<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('serveurs', function (Blueprint $table) {
            $table->string('categorie', 100)->nullable()->after('type');
            $table->string('marque', 100)->nullable()->after('categorie');
            $table->string('modele', 100)->nullable()->after('marque');
            $table->string('code_inventaire', 100)->nullable()->after('modele');
            $table->string('masque_reseau', 50)->nullable()->after('adresse_mac');
            $table->string('passerelle', 50)->nullable()->after('masque_reseau');
            $table->string('vlan', 50)->nullable()->after('passerelle');
            $table->string('version_firmware', 100)->nullable()->after('version_os');
            $table->string('criticite', 50)->nullable()->default('normale');
            $table->string('rack', 100)->nullable()->after('numero_rack');
            $table->string('position_rack', 50)->nullable()->after('rack');
            $table->string('tension_alimentation', 50)->nullable()->after('type_alimentation');
            $table->integer('nombre_ports')->nullable();
            $table->string('debit_reseau', 50)->nullable();
            $table->string('autonomie_batterie', 50)->nullable();
            $table->string('garantie', 100)->nullable();
            $table->string('technicien_responsable', 255)->nullable();
            $table->date('derniere_maintenance')->nullable();
            $table->date('prochaine_maintenance')->nullable();
            $table->string('image_equipement', 255)->nullable();
            $table->string('document_configuration', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('serveurs', function (Blueprint $table) {
            $table->dropColumn([
                'categorie','marque','modele','code_inventaire',
                'masque_reseau','passerelle','vlan','version_firmware',
                'criticite','rack','position_rack','tension_alimentation',
                'nombre_ports','debit_reseau','autonomie_batterie','garantie',
                'technicien_responsable','derniere_maintenance','prochaine_maintenance',
                'image_equipement','document_configuration',
            ]);
        });
    }
};
