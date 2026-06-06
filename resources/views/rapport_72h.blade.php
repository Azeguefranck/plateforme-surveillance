<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Rapport 72h</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;background:#050b16;color:#fff;padding:24px}
.toolbar{display:flex;gap:12px;margin-bottom:20px;align-items:center}
.btn{padding:10px 20px;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px}
.btn-png{background:#33ff88;color:#000}
.btn-back{background:#1e2f5a;color:#fff}
.btn-png:disabled{opacity:.5;cursor:not-allowed}

#rapport{background:#050b16;padding:24px;border-radius:14px}

.header{border-bottom:2px solid #1e2f5a;padding-bottom:14px;margin-bottom:20px}
.header h1{font-size:22px;font-weight:700;color:#33ff88}
.header .meta{font-size:11px;color:#555;margin-top:6px}

.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px}
.stat{background:#0e1a38;border:1px solid #1e2f5a;border-radius:10px;padding:14px;text-align:center}
.stat .val{font-size:28px;font-weight:800;margin-bottom:4px}
.stat .lbl{font-size:10px;color:#aaa;text-transform:uppercase;letter-spacing:.4px}
.stat.blue .val{color:#33b5ff}
.stat.warn .val{color:#ffd633}
.stat.red  .val{color:#ff5733}
.stat.green .val{color:#33ff88}

.chart-box{background:#0e1a38;border:1px solid #1e2f5a;border-radius:12px;padding:18px;margin-bottom:20px}
.chart-box h3{font-size:13px;color:#aaa;margin-bottom:12px;text-transform:uppercase;letter-spacing:.4px}
canvas{max-height:220px}

.table-box{background:#0e1a38;border:1px solid #1e2f5a;border-radius:12px;overflow:hidden}
.table-box h3{font-size:13px;color:#aaa;padding:12px 16px;text-transform:uppercase;letter-spacing:.4px;border-bottom:1px solid #1e2f5a}
table{width:100%;border-collapse:collapse}
thead th{background:#07102a;padding:8px 12px;text-align:left;font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap}
tbody td{padding:8px 12px;border-top:1px solid #0d1a35;font-size:11px}
.row-critique td{background:rgba(255,87,51,.08)}
.row-warning  td{background:rgba(255,214,51,.06)}
.row-pir      td{background:rgba(51,181,255,.05)}

.badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700}
.badge-critique{background:rgba(255,87,51,.2);color:#ff5733;border:1px solid rgba(255,87,51,.4)}
.badge-warning {background:rgba(255,214,51,.2);color:#ffd633;border:1px solid rgba(255,214,51,.4)}
.badge-normal  {background:rgba(51,255,136,.1);color:#33ff88;border:1px solid rgba(51,255,136,.2)}

.pir-oui{color:#ff5733;font-weight:700}
.pir-non{color:#555}

.no-data{text-align:center;padding:40px;color:#555;font-size:13px}

.footer{margin-top:16px;font-size:10px;color:#333;text-align:center}

@media print{.toolbar{display:none}}
</style>
</head>
<body>

<div class="toolbar">
    <button class="btn btn-back" onclick="history.back()">← Retour</button>
    <button class="btn btn-png" id="btnPng" onclick="telechargerPng()">&#128247; Télécharger PNG</button>
    <span id="status" style="font-size:12px;color:#555"></span>
</div>

<div id="rapport">

    <div class="header">
        <h1>&#128202; Rapport 72 heures — Mesures critiques & alertes</h1>
        <div class="meta">
            Période : {{ $debut->format('d/m/Y H:i') }} → {{ $fin->format('d/m/Y H:i') }}
            &nbsp;·&nbsp;
            Généré le {{ now()->format('d/m/Y à H:i') }}
            &nbsp;·&nbsp;
            {{ count($rows) }} enregistrement(s) affichés
        </div>
    </div>

    @php
        $total    = count($rows);
        $critiques = $rows->filter(fn($r) => $r->temperature >= 32 || $r->humidite >= 85 || $r->gaz >= 600)->count();
        $warnings  = $rows->filter(fn($r) =>
            ($r->temperature >= 28 && $r->temperature < 32) ||
            ($r->humidite    >= 75 && $r->humidite    < 85) ||
            ($r->gaz         >= 300 && $r->gaz        < 600)
        )->count();
        $pirOui   = $rows->filter(fn($r) => $r->pir_detecte)->count();
    @endphp

    <div class="stats">
        <div class="stat blue"><div class="val">{{ $total }}</div><div class="lbl">Total mesures</div></div>
        <div class="stat red"><div class="val">{{ $critiques }}</div><div class="lbl">Critiques</div></div>
        <div class="stat warn"><div class="val">{{ $warnings }}</div><div class="lbl">Warnings</div></div>
        <div class="stat green"><div class="val">{{ $pirOui }}</div><div class="lbl">PIR détecté</div></div>
    </div>

    @if($total > 0)
    <div class="chart-box">
        <h3>Évolution température / humidité / gaz</h3>
        <canvas id="chart72"></canvas>
    </div>
    @endif

    <div class="table-box">
        <h3>Détail des mesures</h3>
        @if($total === 0)
            <div class="no-data">Aucune mesure warning, critique ou PIR sur cette période.</div>
        @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date / Heure</th>
                    <th>Temp (°C)</th>
                    <th>Humidité (%)</th>
                    <th>Gaz (ppm)</th>
                    <th>PIR</th>
                    <th>Niveau</th>
                </tr>
            </thead>
            <tbody>
            @foreach($rows as $r)
                @php
                    $isCritique = $r->temperature >= 32 || $r->humidite >= 85 || $r->gaz >= 600;
                    $isWarning  = !$isCritique && ($r->temperature >= 28 || $r->humidite >= 75 || $r->gaz >= 300);
                    $rowClass   = $isCritique ? 'row-critique' : ($isWarning ? 'row-warning' : ($r->pir_detecte ? 'row-pir' : ''));
                    $niveau     = $isCritique ? 'critique' : ($isWarning ? 'warning' : 'normal');
                @endphp
                <tr class="{{ $rowClass }}">
                    <td style="color:#333">{{ $r->id }}</td>
                    <td style="color:#aaa">{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i:s') }}</td>
                    <td style="color:{{ $r->temperature >= 32 ? '#ff5733' : ($r->temperature >= 28 ? '#ffd633' : '#33ff88') }}">
                        {{ $r->temperature ?? '—' }}
                    </td>
                    <td style="color:{{ $r->humidite >= 85 ? '#ff5733' : ($r->humidite >= 75 ? '#ffd633' : '#33b5ff') }}">
                        {{ $r->humidite ?? '—' }}
                    </td>
                    <td style="color:{{ $r->gaz >= 600 ? '#ff5733' : ($r->gaz >= 300 ? '#ffd633' : '#ccc') }}">
                        {{ $r->gaz ?? '—' }}
                    </td>
                    <td class="{{ $r->pir_detecte ? 'pir-oui' : 'pir-non' }}">
                        {{ $r->pir_detecte ? 'OUI' : 'NON' }}
                    </td>
                    <td><span class="badge badge-{{ $niveau }}">{{ strtoupper($niveau) }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="footer">
        Plateforme de Surveillance &nbsp;·&nbsp; Rapport automatique 72h &nbsp;·&nbsp; {{ now()->format('d/m/Y H:i') }}
    </div>

</div>

@if($total > 0)
<script>
const rows = @json($rows->values());
const labels = rows.map(r => {
    const d = new Date(r.created_at.replace(' ', 'T'));
    return d.toLocaleDateString('fr-FR',{day:'2-digit',month:'2-digit'}) + ' '
         + d.toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'});
});
new Chart(document.getElementById('chart72').getContext('2d'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            { label: 'Temp (°C)',   data: rows.map(r => r.temperature), borderColor: '#ff5733', backgroundColor: 'rgba(255,87,51,.07)',  tension: .3, pointRadius: 2, fill: true },
            { label: 'Hum (%)',    data: rows.map(r => r.humidite),    borderColor: '#33b5ff', backgroundColor: 'rgba(51,181,255,.07)', tension: .3, pointRadius: 2, fill: true },
            { label: 'Gaz / 10',  data: rows.map(r => r.gaz ? r.gaz / 10 : null), borderColor: '#ffd633', backgroundColor: 'rgba(255,214,51,.05)', tension: .3, pointRadius: 2, fill: true },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: {
            legend: { labels: { color: '#aaa', font: { size: 11 } } },
            annotation: {}
        },
        scales: {
            x: { ticks: { color: '#444', maxTicksLimit: 12, font: { size: 9 } }, grid: { color: '#0d1a35' } },
            y: { ticks: { color: '#444', font: { size: 10 } }, grid: { color: '#0d1a35' } }
        }
    }
});
</script>
@endif

<script>
function telechargerPng() {
    const btn = document.getElementById('btnPng');
    const st  = document.getElementById('status');
    btn.disabled = true;
    st.textContent = 'Génération en cours…';
    const el = document.getElementById('rapport');
    html2canvas(el, {
        backgroundColor: '#050b16',
        scale: 2,
        useCORS: true,
        logging: false
    }).then(canvas => {
        const date = new Date().toISOString().slice(0, 10);
        const link = document.createElement('a');
        link.download = 'rapport_72h_' + date + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        st.textContent = 'Téléchargement lancé.';
        btn.disabled = false;
    }).catch(() => {
        st.textContent = 'Erreur de génération.';
        btn.disabled = false;
    });
}

// Téléchargement automatique dès que la page et le graphique sont prêts
window.addEventListener('load', function() {
    const st = document.getElementById('status');
    st.textContent = 'Génération du PNG en cours…';
    // Attendre 1.2s pour que Chart.js finisse de dessiner
    setTimeout(function() {
        telechargerPng();
    }, 1200);
});
</script>

</body>
</html>
