<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SMS/MAILS — Surveillance IoT</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;}
body{background:#050816;color:white;min-height:100vh;}
.wrapper{display:flex;width:100%;min-height:100vh;}

/* Sidebar */
.sidebar{width:240px;background:#081126;padding:20px;position:fixed;top:0;left:0;bottom:0;overflow-y:auto;}
.logo{font-size:22px;font-weight:bold;color:#39ff14;margin-bottom:25px;}
.sidebar a{display:block;padding:12px;margin-bottom:8px;background:#111c3d;border-radius:10px;text-decoration:none;color:white;font-size:14px;font-weight:bold;transition:.2s;}
.sidebar a:hover{background:#1f2d5e;}

.main{margin-left:240px;width:calc(100% - 240px);padding:20px;}
.topbar{display:flex;justify-content:space-between;align-items:center;background:#111c3d;padding:14px;border-radius:12px;margin-bottom:20px;}
.datetime{font-size:16px;font-weight:bold;color:#00ffcc;}
.logout{background:red;padding:10px 16px;border:none;border-radius:8px;color:white;font-weight:bold;cursor:pointer;text-decoration:none;font-size:14px;}

/* Content */
.page-title{font-size:22px;font-weight:bold;color:#39ff14;margin-bottom:18px;letter-spacing:1px;}

.info-bar{
  display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:22px;
}
.info-box{
  background:#111c3d;border-radius:12px;padding:16px;border:1px solid #1f2d5e;
}
.info-box h4{font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#9ca3af;margin-bottom:8px;}
.info-val{font-size:18px;font-weight:bold;color:#39ff14;}
.info-sub{font-size:12px;color:#6b7280;margin-top:4px;}

.gsm-config{
  background:#111c3d;border-radius:14px;padding:20px;margin-bottom:22px;
  border:1px solid #1f2d5e;
}
.gsm-config h3{color:#39ff14;font-size:14px;margin-bottom:16px;letter-spacing:1px;}
.config-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;}
.config-item{background:#0b1225;border-radius:8px;padding:12px;}
.config-item label{display:block;font-size:11px;color:#9ca3af;margin-bottom:4px;letter-spacing:1px;text-transform:uppercase;}
.config-item span{color:white;font-size:14px;font-weight:bold;}

.sms-table-wrap{background:#111c3d;border-radius:14px;overflow:hidden;border:1px solid #1f2d5e;}
table{width:100%;border-collapse:collapse;font-size:13px;}
th{background:#0b1225;padding:12px 14px;text-align:left;color:#9ca3af;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;border-bottom:1px solid #1f2d5e;}
td{padding:11px 14px;border-bottom:1px solid rgba(31,45,94,.5);color:#d1d5db;}
tr:last-child td{border:none;}
tr:hover td{background:rgba(57,255,20,.03);}

.badge-env{background:#0f3020;color:#39ff14;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:bold;}
.badge-att{background:#3d2800;color:#f59e0b;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:bold;}
.badge-err{background:#3d0000;color:#ef4444;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:bold;}

.msg-preview{
  max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  cursor:pointer;color:#9ca3af;font-size:12px;
}
.msg-preview:hover{color:white;}

.modal-overlay{
  display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:1000;
  align-items:center;justify-content:center;
}
.modal-overlay.open{display:flex;}
.modal-box{
  background:#101935;border-radius:16px;padding:30px;max-width:500px;width:90%;
  border:1px solid #1f2d5e;
}
.modal-box h3{color:#39ff14;margin-bottom:16px;}
.modal-msg{
  background:#0b1225;border-radius:8px;padding:16px;
  color:white;font-size:13px;white-space:pre-wrap;line-height:1.6;
  max-height:300px;overflow-y:auto;
}
.modal-close{margin-top:16px;padding:8px 20px;border:1px solid #1f2d5e;border-radius:6px;background:transparent;color:#9ca3af;cursor:pointer;}
.modal-close:hover{background:#1f2d5e;color:white;}

.refresh-btn{padding:8px 16px;border-radius:8px;border:1px solid #39ff14;background:transparent;color:#39ff14;cursor:pointer;font-size:13px;}
.refresh-btn:hover{background:#39ff14;color:#050816;}

.empty{text-align:center;padding:40px;color:#6b7280;}

@media(max-width:900px){
  .sidebar{position:relative;width:100%;}
  .main{margin-left:0;width:100%;}
  .wrapper{flex-direction:column;}
  .info-bar{grid-template-columns:1fr 1fr;}
}

/* CyberConfirm */
#cc-overlay{display:none;position:fixed;inset:0;z-index:99999;background:rgba(4,8,18,0.78);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);align-items:center;justify-content:center;}
#cc-overlay.cc-open{display:flex;}
#cc-modal{background:rgba(10,20,38,0.97);border:1px solid rgba(57,255,20,0.18);border-radius:18px;padding:36px 30px 28px;max-width:420px;width:90%;text-align:center;box-shadow:0 0 60px rgba(0,0,0,.7);animation:ccZoomIn .24s cubic-bezier(.34,1.28,.64,1) forwards;}
#cc-icon-wrap{width:70px;height:70px;border-radius:50%;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;font-size:28px;}
#cc-icon-wrap.cc-danger{background:rgba(192,57,43,0.13);border:2px solid rgba(192,57,43,0.7);box-shadow:0 0 22px rgba(192,57,43,0.35);}
#cc-icon-wrap.cc-warning{background:rgba(230,126,34,0.13);border:2px solid rgba(230,126,34,0.7);box-shadow:0 0 22px rgba(230,126,34,0.35);}
#cc-icon-wrap.cc-danger #cc-icon{color:#e74c3c;}
#cc-icon-wrap.cc-warning #cc-icon{color:#f39c12;}
#cc-title{font-size:18px;font-weight:bold;color:#d4dced;margin-bottom:10px;}
#cc-message{font-size:14px;color:#8090b0;margin-bottom:28px;line-height:1.6;}
#cc-buttons{display:flex;gap:12px;justify-content:center;}
#cc-cancel{flex:1;padding:12px 16px;border-radius:10px;cursor:pointer;font-size:14px;font-weight:bold;background:rgba(20,40,72,0.5);border:1.5px solid #2e4a7a;color:#4a9fc4;transition:.2s;}
#cc-cancel:hover{background:rgba(30,60,100,0.7);border-color:#4a9fc4;color:#7ec8e3;}
#cc-confirm{flex:1;padding:12px 16px;border-radius:10px;cursor:pointer;font-size:14px;font-weight:bold;border:none;color:#fff;transition:.2s;}
#cc-confirm.cc-danger{background:linear-gradient(135deg,#c0392b,#a93226);box-shadow:0 0 16px rgba(192,57,43,0.45);}
#cc-confirm.cc-danger:hover{background:linear-gradient(135deg,#e74c3c,#c0392b);box-shadow:0 0 28px rgba(192,57,43,0.65);}
#cc-confirm.cc-warning{background:linear-gradient(135deg,#e67e22,#d35400);box-shadow:0 0 16px rgba(230,126,34,0.45);}
#cc-confirm.cc-warning:hover{background:linear-gradient(135deg,#f39c12,#e67e22);box-shadow:0 0 28px rgba(230,126,34,0.65);}
@keyframes ccZoomIn{from{opacity:0;transform:scale(.86);}to{opacity:1;transform:scale(1);}}
@media(max-width:480px){#cc-modal{padding:24px 16px 20px;}#cc-buttons{flex-direction:column-reverse;}#cc-icon-wrap{width:56px;height:56px;}}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="wrapper">
<div class="sidebar">
  <div class="logo">SURVEILLANCE</div>
  <a href="/dashboard">Dashboard</a>
  <a href="/surveillance">Surveillance</a>
  <a href="/alertes">Alertes</a>
  <a href="/historique">Historique</a>
  <a href="/statistiques">Statistiques</a>
  <a href="/sms-gsm" style="background:#0f3020;border:1px solid #39ff14;">SMS/MAILS</a>
  <a href="/anomalies">Anomalies</a>
  <a href="/salles">Salles Serveurs</a>
  <a href="/serveurs-web">Serveurs Web</a>
  <a href="/serveurs-bd">Serveurs BD</a>
  <a href="/parametres">Paramètres</a>
  <a href="/rapports">Rapports</a>
</div>

<div class="main">
  <div class="topbar">
    <div class="datetime" id="dt">--</div>
    <a href="/logout" class="logout" onclick="event.preventDefault();CyberConfirm.show({title:'Déconnexion',message:'Voulez-vous vraiment vous déconnecter ?',icon:'fa-solid fa-arrow-right-from-bracket',confirmText:'Déconnecter',confirmColor:'warning'}).then(ok=>{if(ok)window.location.href='/logout';})">Déconnecter</a>
  </div>

  <div class="page-title">📱 GSM SIM900 — Journal SMS</div>

  <div class="info-bar">
    <div class="info-box">
      <h4>Module GSM</h4>
      <div class="info-val">SIM900</div>
      <div class="info-sub">Arduino UNO — Pins 7/8</div>
    </div>
    <div class="info-box">
      <h4>Numéro administrateur</h4>
      <div class="info-val">+237 687 988 340</div>
      <div class="info-sub">Alertes critiques</div>
    </div>
    <div class="info-box">
      <h4>SMS envoyés aujourd'hui</h4>
      <div class="info-val" id="sms-today">--</div>
      <div class="info-sub">Toutes alertes confondues</div>
    </div>
  </div>

  <div class="gsm-config">
    <h3>⚙️ Configuration Arduino SIM900</h3>
    <div class="config-grid">
      <div class="config-item">
        <label>Baudrate SIM900</label>
        <span>9600 baud</span>
      </div>
      <div class="config-item">
        <label>Pin RX Arduino</label>
        <span>7 (SoftwareSerial)</span>
      </div>
      <div class="config-item">
        <label>Pin TX Arduino</label>
        <span>8 (SoftwareSerial)</span>
      </div>
      <div class="config-item">
        <label>Commandes SMS</label>
        <span>AT+CMGF=1, AT+CMGS</span>
      </div>
      <div class="config-item">
        <label>Sketch Arduino</label>
        <span>surveillance_iot.ino</span>
      </div>
      <div class="config-item">
        <label>Intervalle envoi</label>
        <span>5 secondes</span>
      </div>
    </div>
  </div>

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
    <h3 style="color:white;font-size:14px;">📋 Journal des SMS</h3>
    <button class="refresh-btn" onclick="charger()">🔄 Actualiser</button>
  </div>

  <div class="sms-table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Numéro</th>
          <th>Message (aperçu)</th>
          <th>État</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody id="sms-body">
        <tr><td colspan="5" class="empty">Chargement…</td></tr>
      </tbody>
    </table>
  </div>

</div>
</div>

{{-- Modal message complet --}}
<div class="modal-overlay" id="modal-overlay" onclick="closeModal()">
  <div class="modal-box" onclick="event.stopPropagation()">
    <h3>📩 Message SMS complet</h3>
    <div class="modal-msg" id="modal-content">--</div>
    <button class="modal-close" onclick="closeModal()">Fermer</button>
  </div>
</div>

<!-- CyberConfirm -->
<div id="cc-overlay" role="dialog" aria-modal="true">
  <div id="cc-modal">
    <div id="cc-icon-wrap"><i id="cc-icon"></i></div>
    <div id="cc-title"></div>
    <div id="cc-message"></div>
    <div id="cc-buttons">
      <button id="cc-cancel" type="button"></button>
      <button id="cc-confirm" type="button"></button>
    </div>
  </div>
</div>

<script>
(function(){
  const ov=document.getElementById('cc-overlay');
  const iconWrap=document.getElementById('cc-icon-wrap');
  const iconEl=document.getElementById('cc-icon');
  const titleEl=document.getElementById('cc-title');
  const msgEl=document.getElementById('cc-message');
  const btnCancel=document.getElementById('cc-cancel');
  const btnConfirm=document.getElementById('cc-confirm');
  let _res=null;
  function hide(val){ov.classList.remove('cc-open');if(_res){_res(val);_res=null;}}
  ov.addEventListener('click',function(e){if(e.target===ov)hide(false);});
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&ov.classList.contains('cc-open'))hide(false);});
  btnCancel.addEventListener('click',function(){hide(false);});
  btnConfirm.addEventListener('click',function(){hide(true);});
  window.CyberConfirm={
    show:function(opts){
      const color=opts.confirmColor||'danger';
      iconWrap.className='cc-'+color;
      iconEl.className=opts.icon||'fa-solid fa-circle-exclamation';
      titleEl.textContent=opts.title||'Confirmation';
      msgEl.textContent=opts.message||'';
      btnCancel.innerHTML='<i class="fa-solid fa-xmark"></i> '+(opts.cancelText||'Annuler');
      btnConfirm.innerHTML='<i class="fa-solid fa-check"></i> '+(opts.confirmText||'Confirmer');
      btnConfirm.className='cc-'+color;
      ov.classList.add('cc-open');
      setTimeout(()=>btnConfirm.focus(),80);
      return new Promise(function(resolve){_res=resolve;});
    }
  };
})();

function updateDT() {
  const now = new Date();
  document.getElementById('dt').textContent =
    now.toLocaleDateString('fr-FR') + ' | ' + now.toLocaleTimeString('fr-FR');
}
setInterval(updateDT, 1000);
updateDT();

async function charger() {
  try {
    const res  = await fetch('/api/sms/log');
    const data = await res.json();

    const today = new Date().toDateString();
    const todayCount = data.filter(s =>
      s.created_at && new Date(s.created_at).toDateString() === today
    ).length;
    document.getElementById('sms-today').textContent = todayCount;

    if (!data.length) {
      document.getElementById('sms-body').innerHTML =
        '<tr><td colspan="5" class="empty">Aucun SMS enregistré</td></tr>';
      return;
    }

    document.getElementById('sms-body').innerHTML = data.map(s => {
      const etat = s.etat || 'INCONNU';
      const badgeCls = etat.includes('ENVOY') ? 'badge-env'
                     : etat.includes('ATT')   ? 'badge-att' : 'badge-err';
      const dt = s.created_at ? new Date(s.created_at).toLocaleString('fr-FR') : '--';
      const preview = (s.message || '').replace(/\n/g,' ').slice(0, 60) + '…';
      return `<tr>
        <td style="color:#6b7280">${s.id}</td>
        <td style="color:#39ff14;font-weight:bold;">${s.numero}</td>
        <td class="msg-preview" onclick="showMsg(${JSON.stringify((s.message||'').replace(/"/g,'&quot;'))})">${preview}</td>
        <td><span class="${badgeCls}">${etat}</span></td>
        <td style="font-size:12px;color:#9ca3af;">${dt}</td>
      </tr>`;
    }).join('');
  } catch(e) {
    document.getElementById('sms-body').innerHTML =
      '<tr><td colspan="5" class="empty" style="color:#ef4444;">Erreur de connexion API</td></tr>';
  }
}

function showMsg(msg) {
  document.getElementById('modal-content').textContent = msg;
  document.getElementById('modal-overlay').classList.add('open');
}

function closeModal() {
  document.getElementById('modal-overlay').classList.remove('open');
}

charger();
setInterval(charger, 10000);
</script>

</body>
</html>
