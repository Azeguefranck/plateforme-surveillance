@extends('layouts.app')

@section('content')
<style>
:root{--neon:#33ff88;--blue:#33b5ff;--warn:#ffd633;--danger:#ff5733;--bg:#060d1f;--card:#0e1a38;--border:#1e2f5a;}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-title{font-size:22px;font-weight:700;color:var(--neon)}
.pg-title span{color:#fff;font-size:14px;font-weight:400;margin-left:8px;opacity:.7}
.btn{padding:10px 20px;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px;transition:.2s}
.btn-neon{background:transparent;border:1px solid var(--neon);color:var(--neon)}
.btn-neon:hover{background:var(--neon);color:#000}
.btn-danger{background:transparent;border:1px solid var(--danger);color:var(--danger)}
.btn-danger:hover{background:var(--danger);color:#fff}
.btn-blue{background:transparent;border:1px solid var(--blue);color:var(--blue)}
.btn-blue:hover{background:var(--blue);color:#000}

.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px}
.stat-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:18px 20px;text-align:center}
.stat-card .val{font-size:34px;font-weight:800;margin-bottom:4px}
.stat-card .lbl{font-size:12px;color:#aaa;text-transform:uppercase;letter-spacing:.5px}
.stat-card.green .val{color:var(--neon)}
.stat-card.blue  .val{color:var(--blue)}
.stat-card.warn  .val{color:var(--warn)}
.stat-card.red   .val{color:var(--danger)}

.alert{padding:12px 16px;border-radius:8px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:8px}
.alert-success{background:rgba(51,255,136,.1);border:1px solid var(--neon);color:var(--neon)}

.form-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:24px;margin-bottom:28px}
.form-card h3{font-size:15px;font-weight:700;color:var(--blue);margin-bottom:18px}
.form-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.form-group{display:flex;flex-direction:column;gap:6px}
.form-group label{font-size:12px;color:#aaa;font-weight:600;text-transform:uppercase;letter-spacing:.4px}
.form-group input,.form-group select,.form-group textarea{background:#07102a;border:1px solid var(--border);border-radius:8px;padding:10px 12px;color:#fff;font-size:13px;outline:none;transition:.2s}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--neon);box-shadow:0 0 0 2px rgba(51,255,136,.1)}
.form-group textarea{resize:vertical;min-height:72px}
.form-group select option{background:#0e1a38}
.form-actions{display:flex;gap:10px;margin-top:18px;justify-content:flex-end}
.full-span{grid-column:1/-1}

.rooms-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.rooms-title{font-size:16px;font-weight:700;color:#fff}
.rooms-count{font-size:12px;color:#aaa}
.rooms-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
.room-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;transition:.2s;position:relative}
.room-card:hover{border-color:var(--blue);box-shadow:0 4px 20px rgba(51,181,255,.08)}
.room-card-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px}
.room-name{font-size:16px;font-weight:700;color:#fff}
.room-code{font-size:11px;color:var(--blue);font-family:monospace;margin-top:2px}
.badge{display:inline-block;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase}
.badge-active{background:rgba(51,255,136,.12);color:var(--neon);border:1px solid rgba(51,255,136,.3)}
.badge-inactive{background:rgba(255,87,51,.12);color:var(--danger);border:1px solid rgba(255,87,51,.3)}
.badge-maintenance{background:rgba(255,214,51,.12);color:var(--warn);border:1px solid rgba(255,214,51,.3)}
.room-info{display:flex;flex-direction:column;gap:6px;margin-bottom:14px}
.room-info-row{display:flex;gap:8px;align-items:center;font-size:12px;color:#aaa}
.room-info-row span:first-child{color:#666;width:90px;flex-shrink:0}
.room-info-row span:last-child{color:#ccc}
.room-capacity{display:flex;align-items:center;gap:8px;padding-top:10px;border-top:1px solid var(--border)}
.cap-label{font-size:11px;color:#666}
.cap-val{font-size:20px;font-weight:800;color:var(--blue)}
.cap-unit{font-size:11px;color:#aaa}
.room-actions{display:flex;gap:8px;margin-top:14px}
.room-actions .btn{padding:7px 14px;font-size:12px}
.empty-state{text-align:center;padding:60px 20px;color:#555}
.empty-state .icon{font-size:48px;margin-bottom:12px}
.empty-state p{font-size:14px}

.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:1000;align-items:center;justify-content:center}
.modal-overlay.open{display:flex}
.modal{background:#0b1632;border:1px solid var(--border);border-radius:16px;padding:28px;width:min(600px,95vw);max-height:90vh;overflow-y:auto}
.modal h3{font-size:16px;font-weight:700;color:var(--blue);margin-bottom:20px}
.modal-close{float:right;background:none;border:none;color:#aaa;font-size:20px;cursor:pointer;margin-top:-4px}
.modal-close:hover{color:#fff}

@media(max-width:768px){
.stats-row{grid-template-columns:repeat(2,1fr)}
.form-grid{grid-template-columns:1fr}
.rooms-grid{grid-template-columns:1fr}
}
</style>

<div class="pg-header">
    <div>
        <div class="pg-title">Salles Serveurs <span>Gestion des salles</span></div>
    </div>
    <button class="btn btn-neon" onclick="document.getElementById('addModal').classList.add('open')">+ Nouvelle Salle</button>
</div>

@if(session('success_salle'))
<div class="alert alert-success">&#10003; {{ session('success_salle') }}</div>
@endif

<!-- Stats -->
<div class="stats-row">
    <div class="stat-card blue"><div class="val">{{ $stats['total'] }}</div><div class="lbl">Total salles</div></div>
    <div class="stat-card green"><div class="val">{{ $stats['actives'] }}</div><div class="lbl">Actives</div></div>
    <div class="stat-card red"><div class="val">{{ $stats['inactives'] }}</div><div class="lbl">Inactives</div></div>
    <div class="stat-card warn"><div class="val">{{ $stats['maintenance'] }}</div><div class="lbl">Maintenance</div></div>
</div>

@if($derniereMesure)
<div class="form-card" style="margin-bottom:20px;padding:16px 24px">
    <div style="display:flex;gap:32px;flex-wrap:wrap;align-items:center">
        <div style="font-size:12px;color:#aaa">Dernière mesure capteurs</div>
        @if($derniereMesure->temperature ?? null)
        <div><span style="color:#666;font-size:11px">TEMP</span> <span style="color:var(--neon);font-weight:700">{{ $derniereMesure->temperature }}°C</span></div>
        @endif
        @if($derniereMesure->humidite ?? null)
        <div><span style="color:#666;font-size:11px">HUM</span> <span style="color:var(--blue);font-weight:700">{{ $derniereMesure->humidite }}%</span></div>
        @endif
        @if($derniereMesure->gaz ?? null)
        <div><span style="color:#666;font-size:11px">GAZ</span> <span style="color:var(--warn);font-weight:700">{{ $derniereMesure->gaz }} ppm</span></div>
        @endif
        <div style="margin-left:auto;font-size:11px;color:#555">{{ \Carbon\Carbon::parse($derniereMesure->created_at)->diffForHumans() }}</div>
    </div>
</div>
@endif

<!-- Rooms grid -->
<div class="rooms-header">
    <div class="rooms-title">Liste des salles</div>
    <div class="rooms-count">{{ count($salles) }} salle(s)</div>
</div>

@if(count($salles) === 0)
<div class="empty-state">
    <div class="icon">&#127970;</div>
    <p>Aucune salle enregistrée.<br>Cliquez sur <strong>+ Nouvelle Salle</strong> pour commencer.</p>
</div>
@else
<div class="rooms-grid">
@foreach($salles as $salle)
<div class="room-card">
    <div class="room-card-header">
        <div>
            <div class="room-name">{{ $salle->nom }}</div>
            @if($salle->code)<div class="room-code">{{ $salle->code }}</div>@endif
        </div>
        <span class="badge badge-{{ $salle->statut }}">{{ $salle->statut }}</span>
    </div>
    <div class="room-info">
        @if($salle->localisation)
        <div class="room-info-row"><span>Localisation</span><span>{{ $salle->localisation }}</span></div>
        @endif
        @if($salle->responsable)
        <div class="room-info-row"><span>Responsable</span><span>{{ $salle->responsable }}</span></div>
        @endif
        @if($salle->description)
        <div class="room-info-row"><span>Description</span><span>{{ Str::limit($salle->description, 60) }}</span></div>
        @endif
    </div>
    <div class="room-capacity">
        <span class="cap-label">Capacité</span>
        <span class="cap-val">{{ $salle->capacite_serveurs }}</span>
        <span class="cap-unit">serveurs max</span>
    </div>
    <div class="room-actions">
        <button class="btn btn-blue" onclick="openEdit({{ $salle->id }}, '{{ addslashes($salle->nom) }}', '{{ addslashes($salle->code ?? '') }}', '{{ addslashes($salle->localisation ?? '') }}', '{{ addslashes($salle->responsable ?? '') }}', {{ $salle->capacite_serveurs }}, '{{ $salle->statut }}', '{{ addslashes($salle->description ?? '') }}')">Modifier</button>
        <form method="POST" action="/salles/{{ $salle->id }}" id="del-salle-{{ $salle->id }}" style="margin:0">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-danger" onclick="delSalle(this,{{ $salle->id }})">Supprimer</button>
        </form>
    </div>
</div>
@endforeach
</div>
@endif

<!-- Add Modal -->
<div class="modal-overlay" id="addModal" onclick="if(event.target===this)this.classList.remove('open')">
<div class="modal">
    <h3>&#10010; Nouvelle Salle <button class="modal-close" onclick="document.getElementById('addModal').classList.remove('open')">&#215;</button></h3>
    <form method="POST" action="/salles">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label>Nom *</label>
                <input type="text" name="nom" required placeholder="Salle principale">
            </div>
            <div class="form-group">
                <label>Code</label>
                <input type="text" name="code" placeholder="SRV-001" maxlength="30">
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select name="statut">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="form-group">
                <label>Localisation</label>
                <input type="text" name="localisation" placeholder="Bâtiment A, Niveau 2">
            </div>
            <div class="form-group">
                <label>Responsable</label>
                <input type="text" name="responsable" placeholder="Nom du responsable">
            </div>
            <div class="form-group">
                <label>Capacité serveurs</label>
                <input type="number" name="capacite_serveurs" min="0" value="0">
            </div>
            <div class="form-group full-span">
                <label>Description</label>
                <textarea name="description" placeholder="Description de la salle..."></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" class="btn btn-danger" onclick="document.getElementById('addModal').classList.remove('open')">Annuler</button>
            <button type="submit" class="btn btn-neon">Enregistrer</button>
        </div>
    </form>
</div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal" onclick="if(event.target===this)this.classList.remove('open')">
<div class="modal">
    <h3>&#9998; Modifier la Salle <button class="modal-close" onclick="document.getElementById('editModal').classList.remove('open')">&#215;</button></h3>
    <form method="POST" id="editForm" action="#">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label>Nom *</label>
                <input type="text" name="nom" id="edit_nom" required>
            </div>
            <div class="form-group">
                <label>Code</label>
                <input type="text" name="code" id="edit_code" maxlength="30">
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select name="statut" id="edit_statut">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="form-group">
                <label>Localisation</label>
                <input type="text" name="localisation" id="edit_localisation">
            </div>
            <div class="form-group">
                <label>Responsable</label>
                <input type="text" name="responsable" id="edit_responsable">
            </div>
            <div class="form-group">
                <label>Capacité serveurs</label>
                <input type="number" name="capacite_serveurs" id="edit_capacite" min="0">
            </div>
            <div class="form-group full-span">
                <label>Description</label>
                <textarea name="description" id="edit_description"></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" class="btn btn-danger" onclick="document.getElementById('editModal').classList.remove('open')">Annuler</button>
            <button type="submit" class="btn btn-neon">Mettre à jour</button>
        </div>
    </form>
</div>
</div>

<script>
function delSalle(btn, id) {
    confirmDlg('Supprimer cette salle ?','La salle serveurs sera définitivement supprimée. Toutes ses données associées seront perdues. Cette action est irréversible.',{type:'danger',icon:'🏢',confirmText:'Supprimer la salle'}).then(function(ok) {
        if (ok) { btnLoad(btn); document.getElementById('del-salle-'+id).submit(); }
    });
}

function openEdit(id, nom, code, localisation, responsable, capacite, statut, description) {
    document.getElementById('editForm').action = '/salles/' + id;
    document.getElementById('edit_nom').value = nom;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_localisation').value = localisation;
    document.getElementById('edit_responsable').value = responsable;
    document.getElementById('edit_capacite').value = capacite;
    document.getElementById('edit_description').value = description;
    const sel = document.getElementById('edit_statut');
    for (let i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value === statut) { sel.selectedIndex = i; break; }
    }
    document.getElementById('editModal').classList.add('open');
}
</script>
@endsection
