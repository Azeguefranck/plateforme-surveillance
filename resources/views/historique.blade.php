@extends('layouts.app')

@section('content')
<style>
:root{--neon:#33ff88;--blue:#33b5ff;--warn:#ffd633;--danger:#ff5733;--card:#0e1a38;--border:#1e2f5a;}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-title{font-size:22px;font-weight:700;color:var(--blue)}
.btn{padding:8px 16px;border:none;border-radius:7px;font-weight:700;cursor:pointer;font-size:12px;transition:.2s}
.btn-neon{background:transparent;border:1px solid var(--neon);color:var(--neon)}
.btn-neon:hover{background:var(--neon);color:#000}
.btn-blue{background:transparent;border:1px solid var(--blue);color:var(--blue)}
.btn-blue:hover{background:var(--blue);color:#000}

/* tabs */
.tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:20px;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:6px}
.tab{padding:8px 16px;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;color:#666;transition:.2s;white-space:nowrap}
.tab:hover{color:#aaa;background:rgba(255,255,255,.04)}
.tab.active{background:#1e2f5a;color:#fff}
.tab.active.temp{color:var(--danger)}
.tab.active.hum{color:var(--blue)}
.tab.active.gaz{color:var(--warn)}
.tab.active.cur{color:#ff9933}
.tab.active.pwr{color:#cc88ff}
.tab.active.alrt{color:var(--danger)}

/* filter bar */
.filter-bar{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:14px 18px;display:flex;gap:14px;align-items:flex-end;flex-wrap:wrap;margin-bottom:20px}
.filter-group{display:flex;flex-direction:column;gap:5px}
.filter-group label{font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px;font-weight:600}
.filter-group input,.filter-group select{background:#07102a;border:1px solid var(--border);border-radius:7px;padding:8px 11px;color:#fff;font-size:12px;outline:none;transition:.2s}
.filter-group input:focus,.filter-group select:focus{border-color:var(--blue)}
.filter-group select option{background:#0e1a38}

/* stats strip */
.stats-strip{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px}
.stat-s{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:12px 14px;text-align:center}
.stat-s .v{font-size:22px;font-weight:800;margin-bottom:2px}
.stat-s .l{font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px}
.stat-s.blue .v{color:var(--blue)}
.stat-s.green .v{color:var(--neon)}
.stat-s.warn  .v{color:var(--warn)}
.stat-s.red   .v{color:var(--danger)}
.stat-s.purple .v{color:#cc88ff}

/* chart */
.chart-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;margin-bottom:20px}
.chart-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}
.chart-title{font-size:13px;font-weight:700;color:#fff}
canvas{max-height:180px}

/* table */
.table-card{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden}
.table-toolbar{display:flex;justify-content:space-between;align-items:center;padding:13px 18px;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:10px}
.table-toolbar .title{font-size:13px;font-weight:700;color:#fff}
.search-box{background:#07102a;border:1px solid var(--border);border-radius:7px;padding:7px 12px;color:#fff;font-size:12px;outline:none;width:200px}
.search-box:focus{border-color:var(--blue)}
table{width:100%;border-collapse:collapse}
thead tr{background:#07102a}
th{padding:9px 14px;text-align:left;font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap}
td{padding:9px 14px;border-top:1px solid var(--border);font-size:12px;color:#ccc}
tr:hover td{background:rgba(51,181,255,.03)}
.badge{display:inline-block;padding:2px 7px;border-radius:10px;font-size:10px;font-weight:700}
.badge-warning{background:rgba(255,214,51,.1);color:var(--warn);border:1px solid rgba(255,214,51,.3)}
.badge-critique{background:rgba(255,87,51,.1);color:var(--danger);border:1px solid rgba(255,87,51,.3)}
.no-data{text-align:center;padding:40px;color:#555;font-size:12px}
.loading{text-align:center;padding:40px;color:var(--blue);font-size:12px}

/* pagination */
.pagination{display:flex;justify-content:center;align-items:center;gap:8px;padding:14px;border-top:1px solid var(--border)}
.page-btn{background:#07102a;border:1px solid var(--border);border-radius:6px;color:#aaa;padding:5px 12px;cursor:pointer;font-size:12px}
.page-btn:hover{border-color:var(--blue);color:#fff}
.page-btn.active{background:var(--blue);color:#000;border-color:var(--blue)}
.page-info{font-size:12px;color:#555}

@media(max-width:768px){
.stats-strip{grid-template-columns:repeat(3,1fr)}
.filter-bar{flex-direction:column}
table{display:block;overflow-x:auto}
}
</style>

<div class="pg-header">
    <div class="pg-title">Historique</div>
    <div style="font-size:12px;color:#555" id="histTimestamp">—</div>
</div>

<!-- Tabs -->
<div class="tabs">
    <div class="tab active" data-tab="all"     onclick="switchTab(this,'all')">Toutes mesures</div>
    <div class="tab" data-tab="alertes" onclick="switchTab(this,'alertes')">Alertes</div>
    <div class="tab" data-tab="temperature" onclick="switchTab(this,'temperature')">Température</div>
    <div class="tab" data-tab="humidite"    onclick="switchTab(this,'humidite')">Humidité</div>
    <div class="tab" data-tab="gaz" onclick="switchTab(this,'gaz')">Gaz</div>
</div>

<!-- Filter bar -->
<div class="filter-bar">
    <div class="filter-group">
        <label>Date début</label>
        <input type="date" id="hDebut" value="{{ now()->subDays(7)->toDateString() }}" onchange="loadHistory()">
    </div>
    <div class="filter-group">
        <label>Date fin</label>
        <input type="date" id="hFin" value="{{ now()->toDateString() }}" onchange="loadHistory()">
    </div>
    <div class="filter-group">
        <label>Période rapide</label>
        <select onchange="hApplyPeriod(this.value)">
            <option value="">Personnalisée</option>
            <option value="1">Aujourd'hui</option>
            <option value="7" selected>7 jours</option>
            <option value="30">30 jours</option>
            <option value="90">3 mois</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Salle</label>
        <select id="hSalle" onchange="loadHistory()">
            <option value="">Toutes</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Limite</label>
        <select id="hLimit" onchange="loadHistory()">
            <option value="50">50</option>
            <option value="100" selected>100</option>
            <option value="200">200</option>
            <option value="500">500</option>
            <option value="1000">1 000</option>
        </select>
    </div>
    <div class="filter-group" id="niveauGroup" style="display:none">
        <label>Niveau</label>
        <select id="hNiveau" onchange="loadHistory()">
            <option value="">Tous</option>
            <option value="warning">Warning</option>
            <option value="critique">Critique</option>
        </select>
    </div>
    <!-- Sensor range filters — shown per tab -->
    <div class="filter-group" id="rangeGroup" style="display:none">
        <label id="rangeLabel">Plage</label>
        <div style="display:flex;gap:5px;align-items:center">
            <input type="number" id="hRangeMin" placeholder="Min" step="any" style="width:68px" onchange="loadHistory()">
            <span style="color:#555;font-size:11px">–</span>
            <input type="number" id="hRangeMax" placeholder="Max" step="any" style="width:68px" onchange="loadHistory()">
        </div>
    </div>
    <button class="btn btn-blue" onclick="loadHistory()">&#8635; Filtrer</button>
</div>

<!-- Export formats -->
<div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:14px 18px;margin-bottom:20px">
  <div style="font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.5px;font-weight:700;margin-bottom:12px">&#8595; Exporter l'historique</div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(72px,1fr));gap:8px">
    <div onclick="exportHistory('csv')"   title="Tableur universel" style="background:#07102a;border:1px solid rgba(51,255,136,.3);border-radius:9px;padding:10px 6px;cursor:pointer;text-align:center;transition:.2s" onmouseover="this.style.background='rgba(51,255,136,.07)'" onmouseout="this.style.background='#07102a'">
      <div style="font-size:18px">&#128202;</div><div style="font-size:10px;font-weight:700;color:var(--neon)">CSV</div><div style="font-size:9px;color:#555">Tableur</div>
    </div>
    <div onclick="exportHistory('json')"  title="JSON API" style="background:#07102a;border:1px solid rgba(51,181,255,.3);border-radius:9px;padding:10px 6px;cursor:pointer;text-align:center;transition:.2s" onmouseover="this.style.background='rgba(51,181,255,.07)'" onmouseout="this.style.background='#07102a'">
      <div style="font-size:18px">&#123;&#125;</div><div style="font-size:10px;font-weight:700;color:var(--blue)">JSON</div><div style="font-size:9px;color:#555">API</div>
    </div>
    <div onclick="exportHistory('xml')"   title="XML" style="background:#07102a;border:1px solid rgba(255,214,51,.3);border-radius:9px;padding:10px 6px;cursor:pointer;text-align:center;transition:.2s" onmouseover="this.style.background='rgba(255,214,51,.07)'" onmouseout="this.style.background='#07102a'">
      <div style="font-size:18px">&#60;/&#62;</div><div style="font-size:10px;font-weight:700;color:var(--warn)">XML</div><div style="font-size:9px;color:#555">Échange</div>
    </div>
    <div onclick="exportHistory('xlsx')"  title="Excel 2007+" style="background:#07102a;border:1px solid rgba(51,255,136,.3);border-radius:9px;padding:10px 6px;cursor:pointer;text-align:center;transition:.2s" onmouseover="this.style.background='rgba(51,255,136,.07)'" onmouseout="this.style.background='#07102a'">
      <div style="font-size:18px">&#128202;</div><div style="font-size:10px;font-weight:700;color:#00cc66">XLSX</div><div style="font-size:9px;color:#555">Excel</div>
    </div>
    <div onclick="exportHistory('txt')"   title="Texte brut" style="background:#07102a;border:1px solid rgba(170,170,170,.3);border-radius:9px;padding:10px 6px;cursor:pointer;text-align:center;transition:.2s" onmouseover="this.style.background='rgba(170,170,170,.07)'" onmouseout="this.style.background='#07102a'">
      <div style="font-size:18px">&#128220;</div><div style="font-size:10px;font-weight:700;color:#aaa">TXT</div><div style="font-size:9px;color:#555">Brut</div>
    </div>
    <div onclick="exportHistory('sql')"   title="SQL INSERT" style="background:#07102a;border:1px solid rgba(255,153,51,.3);border-radius:9px;padding:10px 6px;cursor:pointer;text-align:center;transition:.2s" onmouseover="this.style.background='rgba(255,153,51,.07)'" onmouseout="this.style.background='#07102a'">
      <div style="font-size:18px">&#128190;</div><div style="font-size:10px;font-weight:700;color:#ff9933">SQL</div><div style="font-size:9px;color:#555">INSERT</div>
    </div>
    <div onclick="exportHistory('docx')"  title="Document Word" style="background:#07102a;border:1px solid rgba(51,181,255,.3);border-radius:9px;padding:10px 6px;cursor:pointer;text-align:center;transition:.2s" onmouseover="this.style.background='rgba(51,181,255,.07)'" onmouseout="this.style.background='#07102a'">
      <div style="font-size:18px">&#128196;</div><div style="font-size:10px;font-weight:700;color:var(--blue)">DOCX</div><div style="font-size:9px;color:#555">Word</div>
    </div>
    <div onclick="exportHistory('zip')"   title="Archive tous formats" style="background:#07102a;border:1px solid rgba(255,214,51,.3);border-radius:9px;padding:10px 6px;cursor:pointer;text-align:center;transition:.2s" onmouseover="this.style.background='rgba(255,214,51,.07)'" onmouseout="this.style.background='#07102a'">
      <div style="font-size:18px">&#128230;</div><div style="font-size:10px;font-weight:700;color:var(--warn)">ZIP</div><div style="font-size:9px;color:#555">Archive</div>
    </div>
    <div onclick="window.open('/rapports/print?type='+(currentTab==='alertes'?'alertes':'mesures')+'&debut='+document.getElementById('hDebut').value+'&fin='+document.getElementById('hFin').value,'_blank')" title="Imprimer / PDF" style="background:#07102a;border:1px solid rgba(255,87,51,.3);border-radius:9px;padding:10px 6px;cursor:pointer;text-align:center;transition:.2s" onmouseover="this.style.background='rgba(255,87,51,.07)'" onmouseout="this.style.background='#07102a'">
      <div style="font-size:18px">&#128438;</div><div style="font-size:10px;font-weight:700;color:var(--danger)">PDF</div><div style="font-size:9px;color:#555">Imprimer</div>
    </div>
  </div>
</div>

<!-- Stats strip -->
<div class="stats-strip">
    <div class="stat-s blue"><div class="v" id="hTotal">—</div><div class="l">Total</div></div>
    <div class="stat-s green"><div class="v" id="hMin">—</div><div class="l">Min</div></div>
    <div class="stat-s warn"><div class="v" id="hMax">—</div><div class="l">Max</div></div>
    <div class="stat-s purple"><div class="v" id="hAvg">—</div><div class="l">Moyenne</div></div>
    <div class="stat-s red"><div class="v" id="hAlerts">—</div><div class="l">Alertes</div></div>
</div>

<!-- Chart -->
<div class="chart-card" id="histChartCard" style="display:none">
    <div class="chart-header">
        <div class="chart-title" id="chartTitle">Évolution</div>
        <div style="font-size:11px;color:#555" id="chartMeta">—</div>
    </div>
    <canvas id="histChart"></canvas>
</div>

<!-- Table -->
<div class="table-card">
    <div class="table-toolbar">
        <div class="title" id="tableTitle">Historique</div>
        <input type="text" class="search-box" id="histSearch" placeholder="Rechercher..." oninput="filterTable()">
    </div>
    <div id="histTableWrapper">
        <div class="loading">Chargement...</div>
    </div>
    <div class="pagination" id="paginationBar" style="display:none">
        <button class="page-btn" id="prevBtn" onclick="changePage(-1)">&#8592; Préc.</button>
        <span class="page-info" id="pageInfo">Page 1</span>
        <button class="page-btn" id="nextBtn" onclick="changePage(1)">Suiv. &#8594;</button>
    </div>
</div>

<script src="/vendor/chartjs/chart.umd.min.js"></script>
<script>
let currentTab = 'all';
let allRows = [];
let currentPage = 1;
const perPage = 25;
let histChart = null;

const tabStyles = {temperature:'temp', humidite:'hum', gaz:'gaz', alertes:'alrt'};
const rangeLabels = {temperature:'Température (°C)', humidite:'Humidité (%)', gaz:'Gaz (ppm)'};
const rangeSteps  = {temperature:0.1, humidite:0.1, gaz:1};

function switchTab(el, tab) {
    document.querySelectorAll('.tab').forEach(t => t.className = 'tab');
    el.classList.add('active');
    if (tabStyles[tab]) el.classList.add(tabStyles[tab]);
    currentTab = tab;
    document.getElementById('niveauGroup').style.display = tab === 'alertes' ? '' : 'none';
    // Show sensor range filter for sensor-specific tabs
    const rangeGroup = document.getElementById('rangeGroup');
    if (rangeLabels[tab]) {
        rangeGroup.style.display = '';
        document.getElementById('rangeLabel').textContent = rangeLabels[tab];
        document.getElementById('hRangeMin').step = rangeSteps[tab] || 1;
        document.getElementById('hRangeMax').step = rangeSteps[tab] || 1;
        document.getElementById('hRangeMin').value = '';
        document.getElementById('hRangeMax').value = '';
    } else {
        rangeGroup.style.display = 'none';
    }
    loadHistory();
}

function hApplyPeriod(days) {
    if (!days) return;
    const end = new Date(), start = new Date();
    start.setDate(start.getDate() - parseInt(days) + 1);
    document.getElementById('hDebut').value = start.toISOString().split('T')[0];
    document.getElementById('hFin').value   = end.toISOString().split('T')[0];
    loadHistory();
}

function loadHistory() {
    const debut   = document.getElementById('hDebut').value;
    const fin     = document.getElementById('hFin').value;
    const limit   = document.getElementById('hLimit').value;
    const niveau  = document.getElementById('hNiveau')?.value || '';
    const salleId = document.getElementById('hSalle')?.value || '';
    const rMin    = document.getElementById('hRangeMin')?.value || '';
    const rMax    = document.getElementById('hRangeMax')?.value || '';
    document.getElementById('histTimestamp').textContent = new Date().toLocaleString('fr-FR');
    document.getElementById('histTableWrapper').innerHTML = '<div class="loading">Chargement...</div>';
    currentPage = 1;

    const type = currentTab === 'alertes' ? 'alertes' : 'mesures';
    let url = `/api/historique-data?type=${type}&debut=${debut}&fin=${fin}&niveau=${niveau}&limit=${limit}&salle_id=${salleId}`;
    // Append sensor range param for active tab
    const rangeParamMap = {temperature:['temp_min','temp_max'], humidite:['hum_min','hum_max'], gaz:['gaz_min','gaz_max']};
    if (rangeParamMap[currentTab]) {
        const [minKey, maxKey] = rangeParamMap[currentTab];
        if (rMin !== '') url += `&${minKey}=${rMin}`;
        if (rMax !== '') url += `&${maxKey}=${rMax}`;
    }

    fetch(url)
        .then(r => r.json())
        .then(data => {
            allRows = Array.isArray(data) ? data : (data.data || []);
            document.getElementById('hTotal').textContent = allRows.length;
            updateStats();
            renderChart();
            renderTable();
        })
        .catch(() => {
            document.getElementById('histTableWrapper').innerHTML = '<div class="no-data">Erreur de chargement.</div>';
        });
}

function getColForTab() {
    const map = {temperature:'temperature', humidite:'humidite', gaz:'gaz'};
    return map[currentTab] || null;
}

function getFilteredRows() {
    const col = getColForTab();
    if (!col || currentTab === 'all' || currentTab === 'alertes') return allRows;
    return allRows.filter(r => r[col] != null);
}

function updateStats() {
    const col = getColForTab();
    const rows = getFilteredRows();
    if (col && rows.length) {
        const vals = rows.map(r => parseFloat(r[col])).filter(v => !isNaN(v));
        document.getElementById('hMin').textContent = vals.length ? Math.min(...vals).toFixed(1) : '—';
        document.getElementById('hMax').textContent = vals.length ? Math.max(...vals).toFixed(1) : '—';
        document.getElementById('hAvg').textContent = vals.length ? (vals.reduce((a,b)=>a+b,0)/vals.length).toFixed(1) : '—';
    } else {
        document.getElementById('hMin').textContent = '—';
        document.getElementById('hMax').textContent = '—';
        document.getElementById('hAvg').textContent = '—';
    }
    if (currentTab === 'alertes') {
        document.getElementById('hAlerts').textContent = allRows.filter(r => r.niveau === 'critique').length;
    } else {
        const warn = allRows.filter(r => r.temperature > 35 || r.humidite > 75 || r.gaz > 300).length;
        document.getElementById('hAlerts').textContent = warn;
    }
}

function renderChart() {
    const canvas = document.getElementById('histChart');
    if (histChart) { histChart.destroy(); histChart = null; }
    const col = getColForTab();
    const rows = getFilteredRows().filter((_, i) => i % Math.max(1, Math.floor(allRows.length/60)) === 0).reverse();
    const labels = rows.map(r => {
        const d = new Date(r.created_at);
        return d.toLocaleString('fr-FR', {day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'});
    });

    let datasets = [];
    const colorMap = {temperature:'#ff5733', humidite:'#33b5ff', gaz:'#ffd633'};

    if (col) {
        datasets = [{ label: col, data: rows.map(r => r[col]), borderColor: colorMap[col]||'#33ff88', backgroundColor: 'rgba(51,255,136,.05)', tension:.4, pointRadius:0 }];
        document.getElementById('chartTitle').textContent = col.charAt(0).toUpperCase() + col.slice(1) + ' sur la période';
    } else if (currentTab === 'alertes') {
        const byDay = {};
        allRows.forEach(r => {
            const d = (r.created_at||'').split('T')[0]||(r.created_at||'').split(' ')[0];
            if (!byDay[d]) byDay[d] = {warning:0,critique:0};
            (byDay[d][r.niveau] = (byDay[d][r.niveau]||0)+1);
        });
        const days = Object.keys(byDay).sort();
        datasets = [
            {label:'Warning', data:days.map(d=>byDay[d].warning),  backgroundColor:'rgba(255,214,51,.5)'},
            {label:'Critique',data:days.map(d=>byDay[d].critique),backgroundColor:'rgba(255,87,51,.5)'},
        ];
        const newLabels = days;
        histChart = new Chart(canvas.getContext('2d'), { type:'bar', data:{labels:newLabels, datasets},
            options:{responsive:true,maintainAspectRatio:true,plugins:{legend:{labels:{color:'#aaa',font:{size:11}}}},scales:{x:{ticks:{color:'#555',font:{size:10}},grid:{color:'#1e2f5a'}},y:{ticks:{color:'#555',font:{size:10}},grid:{color:'#1e2f5a'}}}}
        });
        document.getElementById('chartTitle').textContent = 'Alertes par jour';
        document.getElementById('chartMeta').textContent = allRows.length + ' alertes';
        return;
    } else {
        document.getElementById('histChartCard').style.display = 'none';
        return;
    }

    document.getElementById('histChartCard').style.display = '';
    histChart = new Chart(canvas.getContext('2d'), { type:'line', data:{labels,datasets},
        options:{responsive:true,maintainAspectRatio:true,plugins:{legend:{labels:{color:'#aaa',font:{size:11}}}},scales:{x:{ticks:{color:'#555',maxTicksLimit:10,font:{size:10}},grid:{color:'#1e2f5a'}},y:{ticks:{color:'#555',font:{size:10}},grid:{color:'#1e2f5a'}}}}
    });
    document.getElementById('chartMeta').textContent = rows.length + ' points';
}

function renderTable() {
    const filtered = getFilteredRows();
    const start = (currentPage-1)*perPage;
    const page  = filtered.slice(start, start+perPage);
    const total = filtered.length;
    const pages = Math.ceil(total/perPage);

    document.getElementById('pageInfo').textContent = `Page ${currentPage}/${pages} (${total} entrées)`;
    document.getElementById('paginationBar').style.display = total > perPage ? 'flex' : 'none';
    document.getElementById('prevBtn').disabled = currentPage === 1;
    document.getElementById('nextBtn').disabled = currentPage >= pages;

    if (!page.length) { document.getElementById('histTableWrapper').innerHTML = '<div class="no-data">Aucune donnée sur cette période.</div>'; return; }

    let html = '<div style="overflow-x:auto"><table id="histTable"><thead><tr>';
    if (currentTab === 'alertes') {
        html += '<th>Date</th><th>Message</th><th>Niveau</th><th>Valeur</th><th>Lu</th>';
    } else {
        html += '<th>Date</th><th>Temp °C</th><th>Hum %</th><th>Gaz ppm</th><th>PIR</th>';
    }
    html += '</tr></thead><tbody>';
    page.forEach(r => {
        const t = new Date(r.created_at).toLocaleString('fr-FR');
        if (currentTab === 'alertes') {
            html += `<tr><td style="color:#555;font-size:11px">${t}</td><td>${r.message||'—'}</td><td><span class="badge badge-${r.niveau}">${r.niveau||'—'}</span></td><td style="color:var(--warn)">${r.valeur??'—'}</td><td style="color:${r.lu?'var(--neon)':'#555'}">${r.lu?'✓':'○'}</td></tr>`;
        } else {
            html += `<tr>
                <td style="color:#555;font-size:11px">${t}</td>
                <td style="color:${r.temperature>40?'var(--danger)':r.temperature>35?'var(--warn)':'var(--neon)'}">${r.temperature??'—'}</td>
                <td style="color:${r.humidite>85?'var(--danger)':r.humidite>75?'var(--warn)':'var(--blue)'}">${r.humidite??'—'}</td>
                <td style="color:${r.gaz>500?'var(--danger)':r.gaz>300?'var(--warn)':'#ccc'}">${r.gaz??'—'}</td>
                <td style="color:${r.pir?'var(--danger)':'var(--neon)'}">${r.pir?'OUI':'NON'}</td>
            </tr>`;
        }
    });
    html += '</tbody></table></div>';
    document.getElementById('histTableWrapper').innerHTML = html;
    document.getElementById('tableTitle').textContent = `Historique — ${total} entrées`;
}

function changePage(dir) {
    const filtered = getFilteredRows();
    const pages = Math.ceil(filtered.length/perPage);
    currentPage = Math.max(1, Math.min(pages, currentPage+dir));
    renderTable();
}

function filterTable() {
    const q = document.getElementById('histSearch').value.toLowerCase();
    document.querySelectorAll('#histTable tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}

function exportHistory(format) {
    format = format || 'csv';
    const debut   = document.getElementById('hDebut').value;
    const fin     = document.getElementById('hFin').value;
    const salleId = document.getElementById('hSalle')?.value || '';
    const type    = currentTab === 'alertes' ? 'alertes' : 'mesures';
    window.location.href = `/rapports/export?type=${type}&format=${format}&debut=${debut}&fin=${fin}&salle_id=${salleId}`;
    const labels = {csv:'CSV',json:'JSON',xml:'XML',xlsx:'Excel',txt:'Texte',sql:'SQL',docx:'Word',zip:'Archive ZIP'};
    if (typeof notify === 'function') notify('Téléchargement ' + (labels[format]||format.toUpperCase()) + ' en cours...','i',2500);
}

// Load salles into dropdown
fetch('/api/salles-list').then(r => r.json()).then(salles => {
    const sel = document.getElementById('hSalle');
    salles.forEach(s => {
        const o = document.createElement('option');
        o.value = s.id; o.textContent = s.nom;
        sel.appendChild(o);
    });
}).catch(() => {});

loadHistory();
</script>
@endsection
