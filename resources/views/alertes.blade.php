@extends('layouts.app')

@section('content')
<style>
:root{--neon:#33ff88;--blue:#33b5ff;--warn:#ffd633;--danger:#ff5733;--card:#0e1a38;--border:#1e2f5a;}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;flex-wrap:wrap;gap:12px}
.pg-title{font-size:22px;font-weight:700;color:var(--danger)}
.btn{padding:8px 15px;border:none;border-radius:7px;font-weight:700;cursor:pointer;font-size:12px;transition:.18s;display:inline-flex;align-items:center;gap:5px;white-space:nowrap}
.btn:active{transform:scale(.96)}
.btn-neon{background:transparent;border:1px solid var(--neon);color:var(--neon)}
.btn-neon:hover:not(:disabled){background:var(--neon);color:#000}
.btn-blue{background:transparent;border:1px solid var(--blue);color:var(--blue)}
.btn-blue:hover:not(:disabled){background:var(--blue);color:#000}
.btn-red{background:transparent;border:1px solid var(--danger);color:var(--danger)}
.btn-red:hover:not(:disabled){background:var(--danger);color:#fff}
.btn-warn{background:transparent;border:1px solid var(--warn);color:var(--warn)}
.btn-warn:hover:not(:disabled){background:var(--warn);color:#000}
.btn-gray{background:transparent;border:1px solid #2a3a5a;color:#666}
.btn-gray:hover:not(:disabled){border-color:#aaa;color:#aaa}
.btn:disabled{opacity:.4;cursor:not-allowed}

.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px}
.scard{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:15px 14px;text-align:center}
.scard .v{font-size:26px;font-weight:800;margin-bottom:3px}
.scard .l{font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px}
.scard.t .v{color:var(--blue)}
.scard.r .v{color:var(--danger)}
.scard.w .v{color:var(--warn)}
.scard.g .v{color:var(--neon)}

.filter-bar{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:12px 16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:16px}
.filter-bar select{background:#07102a;border:1px solid var(--border);border-radius:7px;padding:8px 11px;color:#fff;font-size:12px;outline:none;transition:.2s}
.filter-bar select:focus{border-color:var(--blue)}
.filter-bar select option{background:#0e1a38}
.filter-bar input{background:#07102a;border:1px solid var(--border);border-radius:7px;padding:8px 11px;color:#fff;font-size:12px;outline:none;width:200px}
.filter-bar input:focus{border-color:var(--blue)}
.refresh-toggle{display:flex;align-items:center;gap:7px;font-size:12px;color:#666;margin-left:auto}
.toggle-sw{width:36px;height:20px;border-radius:20px;background:#1a2a4a;position:relative;cursor:pointer;border:1px solid var(--border);transition:.3s}
.toggle-sw.on{background:rgba(51,255,136,.2);border-color:var(--neon)}
.toggle-sw::after{content:'';position:absolute;width:14px;height:14px;background:#555;border-radius:50%;top:2px;left:2px;transition:.3s}
.toggle-sw.on::after{left:18px;background:var(--neon)}

.table-card{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden}
.table-toolbar{display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:10px}
.table-toolbar .tit{font-size:13px;font-weight:700;color:#fff}
table{width:100%;border-collapse:collapse}
thead tr{background:#07102a}
th{padding:9px 13px;text-align:left;font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap}
td{padding:9px 13px;border-top:1px solid var(--border);font-size:12px;color:#ccc;vertical-align:middle}
tr:hover td{background:rgba(255,87,51,.03)}
.badge{display:inline-block;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700;text-transform:uppercase}
.badge-warning{background:rgba(255,214,51,.1);color:var(--warn);border:1px solid rgba(255,214,51,.25)}
.badge-critique{background:rgba(255,87,51,.1);color:var(--danger);border:1px solid rgba(255,87,51,.25)}
.badge-lu{background:rgba(51,255,136,.08);color:#33ff88aa;border:1px solid rgba(51,255,136,.15);font-size:9px}
.badge-unread{background:rgba(51,181,255,.08);color:var(--blue);border:1px solid rgba(51,181,255,.2);font-size:9px}
.row-read td{opacity:.55}
.no-data{text-align:center;padding:40px;color:#555;font-size:13px}
.live-dot{width:7px;height:7px;background:var(--neon);border-radius:50%;display:inline-block;animation:_spin 1.5s linear infinite;box-shadow:0 0 6px var(--neon);margin-right:5px}
@keyframes _spin2{0%,100%{opacity:1}50%{opacity:.2}}
.live-dot{animation:_spin2 1.5s ease infinite}

@media(max-width:768px){.stats-row{grid-template-columns:repeat(2,1fr)};table{display:block;overflow-x:auto}}
</style>

<div class="pg-header">
    <div class="pg-title">&#128276; Alertes</div>
    <div style="display:flex;align-items:center;gap:8px;font-size:12px">
        <span class="live-dot"></span>
        <span id="liveLabel" style="color:var(--neon)">Temps réel</span>
        <span style="color:#555;font-size:11px" id="lastRefresh">—</span>
    </div>
</div>

<!-- Stats -->
<div class="stats-row">
    <div class="scard t"><div class="v" id="stTotal">—</div><div class="l">Total</div></div>
    <div class="scard r"><div class="v" id="stCrit">—</div><div class="l">Critiques</div></div>
    <div class="scard w"><div class="v" id="stWarn">—</div><div class="l">Warnings</div></div>
    <div class="scard g"><div class="v" id="stLu">—</div><div class="l">Non lues</div></div>
</div>

<!-- Filters -->
<div class="filter-bar">
    <input type="text" id="aSearch" placeholder="Rechercher..." oninput="filterAlertes()">
    <select id="aNiveau" onchange="filterAlertes()">
        <option value="">Tous niveaux</option>
        <option value="warning">Warning</option>
        <option value="critique">Critique</option>
    </select>
    <select id="aLu" onchange="filterAlertes()">
        <option value="">Toutes</option>
        <option value="0">Non lues</option>
        <option value="1">Lues</option>
    </select>
    <div class="refresh-toggle">
        <div class="toggle-sw on" id="autoRefreshToggle" onclick="toggleAutoRefresh(this)" title="Actualisation auto"></div>
        <span>Auto</span>
    </div>
</div>

<!-- Table -->
<div class="table-card">
    <div class="table-toolbar">
        <div>
            <span class="tit">Historique des alertes</span>
            <span style="font-size:11px;color:#555;margin-left:8px" id="aCount">—</span>
        </div>
        <div style="display:flex;gap:7px;flex-wrap:wrap">
            <button class="btn btn-neon" onclick="marquerToutLu()">&#10003; Tout lire</button>
            <button class="btn btn-warn" onclick="viderAlertes()">&#128465; Vider</button>
            <a href="/rapports/export?type=alertes&format=csv" class="btn btn-gray" style="text-decoration:none">&#8595; CSV</a>
            <a href="/rapports/export?type=alertes&format=json" class="btn btn-gray" style="text-decoration:none">&#8195;&#123;&#125; JSON</a>
            <button class="btn btn-blue" onclick="loadAlertes()">&#8635;</button>
        </div>
    </div>
    <div style="overflow-x:auto">
    <table id="alertesTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Date / Heure</th>
                <th>Message</th>
                <th>Niveau</th>
                <th>Valeur</th>
                <th>Lu</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="alertesTbody">
            <tr><td colspan="7" class="no-data">Chargement...</td></tr>
        </tbody>
    </table>
    </div>
</div>

<script>
var autoRefresh = true;
var autoInterval = null;
var alertesData = [];

function loadAlertes() {
    document.getElementById('lastRefresh').textContent = new Date().toLocaleTimeString('fr-FR');
    fetch('/api/alertes-recentes?limit=200')
        .then(function(r){return r.json();})
        .then(function(data) {
            alertesData = data;
            renderAlertes(data);
            updateStats(data);
        })
        .catch(function(){});
}

function updateStats(data) {
    document.getElementById('stTotal').textContent = data.length;
    document.getElementById('stCrit').textContent  = data.filter(function(a){return a.niveau==='critique';}).length;
    document.getElementById('stWarn').textContent  = data.filter(function(a){return a.niveau==='warning';}).length;
    document.getElementById('stLu').textContent    = data.filter(function(a){return !a.lu;}).length;
}

function renderAlertes(data) {
    var niv    = document.getElementById('aNiveau').value;
    var luFlt  = document.getElementById('aLu').value;
    var q      = document.getElementById('aSearch').value.toLowerCase();
    var filtered = data.filter(function(a) {
        return (!niv   || a.niveau === niv)
            && (luFlt === '' || String(a.lu ? 1 : 0) === luFlt)
            && (!q     || (a.message||'').toLowerCase().includes(q));
    });
    document.getElementById('aCount').textContent = filtered.length+' alerte(s)';
    var tbody = document.getElementById('alertesTbody');
    if (!filtered.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="no-data">Aucune alerte correspondante.</td></tr>';
        return;
    }
    tbody.innerHTML = filtered.map(function(a) {
        var d   = new Date(a.created_at).toLocaleString('fr-FR');
        var cls = a.lu ? ' class="row-read"' : '';
        return '<tr id="arow_'+a.id+'"'+cls+'>'
            + '<td style="color:#555;font-size:10px">#'+a.id+'</td>'
            + '<td style="color:#555;font-size:11px;white-space:nowrap">'+d+'</td>'
            + '<td style="max-width:320px;color:'+(a.lu?'#666':'#ccc')+'">'+escHtml(a.message||'')+'</td>'
            + '<td><span class="badge badge-'+a.niveau+'">'+a.niveau+'</span></td>'
            + '<td style="color:var(--warn)">'+(a.valeur != null ? a.valeur : '—')+'</td>'
            + '<td><span class="badge '+(a.lu?'badge-lu':'badge-unread')+'">'+(a.lu?'Lu':'Non lu')+'</span></td>'
            + '<td style="display:flex;gap:5px">'
            + (!a.lu ? '<button class="btn btn-neon" style="padding:4px 9px;font-size:10px" onclick="lireAlerte(this,'+a.id+')">&#10003; Lire</button>' : '')
            + '<button class="btn btn-red" style="padding:4px 9px;font-size:10px" onclick="supprimerAlerte(this,'+a.id+')">&#10005;</button>'
            + '</td>'
            + '</tr>';
    }).join('');
}

function filterAlertes() { renderAlertes(alertesData); }

function lireAlerte(btn, id) {
    btnLoad(btn);
    csrfFetch('/api/alertes/lire', {method:'POST', body:JSON.stringify({id:id})})
        .then(function(r){return r.json();})
        .then(function() {
            var row = document.getElementById('arow_'+id);
            if (row) { row.classList.add('row-read'); }
            var idx = alertesData.findIndex(function(a){return a.id===id;});
            if (idx > -1) alertesData[idx].lu = true;
            updateStats(alertesData);
            notify('Alerte marquée comme lue.','s');
            loadAlertes();
        }).catch(function(){btnLoad(btn,false);});
}

function supprimerAlerte(btn, id) {
    confirmDlg('Supprimer cette alerte ?','Cette action est irréversible.').then(function(ok){
        if (!ok) return;
        btnLoad(btn);
        csrfFetch('/alerte/'+id, {method:'DELETE'})
            .then(function(r){return r.json();})
            .then(function(d){
                if (d.success) {
                    var row = document.getElementById('arow_'+id);
                    if (row) { row.style.opacity='0'; row.style.transition='opacity .25s'; setTimeout(function(){row.remove();},260); }
                    alertesData = alertesData.filter(function(a){return a.id!==id;});
                    updateStats(alertesData);
                    notify('Alerte supprimée.','s');
                } else { btnLoad(btn,false); notify('Erreur.','e'); }
            }).catch(function(){btnLoad(btn,false);notify('Erreur réseau.','e');});
    });
}

function marquerToutLu() {
    csrfFetch('/api/alertes/lire', {method:'POST', body:JSON.stringify({id:'all'})})
        .then(function(){notify('Toutes les alertes marquées comme lues.','s'); loadAlertes();})
        .catch(function(){notify('Erreur.','e');});
}

function viderAlertes() {
    confirmDlg('Vider toutes les alertes ?','Toutes les alertes seront supprimées définitivement.').then(function(ok){
        if (!ok) return;
        csrfFetch('/alertes/vider', {method:'POST'})
            .then(function(r){return r.json();})
            .then(function(d){
                if (d.success) { alertesData=[]; renderAlertes([]); updateStats([]); notify(d.message||'Alertes vidées.','s'); }
                else notify(d.error||'Erreur.','e');
            }).catch(function(){notify('Erreur réseau.','e');});
    });
}

function toggleAutoRefresh(el) {
    autoRefresh = !autoRefresh;
    el.classList.toggle('on', autoRefresh);
    if (autoRefresh) { autoInterval = setInterval(loadAlertes, 5000); }
    else { clearInterval(autoInterval); }
}

function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

loadAlertes();
autoInterval = setInterval(loadAlertes, 5000);
</script>
@endsection
