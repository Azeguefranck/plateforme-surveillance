@extends('layouts.app')
@section('content')
<style>
*{box-sizing:border-box;margin:0;padding:0}

.tech-header{
  display:flex;justify-content:space-between;align-items:center;
  padding:18px 0 24px;flex-wrap:wrap;gap:12px;
}
.tech-title{font-size:22px;font-weight:700;letter-spacing:.8px;color:#1a2340}
.tech-badge{background:#1e3a8a;color:#fff;font-size:11px;font-weight:700;
  padding:4px 12px;border-radius:20px;letter-spacing:1px;text-transform:uppercase}
.tech-live{display:flex;align-items:center;gap:8px;font-size:13px;color:#16a34a;font-weight:600}
.dot{width:10px;height:10px;border-radius:50%;background:#16a34a;animation:pulse 1.2s infinite;
     box-shadow:0 0 8px rgba(22,163,74,.4)}
@keyframes pulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:.6}}

.no-data-banner{
  display:none;background:rgba(239,68,68,.07);border:1px solid #ef4444;
  border-radius:12px;padding:14px 20px;margin-bottom:22px;
  text-align:center;color:#dc2626;font-weight:700;font-size:14px;
}
.no-data-banner.visible{display:block}

/* ── Panneau salle ── */
.salle-panel{
  background:#fff;border:1.5px solid #e2e8f0;border-radius:18px;
  padding:18px 20px 16px;margin-bottom:20px;
  box-shadow:0 2px 12px rgba(0,0,0,.07);
}
.salle-panel-header{
  display:flex;justify-content:space-between;align-items:center;
  margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f1f5f9;
  flex-wrap:wrap;gap:8px;
}
.salle-panel-nom{font-size:15px;font-weight:700;color:#1e3a8a}
.salle-panel-ts{font-size:11px;color:#94a3b8}

/* ── Jauges ── */
.gauges{display:grid;grid-template-columns:repeat(auto-fit,minmax(165px,1fr));gap:14px;margin-bottom:14px}
.gauge-card{
  background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;
  padding:18px 12px;text-align:center;transition:.3s;
}
.gauge-card:hover{transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.1)}
.gauge-card.alerte-warning{border-color:#f59e0b;box-shadow:0 0 18px rgba(245,158,11,.22)}
.gauge-card.alerte-critique{border-color:#ef4444;box-shadow:0 0 24px rgba(239,68,68,.28);animation:cardPulse 1.5s infinite}
@keyframes cardPulse{0%,100%{box-shadow:0 0 24px rgba(239,68,68,.28)}50%{box-shadow:0 0 38px rgba(239,68,68,.48)}}

.gauge-label{font-size:11px;font-weight:700;letter-spacing:1.5px;color:#64748b;text-transform:uppercase;margin-bottom:12px}
.gauge-ring{
  width:110px;height:110px;border-radius:50%;margin:0 auto 12px;
  display:flex;flex-direction:column;justify-content:center;align-items:center;
  border:10px solid #e2e8f0;background:#fff;transition:.4s;
}
.gauge-val{font-size:22px;font-weight:700;color:#1a2340;line-height:1}
.gauge-unit{font-size:11px;color:#94a3b8;margin-top:3px}
.gauge-bar{height:5px;border-radius:3px;background:#e2e8f0;margin-top:8px;overflow:hidden}
.gauge-fill{height:100%;border-radius:3px;transition:width .6s ease,background .4s}

.ok   .gauge-ring{border-color:#22c55e;box-shadow:0 0 14px rgba(34,197,94,.2)}
.ok   .gauge-val {color:#16a34a}
.ok   .gauge-fill{background:#22c55e}
.warn .gauge-ring{border-color:#f59e0b;box-shadow:0 0 14px rgba(245,158,11,.25)}
.warn .gauge-val {color:#d97706}
.warn .gauge-fill{background:#f59e0b}
.crit .gauge-ring{border-color:#ef4444;box-shadow:0 0 18px rgba(239,68,68,.3)}
.crit .gauge-val {color:#dc2626}
.crit .gauge-fill{background:#ef4444}

/* ── PIR badge ── */
.pir-badge{display:inline-block;padding:5px 16px;border-radius:50px;font-size:12px;font-weight:700;margin-top:6px}
.pir-ok {background:rgba(34,197,94,.1);color:#16a34a;border:1px solid #22c55e}
.pir-det{background:rgba(239,68,68,.1);color:#dc2626;border:1px solid #ef4444;animation:pir-flash .8s infinite}
@keyframes pir-flash{0%,100%{opacity:1}50%{opacity:.5}}

/* ── Alertes récentes ── */
.alerts-section{background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:16px 18px;margin-top:16px}
.alerts-title{font-size:13px;font-weight:700;color:#1e3a8a;margin-bottom:12px}
.alert-row{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:12px}
.alert-row:last-child{border-bottom:none}
.alert-badge{padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;white-space:nowrap}
.badge-critique{background:rgba(239,68,68,.1);color:#dc2626;border:1px solid #fca5a5}
.badge-warning {background:rgba(245,158,11,.1);color:#d97706;border:1px solid #fde68a}
.alert-msg{flex:1;color:#475569}
.alert-ts{color:#94a3b8;font-size:11px;white-space:nowrap}
.no-alerts{color:#94a3b8;font-size:12px;padding:8px 0}
</style>

<div class="tech-header">
  <div style="display:flex;align-items:center;gap:12px">
    <div class="tech-title">
      <i class="fa-solid fa-screwdriver-wrench" style="color:#1e3a8a;margin-right:10px"></i>TABLEAU DE BORD TECHNICIEN
    </div>
    <span class="tech-badge">Technicien</span>
  </div>
  <div class="tech-live"><div class="dot"></div>EN DIRECT — <span id="tech-last-update">--</span></div>
</div>

<div class="no-data-banner" id="tech-no-data">
  <i class="fa-solid fa-triangle-exclamation"></i> Aucun Arduino connecté — Aucune donnée en temps réel
</div>

<div id="tech-salle-panels"></div>

<div class="alerts-section">
  <div class="alerts-title"><i class="fa-solid fa-bell" style="color:#ef4444;margin-right:6px"></i>Alertes récentes (5 dernières)</div>
  <div id="tech-alertes-list"><div class="no-alerts">Chargement...</div></div>
</div>

<script>
let SEUILS = {
  temperature: { warn:28,  crit:32,  max:80   },
  humidite:    { warn:75,  crit:85,  max:100  },
  gaz:         { warn:400, crit:600, max:1000 },
};
fetch('/api/seuils').then(r=>r.json()).then(s=>{
  if(s.temperature) SEUILS.temperature = { warn:s.temperature.warning, crit:s.temperature.critique, max:80 };
  if(s.humidite)    SEUILS.humidite    = { warn:s.humidite.warning,    crit:s.humidite.critique,    max:100 };
  if(s.gaz)         SEUILS.gaz         = { warn:s.gaz.warning,         crit:s.gaz.critique,         max:1000 };
}).catch(()=>{});

function getPanel(sid, nom) {
  let p = document.getElementById('tpanel-' + sid);
  if (!p) {
    p = document.createElement('div');
    p.id        = 'tpanel-' + sid;
    p.className = 'salle-panel';
    p.innerHTML =
      `<div class="salle-panel-header">
         <span class="salle-panel-nom">
           <i class="fa-solid fa-warehouse" style="color:#3b82f6;margin-right:8px"></i>${nom}
         </span>
         <span class="salle-panel-ts" id="tts-${sid}">—</span>
       </div>
       <div class="gauges" id="tgauges-${sid}">
         <div class="gauge-card ok" id="tcard-temperature-${sid}">
           <div class="gauge-label"><i class="fa-solid fa-temperature-half" style="color:#ef4444;margin-right:4px"></i>Température</div>
           <div class="gauge-ring"><span class="gauge-val" id="tg-temperature-${sid}">—</span><span class="gauge-unit">°C</span></div>
           <div class="gauge-bar"><div class="gauge-fill" id="tf-temperature-${sid}" style="width:0%"></div></div>
         </div>
         <div class="gauge-card ok" id="tcard-humidite-${sid}">
           <div class="gauge-label"><i class="fa-solid fa-droplet" style="color:#3b82f6;margin-right:4px"></i>Humidité</div>
           <div class="gauge-ring"><span class="gauge-val" id="tg-humidite-${sid}">—</span><span class="gauge-unit">%</span></div>
           <div class="gauge-bar"><div class="gauge-fill" id="tf-humidite-${sid}" style="width:0%"></div></div>
         </div>
         <div class="gauge-card ok" id="tcard-gaz-${sid}">
           <div class="gauge-label"><i class="fa-solid fa-wind" style="color:#6b7280;margin-right:4px"></i>Gaz / Air</div>
           <div class="gauge-ring"><span class="gauge-val" id="tg-gaz-${sid}">—</span><span class="gauge-unit">ppm</span></div>
           <div class="gauge-bar"><div class="gauge-fill" id="tf-gaz-${sid}" style="width:0%"></div></div>
         </div>
         <div class="gauge-card ok" id="tcard-pir-${sid}" style="display:flex;flex-direction:column;justify-content:center;align-items:center">
           <div class="gauge-label"><i class="fa-solid fa-person-walking" style="margin-right:4px"></i>Mouvement</div>
           <div style="font-size:38px;margin:10px 0"><i class="fa-solid fa-person-walking"></i></div>
           <div class="pir-badge pir-ok" id="tpir-badge-${sid}">AUCUN MOUVEMENT</div>
         </div>
       </div>`;
    document.getElementById('tech-salle-panels').appendChild(p);
  }
  return p;
}

function majJauge(sid, nom, val) {
  const s    = SEUILS[nom];
  const el   = document.getElementById('tg-'    + nom + '-' + sid);
  const fill = document.getElementById('tf-'    + nom + '-' + sid);
  const card = document.getElementById('tcard-' + nom + '-' + sid);
  if (!el || !s) return;
  el.textContent   = val;
  fill.style.width = Math.min(100, (val / s.max) * 100) + '%';
  const lvl = val >= s.crit ? 'crit' : val >= s.warn ? 'warn' : 'ok';
  card.className = 'gauge-card ' + lvl + (val >= s.crit ? ' alerte-critique' : val >= s.warn ? ' alerte-warning' : '');
}

function pollMesures() {
  fetch('/api/mesures-live')
    .then(r => { if (!r.ok) throw 0; return r.json(); })
    .then(data => {
      const keys = Object.keys(data || {});
      if (!keys.length) {
        document.getElementById('tech-no-data').classList.add('visible');
        document.getElementById('tech-last-update').textContent = '--';
        return;
      }
      document.getElementById('tech-no-data').classList.remove('visible');

      keys.forEach(sid => {
        const d = data[sid];
        getPanel(sid, d.salle_nom);

        majJauge(sid, 'temperature', parseFloat(d.temperature) || 0);
        majJauge(sid, 'humidite',    parseFloat(d.humidite)    || 0);
        majJauge(sid, 'gaz',         parseInt(d.gaz)           || 0);

        const pir   = d.pir == 1 || d.pir === true || d.pir === 'true';
        const badge = document.getElementById('tpir-badge-' + sid);
        const pCard = document.getElementById('tcard-pir-'  + sid);
        if (badge) { badge.className = 'pir-badge ' + (pir ? 'pir-det' : 'pir-ok'); badge.textContent = pir ? 'MOUVEMENT DÉTECTÉ' : 'AUCUN MOUVEMENT'; }
        if (pCard) pCard.className = 'gauge-card ' + (pir ? 'crit alerte-critique' : 'ok');

        const tsEl = document.getElementById('tts-' + sid);
        if (tsEl) tsEl.textContent = 'Màj ' + new Date().toLocaleTimeString('fr-FR');
      });

      document.querySelectorAll('.salle-panel').forEach(p => {
        if (!data[p.id.replace('tpanel-', '')]) p.remove();
      });

      document.getElementById('tech-last-update').textContent = new Date().toLocaleTimeString('fr-FR');
    })
    .catch(() => {
      document.getElementById('tech-no-data').classList.add('visible');
      document.getElementById('tech-last-update').textContent = '--';
    });
}

function pollAlertes() {
  fetch('/api/alertes-mails?limite=5')
    .then(r => r.json())
    .then(data => {
      const list = document.getElementById('tech-alertes-list');
      const rows = data.alertes || data.data || [];
      if (!rows.length) {
        list.innerHTML = '<div class="no-alerts"><i class="fa-solid fa-circle-check" style="color:#22c55e;margin-right:6px"></i>Aucune alerte récente</div>';
        return;
      }
      list.innerHTML = rows.slice(0, 5).map(a => {
        const d = new Date((a.created_at || '').replace(' ', 'T'));
        const ts = isNaN(d) ? '' : d.toLocaleString('fr-FR', {day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'});
        return `<div class="alert-row">
          <span class="alert-badge badge-${a.niveau}">${(a.niveau||'').toUpperCase()}</span>
          <span class="alert-msg">${a.message||'—'}</span>
          <span class="alert-ts">${ts}</span>
        </div>`;
      }).join('');
    })
    .catch(() => {});
}

pollMesures();
pollAlertes();
setInterval(pollMesures, 3000);
setInterval(pollAlertes, 30000);
</script>
@endsection
