@extends('layouts.app')

@section('content')
<style>
:root{--neon:#33ff88;--blue:#33b5ff;--warn:#ffd633;--danger:#ff5733;--card:#0e1a38;--border:#1e2f5a;}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-title{font-size:22px;font-weight:700;color:var(--neon)}
.btn{padding:8px 14px;border:none;border-radius:7px;font-weight:700;cursor:pointer;font-size:12px;transition:.18s;display:inline-flex;align-items:center;gap:5px;white-space:nowrap}
.btn:active{transform:scale(.96)}
.btn-neon{background:transparent;border:1px solid var(--neon);color:var(--neon)}
.btn-neon:hover{background:var(--neon);color:#000}
.btn-blue{background:transparent;border:1px solid var(--blue);color:var(--blue)}
.btn-blue:hover{background:var(--blue);color:#000}
.btn-warn{background:transparent;border:1px solid var(--warn);color:var(--warn)}
.btn-warn:hover{background:var(--warn);color:#000}
.btn-danger{background:transparent;border:1px solid var(--danger);color:var(--danger)}
.btn-danger:hover{background:var(--danger);color:#fff}
.btn-purple{background:transparent;border:1px solid #cc88ff;color:#cc88ff}
.btn-purple:hover{background:#cc88ff;color:#000}
.btn-gray{background:transparent;border:1px solid #2a3a5a;color:#777}
.btn-gray:hover{border-color:#aaa;color:#aaa}

/* type selector */
.type-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px}
.type-card{background:var(--card);border:2px solid var(--border);border-radius:12px;padding:16px;cursor:pointer;transition:.2s;text-align:center}
.type-card:hover{border-color:var(--blue)}
.type-card.selected{border-color:var(--neon);background:rgba(51,255,136,.04)}
.type-icon{font-size:28px;margin-bottom:8px}
.type-name{font-size:13px;font-weight:700;color:#fff;margin-bottom:4px}
.type-desc{font-size:11px;color:#555}

/* filter section */
.filter-section{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;margin-bottom:20px}
.filter-section h4{font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;font-weight:700}
.filter-row{display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end}
.filter-group{display:flex;flex-direction:column;gap:5px}
.filter-group label{font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px;font-weight:600}
.filter-group input,.filter-group select{background:#07102a;border:1px solid var(--border);border-radius:7px;padding:8px 11px;color:#fff;font-size:12px;outline:none;transition:.2s;min-width:120px}
.filter-group input:focus,.filter-group select:focus{border-color:var(--neon)}
.filter-group select option{background:#0e1a38}
.range-pair{display:flex;gap:6px;align-items:center}
.range-pair input{min-width:70px!important;width:70px}
.range-sep{font-size:11px;color:#555}
.filter-extra{display:none}

/* format grid */
.format-section{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;margin-bottom:20px}
.format-section h4{font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;font-weight:700}
.format-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:10px}
.fmt-btn{background:#07102a;border:1px solid var(--border);border-radius:10px;padding:12px 8px;cursor:pointer;transition:.2s;text-align:center;display:flex;flex-direction:column;align-items:center;gap:5px}
.fmt-btn:hover{border-color:var(--neon);background:rgba(51,255,136,.05);transform:translateY(-1px)}
.fmt-btn:active{transform:scale(.96)}
.fmt-btn .fmt-icon{font-size:20px}
.fmt-btn .fmt-label{font-size:11px;font-weight:700}
.fmt-btn .fmt-desc{font-size:9px;color:#555}
.fmt-btn.csv{border-color:rgba(51,255,136,.3)} .fmt-btn.csv .fmt-label{color:var(--neon)}
.fmt-btn.json{border-color:rgba(51,181,255,.3)} .fmt-btn.json .fmt-label{color:var(--blue)}
.fmt-btn.xml{border-color:rgba(255,214,51,.3)} .fmt-btn.xml .fmt-label{color:var(--warn)}
.fmt-btn.xls{border-color:rgba(51,255,136,.3)} .fmt-btn.xls .fmt-label{color:#00cc66}
.fmt-btn.xlsx{border-color:rgba(51,255,136,.3)} .fmt-btn.xlsx .fmt-label{color:#00cc66}
.fmt-btn.txt{border-color:rgba(170,170,170,.3)} .fmt-btn.txt .fmt-label{color:#aaa}
.fmt-btn.sql{border-color:rgba(255,153,51,.3)} .fmt-btn.sql .fmt-label{color:#ff9933}
.fmt-btn.docx{border-color:rgba(51,181,255,.3)} .fmt-btn.docx .fmt-label{color:var(--blue)}
.fmt-btn.html{border-color:rgba(204,136,255,.3)} .fmt-btn.html .fmt-label{color:#cc88ff}
.fmt-btn.zip{border-color:rgba(255,214,51,.3)} .fmt-btn.zip .fmt-label{color:var(--warn)}
.fmt-btn.pdf{border-color:rgba(255,87,51,.3)} .fmt-btn.pdf .fmt-label{color:var(--danger)}
.fmt-btn.backup{border-color:rgba(51,255,136,.2)} .fmt-btn.backup .fmt-label{color:var(--neon)}

/* stats row */
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.stat-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:14px 18px;text-align:center}
.stat-card .val{font-size:26px;font-weight:800;margin-bottom:4px}
.stat-card .lbl{font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:.4px}
.stat-card.green .val{color:var(--neon)}
.stat-card.blue  .val{color:var(--blue)}
.stat-card.warn  .val{color:var(--warn)}
.stat-card.red   .val{color:var(--danger)}

/* chart */
.chart-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;margin-bottom:20px}
.chart-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}
.chart-title{font-size:14px;font-weight:700;color:#fff}
canvas{max-height:200px}

/* table */
.table-card{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden}
.table-header{display:flex;justify-content:space-between;align-items:center;padding:13px 18px;border-bottom:1px solid var(--border)}
.table-title{font-size:13px;font-weight:700;color:#fff}
.table-count{font-size:11px;color:#555}
table{width:100%;border-collapse:collapse}
thead tr{background:#07102a}
th{padding:9px 14px;text-align:left;font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap}
td{padding:9px 14px;border-top:1px solid var(--border);font-size:12px;color:#ccc}
tr:hover td{background:rgba(51,181,255,.03)}
.badge{display:inline-block;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700}
.badge-warning{background:rgba(255,214,51,.1);color:var(--warn);border:1px solid rgba(255,214,51,.3)}
.badge-critique{background:rgba(255,87,51,.1);color:var(--danger);border:1px solid rgba(255,87,51,.3)}
.no-data{text-align:center;padding:40px;color:#555}
.loading{text-align:center;padding:40px;color:#33b5ff;font-size:13px}

@media(max-width:900px){
.type-grid{grid-template-columns:1fr 1fr}
.stats-row{grid-template-columns:1fr 1fr}
.format-grid{grid-template-columns:repeat(3,1fr)}
.filter-row{flex-direction:column}
}
@media(max-width:600px){
.format-grid{grid-template-columns:repeat(2,1fr)}
}
</style>

<div class="pg-header">
    <div class="pg-title">&#128202; Rapports</div>
    <div style="display:flex;align-items:center;gap:12px">
        <a href="/rapports/rapport-72h" target="_blank"
           style="padding:8px 14px;background:transparent;border:1px solid #ff5733;color:#ff5733;border-radius:7px;font-weight:700;font-size:12px;text-decoration:none;white-space:nowrap">
            &#128247; Rapport 72h PNG
        </a>
        <div style="font-size:12px;color:#555" id="reportTimestamp">—</div>
    </div>
</div>

<!-- Report type selector -->
<div class="type-grid">
    <div class="type-card selected" data-type="mesures" onclick="selectType(this,'mesures')">
        <div class="type-icon">&#127777;</div>
        <div class="type-name">Mesures capteurs</div>
        <div class="type-desc">Données IoT température, humidité, gaz…</div>
    </div>
    <div class="type-card" data-type="alertes" onclick="selectType(this,'alertes')">
        <div class="type-icon">&#128276;</div>
        <div class="type-name">Alertes</div>
        <div class="type-desc">Historique des alertes déclenchées</div>
    </div>
    <div class="type-card" data-type="salles" onclick="selectType(this,'salles')">
        <div class="type-icon">&#127970;</div>
        <div class="type-name">Salles &amp; Équipements</div>
        <div class="type-desc">Inventaire des équipements</div>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <h4>&#9881; Filtres</h4>
    <div class="filter-row">
        <div class="filter-group">
            <label>Date début</label>
            <input type="date" id="dateDebut" value="{{ now()->subDays(7)->toDateString() }}" onchange="loadReport()">
        </div>
        <div class="filter-group">
            <label>Date fin</label>
            <input type="date" id="dateFin" value="{{ now()->toDateString() }}" onchange="loadReport()">
        </div>
        <div class="filter-group">
            <label>Période rapide</label>
            <select onchange="applyPeriod(this.value)">
                <option value="">Personnalisée</option>
                <option value="1">Aujourd'hui</option>
                <option value="7" selected>7 derniers jours</option>
                <option value="30">30 derniers jours</option>
                <option value="90">3 derniers mois</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Salle serveurs</label>
            <select id="filterSalle" onchange="loadReport()">
                <option value="">Toutes les salles</option>
            </select>
        </div>
        <div class="filter-group" id="niveauGroup">
            <label>Niveau alerte</label>
            <select id="filterNiveau" onchange="loadReport()">
                <option value="">Tous</option>
                <option value="warning">Warning</option>
                <option value="critique">Critique</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Limite enreg.</label>
            <select id="filterLimit" onchange="loadReport()">
                <option value="100">100</option>
                <option value="200" selected>200</option>
                <option value="500">500</option>
                <option value="1000">1 000</option>
                <option value="5000">5 000</option>
            </select>
        </div>
        <button class="btn btn-blue" onclick="loadReport()">&#8635; Filtrer</button>
    </div>

    <!-- Extra filters for mesures -->
    <div class="filter-row filter-extra" id="extraFilters" style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border)">
        <div class="filter-group">
            <label>&#127777; Température (°C)</label>
            <div class="range-pair">
                <input type="number" id="fTempMin" placeholder="Min" step="0.1" onchange="loadReport()">
                <span class="range-sep">–</span>
                <input type="number" id="fTempMax" placeholder="Max" step="0.1" onchange="loadReport()">
            </div>
        </div>
        <div class="filter-group">
            <label>&#128167; Humidité (%)</label>
            <div class="range-pair">
                <input type="number" id="fHumMin" placeholder="Min" step="0.1" onchange="loadReport()">
                <span class="range-sep">–</span>
                <input type="number" id="fHumMax" placeholder="Max" step="0.1" onchange="loadReport()">
            </div>
        </div>
        <div class="filter-group">
            <label>&#9729; Gaz (ppm)</label>
            <div class="range-pair">
                <input type="number" id="fGazMin" placeholder="Min" onchange="loadReport()">
                <span class="range-sep">–</span>
                <input type="number" id="fGazMax" placeholder="Max" onchange="loadReport()">
            </div>
        </div>
        <button class="btn btn-gray" onclick="clearRanges()" style="font-size:11px;padding:7px 10px">&#10005; Réinitialiser</button>
    </div>
</div>

<!-- Export format grid -->
<div class="format-section">
    <h4>&#8595; Exporter le rapport</h4>
    <div class="format-grid">
        <div class="fmt-btn csv"    onclick="exportData('csv')"   title="Tableur universel">
            <div class="fmt-icon">&#128200;</div>
            <div class="fmt-label">CSV</div>
            <div class="fmt-desc">Tableur</div>
        </div>
        <div class="fmt-btn json"   onclick="exportData('json')"  title="API / développeur">
            <div class="fmt-icon">&#123;&#125;</div>
            <div class="fmt-label">JSON</div>
            <div class="fmt-desc">Données API</div>
        </div>
        <div class="fmt-btn xml"    onclick="exportData('xml')"   title="Échange de données">
            <div class="fmt-icon">&#60;/&#62;</div>
            <div class="fmt-label">XML</div>
            <div class="fmt-desc">Échange</div>
        </div>
        <div class="fmt-btn xls"    onclick="exportData('xls')"   title="Excel classique">
            <div class="fmt-icon">&#128202;</div>
            <div class="fmt-label">XLS</div>
            <div class="fmt-desc">Excel 97+</div>
        </div>
        <div class="fmt-btn xlsx"   onclick="exportData('xlsx')"  title="Excel moderne">
            <div class="fmt-icon">&#128202;</div>
            <div class="fmt-label">XLSX</div>
            <div class="fmt-desc">Excel 2007+</div>
        </div>
        <div class="fmt-btn txt"    onclick="exportData('txt')"   title="Texte brut formaté">
            <div class="fmt-icon">&#128220;</div>
            <div class="fmt-label">TXT</div>
            <div class="fmt-desc">Texte brut</div>
        </div>
        <div class="fmt-btn sql"    onclick="exportData('sql')"   title="Script SQL INSERT">
            <div class="fmt-icon">&#128190;</div>
            <div class="fmt-label">SQL</div>
            <div class="fmt-desc">Script INSERT</div>
        </div>
        <div class="fmt-btn docx"   onclick="exportData('docx')"  title="Document Word">
            <div class="fmt-icon">&#128196;</div>
            <div class="fmt-label">DOCX</div>
            <div class="fmt-desc">Word</div>
        </div>
        <div class="fmt-btn html"   onclick="exportData('html')"  title="Page HTML autonome">
            <div class="fmt-icon">&#127760;</div>
            <div class="fmt-label">HTML</div>
            <div class="fmt-desc">Page web</div>
        </div>
        <div class="fmt-btn zip"    onclick="exportData('zip')"   title="Archive tous formats">
            <div class="fmt-icon">&#128230;</div>
            <div class="fmt-label">ZIP</div>
            <div class="fmt-desc">Archive</div>
        </div>
        <div class="fmt-btn pdf"    onclick="printRapport()"      title="Impression PDF">
            <div class="fmt-icon">&#128438;</div>
            <div class="fmt-label">PDF</div>
            <div class="fmt-desc">Imprimer</div>
        </div>
        <div class="fmt-btn backup" onclick="downloadBackup()"    title="Sauvegarde complète">
            <div class="fmt-icon">&#128190;</div>
            <div class="fmt-label">BACKUP</div>
            <div class="fmt-desc">Tout exporter</div>
        </div>
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

<script src="/vendor/chartjs/chart.umd.min.js"></script>
<script>
let currentType = 'mesures';
let reportChart = null;

function selectType(el, type) {
    document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    currentType = type;
    const labels = {mesures:'Mesures capteurs', alertes:'Alertes', salles:'Salles & Équipements'};
    document.getElementById('chartTitle').textContent = labels[type];
    // Show/hide mesures-specific filters
    document.getElementById('extraFilters').style.display = type === 'mesures' ? 'flex' : 'none';
    document.getElementById('niveauGroup').style.display  = type === 'alertes' ? '' : 'none';
    loadReport();
}

function applyPeriod(days) {
    if (!days) return;
    const end = new Date(), start = new Date();
    start.setDate(start.getDate() - parseInt(days) + 1);
    document.getElementById('dateDebut').value = start.toISOString().split('T')[0];
    document.getElementById('dateFin').value   = end.toISOString().split('T')[0];
    loadReport();
}

function clearRanges() {
    ['fTempMin','fTempMax','fHumMin','fHumMax','fGazMin','fGazMax'].forEach(id => {
        const el = document.getElementById(id); if (el) el.value = '';
    });
    loadReport();
}

function buildParams() {
    const debut  = document.getElementById('dateDebut').value;
    const fin    = document.getElementById('dateFin').value;
    const niveau = document.getElementById('filterNiveau').value;
    const salle  = document.getElementById('filterSalle').value;
    const limit  = document.getElementById('filterLimit').value;
    let params   = `type=${currentType}&debut=${debut}&fin=${fin}&niveau=${niveau}&salle_id=${salle}&limit=${limit}`;
    if (currentType === 'mesures') {
        const ids = ['fTempMin','fTempMax','fHumMin','fHumMax','fGazMin','fGazMax'];
        const keys= ['temp_min','temp_max','hum_min','hum_max','gaz_min','gaz_max'];
        ids.forEach((id, i) => {
            const v = document.getElementById(id)?.value;
            if (v !== '' && v != null) params += `&${keys[i]}=${v}`;
        });
    }
    return params;
}

function loadReport() {
    document.getElementById('reportTimestamp').textContent = 'Généré le ' + new Date().toLocaleString('fr-FR');
    document.getElementById('tableWrapper').innerHTML = '<div class="loading">Chargement...</div>';
    const params = buildParams();

    fetch(`/api/filter?${params}`)
        .then(r => r.json())
        .then(res => {
            const rows = res.data || [];
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
        const map = {mesures: s.totalMesures, alertes: (s.alertesWarning||0)+(s.alertesCritiques||0), salles: '—'};
        document.getElementById('statTotal').textContent = map[currentType] ?? '—';
    }).catch(() => {});
}

function renderMesuresTable(rows) {
    if (!rows.length) { document.getElementById('tableWrapper').innerHTML = '<div class="no-data">Aucune mesure sur cette période.</div>'; return; }
    let html = `<div style="overflow-x:auto"><table><thead><tr>
        <th>Date</th><th>Temp (°C)</th><th>Hum (%)</th><th>Gaz (ppm)</th><th>PIR</th>
    </tr></thead><tbody>`;
    rows.forEach(r => {
        const t = new Date(r.created_at).toLocaleString('fr-FR');
        html += `<tr>
            <td style="color:#555;font-size:11px">${t}</td>
            <td style="color:${r.temperature>40?'var(--danger)':r.temperature>35?'var(--warn)':'var(--neon)'}">${r.temperature??'—'}</td>
            <td style="color:${r.humidite>85?'var(--danger)':r.humidite>75?'var(--warn)':'var(--blue)'}">${r.humidite??'—'}</td>
            <td style="color:${r.gaz>500?'var(--danger)':r.gaz>300?'var(--warn)':'#ccc'}">${r.gaz??'—'}</td>
            <td style="color:${r.pir?'var(--danger)':'var(--neon)'}">${r.pir?'OUI':'NON'}</td>
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
            <td style="color:#ccc;max-width:300px">${r.message??'—'}</td>
            <td><span class="badge badge-${r.niveau}">${r.niveau??'—'}</span></td>
            <td style="color:var(--warn)">${r.valeur??'—'}</td>
            <td style="color:${r.lu?'var(--neon)':'#555'}">${r.lu?'✓':'○'}</td>
        </tr>`;
    });
    html += '</tbody></table></div>';
    document.getElementById('tableWrapper').innerHTML = html;
}

function renderGenericTable(rows) {
    if (!rows.length) { document.getElementById('tableWrapper').innerHTML = '<div class="no-data">Aucune donnée.</div>'; return; }
    const keys = Object.keys(rows[0]).filter(k => !['created_at','updated_at'].includes(k)).concat(['created_at']);
    let html = `<div style="overflow-x:auto"><table><thead><tr>${keys.map(k=>`<th>${k}</th>`).join('')}</tr></thead><tbody>`;
    rows.forEach(r => {
        html += '<tr>' + keys.map(k => `<td>${r[k]??'—'}</td>`).join('') + '</tr>';
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
            { label: 'Critique', data: days.map(d => byDay[d].critique), backgroundColor: 'rgba(255,87,51,.5)'  },
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
    document.getElementById('chartMeta').textContent = rows.length + ' alertes';
}

function exportData(format) {
    const params = buildParams();
    window.location.href = `/rapports/export?${params}&format=${format}`;
    notify('Téléchargement ' + format.toUpperCase() + ' en cours…', 'i', 3000);
}

function printRapport() {
    const debut  = document.getElementById('dateDebut').value;
    const fin    = document.getElementById('dateFin').value;
    const niveau = document.getElementById('filterNiveau').value;
    const salle  = document.getElementById('filterSalle').value;
    window.open(`/rapports/print?type=${currentType}&debut=${debut}&fin=${fin}&niveau=${niveau}&salle_id=${salle}`, '_blank');
}

function downloadBackup() {
    window.location.href = '/rapports/backup';
    notify('Génération du backup complet en cours…', 'i', 4000);
}

// Load salles into dropdown
fetch('/api/salles-list').then(r => r.json()).then(salles => {
    const sel = document.getElementById('filterSalle');
    salles.forEach(s => {
        const o = document.createElement('option');
        o.value = s.id; o.textContent = s.nom;
        sel.appendChild(o);
    });
}).catch(() => {});

loadReport();
</script>
@endsection
