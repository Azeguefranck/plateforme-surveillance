@extends('layouts.app')

@section('content')

<style>
@keyframes fadeIn{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
.srv-wrap{animation:fadeIn .4s ease;}

/* ── Stats bar ── */
.stat-row{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:20px;}
.stat-card{
  background:#0d1a2e;border:1px solid #182640;border-radius:14px;
  padding:16px 22px;flex:1 1 160px;min-width:140px;
  display:flex;flex-direction:column;gap:4px;
}
.stat-label{font-size:11px;font-weight:bold;letter-spacing:1.5px;color:#6b7fa0;text-transform:uppercase;}
.stat-val{font-size:28px;font-weight:bold;color:#d4dced;}
.stat-val.green{color:#2fa84f;}
.stat-val.red{color:#e74c3c;}
.stat-val.orange{color:#e67e22;}

/* ── Header ── */
.page-header{
  display:flex;justify-content:space-between;align-items:center;
  margin-bottom:20px;flex-wrap:wrap;gap:10px;
}
.page-title{font-size:22px;font-weight:bold;color:#d4dced;}
.btn-add{
  background:#2fa84f;color:#060c1a;border:none;border-radius:9px;
  padding:10px 20px;font-weight:bold;font-size:14px;cursor:pointer;
  display:inline-flex;align-items:center;gap:8px;transition:.2s;
}
.btn-add:hover{background:#249040;}

/* ── Alert flash ── */
.flash{
  padding:12px 18px;border-radius:10px;margin-bottom:16px;font-weight:bold;font-size:14px;
}
.flash.success{background:rgba(47,168,79,.15);border:1px solid #2fa84f;color:#2fa84f;}
.flash.error{background:rgba(231,76,60,.15);border:1px solid #e74c3c;color:#e74c3c;}

/* ── Table ── */
.table-wrap{
  background:#0d1a2e;border:1px solid #182640;border-radius:16px;overflow:hidden;
}
.tbl{width:100%;border-collapse:collapse;font-size:13px;}
.tbl th{
  background:#091527;padding:12px 14px;text-align:left;
  color:#6b7fa0;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;
  border-bottom:1px solid #182640;
}
.tbl td{padding:12px 14px;border-bottom:1px solid rgba(24,38,64,.6);color:#d4dced;vertical-align:middle;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tbody tr:hover td{background:rgba(47,168,79,.04);}
.tbl .no-data{text-align:center;padding:40px;color:#6b7fa0;}

/* ── Badges statut ── */
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:bold;letter-spacing:.5px;}
.badge.actif{background:rgba(47,168,79,.15);border:1px solid #2fa84f;color:#2fa84f;}
.badge.inactif{background:rgba(231,76,60,.15);border:1px solid #e74c3c;color:#e74c3c;}
.badge.maintenance{background:rgba(230,126,34,.15);border:1px solid #e67e22;color:#e67e22;}
.badge.panne{background:rgba(231,76,60,.15);border:1px solid #e74c3c;color:#e74c3c;}

/* ── Action buttons ── */
.btn-edit,.btn-del{
  border:none;border-radius:7px;padding:6px 12px;
  font-size:12px;font-weight:bold;cursor:pointer;transition:.2s;
}
.btn-edit{background:rgba(46,134,193,.2);color:#2e86c1;border:1px solid #2e86c1;}
.btn-edit:hover{background:#2e86c1;color:white;}
.btn-del{background:rgba(231,76,60,.15);color:#e74c3c;border:1px solid #e74c3c;margin-left:6px;}
.btn-del:hover{background:#e74c3c;color:white;}

/* ── Modal ── */
.modal-bg{
  display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
  z-index:1000;align-items:flex-start;justify-content:center;padding:20px;overflow-y:auto;
}
.modal-bg.open{display:flex;}
.modal{
  background:#0d1a2e;border:1px solid #182640;border-radius:16px;
  padding:28px;width:100%;max-width:860px;margin:auto;
  animation:fadeIn .3s ease;
}
.modal h2{font-size:18px;font-weight:bold;color:#d4dced;margin-bottom:20px;}
.modal-close{
  float:right;background:none;border:none;color:#6b7fa0;
  font-size:22px;cursor:pointer;line-height:1;transition:.2s;
}
.modal-close:hover{color:#e74c3c;}

.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
@media(max-width:640px){.form-grid{grid-template-columns:1fr;}}
.form-group{display:flex;flex-direction:column;gap:5px;}
.form-group.full{grid-column:1/-1;}
.form-label{font-size:12px;font-weight:bold;color:#6b7fa0;letter-spacing:.5px;}
.form-control{
  background:#0a1525;border:1.5px solid #1e3050;border-radius:9px;
  color:#d4dced;padding:9px 13px;font-size:13px;outline:none;
  transition:border-color .2s;width:100%;
}
.form-control:focus{border-color:#2fa84f;}
.form-control option{background:#0a1525;}

.modal-footer{margin-top:20px;display:flex;justify-content:flex-end;gap:10px;}
.btn-cancel{
  background:transparent;border:1px solid #182640;border-radius:9px;
  color:#6b7fa0;padding:10px 20px;font-size:14px;cursor:pointer;transition:.2s;
}
.btn-cancel:hover{border-color:#6b7fa0;color:#d4dced;}
.btn-submit{
  background:#2fa84f;color:#060c1a;border:none;border-radius:9px;
  padding:10px 24px;font-weight:bold;font-size:14px;cursor:pointer;transition:.2s;
}
.btn-submit:hover{background:#249040;}

.section-label{
  font-size:11px;font-weight:bold;letter-spacing:1.5px;color:#2fa84f;
  text-transform:uppercase;grid-column:1/-1;padding:8px 0 2px;
  border-bottom:1px solid #182640;margin-bottom:4px;
}
</style>

<div class="srv-wrap">

{{-- Flash messages --}}
@if(session('success'))
  <div class="flash success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="flash error"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
@endif

{{-- Stats --}}
@php
  $total       = $serveurs->count();
  $actifs      = $serveurs->where('statut','actif')->count();
  $inactifs    = $serveurs->where('statut','inactif')->count();
  $maintenance = $serveurs->where('statut','maintenance')->count();
  $panne       = $serveurs->where('statut','panne')->count();
@endphp
<div class="stat-row">
  <div class="stat-card">
    <span class="stat-label">Total</span>
    <span class="stat-val">{{ $total }}</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Actifs</span>
    <span class="stat-val green">{{ $actifs }}</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Inactifs</span>
    <span class="stat-val red">{{ $inactifs }}</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Maintenance</span>
    <span class="stat-val orange">{{ $maintenance }}</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">En panne</span>
    <span class="stat-val red">{{ $panne }}</span>
  </div>
</div>

{{-- Header --}}
<div class="page-header">
  <div class="page-title"><i class="fa-solid fa-server" style="color:#2fa84f;margin-right:10px;"></i>Gestion des Serveurs</div>
  <button class="btn-add" onclick="openModal('modal-add')">
    <i class="fa-solid fa-plus"></i> Ajouter un serveur
  </button>
</div>

{{-- Table --}}
<div class="table-wrap">
  <table class="tbl">
    <thead>
      <tr>
        <th>#</th>
        <th>Nom</th>
        <th>Type</th>
        <th>Adresse IP</th>
        <th>Salle</th>
        <th>OS</th>
        <th>Statut</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($serveurs as $s)
      <tr>
        <td style="color:#6b7fa0">{{ $s->id }}</td>
        <td style="font-weight:bold;">{{ $s->nom }}</td>
        <td style="color:#4a9fc4">{{ $s->type }}</td>
        <td><code style="color:#2fa84f;font-size:12px;">{{ $s->adresse_ip ?: '—' }}</code></td>
        <td>
          @if($s->salle_nom)
            <span style="background:rgba(47,168,79,.1);border:1px solid rgba(47,168,79,.3);border-radius:6px;padding:2px 8px;font-size:12px;color:#2fa84f;">
              {{ $s->salle_code }} — {{ $s->salle_nom }}
            </span>
          @else
            <span style="color:#6b7fa0">—</span>
          @endif
        </td>
        <td style="font-size:12px;">{{ $s->systeme_exploitation ?: '—' }}{{ $s->version_os ? ' '.$s->version_os : '' }}</td>
        <td>
          @php
            $badgeClass = match($s->statut ?? 'actif') {
              'actif'       => 'actif',
              'inactif'     => 'inactif',
              'maintenance' => 'maintenance',
              'panne'       => 'panne',
              default       => 'actif',
            };
          @endphp
          <span class="badge {{ $badgeClass }}">{{ strtoupper($s->statut ?? 'actif') }}</span>
        </td>
        <td>
          <button class="btn-edit" onclick="openEdit({{ json_encode($s) }})">
            <i class="fa-solid fa-pen"></i> Modifier
          </button>
          <form method="POST" action="/serveurs/delete/{{ $s->id }}" style="display:inline;"
                onsubmit="event.preventDefault();const f=this;CyberConfirm.show({title:'Supprimer le serveur',message:'Supprimer ce serveur ? Cette action est irréversible.',icon:'fa-solid fa-server',confirmText:'Supprimer',confirmColor:'danger'}).then(ok=>{if(ok){f.onsubmit=null;f.submit();}})">
            @csrf
            <button type="submit" class="btn-del"><i class="fa-solid fa-trash"></i> Supprimer</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" class="no-data"><i class="fa-solid fa-server" style="font-size:32px;opacity:.3;display:block;margin-bottom:10px;"></i>Aucun serveur enregistré</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

</div>

{{-- ══ MODAL AJOUT ══ --}}
<div class="modal-bg" id="modal-add">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modal-add')">&times;</button>
    <h2><i class="fa-solid fa-plus" style="color:#2fa84f;"></i> Ajouter un serveur</h2>
    <form method="POST" action="/serveurs/store">
      @csrf
      <div class="form-grid">
        <div class="section-label">Informations générales</div>
        <div class="form-group">
          <label class="form-label">Nom *</label>
          <input class="form-control" type="text" name="nom" required placeholder="Nom du serveur">
        </div>
        <div class="form-group">
          <label class="form-label">Type *</label>
          <select class="form-control" name="type" required>
            <option value="">-- Sélectionner --</option>
            @foreach(['Serveur Web','Serveur Base de Données','Serveur DNS','Serveur DHCP','Serveur FTP','Serveur Mail','Serveur Proxy','Serveur Cloud','Serveur Virtualisation','Serveur IA','Serveur Backup','Serveur Monitoring','Serveur Linux','Serveur Windows','Serveur NAS','Serveur Applications','Serveur Streaming','Serveur Kubernetes','Serveur Docker','Serveur API','Serveur Sécurité','Serveur VPN','Serveur Active Directory','Serveur IoT','Serveur Apache','Serveur Nginx','Serveur MySQL','Serveur PostgreSQL','Serveur MongoDB','Serveur Oracle','Serveur Redis','Serveur Cassandra','Serveur GPU','Serveur HPC','Serveur ERP','Serveur Odoo','Serveur SAP'] as $t)
            <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Adresse IP</label>
          <input class="form-control" type="text" name="adresse_ip" placeholder="192.168.1.1">
        </div>
        <div class="form-group">
          <label class="form-label">Nom de domaine</label>
          <input class="form-control" type="text" name="nom_domaine" placeholder="srv.exemple.com">
        </div>
        <div class="form-group">
          <label class="form-label">Salle</label>
          <select class="form-control" name="salle_id">
            <option value="">-- Aucune --</option>
            @foreach($salles as $sl)
            <option value="{{ $sl->id }}">{{ $sl->code }} — {{ $sl->nom }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Responsable</label>
          <input class="form-control" type="text" name="responsable" placeholder="Nom du responsable">
        </div>

        <div class="section-label">Système & Matériel</div>
        <div class="form-group">
          <label class="form-label">Système d'exploitation</label>
          <input class="form-control" type="text" name="systeme_exploitation" placeholder="Ubuntu, Windows Server...">
        </div>
        <div class="form-group">
          <label class="form-label">Version OS</label>
          <input class="form-control" type="text" name="version_os" placeholder="22.04 LTS, 2022...">
        </div>
        <div class="form-group">
          <label class="form-label">RAM</label>
          <input class="form-control" type="text" name="ram" placeholder="16 Go, 64 Go...">
        </div>
        <div class="form-group">
          <label class="form-label">CPU</label>
          <input class="form-control" type="text" name="cpu" placeholder="Intel Xeon E5-2690...">
        </div>
        <div class="form-group">
          <label class="form-label">Stockage</label>
          <input class="form-control" type="text" name="stockage" placeholder="1 To SSD, 4 To HDD...">
        </div>
        <div class="form-group">
          <label class="form-label">Température (°C)</label>
          <input class="form-control" type="number" step="0.1" name="temperature" placeholder="45.5">
        </div>

        <div class="section-label">Statut & Installation</div>
        <div class="form-group">
          <label class="form-label">Statut</label>
          <select class="form-control" name="statut">
            <option value="actif">Actif</option>
            <option value="inactif">Inactif</option>
            <option value="maintenance">En maintenance</option>
            <option value="panne">En panne</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Date d'installation</label>
          <input class="form-control" type="date" name="date_installation">
        </div>
        <div class="form-group full">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" rows="2" placeholder="Description du serveur..."></textarea>
        </div>

        <div class="section-label">Localisation & Réseau</div>
        <div class="form-group">
          <label class="form-label">Localisation physique</label>
          <input class="form-control" type="text" name="localisation_physique" placeholder="Baie A, Rack 3...">
        </div>
        <div class="form-group">
          <label class="form-label">Numéro de rack</label>
          <input class="form-control" type="text" name="numero_rack" placeholder="Rack-01">
        </div>
        <div class="form-group">
          <label class="form-label">Adresse MAC</label>
          <input class="form-control" type="text" name="adresse_mac" placeholder="AA:BB:CC:DD:EE:FF">
        </div>
        <div class="form-group">
          <label class="form-label">Port réseau</label>
          <input class="form-control" type="text" name="port_reseau" placeholder="Port 24, Switch-A">
        </div>

        <div class="section-label">Fournisseur & Énergie</div>
        <div class="form-group">
          <label class="form-label">Fournisseur</label>
          <input class="form-control" type="text" name="fournisseur" placeholder="Dell, HP, Lenovo...">
        </div>
        <div class="form-group">
          <label class="form-label">Numéro de série</label>
          <input class="form-control" type="text" name="numero_serie" placeholder="SN-XXXXXXXX">
        </div>
        <div class="form-group">
          <label class="form-label">Type d'alimentation</label>
          <input class="form-control" type="text" name="type_alimentation" placeholder="220V, Redondante...">
        </div>
        <div class="form-group">
          <label class="form-label">Consommation (W)</label>
          <input class="form-control" type="number" step="0.1" name="consommation_energetique" placeholder="350">
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('modal-add')">Annuler</button>
        <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
      </div>
    </form>
  </div>
</div>

{{-- ══ MODAL ÉDITION ══ --}}
<div class="modal-bg" id="modal-edit">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modal-edit')">&times;</button>
    <h2><i class="fa-solid fa-pen" style="color:#2e86c1;"></i> Modifier le serveur</h2>
    <form method="POST" id="edit-form" action="">
      @csrf
      <div class="form-grid">
        <div class="section-label">Informations générales</div>
        <div class="form-group">
          <label class="form-label">Nom *</label>
          <input class="form-control" type="text" name="nom" id="e_nom" required>
        </div>
        <div class="form-group">
          <label class="form-label">Type *</label>
          <select class="form-control" name="type" id="e_type" required>
            <option value="">-- Sélectionner --</option>
            @foreach(['Serveur Web','Serveur Base de Données','Serveur DNS','Serveur DHCP','Serveur FTP','Serveur Mail','Serveur Proxy','Serveur Cloud','Serveur Virtualisation','Serveur IA','Serveur Backup','Serveur Monitoring','Serveur Linux','Serveur Windows','Serveur NAS','Serveur Applications','Serveur Streaming','Serveur Kubernetes','Serveur Docker','Serveur API','Serveur Sécurité','Serveur VPN','Serveur Active Directory','Serveur IoT','Serveur Apache','Serveur Nginx','Serveur MySQL','Serveur PostgreSQL','Serveur MongoDB','Serveur Oracle','Serveur Redis','Serveur Cassandra','Serveur GPU','Serveur HPC','Serveur ERP','Serveur Odoo','Serveur SAP'] as $t)
            <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Adresse IP</label>
          <input class="form-control" type="text" name="adresse_ip" id="e_adresse_ip">
        </div>
        <div class="form-group">
          <label class="form-label">Nom de domaine</label>
          <input class="form-control" type="text" name="nom_domaine" id="e_nom_domaine">
        </div>
        <div class="form-group">
          <label class="form-label">Salle</label>
          <select class="form-control" name="salle_id" id="e_salle_id">
            <option value="">-- Aucune --</option>
            @foreach($salles as $sl)
            <option value="{{ $sl->id }}">{{ $sl->code }} — {{ $sl->nom }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Responsable</label>
          <input class="form-control" type="text" name="responsable" id="e_responsable">
        </div>

        <div class="section-label">Système & Matériel</div>
        <div class="form-group">
          <label class="form-label">Système d'exploitation</label>
          <input class="form-control" type="text" name="systeme_exploitation" id="e_systeme_exploitation">
        </div>
        <div class="form-group">
          <label class="form-label">Version OS</label>
          <input class="form-control" type="text" name="version_os" id="e_version_os">
        </div>
        <div class="form-group">
          <label class="form-label">RAM</label>
          <input class="form-control" type="text" name="ram" id="e_ram">
        </div>
        <div class="form-group">
          <label class="form-label">CPU</label>
          <input class="form-control" type="text" name="cpu" id="e_cpu">
        </div>
        <div class="form-group">
          <label class="form-label">Stockage</label>
          <input class="form-control" type="text" name="stockage" id="e_stockage">
        </div>
        <div class="form-group">
          <label class="form-label">Température (°C)</label>
          <input class="form-control" type="number" step="0.1" name="temperature" id="e_temperature">
        </div>

        <div class="section-label">Statut & Installation</div>
        <div class="form-group">
          <label class="form-label">Statut</label>
          <select class="form-control" name="statut" id="e_statut">
            <option value="actif">Actif</option>
            <option value="inactif">Inactif</option>
            <option value="maintenance">En maintenance</option>
            <option value="panne">En panne</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Date d'installation</label>
          <input class="form-control" type="date" name="date_installation" id="e_date_installation">
        </div>
        <div class="form-group full">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" id="e_description" rows="2"></textarea>
        </div>

        <div class="section-label">Localisation & Réseau</div>
        <div class="form-group">
          <label class="form-label">Localisation physique</label>
          <input class="form-control" type="text" name="localisation_physique" id="e_localisation_physique">
        </div>
        <div class="form-group">
          <label class="form-label">Numéro de rack</label>
          <input class="form-control" type="text" name="numero_rack" id="e_numero_rack">
        </div>
        <div class="form-group">
          <label class="form-label">Adresse MAC</label>
          <input class="form-control" type="text" name="adresse_mac" id="e_adresse_mac">
        </div>
        <div class="form-group">
          <label class="form-label">Port réseau</label>
          <input class="form-control" type="text" name="port_reseau" id="e_port_reseau">
        </div>

        <div class="section-label">Fournisseur & Énergie</div>
        <div class="form-group">
          <label class="form-label">Fournisseur</label>
          <input class="form-control" type="text" name="fournisseur" id="e_fournisseur">
        </div>
        <div class="form-group">
          <label class="form-label">Numéro de série</label>
          <input class="form-control" type="text" name="numero_serie" id="e_numero_serie">
        </div>
        <div class="form-group">
          <label class="form-label">Type d'alimentation</label>
          <input class="form-control" type="text" name="type_alimentation" id="e_type_alimentation">
        </div>
        <div class="form-group">
          <label class="form-label">Consommation (W)</label>
          <input class="form-control" type="number" step="0.1" name="consommation_energetique" id="e_consommation_energetique">
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('modal-edit')">Annuler</button>
        <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Mettre à jour</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id){ document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal(id){ document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }

// Fermer en cliquant sur le fond
document.querySelectorAll('.modal-bg').forEach(bg => {
  bg.addEventListener('click', function(e){ if(e.target===this) closeModal(this.id); });
});

function setVal(id, val){ var el=document.getElementById(id); if(el) el.value = val ?? ''; }

function openEdit(s){
  document.getElementById('edit-form').action = '/serveurs/update/' + s.id;
  setVal('e_nom', s.nom);
  setVal('e_type', s.type);
  setVal('e_adresse_ip', s.adresse_ip);
  setVal('e_nom_domaine', s.nom_domaine);
  setVal('e_salle_id', s.salle_id);
  setVal('e_responsable', s.responsable);
  setVal('e_systeme_exploitation', s.systeme_exploitation);
  setVal('e_version_os', s.version_os);
  setVal('e_ram', s.ram);
  setVal('e_cpu', s.cpu);
  setVal('e_stockage', s.stockage);
  setVal('e_temperature', s.temperature);
  setVal('e_statut', s.statut);
  setVal('e_date_installation', s.date_installation ? s.date_installation.substring(0,10) : '');
  setVal('e_description', s.description);
  setVal('e_localisation_physique', s.localisation_physique);
  setVal('e_numero_rack', s.numero_rack);
  setVal('e_adresse_mac', s.adresse_mac);
  setVal('e_port_reseau', s.port_reseau);
  setVal('e_fournisseur', s.fournisseur);
  setVal('e_numero_serie', s.numero_serie);
  setVal('e_type_alimentation', s.type_alimentation);
  setVal('e_consommation_energetique', s.consommation_energetique);
  openModal('modal-edit');
}
</script>

@endsection
