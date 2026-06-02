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

.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px}
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
            <div class="chart-title">🌡️ Température / Heure</div>
            <div class="chart-meta" id="chartMeta1">24 dernières heures · seuils critiques</div>
        </div>
        <canvas id="chartTemperature"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">💧 Humidité / Heure</div>
            <div class="chart-meta" id="chartMeta2">24 dernières heures · stabilité</div>
        </div>
        <canvas id="chartHumidite"></canvas>
    </div>
</div>
<div class="chart-card" style="margin-bottom:24px">
    <div class="chart-header">
        <div class="chart-title">☁️ Gaz / Qualité Air / Heure</div>
        <div class="chart-meta" id="chartMeta3">24 dernières heures · fumée · pollution · seuils</div>
    </div>
    <canvas id="chartGaz" style="max-height:180px"></canvas>
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

<script src="/vendor/chartjs/chart.umd.min.js"></script>
<script>
var chartTemperature = null, chartHumidite = null, chartGaz = null;

/* ── Labels fixes 0h → 23h ── */
function buildHourlyLabels() {
    var labels = [];
    for (var i = 0; i <= 23; i++) {
        labels.push(i + 'h');
    }
    return labels;
}

/* ── Construit map heure→données depuis l'API horaire ── */
function buildHourMap(rows, live) {
    var map = {};
    (rows || []).forEach(function(r) {
        map[parseInt(r.heure)] = {
            temperature: r.temperature != null ? parseFloat(r.temperature) : null,
            humidite:    r.humidite    != null ? parseFloat(r.humidite)    : null,
            gaz:         r.gaz         != null ? parseFloat(r.gaz)         : null,
        };
    });
    // Heure courante : remplacer par la valeur live (plus précise)
    if (live && !live.error) {
        var h = new Date().getHours();
        map[h] = {
            temperature: live.temperature != null ? parseFloat(live.temperature) : (map[h]||{}).temperature,
            humidite:    live.humidite    != null ? parseFloat(live.humidite)    : (map[h]||{}).humidite,
            gaz:         live.gaz         != null ? parseFloat(live.gaz)         : (map[h]||{}).gaz,
        };
    }
    return map;
}

function getDate(offset) {
    var d = new Date(); d.setDate(d.getDate()+offset);
    return d.toISOString().split('T')[0];
}

var GRID = '#1e2f5a', TICK = '#888';
var commonScaleX = {
    ticks:{
        color:TICK, font:{size:9},
        callback: function(val, index) {
            var label = this.getLabelForValue(val);
            var h = parseInt(label);
            return h % 2 === 0 ? label : '';
        }
    },
    grid:{color:GRID}
};
var commonOpts   = { responsive:true, maintainAspectRatio:true, animation:{duration:600},
    plugins:{legend:{labels:{color:'#aaa',font:{size:10},boxWidth:12}}} };

var chartsBuilt = false;

function buildCharts(rows, live) {
    var labels  = buildHourlyLabels();
    var map     = buildHourMap(rows, live);
    var n       = labels.length;

    var tempData = labels.map(function(_, i){ var d=map[i]; return d?d.temperature:null; });
    var humData  = labels.map(function(_, i){ var d=map[i]; return d?d.humidite:null; });
    var gazData  = labels.map(function(_, i){ var d=map[i]; return d?d.gaz:null; });

    /* ── Mise à jour en place si les graphiques existent déjà ── */
    if (chartsBuilt && chartTemperature && chartHumidite && chartGaz) {
        chartTemperature.data.datasets[0].data = tempData;
        chartTemperature.update('none');

        chartHumidite.data.datasets[0].data = humData;
        chartHumidite.update('none');

        chartGaz.data.datasets[0].data = gazData;
        chartGaz.data.datasets[1].data = gazData.map(function(v){return v!=null?Math.min(v,200):null;});
        chartGaz.update('none');

        var nb = (rows||[]).length;
        document.getElementById('chartMeta1').textContent = nb + ' h enregistrées · aujourd\'hui';
        document.getElementById('chartMeta2').textContent = nb + ' h enregistrées · aujourd\'hui';
        document.getElementById('chartMeta3').textContent = nb + ' h enregistrées · aujourd\'hui';
        return;
    }

    /* ── Construction initiale ── */
    if (chartTemperature) chartTemperature.destroy();
    chartTemperature = new Chart(document.getElementById('chartTemperature').getContext('2d'), {
        type: 'line',
        data: { labels: labels, datasets: [
            { label: 'Température (°C)', data: tempData,
              borderColor: '#ff5733', backgroundColor: 'rgba(255,87,51,.12)',
              borderWidth: 2, fill: true, tension: 0.4,
              pointRadius: 2, pointBackgroundColor: '#ff5733', spanGaps: true },
            { label: 'Seuil critique 40°C', data: Array(n).fill(40),
              borderColor: '#ff0000', borderDash:[6,4], borderWidth:1.5,
              pointRadius:0, fill:false },
            { label: 'Seuil warning 35°C', data: Array(n).fill(35),
              borderColor: '#ffd633', borderDash:[4,3], borderWidth:1,
              pointRadius:0, fill:false },
        ]},
        options: Object.assign({}, commonOpts, {
            scales: {
                x: commonScaleX,
                y: { min:0, suggestedMax:60,
                     ticks:{ color:TICK, font:{size:10}, callback:function(v){return v+'°C';} }, grid:{color:GRID} }
            }
        })
    });

    if (chartHumidite) chartHumidite.destroy();
    chartHumidite = new Chart(document.getElementById('chartHumidite').getContext('2d'), {
        type: 'line',
        data: { labels: labels, datasets: [
            { label: 'Humidité (%)', data: humData,
              borderColor: '#33b5ff', backgroundColor: 'rgba(51,181,255,.15)',
              borderWidth: 2, fill: 'origin', tension: 0.45,
              pointRadius: 2, pointBackgroundColor: '#33b5ff', spanGaps: true },
            { label: 'Seuil critique 85%', data: Array(n).fill(85),
              borderColor: '#ff5733', borderDash:[6,4], borderWidth:1.5,
              pointRadius:0, fill:false },
            { label: 'Seuil warning 75%', data: Array(n).fill(75),
              borderColor: '#ffd633', borderDash:[4,3], borderWidth:1,
              pointRadius:0, fill:false },
        ]},
        options: Object.assign({}, commonOpts, {
            scales: {
                x: commonScaleX,
                y: { min:0, max:100,
                     ticks:{ color:TICK, font:{size:10}, callback:function(v){return v+'%';} }, grid:{color:GRID} }
            }
        })
    });

    if (chartGaz) chartGaz.destroy();
    chartGaz = new Chart(document.getElementById('chartGaz').getContext('2d'), {
        type: 'line',
        data: { labels: labels, datasets: [
            { label: 'Gaz / Qualité air (ppm)', data: gazData,
              borderColor: '#33ff88', backgroundColor: 'rgba(51,255,136,.08)',
              borderWidth: 2.5, fill: true, tension: 0.4,
              pointRadius: 2, pointBackgroundColor: '#33ff88', spanGaps: true },
            { label: 'Pollution modérée (ppm)', data: gazData.map(function(v){return v!=null?Math.min(v,200):null;}),
              borderColor: '#cc88ff', backgroundColor: 'rgba(204,136,255,.06)',
              borderWidth: 1.5, fill: true, tension: 0.4,
              pointRadius: 0, spanGaps: true },
            { label: 'Seuil critique 500 ppm', data: Array(n).fill(500),
              borderColor: '#ff5733', borderDash:[7,4], borderWidth:1.5,
              pointRadius:0, fill:false },
            { label: 'Seuil warning 300 ppm', data: Array(n).fill(300),
              borderColor: '#ffd633', borderDash:[4,3], borderWidth:1,
              pointRadius:0, fill:false },
        ]},
        options: Object.assign({}, commonOpts, {
            scales: {
                x: commonScaleX,
                y: { min:0, suggestedMax:600,
                     ticks:{ color:TICK, font:{size:10}, callback:function(v){return v+' ppm';} }, grid:{color:GRID} }
            }
        })
    });

    chartsBuilt = true;
    var nb = (rows||[]).length;
    document.getElementById('chartMeta1').textContent = nb + ' h enregistrées · aujourd\'hui';
    document.getElementById('chartMeta2').textContent = nb + ' h enregistrées · aujourd\'hui';
    document.getElementById('chartMeta3').textContent = nb + ' h enregistrées · aujourd\'hui';
}

function loadStats() {
    document.getElementById('statsTimestamp').textContent = new Date().toLocaleString('fr-FR');

    /* ── Compteurs ── */
    fetch('/api/stats')
        .then(function(r){return r.json();})
        .then(function(s) {
            document.getElementById('st_mesures').textContent  = s.totalMesures      ?? '—';
            document.getElementById('st_users').textContent    = s.totalUtilisateurs  ?? '—';
            document.getElementById('st_alertes').textContent  = (s.alertesWarning||0)+(s.alertesCritiques||0);
            document.getElementById('st_warn').textContent     = s.alertesWarning    ?? '—';
            document.getElementById('st_crit').textContent     = s.alertesCritiques  ?? '—';
            document.getElementById('st_nonlu').textContent    = s.alertesNonLues    ?? '—';
            document.getElementById('a_total').textContent     = (s.alertesWarning||0)+(s.alertesCritiques||0);
            document.getElementById('a_crit').textContent      = s.alertesCritiques  ?? '—';
            document.getElementById('a_warn').textContent      = s.alertesWarning    ?? '—';
            document.getElementById('a_nonlu').textContent     = s.alertesNonLues    ?? '—';
            document.getElementById('a_lu').textContent        = Math.max(0,((s.alertesWarning||0)+(s.alertesCritiques||0))-(s.alertesNonLues||0));
        }).catch(function(){});

    /* ── Détail capteurs (dernière mesure) ── */
    fetch('/api/dashboard-data')
        .then(function(r){return r.json();})
        .then(function(d) {
            var m = d.derniere_mesure || {};
            var sensors = [
                {k:'Température', v:m.temperature, u:'°C'},
                {k:'Humidité',    v:m.humidite,    u:'%'},
                {k:'Gaz',         v:m.gaz,          u:'ppm'},
            ];
            document.getElementById('sensorDetail').innerHTML = sensors.map(function(s){
                return '<div class="detail-row"><span class="k">'+s.k+'</span>'
                     + '<span class="val '+(s.v!=null?'g':'')+'">'+(s.v!=null?s.v+' '+s.u:'—')+'</span></div>';
            }).join('');
        }).catch(function(){});

}

function loadCharts() {
    Promise.all([
        fetch('/api/mesures-horaires').then(function(r){ return r.json(); }),
        fetch('/api/live-data').then(function(r){ return r.ok ? r.json() : null; }).catch(function(){ return null; })
    ]).then(function(res) {
        buildCharts(Array.isArray(res[0]) ? res[0] : [], res[1]);
    }).catch(function() {
        buildCharts([], null);
    });
}

loadStats();
loadCharts();
setInterval(loadStats,  30000);
setInterval(loadCharts, 2000);
</script>
@endsection
