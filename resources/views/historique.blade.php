@extends('layouts.app')

@section('content')

<style>
@keyframes fadeIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.hist-wrap{animation:fadeIn .4s ease;}

/* ── Filtres ── */
.filter-panel{
  background:#0d1a2e;border:1px solid #182640;border-radius:16px;
  padding:18px 20px;margin-bottom:18px;
}
.filter-row{display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:5px;flex:1 1 160px;}
.filter-label{font-size:11px;font-weight:bold;color:#6b7fa0;letter-spacing:.5px;text-transform:uppercase;}
.form-control{
  background:#0a1525;border:1.5px solid #1e3050;border-radius:9px;
  color:#d4dced;padding:9px 12px;font-size:13px;outline:none;transition:border-color .2s;
}
.form-control:focus{border-color:#2fa84f;}
.form-control option{background:#0a1525;}
.btn-filter{
  background:#2fa84f;color:#060c1a;border:none;border-radius:9px;
  padding:9px 18px;font-weight:bold;font-size:13px;cursor:pointer;transition:.2s;
  display:inline-flex;align-items:center;gap:7px;
}
.btn-filter:hover{background:#249040;}

/* ── Onglets ── */
.tabs{display:flex;gap:4px;margin-bottom:18px;border-bottom:2px solid #182640;}
.tab{
  padding:10px 20px;cursor:pointer;font-size:13px;font-weight:bold;
  color:#6b7fa0;background:transparent;border:none;
  border-bottom:2px solid transparent;margin-bottom:-2px;transition:.2s;
}
.tab.active{color:#2fa84f;border-bottom-color:#2fa84f;}
.tab:hover{color:#d4dced;}

/* ── Toolbar ── */
.toolbar{
  display:flex;justify-content:space-between;align-items:center;
  margin-bottom:14px;flex-wrap:wrap;gap:10px;
}
.toolbar-right{display:flex;gap:8px;align-items:center;}
.btn-sm{
  padding:7px 14px;border-radius:8px;font-size:12px;font-weight:bold;cursor:pointer;transition:.2s;
  display:inline-flex;align-items:center;gap:6px;border:1.5px solid;
}
.btn-refresh{background:transparent;color:#6b7fa0;border-color:#182640;}
.btn-refresh:hover{color:#d4dced;border-color:#6b7fa0;}
.btn-export-csv{background:rgba(47,168,79,.15);color:#2fa84f;border-color:#2fa84f;}
.btn-export-csv:hover{background:#2fa84f;color:#060c1a;}
.total-info{font-size:12px;color:#6b7fa0;}

/* ── Table ── */
.table-wrap{background:#0d1a2e;border:1px solid #182640;border-radius:16px;overflow:hidden;}
.tbl{width:100%;border-collapse:collapse;font-size:13px;}
.tbl th{
  background:#091527;padding:12px 14px;text-align:left;
  color:#6b7fa0;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;
  border-bottom:1px solid #182640;
}
.tbl td{padding:11px 14px;border-bottom:1px solid rgba(24,38,64,.6);color:#d4dced;vertical-align:middle;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tbody tr:hover td{background:rgba(47,168,79,.03);}
.loading-cell{text-align:center;padding:40px;color:#6b7fa0;}

.val-temp{color:#ff5733;font-weight:bold;}
.val-hum{color:#33b5ff;font-weight:bold;}
.val-gaz{color:#ffd633;font-weight:bold;}
.val-cur{color:#33ff88;font-weight:bold;}
.val-pwr{color:#bb66ff;font-weight:bold;}

.badge-crit{background:#3d0000;color:#ef4444;border:1px solid rgba(239,68,68,.4);padding:2px 8px;border-radius:10px;font-size:10px;font-weight:bold;}
.badge-warn{background:#3d2800;color:#f59e0b;border:1px solid rgba(245,158,11,.4);padding:2px 8px;border-radius:10px;font-size:10px;font-weight:bold;}
.badge-ok{color:#6b7fa0;}
.badge-sent{background:rgba(47,168,79,.15);border:1px solid #2fa84f;color:#2fa84f;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:bold;}
.badge-fail{background:rgba(231,76,60,.15);border:1px solid #e74c3c;color:#e74c3c;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:bold;}

/* ── Pagination ── */
.pagination{display:flex;justify-content:center;gap:8px;padding:16px;flex-wrap:wrap;}
.page-btn{
  padding:6px 14px;border-radius:6px;border:1px solid #182640;
  background:#091527;color:#6b7fa0;cursor:pointer;font-size:13px;transition:.2s;
}
.page-btn:hover,.page-btn.active{background:#182640;color:white;border-color:#2fa84f;}
.page-btn:disabled{opacity:.4;cursor:not-allowed;}
</style>

<div class="hist-wrap">

{{-- Filtres --}}
<div class="filter-panel">
  <div class="filter-row">
    <div class="filter-group">
      <label class="filter-label">Catégorie</label>
      <select class="form-control" id="f_categorie" onchange="switchTab(this.value)">
        <option value="capteurs">Capteurs</option>
        <option value="alertes">Alertes</option>
        <option value="sms">SMS</option>
      </select>
    </div>
    <div class="filter-group">
      <label class="filter-label">Date début</label>
      <input class="form-control" type="date" id="f_debut">
    </div>
    <div class="filter-group">
      <label class="filter-label">Date fin</label>
      <input class="form-control" type="date" id="f_fin">
    </div>
    <div class="filter-group">
      <label class="filter-label">Par page</label>
      <select class="form-control" id="f_limit">
        <option value="25">25</option>
        <option value="50" selected>50</option>
        <option value="100">100</option>
      </select>
    </div>
    <button class="btn-filter" onclick="charger(1)">
      <i class="fa-solid fa-magnifying-glass"></i> Filtrer
    </button>
  </div>
</div>

{{-- Onglets --}}
<div class="tabs">
  <button class="tab active" id="tab-capteurs" onclick="switchTab('capteurs')">
    <i class="fa-solid fa-microchip"></i> Capteurs
  </button>
  <button class="tab" id="tab-alertes" onclick="switchTab('alertes')">
    <i class="fa-solid fa-bell"></i> Alertes
  </button>
  <button class="tab" id="tab-sms" onclick="switchTab('sms')">
    <i class="fa-solid fa-comment-sms"></i> SMS
  </button>
</div>

{{-- Toolbar --}}
<div class="toolbar">
  <span class="total-info" id="total-info">Chargement...</span>
  <div class="toolbar-right">
    <button class="btn-sm btn-refresh" onclick="charger(pageCourante)">
      <i class="fa-solid fa-rotate"></i> Actualiser
    </button>
    <button class="btn-sm btn-export-csv" onclick="exportCSV()">
      <i class="fa-solid fa-file-csv"></i> Export CSV
    </button>
  </div>
</div>

{{-- Table capteurs --}}
<div id="table-capteurs" class="table-wrap">
  <table class="tbl">
    <thead>
      <tr>
        <th>#</th><th>Date & Heure</th><th>Temp °C</th>
        <th>Humidité %</th><th>Gaz ppm</th>
        <th>Courant A</th><th>Puissance W</th><th>PIR</th><th>Niveau</th>
      </tr>
    </thead>
    <tbody id="body-capteurs">
      <tr><td colspan="9" class="loading-cell">Chargement...</td></tr>
    </tbody>
  </table>
</div>

{{-- Table alertes --}}
<div id="table-alertes" class="table-wrap" style="display:none;">
  <table class="tbl">
    <thead>
      <tr><th>#</th><th>Date & Heure</th><th>Type</th><th>Message</th><th>Niveau</th></tr>
    </thead>
    <tbody id="body-alertes">
      <tr><td colspan="5" class="loading-cell">Cliquer sur "Alertes" pour charger</td></tr>
    </tbody>
  </table>
</div>

{{-- Table SMS --}}
<div id="table-sms" class="table-wrap" style="display:none;">
  <table class="tbl">
    <thead>
      <tr><th>#</th><th>Date & Heure</th><th>Destinataire</th><th>Message</th><th>Type</th><th>Statut</th></tr>
    </thead>
    <tbody id="body-sms">
      <tr><td colspan="6" class="loading-cell">Cliquer sur "SMS" pour charger</td></tr>
    </tbody>
  </table>
</div>

<div class="pagination" id="pagination"></div>

</div>

<script>
let pageCourante = 1;
let ongletActif  = 'capteurs';
let dernierData  = [];

const SEUILS = {
  temp:{warn:30,crit:40}, hum:{warn:70,crit:80},
  gaz:{warn:300,crit:500}, cur:{warn:10,crit:15},
};

function niveauRow(m){
  if(m.temperature>=SEUILS.temp.crit||m.gaz>=SEUILS.gaz.crit||m.courant>=SEUILS.cur.crit||m.pir_detecte) return 'CRITIQUE';
  if(m.temperature>=SEUILS.temp.warn||m.gaz>=SEUILS.gaz.warn||m.courant>=SEUILS.cur.warn) return 'AVERTISSEMENT';
  return '';
}

function switchTab(tab){
  ongletActif = tab;
  document.getElementById('f_categorie').value = tab;
  ['capteurs','alertes','sms'].forEach(t=>{
    document.getElementById('tab-'+t).classList.toggle('active', t===tab);
    document.getElementById('table-'+t).style.display = t===tab ? '' : 'none';
  });
  pageCourante = 1;
  charger(1);
}

async function charger(page){
  pageCourante = page;
  const limit  = document.getElementById('f_limit').value;
  const debut  = document.getElementById('f_debut').value;
  const fin    = document.getElementById('f_fin').value;
  const info   = document.getElementById('total-info');
  const pag    = document.getElementById('pagination');
  info.textContent = 'Chargement...';
  pag.innerHTML = '';

  if(ongletActif === 'capteurs'){
    const tbody = document.getElementById('body-capteurs');
    tbody.innerHTML = '<tr><td colspan="9" class="loading-cell">Chargement...</td></tr>';
    try {
      let url = `/api/historique?page=${page}&limit=${limit}`;
      if(debut) url += '&debut=' + debut;
      if(fin)   url += '&fin='   + fin;
      const r = await fetch(url);
      const j = await r.json();
      dernierData = j.data || [];
      info.textContent = `Total : ${(j.total||0).toLocaleString('fr-FR')} mesures — Page ${j.page||1}/${j.last_page||1}`;
      if(!dernierData.length){
        tbody.innerHTML='<tr><td colspan="9" class="loading-cell">Aucune donnée</td></tr>';
        return;
      }
      tbody.innerHTML = dernierData.map(m=>{
        const niv = niveauRow(m);
        const badge = niv==='CRITIQUE'?'<span class="badge-crit">CRIT</span>':niv==='AVERTISSEMENT'?'<span class="badge-warn">WARN</span>':'<span class="badge-ok">OK</span>';
        const dt = m.created_at ? new Date(m.created_at).toLocaleString('fr-FR') : '—';
        return `<tr>
          <td style="color:#6b7fa0">${m.id}</td>
          <td style="font-size:12px">${dt}</td>
          <td class="val-temp">${parseFloat(m.temperature||0).toFixed(1)}</td>
          <td class="val-hum">${parseFloat(m.humidite||0).toFixed(1)}</td>
          <td class="val-gaz">${Math.round(m.gaz||0)}</td>
          <td class="val-cur">${parseFloat(m.courant||0).toFixed(2)}</td>
          <td class="val-pwr">${Math.round(m.puissance||0)}</td>
          <td style="color:${m.pir_detecte?'#ef4444':'#6b7fa0'}">${m.pir_detecte?'OUI':'—'}</td>
          <td>${badge}</td>
        </tr>`;
      }).join('');
      buildPagination(j.page||1, j.last_page||1);
    } catch(e){
      tbody.innerHTML='<tr><td colspan="9" class="loading-cell" style="color:#e74c3c">Erreur</td></tr>';
    }

  } else if(ongletActif === 'alertes'){
    const tbody = document.getElementById('body-alertes');
    tbody.innerHTML = '<tr><td colspan="5" class="loading-cell">Chargement...</td></tr>';
    try {
      const r = await fetch('/api/alertes/recent');
      const alertes = await r.json();
      dernierData = Array.isArray(alertes) ? alertes : [];
      info.textContent = `Total : ${dernierData.length} alertes`;
      if(!dernierData.length){
        tbody.innerHTML='<tr><td colspan="5" class="loading-cell">Aucune alerte</td></tr>';
        return;
      }
      tbody.innerHTML = dernierData.slice(0,parseInt(limit)).map((a,i)=>{
        const dt = a.created_at ? new Date(a.created_at).toLocaleString('fr-FR') : '—';
        const niv = a.niveau||a.type_alerte||'info';
        const badge = niv==='critique'?'<span class="badge-crit">CRITIQUE</span>':niv==='avertissement'?'<span class="badge-warn">WARN</span>':'<span style="color:#6b7fa0">INFO</span>';
        return `<tr>
          <td style="color:#6b7fa0">${a.id||i+1}</td>
          <td style="font-size:12px">${dt}</td>
          <td style="color:#4a9fc4">${a.type||a.capteur||'—'}</td>
          <td>${a.message||a.valeur||'—'}</td>
          <td>${badge}</td>
        </tr>`;
      }).join('');
    } catch(e){
      tbody.innerHTML='<tr><td colspan="5" class="loading-cell" style="color:#e74c3c">Données indisponibles</td></tr>';
    }

  } else if(ongletActif === 'sms'){
    const tbody = document.getElementById('body-sms');
    tbody.innerHTML = '<tr><td colspan="6" class="loading-cell">Chargement...</td></tr>';
    try {
      const r = await fetch('/api/sms/log');
      const d = await r.json();
      dernierData = Array.isArray(d) ? d : (d.data||[]);
      info.textContent = `Total : ${dernierData.length} SMS`;
      if(!dernierData.length){
        tbody.innerHTML='<tr><td colspan="6" class="loading-cell">Aucun SMS</td></tr>';
        return;
      }
      tbody.innerHTML = dernierData.slice(0,parseInt(limit)).map((s,i)=>{
        const dt = s.created_at ? new Date(s.created_at).toLocaleString('fr-FR') : '—';
        const st = s.statut||s.status||'';
        const badge = st==='envoye'||st==='sent'?'<span class="badge-sent">ENVOYÉ</span>':st==='echec'||st==='failed'?'<span class="badge-fail">ECHEC</span>':'<span style="color:#6b7fa0">—</span>';
        return `<tr>
          <td style="color:#6b7fa0">${i+1}</td>
          <td style="font-size:12px">${dt}</td>
          <td style="color:#4a9fc4">${s.destinataire||s.numero||'—'}</td>
          <td style="max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${s.message||'—'}</td>
          <td style="color:#6b7fa0;font-size:12px">${s.type||'manuel'}</td>
          <td>${badge}</td>
        </tr>`;
      }).join('');
    } catch(e){
      tbody.innerHTML='<tr><td colspan="6" class="loading-cell" style="color:#e74c3c">Données indisponibles</td></tr>';
    }
  }
}

function buildPagination(current, last){
  const pag = document.getElementById('pagination');
  if(last<=1){ pag.innerHTML=''; return; }
  const pages = [];
  pages.push(`<button class="page-btn" onclick="charger(${Math.max(1,current-1)})" ${current===1?'disabled':''}>← Préc</button>`);
  let s=Math.max(1,current-2), e=Math.min(last,current+2);
  if(s>1) pages.push('<span style="color:#6b7fa0;padding:0 4px">…</span>');
  for(let p=s;p<=e;p++) pages.push(`<button class="page-btn ${p===current?'active':''}" onclick="charger(${p})">${p}</button>`);
  if(e<last) pages.push('<span style="color:#6b7fa0;padding:0 4px">…</span>');
  pages.push(`<button class="page-btn" onclick="charger(${Math.min(last,current+1)})" ${current===last?'disabled':''}>Suiv →</button>`);
  pag.innerHTML = pages.join('');
}

function exportCSV(){
  if(!dernierData.length){ alert('Aucune donnée à exporter'); return; }
  let headers, rows;
  if(ongletActif==='capteurs'){
    headers=['ID','Date','Temp_C','Humidite_pct','Gaz_ppm','Courant_A','Puissance_W','PIR'];
    rows=dernierData.map(m=>[m.id,m.created_at,m.temperature,m.humidite,m.gaz,m.courant,m.puissance,m.pir_detecte?'OUI':'NON'].join(','));
  } else if(ongletActif==='alertes'){
    headers=['ID','Date','Type','Message','Niveau'];
    rows=dernierData.map(a=>[a.id||'',a.created_at,a.type||'','"'+(a.message||'')+'"',a.niveau||''].join(','));
  } else {
    headers=['Date','Destinataire','Message','Type','Statut'];
    rows=dernierData.map(s=>[s.created_at,s.destinataire||s.numero||'','"'+(s.message||'')+'"',s.type||'',s.statut||''].join(','));
  }
  const csv=[headers.join(','),...rows].join('\n');
  const a=document.createElement('a');
  a.href='data:text/csv;charset=utf-8,'+encodeURIComponent(csv);
  a.download=`historique_${ongletActif}_${new Date().toISOString().slice(0,10)}.csv`;
  a.click();
}

charger(1);
</script>

@endsection
