@extends('layouts.app')
@section('content')
<style>
*{box-sizing:border-box;margin:0;padding:0}

.dash-header{
  display:flex;justify-content:space-between;align-items:center;
  padding:18px 0 24px;flex-wrap:wrap;gap:12px;
}
.dash-title{font-size:24px;font-weight:700;letter-spacing:1px;color:#1a2340}
.dash-live{display:flex;align-items:center;gap:8px;font-size:13px;color:#16a34a;font-weight:600}
.dot{width:10px;height:10px;border-radius:50%;background:#16a34a;animation:pulse 1.2s infinite;
     box-shadow:0 0 8px rgba(22,163,74,.4)}
@keyframes pulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:.6}}

.no-data-banner{
  display:none;background:rgba(239,68,68,.07);border:1px solid #ef4444;
  border-radius:12px;padding:14px 20px;margin-bottom:22px;
  text-align:center;color:#dc2626;font-weight:700;font-size:14px;
}
.no-data-banner.visible{display:block}

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

.gauges{display:grid;grid-template-columns:repeat(auto-fit,minmax(175px,1fr));gap:14px;margin-bottom:14px}
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
  width:120px;height:120px;border-radius:50%;margin:0 auto 12px;
  display:flex;flex-direction:column;justify-content:center;align-items:center;
  border:11px solid #e2e8f0;background:#fff;transition:.4s;
}
.gauge-val{font-size:24px;font-weight:700;color:#1a2340;line-height:1}
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

.pir-badge{display:inline-block;padding:5px 16px;border-radius:50px;font-size:12px;font-weight:700;margin-top:6px}
.pir-ok {background:rgba(34,197,94,.1);color:#16a34a;border:1px solid #22c55e}
.pir-det{background:rgba(239,68,68,.1);color:#dc2626;border:1px solid #ef4444;animation:pir-flash .8s infinite}
@keyframes pir-flash{0%,100%{opacity:1}50%{opacity:.5}}

.salle-equips{display:flex;flex-wrap:wrap;gap:6px;padding-top:10px;border-top:1px solid #f1f5f9;align-items:center}
.equip-label{font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-right:4px;white-space:nowrap}
.equip-chip{background:#f1f5f9;border:1px solid #e2e8f0;border-radius:6px;padding:4px 10px;font-size:11px;color:#475569;font-weight:600}
.equip-chip.ok  {background:rgba(34,197,94,.06);border-color:#bbf7d0;color:#166534}
.equip-chip.warn{background:rgba(245,158,11,.07);border-color:#fde68a;color:#92400e}
.equip-chip.crit{background:rgba(239,68,68,.07);border-color:#fca5a5;color:#991b1b}
</style>

<div class="dash-header">
  <div class="dash-title"><i class="fa-solid fa-gauge-high" style="color:#3b82f6;margin-right:10px"></i>TABLEAU DE BORD EN TEMPS RÉEL</div>
  <div class="dash-live"><div class="dot"></div>EN DIRECT — <span id="last-update">--</span></div>
</div>

<div class="no-data-banner" id="no-data-banner">
  <i class="fa-solid fa-triangle-exclamation"></i> Aucun Arduino connecté — Aucune donnée en temps réel
</div>

<div id="salle-panels"></div>

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
  let p = document.getElementById('panel-' + sid);
  if (!p) {
    p = document.createElement('div');
    p.id        = 'panel-' + sid;
    p.className = 'salle-panel';
    p.innerHTML =
      `<div class="salle-panel-header">
         <span class="salle-panel-nom">
           <i class="fa-solid fa-warehouse" style="color:#3b82f6;margin-right:8px"></i>${nom}
         </span>
         <span class="salle-panel-ts" id="ts-${sid}">—</span>
       </div>
       <div class="gauges" id="gauges-${sid}">
         <div class="gauge-card ok" id="card-temperature-${sid}">
           <div class="gauge-label">Température</div>
           <div class="gauge-ring">
             <span class="gauge-val" id="g-temperature-${sid}">—</span>
             <span class="gauge-unit">°C</span>
           </div>
           <div class="gauge-bar"><div class="gauge-fill" id="f-temperature-${sid}" style="width:0%"></div></div>
         </div>
         <div class="gauge-card ok" id="card-humidite-${sid}">
           <div class="gauge-label">Humidité</div>
           <div class="gauge-ring">
             <span class="gauge-val" id="g-humidite-${sid}">—</span>
             <span class="gauge-unit">%</span>
           </div>
           <div class="gauge-bar"><div class="gauge-fill" id="f-humidite-${sid}" style="width:0%"></div></div>
         </div>
         <div class="gauge-card ok" id="card-gaz-${sid}">
           <div class="gauge-label">Gaz / Qualité air</div>
           <div class="gauge-ring">
             <span class="gauge-val" id="g-gaz-${sid}">—</span>
             <span class="gauge-unit">ppm</span>
           </div>
           <div class="gauge-bar"><div class="gauge-fill" id="f-gaz-${sid}" style="width:0%"></div></div>
         </div>
         <div class="gauge-card ok" id="card-pir-${sid}"
              style="display:flex;flex-direction:column;justify-content:center;align-items:center">
           <div class="gauge-label">Mouvement</div>
           <div style="font-size:42px;margin:10px 0"><i class="fa-solid fa-person-walking"></i></div>
           <div class="pir-badge pir-ok" id="pir-badge-${sid}">AUCUN MOUVEMENT</div>
         </div>
       </div>
       <div class="salle-equips" id="equips-${sid}" style="display:none"></div>`;
    document.getElementById('salle-panels').appendChild(p);
  }
  return p;
}

function majJauge(sid, nom, val) {
  const s    = SEUILS[nom];
  const el   = document.getElementById('g-'    + nom + '-' + sid);
  const fill = document.getElementById('f-'    + nom + '-' + sid);
  const card = document.getElementById('card-' + nom + '-' + sid);
  if (!el || !s) return;
  el.textContent   = val;
  fill.style.width = Math.min(100, (val / s.max) * 100) + '%';
  const lvl = val >= s.crit ? 'crit' : val >= s.warn ? 'warn' : 'ok';
  card.className = 'gauge-card ' + lvl + (val >= s.crit ? ' alerte-critique' : val >= s.warn ? ' alerte-warning' : '');
}

function pollMesuresLive() {
  fetch('/api/mesures-live')
    .then(r => { if (!r.ok) throw 0; return r.json(); })
    .then(data => {
      const keys = Object.keys(data || {});

      if (!keys.length) {
        document.getElementById('no-data-banner').classList.add('visible');
        document.getElementById('last-update').textContent = '--';
        return;
      }
      document.getElementById('no-data-banner').classList.remove('visible');

      keys.forEach(sid => {
        const d = data[sid];
        getPanel(sid, d.salle_nom);

        majJauge(sid, 'temperature', parseFloat(d.temperature) || 0);
        majJauge(sid, 'humidite',    parseFloat(d.humidite)    || 0);
        majJauge(sid, 'gaz',         parseInt(d.gaz)           || 0);

        const pir   = d.pir == 1 || d.pir === true || d.pir === 'true';
        const badge = document.getElementById('pir-badge-'  + sid);
        const pCard = document.getElementById('card-pir-'   + sid);
        if (badge) { badge.className = 'pir-badge ' + (pir ? 'pir-det' : 'pir-ok'); badge.textContent = pir ? 'MOUVEMENT DÉTECTÉ' : 'AUCUN MOUVEMENT'; }
        if (pCard) pCard.className = 'gauge-card ' + (pir ? 'crit alerte-critique' : 'ok');

        const tsEl = document.getElementById('ts-' + sid);
        if (tsEl) tsEl.textContent = 'Màj ' + new Date().toLocaleTimeString('fr-FR');

        const equipsEl = document.getElementById('equips-' + sid);
        if (equipsEl) {
          if (d.equipements && d.equipements.length) {
            const t = parseFloat(d.temperature)||0, h = parseFloat(d.humidite)||0, g = parseInt(d.gaz)||0;
            const lvl = (t>=SEUILS.temperature.crit||h>=SEUILS.humidite.crit||g>=SEUILS.gaz.crit) ? 'crit'
                      : (t>=SEUILS.temperature.warn||h>=SEUILS.humidite.warn||g>=SEUILS.gaz.warn) ? 'warn' : 'ok';
            equipsEl.style.display = '';
            equipsEl.innerHTML =
              '<span class="equip-label"><i class="fa-solid fa-server" style="color:#3b82f6;margin-right:3px"></i>Équipements exposés :</span>'
              + d.equipements.map(e =>
                  `<span class="equip-chip ${lvl}"><i class="fa-solid fa-server" style="font-size:9px;margin-right:3px"></i>${e.nom}</span>`
                ).join('');
          } else {
            equipsEl.style.display = 'none';
          }
        }
      });

      document.querySelectorAll('.salle-panel').forEach(p => {
        if (!data[p.id.replace('panel-', '')]) p.remove();
      });

      document.getElementById('last-update').textContent = new Date().toLocaleTimeString('fr-FR');
    })
    .catch(() => {
      document.getElementById('no-data-banner').classList.add('visible');
      document.getElementById('last-update').textContent = '--';
    });
}

pollMesuresLive();
setInterval(pollMesuresLive, 3000);
</script>

@php $role = session('user')->role ?? ''; @endphp
@if(in_array($role, ['admin','superadmin']))
@php $users = DB::table('users')->orderBy('nom')->get(); @endphp
<div style="margin-top:40px;background:#0e1a38;border:1px solid #1e2f5a;border-radius:14px;overflow:hidden">
  <div style="padding:14px 20px;background:#07102a;border-bottom:1px solid #1e2f5a;font-size:14px;font-weight:700;color:#fff;display:flex;align-items:center;gap:8px">
    <i class="fa-solid fa-users" style="color:#33b5ff"></i> Comptes utilisateurs
    <span style="font-size:11px;font-weight:400;color:#556;margin-left:6px">{{ $users->count() }} inscrit(s)</span>
  </div>
  <table style="width:100%;border-collapse:collapse">
    <thead>
      <tr style="background:#060f1e">
        <th style="padding:9px 16px;text-align:left;font-size:10px;color:#3a4a6a;text-transform:uppercase;letter-spacing:.5px">Utilisateur</th>
        <th style="padding:9px 16px;text-align:left;font-size:10px;color:#3a4a6a;text-transform:uppercase;letter-spacing:.5px">Rôle</th>
        <th style="padding:9px 16px;text-align:center;font-size:10px;color:#3a4a6a;text-transform:uppercase;letter-spacing:.5px">Actif</th>
      </tr>
    </thead>
    <tbody>
    @foreach($users as $u)
    @php $actif = ($u->validation_status ?? '') === 'valide'; @endphp
    <tr style="border-top:1px solid #1e2f5a" id="urow_{{ $u->id }}">
      <td style="padding:10px 16px">
        <div style="font-size:13px;font-weight:600;color:#ccd">{{ $u->prenom ?? '' }} {{ $u->nom ?? '' }}</div>
        <div style="font-size:11px;color:#3a4a6a">{{ $u->email }}</div>
      </td>
      <td style="padding:10px 16px">
        @if($u->id != 1 && ($u->role ?? '') !== 'superadmin')
        <select onchange="uRole({{ $u->id }},this.value)" style="background:#07102a;border:1px solid #1e2f5a;border-radius:6px;padding:5px 8px;color:#fff;font-size:12px;outline:none;cursor:pointer">
          <option value="utilisateur" {{ ($u->role??'') === 'utilisateur' ? 'selected':'' }}>Utilisateur</option>

          <option value="admin"       {{ ($u->role??'') === 'admin'       ? 'selected':'' }}>Admin</option>
          <option value="invite"      {{ ($u->role??'') === 'invite'      ? 'selected':'' }}>Invité</option>
        </select>
        @else
        <span style="font-size:12px;color:#33ff88;font-weight:700">Super Admin</span>
        @endif
      </td>
      <td style="padding:10px 16px;text-align:center">
        @if($u->id != 1 && ($u->role ?? '') !== 'superadmin')
        <label style="position:relative;display:inline-block;width:40px;height:22px">
          <input type="checkbox" {{ $actif ? 'checked':'' }} onchange="uToggle({{ $u->id }},this.checked)" style="opacity:0;width:0;height:0">
          <span class="u-slider" style="position:absolute;inset:0;border-radius:22px;cursor:pointer;transition:.3s;background:{{ $actif ? 'rgba(51,255,136,.2)':'#1e2f5a' }};border:1px solid {{ $actif ? '#33ff88':'#2a3a5a' }}">
            <span style="position:absolute;width:16px;height:16px;bottom:2px;left:{{ $actif ? '20px':'2px' }};border-radius:50%;background:{{ $actif ? '#33ff88':'#3a4a6a' }};transition:.3s"></span>
          </span>
        </label>
        @else
        <span style="font-size:10px;color:#3a4a6a">—</span>
        @endif
      </td>
    </tr>
    @endforeach
    </tbody>
  </table>
</div>
<script>
function uRole(id,role){
  csrfFetch('/user/'+id+'/modifier',{method:'POST',body:JSON.stringify({role:role})})
    .then(r=>r.json()).then(d=>notify(d.success?'Rôle mis à jour.':(d.error||'Erreur.'),d.success?'s':'e'))
    .catch(()=>notify('Erreur réseau.','e'));
}
function uToggle(id,actif){
  csrfFetch('/user/'+id+'/statut',{method:'POST',body:JSON.stringify({status:actif?'valide':'bloque'})})
    .then(r=>r.json()).then(d=>notify(d.success?(actif?'Compte activé.':'Compte bloqué.'):(d.error||'Erreur.'),d.success?'s':'e'))
    .catch(()=>notify('Erreur réseau.','e'));
}
</script>
@endif
@endsection
