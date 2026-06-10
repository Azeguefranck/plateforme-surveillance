@extends('layouts.app')

@section('content')

@php
$s = $seuils;
$def = [
    'temperature' => ['warning'=>28,   'critique'=>32,   'unite'=>'°C',  'max'=>100,  'ico'=>'<i class="fa-solid fa-temperature-half"></i>', 'label'=>'Température',   'color'=>'#ff5733', 'desc'=>'Surchauffe des serveurs'],
    'humidite'    => ['warning'=>75,   'critique'=>85,   'unite'=>'%',   'max'=>100,  'ico'=>'<i class="fa-solid fa-droplet"></i>', 'label'=>'Humidité',       'color'=>'#33b5ff', 'desc'=>'Condensation et corrosion'],
    'gaz'         => ['warning'=>400,  'critique'=>600,  'unite'=>'ppm', 'max'=>1000, 'ico'=>'<i class="fa-solid fa-wind"></i>', 'label'=>'Gaz / Air',      'color'=>'#ffd633', 'desc'=>'Fuite dangereuse, risque incendie'],
];
$fieldMap = [
    'temperature' => ['w'=>'temp_warning',  'c'=>'temp_critique'],
    'humidite'    => ['w'=>'hum_warning',   'c'=>'hum_critique'],
    'gaz'         => ['w'=>'gaz_warning',   'c'=>'gaz_critique'],
];
@endphp

<style>
*{box-sizing:border-box}
body{background:#060d1f;color:#e0e8ff;font-family:'Segoe UI',Arial,sans-serif}

.p-header{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:28px;flex-wrap:wrap;gap:12px;
}
.p-header h1{
  font-size:24px;font-weight:700;color:#fff;
  letter-spacing:1px;display:flex;align-items:center;gap:10px;
}
.p-header h1 span{color:#33ff88}
.breadcrumb{font-size:12px;color:#5a6a99}
.breadcrumb a{color:#33ff88;text-decoration:none}

.flash{
  padding:12px 18px;border-radius:10px;margin-bottom:20px;
  font-size:14px;font-weight:600;display:flex;align-items:center;gap:10px;
  animation:fadeUp .4s ease;
}
.flash-ok{background:rgba(51,255,136,.1);border:1px solid rgba(51,255,136,.3);color:#33ff88}
.flash-err{background:rgba(255,87,51,.1);border:1px solid rgba(255,87,51,.3);color:#ff5733}
@keyframes fadeUp{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}

.card{
  background:linear-gradient(135deg,#0e1a38,#0c1530);
  border:1px solid #1e2f5a;border-radius:18px;
  padding:26px;position:relative;overflow:hidden;
  transition:border-color .3s;margin-bottom:20px;
}
.card:hover{border-color:rgba(51,255,136,.18)}
.card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:2px;
  background:linear-gradient(90deg,transparent,rgba(51,255,136,.35),transparent);
  opacity:0;transition:.3s;
}
.card:hover::before{opacity:1}
.card-title{
  font-size:13px;font-weight:700;letter-spacing:1.5px;color:#8899cc;
  text-transform:uppercase;margin-bottom:22px;
  display:flex;align-items:center;gap:8px;
}
.card-title::before{
  content:'';width:3px;height:14px;border-radius:2px;
  background:linear-gradient(180deg,#33ff88,#33b5ff);flex-shrink:0;
}

.sensor-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:16px;
}

.sc{
  background:rgba(255,255,255,.025);
  border:1px solid #1e2f5a;border-radius:14px;
  padding:18px;transition:.3s;position:relative;overflow:hidden;
}
.sc:hover{border-color:rgba(51,255,136,.2);background:rgba(255,255,255,.04)}

.sc-head{
  display:flex;align-items:center;gap:10px;margin-bottom:14px;
}
.sc-ico{
  font-size:22px;width:42px;height:42px;
  display:flex;align-items:center;justify-content:center;
  border-radius:10px;flex-shrink:0;
  background:rgba(255,255,255,.04);border:1px solid #1e2f5a;
}
.sc-info{flex:1}
.sc-name{font-size:15px;font-weight:700;color:#fff}
.sc-desc{font-size:11px;color:#5a6a99;margin-top:2px}

.sc-bar{
  height:4px;border-radius:2px;background:#1e2f5a;
  margin-bottom:14px;overflow:hidden;
}
.sc-fill{height:100%;border-radius:2px;transition:width .6s ease}

.sc-fields{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.sc-field{display:flex;flex-direction:column;gap:4px}
.sc-field label{
  font-size:10px;font-weight:700;color:#5a6a99;
  letter-spacing:.5px;text-transform:uppercase;
}
.sc-field input{
  background:rgba(255,255,255,.04);
  border:1px solid #1e2f5a;border-radius:7px;
  padding:9px 12px;color:#e0e8ff;font-size:14px;
  font-weight:600;outline:none;transition:.25s;
  font-family:inherit;width:100%;
}
.sc-field input:focus{
  border-color:#33ff88;
  box-shadow:0 0 0 3px rgba(51,255,136,.07);
}
.lbl-warn{color:#ffd633!important}
.lbl-crit{color:#ff5733!important}
.input-warn:focus{border-color:#ffd633!important;box-shadow:0 0 0 3px rgba(255,214,51,.07)!important}
.input-crit:focus{border-color:#ff5733!important;box-shadow:0 0 0 3px rgba(255,87,51,.07)!important}

.unite-tag{
  display:inline-block;padding:1px 6px;border-radius:4px;
  font-size:10px;font-weight:700;background:rgba(255,255,255,.06);
  color:#8899cc;margin-left:4px;vertical-align:middle;
}

.pir-card{
  display:flex;align-items:center;justify-content:space-between;
  background:rgba(255,255,255,.025);
  border:1px solid #1e2f5a;border-radius:14px;
  padding:18px 22px;transition:.3s;
}
.pir-card:hover{border-color:rgba(51,255,136,.2);background:rgba(255,255,255,.04)}
.pir-left{display:flex;align-items:center;gap:12px}
.pir-ico{
  font-size:24px;width:46px;height:46px;
  display:flex;align-items:center;justify-content:center;
  background:rgba(255,255,255,.04);border:1px solid #1e2f5a;border-radius:10px;
}
.pir-name{font-size:15px;font-weight:700;color:#fff}
.pir-desc{font-size:11px;color:#5a6a99;margin-top:2px}

.toggle{position:relative;display:inline-block;width:52px;height:28px}
.toggle input{opacity:0;width:0;height:0}
.slider{
  position:absolute;inset:0;background:#1e2f5a;
  border-radius:28px;cursor:pointer;transition:.3s;
}
.slider::before{
  content:'';position:absolute;height:22px;width:22px;
  left:3px;top:3px;background:#5a6a99;
  border-radius:50%;transition:.3s;
}
.toggle input:checked + .slider{background:rgba(51,255,136,.2);border:1px solid rgba(51,255,136,.4)}
.toggle input:checked + .slider::before{transform:translateX(24px);background:#33ff88;box-shadow:0 0 10px rgba(51,255,136,.5)}

.btn-save{
  display:inline-flex;align-items:center;gap:9px;
  padding:13px 28px;border-radius:10px;border:none;
  background:linear-gradient(135deg,rgba(51,255,136,.15),rgba(51,255,136,.08));
  border:1px solid rgba(51,255,136,.35);
  color:#33ff88;font-size:15px;font-weight:700;
  cursor:pointer;transition:.25s;letter-spacing:.8px;
  text-transform:uppercase;
}
.btn-save:hover{
  background:linear-gradient(135deg,rgba(51,255,136,.22),rgba(51,255,136,.12));
  box-shadow:0 0 24px rgba(51,255,136,.3);transform:translateY(-1px);
}

.cfg-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:700px){.cfg-grid{grid-template-columns:1fr}}
.cfg-item{
  display:flex;align-items:flex-start;gap:12px;padding:14px;
  background:rgba(255,255,255,.025);border-radius:10px;
  border:1px solid transparent;transition:.25s;
}
.cfg-item:hover{border-color:#1e2f5a}
.cfg-ico{
  font-size:18px;width:36px;height:36px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  background:rgba(255,255,255,.04);border-radius:8px;border:1px solid #1e2f5a;
}
.cfg-key{font-size:11px;color:#5a6a99;letter-spacing:.5px;text-transform:uppercase;font-weight:700;margin-bottom:4px}
.cfg-val{font-size:14px;color:#c7d2ff;font-weight:500;word-break:break-all}
.cfg-val.green{color:#33ff88}

.status-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px}
.stat-c{
  background:rgba(255,255,255,.025);border-radius:12px;
  padding:16px;text-align:center;border:1px solid transparent;transition:.25s;
}
.stat-c:hover{border-color:#1e2f5a}
.stat-num{font-size:28px;font-weight:700;color:#33ff88;line-height:1}
.stat-lab{font-size:11px;color:#5a6a99;margin-top:5px;letter-spacing:.5px;text-transform:uppercase}
.stat-c.warn .stat-num{color:#ffd633}
.stat-c.crit .stat-num{color:#ff5733}

.sep{height:1px;margin:20px 0;background:linear-gradient(90deg,transparent,#1e2f5a,transparent)}

@media(max-width:600px){
  .sensor-grid{grid-template-columns:1fr}
  .sc-fields{grid-template-columns:1fr}
  .pir-card{flex-direction:column;gap:14px;align-items:flex-start}
}
</style>


<div class="p-header">
  <h1><i class="fa-solid fa-gear"></i> <span>Paramètres</span> Système</h1>
  <div class="breadcrumb"><a href="/dashboard">Dashboard</a> / Paramètres</div>
</div>

@if(session('success_seuils'))
  <div class="flash flash-ok"><i class="fa-solid fa-circle-check" style="color:#33ff88"></i> {{ session('success_seuils') }}</div>
@endif
@if($errors->any())
  @foreach($errors->all() as $err)
    <div class="flash flash-err"><i class="fa-solid fa-triangle-exclamation" style="color:#ffd633"></i> {{ $err }}</div>
  @endforeach
@endif


<div class="card">
  <div class="card-title"><i class="fa-solid fa-satellite-dish"></i> Seuils d'alerte capteurs</div>

  <form action="/parametres/seuils" method="POST">
    @csrf

    <div class="sensor-grid">

      @foreach($def as $key => $meta)
      @php
        $wVal = $s[$key]['warning']  ?? $meta['warning'];
        $cVal = $s[$key]['critique'] ?? $meta['critique'];
        $fw   = $fieldMap[$key]['w'];
        $fc   = $fieldMap[$key]['c'];
      @endphp
      <div class="sc">
        <div class="sc-head">
          <div class="sc-ico">{!! $meta['ico'] !!}</div>
          <div class="sc-info">
            <div class="sc-name">{{ $meta['label'] }} <span class="unite-tag">{{ $meta['unite'] }}</span></div>
            <div class="sc-desc">{{ $meta['desc'] }}</div>
          </div>
        </div>
        <div class="sc-bar">
          <div class="sc-fill" style="width:{{ min(100, ($cVal/$meta['max'])*100) }}%;background:{{ $meta['color'] }}"></div>
        </div>
        <div class="sc-fields">
          <div class="sc-field">
            <label class="lbl-warn"><i class="fa-solid fa-triangle-exclamation"></i> Avertissement</label>
            <input type="number" name="{{ $fw }}" value="{{ $wVal }}"
              step="0.1" min="0" max="{{ $meta['max'] }}"
              class="input-warn" required>
          </div>
          <div class="sc-field">
            <label class="lbl-crit"><i class="fa-solid fa-circle" style="color:#ff5733"></i> Critique</label>
            <input type="number" name="{{ $fc }}" value="{{ $cVal }}"
              step="0.1" min="0" max="{{ $meta['max'] }}"
              class="input-crit" required>
          </div>
        </div>
      </div>
      @endforeach

    </div>

    <div class="sep"></div>

    <div class="pir-card">
      <div class="pir-left">
        <div class="pir-ico"><i class="fa-solid fa-person-walking"></i></div>
        <div>
          <div class="pir-name">Détecteur PIR <span class="unite-tag">mouvement</span></div>
          <div class="pir-desc">Alerte intrusion — Surveillance active de la salle</div>
        </div>
      </div>
      <label class="toggle">
        <input type="checkbox" name="pir_actif" value="1" {{ ($s['pir']['actif'] ?? 1) ? 'checked' : '' }}>
        <span class="slider"></span>
      </label>
    </div>

    <div style="display:flex;justify-content:flex-end;margin-top:22px">
      <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Sauvegarder les seuils</button>
    </div>

  </form>
</div>


<div class="card">
  <div class="card-title"><i class="fa-solid fa-wrench"></i> Configuration système</div>

  <div class="cfg-grid">
    <div class="cfg-item">
      <div class="cfg-ico"><i class="fa-solid fa-envelope"></i></div>
      <div>
        <div class="cfg-key">Email administrateur</div>
        <div class="cfg-val">franckazegue0007@gmail.com</div>
      </div>
    </div>
    <div class="cfg-item">
      <div class="cfg-ico"><i class="fa-solid fa-mobile-screen-button"></i></div>
      <div>
        <div class="cfg-key">Email administrateur</div>
        <div class="cfg-val">franckazegue0007@gmail.com</div>
      </div>
    </div>
    <div class="cfg-item">
      <div class="cfg-ico"><i class="fa-solid fa-clock"></i></div>
      <div>
        <div class="cfg-key">Intervalle envoi Arduino</div>
        <div class="cfg-val">30 secondes</div>
      </div>
    </div>
    <div class="cfg-item">
      <div class="cfg-ico"><i class="fa-solid fa-rotate"></i></div>
      <div>
        <div class="cfg-key">Actualisation dashboard</div>
        <div class="cfg-val">1 seconde (AJAX temps réel)</div>
      </div>
    </div>
    <div class="cfg-item">
      <div class="cfg-ico"><i class="fa-solid fa-globe"></i></div>
      <div>
        <div class="cfg-key">SMTP Gmail</div>
        <div class="cfg-val green">● Configuré</div>
      </div>
    </div>
    <div class="cfg-item">
      <div class="cfg-ico"><i class="fa-solid fa-lock"></i></div>
      <div>
        <div class="cfg-key">Validation comptes</div>
        <div class="cfg-val green">● Activée</div>
      </div>
    </div>
    <div class="cfg-item">
      <div class="cfg-ico"><i class="fa-solid fa-satellite-dish"></i></div>
      <div>
        <div class="cfg-key">Capteurs actifs</div>
        <div class="cfg-val">DHT22, MQ135, PIR</div>
      </div>
    </div>
    <div class="cfg-item">
      <div class="cfg-ico"><i class="fa-solid fa-rocket"></i></div>
      <div>
        <div class="cfg-key">Version plateforme</div>
        <div class="cfg-val">Plateforme de Surveillance v2.0</div>
      </div>
    </div>
  </div>
</div>


<div class="card">
  <div class="card-title" style="justify-content:space-between;display:flex;align-items:center">
    <span style="display:flex;align-items:center;gap:8px">
      <span style="content:'';width:3px;height:14px;border-radius:2px;background:linear-gradient(180deg,#33ff88,#33b5ff);display:inline-block"></span>
      <i class="fa-solid fa-chart-bar"></i> Statut en temps réel
    </span>
    <span style="font-size:11px;color:#5a6a99;font-weight:400;letter-spacing:0;text-transform:none">Mise à jour auto — 10s</span>
  </div>

  <div class="status-row" id="status-row">
    <div class="stat-c">
      <div class="stat-num" id="st-mesures">—</div>
      <div class="stat-lab">Mesures enreg.</div>
    </div>
    <div class="stat-c warn">
      <div class="stat-num" id="st-warn">—</div>
      <div class="stat-lab">Alertes warning</div>
    </div>
    <div class="stat-c crit">
      <div class="stat-num" id="st-crit">—</div>
      <div class="stat-lab">Alertes critiques</div>
    </div>
    <div class="stat-c">
      <div class="stat-num" id="st-users">—</div>
      <div class="stat-lab">Utilisateurs actifs</div>
    </div>
    <div class="stat-c">
      <div class="stat-num" id="st-nonlues">—</div>
      <div class="stat-lab">Alertes non lues</div>
    </div>
  </div>

  <div style="margin-top:14px;font-size:12px;color:#3a4a6a;text-align:right">
    Dernière mesure : <span id="st-last" style="color:#33ff88">—</span>
  </div>
</div>


<script>
document.querySelectorAll('.sc').forEach(sc => {
  const warn = sc.querySelector('.input-warn');
  const crit = sc.querySelector('.input-crit');
  if (!warn || !crit) return;
  [warn, crit].forEach(inp => inp.addEventListener('change', () => {
    if (parseFloat(warn.value) >= parseFloat(crit.value)) {
      crit.value = (parseFloat(warn.value) + 1).toFixed(1);
    }
  }));
});

function loadStats() {
  fetch('/api/stats')
    .then(r => r.json())
    .then(s => {
      document.getElementById('st-mesures').textContent = s.totalMesures     ?? '—';
      document.getElementById('st-warn').textContent    = s.alertesWarning   ?? '—';
      document.getElementById('st-crit').textContent    = s.alertesCritiques ?? '—';
      document.getElementById('st-users').textContent   = s.totalUtilisateurs ?? '—';
      document.getElementById('st-nonlues').textContent = s.alertesNonLues   ?? '—';
      if (s.derniereMesure) {
        document.getElementById('st-last').textContent  = s.derniereMesure.substring(0,16).replace('T',' ');
      }
    }).catch(() => {});
}
loadStats();
setInterval(loadStats, 10000);
</script>

@endsection
