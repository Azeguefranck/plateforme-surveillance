@extends('layouts.app')

@section('content')

<style>
@keyframes fadeIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.stat-wrap{animation:fadeIn .4s ease;}

.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;}
.page-title{font-size:20px;font-weight:bold;color:var(--text);display:flex;align-items:center;gap:10px;}
.page-title i{color:var(--accent);}

/* ── KPI cards ── */
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:22px;}
.kpi-card{
  background:var(--card);border:1px solid var(--border);border-radius:14px;
  padding:18px 20px;display:flex;flex-direction:column;gap:6px;
  transition:border-color .2s,transform .15s;
}
.kpi-card:hover{border-color:var(--accent);transform:translateY(-2px);}
.kpi-label{font-size:11px;font-weight:bold;color:var(--muted);letter-spacing:1px;text-transform:uppercase;}
.kpi-val{font-size:30px;font-weight:bold;color:var(--text);line-height:1;}
.kpi-sub{font-size:12px;color:var(--muted);}
.kpi-card.accent .kpi-val{color:var(--accent);}
.kpi-card.danger .kpi-val{color:var(--danger);}
.kpi-card.warn   .kpi-val{color:#d97706;}
.kpi-card.info   .kpi-val{color:var(--info);}

/* ── Période selector ── */
.period-bar{
  display:flex;gap:8px;align-items:center;margin-bottom:20px;flex-wrap:wrap;
}
.period-btn{
  padding:7px 16px;border-radius:8px;border:1px solid var(--border);
  background:var(--card);color:var(--muted);cursor:pointer;font-size:12px;font-weight:bold;transition:.2s;
}
.period-btn:hover,.period-btn.active{background:rgba(47,168,79,.15);border-color:var(--accent);color:var(--accent);}
.period-divider{width:1px;height:24px;background:var(--border);}

/* ── Chart panels ── */
.charts-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:18px;margin-bottom:22px;}
.chart-panel{
  background:var(--card);border:1px solid var(--border);border-radius:16px;
  padding:20px;
}
.chart-title{font-size:13px;font-weight:bold;color:var(--accent);letter-spacing:.5px;text-transform:uppercase;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
canvas{max-width:100%;}

/* ── Averages table ── */
.avg-table{width:100%;border-collapse:collapse;font-size:13px;}
.avg-table th{background:#091527;padding:11px 14px;text-align:left;color:var(--muted);font-size:11px;letter-spacing:1.5px;text-transform:uppercase;border-bottom:1px solid var(--border);}
.avg-table td{padding:11px 14px;border-bottom:1px solid rgba(24,38,64,.6);color:var(--text);}
.avg-table tr:last-child td{border-bottom:none;}
.avg-table tbody tr:hover td{background:rgba(47,168,79,.03);}

.loading-msg{text-align:center;padding:40px;color:var(--muted);font-size:13px;}

@media(max-width:600px){
  .kpi-grid{grid-template-columns:1fr 1fr;}
  .charts-grid{grid-template-columns:1fr;}
}
</style>

<div class="stat-wrap">

<div class="page-header">
  <div class="page-title"><i class="fa-solid fa-chart-line"></i> Statistiques</div>
  <div style="display:flex;gap:8px;flex-wrap:wrap;">
    <button onclick="exportStats()" style="background:rgba(47,168,79,.15);color:var(--accent);border:1px solid var(--accent);border-radius:8px;padding:8px 16px;cursor:pointer;font-size:12px;font-weight:bold;">
      <i class="fa-solid fa-file-csv"></i> Export CSV
    </button>
    <button onclick="chargerTout()" style="background:var(--card);color:var(--muted);border:1px solid var(--border);border-radius:8px;padding:8px 14px;cursor:pointer;font-size:12px;">
      <i class="fa-solid fa-rotate"></i> Actualiser
    </button>
  </div>
</div>

<!-- KPI -->
<div class="kpi-grid">
  <div class="kpi-card accent">
    <div class="kpi-label">Total mesures</div>
    <div class="kpi-val" id="kpi-mesures">—</div>
    <div class="kpi-sub">Capteurs enregistrés</div>
  </div>
  <div class="kpi-card danger">
    <div class="kpi-label">Alertes critiques</div>
    <div class="kpi-val" id="kpi-crit">—</div>
    <div class="kpi-sub">Niveau CRITIQUE</div>
  </div>
  <div class="kpi-card warn">
    <div class="kpi-label">Alertes aujourd'hui</div>
    <div class="kpi-val" id="kpi-today">—</div>
    <div class="kpi-sub">Dernières 24h</div>
  </div>
  <div class="kpi-card info">
    <div class="kpi-label">Serveurs actifs</div>
    <div class="kpi-val" id="kpi-srv">—</div>
    <div class="kpi-sub">/ <span id="kpi-srv-total">—</span> total</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Temp. moyenne</div>
    <div class="kpi-val" id="kpi-temp">—</div>
    <div class="kpi-sub">°C (historique)</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">SMS envoyés</div>
    <div class="kpi-val" id="kpi-sms">—</div>
    <div class="kpi-sub">Total GSM</div>
  </div>
</div>

<!-- Période -->
<div class="period-bar">
  <span style="font-size:12px;color:var(--muted);font-weight:bold;">Période :</span>
  <button class="period-btn active" onclick="setPeriode(1,this)">1h</button>
  <button class="period-btn" onclick="setPeriode(6,this)">6h</button>
  <button class="period-btn" onclick="setPeriode(24,this)">24h</button>
  <button class="period-btn" onclick="setPeriode(72,this)">3j</button>
  <button class="period-btn" onclick="setPeriode(168,this)">7j</button>
  <div class="period-divider"></div>
  <span id="chart-status" style="font-size:12px;color:var(--muted);"></span>
</div>

<!-- Graphiques -->
<div class="charts-grid">
  <div class="chart-panel">
    <div class="chart-title"><i class="fa-solid fa-temperature-three-quarters"></i> Température (°C)</div>
    <canvas id="chart-temp" height="160"></canvas>
  </div>
  <div class="chart-panel">
    <div class="chart-title"><i class="fa-solid fa-droplet"></i> Humidité (%)</div>
    <canvas id="chart-hum" height="160"></canvas>
  </div>
  <div class="chart-panel">
    <div class="chart-title"><i class="fa-solid fa-wind"></i> Gaz / CO₂ (ppm)</div>
    <canvas id="chart-gaz" height="160"></canvas>
  </div>
  <div class="chart-panel">
    <div class="chart-title"><i class="fa-solid fa-bolt"></i> Courant (A) & Puissance (W)</div>
    <canvas id="chart-power" height="160"></canvas>
  </div>
</div>

<!-- Tableau moyennes -->
<div class="chart-panel" style="margin-bottom:20px;">
  <div class="chart-title"><i class="fa-solid fa-table"></i> Récapitulatif statistique</div>
  <div id="avg-container">
    <div class="loading-msg">Chargement...</div>
  </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let charts = {};
let periodHeures = 1;
let statsData = {};

const chartDefaults = {
  responsive: true,
  animation: { duration: 600 },
  plugins: { legend: { display: false } },
  scales: {
    x: {
      ticks: { color: '#6b7fa0', font: { size: 10 }, maxTicksLimit: 8, maxRotation: 0 },
      grid: { color: 'rgba(24,38,64,.5)' }
    },
    y: {
      ticks: { color: '#6b7fa0', font: { size: 10 } },
      grid: { color: 'rgba(24,38,64,.5)' }
    }
  }
};

function makeChart(id, color, field, data) {
  const labels = data.map(m => {
    const d = new Date(m.created_at);
    return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
  });
  const vals = data.map(m => parseFloat(m[field] || 0));

  if (charts[id]) charts[id].destroy();

  const ctx = document.getElementById(id).getContext('2d');
  charts[id] = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        data: vals,
        borderColor: color,
        backgroundColor: color + '18',
        borderWidth: 2,
        pointRadius: data.length > 100 ? 0 : 2,
        pointBackgroundColor: color,
        fill: true,
        tension: 0.4,
      }]
    },
    options: { ...chartDefaults }
  });
}

function makePowerChart(data) {
  const labels = data.map(m => {
    const d = new Date(m.created_at);
    return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
  });
  const courant  = data.map(m => parseFloat(m.courant  || 0));
  const puisance = data.map(m => parseFloat(m.puissance || 0));

  if (charts['power']) charts['power'].destroy();
  const ctx = document.getElementById('chart-power').getContext('2d');
  charts['power'] = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        { label: 'Courant (A)', data: courant,  borderColor: '#33ff88', backgroundColor: '#33ff8818', borderWidth: 2, pointRadius: data.length > 100 ? 0 : 2, fill: true, tension: 0.4, yAxisID: 'y' },
        { label: 'Puissance (W)', data: puisance, borderColor: '#bb66ff', backgroundColor: '#bb66ff18', borderWidth: 2, pointRadius: data.length > 100 ? 0 : 2, fill: true, tension: 0.4, yAxisID: 'y2' },
      ]
    },
    options: {
      ...chartDefaults,
      plugins: { legend: { display: true, labels: { color: '#6b7fa0', font: { size: 11 } } } },
      scales: {
        ...chartDefaults.scales,
        y:  { ...chartDefaults.scales.y, position: 'left' },
        y2: { ...chartDefaults.scales.y, position: 'right', grid: { drawOnChartArea: false } },
      }
    }
  });
}

function setPeriode(h, btn) {
  periodHeures = h;
  document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  chargerGraphiques();
}

async function chargerGraphiques() {
  document.getElementById('chart-status').textContent = 'Chargement...';
  try {
    const r = await fetch(`/api/stats/graphiques?heures=${periodHeures}`);
    const data = await r.json();
    if (!data.length) {
      document.getElementById('chart-status').textContent = 'Aucune donnée pour cette période';
      return;
    }
    document.getElementById('chart-status').textContent = `${data.length} mesures affichées`;
    makeChart('chart-temp',  '#ff5733', 'temperature', data);
    makeChart('chart-hum',   '#33b5ff', 'humidite',    data);
    makeChart('chart-gaz',   '#ffd633', 'gaz',         data);
    makePowerChart(data);
  } catch(e) {
    document.getElementById('chart-status').textContent = 'Données indisponibles';
  }
}

async function chargerKPI() {
  try {
    const r = await fetch('/api/stats/resume');
    const d = await r.json();
    statsData = d;
    document.getElementById('kpi-mesures').textContent = (d.total_mesures || 0).toLocaleString('fr-FR');
    document.getElementById('kpi-crit').textContent    = d.alertes_crit   || 0;
    document.getElementById('kpi-today').textContent   = d.alertes_today  || 0;
    document.getElementById('kpi-srv').textContent     = d.serveurs_actifs || 0;
    document.getElementById('kpi-srv-total').textContent = d.total_serveurs || 0;
    document.getElementById('kpi-temp').textContent    = (d.avg_temp || 0).toFixed(1);
    document.getElementById('kpi-sms').textContent     = d.total_sms || 0;

    document.getElementById('avg-container').innerHTML = `
      <table class="avg-table">
        <thead>
          <tr>
            <th>Indicateur</th>
            <th>Moyenne</th>
            <th>Maximum</th>
            <th>Mesures aujourd'hui</th>
            <th>Total alertes</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="color:#ff5733;font-weight:bold;">🌡️ Température</td>
            <td>${(d.avg_temp||0).toFixed(1)} °C</td>
            <td style="color:#e74c3c;">${(d.max_temp||0).toFixed(1)} °C</td>
            <td rowspan="5" style="text-align:center;vertical-align:middle;font-size:22px;font-weight:bold;color:var(--accent);">${d.mesures_today||0}</td>
            <td rowspan="5" style="text-align:center;vertical-align:middle;font-size:22px;font-weight:bold;color:#d97706;">${d.total_alertes||0}</td>
          </tr>
          <tr>
            <td style="color:#33b5ff;font-weight:bold;">💧 Humidité</td>
            <td>${(d.avg_hum||0).toFixed(1)} %</td>
            <td>—</td>
          </tr>
          <tr>
            <td style="color:#ffd633;font-weight:bold;">🌬️ Gaz / CO₂</td>
            <td>${Math.round(d.avg_gaz||0)} ppm</td>
            <td style="color:#e74c3c;">${Math.round(d.max_gaz||0)} ppm</td>
          </tr>
          <tr>
            <td style="color:#33ff88;font-weight:bold;">⚡ Courant</td>
            <td>${(d.avg_courant||0).toFixed(2)} A</td>
            <td>—</td>
          </tr>
          <tr>
            <td style="color:#bb66ff;font-weight:bold;">🔌 Puissance</td>
            <td>${Math.round(d.avg_puissance||0)} W</td>
            <td>—</td>
          </tr>
        </tbody>
      </table>`;
  } catch(e) {
    document.getElementById('avg-container').innerHTML = '<div class="loading-msg" style="color:#e74c3c">Impossible de charger les statistiques</div>';
  }
}

function chargerTout() {
  chargerKPI();
  chargerGraphiques();
}

function exportStats() {
  const rows = [
    ['Indicateur','Valeur'],
    ['Total mesures', statsData.total_mesures||0],
    ['Mesures aujourd\'hui', statsData.mesures_today||0],
    ['Total alertes', statsData.total_alertes||0],
    ['Alertes critiques', statsData.alertes_crit||0],
    ['Alertes aujourd\'hui', statsData.alertes_today||0],
    ['Temp. moyenne', (statsData.avg_temp||0).toFixed(1)+' °C'],
    ['Temp. max', (statsData.max_temp||0).toFixed(1)+' °C'],
    ['Hum. moyenne', (statsData.avg_hum||0).toFixed(1)+' %'],
    ['Gaz moyen', Math.round(statsData.avg_gaz||0)+' ppm'],
    ['Courant moyen', (statsData.avg_courant||0).toFixed(2)+' A'],
    ['Puissance moyenne', Math.round(statsData.avg_puissance||0)+' W'],
    ['Serveurs actifs', statsData.serveurs_actifs||0],
    ['Total serveurs', statsData.total_serveurs||0],
    ['Total SMS', statsData.total_sms||0],
  ];
  const csv = rows.map(r => r.join(',')).join('\n');
  const a = document.createElement('a');
  a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
  a.download = 'statistiques_' + new Date().toISOString().slice(0,10) + '.csv';
  a.click();
}

chargerTout();
setInterval(chargerKPI, 30000);
</script>

@endsection
