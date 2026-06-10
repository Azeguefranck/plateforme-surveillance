<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('serveurs', function (Blueprint $table) {
            if (!Schema::hasColumn('serveurs', 'categorie'))
                $table->string('categorie', 100)->nullable()->after('type');
            if (!Schema::hasColumn('serveurs', 'marque'))
                $table->string('marque', 100)->nullable();
            if (!Schema::hasColumn('serveurs', 'modele'))
                $table->string('modele', 100)->nullable();
            if (!Schema::hasColumn('serveurs', 'code_inventaire'))
                $table->string('code_inventaire', 100)->nullable();
            if (!Schema::hasColumn('serveurs', 'numero_serie'))
                $table->string('numero_serie', 100)->nullable();
            if (!Schema::hasColumn('serveurs', 'adresse_mac'))
                $table->string('adresse_mac', 20)->nullable();
            if (!Schema::hasColumn('serveurs', 'masque_reseau'))
                $table->string('masque_reseau', 50)->nullable();
            if (!Schema::hasColumn('serveurs', 'passerelle'))
                $table->string('passerelle', 50)->nullable();
            if (!Schema::hasColumn('serveurs', 'vlan'))
                $table->string('vlan', 50)->nullable();
            if (!Schema::hasColumn('serveurs', 'port_reseau'))
                $table->string('port_reseau', 50)->nullable();
            if (!Schema::hasColumn('serveurs', 'numero_rack'))
                $table->string('numero_rack', 50)->nullable();
            if (!Schema::hasColumn('serveurs', 'rack'))
                $table->string('rack', 100)->nullable();
            if (!Schema::hasColumn('serveurs', 'position_rack'))
                $table->string('position_rack', 50)->nullable();
            if (!Schema::hasColumn('serveurs', 'localisation_physique'))
                $table->string('localisation_physique', 255)->nullable();
            if (!Schema::hasColumn('serveurs', 'systeme_exploitation'))
                $table->string('systeme_exploitation', 100)->nullable();
            if (!Schema::hasColumn('serveurs', 'version_os'))
                $table->string('version_os', 100)->nullable();
            if (!Schema::hasColumn('serveurs', 'version_firmware'))
                $table->string('version_firmware', 100)->nullable();
            if (!Schema::hasColumn('serveurs', 'consommation_energetique'))
                $table->string('consommation_energetique', 50)->nullable();
            if (!Schema::hasColumn('serveurs', 'tension_alimentation'))
                $table->string('tension_alimentation', 50)->nullable();
            if (!Schema::hasColumn('serveurs', 'criticite'))
                $table->string('criticite', 50)->nullable()->default('normale');
            if (!Schema::hasColumn('serveurs', 'nombre_ports'))
                $table->integer('nombre_ports')->nullable();
            if (!Schema::hasColumn('serveurs', 'debit_reseau'))
                $table->string('debit_reseau', 50)->nullable();
            if (!Schema::hasColumn('serveurs', 'autonomie_batterie'))
                $table->string('autonomie_batterie', 50)->nullable();
            if (!Schema::hasColumn('serveurs', 'garantie'))
                $table->string('garantie', 100)->nullable();
            if (!Schema::hasColumn('serveurs', 'fournisseur'))
                $table->string('fournisseur', 255)->nullable();
            if (!Schema::hasColumn('serveurs', 'technicien_responsable'))
                $table->string('technicien_responsable', 255)->nullable();
            if (!Schema::hasColumn('serveurs', 'description'))
                $table->text('description')->nullable();
            if (!Schema::hasColumn('serveurs', 'derniere_maintenance'))
                $table->date('derniere_maintenance')->nullable();
            if (!Schema::hasColumn('serveurs', 'prochaine_maintenance'))
                $table->date('prochaine_maintenance')->nullable();
            if (!Schema::hasColumn('serveurs', 'image_equipement'))
                $table->string('image_equipement', 255)->nullable();
            if (!Schema::hasColumn('serveurs', 'document_configuration'))
                $table->string('document_configuration', 255)->nullable();
        });

        if (Schema::hasColumn('serveurs', 'statut')) {
            \DB::statement("ALTER TABLE serveurs MODIFY statut ENUM('en_ligne','hors_ligne','maintenance') DEFAULT 'en_ligne'");
        }
    }

    public function down(): void
    {
        Schema::table('serveurs', function (Blueprint $table) {
            $cols = [
                'categorie','marque','modele','code_inventaire','numero_serie',
                'adresse_mac','masque_reseau','passerelle','vlan','port_reseau',
                'numero_rack','rack','position_rack','localisation_physique',
                'systeme_exploitation','version_os','version_firmware',
                'consommation_energetique','tension_alimentation','criticite',
                'nombre_ports','debit_reseau','autonomie_batterie','garantie',
                'fournisseur','technicien_responsable','description',
                'derniere_maintenance','prochaine_maintenance',
                'image_equipement','document_configuration',
            ];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('serveurs', $c));
            if ($existing) $table->dropColumn(array_values($existing));
        });
    }
};
