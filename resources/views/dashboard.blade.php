@extends('layouts.app')

@section('content')

<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#060d1f;color:#e0e8ff;font-family:'Segoe UI',Arial,sans-serif}

/* ── Header ── */
.dash-header{
  display:flex;justify-content:space-between;align-items:center;
  padding:18px 0 28px;flex-wrap:wrap;gap:12px;
}
.dash-title{
  font-size:26px;font-weight:700;letter-spacing:1px;
  color:#fff;text-shadow:0 0 18px rgba(51,255,136,.5);
}
.dash-title span{color:#33ff88}
.dash-live{
  display:flex;align-items:center;gap:8px;
  font-size:13px;color:#33ff88;font-weight:600;letter-spacing:.5px;
}
.dot{width:10px;height:10px;border-radius:50%;background:#33ff88;
  animation:pulse 1.2s infinite;box-shadow:0 0 8px #33ff88}
@keyframes pulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:.6}}

/* ── Stats row ── */
.stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:28px}
.stat-card{
  background:linear-gradient(135deg,#0e1a38,#111c3d);
  border:1px solid #1e2f5a;border-radius:14px;
  padding:18px;text-align:center;transition:.3s;
  position:relative;overflow:hidden;
}
.stat-card::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(circle at 50% 0%,rgba(51,255,136,.07),transparent 70%);
}
.stat-card:hover{transform:translateY(-4px);border-color:#33ff88;box-shadow:0 0 20px rgba(51,255,136,.15)}
.stat-num{font-size:32px;font-weight:700;color:#33ff88;line-height:1}
.stat-label{font-size:12px;color:#8899cc;margin-top:6px;letter-spacing:.5px;text-transform:uppercase}
.stat-card.warning .stat-num{color:#ffd633}
.stat-card.danger  .stat-num{color:#ff5733}

/* ── Gauge grid ── */
.gauges{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:28px}
.gauge-card{
  background:linear-gradient(135deg,#0e1a38,#111c3d);
  border:1px solid #1e2f5a;border-radius:18px;
  padding:22px 16px;text-align:center;transition:.3s;
  position:relative;overflow:hidden;
}
.gauge-card:hover{transform:translateY(-4px)}
.gauge-card.alerte-warning{border-color:#ffd633;box-shadow:0 0 20px rgba(255,214,51,.2)}
.gauge-card.alerte-critique{border-color:#ff5733;box-shadow:0 0 25px rgba(255,87,51,.3);animation:cardPulse 1.5s infinite}
@keyframes cardPulse{0%,100%{box-shadow:0 0 25px rgba(255,87,51,.3)}50%{box-shadow:0 0 45px rgba(255,87,51,.55)}}

.gauge-label{font-size:12px;font-weight:700;letter-spacing:1.5px;color:#8899cc;text-transform:uppercase;margin-bottom:14px}
.gauge-ring{
  width:130px;height:130px;border-radius:50%;margin:0 auto 14px;
  display:flex;flex-direction:column;justify-content:center;align-items:center;
  border:12px solid #1e2f5a;background:#0a1225;
  transition:.4s;position:relative;
}
.gauge-val{font-size:26px;font-weight:700;color:#fff;line-height:1}
.gauge-unit{font-size:12px;color:#8899cc;margin-top:3px}
.gauge-bar{height:5px;border-radius:3px;background:#1e2f5a;margin-top:10px;overflow:hidden}
.gauge-fill{height:100%;border-radius:3px;transition:width .6s ease,background .4s}

/* Couleurs dynamiques */
.ok    .gauge-ring{border-color:#33ff88;box-shadow:0 0 18px rgba(51,255,136,.25)}
.ok    .gauge-val {color:#33ff88}
.ok    .gauge-fill{background:#33ff88}
.warn  .gauge-ring{border-color:#ffd633;box-shadow:0 0 18px rgba(255,214,51,.3)}
.warn  .gauge-val {color:#ffd633}
.warn  .gauge-fill{background:#ffd633}
.crit  .gauge-ring{border-color:#ff5733;box-shadow:0 0 25px rgba(255,87,51,.4)}
.crit  .gauge-val {color:#ff5733}
.crit  .gauge-fill{background:#ff5733}

/* PIR */
.pir-badge{
  display:inline-block;padding:6px 18px;border-radius:50px;
  font-size:13px;font-weight:700;letter-spacing:.5px;margin-top:8px;
}
.pir-ok  {background:rgba(51,255,136,.1);color:#33ff88;border:1px solid #33ff88}
.pir-det {background:rgba(255,87,51,.15);color:#ff5733;border:1px solid #ff5733;animation:pir-flash .8s infinite}
@keyframes pir-flash{0%,100%{opacity:1}50%{opacity:.5}}

/* ── Graphiques ── */
.charts-row{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:28px}
@media(max-width:860px){.charts-row{grid-template-columns:1fr}}
.chart-card{
  background:linear-gradient(135deg,#0e1a38,#111c3d);
  border:1px solid #1e2f5a;border-radius:18px;padding:22px;
}
.chart-card h3{font-size:14px;color:#8899cc;letter-spacing:1px;text-transform:uppercase;margin-bottom:18px}
canvas{max-height:240px}

/* ── Alertes ── */
.alertes-card{
  background:linear-gradient(135deg,#0e1a38,#111c3d);
  border:1px solid #1e2f5a;border-radius:18px;padding:22px;margin-bottom:28px;
}
.alertes-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.alertes-header h3{font-size:14px;color:#8899cc;letter-spacing:1px;text-transform:uppercase}
.btn-lire-tout{
  font-size:11px;padding:5px 12px;border-radius:8px;border:1px solid #33ff88;
  color:#33ff88;background:transparent;cursor:pointer;transition:.2s;
}
.btn-lire-tout:hover{background:rgba(51,255,136,.1)}
#alertes-list{max-height:300px;overflow-y:auto;display:flex;flex-direction:column;gap:8px}
#alertes-list::-webkit-scrollbar{width:4px}
#alertes-list::-webkit-scrollbar-track{background:#0a1225}
#alertes-list::-webkit-scrollbar-thumb{background:#33ff88;border-radius:2px}
.alerte-item{
  display:flex;align-items:flex-start;gap:12px;padding:12px 14px;
  border-radius:10px;border-left:3px solid;transition:.2s;
}
.alerte-item.critique{background:rgba(255,87,51,.07);border-color:#ff5733}
.alerte-item.warning {background:rgba(255,214,51,.07);border-color:#ffd633}
.alerte-item.info    {background:rgba(51,255,136,.07);border-color:#33ff88}
.alerte-item.non-lu {filter:brightness(1.15)}
.alerte-icon{font-size:18px;margin-top:1px;flex-shrink:0}
.alerte-msg {font-size:13px;color:#c7d2ff;line-height:1.4}
.alerte-time{font-size:11px;color:#5a6a99;margin-top:3px}
.alerte-vide{text-align:center;color:#5a6a99;padding:30px;font-size:14px}

/* ── Status bar ── */
.status-bar{
  background:linear-gradient(135deg,#0e1a38,#111c3d);
  border:1px solid #1e2f5a;border-radius:14px;
  padding:16px 22px;display:flex;flex-wrap:wrap;gap:20px;align-items:center;
}
.status-item{font-size:13px;display:flex;align-items:center;gap:8px;color:#8899cc}
.status-item strong{color:#fff}
.badge{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.5px}
.badge-ok{background:rgba(51,255,136,.15);color:#33ff88;border:1px solid rgba(51,255,136,.3)}
.badge-warn{background:rgba(255,214,51,.15);color:#ffd633;border:1px solid rgba(255,214,51,.3)}
.badge-crit{background:rgba(255,87,51,.15);color:#ff5733;border:1px solid rgba(255,87,51,.3)}
</style>


<div class="dash-header">
  <div class="dash-title">⚡ Dashboard <span>IoT</span> Temps Réel</div>
  <div class="dash-live">
    <div class="dot"></div>
    EN DIRECT — <span id="last-update">--</span>
  </div>
</div>


<!-- Stats -->
<div class="stats-row" id="stats-row">
  <div class="stat-card">
    <div class="stat-num" id="stat-mesures">—</div>
    <div class="stat-label">Mesures totales</div>
  </div>
  <div class="stat-card warning" id="sc-warning">
    <div class="stat-num" id="stat-alertes-w">—</div>
    <div class="stat-label">Alertes warning</div>
  </div>
  <div class="stat-card danger" id="sc-critique">
    <div class="stat-num" id="stat-alertes-c">—</div>
    <div class="stat-label">Alertes critiques</div>
  </div>
  <div class="stat-card">
    <div class="stat-num" id="stat-users">—</div>
    <div class="stat-label">Utilisateurs actifs</div>
  </div>
  <div class="stat-card">
    <div class="stat-num" id="stat-nonlues">—</div>
    <div class="stat-label">Alertes non lues</div>
  </div>
</div>


<!-- Jauges capteurs -->
<div class="gauges">

  <div class="gauge-card ok" id="card-temperature">
    <div class="gauge-label">Température</div>
    <div class="gauge-ring">
      <span class="gauge-val" id="g-temperature">0</span>
      <span class="gauge-unit">°C</span>
    </div>
    <div class="gauge-bar"><div class="gauge-fill" id="f-temperature" style="width:0%"></div></div>
  </div>

  <div class="gauge-card ok" id="card-humidite">
    <div class="gauge-label">Humidité</div>
    <div class="gauge-ring">
      <span class="gauge-val" id="g-humidite">0</span>
      <span class="gauge-unit">%</span>
    </div>
    <div class="gauge-bar"><div class="gauge-fill" id="f-humidite" style="width:0%"></div></div>
  </div>

  <div class="gauge-card ok" id="card-gaz">
    <div class="gauge-label">Gaz / Qualité air</div>
    <div class="gauge-ring">
      <span class="gauge-val" id="g-gaz">0</span>
      <span class="gauge-unit">ppm</span>
    </div>
    <div class="gauge-bar"><div class="gauge-fill" id="f-gaz" style="width:0%"></div></div>
  </div>

  <div class="gauge-card ok" id="card-courant">
    <div class="gauge-label">Courant</div>
    <div class="gauge-ring">
      <span class="gauge-val" id="g-courant">0</span>
      <span class="gauge-unit">A</span>
    </div>
    <div class="gauge-bar"><div class="gauge-fill" id="f-courant" style="width:0%"></div></div>
  </div>

  <div class="gauge-card ok" id="card-puissance">
    <div class="gauge-label">Puissance</div>
    <div class="gauge-ring">
      <span class="gauge-val" id="g-puissance">0</span>
      <span class="gauge-unit">W</span>
    </div>
    <div class="gauge-bar"><div class="gauge-fill" id="f-puissance" style="width:0%"></div></div>
  </div>

  <div class="gauge-card ok" id="card-tension">
    <div class="gauge-label">Tension</div>
    <div class="gauge-ring">
      <span class="gauge-val" id="g-tension">0</span>
      <span class="gauge-unit">V</span>
    </div>
    <div class="gauge-bar"><div class="gauge-fill" id="f-tension" style="width:0%"></div></div>
  </div>

  <div class="gauge-card ok" id="card-pir" style="display:flex;flex-direction:column;justify-content:center;align-items:center">
    <div class="gauge-label">Détecteur PIR</div>
    <div style="font-size:48px;margin:14px 0">🚶</div>
    <div class="pir-badge pir-ok" id="pir-badge">AUCUN MOUVEMENT</div>
  </div>

</div>


<!-- Graphiques -->
<div class="charts-row">
  <div class="chart-card">
    <h3>🌡 Température &amp; Humidité &amp; Gaz</h3>
    <canvas id="chart1"></canvas>
  </div>
  <div class="chart-card">
    <h3>⚡ Courant &amp; Puissance &amp; Tension</h3>
    <canvas id="chart2"></canvas>
  </div>
</div>


<!-- Alertes récentes -->
<div class="alertes-card">
  <div class="alertes-header">
    <h3>🔔 Alertes récentes</h3>
    <button class="btn-lire-tout" onclick="lireTout()">Tout marquer lu</button>
  </div>
  <div id="alertes-list">
    <div class="alerte-vide">Chargement des alertes…</div>
  </div>
</div>


<!-- Barre de statut -->
<div class="status-bar">
  <div class="status-item">État système : <span class="badge badge-ok">EN LIGNE</span></div>
  <div class="status-item">Alerte active : <strong id="alerte-active"><span class="badge badge-ok">AUCUNE</span></strong></div>
  <div class="status-item">Dernière mesure : <strong id="last-mesure">--</strong></div>
  <div class="status-item" style="margin-left:auto;font-size:11px;color:#3a4a6a">SupServer IoT v2.0</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ── Seuils JS (miroir PHP) ──────────────────────────────
const SEUILS = {
  temperature: { warn: 35, crit: 40, max: 80  },
  humidite:    { warn: 75, crit: 85, max: 100 },
  gaz:         { warn: 300, crit: 500, max: 1000 },
  courant:     { warn: 10, crit: 15, max: 30 },
  puissance:   { warn: 3000, crit: 5000, max: 8000 },
  tension:     { warn: 240, crit: 260, max: 300 },
};

// ── Données graphiques ──────────────────────────────────
const MAX_POINTS = 20;
const labels = [];
const D = { temperature:[], humidite:[], gaz:[], courant:[], puissance:[], tension:[] };

const chartOpts = (unit) => ({
  responsive: true,
  animation: false,
  interaction: { mode: 'index', intersect: false },
  plugins: { legend: { labels: { color: '#8899cc', font: { size: 11 } } } },
  scales: {
    x: { ticks: { color: '#5a6a99', maxTicksLimit: 8 }, grid: { color: '#1a2540' } },
    y: { ticks: { color: '#5a6a99', callback: v => v + unit }, grid: { color: '#1a2540' } }
  }
});

const chart1 = new Chart(document.getElementById('chart1'), {
  type: 'line',
  data: {
    labels,
    datasets: [
      { label:'Temp (°C)', data: D.temperature, borderColor:'#ff5733', backgroundColor:'rgba(255,87,51,.08)', borderWidth:2, fill:true, tension:.4, pointRadius:2 },
      { label:'Humidité (%)', data: D.humidite, borderColor:'#33b5ff', backgroundColor:'rgba(51,181,255,.08)', borderWidth:2, fill:true, tension:.4, pointRadius:2 },
      { label:'Gaz (ppm)', data: D.gaz, borderColor:'#ffd633', backgroundColor:'rgba(255,214,51,.08)', borderWidth:2, fill:true, tension:.4, pointRadius:2 },
    ]
  },
  options: chartOpts('')
});

const chart2 = new Chart(document.getElementById('chart2'), {
  type: 'line',
  data: {
    labels,
    datasets: [
      { label:'Courant (A)', data: D.courant, borderColor:'#33ff88', backgroundColor:'rgba(51,255,136,.08)', borderWidth:2, fill:true, tension:.4, pointRadius:2 },
      { label:'Puissance (W)', data: D.puissance, borderColor:'#bb66ff', backgroundColor:'rgba(187,102,255,.08)', borderWidth:2, fill:true, tension:.4, pointRadius:2 },
      { label:'Tension (V)', data: D.tension, borderColor:'#ff9933', backgroundColor:'rgba(255,153,51,.08)', borderWidth:2, fill:true, tension:.4, pointRadius:2 },
    ]
  },
  options: chartOpts('')
});

// ── Mise à jour jauge ───────────────────────────────────
function majJauge(nom, val) {
  const s = SEUILS[nom];
  const el = document.getElementById('g-' + nom);
  const fill = document.getElementById('f-' + nom);
  const card = document.getElementById('card-' + nom);
  if (!el || !s) return;

  el.textContent = val;
  const pct = Math.min(100, (val / s.max) * 100);
  fill.style.width = pct + '%';

  const level = val >= s.crit ? 'crit' : val >= s.warn ? 'warn' : 'ok';
  card.className = 'gauge-card ' + level;
  if (val >= s.crit) card.classList.add('alerte-critique');
  else if (val >= s.warn) card.classList.add('alerte-warning');
}

// ── Push données graphique ──────────────────────────────
function pushData(data) {
  const now = new Date();
  const t = now.getHours().toString().padStart(2,'0') + ':' +
            now.getMinutes().toString().padStart(2,'0') + ':' +
            now.getSeconds().toString().padStart(2,'0');
  labels.push(t);
  D.temperature.push(data.temperature ?? 0);
  D.humidite.push(data.humidite ?? 0);
  D.gaz.push(data.gaz ?? 0);
  D.courant.push(data.courant ?? 0);
  D.puissance.push(data.puissance ?? 0);
  D.tension.push(data.tension ?? 0);

  if (labels.length > MAX_POINTS) {
    labels.shift();
    Object.values(D).forEach(arr => arr.shift());
  }
  chart1.update();
  chart2.update();
}

// ── Polling dashboard-data ──────────────────────────────
function pollSensors() {
  fetch('/api/dashboard-data')
    .then(r => r.json())
    .then(data => {
      majJauge('temperature', parseFloat(data.temperature) || 0);
      majJauge('humidite',    parseFloat(data.humidite)    || 0);
      majJauge('gaz',         parseFloat(data.gaz)         || 0);
      majJauge('courant',     parseFloat(data.courant)      || 0);
      majJauge('puissance',   parseFloat(data.puissance)   || 0);
      majJauge('tension',     parseFloat(data.tension)     || 0);

      const pir = data.pir == 1 || data.pir === true || data.pir === 'true';
      const pirBadge = document.getElementById('pir-badge');
      const pirCard  = document.getElementById('card-pir');
      pirBadge.className = pir ? 'pir-badge pir-det' : 'pir-badge pir-ok';
      pirBadge.textContent = pir ? 'MOUVEMENT DÉTECTÉ' : 'AUCUN MOUVEMENT';
      pirCard.className = 'gauge-card ' + (pir ? 'crit alerte-critique' : 'ok');

      // Alerte active
      let alerteActive = '';
      if (parseFloat(data.temperature) >= 40) alerteActive = 'TEMPÉRATURE CRITIQUE';
      else if (parseFloat(data.gaz) >= 500) alerteActive = 'GAZ CRITIQUE';
      else if (parseFloat(data.temperature) >= 35) alerteActive = 'TEMPÉRATURE ÉLEVÉE';
      else if (parseFloat(data.gaz) >= 300) alerteActive = 'GAZ ÉLEVÉ';
      else if (pir) alerteActive = 'MOUVEMENT DÉTECTÉ';

      const alerteEl = document.getElementById('alerte-active');
      if (alerteActive) {
        alerteEl.innerHTML = `<span class="badge badge-crit">${alerteActive}</span>`;
      } else {
        alerteEl.innerHTML = `<span class="badge badge-ok">AUCUNE</span>`;
      }

      document.getElementById('last-update').textContent = new Date().toLocaleTimeString('fr-FR');
      if (data.created_at) document.getElementById('last-mesure').textContent = data.created_at.replace('T',' ').substring(0,19);

      pushData(data);
    })
    .catch(() => {});
}

// ── Polling alertes récentes (5s) ──────────────────────
function pollAlertes() {
  fetch('/api/alertes-recentes')
    .then(r => r.json())
    .then(alertes => {
      const list = document.getElementById('alertes-list');
      if (!alertes.length) {
        list.innerHTML = '<div class="alerte-vide">Aucune alerte enregistrée</div>';
        return;
      }
      list.innerHTML = alertes.map(a => {
        const icon  = a.niveau === 'critique' ? '🔴' : a.niveau === 'warning' ? '🟡' : '🟢';
        const niv   = a.niveau === 'critique' ? 'critique' : a.niveau === 'warning' ? 'warning' : 'info';
        const nonLu = a.lu == 0 ? ' non-lu' : '';
        const date  = (a.created_at || '').substring(0, 16).replace('T', ' ');
        return `<div class="alerte-item ${niv}${nonLu}" data-id="${a.id}">
          <span class="alerte-icon">${icon}</span>
          <div>
            <div class="alerte-msg">${a.message}${a.valeur ? ' — <strong>' + a.valeur + '</strong>' : ''}</div>
            <div class="alerte-time">${date}</div>
          </div>
        </div>`;
      }).join('');
    })
    .catch(() => {});
}

// ── Polling stats (10s) ────────────────────────────────
function pollStats() {
  fetch('/api/stats')
    .then(r => r.json())
    .then(s => {
      document.getElementById('stat-mesures').textContent   = s.totalMesures ?? '—';
      document.getElementById('stat-alertes-w').textContent = s.alertesWarning ?? '—';
      document.getElementById('stat-alertes-c').textContent = s.alertesCritiques ?? '—';
      document.getElementById('stat-users').textContent     = s.totalUtilisateurs ?? '—';
      document.getElementById('stat-nonlues').textContent   = s.alertesNonLues ?? '—';
    })
    .catch(() => {});
}

// ── Marquer alertes lues ───────────────────────────────
function lireTout() {
  fetch('/api/alertes/lire', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
    body: JSON.stringify({ id: 'all' })
  }).then(() => pollAlertes());
}

// ── Démarrage ──────────────────────────────────────────
pollSensors();
pollAlertes();
pollStats();
setInterval(pollSensors, 1000);
setInterval(pollAlertes, 5000);
setInterval(pollStats,  10000);
</script>

@endsection
