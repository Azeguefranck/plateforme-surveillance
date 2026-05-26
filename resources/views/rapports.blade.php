@extends('layouts.app')

@section('content')

<style>
@keyframes fadeIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.rpt-wrap{animation:fadeIn .4s ease;}

/* ── Filter panel ── */
.filter-panel{background:#0d1a2e;border:1px solid #182640;border-radius:16px;padding:20px;margin-bottom:20px;}
.filter-title{font-size:13px;font-weight:bold;letter-spacing:1px;color:#2fa84f;text-transform:uppercase;margin-bottom:14px;}
.filter-grid{display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:5px;flex:1 1 160px;}
.filter-label{font-size:11px;font-weight:bold;color:#6b7fa0;letter-spacing:.5px;text-transform:uppercase;}
.form-control{background:#0a1525;border:1.5px solid #1e3050;border-radius:9px;color:#d4dced;padding:9px 12px;font-size:13px;outline:none;transition:border-color .2s;}
.form-control:focus{border-color:#2fa84f;}
.form-control option{background:#0a1525;}
.btn-filter{background:#2fa84f;color:#060c1a;border:none;border-radius:9px;padding:9px 20px;font-weight:bold;font-size:13px;cursor:pointer;transition:.2s;display:inline-flex;align-items:center;gap:7px;align-self:flex-end;}
.btn-filter:hover{background:#249040;}
.btn-reset{background:transparent;color:#6b7fa0;border:1px solid #182640;border-radius:9px;padding:9px 16px;font-weight:bold;font-size:13px;cursor:pointer;transition:.2s;display:inline-flex;align-items:center;gap:7px;align-self:flex-end;}
.btn-reset:hover{color:#d4dced;border-color:#d4dced;}

/* ── Section title ── */
.section-title{font-size:15px;font-weight:bold;color:#d4dced;margin:22px 0 14px;display:flex;align-items:center;gap:10px;}
.section-title i{color:#2fa84f;}

/* ── Report type cards ── */
.rpt-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(185px,1fr));gap:12px;margin-bottom:24px;}
.rpt-card{background:#0d1a2e;border:1px solid #182640;border-radius:14px;padding:16px;display:flex;flex-direction:column;gap:8px;transition:border-color .2s,box-shadow .2s;}
.rpt-card:hover{border-color:#2fa84f;box-shadow:0 4px 16px rgba(47,168,79,.08);}
.rpt-card.active-type{border-color:#2fa84f;background:rgba(47,168,79,.05);}
.rpt-icon{font-size:22px;color:#2fa84f;}
.rpt-name{font-size:13px;font-weight:bold;color:#d4dced;}
.rpt-desc{font-size:11px;color:#6b7fa0;line-height:1.4;}
.rpt-actions{display:flex;gap:7px;margin-top:4px;}
.btn-preview{flex:1;background:rgba(47,168,79,.1);color:#2fa84f;border:1px solid rgba(47,168,79,.3);border-radius:7px;padding:6px;font-size:11px;font-weight:bold;cursor:pointer;transition:.2s;text-align:center;}
.btn-preview:hover{background:rgba(47,168,79,.22);}
.btn-gen{flex:1;background:#2fa84f;color:#060c1a;border:none;border-radius:7px;padding:6px;font-size:11px;font-weight:bold;cursor:pointer;transition:.2s;text-align:center;}
.btn-gen:hover{background:#249040;}

/* ── Quick exports ── */
.exports-panel{background:#0d1a2e;border:1px solid #182640;border-radius:16px;padding:20px;margin-bottom:20px;}
.exports-row{display:flex;flex-wrap:wrap;gap:10px;margin-top:14px;}
.btn-export{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:9px;font-weight:bold;font-size:12px;cursor:pointer;transition:.2s;border:1.5px solid;text-decoration:none;}
.btn-csv{background:rgba(47,168,79,.12);color:#2fa84f;border-color:#2fa84f;}
.btn-csv:hover{background:#2fa84f;color:#060c1a;}
.btn-json{background:rgba(46,134,193,.12);color:#2e86c1;border-color:#2e86c1;}
.btn-json:hover{background:#2e86c1;color:white;}
.btn-pdf{background:rgba(231,76,60,.12);color:#e74c3c;border-color:#e74c3c;}
.btn-pdf:hover{background:#e74c3c;color:white;}
.btn-txt{background:rgba(230,126,34,.12);color:#e67e22;border-color:#e67e22;}
.btn-txt:hover{background:#e67e22;color:white;}
.btn-purple{background:rgba(139,92,246,.12);color:#8b5cf6;border-color:#8b5cf6;}
.btn-purple:hover{background:#8b5cf6;color:white;}

/* ── Preview table ── */
.table-wrap{background:#0d1a2e;border:1px solid #182640;border-radius:16px;overflow:hidden;margin-bottom:20px;}
.table-header{display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid #182640;flex-wrap:wrap;gap:8px;}
.table-title{font-size:13px;font-weight:bold;color:#2fa84f;letter-spacing:.5px;text-transform:uppercase;}
.table-meta{font-size:12px;color:#6b7fa0;margin-top:2px;}
.tbl-scroll{overflow-x:auto;}
.tbl{width:100%;border-collapse:collapse;font-size:13px;min-width:500px;}
.tbl th{background:#091527;padding:10px 13px;text-align:left;color:#6b7fa0;font-size:11px;letter-spacing:1px;text-transform:uppercase;border-bottom:1px solid #182640;white-space:nowrap;}
.tbl td{padding:9px 13px;border-bottom:1px solid rgba(24,38,64,.6);color:#d4dced;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tbody tr:hover td{background:rgba(47,168,79,.03);}
.loading-cell{text-align:center;padding:40px;color:#6b7fa0;}

.badge-crit{background:#3d0000;color:#ef4444;border:1px solid rgba(239,68,68,.4);padding:2px 7px;border-radius:10px;font-size:10px;font-weight:bold;}
.badge-warn{background:#3d2800;color:#f59e0b;border:1px solid rgba(245,158,11,.4);padding:2px 7px;border-radius:10px;font-size:10px;font-weight:bold;}
.badge-ok{background:#0d2e14;color:#2fa84f;border:1px solid rgba(47,168,79,.4);padding:2px 7px;border-radius:10px;font-size:10px;font-weight:bold;}
.badge-info{background:#062e38;color:#2e86c1;border:1px solid rgba(46,134,193,.4);padding:2px 7px;border-radius:10px;font-size:10px;font-weight:bold;}
.badge-ref{background:#3d0000;color:#ef4444;border:1px solid rgba(239,68,68,.3);padding:2px 7px;border-radius:10px;font-size:10px;}

/* ── Generate modal ── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.72);z-index:1000;align-items:center;justify-content:center;padding:16px;}
.modal-overlay.open{display:flex;}
.modal-box{background:#0d1a2e;border:1px solid #2fa84f;border-radius:20px;padding:28px 26px;width:100%;max-width:520px;animation:fadeIn .25s ease;}
.modal-title{font-size:18px;font-weight:bold;color:#2fa84f;margin-bottom:4px;}
.modal-sub{font-size:13px;color:#6b7fa0;margin-bottom:22px;}
.fmt-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:22px;}
.fmt-btn{padding:14px 8px;border-radius:10px;border:1.5px solid #182640;background:#091527;cursor:pointer;text-align:center;transition:.2s;}
.fmt-btn:hover{border-color:#2fa84f;}
.fmt-btn.selected{border-color:#2fa84f;background:rgba(47,168,79,.1);}
.fmt-btn i{font-size:22px;display:block;margin-bottom:6px;}
.fmt-btn span{font-size:12px;font-weight:bold;color:#d4dced;}
.fmt-csv  i{color:#2fa84f;}
.fmt-json i{color:#2e86c1;}
.fmt-xml  i{color:#8b5cf6;}
.fmt-txt  i{color:#e67e22;}
.fmt-excel i{color:#16a34a;}
.fmt-sql  i{color:#e74c3c;}
.fmt-word i{color:#2563eb;}
.fmt-pdf  i{color:#dc2626;}
.modal-dates{display:flex;gap:12px;margin-bottom:22px;}
.modal-dates .filter-group{flex:1;}
.modal-foot{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;}
.btn-cancel{background:transparent;color:#6b7fa0;border:1px solid #182640;border-radius:9px;padding:10px 20px;font-weight:bold;cursor:pointer;transition:.2s;font-size:13px;}
.btn-cancel:hover{color:#d4dced;border-color:#d4dced;}
.btn-download{background:#2fa84f;color:#060c1a;border:none;border-radius:9px;padding:10px 22px;font-weight:bold;font-size:14px;cursor:pointer;transition:.2s;display:inline-flex;align-items:center;gap:8px;}
.btn-download:hover{background:#249040;}

.btn-refresh{background:transparent;border:1px solid #182640;color:#6b7fa0;border-radius:8px;padding:6px 14px;cursor:pointer;font-size:12px;transition:.2s;}
.btn-refresh:hover{color:#2fa84f;border-color:#2fa84f;}

@media(max-width:600px){
  .fmt-grid{grid-template-columns:repeat(2,1fr);}
  .filter-group{flex:1 1 100%;}
  .modal-dates{flex-direction:column;}
  .modal-box{padding:20px 16px;}
}
@media(max-width:380px){
  .fmt-grid{grid-template-columns:repeat(2,1fr);}
}
</style>

<div class="rpt-wrap">

{{-- ═══ FILTRES ═══ --}}
<div class="filter-panel">
  <div class="filter-title"><i class="fa-solid fa-filter"></i> Filtres</div>
  <div class="filter-grid">
    <div class="filter-group">
      <label class="filter-label">Date début</label>
      <input class="form-control" type="date" id="f_debut">
    </div>
    <div class="filter-group">
      <label class="filter-label">Date fin</label>
      <input class="form-control" type="date" id="f_fin">
    </div>
    <div class="filter-group">
      <label class="filter-label">Niveau alerte</label>
      <select class="form-control" id="f_niveau">
        <option value="">Tous niveaux</option>
        <option value="critique">Critique</option>
        <option value="avertissement">Avertissement</option>
        <option value="info">Info</option>
      </select>
    </div>
    <div class="filter-group">
      <label class="filter-label">Salle</label>
      <select class="form-control" id="f_salle">
        <option value="">Toutes les salles</option>
        @foreach($salles as $sl)
        <option value="{{ $sl->id }}">{{ $sl->nom ?? ($sl->code ?? $sl->id) }}</option>
        @endforeach
      </select>
    </div>
    <button class="btn-filter" onclick="loadPreview()">
      <i class="fa-solid fa-magnifying-glass"></i> Filtrer
    </button>
    <button class="btn-reset" onclick="resetFilters()">
      <i class="fa-solid fa-xmark"></i> Réinitialiser
    </button>
  </div>
</div>

{{-- ═══ TYPES DE RAPPORTS ═══ --}}
<div class="section-title"><i class="fa-solid fa-file-chart-column"></i> Types de rapports</div>
<div class="rpt-grid">
  @php
  $types = [
    ['key'=>'capteurs',     'icon'=>'fa-temperature-three-quarters','name'=>'Capteurs',     'desc'=>'Mesures temp, humidité, gaz, courant'],
    ['key'=>'anomalies',    'icon'=>'fa-triangle-exclamation',      'name'=>'Anomalies',    'desc'=>'Détections et incidents anormaux'],
    ['key'=>'securite',     'icon'=>'fa-shield-halved',             'name'=>'Sécurité',     'desc'=>'Accès, intrusions, PIR détectés'],
    ['key'=>'utilisateurs', 'icon'=>'fa-users',                     'name'=>'Utilisateurs', 'desc'=>'Comptes et statuts utilisateurs'],
    ['key'=>'salles',       'icon'=>'fa-building-server',           'name'=>'Salles',       'desc'=>'État et historique des salles'],
    ['key'=>'serveurs',     'icon'=>'fa-server',                    'name'=>'Serveurs',     'desc'=>'Supervision des serveurs'],
    ['key'=>'energie',      'icon'=>'fa-bolt',                      'name'=>'Énergie',      'desc'=>'Consommation et puissance électrique'],
    ['key'=>'historique',   'icon'=>'fa-clock-rotate-left',         'name'=>'Historique',   'desc'=>'Journal complet des mesures'],
    ['key'=>'alertes',      'icon'=>'fa-bell',                      'name'=>'Alertes',      'desc'=>'Historique de toutes les alertes'],
    ['key'=>'incidents',    'icon'=>'fa-circle-exclamation',        'name'=>'Incidents',    'desc'=>'Incidents critiques uniquement'],
    ['key'=>'maintenance',  'icon'=>'fa-screwdriver-wrench',        'name'=>'Maintenance',  'desc'=>'Journal des interventions'],
  ];
  @endphp
  @foreach($types as $t)
  <div class="rpt-card" id="card-{{ $t['key'] }}">
    <i class="fa-solid {{ $t['icon'] }} rpt-icon"></i>
    <div class="rpt-name">{{ $t['name'] }}</div>
    <div class="rpt-desc">{{ $t['desc'] }}</div>
    <div class="rpt-actions">
      <button class="btn-preview" onclick="loadPreview('{{ $t['key'] }}')">
        <i class="fa-solid fa-eye"></i> Aperçu
      </button>
      <button class="btn-gen" onclick="openGenerateModal('{{ $t['key'] }}','{{ $t['name'] }}')">
        <i class="fa-solid fa-download"></i> Générer
      </button>
    </div>
  </div>
  @endforeach
</div>

{{-- ═══ EXPORTS RAPIDES ═══ --}}
<div class="exports-panel">
  <div class="filter-title"><i class="fa-solid fa-bolt-lightning"></i> Exports rapides</div>
  <div class="exports-row">
    <button class="btn-export btn-csv" onclick="quickExport('capteurs','csv')">
      <i class="fa-solid fa-file-csv"></i> Mesures CSV
    </button>
    <button class="btn-export btn-json" onclick="quickExport('capteurs','json')">
      <i class="fa-solid fa-file-code"></i> Mesures JSON
    </button>
    <button class="btn-export btn-txt" onclick="quickExport('alertes','csv')">
      <i class="fa-solid fa-bell"></i> Alertes CSV
    </button>
    <button class="btn-export btn-purple" onclick="quickExport('utilisateurs','json')">
      <i class="fa-solid fa-users"></i> Utilisateurs JSON
    </button>
    <button class="btn-export btn-pdf" onclick="quickExport(currentType,'pdf')">
      <i class="fa-solid fa-file-pdf"></i> PDF actuel
    </button>
    <button class="btn-export" style="background:rgba(37,99,235,.12);color:#2563eb;border:1.5px solid #2563eb;border-radius:9px;font-weight:bold;font-size:12px;cursor:pointer;transition:.2s;display:inline-flex;align-items:center;gap:8px;padding:9px 16px;text-decoration:none;"
      onclick="quickExport(currentType,'word')"
      onmouseover="this.style.background='#2563eb';this.style.color='white'"
      onmouseout="this.style.background='rgba(37,99,235,.12)';this.style.color='#2563eb'">
      <i class="fa-solid fa-file-word"></i> Word actuel
    </button>
    <button class="btn-export btn-pdf" onclick="window.print()" style="border-color:#9aa5b4;color:#9aa5b4;background:transparent;">
      <i class="fa-solid fa-print"></i> Imprimer
    </button>
    <button class="btn-export btn-txt" onclick="exportPreviewTXT()">
      <i class="fa-solid fa-file-lines"></i> Aperçu TXT
    </button>
  </div>
</div>

{{-- ═══ PRÉVISUALISATION ═══ --}}
<div class="table-wrap">
  <div class="table-header">
    <div>
      <div class="table-title">
        <i class="fa-solid fa-table"></i>
        Prévisualisation — <span id="preview-type-label">Capteurs</span>
      </div>
      <div class="table-meta" id="preview-meta"></div>
    </div>
    <button class="btn-refresh" onclick="loadPreview()">
      <i class="fa-solid fa-rotate"></i> Actualiser
    </button>
  </div>
  <div class="tbl-scroll">
    <table class="tbl">
      <thead id="preview-head">
        <tr><th colspan="9">Chargement...</th></tr>
      </thead>
      <tbody id="preview-body">
        <tr><td colspan="9" class="loading-cell">
          <i class="fa-solid fa-spinner fa-spin"></i> Chargement...
        </td></tr>
      </tbody>
    </table>
  </div>
</div>

</div>

{{-- ═══ MODAL GÉNÉRER ═══ --}}
<div class="modal-overlay" id="gen-modal" onclick="if(event.target===this)closeModal()">
  <div class="modal-box">
    <div class="modal-title"><i class="fa-solid fa-file-arrow-down"></i> Générer un rapport</div>
    <div class="modal-sub">Type sélectionné : <b id="gen-type-label" style="color:#d4dced;"></b></div>

    <div class="fmt-grid">
      <button class="fmt-btn fmt-csv selected" data-fmt="csv" onclick="selectFmt(this)">
        <i class="fa-solid fa-file-csv"></i><span>CSV</span>
      </button>
      <button class="fmt-btn fmt-json" data-fmt="json" onclick="selectFmt(this)">
        <i class="fa-solid fa-file-code"></i><span>JSON</span>
      </button>
      <button class="fmt-btn fmt-xml" data-fmt="xml" onclick="selectFmt(this)">
        <i class="fa-solid fa-file-lines"></i><span>XML</span>
      </button>
      <button class="fmt-btn fmt-txt" data-fmt="txt" onclick="selectFmt(this)">
        <i class="fa-solid fa-file-alt"></i><span>TXT</span>
      </button>
      <button class="fmt-btn fmt-excel" data-fmt="excel" onclick="selectFmt(this)">
        <i class="fa-solid fa-table-cells"></i><span>Excel</span>
      </button>
      <button class="fmt-btn fmt-sql" data-fmt="sql" onclick="selectFmt(this)">
        <i class="fa-solid fa-database"></i><span>SQL</span>
      </button>
      <button class="fmt-btn fmt-word" data-fmt="word" onclick="selectFmt(this)">
        <i class="fa-solid fa-file-word"></i><span>Word</span>
      </button>
      <button class="fmt-btn fmt-pdf" data-fmt="pdf" onclick="selectFmt(this)">
        <i class="fa-solid fa-file-pdf"></i><span>PDF</span>
      </button>
    </div>

    <div class="modal-dates">
      <div class="filter-group">
        <label class="filter-label">Date début</label>
        <input class="form-control" type="date" id="gen-debut">
      </div>
      <div class="filter-group">
        <label class="filter-label">Date fin</label>
        <input class="form-control" type="date" id="gen-fin">
      </div>
    </div>

    <div class="modal-foot">
      <button class="btn-cancel" onclick="closeModal()">
        <i class="fa-solid fa-xmark"></i> Annuler
      </button>
      <button class="btn-download" onclick="generateReport()">
        <i class="fa-solid fa-file-arrow-down"></i> Télécharger
      </button>
    </div>
  </div>
</div>

<script>
let currentType = 'capteurs';
let previewData = [];

const TYPE_LABELS = {
  capteurs:'Capteurs', anomalies:'Anomalies', securite:'Sécurité',
  utilisateurs:'Utilisateurs', salles:'Salles', serveurs:'Serveurs',
  energie:'Énergie', historique:'Historique', alertes:'Alertes',
  incidents:'Incidents', maintenance:'Maintenance'
};

function fmtDate(v){ return v ? new Date(v).toLocaleString('fr-FR') : '—'; }

const SEUILS = {temp:{w:30,c:40},gaz:{w:300,c:500},cur:{w:10,c:15}};
function niveauMesure(m){
  if((+m.temperature||0)>=SEUILS.temp.c||(+m.gaz||0)>=SEUILS.gaz.c||(+m.courant||0)>=SEUILS.cur.c||m.pir_detecte) return 'CRITIQUE';
  if((+m.temperature||0)>=SEUILS.temp.w||(+m.gaz||0)>=SEUILS.gaz.w||(+m.courant||0)>=SEUILS.cur.w) return 'AVERT';
  return 'OK';
}
function nivBadge(n){
  const u = (n||'').toUpperCase();
  if(u==='CRITIQUE'||u==='CRIT') return '<span class="badge-crit">CRIT</span>';
  if(u==='AVERT'||u==='AVERTISSEMENT') return '<span class="badge-warn">WARN</span>';
  if(u==='INFO') return '<span class="badge-info">INFO</span>';
  return '<span class="badge-ok">OK</span>';
}
function statuBadge(s){
  const u=(s||'').toLowerCase();
  if(u==='valide') return '<span class="badge-ok">Validé</span>';
  if(u==='attente') return '<span class="badge-warn">Attente</span>';
  return '<span class="badge-ref">Refusé</span>';
}

// ── Column config per type ──
const COL = {
  capteurs:{
    heads:['#','Date','Temp °C','Hum %','Gaz ppm','Courant A','Pui W','PIR','Niveau'],
    row:m=>[
      `<span style="color:#6b7fa0">${m.id}</span>`,
      `<span style="font-size:11px">${fmtDate(m.created_at)}</span>`,
      `<b style="color:#ff5733">${(+(m.temperature||0)).toFixed(1)}</b>`,
      `<b style="color:#33b5ff">${(+(m.humidite||0)).toFixed(1)}</b>`,
      `<b style="color:#ffd633">${Math.round(m.gaz||0)}</b>`,
      `<b style="color:#33ff88">${(+(m.courant||0)).toFixed(2)}</b>`,
      `<b style="color:#bb66ff">${Math.round(m.puissance||0)}</b>`,
      m.pir_detecte?'<b style="color:#ef4444">OUI</b>':'<span style="color:#6b7fa0">—</span>',
      nivBadge(niveauMesure(m)),
    ]
  },
  energie:{
    heads:['#','Date','Courant (A)','Puissance (W)','Tension (V)','Temp °C','Hum %'],
    row:m=>[
      `<span style="color:#6b7fa0">${m.id}</span>`,
      `<span style="font-size:11px">${fmtDate(m.created_at)}</span>`,
      `<b style="color:#33ff88">${(+(m.courant||0)).toFixed(2)}</b>`,
      `<b style="color:#bb66ff">${Math.round(m.puissance||0)}</b>`,
      `<b style="color:#ffd633">${Math.round(m.tension||220)}</b>`,
      `<b style="color:#ff5733">${(+(m.temperature||0)).toFixed(1)}</b>`,
      `<b style="color:#33b5ff">${(+(m.humidite||0)).toFixed(1)}</b>`,
    ]
  },
  alertes:{
    heads:['#','Date','Type','Niveau','Valeur','Message','Résolu'],
    row:m=>[
      `<span style="color:#6b7fa0">${m.id}</span>`,
      `<span style="font-size:11px">${fmtDate(m.created_at)}</span>`,
      `<span style="text-transform:capitalize">${m.type||'—'}</span>`,
      m.niveau==='CRITIQUE'?'<span class="badge-crit">CRITIQUE</span>':'<span class="badge-warn">'+(m.niveau||'—')+'</span>',
      `<span style="color:#6b7fa0">${m.valeur||'—'}</span>`,
      `<span style="font-size:11px;color:#6b7fa0">${(m.message||'').substring(0,70)}</span>`,
      m.resolu?'<span class="badge-ok">OUI</span>':'<span class="badge-ref">NON</span>',
    ]
  },
  incidents:{
    heads:['#','Date','Type','Valeur','Message','Résolu'],
    row:m=>[
      `<span style="color:#6b7fa0">${m.id}</span>`,
      `<span style="font-size:11px">${fmtDate(m.created_at)}</span>`,
      `<b style="color:#ef4444;text-transform:capitalize">${m.type||'—'}</b>`,
      `<b style="color:#ef4444">${m.valeur||'—'}</b>`,
      `<span style="font-size:11px;color:#6b7fa0">${(m.message||'').substring(0,80)}</span>`,
      m.resolu?'<span class="badge-ok">OUI</span>':'<span class="badge-ref">NON</span>',
    ]
  },
  securite:{
    heads:['#','Date','Type','Niveau','Valeur','Message'],
    row:m=>[
      `<span style="color:#6b7fa0">${m.id}</span>`,
      `<span style="font-size:11px">${fmtDate(m.created_at)}</span>`,
      `<b style="color:#ef4444;text-transform:capitalize">${m.type||'intrusion'}</b>`,
      m.niveau==='CRITIQUE'?'<span class="badge-crit">CRITIQUE</span>':'<span class="badge-warn">'+(m.niveau||'—')+'</span>',
      `<b style="color:#ef4444">${m.valeur||'—'}</b>`,
      `<span style="font-size:11px;color:#6b7fa0">${(m.message||'').substring(0,70)}</span>`,
    ]
  },
  utilisateurs:{
    heads:['#','Nom','Prénom','Email','Rôle','Statut','Téléphone'],
    row:m=>[
      `<span style="color:#6b7fa0">${m.id}</span>`,
      `<b>${m.nom||'—'}</b>`,
      `${m.prenom||'—'}`,
      `<span style="color:#2e86c1">${m.email||'—'}</span>`,
      `<span style="color:#6b7fa0">${m.role||'user'}</span>`,
      statuBadge(m.validation_status),
      `<span style="color:#6b7fa0">${m.telephone||'—'}</span>`,
    ]
  },
  salles:{
    heads:['#','Nom','Code','Capacité','État','Créé le'],
    row:m=>[
      `<span style="color:#6b7fa0">${m.id}</span>`,
      `<b>${m.nom||'—'}</b>`,
      `<span style="color:#2e86c1">${m.code||'—'}</span>`,
      `${m.capacite||'—'}`,
      `<span style="color:#2fa84f">${m.etat||'—'}</span>`,
      `<span style="font-size:11px">${fmtDate(m.created_at)}</span>`,
    ]
  },
  serveurs:{
    heads:['#','Nom','IP','Type','OS','Statut','Créé le'],
    row:m=>[
      `<span style="color:#6b7fa0">${m.id}</span>`,
      `<b>${m.nom||'—'}</b>`,
      `<span style="color:#2e86c1">${m.ip||'—'}</span>`,
      `${m.type||'—'}`,
      `${m.os||'—'}`,
      m.statut==='actif'?'<span class="badge-ok">Actif</span>':'<span class="badge-ref">'+(m.statut||'—')+'</span>',
      `<span style="font-size:11px">${fmtDate(m.created_at)}</span>`,
    ]
  },
  maintenance:{
    heads:['#','Date','Action'],
    row:m=>[
      `<span style="color:#6b7fa0">${m.id}</span>`,
      `<span style="font-size:11px">${fmtDate(m.created_at)}</span>`,
      `<span style="color:#d4dced">${m.action||'—'}</span>`,
    ]
  },
};
COL.historique = COL.capteurs;
COL.anomalies  = COL.alertes;

function buildParams(extra){
  return new URLSearchParams(Object.assign({
    type:   currentType,
    debut:  document.getElementById('f_debut').value  || '',
    fin:    document.getElementById('f_fin').value    || '',
    niveau: document.getElementById('f_niveau').value || '',
    salle:  document.getElementById('f_salle').value  || '',
    page:   1,
    limit:  50,
  }, extra||{})).toString();
}

async function loadPreview(type){
  if(type){
    currentType = type;
    document.querySelectorAll('.rpt-card').forEach(c=>c.classList.remove('active-type'));
    const card = document.getElementById('card-'+type);
    if(card){ card.classList.add('active-type'); card.scrollIntoView({behavior:'smooth',block:'nearest'}); }
  }
  document.getElementById('preview-type-label').textContent = TYPE_LABELS[currentType]||currentType;
  const thead = document.getElementById('preview-head');
  const tbody = document.getElementById('preview-body');
  const meta  = document.getElementById('preview-meta');
  const cfg   = COL[currentType] || COL.capteurs;
  const cols  = cfg.heads.length;

  thead.innerHTML = '<tr>' + cfg.heads.map(h=>`<th>${h}</th>`).join('') + '</tr>';
  tbody.innerHTML = `<tr><td colspan="${cols}" class="loading-cell"><i class="fa-solid fa-spinner fa-spin"></i> Chargement...</td></tr>`;
  meta.textContent = '';

  try {
    const r = await fetch('/api/report-data?' + buildParams());
    if(!r.ok) throw new Error('HTTP '+r.status);
    const j = await r.json();
    previewData = j.data || [];

    if(!previewData.length){
      tbody.innerHTML = `<tr><td colspan="${cols}" class="loading-cell">Aucune donnée pour ces filtres</td></tr>`;
      meta.textContent = '0 résultats';
      return;
    }
    meta.textContent = `${previewData.length} affichés / ${j.total||previewData.length} total`;
    tbody.innerHTML = previewData.map(m=>{
      try {
        const cells = cfg.row(m);
        return '<tr>' + cells.map(c=>`<td>${c}</td>`).join('') + '</tr>';
      } catch(e){
        return '<tr><td colspan="'+cols+'" style="color:#6b7fa0;padding:8px 13px">—</td></tr>';
      }
    }).join('');
  } catch(e){
    tbody.innerHTML = `<tr><td colspan="${cols}" class="loading-cell" style="color:#e74c3c"><i class="fa-solid fa-circle-exclamation"></i> Données indisponibles</td></tr>`;
  }
}

function resetFilters(){
  ['f_debut','f_fin','f_niveau','f_salle'].forEach(id=>{ const el=document.getElementById(id); if(el) el.value=''; });
  loadPreview();
}

// ── Generate modal ──
function openGenerateModal(type, label){
  currentType = type;
  document.getElementById('gen-type-label').textContent = label || TYPE_LABELS[type] || type;
  document.getElementById('gen-debut').value = document.getElementById('f_debut').value;
  document.getElementById('gen-fin').value   = document.getElementById('f_fin').value;
  document.getElementById('gen-modal').classList.add('open');
}

function closeModal(){
  document.getElementById('gen-modal').classList.remove('open');
}

function selectFmt(btn){
  document.querySelectorAll('.fmt-btn').forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');
}

function generateReport(){
  const fmt    = document.querySelector('.fmt-btn.selected')?.dataset.fmt || 'csv';
  const debut  = document.getElementById('gen-debut').value;
  const fin    = document.getElementById('gen-fin').value;
  const niveau = document.getElementById('f_niveau').value;
  const salle  = document.getElementById('f_salle').value;
  const url    = '/rapports/generer?' + new URLSearchParams({type:currentType,format:fmt,debut,fin,niveau,salle}).toString();
  window.location.href = url;
  if(window.showToast) showToast('Téléchargement en cours…', 'info');
  setTimeout(closeModal, 600);
}

function quickExport(type, format){
  const debut = document.getElementById('f_debut').value;
  const fin   = document.getElementById('f_fin').value;
  window.location.href = '/rapports/generer?' + new URLSearchParams({type,format,debut,fin}).toString();
  if(window.showToast) showToast('Export '+format.toUpperCase()+' en cours…', 'info');
}

function exportPreviewTXT(){
  if(!previewData.length){
    if(window.showToast) showToast('Chargez les données d\'abord.', 'error');
    return;
  }
  let txt = '=== RAPPORT ' + (TYPE_LABELS[currentType]||currentType).toUpperCase() + ' ===\n';
  txt += 'Export : ' + new Date().toLocaleString('fr-FR') + '\n';
  txt += 'Total  : ' + previewData.length + ' enregistrement(s)\n\n';
  previewData.forEach((m,i)=>{
    txt += '── ' + (i+1) + ' ──\n';
    Object.entries(m).forEach(([k,v])=>{ txt += '  ' + k.padEnd(26) + ': ' + (v??'—') + '\n'; });
    txt += '\n';
  });
  const a = document.createElement('a');
  a.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(txt);
  a.download = 'rapport_' + currentType + '_' + new Date().toISOString().slice(0,10) + '.txt';
  a.click();
}

// ── Init ──
loadPreview('capteurs');
</script>

@endsection
