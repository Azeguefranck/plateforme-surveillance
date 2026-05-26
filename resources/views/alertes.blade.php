@extends('layouts.app')

@section('content')

<style>
.page-title{font-size:22px;font-weight:bold;color:#39ff14;margin-bottom:18px;letter-spacing:1px;}

.stats-bar{
  display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:22px;
}
.stat-box{
  background:#111c3d;border-radius:12px;padding:16px;text-align:center;
  border:1px solid #1f2d5e;
}
.stat-num{font-size:26px;font-weight:bold;margin-bottom:4px;}
.stat-label{font-size:11px;color:#9ca3af;letter-spacing:1px;text-transform:uppercase;}
.num-crit{color:#ef4444;}
.num-warn{color:#f59e0b;}
.num-ok{color:#39ff14;}
.num-blue{color:#33b5ff;}

.filter-bar{
  display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;align-items:center;
}
.filter-btn{
  padding:8px 16px;border-radius:8px;border:1px solid #1f2d5e;
  background:#111c3d;color:#9ca3af;cursor:pointer;font-size:13px;
  transition:.2s;
}
.filter-btn:hover,.filter-btn.active{
  background:#1f2d5e;color:white;border-color:#39ff14;
}
.filter-btn.f-crit.active{background:#3d0000;border-color:#ef4444;color:#ef4444;}
.filter-btn.f-warn.active{background:#3d2800;border-color:#f59e0b;color:#f59e0b;}

.refresh-info{margin-left:auto;color:#6b7280;font-size:12px;}
.live-dot{width:8px;height:8px;border-radius:50%;background:#39ff14;display:inline-block;animation:blink 1s infinite;margin-right:4px;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}

.alerts-list{display:flex;flex-direction:column;gap:10px;}

.alert-card{
  background:#111c3d;border-radius:12px;padding:16px 18px;
  border-left:4px solid #1f2d5e;display:flex;align-items:center;gap:16px;
  animation:fadeIn .3s ease;transition:border-color .2s;
}
@keyframes fadeIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.alert-card.CRITIQUE{border-left-color:#ef4444;}
.alert-card.AVERTISSEMENT{border-left-color:#f59e0b;}
.alert-card.info{border-left-color:#6b7280;}
.alert-card.resolu{opacity:.5;}

.badge-niv{
  padding:4px 12px;border-radius:20px;font-size:11px;font-weight:bold;
  letter-spacing:1px;white-space:nowrap;
}
.badge-CRITIQUE{background:#3d0000;color:#ef4444;border:1px solid rgba(239,68,68,.4);}
.badge-AVERTISSEMENT{background:#3d2800;color:#f59e0b;border:1px solid rgba(245,158,11,.4);}
.badge-INFO{background:#1f2d5e;color:#9ca3af;}

.alert-body{flex:1;}
.alert-type{font-weight:bold;color:white;font-size:14px;margin-bottom:3px;}
.alert-val{color:#39ff14;font-size:13px;margin-bottom:3px;}
.alert-msg{color:#9ca3af;font-size:12px;}

.alert-meta{display:flex;flex-direction:column;align-items:flex-end;gap:6px;}
.alert-time{color:#6b7280;font-size:11px;white-space:nowrap;}
.sms-badge{font-size:10px;background:#0f3020;color:#39ff14;padding:2px 8px;border-radius:10px;}
.resolu-badge{font-size:10px;background:#1f2d5e;color:#9ca3af;padding:2px 8px;border-radius:10px;}
.resoudre-btn{
  padding:5px 12px;border:1px solid #1f2d5e;border-radius:6px;
  background:transparent;color:#9ca3af;cursor:pointer;font-size:11px;
  transition:.2s;
}
.resoudre-btn:hover{background:#0f3020;color:#39ff14;border-color:#39ff14;}

.empty-state{
  text-align:center;padding:60px;color:#6b7280;
  background:#111c3d;border-radius:12px;
}
.empty-state p{font-size:36px;margin-bottom:10px;}

@media(max-width:600px){
  .stats-bar{grid-template-columns:1fr 1fr;}
  .alert-card{flex-wrap:wrap;}
  .alert-meta{align-items:flex-start;}
}
</style>

<div class="page-title">🚨 Alertes en temps réel</div>

{{-- Stats --}}
<div class="stats-bar">
  <div class="stat-box">
    <div class="stat-num num-blue"  id="s-total">--</div>
    <div class="stat-label">Total alertes</div>
  </div>
  <div class="stat-box">
    <div class="stat-num num-crit"  id="s-crit">--</div>
    <div class="stat-label">Critiques</div>
  </div>
  <div class="stat-box">
    <div class="stat-num num-warn"  id="s-warn">--</div>
    <div class="stat-label">Non résolues</div>
  </div>
  <div class="stat-box">
    <div class="stat-num num-ok"    id="s-today">--</div>
    <div class="stat-label">Aujourd'hui</div>
  </div>
</div>

{{-- Filtres --}}
<div class="filter-bar">
  <button class="filter-btn active" onclick="filtrer('tous',this)">Toutes</button>
  <button class="filter-btn f-crit" onclick="filtrer('CRITIQUE',this)">🔴 Critiques</button>
  <button class="filter-btn f-warn" onclick="filtrer('AVERTISSEMENT',this)">🟡 Avertissements</button>
  <button class="filter-btn" onclick="filtrer('non-resolu',this)">Non résolues</button>
  <span class="refresh-info"><span class="live-dot"></span>Actualisation automatique</span>
</div>

<div class="alerts-list" id="alerts-list">
  <div class="empty-state"><p>⏳</p>Chargement…</div>
</div>

<script>
let filtreCourant = 'tous';
let toutesAlertes = [];

function filtrer(f, btn) {
  filtreCourant = f;
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderAlertes();
}

function renderAlertes() {
  let data = toutesAlertes;

  if (filtreCourant === 'CRITIQUE')      data = data.filter(a => a.niveau === 'CRITIQUE');
  if (filtreCourant === 'AVERTISSEMENT') data = data.filter(a => a.niveau === 'AVERTISSEMENT');
  if (filtreCourant === 'non-resolu')    data = data.filter(a => !a.resolu);

  const list = document.getElementById('alerts-list');

  if (!data.length) {
    list.innerHTML = '<div class="empty-state"><p>✅</p>Aucune alerte dans cette catégorie</div>';
    return;
  }

  list.innerHTML = data.map(a => {
    const badgeCls = a.niveau === 'CRITIQUE' ? 'badge-CRITIQUE' : (a.niveau === 'AVERTISSEMENT' ? 'badge-AVERTISSEMENT' : 'badge-INFO');
    const cardCls  = a.niveau + (a.resolu ? ' resolu' : '');
    const time     = a.created_at ? new Date(a.created_at).toLocaleString('fr-FR') : '--';
    const smsBadge = a.envoi_sms ? '<span class="sms-badge">📱 SMS</span>' : '';
    const resTag   = a.resolu ? '<span class="resolu-badge">✓ résolu</span>' : `<button class="resoudre-btn" onclick="resoudre(${a.id}, this)">Résoudre</button>`;
    return `<div class="alert-card ${cardCls}" id="alert-${a.id}">
      <span class="badge-niv ${badgeCls}">${a.niveau || 'INFO'}</span>
      <div class="alert-body">
        <div class="alert-type">${(a.type || 'alerte').toUpperCase()}</div>
        <div class="alert-val">${a.valeur || '--'}</div>
        <div class="alert-msg">${a.message || ''}</div>
      </div>
      <div class="alert-meta">
        <div class="alert-time">${time}</div>
        ${smsBadge}
        ${resTag}
      </div>
    </div>`;
  }).join('');
}

async function chargerAlertes() {
  try {
    const res  = await fetch('/api/alertes/recent?limit=50');
    const json = await res.json();

    toutesAlertes = json.alertes || [];

    const s = json.stats || {};
    document.getElementById('s-total').textContent = s.total      ?? '--';
    document.getElementById('s-crit' ).textContent = s.critiques  ?? '--';
    document.getElementById('s-warn' ).textContent = s.non_resolu ?? '--';
    document.getElementById('s-today').textContent = s.aujourd_hui ?? '--';

    renderAlertes();
  } catch(e) {}
}

async function resoudre(id, btn) {
  btn.disabled = true;
  btn.textContent = '…';
  try {
    await fetch(`/api/alertes/${id}/resoudre`, {method:'POST'});
    const card = document.getElementById('alert-' + id);
    if (card) card.classList.add('resolu');
    btn.textContent = '✓ résolu';
    btn.className = 'resolu-badge';
    await chargerAlertes();
  } catch(e) {
    btn.disabled = false;
    btn.textContent = 'Résoudre';
  }
}

chargerAlertes();
setInterval(chargerAlertes, 5000);
</script>

@endsection
