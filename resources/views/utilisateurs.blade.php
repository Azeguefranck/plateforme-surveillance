@extends('layouts.app')

@section('content')

<style>
.page-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
  flex-wrap:wrap;
  gap:10px;
}
.page-title{
  font-size:20px;
  font-weight:bold;
  color:var(--text);
  display:flex;
  align-items:center;
  gap:10px;
}
.page-title i{ color:var(--accent); }

.stats-bar{
  display:flex;
  gap:12px;
  margin-bottom:20px;
  flex-wrap:wrap;
}
.stat-card{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:10px;
  padding:14px 20px;
  min-width:130px;
  text-align:center;
}
.stat-card .num{
  font-size:24px;
  font-weight:bold;
  color:var(--accent);
}
.stat-card .lbl{
  font-size:12px;
  color:var(--muted);
  margin-top:2px;
}
.stat-card.danger .num{ color:var(--danger); }
.stat-card.warn   .num{ color:#d97706; }
.stat-card.info   .num{ color:var(--info); }

.flash-msg{
  padding:12px 18px;
  border-radius:10px;
  margin-bottom:16px;
  font-weight:bold;
  font-size:14px;
  display:flex;
  align-items:center;
  gap:10px;
}
.flash-success{ background:rgba(47,168,79,0.15); border:1px solid var(--accent); color:var(--accent); }
.flash-error  { background:rgba(192,57,43,0.15);  border:1px solid var(--danger);  color:var(--danger);  }

.search-bar{
  display:flex;
  gap:10px;
  margin-bottom:16px;
  flex-wrap:wrap;
}
.search-bar input,
.search-bar select{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:8px;
  color:var(--text);
  padding:8px 14px;
  font-size:13px;
  outline:none;
}
.search-bar input{ flex:1; min-width:200px; }
.search-bar input::placeholder{ color:var(--muted); }
.search-bar select option{ background:var(--card); }

.table-wrap{
  background:var(--card);
  border:1px solid var(--border);
  border-radius:14px;
  overflow:hidden;
}
.table-wrap table{
  width:100%;
  border-collapse:collapse;
}
.table-wrap thead th{
  background:#0a1525;
  padding:12px 14px;
  text-align:left;
  font-size:12px;
  color:var(--muted);
  text-transform:uppercase;
  letter-spacing:1px;
  white-space:nowrap;
  border-bottom:1px solid var(--border);
}
.table-wrap tbody tr{
  border-bottom:1px solid var(--border);
  transition:.15s;
}
.table-wrap tbody tr:last-child{ border-bottom:none; }
.table-wrap tbody tr:hover{ background:rgba(255,255,255,0.02); }
.table-wrap tbody td{
  padding:12px 14px;
  font-size:13px;
  color:var(--text);
  vertical-align:middle;
}

.avatar{
  width:36px;
  height:36px;
  border-radius:50%;
  background:var(--accent);
  color:#060c1a;
  font-weight:bold;
  font-size:14px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  flex-shrink:0;
  margin-right:8px;
}
.user-cell{
  display:flex;
  align-items:center;
}
.user-name{ font-weight:bold; font-size:13px; }
.user-email{ color:var(--muted); font-size:12px; }

.badge{
  display:inline-flex;
  align-items:center;
  gap:5px;
  padding:4px 10px;
  border-radius:20px;
  font-size:11px;
  font-weight:bold;
  letter-spacing:.5px;
  text-transform:uppercase;
}
.badge-valide    { background:rgba(47,168,79,0.15);  color:#2fa84f; }
.badge-attente   { background:rgba(217,119,6,0.15);  color:#d97706; }
.badge-refuse    { background:rgba(192,57,43,0.15);  color:#c0392b; }
.badge-bloque    { background:rgba(107,127,160,0.15);color:var(--muted); }
.badge-admin     { background:rgba(46,134,193,0.15); color:#2e86c1; }
.badge-user      { background:rgba(47,168,79,0.08);  color:var(--muted); }

.action-btns{
  display:flex;
  gap:6px;
  flex-wrap:nowrap;
}
.btn-action{
  display:inline-flex;
  align-items:center;
  gap:5px;
  padding:6px 12px;
  border:none;
  border-radius:7px;
  font-weight:bold;
  font-size:12px;
  cursor:pointer;
  transition:.2s;
  white-space:nowrap;
}
.btn-action:hover{ opacity:.85; transform:translateY(-1px); }
.btn-valider { background:#2fa84f; color:#060c1a; }
.btn-attente { background:#d97706; color:white; }
.btn-refuser { background:var(--danger); color:white; }

.empty-row td{
  text-align:center;
  padding:40px;
  color:var(--muted);
  font-size:14px;
}

@media(max-width:900px){
  .action-btns{ flex-wrap:wrap; }
  .table-wrap{ overflow-x:auto; }
  .table-wrap table{ min-width:700px; }
}
</style>

<div class="page-header">
  <div class="page-title">
    <i class="fa-solid fa-users"></i>
    Gestion des Utilisateurs
  </div>
</div>

@if(session('success'))
<div class="flash-msg flash-success">
  <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="flash-msg flash-error">
  <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
</div>
@endif

<!-- Stats -->
<div class="stats-bar">
  @php
    $total    = count($users);
    $valides  = $users->where('statut','valide')->count();
    $attentes = $users->whereIn('statut',['en_attente',null,''])->filter(fn($u)=> in_array($u->statut,['en_attente']))->count();
    $refuses  = $users->where('statut','refuse')->count();
  @endphp
  <div class="stat-card">
    <div class="num">{{ $total }}</div>
    <div class="lbl">Total</div>
  </div>
  <div class="stat-card">
    <div class="num">{{ $valides }}</div>
    <div class="lbl">Validés</div>
  </div>
  <div class="stat-card warn">
    <div class="num">{{ $attentes }}</div>
    <div class="lbl">En attente</div>
  </div>
  <div class="stat-card danger">
    <div class="num">{{ $refuses }}</div>
    <div class="lbl">Refusés</div>
  </div>
</div>

<!-- Search -->
<div class="search-bar">
  <input type="text" id="searchInput" placeholder="&#xf002;  Rechercher par nom, email, profession..." onkeyup="filterTable()">
  <select id="filterStatut" onchange="filterTable()">
    <option value="">Tous les statuts</option>
    <option value="valide">Validé</option>
    <option value="en_attente">En attente</option>
    <option value="refuse">Refusé</option>
  </select>
  <select id="filterRole" onchange="filterTable()">
    <option value="">Tous les rôles</option>
    <option value="admin">Admin</option>
    <option value="utilisateur">Utilisateur</option>
  </select>
</div>

<!-- Table -->
<div class="table-wrap">
  <table id="usersTable">
    <thead>
      <tr>
        <th>Utilisateur</th>
        <th>Téléphone</th>
        <th>Profession</th>
        <th>Rôle</th>
        <th>Statut</th>
        <th>Inscription</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $user)
      <tr data-statut="{{ $user->statut }}" data-role="{{ $user->role }}">
        <td>
          <div class="user-cell">
            <div class="avatar">{{ strtoupper(substr($user->nom ?? 'U', 0, 1)) }}</div>
            <div>
              <div class="user-name">{{ $user->nom }} {{ $user->prenom }}</div>
              <div class="user-email">{{ $user->email }}</div>
            </div>
          </div>
        </td>
        <td>{{ $user->telephone ?? '—' }}</td>
        <td>{{ $user->profession ?? '—' }}</td>
        <td>
          @if($user->role === 'admin')
            <span class="badge badge-admin"><i class="fa-solid fa-shield-halved"></i> Admin</span>
          @else
            <span class="badge badge-user"><i class="fa-solid fa-user"></i> Utilisateur</span>
          @endif
        </td>
        <td>
          @if($user->statut === 'valide')
            <span class="badge badge-valide"><i class="fa-solid fa-circle-check"></i> Validé</span>
          @elseif($user->statut === 'refuse')
            <span class="badge badge-refuse"><i class="fa-solid fa-circle-xmark"></i> Refusé</span>
          @elseif($user->statut === 'bloque')
            <span class="badge badge-bloque"><i class="fa-solid fa-lock"></i> Bloqué</span>
          @else
            <span class="badge badge-attente"><i class="fa-solid fa-clock"></i> En attente</span>
          @endif
        </td>
        <td style="color:var(--muted);font-size:12px;white-space:nowrap;">
          {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') : '—' }}
        </td>
        <td>
          <div class="action-btns">
            @if($user->statut !== 'valide')
            <form action="/admin/valider/{{ $user->id }}" method="POST" style="display:inline;">
              @csrf
              <button class="btn-action btn-valider" data-nom="{{ e($user->nom) }}" onclick="event.preventDefault();CyberConfirm.show({title:'Valider le compte',message:'Valider le compte de '+this.dataset.nom+' ?',icon:'fa-solid fa-user-check',confirmText:'Valider',confirmColor:'success'}).then(ok=>{if(ok)this.closest('form').submit();})">
                <i class="fa-solid fa-check"></i> Valider
              </button>
            </form>
            @endif

            @if($user->statut !== 'en_attente')
            <form action="/admin/attente/{{ $user->id }}" method="POST" style="display:inline;">
              @csrf
              <button class="btn-action btn-attente" data-nom="{{ e($user->nom) }}" onclick="event.preventDefault();CyberConfirm.show({title:'Mettre en attente',message:'Mettre en attente le compte de '+this.dataset.nom+' ?',icon:'fa-solid fa-clock',confirmText:'Mettre en attente',confirmColor:'warning'}).then(ok=>{if(ok)this.closest('form').submit();})">
                <i class="fa-solid fa-clock"></i> Attente
              </button>
            </form>
            @endif

            @if($user->statut !== 'refuse')
            <form action="/admin/refuser/{{ $user->id }}" method="POST" style="display:inline;">
              @csrf
              <button class="btn-action btn-refuser" data-nom="{{ e($user->nom) }}" onclick="event.preventDefault();CyberConfirm.show({title:'Refuser le compte',message:'Refuser le compte de '+this.dataset.nom+' ?',icon:'fa-solid fa-user-xmark',confirmText:'Refuser',confirmColor:'danger'}).then(ok=>{if(ok)this.closest('form').submit();})">
                <i class="fa-solid fa-xmark"></i> Refuser
              </button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr class="empty-row">
        <td colspan="7">
          <i class="fa-solid fa-users" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>
          Aucun utilisateur enregistré
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<script>
function filterTable(){
  const q       = document.getElementById('searchInput').value.toLowerCase();
  const statut  = document.getElementById('filterStatut').value;
  const role    = document.getElementById('filterRole').value;
  document.querySelectorAll('#usersTable tbody tr:not(.empty-row)').forEach(tr => {
    const text  = tr.textContent.toLowerCase();
    const tStat = tr.dataset.statut || '';
    const tRole = tr.dataset.role   || '';
    const ok = text.includes(q)
      && (!statut || tStat === statut)
      && (!role   || tRole === role);
    tr.style.display = ok ? '' : 'none';
  });
}
</script>

@endsection
