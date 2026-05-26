@extends('layouts.app')

@section('content')

<style>
@keyframes fadeIn{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
.sl-wrap{animation:fadeIn .4s ease;}

/* ── Stats ── */
.stat-row{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:20px;}
.stat-card{
  background:#0d1a2e;border:1px solid #182640;border-radius:14px;
  padding:16px 22px;flex:1 1 160px;min-width:140px;
  display:flex;flex-direction:column;gap:4px;
}
.stat-label{font-size:11px;font-weight:bold;letter-spacing:1.5px;color:#6b7fa0;text-transform:uppercase;}
.stat-val{font-size:28px;font-weight:bold;color:#d4dced;}
.stat-val.green{color:#2fa84f;}
.stat-val.orange{color:#e67e22;}
.stat-val.blue{color:#2e86c1;}

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

/* ── Flash ── */
.flash{padding:12px 18px;border-radius:10px;margin-bottom:16px;font-weight:bold;font-size:14px;}
.flash.success{background:rgba(47,168,79,.15);border:1px solid #2fa84f;color:#2fa84f;}
.flash.error{background:rgba(231,76,60,.15);border:1px solid #e74c3c;color:#e74c3c;}

/* ── Cards grille ── */
.salles-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px;}
.salle-card{
  background:#0d1a2e;border:1px solid #182640;border-radius:16px;
  padding:20px;transition:border-color .2s,box-shadow .2s;
}
.salle-card:hover{border-color:#2fa84f;box-shadow:0 4px 20px rgba(47,168,79,.08);}

.salle-card-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;flex-wrap:wrap;gap:8px;}
.salle-code{
  background:rgba(47,168,79,.15);border:1px solid rgba(47,168,79,.4);
  color:#2fa84f;padding:4px 12px;border-radius:20px;
  font-size:12px;font-weight:bold;letter-spacing:1px;
}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:bold;}
.badge.actif{background:rgba(47,168,79,.15);border:1px solid #2fa84f;color:#2fa84f;}
.badge.inactif{background:rgba(231,76,60,.15);border:1px solid #e74c3c;color:#e74c3c;}
.badge.maintenance{background:rgba(230,126,34,.15);border:1px solid #e67e22;color:#e67e22;}

.salle-nom{font-size:16px;font-weight:bold;color:#d4dced;margin-bottom:12px;}
.salle-info{display:flex;flex-direction:column;gap:7px;margin-bottom:16px;}
.salle-info-row{display:flex;align-items:center;gap:8px;font-size:13px;color:#a0aec0;}
.salle-info-row i{color:#2fa84f;width:14px;text-align:center;font-size:12px;}
.salle-info-row span{font-weight:bold;color:#d4dced;margin-left:auto;}

.badge-sec{
  font-size:10px;font-weight:bold;letter-spacing:.5px;padding:2px 8px;border-radius:10px;
}
.badge-sec.standard{background:rgba(46,134,193,.15);color:#2e86c1;border:1px solid rgba(46,134,193,.4);}
.badge-sec.eleve{background:rgba(230,126,34,.15);color:#e67e22;border:1px solid rgba(230,126,34,.4);}
.badge-sec.critique{background:rgba(231,76,60,.15);color:#e74c3c;border:1px solid rgba(231,76,60,.4);}

.badge-net{
  font-size:10px;font-weight:bold;letter-spacing:.5px;padding:2px 8px;border-radius:10px;
}
.badge-net.connecte{background:rgba(47,168,79,.15);color:#2fa84f;border:1px solid rgba(47,168,79,.4);}
.badge-net.deconnecte{background:rgba(231,76,60,.15);color:#e74c3c;border:1px solid rgba(231,76,60,.4);}

.salle-actions{display:flex;gap:8px;border-top:1px solid #182640;padding-top:14px;}
.btn-edit,.btn-del{
  flex:1;border:none;border-radius:8px;padding:8px;
  font-size:12px;font-weight:bold;cursor:pointer;transition:.2s;
  display:flex;align-items:center;justify-content:center;gap:6px;
}
.btn-edit{background:rgba(46,134,193,.2);color:#2e86c1;border:1px solid rgba(46,134,193,.4);}
.btn-edit:hover{background:#2e86c1;color:white;}
.btn-del{background:rgba(231,76,60,.15);color:#e74c3c;border:1px solid rgba(231,76,60,.4);}
.btn-del:hover{background:#e74c3c;color:white;}

/* ── Empty state ── */
.empty-state{
  text-align:center;padding:60px 20px;
  background:#0d1a2e;border:1px solid #182640;border-radius:16px;
}
.empty-state i{font-size:48px;color:#182640;margin-bottom:16px;display:block;}
.empty-state p{color:#6b7fa0;font-size:15px;margin-bottom:20px;}

/* ── Modal ── */
.modal-bg{
  display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
  z-index:1000;align-items:flex-start;justify-content:center;padding:20px;overflow-y:auto;
}
.modal-bg.open{display:flex;}
.modal{
  background:#0d1a2e;border:1px solid #182640;border-radius:16px;
  padding:28px;width:100%;max-width:600px;margin:auto;
  animation:fadeIn .3s ease;
}
.modal h2{font-size:18px;font-weight:bold;color:#d4dced;margin-bottom:20px;}
.modal-close{float:right;background:none;border:none;color:#6b7fa0;font-size:22px;cursor:pointer;line-height:1;}
.modal-close:hover{color:#e74c3c;}

.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
@media(max-width:560px){.form-grid{grid-template-columns:1fr;}}
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

.code-info{
  background:rgba(47,168,79,.08);border:1px solid rgba(47,168,79,.3);border-radius:9px;
  padding:10px 14px;grid-column:1/-1;font-size:13px;color:#2fa84f;
  display:flex;align-items:center;gap:8px;
}

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
</style>

<div class="sl-wrap">

{{-- Flash messages --}}
@if(session('success'))
  <div class="flash success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="flash error"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
@endif

{{-- Stats --}}
<div class="stat-row">
  <div class="stat-card">
    <span class="stat-label">Total salles</span>
    <span class="stat-val">{{ $stats['total'] }}</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Actives</span>
    <span class="stat-val green">{{ $stats['actives'] }}</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Maintenance</span>
    <span class="stat-val orange">{{ $stats['maintenance'] }}</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Total serveurs</span>
    <span class="stat-val blue">{{ $stats['total_serveurs'] }}</span>
  </div>
</div>

{{-- Header --}}
<div class="page-header">
  <div class="page-title"><i class="fa-solid fa-building-server" style="color:#2fa84f;margin-right:10px;"></i>Gestion des Salles Serveurs</div>
  <button class="btn-add" onclick="openModal('modal-add')">
    <i class="fa-solid fa-plus"></i> Ajouter une salle
  </button>
</div>

{{-- Cards --}}
@if($salles->isEmpty())
  <div class="empty-state">
    <i class="fa-solid fa-building-server"></i>
    <p>Aucune salle créée pour le moment.</p>
    <button class="btn-add" onclick="openModal('modal-add')">
      <i class="fa-solid fa-plus"></i> Créer la première salle
    </button>
  </div>
@else
  <div class="salles-grid">
    @foreach($salles as $sl)
    <div class="salle-card">
      <div class="salle-card-header">
        <span class="salle-code">{{ $sl->code }}</span>
        @php
          $bc = match($sl->statut) { 'actif'=>'actif','inactif'=>'inactif','maintenance'=>'maintenance', default=>'actif' };
        @endphp
        <span class="badge {{ $bc }}">{{ strtoupper($sl->statut) }}</span>
      </div>
      <div class="salle-nom">{{ $sl->nom }}</div>
      <div class="salle-info">
        @if($sl->localisation)
        <div class="salle-info-row">
          <i class="fa-solid fa-location-dot"></i>
          <span>Localisation</span>
          <span>{{ $sl->localisation }}</span>
        </div>
        @endif
        @if($sl->responsable)
        <div class="salle-info-row">
          <i class="fa-solid fa-user"></i>
          <span>Responsable</span>
          <span>{{ $sl->responsable }}</span>
        </div>
        @endif
        @if($sl->capacite)
        <div class="salle-info-row">
          <i class="fa-solid fa-server"></i>
          <span>Capacité</span>
          <span>{{ $sl->capacite }} serveurs</span>
        </div>
        @endif
        <div class="salle-info-row">
          <i class="fa-solid fa-shield-halved"></i>
          <span>Sécurité</span>
          @php $secClass = match($sl->niveau_securite) { 'eleve'=>'eleve','critique'=>'critique', default=>'standard' }; @endphp
          <span class="badge-sec {{ $secClass }}">{{ strtoupper($sl->niveau_securite) }}</span>
        </div>
        <div class="salle-info-row">
          <i class="fa-solid fa-network-wired"></i>
          <span>Réseau</span>
          @php $netClass = $sl->statut_reseau === 'connecte' ? 'connecte' : 'deconnecte'; @endphp
          <span class="badge-net {{ $netClass }}">{{ strtoupper($sl->statut_reseau) }}</span>
        </div>
        @if($sl->description)
        <div class="salle-info-row" style="align-items:flex-start;">
          <i class="fa-solid fa-align-left" style="margin-top:2px;"></i>
          <span style="color:#a0aec0;font-size:12px;margin-left:22px;font-style:italic;">{{ Str::limit($sl->description, 80) }}</span>
        </div>
        @endif
      </div>
      <div class="salle-actions">
        <button class="btn-edit" onclick="openEdit({{ json_encode($sl) }})">
          <i class="fa-solid fa-pen"></i> Modifier
        </button>
        <form method="POST" action="/salles/delete/{{ $sl->id }}" style="flex:1;"
              data-code="{{ e($sl->code) }}"
              onsubmit="event.preventDefault();const f=this;CyberConfirm.show({title:'Supprimer la salle',message:'Supprimer la salle '+f.dataset.code+' ? Cette action est irréversible.',icon:'fa-solid fa-server',confirmText:'Supprimer',confirmColor:'danger'}).then(ok=>{if(ok){f.onsubmit=null;f.submit();}})">
          @csrf
          <button type="submit" class="btn-del" style="width:100%;">
            <i class="fa-solid fa-trash"></i> Supprimer
          </button>
        </form>
      </div>
    </div>
    @endforeach
  </div>
@endif

</div>

{{-- ══ MODAL AJOUT ══ --}}
<div class="modal-bg" id="modal-add">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modal-add')">&times;</button>
    <h2><i class="fa-solid fa-plus" style="color:#2fa84f;"></i> Ajouter une salle</h2>
    <form method="POST" action="/salles/store">
      @csrf
      <div class="form-grid">
        <div class="code-info">
          <i class="fa-solid fa-tag"></i>
          Le code identifiant (SALLE-XXX) est <strong>généré automatiquement</strong> par le système.
        </div>
        <div class="form-group full">
          <label class="form-label">Nom de la salle *</label>
          <input class="form-control" type="text" name="nom" required placeholder="Ex: Salle Principale, DC-A1...">
        </div>
        <div class="form-group full">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" rows="2" placeholder="Description de la salle..."></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Localisation</label>
          <input class="form-control" type="text" name="localisation" placeholder="Bâtiment A, Étage 2...">
        </div>
        <div class="form-group">
          <label class="form-label">Capacité (nb serveurs)</label>
          <input class="form-control" type="number" name="capacite" min="0" placeholder="20">
        </div>
        <div class="form-group">
          <label class="form-label">Responsable</label>
          <input class="form-control" type="text" name="responsable" placeholder="Nom du responsable">
        </div>
        <div class="form-group">
          <label class="form-label">Statut</label>
          <select class="form-control" name="statut">
            <option value="actif">Actif</option>
            <option value="inactif">Inactif</option>
            <option value="maintenance">En maintenance</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Niveau de sécurité</label>
          <select class="form-control" name="niveau_securite">
            <option value="standard">Standard</option>
            <option value="eleve">Élevé</option>
            <option value="critique">Critique</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Statut réseau</label>
          <select class="form-control" name="statut_reseau">
            <option value="connecte">Connecté</option>
            <option value="deconnecte">Déconnecté</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('modal-add')">Annuler</button>
        <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Créer la salle</button>
      </div>
    </form>
  </div>
</div>

{{-- ══ MODAL ÉDITION ══ --}}
<div class="modal-bg" id="modal-edit">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modal-edit')">&times;</button>
    <h2><i class="fa-solid fa-pen" style="color:#2e86c1;"></i> Modifier la salle <span id="edit-code" style="color:#2fa84f;"></span></h2>
    <form method="POST" id="edit-form-salle" action="">
      @csrf
      <div class="form-grid">
        <div class="form-group full">
          <label class="form-label">Nom de la salle *</label>
          <input class="form-control" type="text" name="nom" id="se_nom" required>
        </div>
        <div class="form-group full">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" id="se_description" rows="2"></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Localisation</label>
          <input class="form-control" type="text" name="localisation" id="se_localisation">
        </div>
        <div class="form-group">
          <label class="form-label">Capacité (nb serveurs)</label>
          <input class="form-control" type="number" name="capacite" id="se_capacite" min="0">
        </div>
        <div class="form-group">
          <label class="form-label">Responsable</label>
          <input class="form-control" type="text" name="responsable" id="se_responsable">
        </div>
        <div class="form-group">
          <label class="form-label">Statut</label>
          <select class="form-control" name="statut" id="se_statut">
            <option value="actif">Actif</option>
            <option value="inactif">Inactif</option>
            <option value="maintenance">En maintenance</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Niveau de sécurité</label>
          <select class="form-control" name="niveau_securite" id="se_niveau_securite">
            <option value="standard">Standard</option>
            <option value="eleve">Élevé</option>
            <option value="critique">Critique</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Statut réseau</label>
          <select class="form-control" name="statut_reseau" id="se_statut_reseau">
            <option value="connecte">Connecté</option>
            <option value="deconnecte">Déconnecté</option>
          </select>
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

document.querySelectorAll('.modal-bg').forEach(bg => {
  bg.addEventListener('click', function(e){ if(e.target===this) closeModal(this.id); });
});

function setVal(id, val){ var el=document.getElementById(id); if(el) el.value = val ?? ''; }

function openEdit(s){
  document.getElementById('edit-form-salle').action = '/salles/update/' + s.id;
  document.getElementById('edit-code').textContent = s.code;
  setVal('se_nom', s.nom);
  setVal('se_description', s.description);
  setVal('se_localisation', s.localisation);
  setVal('se_capacite', s.capacite);
  setVal('se_responsable', s.responsable);
  setVal('se_statut', s.statut);
  setVal('se_niveau_securite', s.niveau_securite);
  setVal('se_statut_reseau', s.statut_reseau);
  openModal('modal-edit');
}
</script>

@endsection
