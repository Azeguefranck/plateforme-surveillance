@extends('layouts.app')

@section('content')

<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#060d1f;color:#e0e8ff;font-family:'Segoe UI',Arial,sans-serif}

.page-header{padding:18px 0 28px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.page-title{font-size:24px;font-weight:700;color:#fff;letter-spacing:1px}
.page-title span{color:#33ff88}

.card{background:linear-gradient(135deg,#0e1a38,#111c3d);border:1px solid #1e2f5a;border-radius:18px;padding:24px;margin-bottom:24px}

.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:24px}
.stat{background:linear-gradient(135deg,#0e1a38,#111c3d);border:1px solid #1e2f5a;border-radius:14px;padding:18px;text-align:center}
.stat-num{font-size:30px;font-weight:700;color:#33ff88}
.stat-num.crit{color:#ff5733}
.stat-label{font-size:11px;color:#8899cc;margin-top:5px;text-transform:uppercase;letter-spacing:.5px}

.filtres{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:center}
.filtres select,.filtres input{
  background:#07102a;border:1px solid #1e2f5a;color:#c7d2ff;
  padding:8px 14px;border-radius:8px;font-size:13px;outline:none;
}
.filtres select:focus,.filtres input:focus{border-color:#33ff88}
.btn-filtre{
  padding:8px 18px;background:rgba(51,255,136,.1);border:1px solid #33ff88;
  color:#33ff88;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;transition:.2s;
}
.btn-filtre:hover{background:rgba(51,255,136,.2)}

.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
thead th{
  background:#07102a;color:#8899cc;text-transform:uppercase;letter-spacing:.8px;
  font-size:11px;padding:12px 14px;border-bottom:1px solid #1e2f5a;text-align:left;
}
tbody tr{border-bottom:1px solid #0e1c35;transition:.15s}
tbody tr:hover{background:rgba(51,255,136,.04)}
tbody td{padding:12px 14px;color:#c7d2ff;vertical-align:middle}

.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.5px;white-space:nowrap}
.badge-critique{background:rgba(255,87,51,.15);color:#ff5733;border:1px solid rgba(255,87,51,.3)}
.badge-warning {background:rgba(255,214,51,.15);color:#ffd633;border:1px solid rgba(255,214,51,.3)}
.badge-info    {background:rgba(51,255,136,.15);color:#33ff88;border:1px solid rgba(51,255,136,.3)}

.dest{font-size:12px;color:#5a7aaa}
.vide{text-align:center;color:#5a6a99;padding:40px;font-size:14px}
.pagination{display:flex;justify-content:center;gap:8px;margin-top:20px;flex-wrap:wrap}
.page-btn{
  padding:6px 14px;border-radius:8px;border:1px solid #1e2f5a;
  background:#07102a;color:#8899cc;cursor:pointer;font-size:12px;transition:.2s;
}
.page-btn:hover,.page-btn.active{border-color:#33ff88;color:#33ff88;background:rgba(51,255,136,.08)}
</style>

<div class="page-header">
  <div class="page-title">HISTORIQUE DES <span>MAILS D'ALERTES</span></div>
  <div style="font-size:12px;color:#5a6a99">Destinataire : franckazegue0007@gmail.com</div>
</div>

<div class="stats">
  <div class="stat">
    <div class="stat-num" id="st-total">—</div>
    <div class="stat-label">Alertes totales</div>
  </div>
  <div class="stat">
    <div class="stat-num crit" id="st-critique">—</div>
    <div class="stat-label">Critiques</div>
  </div>
  <div class="stat">
    <div class="stat-num" style="color:#ffd633" id="st-warning">—</div>
    <div class="stat-label">Warnings</div>
  </div>
  <div class="stat">
    <div class="stat-num" id="st-today">—</div>
    <div class="stat-label">Aujourd'hui</div>
  </div>
</div>

<div class="card">
  <div class="filtres">
    <select id="f-niveau">
      <option value="">Tous niveaux</option>
      <option value="critique">Critique</option>
      <option value="warning">Warning</option>
    </select>
    <input type="date" id="f-debut" placeholder="Date début">
    <input type="date" id="f-fin" placeholder="Date fin">
    <button class="btn-filtre" onclick="charger(1)">Filtrer</button>
    <button class="btn-filtre" style="border-color:#5a6a99;color:#5a6a99" onclick="reinitFiltres()">Réinitialiser</button>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Date / Heure</th>
          <th>Niveau</th>
          <th>Capteur</th>
          <th>Valeur mesurée</th>
          <th>Message</th>
          <th>Destinataire</th>
        </tr>
      </thead>
      <tbody id="mails-body">
        <tr><td colspan="7" class="vide">Chargement…</td></tr>
      </tbody>
    </table>
  </div>

  <div class="pagination" id="pagination"></div>
</div>

<script>
let pageCourante = 1;
const PAR_PAGE   = 20;

function reinitFiltres() {
  document.getElementById('f-niveau').value = '';
  document.getElementById('f-debut').value  = '';
  document.getElementById('f-fin').value    = '';
  charger(1);
}

function charger(page) {
  pageCourante = page;
  const niveau = document.getElementById('f-niveau').value;
  const debut  = document.getElementById('f-debut').value;
  const fin    = document.getElementById('f-fin').value;

  let url = `/api/alertes-mails?page=${page}&par_page=${PAR_PAGE}`;
  if (niveau) url += `&niveau=${encodeURIComponent(niveau)}`;
  if (debut)  url += `&debut=${debut}`;
  if (fin)    url += `&fin=${fin}`;

  fetch(url)
    .then(r => r.json())
    .then(res => {
      afficherStats(res.stats);
      afficherTable(res.data);
      afficherPagination(res.total, page);
    })
    .catch(() => {
      document.getElementById('mails-body').innerHTML = '<tr><td colspan="7" class="vide">Erreur de chargement</td></tr>';
    });
}

function afficherStats(s) {
  if (!s) return;
  document.getElementById('st-total').textContent    = s.total    ?? '—';
  document.getElementById('st-critique').textContent = s.critique ?? '—';
  document.getElementById('st-warning').textContent  = s.warning  ?? '—';
  document.getElementById('st-today').textContent    = s.today    ?? '—';
}

function afficherTable(rows) {
  const tbody = document.getElementById('mails-body');
  if (!rows || !rows.length) {
    tbody.innerHTML = '<tr><td colspan="7" class="vide">Aucun mail d\'alerte enregistré</td></tr>';
    return;
  }
  tbody.innerHTML = rows.map((a, i) => {
    const niv   = (a.niveau || '').toLowerCase();
    const badge = niv === 'critique' ? 'badge-critique' : niv === 'warning' ? 'badge-warning' : 'badge-info';
    const date  = (a.created_at || '').substring(0, 16).replace('T', ' ');
    const type  = a.type || a.capteur || '—';
    return `<tr>
      <td style="color:#5a6a99">${a.id}</td>
      <td>${date}</td>
      <td><span class="badge ${badge}">${(a.niveau||'').toUpperCase()}</span></td>
      <td style="color:#fff;font-weight:600">${type.toUpperCase()}</td>
      <td style="color:#ff5733;font-weight:700">${a.valeur || '—'}</td>
      <td style="max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${a.message||''}">${a.message || '—'}</td>
      <td class="dest">franckazegue0007@gmail.com</td>
    </tr>`;
  }).join('');
}

function afficherPagination(total, page) {
  const pages = Math.ceil(total / PAR_PAGE);
  const pg    = document.getElementById('pagination');
  if (pages <= 1) { pg.innerHTML = ''; return; }
  let html = '';
  for (let i = 1; i <= pages; i++) {
    html += `<button class="page-btn${i===page?' active':''}" onclick="charger(${i})">${i}</button>`;
  }
  pg.innerHTML = html;
}

charger(1);
</script>

@endsection
