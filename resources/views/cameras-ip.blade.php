<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Caméras IP — Surveillance</title>
<link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;user-select:none;-webkit-user-select:none}
input,select,textarea{user-select:text;-webkit-user-select:text;cursor:auto}

body{background:#050816;color:#fff;min-height:100vh;padding:0}

.topbar{
  display:flex;align-items:center;justify-content:space-between;
  background:#081126;padding:14px 20px;
  border-bottom:1px solid #1e2f5a;
  flex-wrap:wrap;gap:10px;
}
.topbar-left{display:flex;align-items:center;gap:14px}
.btn-back{
  display:flex;align-items:center;gap:7px;
  background:#111c3d;border:1px solid #1e2f5a;
  border-radius:9px;padding:9px 14px;
  color:#8899cc;text-decoration:none;font-size:13px;font-weight:600;
  transition:.2s;
}
.btn-back:hover{background:#1f2d5e;color:#fff;border-color:#33b5ff}
.page-title{
  font-size:18px;font-weight:900;color:#fff;
  display:flex;align-items:center;gap:9px;letter-spacing:.5px;
}
.page-title i{color:#33b5ff}
.btn-add{
  display:flex;align-items:center;gap:7px;
  background:rgba(51,181,255,.12);border:1px solid rgba(51,181,255,.4);
  border-radius:9px;padding:9px 16px;
  color:#33b5ff;font-size:13px;font-weight:700;cursor:pointer;transition:.2s;
}
.btn-add:hover{background:rgba(51,181,255,.22);box-shadow:0 0 16px rgba(51,181,255,.25)}
.btn-check-all{
  display:flex;align-items:center;gap:7px;
  background:rgba(57,255,20,.08);border:1px solid rgba(57,255,20,.25);
  border-radius:9px;padding:8px 14px;
  color:#39ff14;font-size:12px;font-weight:700;cursor:pointer;transition:.2s;
}
.btn-check-all:hover{background:rgba(57,255,20,.15)}

.page-body{padding:20px;max-width:1400px}

.stats{
  display:grid;grid-template-columns:repeat(4,1fr);gap:12px;
  margin-bottom:18px;
}
@media(max-width:700px){.stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:400px){.stats{grid-template-columns:1fr 1fr}}

.stat-card{
  background:#0e1a38;border:1px solid #1e2f5a;border-radius:12px;
  padding:14px 16px;display:flex;align-items:center;gap:12px;
}
.stat-card i{font-size:22px;opacity:.7}
.stat-card .val{font-size:24px;font-weight:900;line-height:1}
.stat-card .lbl{font-size:11px;color:#5a6a99;text-transform:uppercase;letter-spacing:.5px;margin-top:3px}
.stat-total i{color:#33b5ff}
.stat-active i{color:#33ff88}
.stat-offline i{color:#ff5733}
.stat-pending i{color:#ffd633}

.action-bar{display:flex;align-items:center;gap:10px;margin-bottom:18px;flex-wrap:wrap}

.cameras-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
  gap:16px;
}

.empty-state{
  grid-column:1/-1;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:60px 20px;background:#0e1a38;border:1px dashed #1e2f5a;border-radius:16px;
  gap:12px;color:#3a4a6a;
}
.empty-state i{font-size:48px;opacity:.4}
.empty-state p{font-size:15px;font-weight:700}
.empty-state small{font-size:12px;opacity:.7;text-align:center}

.cam-card{
  background:#0e1a38;border:1px solid #1e2f5a;border-radius:14px;
  overflow:hidden;transition:.2s;
}
.cam-card:hover{border-color:#33b5ff;box-shadow:0 4px 24px rgba(51,181,255,.1)}

.cam-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:12px 14px 0;
}
.cam-icon-wrap{
  width:44px;height:44px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;
  font-size:20px;flex-shrink:0;
}
.icon-active{background:rgba(51,255,136,.1);color:#33ff88;border:1px solid rgba(51,255,136,.25)}
.icon-offline{background:rgba(255,87,51,.1);color:#ff5733;border:1px solid rgba(255,87,51,.25)}
.icon-pending{background:rgba(255,214,51,.08);color:#ffd633;border:1px solid rgba(255,214,51,.2)}

.cam-status-wrap{display:flex;flex-direction:column;align-items:flex-end;gap:6px}
.cam-status{
  font-size:10px;font-weight:800;letter-spacing:.8px;
  padding:3px 8px;border-radius:20px;text-transform:uppercase;
}
.status-active{background:rgba(51,255,136,.12);color:#33ff88;border:1px solid rgba(51,255,136,.3)}
.status-offline{background:rgba(255,87,51,.12);color:#ff5733;border:1px solid rgba(255,87,51,.3)}
.status-pending{background:rgba(255,214,51,.1);color:#ffd633;border:1px solid rgba(255,214,51,.25)}

.signal-bars{display:flex;align-items:flex-end;gap:2px;height:16px}
.sig-bar{width:4px;background:#1e2f5a;border-radius:1px;transition:.3s}
.sig-bar.sig-on{background:#33ff88}

.cam-stream{
  height:160px;background:#040d1e;
  display:flex;align-items:center;justify-content:center;
  margin:10px 14px;border-radius:8px;overflow:hidden;
  border:1px solid #0e1a38;position:relative;
}
.cam-stream img{width:100%;height:100%;object-fit:cover}
.stream-placeholder{
  display:flex;flex-direction:column;align-items:center;gap:8px;
  color:#1e2f5a;
}
.stream-placeholder i{font-size:32px}
.stream-placeholder span{font-size:11px;letter-spacing:.5px}

.cam-info{padding:0 14px 10px}
.cam-name{font-size:14px;font-weight:800;color:#fff;margin-bottom:6px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.cam-type,.cam-ip,.cam-salle,.cam-ping{
  font-size:11px;color:#5a6a99;display:flex;align-items:center;gap:6px;
  margin-bottom:3px;
}
.cam-type i{color:#33b5ff;width:12px;text-align:center}
.cam-ip i{color:#33b5ff;width:12px;text-align:center}
.cam-salle i{color:#33b5ff;width:12px;text-align:center}
.cam-ping i{color:#33ff88;width:12px;text-align:center}
.cam-ping{color:#33ff88}

.cam-actions{
  display:flex;gap:6px;padding:8px 14px 12px;
  border-top:1px solid #0e1a38;
}
.cam-actions button{
  flex:1;padding:7px 4px;border-radius:7px;font-size:11px;font-weight:600;
  cursor:pointer;transition:.2s;border:1px solid transparent;
  display:flex;align-items:center;justify-content:center;gap:4px;
  background:#111c3d;color:#8899cc;border-color:#1e2f5a;
}
.cam-actions button:hover{background:#1f2d5e;color:#fff;border-color:#33b5ff}
.cam-actions button.btn-delete:hover{background:rgba(255,87,51,.12);color:#ff5733;border-color:rgba(255,87,51,.35)}
.cam-actions button i{font-size:12px}
.cam-actions button.btn-ping-busy{opacity:.6;cursor:wait}

.modal-overlay{
  display:none;position:fixed;inset:0;
  background:rgba(2,5,18,.88);backdrop-filter:blur(10px);
  -webkit-backdrop-filter:blur(10px);z-index:1000;
  align-items:center;justify-content:center;
  padding:20px;
}
.modal-overlay.open{display:flex}

.modal{
  background:#0a1225;border:1px solid #1e2f5a;border-radius:18px;
  width:100%;max-width:520px;max-height:90vh;overflow-y:auto;
  animation:modalIn .3s cubic-bezier(.21,1.02,.73,1);
}
@keyframes modalIn{from{opacity:0;transform:scale(.88) translateY(16px)}to{opacity:1;transform:none}}

.modal-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:18px 22px 14px;border-bottom:1px solid #1e2f5a;
}
.modal-header h2{font-size:16px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px}
.modal-header h2 i{color:#33b5ff}
.modal-close{
  background:none;border:none;color:#5a6a99;font-size:18px;
  cursor:pointer;padding:4px;transition:.2s;
}
.modal-close:hover{color:#ff5733}

.modal-body{padding:18px 22px}

.form-row{display:flex;gap:12px}
.form-row .field{flex:1}
.form-row .field-sm{flex:0 0 90px}

.field{margin-bottom:14px}
.field label{
  display:block;font-size:11px;font-weight:700;letter-spacing:.6px;
  color:#5a6a99;text-transform:uppercase;margin-bottom:5px;
}
.field input,.field select{
  width:100%;padding:10px 13px;
  background:rgba(255,255,255,.04);border:1px solid #1e2f5a;border-radius:8px;
  color:#e0e8ff;font-size:14px;outline:none;transition:.2s;
}
.field input:focus,.field select:focus{border-color:#33b5ff;box-shadow:0 0 0 3px rgba(51,181,255,.08)}
.field input::placeholder{color:#3a4a6a}
.field select option{background:#0e1a38;color:#e0e8ff}

.modal-footer{
  display:flex;gap:10px;padding:14px 22px 20px;
  border-top:1px solid #0e1a38;
}
.btn-cancel{
  flex:1;padding:11px;border-radius:9px;font-weight:700;font-size:13px;cursor:pointer;
  background:#111c3d;border:1px solid #1e2f5a;color:#8899cc;transition:.2s;
}
.btn-cancel:hover{background:#1f2d5e;color:#fff}
.btn-save{
  flex:2;padding:11px;border-radius:9px;font-weight:700;font-size:13px;cursor:pointer;
  background:rgba(51,181,255,.12);border:1px solid rgba(51,181,255,.4);color:#33b5ff;
  transition:.2s;display:flex;align-items:center;justify-content:center;gap:7px;
}
.btn-save:hover{background:rgba(51,181,255,.22);box-shadow:0 0 16px rgba(51,181,255,.25)}

.confirm-overlay{
  display:none;position:fixed;inset:0;
  background:rgba(2,5,18,.88);backdrop-filter:blur(10px);
  -webkit-backdrop-filter:blur(10px);z-index:1100;
  align-items:center;justify-content:center;padding:20px;
}
.confirm-overlay.open{display:flex}
.confirm-box{
  background:#0a1225;border:1px solid rgba(255,87,51,.3);border-radius:16px;
  padding:28px;max-width:380px;width:100%;text-align:center;
  animation:modalIn .28s cubic-bezier(.21,1.02,.73,1);
}
.confirm-icon{font-size:38px;margin-bottom:12px}
.confirm-title{font-size:15px;font-weight:800;color:#fff;margin-bottom:8px}
.confirm-msg{font-size:13px;color:#8899bb;line-height:1.6;margin-bottom:20px}
.confirm-btns{display:flex;gap:10px}
.confirm-btns button{flex:1;padding:11px;border-radius:9px;font-weight:700;font-size:13px;cursor:pointer;transition:.2s}
.confirm-no{background:#111c3d;border:1px solid #1e2f5a;color:#8899cc}
.confirm-no:hover{background:#1f2d5e;color:#fff}
.confirm-yes{background:rgba(255,87,51,.1);border:1px solid rgba(255,87,51,.35);color:#ff5733}
.confirm-yes:hover{background:#ff5733;color:#fff}

@keyframes tIn{from{transform:translateX(110%);opacity:0}to{transform:none;opacity:1}}
@keyframes tOut{from{opacity:1}to{opacity:0;transform:translateX(110%)}}
#toasts{position:fixed;top:16px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:7px;pointer-events:none}
.toast{
  pointer-events:auto;min-width:220px;max-width:340px;
  padding:10px 14px;border-radius:9px;font-size:12px;font-weight:600;
  display:flex;align-items:center;gap:8px;
  animation:tIn .28s cubic-bezier(.21,1.02,.73,1) forwards;cursor:pointer;line-height:1.4;
}
.toast.out{animation:tOut .2s ease forwards}
.toast.s{background:rgba(6,20,14,.95);border:1px solid #33ff88;color:#33ff88}
.toast.e{background:rgba(22,6,6,.95);border:1px solid #ff5733;color:#ff5733}
.toast.w{background:rgba(22,18,6,.95);border:1px solid #ffd633;color:#ffd633}
.toast.i{background:rgba(6,14,22,.95);border:1px solid #33b5ff;color:#33b5ff}

@media(max-width:600px){
  .topbar{padding:12px 14px}
  .page-title{font-size:15px}
  .page-body{padding:14px}
  .cameras-grid{grid-template-columns:1fr}
  .form-row{flex-direction:column;gap:0}
  .form-row .field-sm{flex:1}
}

*{overflow-wrap:break-word;word-break:break-word}
img,video,iframe{max-width:100%;height:auto}
@media(max-width:640px){
body{overflow-x:hidden}
input,select,textarea{max-width:100%!important;width:100%!important}
table{display:block;overflow-x:auto;-webkit-overflow-scrolling:touch}
[class*="card"],[class*="modal"],[class*="box"]{max-width:100%!important}
}
@media(max-width:400px){
h1,h2,.page-title{font-size:clamp(14px,4vw,20px)!important}
}
</style>
</head>
<body>

<div class="topbar">
  <div class="topbar-left">
    <a href="/dashboard" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
    <div class="page-title"><i class="fa-solid fa-video"></i> Caméras IP</div>
  </div>
  <button class="btn-add" onclick="openAddModal()">
    <i class="fa-solid fa-plus"></i> Ajouter une caméra
  </button>
</div>

<div class="page-body">

  <div class="stats">
    <div class="stat-card stat-total">
      <i class="fa-solid fa-video"></i>
      <div>
        <div class="val" id="stat-total">0</div>
        <div class="lbl">Total</div>
      </div>
    </div>
    <div class="stat-card stat-active">
      <i class="fa-solid fa-circle-check"></i>
      <div>
        <div class="val" id="stat-active">0</div>
        <div class="lbl">Actives</div>
      </div>
    </div>
    <div class="stat-card stat-offline">
      <i class="fa-solid fa-circle-xmark"></i>
      <div>
        <div class="val" id="stat-offline">0</div>
        <div class="lbl">Hors ligne</div>
      </div>
    </div>
    <div class="stat-card stat-pending">
      <i class="fa-solid fa-circle-dot"></i>
      <div>
        <div class="val" id="stat-pending">0</div>
        <div class="lbl">En attente</div>
      </div>
    </div>
  </div>

  <div class="action-bar">
    <button class="btn-check-all" onclick="pingAll()">
      <i class="fa-solid fa-satellite-dish"></i> Vérifier toutes les caméras
    </button>
  </div>

  <div class="cameras-grid" id="camerasGrid">
    <div class="empty-state" id="emptyState" style="display:none">
      <i class="fa-solid fa-video-slash"></i>
      <p>Aucune caméra configurée</p>
      <small>Cliquez sur "Ajouter une caméra" pour commencer</small>
    </div>
  </div>

</div>

<div class="modal-overlay" id="camModal">
  <div class="modal">
    <div class="modal-header">
      <h2><i class="fa-solid fa-video"></i> <span id="modal-title">Ajouter une caméra</span></h2>
      <button class="modal-close" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="cam-id">
      <div class="form-row">
        <div class="field">
          <label>Nom de la caméra *</label>
          <input type="text" id="cam-nom" placeholder="Ex: Hall d'entrée">
        </div>
        <div class="field">
          <label>Type de caméra</label>
          <select id="cam-type">
            <option value="ip">IP Standard</option>
            <option value="ptz">PTZ (Pan-Tilt-Zoom)</option>
            <option value="fisheye">Fisheye 360°</option>
            <option value="thermal">Thermique</option>
            <option value="pir">PIR (Détection mouvement)</option>
            <option value="ir">Infrarouge / Nocturne</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="field">
          <label>Adresse IP *</label>
          <input type="text" id="cam-ip" placeholder="192.168.1.100">
        </div>
        <div class="field field-sm">
          <label>Port</label>
          <input type="number" id="cam-port" placeholder="80" value="80" min="1" max="65535">
        </div>
      </div>
      <div class="form-row">
        <div class="field">
          <label>Login</label>
          <input type="text" id="cam-login" placeholder="admin" autocomplete="username">
        </div>
        <div class="field">
          <label>Mot de passe</label>
          <input type="password" id="cam-pass" placeholder="••••••••" autocomplete="current-password">
        </div>
      </div>
      <div class="field">
        <label>URL du flux vidéo (optionnel)</label>
        <input type="text" id="cam-url" placeholder="rtsp://192.168.1.100:554/stream ou http://...">
      </div>
      <div class="field">
        <label>Salle / Emplacement</label>
        <input type="text" id="cam-salle" placeholder="Salle serveur principale">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Annuler</button>
      <button class="btn-save" onclick="saveCamera()">
        <i class="fa-solid fa-floppy-disk"></i> Enregistrer
      </button>
    </div>
  </div>
</div>

<div class="confirm-overlay" id="confirmDlg">
  <div class="confirm-box">
    <div class="confirm-icon"><i class="fa-solid fa-trash"></i></div>
    <div class="confirm-title">Supprimer cette caméra ?</div>
    <div class="confirm-msg">Cette action est irréversible. La configuration de la caméra sera définitivement supprimée.</div>
    <div class="confirm-btns">
      <button class="confirm-no" onclick="closeConfirm()">Annuler</button>
      <button class="confirm-yes" id="confirmYesBtn" onclick="doDelete()">Supprimer</button>
    </div>
  </div>
</div>

<div id="toasts"></div>

<script>
const STORAGE_KEY = 'plateforme_cameras_v2';
let _deletePendingId = null;
let _editingId = null;

function getCameras() {
  try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }
  catch(e) { return []; }
}
function saveCameras(cams) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(cams));
}

const TYPE_ICONS = {
  ip:'fa-video', ptz:'fa-camera-rotate', fisheye:'fa-circle',
  thermal:'fa-fire', pir:'fa-person-rays', ir:'fa-moon'
};
const TYPE_LABELS = {
  ip:'IP Standard', ptz:'PTZ Pan-Tilt-Zoom', fisheye:'Fisheye 360°',
  thermal:'Thermique', pir:'PIR Mouvement', ir:'Infrarouge'
};

function toast(msg, type, dur) {
  type = type||'s'; dur = dur||3500;
  const icons = {s:'<i class="fa-solid fa-check"></i>', e:'<i class="fa-solid fa-xmark"></i>', w:'<i class="fa-solid fa-triangle-exclamation"></i>', i:'<i class="fa-solid fa-circle-info"></i>'};
  const t = document.createElement('div');
  t.className = 'toast ' + type;
  t.innerHTML = '<span>'+icons[type]+'</span><span style="flex:1">'+msg+'</span>';
  t.onclick = () => dismissToast(t);
  document.getElementById('toasts').prepend(t);
  setTimeout(() => dismissToast(t), dur);
}
function dismissToast(el) {
  if (!el || el.classList.contains('out')) return;
  el.classList.add('out');
  setTimeout(() => el.remove(), 220);
}

function renderCameras() {
  const cams = getCameras();
  const grid = document.getElementById('camerasGrid');
  const empty = document.getElementById('emptyState');

  const total   = cams.length;
  const active  = cams.filter(c => c.status === 'active').length;
  const offline = cams.filter(c => c.status === 'offline').length;
  const pending = total - active - offline;

  document.getElementById('stat-total').textContent   = total;
  document.getElementById('stat-active').textContent  = active;
  document.getElementById('stat-offline').textContent = offline;
  document.getElementById('stat-pending').textContent = pending;

  grid.querySelectorAll('.cam-card').forEach(c => c.remove());

  if (total === 0) { empty.style.display = 'flex'; return; }
  empty.style.display = 'none';

  cams.forEach(cam => {
    const card = document.createElement('div');
    card.className = 'cam-card';
    card.dataset.id = cam.id;

    const st = cam.status || 'pending';
    const stClass = st === 'active' ? 'status-active' : st === 'offline' ? 'status-offline' : 'status-pending';
    const stLabel = st === 'active' ? 'ACTIVE' : st === 'offline' ? 'HORS LIGNE' : 'EN ATTENTE';
    const icoClass = st === 'active' ? 'icon-active' : st === 'offline' ? 'icon-offline' : 'icon-pending';
    const icon = TYPE_ICONS[cam.type] || 'fa-video';
    const typeLabel = TYPE_LABELS[cam.type] || 'IP';

    const signal = cam.signal ?? 2;
    let bars = '';
    for (let i = 1; i <= 4; i++)
      bars += `<div class="sig-bar${i <= signal ? ' sig-on' : ''}" style="height:${4+i*4}px"></div>`;

    let streamHtml = '';
    if (cam.url) {
      streamHtml = `<img src="${escAttr(cam.url)}"
        onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
        style="width:100%;height:100%;object-fit:cover">
        <div class="stream-placeholder" style="display:none">
          <i class="fa-solid fa-video-slash"></i><span>Flux indisponible</span>
        </div>`;
    } else {
      streamHtml = `<div class="stream-placeholder">
          <i class="fa-solid ${icon}" style="opacity:.3"></i>
          <span style="font-size:10px;letter-spacing:.5px">Aucun flux configuré</span>
        </div>`;
    }

    card.innerHTML = `
      <div class="cam-header">
        <div class="cam-icon-wrap ${icoClass}">
          <i class="fa-solid ${icon}"></i>
        </div>
        <div class="cam-status-wrap">
          <span class="cam-status ${stClass}">${stLabel}</span>
          <div class="signal-bars">${bars}</div>
        </div>
      </div>
      <div class="cam-stream">${streamHtml}</div>
      <div class="cam-info">
        <div class="cam-name">${escHtml(cam.nom)}</div>
        <div class="cam-type"><i class="fa-solid ${icon}"></i>${typeLabel}</div>
        <div class="cam-ip"><i class="fa-solid fa-network-wired"></i>${escHtml(cam.ip)}:${escHtml(cam.port||'80')}</div>
        ${cam.salle ? `<div class="cam-salle"><i class="fa-solid fa-building-server"></i>${escHtml(cam.salle)}</div>` : ''}
        ${cam.pingTime ? `<div class="cam-ping"><i class="fa-solid fa-stopwatch"></i>Latence : ${cam.pingTime} ms</div>` : ''}
      </div>
      <div class="cam-actions">
        <button title="Vérifier connexion" data-ping="${cam.id}" onclick="pingCamera('${cam.id}',this)">
          <i class="fa-solid fa-satellite-dish"></i>
        </button>
        <button title="Ouvrir le flux" onclick="openStream('${cam.id}')">
          <i class="fa-solid fa-camera"></i>
        </button>
        <button title="Modifier" onclick="openEditModal('${cam.id}')">
          <i class="fa-solid fa-pen-to-square"></i>
        </button>
        <button class="btn-delete" title="Supprimer" onclick="askDelete('${cam.id}')">
          <i class="fa-solid fa-trash"></i>
        </button>
      </div>`;

    grid.appendChild(card);
  });
}

function escHtml(s) {
  const d = document.createElement('div');
  d.textContent = s || '';
  return d.innerHTML;
}
function escAttr(s) {
  return String(s||'').replace(/"/g,'&quot;');
}

function openAddModal() {
  _editingId = null;
  document.getElementById('modal-title').textContent = 'Ajouter une caméra';
  document.getElementById('cam-id').value    = '';
  document.getElementById('cam-nom').value   = '';
  document.getElementById('cam-type').value  = 'ip';
  document.getElementById('cam-ip').value    = '';
  document.getElementById('cam-port').value  = '80';
  document.getElementById('cam-login').value = '';
  document.getElementById('cam-pass').value  = '';
  document.getElementById('cam-url').value   = '';
  document.getElementById('cam-salle').value = '';
  document.getElementById('camModal').classList.add('open');
  setTimeout(() => document.getElementById('cam-nom').focus(), 80);
}

function openEditModal(id) {
  const cam = getCameras().find(c => c.id === id);
  if (!cam) return;
  _editingId = id;
  document.getElementById('modal-title').textContent = 'Modifier la caméra';
  document.getElementById('cam-id').value    = id;
  document.getElementById('cam-nom').value   = cam.nom   || '';
  document.getElementById('cam-type').value  = cam.type  || 'ip';
  document.getElementById('cam-ip').value    = cam.ip    || '';
  document.getElementById('cam-port').value  = cam.port  || '80';
  document.getElementById('cam-login').value = cam.login || '';
  document.getElementById('cam-pass').value  = cam.pass  || '';
  document.getElementById('cam-url').value   = cam.url   || '';
  document.getElementById('cam-salle').value = cam.salle || '';
  document.getElementById('camModal').classList.add('open');
}

function closeModal() {
  document.getElementById('camModal').classList.remove('open');
}

function saveCamera() {
  const nom = document.getElementById('cam-nom').value.trim();
  const ip  = document.getElementById('cam-ip').value.trim();
  if (!nom) { toast('Le nom de la caméra est obligatoire.', 'e'); return; }
  if (!ip)  { toast("L'adresse IP est obligatoire.", 'e'); return; }

  const existing = _editingId ? getCameras().find(c => c.id === _editingId) : null;
  const cam = {
    id:     _editingId || Date.now().toString(36) + Math.random().toString(36).slice(2,6),
    nom,
    type:   document.getElementById('cam-type').value,
    ip,
    port:   document.getElementById('cam-port').value || '80',
    login:  document.getElementById('cam-login').value,
    pass:   document.getElementById('cam-pass').value,
    url:    document.getElementById('cam-url').value.trim(),
    salle:  document.getElementById('cam-salle').value.trim(),
    status: existing?.status || 'pending',
    signal: existing?.signal ?? 2,
    pingTime: existing?.pingTime || null,
  };

  let cams = getCameras();
  if (_editingId) {
    const idx = cams.findIndex(c => c.id === _editingId);
    if (idx >= 0) cams[idx] = cam; else cams.push(cam);
    toast('Caméra mise à jour.', 's');
  } else {
    cams.push(cam);
    toast('Caméra ajoutée.', 's');
  }
  saveCameras(cams);
  closeModal();
  renderCameras();
}

function askDelete(id) {
  _deletePendingId = id;
  document.getElementById('confirmDlg').classList.add('open');
}
function closeConfirm() {
  _deletePendingId = null;
  document.getElementById('confirmDlg').classList.remove('open');
}
function doDelete() {
  if (!_deletePendingId) return;
  const cams = getCameras().filter(c => c.id !== _deletePendingId);
  saveCameras(cams);
  _deletePendingId = null;
  document.getElementById('confirmDlg').classList.remove('open');
  renderCameras();
  toast('Caméra supprimée.', 'w');
}

function pingCamera(id, btn) {
  const cam = getCameras().find(c => c.id === id);
  if (!cam) return;

  if (btn) { btn.classList.add('btn-ping-busy'); btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>'; }

  const tok = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  fetch(`/camera/ping?ip_addr=${encodeURIComponent(cam.ip)}&port=${encodeURIComponent(cam.port||80)}`, {
    headers: { 'X-CSRF-TOKEN': tok, 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    let cams = getCameras();
    const idx = cams.findIndex(c => c.id === id);
    if (idx >= 0) {
      cams[idx].status   = data.reachable ? 'active' : 'offline';
      cams[idx].pingTime = data.time ?? null;
      if (data.reachable) {
        const t = data.time || 999;
        cams[idx].signal = t < 50 ? 4 : t < 100 ? 3 : t < 200 ? 2 : 1;
      } else {
        cams[idx].signal = 0;
      }
      saveCameras(cams);
    }
    renderCameras();
    if (data.reachable) toast(`${cam.nom} — En ligne (${data.time}ms)`, 's');
    else toast(`${cam.nom} — Hors ligne`, 'e');
  })
  .catch(() => {
    let cams = getCameras();
    const idx = cams.findIndex(c => c.id === id);
    if (idx >= 0) { cams[idx].status = 'offline'; cams[idx].signal = 0; saveCameras(cams); }
    renderCameras();
    toast(`${cam.nom} — Inaccessible`, 'e');
  });
}

function pingAll() {
  const cams = getCameras();
  if (cams.length === 0) { toast('Aucune caméra à vérifier.', 'i'); return; }
  toast(`Vérification de ${cams.length} caméra(s)…`, 'i', 2000);
  cams.forEach(cam => pingCamera(cam.id));
}

function openStream(id) {
  const cam = getCameras().find(c => c.id === id);
  if (!cam) return;
  const url = cam.url || `http://${cam.ip}:${cam.port || 80}/`;
  window.open(url, '_blank');
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeModal();
    closeConfirm();
  }
});
document.getElementById('camModal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
document.getElementById('confirmDlg').addEventListener('click', function(e) {
  if (e.target === this) closeConfirm();
});

renderCameras();
</script>
</body>
</html>
