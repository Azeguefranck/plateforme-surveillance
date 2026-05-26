@extends('layouts.app')

@section('content')

<style>
@keyframes fadeIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.anom-wrap{animation:fadeIn .4s ease;}

.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;}
.page-title{font-size:20px;font-weight:bold;color:var(--text);display:flex;align-items:center;gap:10px;}
.page-title i{color:var(--danger);}

/* ── KPI ── */
.kpi-row{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;}
.kpi-box{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:14px 20px;flex:1 1 130px;min-width:120px;}
.kpi-box .num{font-size:26px;font-weight:bold;}
.kpi-box .lbl{font-size:11px;color:var(--muted);letter-spacing:1px;text-transform:uppercase;margin-top:2px;}
.kpi-box.crit .num{color:var(--danger);}
.kpi-box.warn .num{color:#d97706;}
.kpi-box.ok   .num{color:var(--accent);}
.kpi-box.blue .num{color:var(--info);}

/* ── Filter bar ── */
.filter-bar{
  background:var(--card);border:1px solid var(--border);border-radius:12px;
  padding:14px 18px;margin-bottom:18px;display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;
}
.f-group{display:flex;flex-direction:column;gap:5px;flex:1 1 140px;}
.f-label{font-size:11px;font-weight:bold;color:var(--muted);letter-spacing:.5px;text-transform:uppercase;}
.f-ctrl{
  background:#0a1525;border:1.5px solid var(--border);border-radius:8px;
  color:var(--text);padding:8px 12px;font-size:13px;outline:none;transition:border-color .2s;
}
.f-ctrl:focus{border-color:var(--accent);}
.f-ctrl option{background:#0a1525;}
.btn-filter{
  background:var(--accent);color:#060c1a;border:none;border-radius:8px;
  padding:9px 18px;font-weight:bold;font-size:13px;cursor:pointer;transition:.2s;
  display:inline-flex;align-items:center;gap:6px;align-self:flex-end;
}
.btn-filter:hover{background:#249040;}

/* ── Toolbar ── */
.toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px;}
.info-text{font-size:12px;color:var(--muted);}
.toolbar-btns{display:flex;gap:8px;}
.btn-sm{
  padding:7px 14px;border-radius:8px;font-size:12px;font-weight:bold;cursor:pointer;transition:.2s;
  display:inline-flex;align-items:center;gap:6px;border:1.5px solid;
}
.btn-refresh{background:transparent;color:var(--muted);border-color:var(--border);}
.btn-refresh:hover{color:var(--text);border-color:var(--muted);}
.btn-export{background:rgba(47,168,79,.15);color:var(--accent);border-color:var(--accent);}
.btn-export:hover{background:var(--accent);color:#060c1a;}
.btn-all-resolve{background:rgba(46,134,193,.15);color:var(--info);border-color:var(--info);}
.btn-all-resolve:hover{background:var(--info);color:white;}

/* ── Table ── */
.table-wrap{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden;}
.tbl{width:100%;border-collapse:collapse;font-size:13px;}
.tbl th{background:#091527;padding:12px 14px;text-align:left;color:var(--muted);font-size:11px;letter-spacing:1.5px;text-transform:uppercase;border-bottom:1px solid var(--border);}
.tbl td{padding:11px 14px;border-bottom:1px solid rgba(24,38,64,.6);color:var(--text);vertical-align:middle;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tbody tr:hover td{background:rgba(47,168,79,.03);}

.badge-crit{background:#3d0000;color:#ef4444;border:1px solid rgba(239,68,68,.4);padding:3px 10px;border-radius:10px;font-size:11px;font-weight:bold;display:inline-block;}
.badge-warn{background:#3d2800;color:#f59e0b;border:1px solid rgba(245,158,11,.4);padding:3px 10px;border-radius:10px;font-size:11px;font-weight:bold;display:inline-block;}
.badge-info{background:rgba(46,134,193,.15);color:#2e86c1;border:1px solid rgba(46,134,193,.4);padding:3px 10px;border-radius:10px;font-size:11px;display:inline-block;}
.badge-ok{background:rgba(47,168,79,.15);color:var(--accent);border:1px solid rgba(47,168,79,.4);padding:3px 10px;border-radius:10px;font-size:11px;display:inline-block;}

.type-icon{display:inline-flex;align-items:center;gap:6px;}
.live-dot{width:8px;height:8px;border-radius:50%;background:var(--danger);box-shadow:0 0 6px var(--danger);display:inline-block;animation:blink 1s infinite;}

.btn-resolve{
  background:rgba(47,168,79,.1);color:var(--accent);border:1px solid rgba(47,168,79,.3);
  border-radius:6px;padding:4px 10px;cursor:pointer;font-size:11px;font-weight:bold;transition:.2s;
}
.btn-resolve:hover{background:var(--accent);color:#060c1a;}
.btn-resolve:disabled{opacity:.5;cursor:not-allowed;}

.empty-cell{text-align:center;padding:50px;color:var(--muted);}

/* ── Pagination ── */
.pagination{display:flex;justify-content:center;gap:8px;padding:16px;flex-wrap:wrap;}
.page-btn{padding:6px 14px;border-radius:6px;border:1px solid var(--border);background:#091527;color:var(--muted);cursor:pointer;font-size:13px;transition:.2s;}
.page-btn:hover,.page-btn.active{background:var(--border);color:white;border-color:var(--accent);}
.page-btn:disabled{opacity:.4;cursor:not-allowed;}

@media(max-width:768px){
  .table-wrap{overflow-x:auto;}
  .tbl{min-width:700px;}
}
</style>

<div class="anom-wrap">

<div class="page-header">
  <div class="page-title">
    <i class="fa-solid fa-triangle-exclamation"></i> Anomalies
    <div class="live-dot" title="Données en temps réel"></div>
  </div>
  <div style="display:flex;gap:8px;">
    <button class="btn-sm btn-export" onclick="exportCSV()"><i class="fa-solid fa-file-csv"></i> Export</button>
    <a href="/rapports/export/alertes" class="btn-sm btn-export" style="text-decoration:none;"><i class="fa-solid fa-download"></i> CSV complet</a>
  </div>
</div>

<!-- KPI -->
<div class="kpi-row">
  <div class="kpi-box crit">
    <div class="num" id="kpi-crit">—</div>
    <div class="lbl">Critiques</div>
  </div>
  <div class="kpi-box warn">
    <div class="num" id="kpi-warn">—</div>
    <div class="lbl">Avertissements</div>
  </div>
  <div class="kpi-box blue">
    <div class="num" id="kpi-nonres">—</div>
    <div class="lbl">Non résolues</div>
  </div>
  <div class="kpi-box ok">
    <div class="num" id="kpi-today">—</div>
    <div class="lbl">Aujourd'hui</div>
  </div>
  <div class="kpi-box">
    <div class="num" id="kpi-total" style="color:var(--text);">—</div>
    <div class="lbl">Total</div>
  </div>
</div>

<!-- Filtres -->
<div class="filter-bar">
  <div class="f-group">
    <label class="f-label">Niveau</label>
    <select class="f-ctrl" id="f-niv">
      <option value="">Tous</option>
      <option value="CRITIQUE">Critique</option>
      <option value="AVERTISSEMENT">Avertissement</option>
    </select>
  </div>
  <div class="f-group">
    <label class="f-label">Date début</label>
    <input class="f-ctrl" type="date" id="f-debut">
  </div>
  <div class="f-group">
    <label class="f-label">Date fin</label>
    <input class="f-ctrl" type="date" id="f-fin">
  </div>
  <div class="f-group">
    <label class="f-label">Par page</label>
    <select class="f-ctrl" id="f-limit">
      <option value="25">25</option>
      <option value="50" selected>50</option>
      <option value="100">100</option>
    </select>
  </div>
  <button class="btn-filter" onclick="charger(1)"><i class="fa-solid fa-magnifying-glass"></i> Filtrer</button>
</div>

<!-- Toolbar -->
<div class="toolbar">
  <span class="info-text" id="info-text">Chargement...</span>
  <div class="toolbar-btns">
    <button class="btn-sm btn-refresh" onclick="charger(pageCourante)"><i class="fa-solid fa-rotate"></i> Actualiser</button>
    <button class="btn-sm btn-all-resolve" onclick="toutResoudre()"><i class="fa-solid fa-check-double"></i> Tout résoudre</button>
  </div>
</div>

<!-- Table -->
<div class="table-wrap">
  <table class="tbl">
    <thead>
      <tr>
        <th>#</th>
        <th>Date & Heure</th>
        <th>Type</th>
        <th>Valeur</th>
        <th>Niveau</th>
        <th>Description</th>
        <th>Mail</th>
        <th>Statut</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="anom-body">
      <tr><td colspan="9" class="empty-cell">Chargement...</td></tr>
    </tbody>
  </table>
</div>

<div class="pagination" id="pagination"></div>

</div>

<script>
let pageCourante = 1;
let dernierData  = [];

const typeIcons = {
  temperature: 'fa-temperature-three-quarters',
  humidite:    'fa-droplet',
  gaz:         'fa-wind',
  courant:     'fa-bolt',
  puissance:   'fa-plug',
  intrusion:   'fa-person-walking',
  pir:         'fa-person-walking',
};

async function charger(page) {
  pageCourante = page;
  const niv    = document.getElementById('f-niv').value;
  const debut  = document.getElementById('f-debut').value;
  const fin    = document.getElementById('f-fin').value;
  const limit  = document.getElementById('f-limit').value;
  const tbody  = document.getElementById('anom-body');
  const info   = document.getElementById('info-text');

  tbody.innerHTML = '<tr><td colspan="9" class="empty-cell">Chargement...</td></tr>';
  info.textContent = 'Chargement...';

  try {
    let url = `/api/anomalies?page=${page}&limit=${limit}`;
    if (niv)   url += '&niveau=' + encodeURIComponent(niv);
    if (debut) url += '&debut='  + debut;
    if (fin)   url += '&fin='    + fin;

    const r = await fetch(url);
    const j = await r.json();
    dernierData = j.data || [];

    // KPI
    const s = j.stats || {};
    document.getElementById('kpi-crit').textContent   = s.critiques  || 0;
    document.getElementById('kpi-warn').textContent   = (s.total||0) - (s.critiques||0);
    document.getElementById('kpi-nonres').textContent = s.non_resolu || 0;
    document.getElementById('kpi-today').textContent  = s.today       || 0;
    document.getElementById('kpi-total').textContent  = s.total       || 0;

    info.textContent = `${(j.total||0).toLocaleString('fr-FR')} anomalie(s) — Page ${j.page||1}/${j.last_page||1}`;

    if (!dernierData.length) {
      tbody.innerHTML = '<tr><td colspan="9" class="empty-cell"><i class="fa-solid fa-circle-check" style="font-size:28px;display:block;margin-bottom:8px;color:var(--accent);"></i>Aucune anomalie trouvée</td></tr>';
      return;
    }

    tbody.innerHTML = dernierData.map(a => {
      const dt    = a.created_at ? new Date(a.created_at).toLocaleString('fr-FR') : '—';
      const niv   = (a.niveau || 'INFO').toUpperCase();
      const badge = niv === 'CRITIQUE'      ? `<span class="badge-crit">${niv}</span>`
                  : niv === 'AVERTISSEMENT' ? `<span class="badge-warn">${niv}</span>`
                  : `<span class="badge-info">${niv}</span>`;
      const icon  = typeIcons[a.type] || 'fa-circle-exclamation';
      const sms   = a.envoi_sms ? '<span class="badge-ok">Mail</span>' : '<span style="color:var(--muted);font-size:11px;">—</span>';
      const stat  = a.resolu
        ? '<span class="badge-ok">Résolu</span>'
        : '<span class="badge-warn">Ouvert</span>';
      const btnRes = a.resolu
        ? '<span style="color:var(--muted);font-size:11px;">✓</span>'
        : `<button class="btn-resolve" id="res-${a.id}" onclick="resoudre(${a.id},this)"><i class="fa-solid fa-check"></i></button>`;
      const msg = (a.message || '—').replace(/RISQUES:/,'<span style="color:#f59e0b">RISQUES:</span>').replace(/SOLUTIONS:/,'<span style="color:var(--accent)">SOLUTIONS:</span>');
      return `<tr id="row-${a.id}">
        <td style="color:var(--muted)">${a.id}</td>
        <td style="font-size:12px;white-space:nowrap;">${dt}</td>
        <td><div class="type-icon"><i class="fa-solid ${icon}" style="color:var(--accent);"></i>${a.type||'—'}</div></td>
        <td style="font-weight:bold;color:#ffd633;">${a.valeur||'—'}</td>
        <td>${badge}</td>
        <td style="font-size:12px;max-width:300px;">${msg}</td>
        <td>${sms}</td>
        <td>${stat}</td>
        <td>${btnRes}</td>
      </tr>`;
    }).join('');

    buildPagination(j.page||1, j.last_page||1);
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="9" class="empty-cell" style="color:var(--danger)">Erreur de chargement</td></tr>';
  }
}

async function resoudre(id, btn) {
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
  try {
    await fetch(`/api/alertes/${id}/resoudre`, { method: 'POST' });
    const row = document.getElementById('row-' + id);
    if (row) {
      row.querySelectorAll('td')[7].innerHTML = '<span class="badge-ok">Résolu</span>';
      row.style.opacity = '.6';
    }
    btn.outerHTML = '<span style="color:var(--accent);font-size:11px;">✓</span>';
    await charger(pageCourante);
  } catch(e) {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-check"></i>';
  }
}

async function toutResoudre() {
  const ok = await CyberConfirm.show({title:'Résoudre toutes les anomalies',message:'Marquer toutes les anomalies comme résolues ? Cette action est irréversible.',icon:'fa-solid fa-check-double',confirmText:'Tout résoudre',confirmColor:'success'});
  if (!ok) return;
  try {
    const ids = dernierData.filter(a => !a.resolu).map(a => a.id);
    await Promise.all(ids.map(id => fetch(`/api/alertes/${id}/resoudre`, { method: 'POST' })));
    charger(1);
  } catch(e) {}
}

function buildPagination(cur, last) {
  const pag = document.getElementById('pagination');
  if (last <= 1) { pag.innerHTML = ''; return; }
  const pages = [];
  pages.push(`<button class="page-btn" onclick="charger(${Math.max(1,cur-1)})" ${cur===1?'disabled':''}>← Préc</button>`);
  let s = Math.max(1,cur-2), e = Math.min(last,cur+2);
  if (s>1) pages.push('<span style="color:var(--muted);padding:0 4px;">…</span>');
  for (let p=s;p<=e;p++) pages.push(`<button class="page-btn ${p===cur?'active':''}" onclick="charger(${p})">${p}</button>`);
  if (e<last) pages.push('<span style="color:var(--muted);padding:0 4px;">…</span>');
  pages.push(`<button class="page-btn" onclick="charger(${Math.min(last,cur+1)})" ${cur===last?'disabled':''}>Suiv →</button>`);
  pag.innerHTML = pages.join('');
}

function exportCSV() {
  if (!dernierData.length) { alert('Aucune donnée à exporter'); return; }
  const headers = ['ID','Date','Type','Niveau','Valeur','Message','Mail','Resolu'];
  const rows = dernierData.map(a => [
    a.id, '"'+(a.created_at||'')+'"', a.type||'', a.niveau||'',
    '"'+(a.valeur||'')+'"', '"'+((a.message||'').replace(/"/g,'""'))+'"',
    a.envoi_sms?'OUI':'NON', a.resolu?'OUI':'NON'
  ].join(','));
  const csv = [headers.join(','), ...rows].join('\n');
  const link = document.createElement('a');
  link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
  link.download = 'anomalies_' + new Date().toISOString().slice(0,10) + '.csv';
  link.click();
}

charger(1);
setInterval(() => charger(pageCourante), 15000);
</script>

@endsection
