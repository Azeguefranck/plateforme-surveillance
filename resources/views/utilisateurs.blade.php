@extends('layouts.app')

@section('content')
<style>
:root{--neon:#33ff88;--blue:#33b5ff;--warn:#ffd633;--danger:#ff5733;--card:#0e1a38;--border:#1e2f5a;}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-title{font-size:22px;font-weight:700;color:#fff}
.btn{padding:8px 16px;border:none;border-radius:7px;font-weight:700;cursor:pointer;font-size:12px;transition:.18s;display:inline-flex;align-items:center;gap:5px;white-space:nowrap}
.btn:active{transform:scale(.96)}
.btn-green{background:transparent;border:1px solid var(--neon);color:var(--neon)}
.btn-green:hover:not(:disabled){background:var(--neon);color:#000}
.btn-red{background:transparent;border:1px solid var(--danger);color:var(--danger)}
.btn-red:hover:not(:disabled){background:var(--danger);color:#fff}
.btn-blue{background:transparent;border:1px solid var(--blue);color:var(--blue)}
.btn-blue:hover:not(:disabled){background:var(--blue);color:#000}
.btn-warn{background:transparent;border:1px solid var(--warn);color:var(--warn)}
.btn-warn:hover:not(:disabled){background:var(--warn);color:#000}
.btn-gray{background:transparent;border:1px solid #3a4a6a;color:#777}
.btn-gray:hover:not(:disabled){border-color:#aaa;color:#aaa}
.btn:disabled{opacity:.45;cursor:not-allowed}

.stats-row{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:22px}
.scard{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 14px;text-align:center}
.scard .v{font-size:28px;font-weight:800;margin-bottom:3px}
.scard .l{font-size:10px;color:#666;text-transform:uppercase;letter-spacing:.4px}
.scard.t .v{color:var(--blue)}
.scard.g .v{color:var(--neon)}
.scard.w .v{color:var(--warn)}
.scard.r .v{color:var(--danger)}
.scard.d .v{color:#888}

.filter-bar{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:13px 18px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-bottom:18px}
.filter-bar input,.filter-bar select{background:#07102a;border:1px solid var(--border);border-radius:7px;padding:8px 11px;color:#fff;font-size:12px;outline:none;transition:.2s}
.filter-bar input:focus,.filter-bar select:focus{border-color:var(--blue)}
.filter-bar select option{background:#0e1a38}
.filter-bar input{width:220px}

.table-card{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden}
table{width:100%;border-collapse:collapse}
thead tr{background:#07102a}
th{padding:10px 14px;text-align:left;font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap}
td{padding:10px 14px;border-top:1px solid var(--border);font-size:12px;color:#ccc;vertical-align:middle}
tr:hover td{background:rgba(51,181,255,.03)}
.avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#1e2f5a,#0b1632);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--blue);flex-shrink:0}
.user-cell{display:flex;align-items:center;gap:10px}
.user-name{font-weight:600;color:#fff;font-size:13px}
.user-date{font-size:10px;color:#555;margin-top:1px}
.badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase}
.badge-valide{background:rgba(51,255,136,.1);color:var(--neon);border:1px solid rgba(51,255,136,.25)}
.badge-en_attente{background:rgba(255,214,51,.1);color:var(--warn);border:1px solid rgba(255,214,51,.25)}
.badge-bloque{background:rgba(255,87,51,.1);color:var(--danger);border:1px solid rgba(255,87,51,.25)}
.badge-refuse{background:rgba(120,120,120,.1);color:#888;border:1px solid rgba(120,120,120,.25)}
.actions-cell{display:flex;gap:5px;flex-wrap:wrap;align-items:center}
.no-data{text-align:center;padding:40px;color:#555;font-size:13px}
.toolbar{display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:10px}
.toolbar-title{font-size:13px;font-weight:700;color:#fff}
.count-badge{background:#07102a;border:1px solid var(--border);border-radius:20px;padding:3px 10px;font-size:11px;color:#aaa}

@media(max-width:900px){.stats-row{grid-template-columns:repeat(3,1fr)}table{display:block;overflow-x:auto}}
</style>

@php
try {
    $users = DB::table('users')->orderByDesc('created_at')->get();
    $statCounts = [
        'total'      => $users->count(),
        'valide'     => $users->where('validation_status','valide')->count(),
        'en_attente' => $users->where('validation_status','en_attente')->count(),
        'bloque'     => $users->where('validation_status','bloque')->count(),
        'refuse'     => $users->where('validation_status','refuse')->count(),
    ];
} catch(\Exception $e) {
    $users = collect();
    $statCounts = ['total'=>0,'valide'=>0,'en_attente'=>0,'bloque'=>0,'refuse'=>0];
}
@endphp

<div class="pg-header">
    <div class="pg-title">Gestion des Utilisateurs</div>
    <button class="btn btn-green" onclick="ouvrirModalCreer()"><i class="fa-solid fa-user-plus"></i> Créer un utilisateur</button>
</div>

<div id="modalCreer" style="display:none;position:fixed;inset:0;background:rgba(2,5,18,.88);backdrop-filter:blur(10px);z-index:10000;align-items:center;justify-content:center">
  <div style="background:#0a1428;border:1px solid #1e2f5a;border-radius:18px;padding:30px;max-width:440px;width:94%;box-shadow:0 12px 60px rgba(0,0,0,.8)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px">
      <div style="font-size:16px;font-weight:800;color:#fff"><i class="fa-solid fa-user-plus" style="color:var(--neon);margin-right:8px"></i>Créer un utilisateur</div>
      <button onclick="fermerModalCreer()" style="background:none;border:none;color:#555;font-size:20px;cursor:pointer">×</button>
    </div>
    <div id="creerMsg" style="display:none;margin-bottom:14px;padding:10px 14px;border-radius:8px;font-size:13px;font-weight:600"></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
      <div>
        <label style="font-size:11px;color:#8899cc;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:5px">Prénom</label>
        <input id="c_prenom" type="text" placeholder="Prénom" style="width:100%;background:#07102a;border:1px solid #1e2f5a;border-radius:7px;padding:9px 11px;color:#fff;font-size:13px;outline:none">
      </div>
      <div>
        <label style="font-size:11px;color:#8899cc;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:5px">Nom</label>
        <input id="c_nom" type="text" placeholder="Nom" style="width:100%;background:#07102a;border:1px solid #1e2f5a;border-radius:7px;padding:9px 11px;color:#fff;font-size:13px;outline:none">
      </div>
    </div>
    <div style="margin-bottom:12px">
      <label style="font-size:11px;color:#8899cc;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:5px">Adresse email officielle</label>
      <input id="c_email" type="email" placeholder="prenom.nom@organisation.com" style="width:100%;background:#07102a;border:1px solid #1e2f5a;border-radius:7px;padding:9px 11px;color:#fff;font-size:13px;outline:none">
    </div>
    <div style="margin-bottom:22px">
      <label style="font-size:11px;color:#8899cc;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:5px">Rôle</label>
      <select id="c_role" style="width:100%;background:#07102a;border:1px solid #1e2f5a;border-radius:7px;padding:9px 11px;color:#fff;font-size:13px;outline:none">
        <option value="utilisateur">Utilisateur</option>
        <option value="technicien">Technicien</option>
        <option value="admin">Administrateur</option>
        <option value="invite">Invité</option>
      </select>
    </div>
    <p style="font-size:11px;color:#3a4a6a;margin-bottom:18px">Un mot de passe temporaire sera généré et envoyé automatiquement à l'adresse email fournie.</p>
    <div style="display:flex;gap:10px">
      <button onclick="fermerModalCreer()" style="flex:1;background:rgba(18,30,68,.65);border:1px solid #1e2f5a;color:#7788aa;padding:11px;border-radius:9px;font-weight:700;cursor:pointer;font-size:13px">Annuler</button>
      <button id="c_btn" onclick="creerUtilisateur()" style="flex:2;background:rgba(51,255,136,.1);border:1px solid rgba(51,255,136,.35);color:var(--neon);padding:11px;border-radius:9px;font-weight:700;cursor:pointer;font-size:13px"><i class="fa-solid fa-paper-plane"></i> Créer et envoyer</button>
    </div>
  </div>
</div>

<div class="stats-row">
    <div class="scard t"><div class="v">{{ $statCounts['total'] }}</div><div class="l">Total</div></div>
    <div class="scard g"><div class="v">{{ $statCounts['valide'] }}</div><div class="l">Validés</div></div>
    <div class="scard w"><div class="v">{{ $statCounts['en_attente'] }}</div><div class="l">En attente</div></div>
    <div class="scard r"><div class="v">{{ $statCounts['bloque'] }}</div><div class="l">Bloqués</div></div>
    <div class="scard d"><div class="v">{{ $statCounts['refuse'] }}</div><div class="l">Refusés</div></div>
</div>

<div class="filter-bar">
    <input type="text" id="userSearch" placeholder="Rechercher nom, email..." oninput="filterUsers()">
    <select id="statusFilter" onchange="filterUsers()">
        <option value="">Tous les statuts</option>
        <option value="valide">Validé</option>
        <option value="en_attente">En attente</option>
        <option value="bloque">Bloqué</option>
        <option value="refuse">Refusé</option>
    </select>
    <select id="roleFilter" onchange="filterUsers()">
        <option value="">Tous les rôles</option>
        <option value="admin">Admin</option>
        <option value="user">Utilisateur</option>
    </select>
    <button class="btn btn-gray" onclick="resetFilters()">&#8635; Réinitialiser</button>
</div>

<div class="table-card">
    <div class="toolbar">
        <div>
            <span class="toolbar-title">Liste des comptes</span>
            <span class="count-badge" id="userCount">{{ $users->count() }} utilisateurs</span>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button class="btn btn-gray" onclick="window.location.reload()">&#8635; Actualiser</button>
            <a href="/rapports/export?type=users&format=csv" class="btn btn-gray" style="text-decoration:none">&#8595; CSV</a>
        </div>
    </div>
    <div style="overflow-x:auto">
    <table id="usersTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Utilisateur</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Pays</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Inscription</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $u)
        <tr id="urow_{{ $u->id }}" data-status="{{ $u->validation_status }}" data-role="{{ $u->role ?? 'user' }}" data-search="{{ strtolower(($u->nom ?? '').' '.($u->prenom ?? '').' '.($u->email ?? '')) }}">
            <td style="color:#555;font-size:11px">#{{ $u->id }}</td>
            <td>
                <div class="user-cell">
                    <div class="avatar">{{ strtoupper(substr($u->prenom ?? $u->nom ?? '?', 0, 1)) }}</div>
                    <div>
                        <div class="user-name">{{ $u->prenom ?? '' }} {{ $u->nom ?? '—' }}</div>
                        <div class="user-date">{{ \Carbon\Carbon::parse($u->created_at)->format('d/m/Y') }}</div>
                    </div>
                </div>
            </td>
            <td style="color:var(--blue);font-size:12px">{{ $u->email }}</td>
            <td>{{ $u->telephone ?? '—' }}</td>
            <td>{{ $u->pays ?? '—' }}</td>
            <td><span style="color:{{ ($u->role ?? '') === 'admin' ? 'var(--warn)' : '#aaa' }};font-size:11px">{{ $u->role ?? 'user' }}</span></td>
            <td><span class="badge badge-{{ $u->validation_status }}" id="badge_{{ $u->id }}">{{ str_replace('_',' ',$u->validation_status) }}</span></td>
            <td style="color:#555;font-size:11px;white-space:nowrap">{{ \Carbon\Carbon::parse($u->created_at)->format('d/m/Y H:i') }}</td>
            <td class="actions-cell" id="actions_{{ $u->id }}">
                <button class="btn btn-green" title="Valider" onclick="changeStatut(this,{{ $u->id }},'valide')" {{ $u->validation_status === 'valide' ? 'disabled' : '' }}><i class="fa-solid fa-check"></i> Valider</button>
                <button class="btn btn-warn"  title="Refuser"  onclick="changeStatut(this,{{ $u->id }},'refuse')" {{ $u->validation_status === 'refuse' ? 'disabled' : '' }}><i class="fa-solid fa-xmark"></i> Refuser</button>
                <button class="btn btn-red"   title="Bloquer"  onclick="changeStatut(this,{{ $u->id }},'bloque')" {{ $u->validation_status === 'bloque' ? 'disabled' : '' }}><i class="fa-solid fa-ban"></i> Bloquer</button>
                @if($u->validation_status === 'bloque')
                <button class="btn btn-blue" title="Débloquer" onclick="changeStatut(this,{{ $u->id }},'en_attente')">↩ Débloquer</button>
                @else
                <button class="btn btn-gray" title="Attente"   onclick="changeStatut(this,{{ $u->id }},'en_attente')" {{ $u->validation_status === 'en_attente' ? 'disabled' : '' }}><i class="fa-solid fa-clock"></i> Attente</button>
                @endif
                <button class="btn btn-gray" title="Supprimer" onclick="supprimerUser(this,{{ $u->id }})"><i class="fa-solid fa-trash"></i></button>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="no-data">Aucun utilisateur enregistré.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>

<script>
function changeStatut(btn, userId, status) {
    var needsConfirm = status === 'refuse' || status === 'bloque';
    if (needsConfirm) {
        var cfgs = {
            refuse: {title:'Refuser cet utilisateur ?', msg:'L\'utilisateur sera refusé et ne pourra plus accéder à la plateforme de surveillance.', type:'warning', icon:'<i class="fa-solid fa-xmark"></i>', confirmText:'Refuser'},
            bloque: {title:'Bloquer cet utilisateur ?', msg:'L\'accès de cet utilisateur sera immédiatement et définitivement bloqué.', type:'danger', icon:'<i class="fa-solid fa-ban"></i>', confirmText:'Bloquer'}
        };
        var cfg = cfgs[status];
        confirmDlg(cfg.title, cfg.msg, {type:cfg.type, icon:cfg.icon, confirmText:cfg.confirmText}).then(function(ok) {
            if (ok) _doChangeStatut(btn, userId, status);
        });
        return;
    }
    _doChangeStatut(btn, userId, status);
}

function _doChangeStatut(btn, userId, status) {
    btnLoad(btn);
    csrfFetch('/user/'+userId+'/statut', {method:'POST', body:JSON.stringify({status:status})})
        .then(function(r){return r.json();})
        .then(function(d) {
            btnLoad(btn, false);
            if (d.success) {
                notify(d.message, 's');
                var row = document.getElementById('urow_'+userId);
                if (row) {
                    row.dataset.status = status;
                    var badge = document.getElementById('badge_'+userId);
                    if (badge) {
                        badge.className = 'badge badge-'+status;
                        badge.textContent = status.replace('_',' ');
                    }
                    var btns = row.querySelectorAll('.actions-cell .btn');
                    btns.forEach(function(b) {
                        var st = null;
                        if (b.textContent.includes('Valider'))   st = 'valide';
                        if (b.textContent.includes('Refuser'))   st = 'refuse';
                        if (b.textContent.includes('Bloquer'))   st = 'bloque';
                        if (b.textContent.includes('Attente') || b.textContent.includes('Débloquer')) st = 'en_attente';
                        if (st) b.disabled = (st === status);
                    });
                }
            } else {
                notify(d.error || 'Erreur lors de la mise à jour.', 'e');
            }
        })
        .catch(function() { btnLoad(btn, false); notify('Erreur réseau.','e'); });
}

function supprimerUser(btn, userId) {
    confirmDlg('Supprimer cet utilisateur ?', 'Cet utilisateur perdra définitivement l\'accès à la plateforme. Cette action est irréversible.',{type:'danger',icon:'<i class="fa-solid fa-user"></i>',confirmText:'Supprimer l\'utilisateur'}).then(function(ok) {
        if (!ok) return;
        btnLoad(btn);
        csrfFetch('/user/'+userId, {method:'DELETE'})
            .then(function(r){return r.json();})
            .then(function(d) {
                if (d.success) {
                    notify(d.message || 'Utilisateur supprimé.', 's');
                    var row = document.getElementById('urow_'+userId);
                    if (row) { row.style.opacity='0'; row.style.transition='opacity .3s'; setTimeout(function(){row.remove();},300); }
                } else {
                    btnLoad(btn, false);
                    notify(d.error||'Erreur.','e');
                }
            })
            .catch(function(){ btnLoad(btn, false); notify('Erreur réseau.','e'); });
    });
}

function filterUsers() {
    var q      = document.getElementById('userSearch').value.toLowerCase();
    var status = document.getElementById('statusFilter').value;
    var role   = document.getElementById('roleFilter').value;
    var rows   = document.querySelectorAll('#usersTable tbody tr[id^="urow_"]');
    var visible = 0;
    rows.forEach(function(r) {
        var show = (!q || r.dataset.search.includes(q))
                && (!status || r.dataset.status === status)
                && (!role   || r.dataset.role   === role);
        r.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('userCount').textContent = visible+' utilisateur(s)';
}

function resetFilters() {
    document.getElementById('userSearch').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('roleFilter').value = '';
    filterUsers();
}

function ouvrirModalCreer() {
    document.getElementById('modalCreer').style.display = 'flex';
    document.getElementById('creerMsg').style.display = 'none';
    document.getElementById('c_prenom').value = '';
    document.getElementById('c_nom').value = '';
    document.getElementById('c_email').value = '';
    document.getElementById('c_role').value = 'utilisateur';
}
function fermerModalCreer() {
    document.getElementById('modalCreer').style.display = 'none';
}
function creerUtilisateur() {
    var btn   = document.getElementById('c_btn');
    var msg   = document.getElementById('creerMsg');
    var email = document.getElementById('c_email').value.trim();
    var nom   = document.getElementById('c_nom').value.trim();
    var prenom= document.getElementById('c_prenom').value.trim();
    var role  = document.getElementById('c_role').value;
    if (!email || !nom || !prenom) { showCreerMsg('Tous les champs sont obligatoires.','e'); return; }
    btnLoad(btn, true);
    csrfFetch('/user/creer', {method:'POST', body:JSON.stringify({email:email,nom:nom,prenom:prenom,role:role})})
        .then(function(r){return r.json();})
        .then(function(d) {
            btnLoad(btn, false);
            if (d.success) {
                showCreerMsg(d.message, d.warn ? 'w' : 's');
                setTimeout(function(){ fermerModalCreer(); window.location.reload(); }, 2200);
            } else {
                showCreerMsg(d.error || 'Erreur.', 'e');
            }
        })
        .catch(function(){ btnLoad(btn, false); showCreerMsg('Erreur réseau.','e'); });
}
function showCreerMsg(txt, type) {
    var el = document.getElementById('creerMsg');
    var colors = {s:'rgba(51,255,136,.12)',e:'rgba(255,87,51,.12)',w:'rgba(255,214,51,.12)'};
    var borders = {s:'rgba(51,255,136,.4)',e:'rgba(255,87,51,.4)',w:'rgba(255,214,51,.4)'};
    var txtc = {s:'#33ff88',e:'#ff5733',w:'#ffd633'};
    el.style.background = colors[type]||colors.e;
    el.style.border = '1px solid '+(borders[type]||borders.e);
    el.style.color = txtc[type]||txtc.e;
    el.textContent = txt;
    el.style.display = 'block';
}
document.getElementById('modalCreer').addEventListener('click', function(e){ if(e.target===this) fermerModalCreer(); });
</script>
@endsection
