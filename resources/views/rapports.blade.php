@extends('layouts.app')

@section('content')
<style>
:root{--neon:#33ff88;--blue:#33b5ff;--warn:#ffd633;--danger:#ff5733;--card:#0e1a38;--border:#1e2f5a;}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-title{font-size:22px;font-weight:700;color:var(--neon)}
.btn{padding:10px 20px;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px;transition:.2s;display:inline-flex;align-items:center;gap:6px}
.btn-neon{background:transparent;border:1px solid var(--neon);color:var(--neon)}
.btn-neon:hover{background:var(--neon);color:#000}
.btn-blue{background:transparent;border:1px solid var(--blue);color:var(--blue)}
.btn-blue:hover{background:var(--blue);color:#000}
.btn-warn{background:transparent;border:1px solid var(--warn);color:var(--warn)}
.btn-warn:hover{background:var(--warn);color:#000}
.btn-danger{background:transparent;border:1px solid var(--danger);color:var(--danger)}
.btn-danger:hover{background:var(--danger);color:#fff}

.config-row{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
.config-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px}
.config-card h4{font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px}
.config-card select,.config-card input{background:#07102a;border:1px solid var(--border);border-radius:8px;padding:9px 12px;color:#fff;font-size:13px;outline:none;width:100%;transition:.2s}
.config-card select:focus,.config-card input:focus{border-color:var(--neon)}
.config-card select option{background:#0e1a38}

/* type selector */
.type-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px}
.type-card{background:var(--card);border:2px solid var(--border);border-radius:12px;padding:16px;cursor:pointer;transition:.2s;text-align:center}
.type-card:hover{border-color:var(--blue)}
.type-card.selected{border-color:var(--neon);background:rgba(51,255,136,.04)}
.type-icon{font-size:28px;margin-bottom:8px}
.type-name{font-size:13px;font-weight:700;color:#fff;margin-bottom:4px}
.type-desc{font-size:11px;color:#555}

/* export bar */
.export-bar{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:24px}
.export-label{font-size:13px;color:#aaa}
.export-label strong{color:#fff}
.export-btns{display:flex;gap:10px;flex-wrap:wrap}

/* stats row */
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px}
.stat-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 20px;text-align:center}
.stat-card .val{font-size:28px;font-weight:800;margin-bottom:4px}
.stat-card .lbl{font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:.4px}
.stat-card.green .val{color:var(--neon)}
.stat-card.blue  .val{color:var(--blue)}
.stat-card.warn  .val{color:var(--warn)}
.stat-card.red   .val{color:var(--danger)}

/* chart */
.chart-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;margin-bottom:24px}
.chart-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.chart-title{font-size:14px;font-weight:700;color:#fff}
canvas{max-height:200px}

/* table */
.table-card{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden}
.table-header{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border)}
.table-title{font-size:14px;font-weight:700;color:#fff}
.table-count{font-size:11px;color:#555}
table{width:100%;border-collapse:collapse}
thead tr{background:#07102a}
th{padding:10px 14px;text-align:left;font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap}
td{padding:10px 14px;border-top:1px solid var(--border);font-size:12px;color:#ccc}
tr:hover td{background:rgba(51,181,255,.03)}
.badge{display:inline-block;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700}
.badge-warning{background:rgba(255,214,51,.1);color:var(--warn);border:1px solid rgba(255,214,51,.3)}
.badge-critique{background:rgba(255,87,51,.1);color:var(--danger);border:1px solid rgba(255,87,51,.3)}
.no-data{text-align:center;padding:40px;color:#555}
.loading{text-align:center;padding:40px;color:#33b5ff;font-size:13px}

@media(max-width:900px){
.config-row{grid-template-columns:1fr 1fr}
.type-grid{grid-template-columns:1fr 1fr}
.stats-row{grid-template-columns:1fr 1fr}
}
</style>

<div class="pg-header">
    <div class="pg-title">Rapports</div>
    <div style="font-size:12px;color:#555" id="reportTimestamp">—</div>
</div>

<!-- Report type selector -->
<div class="type-grid">
    <div class="type-card selected" data-type="mesures" onclick="selectType(this,'mesures')">
        <div class="type-icon">&#128200;</div>
        <div class="type-name">Mesures capteurs</div>
        <div class="type-desc">Toutes les données IoT collectées</div>
    </div>
    <div class="type-card" data-type="alertes" onclick="selectType(this,'alertes')">
        <div class="type-icon">&#128276;</div>
        <div class="type-name">Alertes</div>
        <div class="type-desc">Historique des alertes déclenchées</div>
    </div>
    <div class="type-card" data-type="salles" onclick="selectType(this,'salles')">
        <div class="type-icon">&#127970;</div>
        <div class="type-name">Salles serveurs</div>
        <div class="type-desc">Inventaire des salles</div>
    </div>
</div>

<!-- Config row -->
<div class="config-row">
    <div class="config-card">
        <h4>Date début</h4>
        <input type="date" id="dateDebut" value="{{ now()->subDays(7)->toDateString() }}" onchange="loadReport()">
    </div>
    <div class="config-card">
        <h4>Date fin</h4>
        <input type="date" id="dateFin" value="{{ now()->toDateString() }}" onchange="loadReport()">
    </div>
    <div class="config-card">
        <h4>Période rapide</h4>
        <select onchange="applyPeriod(this.value)">
            <option value="">Personnalisée</option>
            <option value="1">Aujourd'hui</option>
            <option value="7" selected>7 derniers jours</option>
            <option value="30">30 derniers jours</option>
            <option value="90">3 derniers mois</option>
        </select>
    </div>
    <div class="config-card">
        <h4>Niveau d'alerte</h4>
        <select id="filterNiveau" onchange="loadReport()">
            <option value="">Tous</option>
            <option value="warning">Warning</option>
            <option value="critique">Critique</option>
        </select>
    </div>
</div>

<!-- Export bar -->
<div class="export-bar">
    <div class="export-label">Rapport : <strong id="exportLabel">Mesures capteurs</strong> · <span id="exportRange">7 derniers jours</span></div>
    <div class="export-btns">
        <button class="btn btn-neon"  onclick="exportData('csv')">&#8595; CSV</button>
        <button class="btn btn-blue"  onclick="exportData('json')">&#123;&#125; JSON</button>
        <button class="btn btn-warn"  onclick="exportData('xls')">&#128202; Excel</button>
        <button class="btn btn-blue"  onclick="exportData('xml')">&#60;/&#62; XML</button>
        <button class="btn btn-gray"  onclick="printRapport()">&#128438; PDF Print</button>
    </div>
</div>

<!-- Stats row -->
<div class="stats-row">
    <div class="stat-card blue"><div class="val" id="statTotal">—</div><div class="lbl">Total enreg.</div></div>
    <div class="stat-card green"><div class="val" id="statPeriod">—</div><div class="lbl">Sur la période</div></div>
    <div class="stat-card warn"><div class="val" id="statWarn">—</div><div class="lbl">Avertissements</div></div>
    <div class="stat-card red"><div class="val" id="statCrit">—</div><div class="lbl">Critiques</div></div>
</div>

<!-- Chart -->
<div class="chart-card" id="chartSection">
    <div class="chart-header">
        <div class="chart-title" id="chartTitle">Évolution sur la période</div>
        <div style="font-size:11px;color:#555" id="chartMeta">—</div>
    </div>
    <canvas id="reportChart"></canvas>
</div>

<!-- Data table -->
<div class="table-card">
    <div class="table-header">
        <div class="table-title" id="tableTitle">Données</div>
        <div class="table-count" id="tableCount">—</div>
    </div>
    <div id="tableWrapper">
        <div class="loading">Chargement...</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
let currentType = 'mesures';
let reportChart = null;

function selectType(el, type) {
    document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    currentType = type;
    const labels = {mesures:'Mesures capteurs', alertes:'Alertes', salles:'Salles serveurs'};
    document.getElementById('exportLabel').textContent = labels[type];
    loadReport();
}

function applyPeriod(days) {
    if (!days) return;
    const end = new Date();
    const start = new Date();
    start.setDate(start.getDate() - parseInt(days) + 1);
    document.getElementById('dateDebut').value = start.toISOString().split('T')[0];
    document.getElementById('dateFin').value   = end.toISOString().split('T')[0];
    document.getElementById('exportRange').textContent = days === '1' ? "Aujourd'hui" : `${days} derniers jours`;
    loadReport();
}

function loadReport() {
    const debut   = document.getElementById('dateDebut').value;
    const fin     = document.getElementById('dateFin').value;
    const niveau  = document.getElementById('filterNiveau').value;
    document.getElementById('reportTimestamp').textContent = 'Généré le ' + new Date().toLocaleString('fr-FR');
    document.getElementById('tableWrapper').innerHTML = '<div class="loading">Chargement...</div>';

    fetch(`/api/historique-data?type=${currentType}&debut=${debut}&fin=${fin}&niveau=${niveau}&limit=200`)
        .then(r => r.json())
        .then(data => {
            const rows = Array.isArray(data) ? data : (data.data || []);
            document.getElementById('statPeriod').textContent = rows.length;
            document.getElementById('tableCount').textContent = rows.length + ' enregistrements';

            if (currentType === 'mesures') {
                renderMesuresTable(rows);
                renderMesuresChart(rows);
                const warnCount = rows.filter(r => r.temperature > 35 || r.humidite > 75 || r.gaz > 300).length;
                const critCount = rows.filter(r => r.temperature > 40 || r.humidite > 85 || r.gaz > 500).length;
                document.getElementById('statWarn').textContent = warnCount;
                document.getElementById('statCrit').textContent = critCount;
            } else if (currentType === 'alertes') {
                renderAlertesTable(rows);
                renderAlertesChart(rows);
                document.getElementById('statWarn').textContent = rows.filter(r => r.niveau === 'warning').length;
                document.getElementById('statCrit').textContent = rows.filter(r => r.niveau === 'critique').length;
            } else {
                renderGenericTable(rows);
                document.getElementById('statWarn').textContent = '—';
                document.getElementById('statCrit').textContent = '—';
            }
        })
        .catch(() => {
            document.getElementById('tableWrapper').innerHTML = '<div class="no-data">Erreur de chargement. Vérifiez la connexion.</div>';
        });

    fetch('/api/stats').then(r => r.json()).then(s => {
        const map = {mesures: s.totalMesures, alertes: (s.alertesWarning||0)+(s.alertesCritiques||0)};
        document.getElementById('statTotal').textContent = map[currentType] ?? '—';
    }).catch(() => {});
}

function renderMesuresTable(rows) {
    if (!rows.length) { document.getElementById('tableWrapper').innerHTML = '<div class="no-data">Aucune mesure sur cette période.</div>'; return; }
    let html = `<div style="overflow-x:auto"><table><thead><tr>
        <th>Date</th><th>Temp (°C)</th><th>Hum (%)</th><th>Gaz (ppm)</th><th>Courant (A)</th><th>Puissance (W)</th><th>Tension (V)</th><th>PIR</th>
    </tr></thead><tbody>`;
    rows.forEach(r => {
        const t = new Date(r.created_at).toLocaleString('fr-FR');
        const cls = v => v == null ? '#555' : (parseFloat(v) > 40 || parseFloat(v) > 85 ? 'var(--danger)' : 'var(--neon)');
        html += `<tr>
            <td style="color:#555;font-size:11px">${t}</td>
            <td style="color:${r.temperature > 40 ? 'var(--danger)' : r.temperature > 35 ? 'var(--warn)' : 'var(--neon)'}">${r.temperature ?? '—'}</td>
            <td style="color:${r.humidite > 85 ? 'var(--danger)' : r.humidite > 75 ? 'var(--warn)' : 'var(--blue)'}">${r.humidite ?? '—'}</td>
            <td style="color:${r.gaz > 500 ? 'var(--danger)' : r.gaz > 300 ? 'var(--warn)' : '#ccc'}">${r.gaz ?? '—'}</td>
            <td>${r.courant ?? '—'}</td><td>${r.puissance ?? '—'}</td><td>${r.tension ?? '—'}</td>
            <td><span style="color:${r.pir ? 'var(--danger)' : 'var(--neon)';}">${r.pir ? 'OUI' : 'NON'}</span></td>
        </tr>`;
    });
    html += '</tbody></table></div>';
    document.getElementById('tableWrapper').innerHTML = html;
}

function renderAlertesTable(rows) {
    if (!rows.length) { document.getElementById('tableWrapper').innerHTML = '<div class="no-data">Aucune alerte sur cette période.</div>'; return; }
    let html = `<div style="overflow-x:auto"><table><thead><tr>
        <th>Date</th><th>Message</th><th>Niveau</th><th>Valeur</th><th>Lu</th>
    </tr></thead><tbody>`;
    rows.forEach(r => {
        const t = new Date(r.created_at).toLocaleString('fr-FR');
        html += `<tr>
            <td style="color:#555;font-size:11px">${t}</td>
            <td style="color:#ccc;max-width:300px">${r.message}</td>
            <td><span class="badge badge-${r.niveau}">${r.niveau}</span></td>
            <td style="color:var(--warn)">${r.valeur ?? '—'}</td>
            <td style="color:${r.lu ? 'var(--neon)' : '#555'}">${r.lu ? '✓' : '○'}</td>
        </tr>`;
    });
    html += '</tbody></table></div>';
    document.getElementById('tableWrapper').innerHTML = html;
}

function renderGenericTable(rows) {
    if (!rows.length) { document.getElementById('tableWrapper').innerHTML = '<div class="no-data">Aucune donnée.</div>'; return; }
    const keys = Object.keys(rows[0]);
    let html = `<div style="overflow-x:auto"><table><thead><tr>${keys.map(k => `<th>${k}</th>`).join('')}</tr></thead><tbody>`;
    rows.forEach(r => {
        html += '<tr>' + keys.map(k => `<td>${r[k] ?? '—'}</td>`).join('') + '</tr>';
    });
    html += '</tbody></table></div>';
    document.getElementById('tableWrapper').innerHTML = html;
}

function renderMesuresChart(rows) {
    const canvas = document.getElementById('reportChart');
    if (reportChart) { reportChart.destroy(); reportChart = null; }
    const sample = rows.filter((_, i) => i % Math.max(1, Math.floor(rows.length/50)) === 0).reverse();
    const labels = sample.map(r => new Date(r.created_at).toLocaleString('fr-FR',{hour:'2-digit',minute:'2-digit',day:'2-digit',month:'2-digit'}));
    reportChart = new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: { labels, datasets: [
            { label: 'Temp (°C)', data: sample.map(r => r.temperature), borderColor: '#ff5733', tension: .4, pointRadius: 0 },
            { label: 'Hum (%)',   data: sample.map(r => r.humidite),    borderColor: '#33b5ff', tension: .4, pointRadius: 0 },
            { label: 'Gaz/10',   data: sample.map(r => r.gaz ? r.gaz/10 : null), borderColor: '#ffd633', tension: .4, pointRadius: 0 },
        ]},
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: { legend: { labels: { color: '#aaa', font: { size: 11 } } } },
            scales: {
                x: { ticks: { color: '#555', maxTicksLimit: 10, font: { size: 10 } }, grid: { color: '#1e2f5a' } },
                y: { ticks: { color: '#555', font: { size: 10 } }, grid: { color: '#1e2f5a' } }
            }
        }
    });
    document.getElementById('chartMeta').textContent = sample.length + ' points';
}

function renderAlertesChart(rows) {
    const canvas = document.getElementById('reportChart');
    if (reportChart) { reportChart.destroy(); reportChart = null; }
    const byDay = {};
    rows.forEach(r => {
        const d = r.created_at?.split('T')[0] || r.created_at?.split(' ')[0];
        if (!d) return;
        if (!byDay[d]) byDay[d] = {warning:0, critique:0};
        byDay[d][r.niveau] = (byDay[d][r.niveau] || 0) + 1;
    });
    const days = Object.keys(byDay).sort();
    reportChart = new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: { labels: days, datasets: [
            { label: 'Warning',  data: days.map(d => byDay[d].warning),  backgroundColor: 'rgba(255,214,51,.5)' },
            { label: 'Critique', data: days.map(d => byDay[d].critique), backgroundColor: 'rgba(255,87,51,.5)' },
        ]},
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: { legend: { labels: { color: '#aaa', font: { size: 11 } } } },
            scales: {
                x: { ticks: { color: '#555', font: { size: 10 } }, grid: { color: '#1e2f5a' } },
                y: { ticks: { color: '#555', font: { size: 10 } }, grid: { color: '#1e2f5a' } }
            }
        }
    });
}

function exportData(format) {
    const debut = document.getElementById('dateDebut').value;
    const fin   = document.getElementById('dateFin').value;
    window.location.href = `/rapports/export?type=${currentType}&format=${format}&debut=${debut}&fin=${fin}`;
    notify('Téléchargement en cours...', 'i', 2500);
}

function printRapport() {
    const debut = document.getElementById('dateDebut').value;
    const fin   = document.getElementById('dateFin').value;
    window.open(`/rapports/print?type=${currentType}&debut=${debut}&fin=${fin}`, '_blank');
}

loadReport();
</script>
@endsection
