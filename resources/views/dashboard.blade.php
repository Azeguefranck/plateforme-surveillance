@extends('layouts.app')

@section('content')

<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f4f6f9;color:#1a2340;font-family:'Segoe UI',Arial,sans-serif}

.dash-header{
  display:flex;justify-content:space-between;align-items:center;
  padding:18px 0 28px;flex-wrap:wrap;gap:12px;
}
.dash-title{font-size:26px;font-weight:700;letter-spacing:1px;color:#1a2340}
.dash-live{display:flex;align-items:center;gap:8px;font-size:13px;color:#16a34a;font-weight:600;letter-spacing:.5px}
.dot{width:10px;height:10px;border-radius:50%;background:#16a34a;animation:pulse 1.2s infinite;box-shadow:0 0 8px rgba(22,163,74,.4)}
@keyframes pulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:.6}}

.gauges{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:28px}
.gauge-card{
  background:#fff;
  border:1.5px solid #e2e8f0;border-radius:18px;
  padding:22px 16px;text-align:center;transition:.3s;
  box-shadow:0 2px 12px rgba(0,0,0,.07);
}
.gauge-card:hover{transform:translateY(-4px);box-shadow:0 6px 24px rgba(0,0,0,.12)}
.gauge-card.alerte-warning{border-color:#f59e0b;box-shadow:0 0 20px rgba(245,158,11,.2)}
.gauge-card.alerte-critique{border-color:#ef4444;box-shadow:0 0 25px rgba(239,68,68,.25);animation:cardPulse 1.5s infinite}
@keyframes cardPulse{0%,100%{box-shadow:0 0 25px rgba(239,68,68,.25)}50%{box-shadow:0 0 40px rgba(239,68,68,.45)}}

.gauge-label{font-size:12px;font-weight:700;letter-spacing:1.5px;color:#64748b;text-transform:uppercase;margin-bottom:14px}
.gauge-ring{
  width:130px;height:130px;border-radius:50%;margin:0 auto 14px;
  display:flex;flex-direction:column;justify-content:center;align-items:center;
  border:12px solid #e2e8f0;background:#f8fafc;transition:.4s;
}
.gauge-val{font-size:26px;font-weight:700;color:#1a2340;line-height:1}
.gauge-unit{font-size:12px;color:#94a3b8;margin-top:3px}
.gauge-bar{height:6px;border-radius:3px;background:#e2e8f0;margin-top:10px;overflow:hidden}
.gauge-fill{height:100%;border-radius:3px;transition:width .6s ease,background .4s}

.ok   .gauge-ring{border-color:#22c55e;box-shadow:0 0 16px rgba(34,197,94,.2)}
.ok   .gauge-val {color:#16a34a}
.ok   .gauge-fill{background:#22c55e}
.warn .gauge-ring{border-color:#f59e0b;box-shadow:0 0 16px rgba(245,158,11,.25)}
.warn .gauge-val {color:#d97706}
.warn .gauge-fill{background:#f59e0b}
.crit .gauge-ring{border-color:#ef4444;box-shadow:0 0 20px rgba(239,68,68,.3)}
.crit .gauge-val {color:#dc2626}
.crit .gauge-fill{background:#ef4444}

.pir-badge{display:inline-block;padding:6px 18px;border-radius:50px;font-size:13px;font-weight:700;letter-spacing:.5px;margin-top:8px}
.pir-ok {background:rgba(34,197,94,.1);color:#16a34a;border:1px solid #22c55e}
.pir-det{background:rgba(239,68,68,.1);color:#dc2626;border:1px solid #ef4444;animation:pir-flash .8s infinite}
@keyframes pir-flash{0%,100%{opacity:1}50%{opacity:.5}}

/* ── Bandeau déconnecté ── */
.no-data-banner{
  display:none;background:rgba(239,68,68,.07);border:1px solid #ef4444;
  border-radius:12px;padding:14px 20px;margin-bottom:22px;
  text-align:center;color:#dc2626;font-weight:700;font-size:14px;letter-spacing:.5px;
}
.no-data-banner.visible{display:block}
</style>

<div class="dash-header">
  <div class="dash-title">TABLEAU DE BORD EN TEMPS RÉEL</div>
  <div class="dash-live">
    <div class="dot"></div>
    EN DIRECT — <span id="last-update">--</span>
  </div>
</div>

<!-- Bandeau déconnecté -->
<div class="no-data-banner" id="no-data-banner">⚠ Arduino déconnecté — Aucune donnée en temps réel</div>

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


  <div class="gauge-card ok" id="card-pir" style="display:flex;flex-direction:column;justify-content:center;align-items:center">
    <div class="gauge-label">Mouvement</div>
    <div style="font-size:48px;margin:14px 0">🚶</div>
    <div class="pir-badge pir-ok" id="pir-badge">AUCUN MOUVEMENT</div>
  </div>

</div>

<script>
const SEUILS = {
  temperature: { warn:35,  crit:40,  max:80   },
  humidite:    { warn:75,  crit:85,  max:100  },
  gaz:         { warn:300, crit:500, max:1000 },
};

function majJauge(nom, val) {
  const s    = SEUILS[nom];
  const el   = document.getElementById('g-' + nom);
  const fill = document.getElementById('f-' + nom);
  const card = document.getElementById('card-' + nom);
  if (!el || !s) return;
  el.textContent   = val;
  fill.style.width = Math.min(100, (val / s.max) * 100) + '%';
  const level      = val >= s.crit ? 'crit' : val >= s.warn ? 'warn' : 'ok';
  card.className   = 'gauge-card ' + level;
  if (val >= s.crit) card.classList.add('alerte-critique');
  else if (val >= s.warn) card.classList.add('alerte-warning');
}

function viderJauges() {
  ['temperature','humidite','gaz'].forEach(nom => {
    const el   = document.getElementById('g-' + nom);
    const fill = document.getElementById('f-' + nom);
    const card = document.getElementById('card-' + nom);
    if (el)   el.textContent   = '—';
    if (fill) fill.style.width = '0%';
    if (card) card.className   = 'gauge-card';
  });
  const badge   = document.getElementById('pir-badge');
  const pirCard = document.getElementById('card-pir');
  if (badge)   { badge.className = 'pir-badge pir-ok'; badge.textContent = '—'; }
  if (pirCard) pirCard.className = 'gauge-card';
  document.getElementById('no-data-banner').classList.add('visible');
  document.getElementById('last-update').textContent = '--';
}

function pollLive() {
  fetch('/api/live-data')
    .then(r => {
      if (r.status === 204 || !r.ok) { viderJauges(); return null; }
      return r.json();
    })
    .then(data => {
      if (!data || data.error === 'no_data') { viderJauges(); return; }

      document.getElementById('no-data-banner').classList.remove('visible');

      majJauge('temperature', parseFloat(data.temperature) || 0);
      majJauge('humidite',    parseFloat(data.humidite)    || 0);
      majJauge('gaz',         parseFloat(data.gaz)         || 0);

      const pir     = data.pir == 1 || data.pir === true || data.pir === 'true';
      const badge   = document.getElementById('pir-badge');
      const pirCard = document.getElementById('card-pir');
      badge.className   = pir ? 'pir-badge pir-det' : 'pir-badge pir-ok';
      badge.textContent = pir ? 'MOUVEMENT DÉTECTÉ' : 'AUCUN MOUVEMENT';
      pirCard.className = 'gauge-card ' + (pir ? 'crit alerte-critique' : 'ok');

      document.getElementById('last-update').textContent = new Date().toLocaleTimeString('fr-FR');
    })
    .catch(() => viderJauges());
}

pollLive();
setInterval(pollLive, 1000);
</script>

@endsection
