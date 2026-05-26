@extends('layouts.app')

@section('content')

<style>
@keyframes fadeIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.cam-wrap{animation:fadeIn .4s ease;}

.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;}
.page-title{font-size:20px;font-weight:bold;color:var(--text);display:flex;align-items:center;gap:10px;}
.page-title i{color:var(--accent);}
.btn-add{background:var(--accent);color:#060c1a;border:none;border-radius:9px;padding:10px 20px;font-weight:bold;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:.2s;}
.btn-add:hover{background:#249040;}

.flash{padding:12px 18px;border-radius:10px;margin-bottom:16px;font-weight:bold;font-size:14px;display:flex;align-items:center;gap:8px;}
.flash.success{background:rgba(47,168,79,.15);border:1px solid var(--accent);color:var(--accent);}
.flash.error{background:rgba(192,57,43,.15);border:1px solid var(--danger);color:var(--danger);}

/* ── Stats ── */
.kpi-row{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;}
.kpi-box{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:14px 20px;flex:1 1 120px;min-width:110px;}
.kpi-box .num{font-size:24px;font-weight:bold;}
.kpi-box .lbl{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-top:2px;}
.kpi-box.green .num{color:var(--accent);}
.kpi-box.red   .num{color:var(--danger);}
.kpi-box.blue  .num{color:var(--info);}

/* ── Camera cards grid ── */
.cam-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px;margin-bottom:20px;}
.cam-card{
  background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;
  transition:border-color .2s,box-shadow .2s;
}
.cam-card:hover{border-color:rgba(47,168,79,.4);box-shadow:0 4px 16px rgba(47,168,79,.08);}
.cam-card.inactive{opacity:.6;}

.cam-preview{
  height:160px;background:#030810;
  display:flex;align-items:center;justify-content:center;
  font-size:44px;color:rgba(47,168,79,.3);
  border-bottom:1px solid var(--border);position:relative;
}
.cam-preview.active-cam{color:var(--accent);}
.cam-live-badge{
  position:absolute;top:10px;left:10px;
  background:var(--danger);color:white;font-size:10px;font-weight:bold;
  padding:3px 8px;border-radius:4px;letter-spacing:1px;display:flex;align-items:center;gap:4px;
}
.cam-live-dot{width:6px;height:6px;border-radius:50%;background:white;animation:blink .8s infinite;}
.cam-status-badge{
  position:absolute;top:10px;right:10px;
  padding:3px 10px;border-radius:10px;font-size:11px;font-weight:bold;
}
.badge-active{background:rgba(47,168,79,.25);border:1px solid var(--accent);color:var(--accent);}
.badge-inactive{background:rgba(192,57,43,.2);border:1px solid var(--danger);color:var(--danger);}

.cam-body{padding:16px;}
.cam-name{font-size:15px;font-weight:bold;color:var(--text);margin-bottom:6px;}
.cam-ip{font-family:monospace;font-size:13px;color:var(--info);}
.cam-meta{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;}
.cam-tag{background:#091527;border:1px solid var(--border);border-radius:6px;padding:3px 9px;font-size:11px;color:var(--muted);}

.cam-actions{display:flex;gap:8px;padding:12px 16px;border-top:1px solid var(--border);background:rgba(0,0,0,.1);}
.btn-cam{border:none;border-radius:7px;padding:7px 14px;font-size:12px;font-weight:bold;cursor:pointer;transition:.2s;display:inline-flex;align-items:center;gap:5px;}
.btn-edit{background:rgba(46,134,193,.15);color:var(--info);border:1px solid var(--info);}
.btn-edit:hover{background:var(--info);color:white;}
.btn-toggle{background:rgba(217,119,6,.15);color:#d97706;border:1px solid #d97706;}
.btn-toggle:hover{background:#d97706;color:white;}
.btn-del{background:rgba(192,57,43,.15);color:var(--danger);border:1px solid var(--danger);margin-left:auto;}
.btn-del:hover{background:var(--danger);color:white;}

.empty-state{text-align:center;padding:60px;color:var(--muted);background:var(--card);border:1px solid var(--border);border-radius:16px;}
.empty-state .icon{font-size:48px;display:block;margin-bottom:14px;opacity:.3;}

/* ── Modal ── */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:1000;align-items:flex-start;justify-content:center;padding:20px;overflow-y:auto;}
.modal-bg.open{display:flex;}
.modal{background:#0d1a2e;border:1px solid var(--border);border-radius:16px;width:100%;max-width:520px;margin:auto;}
.modal-header{display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid var(--border);}
.modal-title{font-size:16px;font-weight:bold;color:var(--text);}
.modal-close{background:none;border:none;color:var(--muted);font-size:20px;cursor:pointer;line-height:1;}
.modal-close:hover{color:var(--text);}
.modal-body{padding:22px;}
.form-group{display:flex;flex-direction:column;gap:6px;margin-bottom:14px;}
.form-label{font-size:12px;font-weight:bold;color:var(--muted);letter-spacing:.5px;}
.form-input{background:#0a1525;border:1.5px solid var(--border);border-radius:8px;color:var(--text);padding:9px 12px;font-size:13px;outline:none;width:100%;transition:border-color .2s;}
.form-input:focus{border-color:var(--accent);}
.form-input option{background:#0a1525;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.btn-submit{background:var(--accent);color:#060c1a;border:none;border-radius:9px;padding:11px;font-weight:bold;font-size:14px;cursor:pointer;width:100%;margin-top:6px;transition:.2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.btn-submit:hover{background:#249040;}
.btn-submit:disabled{background:var(--border);color:var(--muted);cursor:not-allowed;}

#toast{position:fixed;bottom:24px;right:24px;z-index:9999;display:none;background:#0d1a2e;border:1px solid var(--border);border-radius:10px;padding:14px 20px;font-size:14px;font-weight:bold;max-width:320px;box-shadow:0 4px 20px rgba(0,0,0,.4);}
#toast.success{border-color:var(--accent);color:var(--accent);}
#toast.error{border-color:var(--danger);color:var(--danger);}
</style>

<!-- Toast -->
<div id="toast"></div>

<div class="cam-wrap">

<div class="page-header">
  <div class="page-title"><i class="fa-solid fa-camera"></i> Caméras IP</div>
  <button class="btn-add" onclick="openAdd()"><i class="fa-solid fa-plus"></i> Ajouter caméra</button>
</div>

<!-- KPI -->
<div class="kpi-row">
  <div class="kpi-box green"><div class="num" id="kpi-actives">—</div><div class="lbl">Actives</div></div>
  <div class="kpi-box red"><div class="num" id="kpi-inactives">—</div><div class="lbl">Inactives</div></div>
  <div class="kpi-box blue"><div class="num" id="kpi-total">—</div><div class="lbl">Total</div></div>
</div>

<!-- Grille caméras -->
<div id="cam-grid" class="cam-grid">
  <div class="empty-state"><span class="icon"><i class="fa-solid fa-camera-slash"></i></span>Chargement des caméras...</div>
</div>

</div>

<!-- Modal Ajouter/Modifier -->
<div class="modal-bg" id="modal-cam">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title" id="modal-title-text">Ajouter une caméra</span>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="cam-id">
      <div class="form-group">
        <label class="form-label">Nom de la caméra *</label>
        <input class="form-input" type="text" id="cam-nom" placeholder="Caméra Principale, Entrée Sud...">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Adresse IP *</label>
          <input class="form-input" type="text" id="cam-ip" placeholder="192.168.1.50">
        </div>
        <div class="form-group">
          <label class="form-label">Port</label>
          <input class="form-input" type="number" id="cam-port" value="554" placeholder="554">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Protocole</label>
          <select class="form-input" id="cam-proto">
            <option value="RTSP">RTSP</option>
            <option value="RTMP">RTMP</option>
            <option value="HTTP">HTTP</option>
            <option value="ONVIF">ONVIF</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Statut</label>
          <select class="form-input" id="cam-statut">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Localisation / Emplacement</label>
        <input class="form-input" type="text" id="cam-loc" placeholder="Salle Serveur 1, Couloir, Entrée...">
      </div>
      <div class="form-group">
        <label class="form-label">Utilisateur (optionnel)</label>
        <input class="form-input" type="text" id="cam-user" placeholder="admin">
      </div>
      <button class="btn-submit" id="btn-save-cam" onclick="saveCam()">
        <i class="fa-solid fa-floppy-disk"></i> Enregistrer
      </button>
    </div>
  </div>
</div>

<script>
let cameras = [];

function toast(msg, type='success') {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.className = type;
  el.style.display = 'block';
  clearTimeout(el._t);
  el._t = setTimeout(() => el.style.display='none', 3500);
}

async function chargerCameras() {
  try {
    const r = await fetch('/api/cameras');
    cameras = await r.json();
    renderCameras();
  } catch(e) {
    document.getElementById('cam-grid').innerHTML = '<div class="empty-state"><span class="icon"><i class="fa-solid fa-triangle-exclamation"></i></span>Impossible de charger les caméras</div>';
  }
}

function renderCameras() {
  const actives   = cameras.filter(c => c.statut === 'active').length;
  const inactives = cameras.filter(c => c.statut !== 'active').length;
  document.getElementById('kpi-actives').textContent   = actives;
  document.getElementById('kpi-inactives').textContent = inactives;
  document.getElementById('kpi-total').textContent     = cameras.length;

  const grid = document.getElementById('cam-grid');
  if (!cameras.length) {
    grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1"><span class="icon"><i class="fa-solid fa-camera-slash"></i></span>Aucune caméra configurée.<br><small>Cliquez sur "Ajouter caméra" pour commencer.</small></div>';
    return;
  }
  grid.innerHTML = cameras.map(c => {
    const isActive = c.statut === 'active';
    const liveBadge = isActive ? `<div class="cam-live-badge"><div class="cam-live-dot"></div>LIVE</div>` : '';
    const statusBadge = isActive
      ? '<span class="cam-status-badge badge-active">ACTIVE</span>'
      : '<span class="cam-status-badge badge-inactive">INACTIVE</span>';
    const iconClass = isActive ? 'active-cam' : '';
    return `<div class="cam-card ${isActive?'':'inactive'}" id="cam-card-${c.id}">
      <div class="cam-preview ${iconClass}">
        ${liveBadge}
        <i class="fa-solid fa-camera"></i>
        ${statusBadge}
      </div>
      <div class="cam-body">
        <div class="cam-name">${esc(c.nom)}</div>
        <div class="cam-ip">${esc(c.ip)}:${c.port||554}</div>
        <div class="cam-meta">
          <span class="cam-tag"><i class="fa-solid fa-tower-broadcast"></i> ${esc(c.protocole||'RTSP')}</span>
          ${c.localisation ? `<span class="cam-tag"><i class="fa-solid fa-location-dot"></i> ${esc(c.localisation)}</span>` : ''}
          ${c.username ? `<span class="cam-tag"><i class="fa-solid fa-user"></i> ${esc(c.username)}</span>` : ''}
        </div>
      </div>
      <div class="cam-actions">
        <button class="btn-cam btn-edit" onclick="openEdit(${c.id})"><i class="fa-solid fa-pen"></i> Modifier</button>
        <button class="btn-cam btn-toggle" onclick="toggleCam(${c.id})">${isActive ? '<i class="fa-solid fa-circle-pause"></i> Désactiver' : '<i class="fa-solid fa-circle-play"></i> Activer'}</button>
        <button class="btn-cam btn-del" onclick="deleteCam(${c.id})"><i class="fa-solid fa-trash"></i></button>
      </div>
    </div>`;
  }).join('');
}

function esc(s) { return String(s||'').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function openAdd() {
  document.getElementById('modal-title-text').textContent = 'Ajouter une caméra';
  document.getElementById('cam-id').value = '';
  document.getElementById('cam-nom').value = '';
  document.getElementById('cam-ip').value = '';
  document.getElementById('cam-port').value = '554';
  document.getElementById('cam-proto').value = 'RTSP';
  document.getElementById('cam-statut').value = 'active';
  document.getElementById('cam-loc').value = '';
  document.getElementById('cam-user').value = '';
  document.getElementById('modal-cam').classList.add('open');
}

function openEdit(id) {
  const c = cameras.find(x => x.id == id);
  if (!c) return;
  document.getElementById('modal-title-text').textContent = 'Modifier la caméra';
  document.getElementById('cam-id').value = c.id;
  document.getElementById('cam-nom').value = c.nom || '';
  document.getElementById('cam-ip').value = c.ip || '';
  document.getElementById('cam-port').value = c.port || 554;
  document.getElementById('cam-proto').value = c.protocole || 'RTSP';
  document.getElementById('cam-statut').value = c.statut || 'active';
  document.getElementById('cam-loc').value = c.localisation || '';
  document.getElementById('cam-user').value = c.username || '';
  document.getElementById('modal-cam').classList.add('open');
}

function closeModal() { document.getElementById('modal-cam').classList.remove('open'); }

async function saveCam() {
  const id  = document.getElementById('cam-id').value;
  const nom = document.getElementById('cam-nom').value.trim();
  const ip  = document.getElementById('cam-ip').value.trim();
  if (!nom || !ip) { toast('Nom et IP sont requis.', 'error'); return; }

  const btn = document.getElementById('btn-save-cam');
  btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement...';

  const body = {
    nom, ip,
    port:        document.getElementById('cam-port').value,
    protocole:   document.getElementById('cam-proto').value,
    statut:      document.getElementById('cam-statut').value,
    localisation:document.getElementById('cam-loc').value,
    username:    document.getElementById('cam-user').value,
  };

  try {
    const url = id ? `/api/cameras/${id}/update` : '/api/cameras/store';
    const r = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(body),
    });
    const d = await r.json();
    if (d.success) {
      toast(d.message || 'Opération réussie.', 'success');
      closeModal();
      chargerCameras();
    } else {
      toast(d.message || 'Erreur.', 'error');
    }
  } catch(e) {
    toast('Erreur de connexion.', 'error');
  }
  btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Enregistrer';
}

async function toggleCam(id) {
  try {
    const r = await fetch(`/api/cameras/${id}/toggle`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const d = await r.json();
    if (d.success) {
      toast(`Caméra ${d.statut === 'active' ? 'activée' : 'désactivée'}.`, 'success');
      chargerCameras();
    }
  } catch(e) { toast('Erreur.', 'error'); }
}

async function deleteCam(id) {
  const ok = await CyberConfirm.show({title:'Supprimer la caméra',message:'Supprimer définitivement cette caméra ? Cette action est irréversible.',icon:'fa-solid fa-camera',confirmText:'Supprimer',confirmColor:'danger'});
  if (!ok) return;
  try {
    const r = await fetch(`/api/cameras/${id}/delete`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const d = await r.json();
    if (d.success) {
      toast('Caméra supprimée.', 'success');
      chargerCameras();
    }
  } catch(e) { toast('Erreur.', 'error'); }
}

document.getElementById('modal-cam').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

chargerCameras();
</script>

@endsection
