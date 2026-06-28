<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;font-size:11px;color:#111;background:#fff;padding:20px}
.no-print{margin-bottom:16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.no-print button{border:none;padding:9px 20px;border-radius:7px;font-weight:700;cursor:pointer;font-size:13px}
.btn-print{background:#060d1f;color:#33ff88}
.btn-close{background:#eee;color:#333}
.header{border-bottom:2px solid #060d1f;padding-bottom:10px;margin-bottom:14px}
.header h1{font-size:18px;font-weight:700;color:#060d1f}
.header .meta{font-size:10px;color:#666;margin-top:4px}
table{width:100%;border-collapse:collapse}
thead th{background:#060d1f;color:#fff;padding:6px 8px;text-align:left;font-size:9px;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap}
tbody td{padding:5px 8px;border-bottom:1px solid #e5e5e5;vertical-align:top;word-break:break-word;max-width:200px}
tbody tr:nth-child(even) td{background:#f7f9fc}
.footer{margin-top:14px;font-size:9px;color:#aaa;text-align:center;border-top:1px solid #eee;padding-top:8px}
.no-data{text-align:center;padding:40px;color:#999}
@media print{
    .no-print{display:none!important}
    body{padding:8mm}
    thead{display:table-header-group}
}
</style>
</head>
<body>

<div class="no-print">
    <button class="btn-print" onclick="window.print()">&#128438; Imprimer / Exporter PDF</button>
    <button class="btn-close" onclick="window.close()">Fermer</button>
    <span style="font-size:12px;color:#666">Utilisez <strong>Ctrl+P</strong> → Enregistrer en PDF</span>
</div>

<div class="header">
    <h1>{{ $title }}</h1>
    <div class="meta">
        Plateforme Surveillance &nbsp;·&nbsp;
        Généré le {{ now()->format('d/m/Y à H:i') }} &nbsp;·&nbsp;
        {{ count($data) }} enregistrement(s)
    </div>
</div>

@if(count($data) > 0)
<table>
    <thead>
        <tr>
            @foreach(array_keys($data[0]) as $col)
            <th>{{ $col }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            @foreach($row as $val)
            <td>{{ $val ?? '—' }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="no-data">Aucune donnée sur cette période.</div>
@endif

<div class="footer">
    Plateforme Surveillance &nbsp;·&nbsp; {{ now()->format('d/m/Y H:i') }} &nbsp;·&nbsp; {{ count($data) }} enregistrements
</div>

<script>
window.onload = function() { setTimeout(function() { window.print(); }, 600); };
</script>
</body>
</html>
