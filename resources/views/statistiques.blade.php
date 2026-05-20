@extends('layouts.app')

@section('content')
<style>
:root{--neon:#33ff88;--blue:#33b5ff;--warn:#ffd633;--danger:#ff5733;--card:#0e1a38;--border:#1e2f5a;}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;flex-wrap:wrap;gap:12px}
.pg-title{font-size:22px;font-weight:700;color:var(--blue)}
.btn{padding:9px 18px;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:12px;transition:.18s;display:inline-flex;align-items:center;gap:6px}
.btn:active{transform:scale(.97)}
.btn-neon{background:transparent;border:1px solid var(--neon);color:var(--neon)}
.btn-neon:hover{background:var(--neon);color:#000}
.btn-blue{background:transparent;border:1px solid var(--blue);color:var(--blue)}
.btn-blue:hover{background:var(--blue);color:#000}
.btn-warn{background:transparent;border:1px solid var(--warn);color:var(--warn)}
.btn-warn:hover{background:var(--warn);color:#000}
.btn-gray{background:transparent;border:1px solid #2a3a5a;color:#666}
.btn-gray:hover{border-color:#aaa;color:#aaa}

.stats-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:24px}
.scard{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 12px;text-align:center;transition:.2s}
.scard:hover{border-color:var(--blue);transform:translateY(-2px)}
.scard .v{font-size:26px;font-weight:800;margin-bottom:4px}
.scard .l{font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px}
.scard .delta{font-size:10px;margin-top:4px}
.scard.c0 .v{color:var(--blue)}
.scard.c1 .v{color:var(--neon)}
.scard.c2 .v{color:var(--danger)}
.scard.c3 .v{color:var(--warn)}
.scard.c4 .v{color:#cc88ff}
.scard.c5 .v{color:#ff9933}

.charts-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px}
.chart-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px}
.chart-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:8px}
.chart-title{font-size:13px;font-weight:700;color:#fff}
.chart-meta{font-size:11px;color:#555}
canvas{max-height:200px}

.detail-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:24px}
.detail-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px}
.detail-card h4{font-size:12px;font-weight:700;color:var(--blue);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px}
.detail-row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid rgba(30,47,90,.4);font-size:12px}
.detail-row:last-child{border-bottom:none}
.detail-row .k{color:#666}
.detail-row .val{color:#ccc;font-weight:600}
.detail-row .val.g{color:var(--neon)}
.detail-row .val.w{color:var(--warn)}
.detail-row .val.r{color:var(--danger)}

.export-bar{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:14px 18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.export-bar .lbl{font-size:13px;color:#aaa}
.export-btns{display:flex;gap:8px;flex-wrap:wrap}

@media(max-width:1100px){.stats-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:768px){.charts-grid,.detail-grid{grid-template-columns:1fr}.stats-grid{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="pg-header">
    <div class="pg-title">Statistiques</div>
    <div style="font-size:12px;color:#555" id="statsTimestamp">—</div>
</div>

<!-- Stats grid -->
<div class="stats-grid">
    <div class="scard c0"><div class="v" id="st_mesures">—</div><div class="l">Mesures</div><div class="delta" style="color:#555" id="st_mesures_d"></div></div>
    <div class="scard c1"><div class="v" id="st_users">—</div><div class="l">Utilisateurs</div></div>
    <div class="scard c2"><div class="v" id="st_alertes">—</div><div class="l">Alertes total</div></div>
    <div class="scard c3"><div class="v" id="st_warn">—</div><div class="l">Warnings</div></div>
    <div class="scard c4"><div class="v" id="st_crit">—</div><div class="l">Critiques</div></div>
    <div class="scard c5"><div class="v" id="st_nonlu">—</div><div class="l">Non lues</div></div>
</div>

<!-- Charts -->
<div class="charts-grid">
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Alertes par niveau (7 jours)</div>
            <div class="chart-meta" id="chartMeta1">—</div>
        </div>
        <canvas id="chartAlertes"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Capteurs — dernières mesures</div>
            <div class="chart-meta" id="chartMeta2">temps réel</div>
        </div>
        <canvas id="chartSensors"></canvas>
    </div>
</div>

<!-- Details -->
<div class="detail-grid">
    <div class="detail-card">
        <h4>Capteurs (dernière mesure)</h4>
        <div id="sensorDetail">
            <div class="detail-row"><span class="k">Chargement...</span><span class="val">—</span></div>
        </div>
    </div>
    <div class="detail-card">
        <h4>Comptes utilisateurs</h4>
        <div class="detail-row"><span class="k">Total</span><span class="val" id="u_total">—</span></div>
        <div class="detail-row"><span class="k">Validés</span><span class="val g" id="u_valide">—</span></div>
        <div class="detail-row"><span class="k">En attente</span><span class="val w" id="u_attente">—</span></div>
        <div class="detail-row"><span class="k">Bloqués</span><span class="val r" id="u_bloque">—</span></div>
        <div class="detail-row"><span class="k">Refusés</span><span class="val" id="u_refuse">—</span></div>
    </div>
    <div class="detail-card">
        <h4>Alertes (résumé)</h4>
        <div class="detail-row"><span class="k">Total alertes</span><span class="val" id="a_total">—</span></div>
        <div class="detail-row"><span class="k">Critiques</span><span class="val r" id="a_crit">—</span></div>
        <div class="detail-row"><span class="k">Warnings</span><span class="val w" id="a_warn">—</span></div>
        <div class="detail-row"><span class="k">Non lues</span><span class="val" id="a_nonlu">—</span></div>
        <div class="detail-row"><span class="k">Lues</span><span class="val g" id="a_lu">—</span></div>
    </div>
</div>

<!-- Export bar -->
<div class="export-bar">
    <div class="lbl">Exporter les statistiques</div>
    <div class="export-btns">
        <a href="/rapports/export?type=mesures&format=csv&debut={{ now()->subDays(30)->toDateString() }}&fin={{ now()->toDateString() }}" class="btn btn-neon" style="text-decoration:none">&#8595; CSV Mesures</a>
        <a href="/rapports/export?type=alertes&format=csv&debut={{ now()->subDays(30)->toDateString() }}&fin={{ now()->toDateString() }}" class="btn btn-warn" style="text-decoration:none">&#8595; CSV Alertes</a>
        <a href="/rapports/export?type=mesures&format=json&debut={{ now()->subDays(30)->toDateString() }}&fin={{ now()->toDateString() }}" class="btn btn-blue" style="text-decoration:none">&#8195;&#123;&#125; JSON</a>
        <a href="/rapports/export?type=mesures&format=xls&debut={{ now()->subDays(30)->toDateString() }}&fin={{ now()->toDateString() }}" class="btn btn-gray" style="text-decoration:none">&#128202; Excel</a>
        <button class="btn btn-gray" onclick="window.open('/rapports/print?type=mesures&debut={{ now()->subDays(7)->toDateString() }}&fin={{ now()->toDateString() }}','_blank')">&#128438; PDF</button>
        <button class="btn btn-gray" onclick="loadStats()">&#8635; Actualiser</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
var chartAlertes = null, chartSensors = null;

function loadStats() {
    document.getElementById('statsTimestamp').textContent = new Date().toLocaleString('fr-FR');

    fetch('/api/stats')
        .then(function(r){return r.json();})
        .then(function(s) {
            document.getElementById('st_mesures').textContent = s.totalMesures     ?? '—';
            document.getElementById('st_users').textContent   = s.totalUtilisateurs ?? '—';
            document.getElementById('st_alertes').textContent = (s.alertesWarning||0) + (s.alertesCritiques||0);
            document.getElementById('st_warn').textContent    = s.alertesWarning   ?? '—';
            document.getElementById('st_crit').textContent    = s.alertesCritiques ?? '—';
            document.getElementById('st_nonlu').textContent   = s.alertesNonLues   ?? '—';

            document.getElementById('a_total').textContent  = (s.alertesWarning||0)+(s.alertesCritiques||0);
            document.getElementById('a_crit').textContent   = s.alertesCritiques ?? '—';
            document.getElementById('a_warn').textContent   = s.alertesWarning   ?? '—';
            document.getElementById('a_nonlu').textContent  = s.alertesNonLues   ?? '—';
            document.getElementById('a_lu').textContent     = Math.max(0,((s.alertesWarning||0)+(s.alertesCritiques||0))-(s.alertesNonLues||0));
        }).catch(function(){});

    fetch('/api/dashboard-data')
        .then(function(r){return r.json();})
        .then(function(d) {
            var m = d.derniere_mesure || {};
            var sensors = [
                {k:'Température',v:m.temperature,u:'°C'},
                {k:'Humidité',   v:m.humidite,   u:'%'},
                {k:'Gaz',        v:m.gaz,         u:'ppm'},
                {k:'Courant',    v:m.courant,     u:'A'},
                {k:'Puissance',  v:m.puissance,   u:'W'},
                {k:'Tension',    v:m.tension,     u:'V'},
            ];
            document.getElementById('sensorDetail').innerHTML = sensors.map(function(s) {
                return '<div class="detail-row"><span class="k">'+s.k+'</span><span class="val '+(s.v != null ? 'g' : '')+'">'+(s.v != null ? s.v+' '+s.u : '—')+'</span></div>';
            }).join('');

            // Sensor radar chart
            var svLabels = sensors.map(function(s){return s.k;});
            var svData   = sensors.map(function(s){return s.v != null ? s.v : 0;});
            if (chartSensors) { chartSensors.destroy(); }
            chartSensors = new Chart(document.getElementById('chartSensors').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: svLabels,
                    datasets: [{ label: 'Valeur actuelle', data: svData,
                        backgroundColor: ['rgba(255,87,51,.5)','rgba(51,181,255,.5)','rgba(255,214,51,.5)','rgba(255,153,51,.5)','rgba(204,136,255,.5)','rgba(51,255,136,.5)'],
                        borderColor:     ['#ff5733','#33b5ff','#ffd633','#ff9933','#cc88ff','#33ff88'],
                        borderWidth: 1, borderRadius: 5 }]
                },
                options: { responsive:true, maintainAspectRatio:true,
                    plugins:{legend:{display:false}},
                    scales:{x:{ticks:{color:'#555',font:{size:10}},grid:{color:'#1e2f5a'}},y:{ticks:{color:'#555',font:{size:10}},grid:{color:'#1e2f5a'}}}
                }
            });
        }).catch(function(){});

    fetch('/api/historique-data?type=alertes&debut='+getDate(-7)+'&fin='+getDate(0)+'&limit=500')
        .then(function(r){return r.json();})
        .then(function(alertes) {
            var byDay = {};
            alertes.forEach(function(a) {
                var d = (a.created_at||'').split('T')[0]||(a.created_at||'').split(' ')[0];
                if (!byDay[d]) byDay[d] = {warning:0,critique:0};
                byDay[d][a.niveau] = (byDay[d][a.niveau]||0)+1;
            });
            var days = [];
            for (var i=6; i>=0; i--) { days.push(getDate(-i)); }
            if (chartAlertes) { chartAlertes.destroy(); }
            chartAlertes = new Chart(document.getElementById('chartAlertes').getContext('2d'), {
                type: 'bar',
                data: { labels: days.map(function(d){return d.slice(5).replace('-','/');}) ,
                    datasets: [
                        {label:'Warning', data:days.map(function(d){return (byDay[d]||{}).warning||0;}),  backgroundColor:'rgba(255,214,51,.5)',borderColor:'#ffd633',borderWidth:1,borderRadius:4},
                        {label:'Critique',data:days.map(function(d){return (byDay[d]||{}).critique||0;}), backgroundColor:'rgba(255,87,51,.5)', borderColor:'#ff5733',borderWidth:1,borderRadius:4},
                    ]
                },
                options:{ responsive:true,maintainAspectRatio:true,
                    plugins:{legend:{labels:{color:'#aaa',font:{size:10}}}},
                    scales:{x:{stacked:true,ticks:{color:'#555',font:{size:10}},grid:{color:'#1e2f5a'}},y:{stacked:true,ticks:{color:'#555',font:{size:10}},grid:{color:'#1e2f5a'}}}
                }
            });
            document.getElementById('chartMeta1').textContent = alertes.length+' alertes / 7j';
        }).catch(function(){});

    // User stats
    fetch('/rapports/export?type=users&format=json&debut=2020-01-01&fin='+getDate(0))
        .catch(function(){});
}

function getDate(offset) {
    var d = new Date(); d.setDate(d.getDate()+offset);
    return d.toISOString().split('T')[0];
}

loadStats();
setInterval(loadStats, 30000);
</script>
@endsection
