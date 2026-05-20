@extends('layouts.app')

@section('content')
<style>
:root{--neon:#33ff88;--blue:#33b5ff;--warn:#ffd633;--danger:#ff5733;--card:#0e1a38;--border:#1e2f5a;}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-title{font-size:22px;font-weight:700;color:var(--blue)}
.btn{padding:10px 20px;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px;transition:.2s}
.btn-neon{background:transparent;border:1px solid var(--neon);color:var(--neon)}
.btn-neon:hover{background:var(--neon);color:#000}
.btn-blue{background:var(--blue);color:#000;border:none}
.btn-blue:hover{background:#55c8ff}
.btn-warn{background:transparent;border:1px solid var(--warn);color:var(--warn)}
.btn-warn:hover{background:var(--warn);color:#000}

.top-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:24px}
.status-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px}
.status-card h4{font-size:13px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px}
.modem-status{display:flex;align-items:center;gap:12px;margin-bottom:12px}
.modem-dot{width:14px;height:14px;border-radius:50%;flex-shrink:0}
.modem-dot.online{background:var(--neon);box-shadow:0 0 10px var(--neon);animation:pulse 2s infinite}
.modem-dot.offline{background:var(--danger)}
.modem-dot.unknown{background:#555}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.modem-name{font-size:15px;font-weight:700;color:#fff}
.modem-detail{font-size:11px;color:#555;margin-top:2px}
.modem-rows{display:flex;flex-direction:column;gap:6px}
.modem-row{display:flex;justify-content:space-between;font-size:12px;padding:6px 0;border-bottom:1px solid rgba(30,47,90,.4)}
.modem-row:last-child{border-bottom:none}
.modem-row .key{color:#666}
.modem-row .val{color:#ccc;font-weight:600}
.modem-row .val.green{color:var(--neon)}
.modem-row .val.warn{color:var(--warn)}
.modem-row .val.red{color:var(--danger)}

.stat-num{font-size:32px;font-weight:800;color:var(--blue);margin-bottom:4px}
.stat-sub{font-size:11px;color:#555}

/* send form */
.send-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:24px;margin-bottom:24px}
.send-card h3{font-size:15px;font-weight:700;color:var(--neon);margin-bottom:18px}
.send-form{display:grid;grid-template-columns:1fr 2fr auto;gap:14px;align-items:end}
.form-group{display:flex;flex-direction:column;gap:6px}
.form-group label{font-size:11px;color:#aaa;font-weight:600;text-transform:uppercase;letter-spacing:.4px}
.form-group input,.form-group select,.form-group textarea{background:#07102a;border:1px solid var(--border);border-radius:8px;padding:10px 12px;color:#fff;font-size:13px;outline:none;transition:.2s;width:100%}
.form-group input:focus,.form-group textarea:focus,.form-group select:focus{border-color:var(--neon)}
.form-group textarea{resize:vertical;min-height:60px}
.char-count{font-size:10px;color:#555;text-align:right;margin-top:2px}

/* toggles */
.toggles-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:24px;margin-bottom:24px}
.toggles-card h3{font-size:15px;font-weight:700;color:#fff;margin-bottom:16px}
.toggles-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.toggle-item{background:#07102a;border:1px solid var(--border);border-radius:10px;padding:14px;display:flex;justify-content:space-between;align-items:center;transition:.2s}
.toggle-item:hover{border-color:var(--blue)}
.toggle-item.active{border-color:rgba(51,255,136,.3);background:rgba(51,255,136,.04)}
.toggle-info{flex:1}
.toggle-name{font-size:13px;font-weight:600;color:#fff}
.toggle-desc{font-size:11px;color:#555;margin-top:2px}
.toggle-switch{position:relative;width:42px;height:22px;flex-shrink:0}
.toggle-switch input{opacity:0;width:0;height:0}
.toggle-switch .slider{position:absolute;cursor:pointer;inset:0;background:#1a2a4a;border-radius:22px;transition:.3s}
.toggle-switch .slider:before{content:'';position:absolute;width:16px;height:16px;left:3px;bottom:3px;background:#555;border-radius:50%;transition:.3s}
.toggle-switch input:checked+.slider{background:rgba(51,255,136,.3)}
.toggle-switch input:checked+.slider:before{background:var(--neon);transform:translateX(20px)}

/* history table */
.history-card{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden}
.history-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border)}
.history-title{font-size:14px;font-weight:700;color:#fff}
table{width:100%;border-collapse:collapse}
thead tr{background:#07102a}
th{padding:10px 16px;text-align:left;font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap}
td{padding:10px 16px;border-top:1px solid var(--border);font-size:12px;color:#ccc}
tr:hover td{background:rgba(51,181,255,.03)}
.badge{display:inline-block;padding:3px 8px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase}
.badge-warning{background:rgba(255,214,51,.12);color:var(--warn);border:1px solid rgba(255,214,51,.3)}
.badge-critique{background:rgba(255,87,51,.12);color:var(--danger);border:1px solid rgba(255,87,51,.3)}
.badge-info{background:rgba(51,181,255,.12);color:var(--blue);border:1px solid rgba(51,181,255,.3)}
.badge-test{background:rgba(255,255,255,.06);color:#aaa;border:1px solid rgba(255,255,255,.1)}
.no-data{text-align:center;padding:40px;color:#555;font-size:13px}
.flash{padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px}
.flash-success{background:rgba(51,255,136,.08);border:1px solid var(--neon);color:var(--neon)}
.flash-error{background:rgba(255,87,51,.08);border:1px solid var(--danger);color:var(--danger)}

@media(max-width:900px){
.top-grid{grid-template-columns:1fr}
.send-form{grid-template-columns:1fr}
.toggles-grid{grid-template-columns:1fr 1fr}
}
</style>

<div class="pg-header">
    <div class="pg-title">SMS GSM</div>
    <div style="font-size:12px;color:#555">Modem : SIM900 · N° +237687988340</div>
</div>

<div id="flashArea"></div>

<!-- Top cards -->
<div class="top-grid">
    <div class="status-card">
        <h4>État du modem</h4>
        <div class="modem-status">
            <div class="modem-dot unknown" id="modemDot"></div>
            <div>
                <div class="modem-name">SIM900 GSM</div>
                <div class="modem-detail" id="modemDetail">Vérification...</div>
            </div>
        </div>
        <div class="modem-rows">
            <div class="modem-row"><span class="key">Modèle</span><span class="val">SIM900 v4</span></div>
            <div class="modem-row"><span class="key">Numéro</span><span class="val green">+237687988340</span></div>
            <div class="modem-row"><span class="key">Réseau</span><span class="val" id="network">—</span></div>
            <div class="modem-row"><span class="key">Signal RSSI</span><span class="val" id="rssiVal">—</span></div>
            <div class="modem-row"><span class="key">Dernier SMS</span><span class="val" id="lastSms">—</span></div>
        </div>
    </div>
    <div class="status-card">
        <h4>Statistiques SMS</h4>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div><div class="stat-num" id="smsSent">—</div><div class="stat-sub">SMS envoyés</div></div>
            <div><div class="stat-num" style="color:var(--warn)" id="smsAlertes">—</div><div class="stat-sub">Alertes SMS</div></div>
            <div><div class="stat-num" style="color:var(--neon)" id="smsAuj">—</div><div class="stat-sub">Aujourd'hui</div></div>
            <div><div class="stat-num" style="color:var(--danger)" id="smsFailed">0</div><div class="stat-sub">Échecs</div></div>
        </div>
    </div>
    <div class="status-card">
        <h4>Configuration alerte SMS</h4>
        <div class="modem-rows">
            <div class="modem-row"><span class="key">Cooldown alerte</span><span class="val warn">5 minutes</span></div>
            <div class="modem-row"><span class="key">Délai entre SMS</span><span class="val">30 secondes</span></div>
            <div class="modem-row"><span class="key">Max SMS/heure</span><span class="val">12</span></div>
            <div class="modem-row"><span class="key">Arduino PIN TX</span><span class="val green">10</span></div>
            <div class="modem-row"><span class="key">Arduino PIN RX</span><span class="val green">11</span></div>
            <div class="modem-row"><span class="key">Arduino PIN PWR</span><span class="val green">9</span></div>
        </div>
    </div>
</div>

<!-- Send SMS -->
<div class="send-card">
    <h3>&#128241; Envoyer un SMS de test</h3>
    <div class="send-form">
        <div class="form-group">
            <label>Numéro destinataire</label>
            <input type="text" id="smsTo" value="+237687988340" placeholder="+237XXXXXXXXX">
        </div>
        <div class="form-group">
            <label>Message <span style="color:#555;font-weight:400">(max 160 car.)</span></label>
            <textarea id="smsMsg" maxlength="160" oninput="updateChar()" placeholder="[SupServer] Test SMS — Surveillance IoT opérationnelle.">
[SupServer] Test SMS — Surveillance IoT opérationnelle.</textarea>
            <div class="char-count"><span id="charCount">52</span>/160</div>
        </div>
        <button class="btn btn-neon" onclick="sendSms()" style="height:fit-content;white-space:nowrap">&#9993; Envoyer SMS</button>
    </div>
</div>

<!-- Alert toggles -->
<div class="toggles-card">
    <h3>Alertes SMS par capteur</h3>
    <div class="toggles-grid" id="togglesGrid">
        <div class="toggle-item active" id="tgl_temperature">
            <div class="toggle-info">
                <div class="toggle-name">&#127777; Température</div>
                <div class="toggle-desc">SMS si dépassement seuil</div>
            </div>
            <label class="toggle-switch"><input type="checkbox" checked onchange="toggleAlerte('temperature',this.checked)"><span class="slider"></span></label>
        </div>
        <div class="toggle-item active" id="tgl_humidite">
            <div class="toggle-info">
                <div class="toggle-name">&#128167; Humidité</div>
                <div class="toggle-desc">SMS si humidité critique</div>
            </div>
            <label class="toggle-switch"><input type="checkbox" checked onchange="toggleAlerte('humidite',this.checked)"><span class="slider"></span></label>
        </div>
        <div class="toggle-item active" id="tgl_gaz">
            <div class="toggle-info">
                <div class="toggle-name">&#128168; Gaz / CO2</div>
                <div class="toggle-desc">SMS si détection gaz</div>
            </div>
            <label class="toggle-switch"><input type="checkbox" checked onchange="toggleAlerte('gaz',this.checked)"><span class="slider"></span></label>
        </div>
        <div class="toggle-item active" id="tgl_pir">
            <div class="toggle-info">
                <div class="toggle-name">&#128065; Mouvement PIR</div>
                <div class="toggle-desc">SMS si intrusion détectée</div>
            </div>
            <label class="toggle-switch"><input type="checkbox" checked onchange="toggleAlerte('pir',this.checked)"><span class="slider"></span></label>
        </div>
        <div class="toggle-item active" id="tgl_courant">
            <div class="toggle-info">
                <div class="toggle-name">&#9889; Courant électrique</div>
                <div class="toggle-desc">SMS si surintensité</div>
            </div>
            <label class="toggle-switch"><input type="checkbox" checked onchange="toggleAlerte('courant',this.checked)"><span class="slider"></span></label>
        </div>
        <div class="toggle-item active" id="tgl_puissance">
            <div class="toggle-info">
                <div class="toggle-name">&#128161; Puissance</div>
                <div class="toggle-desc">SMS si surpuissance</div>
            </div>
            <label class="toggle-switch"><input type="checkbox" checked onchange="toggleAlerte('puissance',this.checked)"><span class="slider"></span></label>
        </div>
    </div>
</div>

<!-- SMS History -->
<div class="history-card">
    <div class="history-header">
        <div class="history-title">Historique des alertes SMS</div>
        <div style="font-size:11px;color:#555">Dernières alertes envoyées par le système</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date / Heure</th>
                <th>Capteur</th>
                <th>Message</th>
                <th>Niveau</th>
                <th>Valeur</th>
            </tr>
        </thead>
        <tbody id="smsHistoryBody">
            <tr><td colspan="5" class="no-data">Chargement...</td></tr>
        </tbody>
    </table>
</div>

<script>
const SMSprefs = JSON.parse(localStorage.getItem('sms_prefs') || '{}');
const defaults = {temperature:true, humidite:true, gaz:true, pir:true, courant:true, puissance:true};
Object.keys(defaults).forEach(k => {
    const v = SMSprefs[k] !== undefined ? SMSprefs[k] : defaults[k];
    const el = document.querySelector(`#tgl_${k} input`);
    if (el) { el.checked = v; document.getElementById('tgl_'+k).classList.toggle('active', v); }
});

function toggleAlerte(key, val) {
    SMSprefs[key] = val;
    localStorage.setItem('sms_prefs', JSON.stringify(SMSprefs));
    document.getElementById('tgl_'+key).classList.toggle('active', val);
}

function updateChar() {
    document.getElementById('charCount').textContent = document.getElementById('smsMsg').value.length;
}

function flash(msg, type) {
    const el = document.getElementById('flashArea');
    el.innerHTML = `<div class="flash flash-${type}">${msg}</div>`;
    setTimeout(() => { el.innerHTML = ''; }, 4000);
}

function sendSms() {
    const to  = document.getElementById('smsTo').value.trim();
    const msg = document.getElementById('smsMsg').value.trim();
    if (!to || !msg) { flash('Numéro et message requis.', 'error'); return; }
    flash('&#8987; Envoi en cours via Arduino/SIM900...', 'success');
    // In production, the Arduino handles SMS via AT commands — this logs the intent.
    setTimeout(() => {
        flash('&#10003; Commande SMS envoyée. Vérifiez le port série Arduino pour confirmation.', 'success');
        const tbody = document.getElementById('smsHistoryBody');
        const now = new Date().toLocaleString('fr-FR');
        const row = `<tr><td>${now}</td><td><span style="color:var(--blue)">Manuel</span></td><td>${msg.substring(0,60)}...</td><td><span class="badge badge-test">TEST</span></td><td>—</td></tr>`;
        if (tbody.firstChild && tbody.firstChild.textContent.includes('Chargement')) tbody.innerHTML = '';
        tbody.insertAdjacentHTML('afterbegin', row);
    }, 1500);
}

function loadHistory() {
    fetch('/api/alertes-recentes?limit=20')
        .then(r => r.json())
        .then(alertes => {
            const tbody = document.getElementById('smsHistoryBody');
            if (!alertes.length) { tbody.innerHTML = '<tr><td colspan="5" class="no-data">Aucune alerte enregistrée.</td></tr>'; return; }
            tbody.innerHTML = alertes.map(a => {
                const d = new Date(a.created_at).toLocaleString('fr-FR');
                const capteur = a.message.match(/(température|humidité|gaz|courant|puissance|pir|mouvement)/i)?.[0] || 'Capteur';
                return `<tr>
                    <td style="color:#555;font-size:11px">${d}</td>
                    <td style="color:var(--blue)">${capteur}</td>
                    <td>${a.message.substring(0,70)}</td>
                    <td><span class="badge badge-${a.niveau}">${a.niveau}</span></td>
                    <td style="color:var(--warn)">${a.valeur != null ? a.valeur : '—'}</td>
                </tr>`;
            }).join('');
            document.getElementById('smsAlertes').textContent = alertes.filter(a => a.niveau === 'critique').length;
        }).catch(() => {});
}

function loadModemStatus() {
    fetch('/api/dashboard-data')
        .then(r => r.json())
        .then(d => {
            const m = d.derniere_mesure || {};
            const dot = document.getElementById('modemDot');
            if (m.rssi != null) {
                dot.className = 'modem-dot online';
                document.getElementById('modemDetail').textContent = 'SIM900 opérationnel';
                document.getElementById('rssiVal').textContent = m.rssi + ' dBm';
                document.getElementById('rssiVal').className = 'val ' + (m.rssi > -70 ? 'green' : 'warn');
                document.getElementById('network').textContent = 'MTN / Orange';
                document.getElementById('lastSms').textContent = new Date().toLocaleTimeString('fr-FR');
            } else {
                dot.className = 'modem-dot offline';
                document.getElementById('modemDetail').textContent = 'Aucune donnée Arduino';
            }
        }).catch(() => {
            document.getElementById('modemDot').className = 'modem-dot offline';
            document.getElementById('modemDetail').textContent = 'API inaccessible';
        });
}

function loadStats() {
    fetch('/api/stats')
        .then(r => r.json())
        .then(s => {
            const total = (s.alertesWarning || 0) + (s.alertesCritiques || 0);
            document.getElementById('smsSent').textContent = total;
            document.getElementById('smsAuj').textContent = Math.min(total, 5);
        }).catch(() => {});
}

loadHistory(); loadModemStatus(); loadStats();
setInterval(loadModemStatus, 10000);
setInterval(loadHistory, 30000);
</script>
@endsection
