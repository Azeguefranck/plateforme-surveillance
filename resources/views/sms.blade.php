@extends('layouts.app')

@section('content')

<style>
@keyframes fadeIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.sms-wrap{animation:fadeIn .4s ease;}

/* ── Stats ── */
.stat-row{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:20px;}
.stat-card{
  background:#0d1a2e;border:1px solid #182640;border-radius:14px;
  padding:16px 22px;flex:1 1 140px;min-width:130px;
  display:flex;flex-direction:column;gap:4px;
}
.stat-label{font-size:11px;font-weight:bold;letter-spacing:1.5px;color:#6b7fa0;text-transform:uppercase;}
.stat-val{font-size:28px;font-weight:bold;color:#d4dced;}
.stat-val.green{color:#2fa84f;}
.stat-val.blue{color:#2e86c1;}
.stat-val.orange{color:#e67e22;}

/* ── Cards ── */
.cards-row{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:20px;}
@media(max-width:768px){.cards-row{grid-template-columns:1fr;}}

.panel{background:#0d1a2e;border:1px solid #182640;border-radius:16px;padding:22px;}
.panel-title{
  font-size:14px;font-weight:bold;letter-spacing:1px;color:#2fa84f;
  text-transform:uppercase;margin-bottom:16px;
  display:flex;align-items:center;gap:8px;
}

/* ── GSM Status ── */
.gsm-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.gsm-item{
  background:#091527;border:1px solid #182640;border-radius:10px;
  padding:12px;display:flex;flex-direction:column;gap:4px;
}
.gsm-item-label{font-size:11px;color:#6b7fa0;letter-spacing:.5px;text-transform:uppercase;}
.gsm-item-val{font-size:15px;font-weight:bold;color:#d4dced;}
.gsm-badge{
  display:inline-block;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:bold;
  background:rgba(47,168,79,.15);border:1px solid #2fa84f;color:#2fa84f;
}

/* ── Formulaire SMS ── */
.form-group{display:flex;flex-direction:column;gap:6px;margin-bottom:14px;}
.form-label{font-size:12px;font-weight:bold;color:#6b7fa0;letter-spacing:.5px;}
.form-control{
  background:#0a1525;border:1.5px solid #1e3050;border-radius:9px;
  color:#d4dced;padding:9px 13px;font-size:13px;outline:none;
  transition:border-color .2s;width:100%;
}
.form-control:focus{border-color:#2fa84f;}
.btn-send{
  background:#2fa84f;color:#060c1a;border:none;border-radius:9px;
  padding:10px 22px;font-weight:bold;font-size:14px;cursor:pointer;transition:.2s;
  display:inline-flex;align-items:center;gap:8px;
}
.btn-send:hover{background:#249040;}
.btn-send:disabled{background:#182640;color:#6b7fa0;cursor:not-allowed;}

/* ── Tableau historique ── */
.table-wrap{
  background:#0d1a2e;border:1px solid #182640;border-radius:16px;overflow:hidden;margin-top:20px;
}
.panel-title-row{
  display:flex;justify-content:space-between;align-items:center;
  padding:16px 18px;border-bottom:1px solid #182640;
}
.tbl{width:100%;border-collapse:collapse;font-size:13px;}
.tbl th{
  background:#091527;padding:12px 14px;text-align:left;
  color:#6b7fa0;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;
  border-bottom:1px solid #182640;
}
.tbl td{padding:11px 14px;border-bottom:1px solid rgba(24,38,64,.6);color:#d4dced;vertical-align:middle;}
.tbl tr:last-child td{border-bottom:none;}
.tbl tbody tr:hover td{background:rgba(47,168,79,.04);}

.badge-sent{background:rgba(47,168,79,.15);border:1px solid #2fa84f;color:#2fa84f;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:bold;}
.badge-fail{background:rgba(231,76,60,.15);border:1px solid #e74c3c;color:#e74c3c;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:bold;}
.badge-pend{background:rgba(230,126,34,.15);border:1px solid #e67e22;color:#e67e22;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:bold;}

.no-data{text-align:center;padding:40px;color:#6b7fa0;}
.loading{text-align:center;padding:30px;color:#6b7fa0;}
</style>

<div class="sms-wrap">

{{-- Stats --}}
<div class="stat-row">
  <div class="stat-card">
    <span class="stat-label">Total SMS</span>
    <span class="stat-val" id="stat-total">—</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Aujourd'hui</span>
    <span class="stat-val green" id="stat-today">—</span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Alertes auto</span>
    <span class="stat-val orange" id="stat-auto">—</span>
  </div>
</div>

<div class="cards-row">
  {{-- État GSM --}}
  <div class="panel">
    <div class="panel-title"><i class="fa-solid fa-sim-card"></i> État GSM / SIM900</div>
    <div class="gsm-grid">
      <div class="gsm-item">
        <div class="gsm-item-label">Statut</div>
        <div><span class="gsm-badge" id="gsm-status">CONNECTÉ</span></div>
      </div>
      <div class="gsm-item">
        <div class="gsm-item-label">Signal</div>
        <div class="gsm-item-val" id="gsm-signal">—</div>
      </div>
      <div class="gsm-item">
        <div class="gsm-item-label">Opérateur</div>
        <div class="gsm-item-val" id="gsm-operateur">—</div>
      </div>
      <div class="gsm-item">
        <div class="gsm-item-label">Modèle</div>
        <div class="gsm-item-val" id="gsm-modele">SIM900</div>
      </div>
    </div>
  </div>

  {{-- Envoyer SMS --}}
  <div class="panel">
    <div class="panel-title"><i class="fa-solid fa-paper-plane"></i> Envoyer SMS manuel</div>
    <div class="form-group">
      <label class="form-label">Numéro de téléphone</label>
      <input class="form-control" type="tel" id="sms-numero" placeholder="+237 6XX XXX XXX">
    </div>
    <div class="form-group">
      <label class="form-label">Message (max 160 car.)</label>
      <textarea class="form-control" id="sms-message" rows="3" maxlength="160" placeholder="Votre message..."></textarea>
    </div>
    <button class="btn-send" id="btn-send-sms" onclick="sendSMS()">
      <i class="fa-solid fa-paper-plane"></i> Envoyer
    </button>
    <div id="send-result" style="margin-top:10px;font-size:13px;"></div>
  </div>
</div>

{{-- Historique --}}
<div class="table-wrap">
  <div class="panel-title-row">
    <div class="panel-title" style="margin:0;"><i class="fa-solid fa-clock-rotate-left"></i> Historique des SMS</div>
    <button onclick="loadSMS()" style="background:transparent;border:1px solid #182640;color:#6b7fa0;border-radius:8px;padding:6px 14px;cursor:pointer;font-size:12px;">
      <i class="fa-solid fa-rotate"></i> Actualiser
    </button>
  </div>
  <table class="tbl">
    <thead>
      <tr>
        <th>#</th>
        <th>Date & Heure</th>
        <th>Destinataire</th>
        <th>Message</th>
        <th>Type</th>
        <th>Statut</th>
      </tr>
    </thead>
    <tbody id="sms-body">
      <tr><td colspan="6" class="loading">Chargement...</td></tr>
    </tbody>
  </table>
</div>

</div>

<script>
async function loadSMS(){
  const tbody = document.getElementById('sms-body');
  tbody.innerHTML = '<tr><td colspan="6" class="loading">Chargement...</td></tr>';
  try {
    const r = await fetch('/api/sms/log');
    const d = await r.json();
    const list = Array.isArray(d) ? d : (d.data || []);

    // Stats
    const today = new Date().toLocaleDateString('fr-FR');
    document.getElementById('stat-total').textContent = list.length;
    document.getElementById('stat-today').textContent = list.filter(s => s.created_at && new Date(s.created_at).toLocaleDateString('fr-FR')===today).length;
    document.getElementById('stat-auto').textContent  = list.filter(s => s.type==='alerte' || s.type==='auto').length;

    if(!list.length){
      tbody.innerHTML='<tr><td colspan="6" class="no-data">Aucun SMS enregistré</td></tr>';
      return;
    }
    tbody.innerHTML = list.slice(0,50).map((s,i)=>{
      const dt = s.created_at ? new Date(s.created_at).toLocaleString('fr-FR') : '—';
      let badge = '';
      switch(s.statut||s.status){
        case 'envoye': case 'sent': badge='<span class="badge-sent">ENVOYÉ</span>'; break;
        case 'echec':  case 'failed': badge='<span class="badge-fail">ECHEC</span>'; break;
        default: badge='<span class="badge-pend">EN ATTENTE</span>';
      }
      return `<tr>
        <td style="color:#6b7fa0">${i+1}</td>
        <td style="font-size:12px">${dt}</td>
        <td style="color:#4a9fc4">${s.destinataire||s.numero||'—'}</td>
        <td style="max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${s.message||'—'}</td>
        <td style="color:#6b7fa0;font-size:12px">${s.type||'manuel'}</td>
        <td>${badge}</td>
      </tr>`;
    }).join('');
  } catch(e){
    tbody.innerHTML='<tr><td colspan="6" class="no-data" style="color:#e74c3c">Données indisponibles</td></tr>';
  }
}

async function loadGSM(){
  try {
    const r = await fetch('/api/gsm/status');
    const d = await r.json();
    if(d.signal)    document.getElementById('gsm-signal').textContent    = d.signal    + ' dBm';
    if(d.operateur) document.getElementById('gsm-operateur').textContent = d.operateur;
    if(d.modele)    document.getElementById('gsm-modele').textContent    = d.modele;
    if(d.statut){
      const el = document.getElementById('gsm-status');
      el.textContent = d.statut.toUpperCase();
      el.style.background = d.statut==='connecte' ? 'rgba(47,168,79,.15)' : 'rgba(231,76,60,.15)';
      el.style.borderColor = d.statut==='connecte' ? '#2fa84f' : '#e74c3c';
      el.style.color       = d.statut==='connecte' ? '#2fa84f' : '#e74c3c';
    }
  } catch(e){}
}

async function sendSMS(){
  const num = document.getElementById('sms-numero').value.trim();
  const msg = document.getElementById('sms-message').value.trim();
  const res = document.getElementById('send-result');
  const btn = document.getElementById('btn-send-sms');

  if(!num || !msg){ res.style.color='#e74c3c'; res.textContent='Numéro et message requis.'; return; }

  btn.disabled=true; btn.textContent='Envoi...';
  res.textContent='';
  try {
    const r = await fetch('/api/sms/send', {
      method:'POST',
      headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
      body: JSON.stringify({numero:num, message:msg})
    });
    const d = await r.json();
    if(r.ok && (d.success || d.statut==='envoye')){
      res.style.color='#2fa84f'; res.textContent='SMS envoyé avec succès !';
      document.getElementById('sms-numero').value='';
      document.getElementById('sms-message').value='';
      loadSMS();
    } else {
      res.style.color='#e74c3c'; res.textContent = d.message || 'Erreur lors de l\'envoi.';
    }
  } catch(e){
    res.style.color='#e74c3c'; res.textContent='Erreur de connexion au serveur.';
  }
  btn.disabled=false; btn.innerHTML='<i class="fa-solid fa-paper-plane"></i> Envoyer';
}

loadSMS();
loadGSM();
</script>

@endsection
