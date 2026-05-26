@extends('layouts.app')

@section('content')


<style>
:root{
  --vert:#39ff14;
  --amber:#f59e0b;
  --rouge:#ef4444;
  --bleu:#33b5ff;
  --violet:#bb66ff;
  --bg:#050816;
  --card:#111c3d;
  --card2:#0b1225;
}

/* ── En-tête IoT ── */
.iot-header{
  display:flex;align-items:center;gap:16px;
  background:var(--card);border-radius:14px;
  padding:14px 20px;margin-bottom:22px;flex-wrap:wrap;
}
.live-dot{
  width:10px;height:10px;border-radius:50%;
  background:var(--vert);
  box-shadow:0 0 8px var(--vert);
  animation:blink 1s infinite;
  flex-shrink:0;
}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.live-label{
  font-size:12px;font-weight:bold;letter-spacing:2px;
  color:var(--vert);text-transform:uppercase;
}
.salle-badge{
  background:#1f2d5e;padding:6px 14px;border-radius:20px;
  font-size:13px;font-weight:bold;color:white;
}
.niveau-badge{
  padding:6px 14px;border-radius:20px;font-size:12px;
  font-weight:bold;letter-spacing:1px;
  background:#0f3020;color:var(--vert);
  border:1px solid var(--vert);
  transition:.3s;
}
.niveau-badge.warn{background:#3d2800;color:var(--amber);border-color:var(--amber);}
.niveau-badge.crit{background:#3d0000;color:var(--rouge);border-color:var(--rouge);animation:pulse-border 1s infinite;}
@keyframes pulse-border{0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,.4)}50%{box-shadow:0 0 0 6px rgba(239,68,68,0)}}
.iot-time{margin-left:auto;font-size:20px;font-weight:bold;color:#00ffcc;font-family:monospace;}

/* ── Grille capteurs ── */
.sensors-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(165px,1fr));
  gap:16px;margin-bottom:22px;
}

.sensor-card{
  background:var(--card);border-radius:16px;padding:18px;
  text-align:center;border:1px solid #1f2d5e;
  transition:border-color .3s,box-shadow .3s;
  position:relative;overflow:hidden;
}
.sensor-card::before{
  content:'';position:absolute;inset:0;border-radius:16px;
  background:radial-gradient(circle at 50% 0,rgba(57,255,20,.05),transparent 70%);
  pointer-events:none;
}
.sensor-card.warn{border-color:var(--amber);box-shadow:0 0 12px rgba(245,158,11,.15);}
.sensor-card.crit{border-color:var(--rouge);box-shadow:0 0 16px rgba(239,68,68,.25);animation:card-pulse 1.5s infinite;}
@keyframes card-pulse{0%,100%{box-shadow:0 0 16px rgba(239,68,68,.25)}50%{box-shadow:0 0 28px rgba(239,68,68,.5)}}

.sensor-label{font-size:11px;font-weight:bold;letter-spacing:1.5px;color:#6b7280;margin-bottom:10px;}

/* SVG Gauge */
.gauge-wrap{position:relative;width:100px;height:100px;margin:0 auto 10px;}
.gauge-wrap svg{width:100%;height:100%;}
.gauge-bg{fill:none;stroke:#1f2d5e;stroke-width:7;}
.gauge-arc{
  fill:none;stroke:var(--vert);stroke-width:7;
  stroke-linecap:round;
  stroke-dasharray:251.2;stroke-dashoffset:251.2;
  transform:rotate(-90deg);transform-origin:50px 50px;
  transition:stroke-dashoffset .8s ease,stroke .3s;
}
.gauge-text{
  font-size:18px;font-weight:bold;fill:white;
  dominant-baseline:middle;text-anchor:middle;
}
.gauge-unit{font-size:10px;fill:#9ca3af;}

.sensor-status{
  font-size:11px;font-weight:bold;letter-spacing:1px;
  padding:3px 10px;border-radius:20px;display:inline-block;
  background:#0f3020;color:var(--vert);border:1px solid rgba(57,255,20,.3);
}
.sensor-status.warn{background:#3d2800;color:var(--amber);border-color:rgba(245,158,11,.3);}
.sensor-status.crit{background:#3d0000;color:var(--rouge);border-color:rgba(239,68,68,.3);}

/* PIR spécial */
.pir-card .pir-icon{
  font-size:40px;margin:10px 0;
  filter:drop-shadow(0 0 8px rgba(57,255,20,.4));
  transition:filter .3s;
}
.pir-card.actif .pir-icon{
  filter:drop-shadow(0 0 20px rgba(239,68,68,.8));
  animation:pir-pulse 0.5s infinite alternate;
}
@keyframes pir-pulse{from{transform:scale(1)}to{transform:scale(1.1)}}
.pir-val{font-size:16px;font-weight:bold;color:var(--vert);}
.pir-card.actif .pir-val{color:var(--rouge);}

/* ── Graphiques ── */
.charts-row{
  display:grid;grid-template-columns:2fr 1fr;
  gap:16px;margin-bottom:22px;
}
.chart-box{
  background:var(--card);border-radius:16px;padding:20px;
  border:1px solid #1f2d5e;
}
.chart-box h3{
  font-size:13px;letter-spacing:1px;color:#9ca3af;
  text-transform:uppercase;margin-bottom:14px;
}
canvas{max-height:220px;}

/* ── Panneau bas ── */
.bottom-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}

.alerts-panel{background:var(--card);border-radius:16px;padding:20px;border:1px solid #1f2d5e;}
.alerts-panel h3{font-size:13px;letter-spacing:1px;color:#9ca3af;text-transform:uppercase;margin-bottom:14px;}

.alert-item{
  display:flex;align-items:flex-start;gap:10px;
  padding:10px;border-radius:8px;margin-bottom:8px;
  background:var(--card2);border-left:3px solid #333;
  animation:slideIn .3s ease;
}
@keyframes slideIn{from{opacity:0;transform:translateX(-10px)}to{opacity:1;transform:translateX(0)}}
.alert-item.info{border-left-color:#6b7280;}
.alert-item.warn{border-left-color:var(--amber);}
.alert-item.crit{border-left-color:var(--rouge);}
.alert-dot{
  width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-top:4px;
}
.alert-dot.info{background:#6b7280;}
.alert-dot.warn{background:var(--amber);}
.alert-dot.crit{background:var(--rouge);animation:blink .8s infinite;}
.alert-text{font-size:13px;color:#d1d5db;flex:1;}
.alert-time{font-size:11px;color:#6b7280;white-space:nowrap;}
.no-alert{text-align:center;padding:30px;color:#6b7280;font-size:13px;}

.status-panel{background:var(--card);border-radius:16px;padding:20px;border:1px solid #1f2d5e;}
.status-panel h3{font-size:13px;letter-spacing:1px;color:#9ca3af;text-transform:uppercase;margin-bottom:14px;}
.status-row{
  display:flex;justify-content:space-between;align-items:center;
  padding:10px 0;border-bottom:1px solid #1f2d5e;font-size:13px;
}
.status-row:last-child{border:none;}
.status-row label{color:#9ca3af;}
.status-val{font-weight:bold;}
.ok{color:var(--vert);}
.nok{color:var(--rouge);}
.med{color:var(--amber);}

/* Power mini chart */
.power-chart-box{background:var(--card);border-radius:16px;padding:20px;border:1px solid #1f2d5e;}
.power-chart-box h3{font-size:13px;letter-spacing:1px;color:#9ca3af;text-transform:uppercase;margin-bottom:14px;}

@media(max-width:900px){
  .charts-row,.bottom-row{grid-template-columns:1fr;}
  .sensors-grid{grid-template-columns:repeat(3,1fr);}
}
@media(max-width:500px){
  .sensors-grid{grid-template-columns:1fr 1fr;}
}
</style>

{{-- EN-TÊTE IoT --}}
<div class="iot-header">
  <div class="live-dot"></div>
  <span class="live-label">En direct</span>
  <span class="salle-badge" id="header-salle">Salle Serveur</span>
  <span class="niveau-badge" id="header-niveau">NORMAL</span>
  <span class="iot-time" id="iot-clock">--:--:--</span>
</div>

{{-- CAPTEURS --}}
<div class="sensors-grid">

  {{-- Température --}}
  <div class="sensor-card" id="card-temp">
    <div class="sensor-label">🌡️ TEMPÉRATURE</div>
    <div class="gauge-wrap">
      <svg viewBox="0 0 100 100">
        <circle class="gauge-bg" cx="50" cy="50" r="40"/>
        <circle class="gauge-arc" id="arc-temp" cx="50" cy="50" r="40"/>
        <text class="gauge-text" id="val-temp" x="50" y="46">--</text>
        <text class="gauge-unit" x="50" y="60">°C</text>
      </svg>
    </div>
    <div class="sensor-status" id="st-temp">NORMAL</div>
  </div>

  {{-- Humidité --}}
  <div class="sensor-card" id="card-hum">
    <div class="sensor-label">💧 HUMIDITÉ</div>
    <div class="gauge-wrap">
      <svg viewBox="0 0 100 100">
        <circle class="gauge-bg" cx="50" cy="50" r="40"/>
        <circle class="gauge-arc" id="arc-hum" cx="50" cy="50" r="40" style="stroke:var(--bleu)"/>
        <text class="gauge-text" id="val-hum" x="50" y="46">--</text>
        <text class="gauge-unit" x="50" y="60">%</text>
      </svg>
    </div>
    <div class="sensor-status" id="st-hum">NORMAL</div>
  </div>

  {{-- Gaz --}}
  <div class="sensor-card" id="card-gaz">
    <div class="sensor-label">🔥 GAZ / FUMÉE</div>
    <div class="gauge-wrap">
      <svg viewBox="0 0 100 100">
        <circle class="gauge-bg" cx="50" cy="50" r="40"/>
        <circle class="gauge-arc" id="arc-gaz" cx="50" cy="50" r="40" style="stroke:var(--amber)"/>
        <text class="gauge-text" id="val-gaz" x="50" y="46">--</text>
        <text class="gauge-unit" x="50" y="60">ppm</text>
      </svg>
    </div>
    <div class="sensor-status" id="st-gaz">NORMAL</div>
  </div>

  {{-- Courant --}}
  <div class="sensor-card" id="card-cur" style="display:none">
    <div class="sensor-label">⚡ COURANT</div>
    <div class="gauge-wrap">
      <svg viewBox="0 0 100 100">
        <circle class="gauge-bg" cx="50" cy="50" r="40"/>
        <circle class="gauge-arc" id="arc-cur" cx="50" cy="50" r="40" style="stroke:var(--violet)"/>
        <text class="gauge-text" id="val-cur" x="50" y="46">--</text>
        <text class="gauge-unit" x="50" y="60">A</text>
      </svg>
    </div>
    <div class="sensor-status" id="st-cur">NORMAL</div>
  </div>

  {{-- Puissance --}}
  <div class="sensor-card" id="card-pwr" style="display:none">
    <div class="sensor-label">💡 PUISSANCE</div>
    <div class="gauge-wrap">
      <svg viewBox="0 0 100 100">
        <circle class="gauge-bg" cx="50" cy="50" r="40"/>
        <circle class="gauge-arc" id="arc-pwr" cx="50" cy="50" r="40" style="stroke:#bb66ff"/>
        <text class="gauge-text" id="val-pwr" x="50" y="46">--</text>
        <text class="gauge-unit" x="50" y="60">W</text>
      </svg>
    </div>
    <div class="sensor-status" id="st-pwr">NORMAL</div>
  </div>

  {{-- Tension --}}
  <div class="sensor-card" id="card-ten" style="display:none">
    <div class="sensor-label">🔌 TENSION</div>
    <div class="gauge-wrap">
      <svg viewBox="0 0 100 100">
        <circle class="gauge-bg" cx="50" cy="50" r="40"/>
        <circle class="gauge-arc" id="arc-ten" cx="50" cy="50" r="40" style="stroke:#00ffcc"/>
        <text class="gauge-text" id="val-ten" x="50" y="46">--</text>
        <text class="gauge-unit" x="50" y="60">V</text>
      </svg>
    </div>
    <div class="sensor-status" id="st-ten">NORMAL</div>
  </div>

  {{-- PIR --}}
  <div class="sensor-card pir-card" id="card-pir">
    <div class="sensor-label">👁️ MOUVEMENT PIR</div>
    <div class="pir-icon" id="pir-icon">🚫</div>
    <div class="pir-val" id="val-pir">AUCUN</div>
    <div class="sensor-status" id="st-pir">INACTIF</div>
  </div>

</div>

{{-- GRAPHIQUES --}}
<div class="charts-row">
  <div class="chart-box">
    <h3>📈 Température • Humidité • Gaz (temps réel)</h3>
    <canvas id="chart-thg"></canvas>
  </div>
  <div class="chart-box" style="display:none">
    <h3>⚡ Courant • Puissance</h3>
    <canvas id="chart-pow"></canvas>
  </div>
</div>

{{-- BAS : ALERTES + ÉTAT SYSTÈME --}}
<div class="bottom-row">

  <div class="alerts-panel">
    <h3>🚨 Alertes récentes</h3>
    <div id="alerts-feed">
      <div class="no-alert">Chargement des alertes…</div>
    </div>
  </div>

  <div class="status-panel">
    <h3>📡 État du système</h3>
    <div class="status-row">
      <label>Système</label>
      <span class="status-val ok" id="sys-status">EN LIGNE</span>
    </div>
    <div class="status-row">
      <label>Salle serveur</label>
      <span class="status-val ok" id="salle-status">ACTIVE</span>
    </div>
    <div class="status-row">
      <label>Arduino</label>
      <span class="status-val" id="arduino-status">--</span>
    </div>
    <div class="status-row">
      <label>Alertes actives</label>
      <span class="status-val" id="alertes-count">0</span>
    </div>
    <div class="status-row">
      <label>Dernière donnée</label>
      <span class="status-val ok" id="last-update" style="font-size:11px;">--</span>
    </div>
    <div class="status-row">
      <label>Total mesures</label>
      <span class="status-val" id="total-mesures" style="color:#9ca3af;">--</span>
    </div>
    <div class="status-row">
      <label>GSM / SIM900</label>
      <span class="status-val ok">CONFIGURÉ</span>
    </div>
    <div class="status-row">
      <label>Numéro admin</label>
      <span class="status-val" style="color:#9ca3af;font-size:11px;">+237 687 988 340</span>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ── Config seuils ──────────────────────────────────────────
const SEUILS = {
  temp:  {warn:30, crit:40, max:80},
  hum:   {minWarn:30, maxWarn:80, minCrit:20, maxCrit:85, max:100},
  gaz:   {warn:300, crit:500, max:1000},
  cur:   {warn:10, crit:15, max:25},
  pwr:   {warn:2200, crit:3300, max:5000},
  ten:   {nominal:220, tol:15, max:260},
};

// ── Gauge SVG ──────────────────────────────────────────────
const CIRC = 251.2; // 2π × 40

function setGauge(arcId, pct, color) {
  const arc = document.getElementById(arcId);
  if (!arc) return;
  const offset = CIRC * (1 - Math.max(0, Math.min(1, pct)));
  arc.style.strokeDashoffset = offset;
  if (color) arc.style.stroke = color;
}

function niveauTemp(v) {
  if (v >= SEUILS.temp.crit)  return 'crit';
  if (v >= SEUILS.temp.warn)  return 'warn';
  return 'ok';
}
function niveauHum(v) {
  if (v > SEUILS.hum.maxCrit || v < SEUILS.hum.minCrit) return 'crit';
  if (v > SEUILS.hum.maxWarn || v < SEUILS.hum.minWarn) return 'warn';
  return 'ok';
}
function niveauGaz(v) {
  if (v >= SEUILS.gaz.crit)  return 'crit';
  if (v >= SEUILS.gaz.warn)  return 'warn';
  return 'ok';
}
function niveauCur(v) {
  if (v >= SEUILS.cur.crit)  return 'crit';
  if (v >= SEUILS.cur.warn)  return 'warn';
  return 'ok';
}
function niveauPwr(v) {
  if (v >= SEUILS.pwr.crit)  return 'crit';
  if (v >= SEUILS.pwr.warn)  return 'warn';
  return 'ok';
}
function niveauTen(v) {
  const diff = Math.abs(v - SEUILS.ten.nominal);
  if (diff > SEUILS.ten.tol * 2) return 'crit';
  if (diff > SEUILS.ten.tol)     return 'warn';
  return 'ok';
}

const COLOR_LEVEL = {ok:'#39ff14', warn:'#f59e0b', crit:'#ef4444'};
const LABEL_LEVEL = {ok:'NORMAL', warn:'ATTENTION', crit:'CRITIQUE'};

function setCard(cardId, stId, niveau) {
  const card = document.getElementById(cardId);
  const st   = document.getElementById(stId);
  if (!card || !st) return;
  card.className = 'sensor-card ' + (niveau !== 'ok' ? niveau : '');
  st.className   = 'sensor-status ' + (niveau !== 'ok' ? niveau : '');
  st.textContent = LABEL_LEVEL[niveau] || 'NORMAL';
}

// ── Graphiques Chart.js ───────────────────────────────────

const chartLabels   = [];
const dTemp = [], dHum = [], dGaz = [], dCur = [], dPwr = [];
const MAX_POINTS = 30;

Chart.defaults.color = '#9ca3af';
Chart.defaults.borderColor = '#1f2d5e';

const ctxTHG = document.getElementById('chart-thg').getContext('2d');
const chartTHG = new Chart(ctxTHG, {
  type: 'line',
  data: {
    labels: chartLabels,
    datasets: [
      {label:'Temp (°C)',  data:dTemp, borderColor:'#ff5733', backgroundColor:'rgba(255,87,51,.08)',  borderWidth:2, tension:.4, pointRadius:0, fill:true},
      {label:'Humidité (%)',data:dHum, borderColor:'#33b5ff', backgroundColor:'rgba(51,181,255,.06)', borderWidth:2, tension:.4, pointRadius:0, fill:true},
      {label:'Gaz (ppm)', data:dGaz, borderColor:'#ffd633', backgroundColor:'rgba(255,214,51,.06)', borderWidth:2, tension:.4, pointRadius:0, fill:true},
    ]
  },
  options: {
    responsive:true, maintainAspectRatio:true,
    animation:{duration:400},
    plugins:{legend:{labels:{color:'#9ca3af',boxWidth:12}}},
    scales:{
      x:{grid:{color:'#1f2d5e'},ticks:{maxTicksLimit:8, font:{size:10}}},
      y:{grid:{color:'#1f2d5e'},ticks:{font:{size:10}}},
    }
  }
});

const ctxPow = document.getElementById('chart-pow').getContext('2d');
const chartPow = new Chart(ctxPow, {
  type: 'line',
  data: {
    labels: chartLabels,
    datasets: [
      {label:'Courant (A)',  data:dCur, borderColor:'#33ff88', backgroundColor:'rgba(51,255,136,.08)', borderWidth:2, tension:.4, pointRadius:0, fill:true},
      {label:'Puissance (W)',data:dPwr, borderColor:'#bb66ff', backgroundColor:'rgba(187,102,255,.06)',borderWidth:2, tension:.4, pointRadius:0, fill:true},
    ]
  },
  options: {
    responsive:true, maintainAspectRatio:true,
    animation:{duration:400},
    plugins:{legend:{labels:{color:'#9ca3af',boxWidth:12}}},
    scales:{
      x:{grid:{color:'#1f2d5e'},ticks:{maxTicksLimit:6, font:{size:10}}},
      y:{grid:{color:'#1f2d5e'},ticks:{font:{size:10}}},
    }
  }
});

function push(arr, val) {
  arr.push(val);
  if (arr.length > MAX_POINTS) arr.shift();
}

// ── Mise à jour dashboard ─────────────────────────────────

let lastNiveau = 'NORMAL';
let arduinoOk  = false;
let totalMes   = 0;

function afficherHorsLigne() {
  arduinoOk = false;
  document.getElementById('arduino-status').textContent = 'HORS LIGNE';
  document.getElementById('arduino-status').className   = 'status-val nok';

  const ids = ['val-temp','val-hum','val-gaz','val-cur','val-pwr','val-ten'];
  ids.forEach(id => { document.getElementById(id).textContent = '--'; });

  setGauge('arc-temp', 0, '#374151'); setGauge('arc-hum',  0, '#374151');
  setGauge('arc-gaz',  0, '#374151'); setGauge('arc-cur',  0, '#374151');
  setGauge('arc-pwr',  0, '#374151'); setGauge('arc-ten',  0, '#374151');

  ['card-temp','card-hum','card-gaz','card-cur','card-pwr','card-ten'].forEach(id => {
    const c = document.getElementById(id);
    if (c) { c.classList.remove('warn','crit'); }
  });
  ['st-temp','st-hum','st-gaz','st-cur','st-pwr','st-ten'].forEach(id => {
    const el = document.getElementById(id);
    if (el) { el.className = 'sensor-status'; el.textContent = 'HORS LIGNE'; }
  });

  document.getElementById('val-pir').textContent = '--';
  document.getElementById('pir-icon').textContent = '⚫';
}

async function updateDashboard() {
  try {
    const res  = await fetch('/api/dashboard-data');
    const data = await res.json();

    // Si Arduino non connecté : ne pas afficher de données en direct
    if (!data.arduino_connecte) {
      afficherHorsLigne();
      document.getElementById('header-salle' ).textContent = data.nom_salle  || 'Salle Serveur';
      document.getElementById('salle-status' ).textContent = data.etat_salle || 'ACTIVE';
      document.getElementById('alertes-count').textContent = data.alertes_actives || 0;
      document.getElementById('alertes-count').className   = (data.alertes_actives > 0) ? 'status-val nok' : 'status-val ok';
      document.getElementById('last-update'  ).textContent = new Date().toLocaleTimeString('fr-FR');
      return;
    }

    const temp = parseFloat(data.temperature || 0);
    const hum  = parseFloat(data.humidite    || 0);
    const gaz  = parseFloat(data.gaz         || 0);
    const cur  = parseFloat(data.courant      || 0);
    const pwr  = parseFloat(data.puissance    || 0);
    const ten  = parseFloat(data.tension       || 220);
    const pir  = parseInt  (data.pir_detecte   || 0);

    // Valeurs texte
    document.getElementById('val-temp').textContent = temp.toFixed(1);
    document.getElementById('val-hum' ).textContent = hum.toFixed(1);
    document.getElementById('val-gaz' ).textContent = Math.round(gaz);
    document.getElementById('val-cur' ).textContent = cur.toFixed(2);
    document.getElementById('val-pwr' ).textContent = Math.round(pwr);
    document.getElementById('val-ten' ).textContent = ten.toFixed(0);

    // Jauges
    const lvTemp = niveauTemp(temp);
    const lvHum  = niveauHum(hum);
    const lvGaz  = niveauGaz(gaz);
    const lvCur  = niveauCur(cur);
    const lvPwr  = niveauPwr(pwr);
    const lvTen  = niveauTen(ten);

    setGauge('arc-temp', temp / SEUILS.temp.max, COLOR_LEVEL[lvTemp]);
    setGauge('arc-hum',  hum  / SEUILS.hum.max,  COLOR_LEVEL[lvHum]);
    setGauge('arc-gaz',  gaz  / SEUILS.gaz.max,  COLOR_LEVEL[lvGaz]);
    setGauge('arc-cur',  cur  / SEUILS.cur.max,  COLOR_LEVEL[lvCur]);
    setGauge('arc-pwr',  pwr  / SEUILS.pwr.max,  COLOR_LEVEL[lvPwr]);
    setGauge('arc-ten',  Math.min(ten / SEUILS.ten.max, 1), COLOR_LEVEL[lvTen]);

    setCard('card-temp', 'st-temp', lvTemp);
    setCard('card-hum',  'st-hum',  lvHum);
    setCard('card-gaz',  'st-gaz',  lvGaz);
    setCard('card-cur',  'st-cur',  lvCur);
    setCard('card-pwr',  'st-pwr',  lvPwr);
    setCard('card-ten',  'st-ten',  lvTen);

    // PIR
    const pirCard = document.getElementById('card-pir');
    document.getElementById('pir-icon').textContent = pir ? '🚨' : '✅';
    document.getElementById('val-pir' ).textContent = pir ? 'DÉTECTÉ !' : 'AUCUN';
    const stPir = document.getElementById('st-pir');
    if (pir) {
      pirCard.classList.add('actif');
      stPir.className = 'sensor-status crit';
      stPir.textContent = 'INTRUSION !';
    } else {
      pirCard.classList.remove('actif');
      stPir.className = 'sensor-status';
      stPir.textContent = 'INACTIF';
    }

    // En-tête niveau global
    const niv = data.niveau_global || 'NORMAL';
    if (niv !== lastNiveau) {
      const badge = document.getElementById('header-niveau');
      badge.textContent = niv;
      badge.className   = 'niveau-badge' + (niv==='CRITIQUE'?' crit':(niv==='AVERTISSEMENT'?' warn':''));
      lastNiveau = niv;
    }
    document.getElementById('header-salle'  ).textContent = data.nom_salle  || 'Salle Serveur';
    document.getElementById('salle-status'  ).textContent = data.etat_salle || 'ACTIVE';
    document.getElementById('alertes-count' ).textContent = data.alertes_actives || 0;
    document.getElementById('last-update'   ).textContent = new Date().toLocaleTimeString('fr-FR');

    if (data.alertes_actives > 0) {
      document.getElementById('alertes-count').className = 'status-val nok';
    } else {
      document.getElementById('alertes-count').className = 'status-val ok';
    }

    arduinoOk = true;
    document.getElementById('arduino-status').textContent = 'EN LIGNE';
    document.getElementById('arduino-status').className   = 'status-val ok';

    // Graphiques
    const now = new Date();
    const timeLabel = now.toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
    push(chartLabels, timeLabel);
    push(dTemp, temp); push(dHum, hum);  push(dGaz, gaz);
    push(dCur,  cur);  push(dPwr,  pwr);

    chartTHG.update('none');
    chartPow.update('none');

  } catch(e) {
    afficherHorsLigne();
  }
}

// ── Alertes récentes ──────────────────────────────────────

async function updateAlertes() {
  try {
    const res  = await fetch('/api/alertes/recent?limit=8');
    const json = await res.json();

    const feed = document.getElementById('alerts-feed');

    if (!json.alertes || json.alertes.length === 0) {
      feed.innerHTML = '<div class="no-alert">✅ Aucune alerte enregistrée</div>';
      return;
    }

    let html = '';
    json.alertes.forEach(a => {
      const niv = (a.niveau || '').toLowerCase().includes('crit') ? 'crit'
                : (a.niveau || '').toLowerCase().includes('avert') ? 'warn' : 'info';
      const time = a.created_at ? new Date(a.created_at).toLocaleTimeString('fr-FR', {hour:'2-digit',minute:'2-digit'}) : '--';
      const resTag = a.resolu ? ' ✓' : '';
      html += `<div class="alert-item ${niv}">
        <div class="alert-dot ${niv}"></div>
        <div class="alert-text">
          <strong>${a.type || 'alerte'}</strong> — ${a.valeur || '--'}${resTag}
          <div style="color:#6b7280;font-size:11px;margin-top:2px;">${a.message || ''}</div>
        </div>
        <div class="alert-time">${time}</div>
      </div>`;
    });
    feed.innerHTML = html;

    // Mise à jour du compteur total
    if (json.stats) {
      document.getElementById('total-mesures').textContent = json.stats.total;
    }
  } catch(e) {}
}

// ── Horloge IoT ───────────────────────────────────────────

function updateClock() {
  document.getElementById('iot-clock').textContent =
    new Date().toLocaleTimeString('fr-FR', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
}

// ── Initialisation ────────────────────────────────────────

updateDashboard();
updateAlertes();
setInterval(updateDashboard, 1000);
setInterval(updateAlertes,   5000);
setInterval(updateClock,     1000);

</script>

<style id="dashboard-scroll-style">
  /* Injecté ici pour surpasser le *::-webkit-scrollbar du layout */
  html { overflow: hidden !important; }
  body { overflow: hidden !important; height: 100vh !important; }

  .main {
    height: 100vh !important;
    overflow-y: scroll !important;
    scrollbar-width: thin !important;
    scrollbar-color: #2fa84f #0a1525 !important;
  }

  /* Chrome / Safari / Edge */
  .main::-webkit-scrollbar         { display: block !important; width: 8px !important; }
  .main::-webkit-scrollbar-track   { background: #0a1525 !important; border-radius: 6px !important; }
  .main::-webkit-scrollbar-thumb   { background: #2fa84f !important; border-radius: 6px !important; }
  .main::-webkit-scrollbar-thumb:hover { background: #39ff14 !important; }
</style>

@endsection
