@extends('layouts.app')

@section('content')
<style>
:root{--neon:#33ff88;--blue:#33b5ff;--warn:#ffd633;--danger:#ff5733;--card:#0e1a38;--border:#1e2f5a;}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-title{font-size:22px;font-weight:700;color:var(--neon)}
.conn-status{display:flex;align-items:center;gap:8px;font-size:13px}
.dot{width:10px;height:10px;border-radius:50%;background:#555;animation:pulse 2s infinite}
.dot.live{background:var(--neon);box-shadow:0 0 8px var(--neon)}
.dot.warn{background:var(--warn)}
.dot.dead{background:var(--danger)}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}

/* gauges grid */
.gauges-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:12px;margin-bottom:28px}
.gauge-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:16px 12px;text-align:center;transition:.3s;position:relative;overflow:hidden}
.gauge-card::before{content:'';position:absolute;inset:0;opacity:.05;border-radius:14px;transition:.3s}
.gauge-card.ok::before{background:var(--neon)}
.gauge-card.warn::before{background:var(--warn)}
.gauge-card.crit::before{background:var(--danger)}
.gauge-card.ok{border-color:rgba(51,255,136,.3)}
.gauge-card.warn{border-color:rgba(255,214,51,.4)}
.gauge-card.crit{border-color:rgba(255,87,51,.5);animation:critBlink .8s infinite}
@keyframes critBlink{0%,100%{box-shadow:none}50%{box-shadow:0 0 16px rgba(255,87,51,.4)}}
.gauge-icon{font-size:22px;margin-bottom:6px}
.gauge-label{font-size:10px;color:#888;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px}
.gauge-value{font-size:24px;font-weight:800;margin-bottom:2px;transition:.3s}
.gauge-unit{font-size:10px;color:#666}
.gauge-status{font-size:10px;font-weight:700;margin-top:6px;text-transform:uppercase;letter-spacing:.5px}
.gauge-card.ok .gauge-value,.gauge-card.ok .gauge-status{color:var(--neon)}
.gauge-card.warn .gauge-value,.gauge-card.warn .gauge-status{color:var(--warn)}
.gauge-card.crit .gauge-value,.gauge-card.crit .gauge-status{color:var(--danger)}
.gauge-card.inactive .gauge-value{color:#555}
.gauge-bar{width:100%;height:4px;background:#1a2a4a;border-radius:2px;margin-top:8px;overflow:hidden}
.gauge-bar-fill{height:100%;border-radius:2px;transition:width .6s,background .3s;background:var(--neon)}

/* pir card special */
.pir-badge{display:inline-block;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:700;margin-top:6px}
.pir-active{background:rgba(255,87,51,.15);color:var(--danger);border:1px solid var(--danger)}
.pir-inactive{background:rgba(51,255,136,.1);color:var(--neon);border:1px solid rgba(51,255,136,.3)}

/* charts row */
.charts-row{display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:24px}
.chart-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px}
.chart-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.chart-title{font-size:14px;font-weight:700;color:#fff}
.chart-meta{font-size:11px;color:#555}
canvas{max-height:180px}

/* alerts panel */
.alerts-panel{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden}
.alerts-panel-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border)}
.alerts-panel-title{font-size:14px;font-weight:700;color:#fff}
.alerts-list{max-height:260px;overflow-y:auto}
.alert-item{display:flex;align-items:flex-start;gap:12px;padding:12px 20px;border-bottom:1px solid rgba(30,47,90,.5);transition:.2s}
.alert-item:hover{background:rgba(51,181,255,.04)}
.alert-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-top:4px}
.alert-dot.warning{background:var(--warn)}
.alert-dot.critique{background:var(--danger)}
.alert-msg{font-size:12px;color:#ccc;flex:1}
.alert-time{font-size:10px;color:#555;white-space:nowrap}
.no-alerts{text-align:center;padding:30px;color:#555;font-size:13px}

/* bottom row */
.bottom-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px}
.info-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px}
.info-card h4{font-size:13px;font-weight:700;color:var(--blue);margin-bottom:14px}
.info-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(30,47,90,.4);font-size:12px}
.info-row:last-child{border-bottom:none}
.info-row .key{color:#666}
.info-row .val{color:#ccc;font-weight:600}
.info-row .val.green{color:var(--neon)}
.info-row .val.warn{color:var(--warn)}
.info-row .val.red{color:var(--danger)}

@media(max-width:1200px){.gauges-grid{grid-template-columns:repeat(4,1fr)}}
@media(max-width:768px){
.gauges-grid{grid-template-columns:repeat(2,1fr)}
.charts-row,.bottom-row{grid-template-columns:1fr}
}
</style>

<div class="pg-header">
    <div class="pg-title">Surveillance en Temps Réel</div>
    <div class="conn-status">
        <span class="dot live" id="connDot"></span>
        <span id="connLabel" style="color:var(--neon)">Connecté</span>
        <span style="color:#555;font-size:11px;margin-left:8px" id="lastUpdate">—</span>
    </div>
</div>

<!-- 7 Sensor Gauges -->
<div class="gauges-grid">
    <div class="gauge-card inactive" id="g_temperature">
        <div class="gauge-icon">&#127777;</div>
        <div class="gauge-label">Température</div>
        <div class="gauge-value" id="v_temperature">—</div>
        <div class="gauge-unit">°C</div>
        <div class="gauge-status" id="s_temperature">—</div>
        <div class="gauge-bar"><div class="gauge-bar-fill" id="b_temperature" style="width:0%"></div></div>
    </div>
    <div class="gauge-card inactive" id="g_humidite">
        <div class="gauge-icon">&#128167;</div>
        <div class="gauge-label">Humidité</div>
        <div class="gauge-value" id="v_humidite">—</div>
        <div class="gauge-unit">%</div>
        <div class="gauge-status" id="s_humidite">—</div>
        <div class="gauge-bar"><div class="gauge-bar-fill" id="b_humidite" style="width:0%"></div></div>
    </div>
    <div class="gauge-card inactive" id="g_gaz">
        <div class="gauge-icon">&#128168;</div>
        <div class="gauge-label">Gaz / CO2</div>
        <div class="gauge-value" id="v_gaz">—</div>
        <div class="gauge-unit">ppm</div>
        <div class="gauge-status" id="s_gaz">—</div>
        <div class="gauge-bar"><div class="gauge-bar-fill" id="b_gaz" style="width:0%"></div></div>
    </div>
    <div class="gauge-card inactive" id="g_courant">
        <div class="gauge-icon">&#9889;</div>
        <div class="gauge-label">Courant</div>
        <div class="gauge-value" id="v_courant">—</div>
        <div class="gauge-unit">A</div>
        <div class="gauge-status" id="s_courant">—</div>
        <div class="gauge-bar"><div class="gauge-bar-fill" id="b_courant" style="width:0%"></div></div>
    </div>
    <div class="gauge-card inactive" id="g_puissance">
        <div class="gauge-icon">&#128161;</div>
        <div class="gauge-label">Puissance</div>
        <div class="gauge-value" id="v_puissance">—</div>
        <div class="gauge-unit">W</div>
        <div class="gauge-status" id="s_puissance">—</div>
        <div class="gauge-bar"><div class="gauge-bar-fill" id="b_puissance" style="width:0%"></div></div>
    </div>
    <div class="gauge-card inactive" id="g_tension">
        <div class="gauge-icon">&#128266;</div>
        <div class="gauge-label">Tension</div>
        <div class="gauge-value" id="v_tension">—</div>
        <div class="gauge-unit">V</div>
        <div class="gauge-status" id="s_tension">—</div>
        <div class="gauge-bar"><div class="gauge-bar-fill" id="b_tension" style="width:0%"></div></div>
    </div>
    <div class="gauge-card inactive" id="g_pir">
        <div class="gauge-icon">&#128065;</div>
        <div class="gauge-label">Mouvement PIR</div>
        <div class="gauge-value" style="font-size:16px" id="v_pir">—</div>
        <div id="pir_badge"><span class="pir-badge pir-inactive">INACTIF</span></div>
    </div>
</div>

<!-- Charts row -->
<div class="charts-row">
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Historique Température &amp; Humidité</div>
            <div class="chart-meta" id="chartPoints">0 points</div>
        </div>
        <canvas id="chartTH"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Alertes récentes</div>
            <button onclick="marquerToutLu()" style="background:none;border:1px solid var(--border);border-radius:6px;color:#aaa;font-size:11px;padding:4px 10px;cursor:pointer">Tout lire</button>
        </div>
        <div class="alerts-list" id="alertsList">
            <div class="no-alerts">Chargement...</div>
        </div>
    </div>
</div>

<!-- Bottom row -->
<div class="bottom-row">
    <div class="info-card">
        <h4>Informations capteurs</h4>
        <div class="info-row"><span class="key">RSSI (signal)</span><span class="val" id="i_rssi">—</span></div>
        <div class="info-row"><span class="key">Dernière mesure</span><span class="val" id="i_lastmesure">—</span></div>
        <div class="info-row"><span class="key">Total mesures</span><span class="val green" id="i_totalmesures">—</span></div>
        <div class="info-row"><span class="key">Alertes actives</span><span class="val warn" id="i_alertes">—</span></div>
    </div>
    <div class="info-card">
        <h4>Seuils actifs</h4>
        <div id="seuilsList">
            <div class="info-row"><span class="key">Chargement...</span><span class="val">—</span></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const SEUILS = { temperature:{warn:35,crit:40,max:60}, humidite:{warn:75,crit:85,max:100}, gaz:{warn:300,crit:500,max:1000}, courant:{warn:10,crit:15,max:30}, puissance:{warn:3000,crit:5000,max:8000}, tension:{warn:240,crit:260,max:300} };

let thLabels = [], thTemp = [], thHum = [];
const ctx = document.getElementById('chartTH').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: thLabels,
        datasets: [
            { label: 'Temp (°C)', data: thTemp, borderColor: '#ff5733', backgroundColor: 'rgba(255,87,51,.08)', tension: .4, pointRadius: 0 },
            { label: 'Hum (%)',   data: thHum,  borderColor: '#33b5ff', backgroundColor: 'rgba(51,181,255,.08)', tension: .4, pointRadius: 0 }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { labels: { color: '#aaa', font: { size: 11 } } } },
        scales: {
            x: { ticks: { color: '#555', maxTicksLimit: 8, font: { size: 10 } }, grid: { color: '#1e2f5a' } },
            y: { ticks: { color: '#555', font: { size: 10 } }, grid: { color: '#1e2f5a' } }
        }
    }
});

function classify(key, val) {
    const s = SEUILS[key];
    if (!s) return 'ok';
    if (val >= s.crit) return 'crit';
    if (val >= s.warn) return 'warn';
    return 'ok';
}

function barPct(key, val) {
    const s = SEUILS[key];
    if (!s) return Math.min(val, 100);
    return Math.min(Math.round(val / s.max * 100), 100);
}

function barColor(cls) {
    return cls === 'crit' ? '#ff5733' : cls === 'warn' ? '#ffd633' : '#33ff88';
}

function updateGauge(key, val) {
    const cls = classify(key, val);
    const card = document.getElementById('g_' + key);
    const vEl  = document.getElementById('v_' + key);
    const sEl  = document.getElementById('s_' + key);
    const bEl  = document.getElementById('b_' + key);
    if (!card) return;
    card.className = 'gauge-card ' + cls;
    vEl.textContent = val;
    sEl.textContent = cls === 'crit' ? 'CRITIQUE' : cls === 'warn' ? 'AVERTISSEMENT' : 'NORMAL';
    if (bEl) { bEl.style.width = barPct(key, val) + '%'; bEl.style.background = barColor(cls); }
}

let missCount = 0;
function pollSensors() {
    fetch('/api/dashboard-data')
        .then(r => r.json())
        .then(d => {
            missCount = 0;
            document.getElementById('connDot').className = 'dot live';
            document.getElementById('connLabel').textContent = 'Connecté';
            document.getElementById('connLabel').style.color = '#33ff88';
            const now = new Date().toLocaleTimeString('fr-FR');
            document.getElementById('lastUpdate').textContent = 'Dernière MAJ: ' + now;

            const m = d.derniere_mesure || {};
            if (m.temperature != null) updateGauge('temperature', m.temperature);
            if (m.humidite    != null) updateGauge('humidite',    m.humidite);
            if (m.gaz         != null) updateGauge('gaz',         m.gaz);
            if (m.courant     != null) updateGauge('courant',     m.courant);
            if (m.puissance   != null) updateGauge('puissance',   m.puissance);
            if (m.tension     != null) updateGauge('tension',     m.tension);

            if (m.rssi != null) document.getElementById('i_rssi').textContent = m.rssi + ' dBm';
            document.getElementById('i_lastmesure').textContent = now;

            if (m.pir != null) {
                const pirCard = document.getElementById('g_pir');
                pirCard.className = 'gauge-card ' + (m.pir ? 'crit' : 'ok');
                document.getElementById('v_pir').textContent = m.pir ? 'DÉTECTÉ' : 'Aucun';
                document.getElementById('pir_badge').innerHTML = m.pir
                    ? '<span class="pir-badge pir-active">MOUVEMENT DÉTECTÉ</span>'
                    : '<span class="pir-badge pir-inactive">INACTIF</span>';
            }

            if (m.temperature != null && m.humidite != null) {
                const t = new Date().toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
                if (thLabels.length >= 30) { thLabels.shift(); thTemp.shift(); thHum.shift(); }
                thLabels.push(t); thTemp.push(m.temperature); thHum.push(m.humidite);
                chart.update('none');
                document.getElementById('chartPoints').textContent = thLabels.length + ' points';
            }
        })
        .catch(() => {
            missCount++;
            if (missCount >= 3) {
                document.getElementById('connDot').className = 'dot dead';
                document.getElementById('connLabel').textContent = 'Déconnecté';
                document.getElementById('connLabel').style.color = '#ff5733';
            }
        });
}

function pollAlertes() {
    fetch('/api/alertes-recentes')
        .then(r => r.json())
        .then(alertes => {
            const container = document.getElementById('alertsList');
            document.getElementById('i_alertes').textContent = alertes.filter(a => !a.lu).length;
            if (!alertes.length) { container.innerHTML = '<div class="no-alerts">Aucune alerte récente</div>'; return; }
            container.innerHTML = alertes.map(a => `
                <div class="alert-item">
                    <span class="alert-dot ${a.niveau}"></span>
                    <span class="alert-msg">${a.message}</span>
                    <span class="alert-time">${new Date(a.created_at).toLocaleTimeString('fr-FR')}</span>
                </div>`).join('');
        }).catch(() => {});
}

function pollStats() {
    fetch('/api/stats')
        .then(r => r.json())
        .then(s => {
            document.getElementById('i_totalmesures').textContent = s.totalMesures ?? '—';
        }).catch(() => {});

    fetch('/api/seuils')
        .then(r => r.json())
        .then(s => {
            const items = [
                {k:'Température',  w:s.temperature?.warning, c:s.temperature?.critique},
                {k:'Humidité',     w:s.humidite?.warning,    c:s.humidite?.critique},
                {k:'Gaz',          w:s.gaz?.warning,         c:s.gaz?.critique},
                {k:'Courant',      w:s.courant?.warning,     c:s.courant?.critique},
                {k:'Puissance',    w:s.puissance?.warning,   c:s.puissance?.critique},
            ];
            document.getElementById('seuilsList').innerHTML = items.map(i =>
                `<div class="info-row"><span class="key">${i.k}</span><span class="val"><span style="color:var(--warn)">${i.w||'—'}</span> / <span style="color:var(--danger)">${i.c||'—'}</span></span></div>`
            ).join('');
            Object.assign(SEUILS, {
                temperature:{warn:+s.temperature?.warning,crit:+s.temperature?.critique,max:60},
                humidite:{warn:+s.humidite?.warning,crit:+s.humidite?.critique,max:100},
                gaz:{warn:+s.gaz?.warning,crit:+s.gaz?.critique,max:1000},
                courant:{warn:+s.courant?.warning,crit:+s.courant?.critique,max:30},
                puissance:{warn:+s.puissance?.warning,crit:+s.puissance?.critique,max:8000},
            });
        }).catch(() => {});
}

function marquerToutLu() {
    fetch('/api/alertes/lire', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content||''}, body:JSON.stringify({id:'all'}) })
        .then(() => pollAlertes()).catch(() => {});
}

pollSensors(); pollAlertes(); pollStats();
setInterval(pollSensors, 1000);
setInterval(pollAlertes, 5000);
setInterval(pollStats,   15000);
</script>
@endsection
