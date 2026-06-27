@extends('layouts.app')
@section('content')
<style>
:root{--neon:#33ff88;--blue:#33b5ff;--warn:#ffd633;--danger:#ff5733;--card:#0e1a38;--border:#1e2f5a}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-title{font-size:22px;font-weight:700;color:#fff}
.btn{padding:8px 16px;border:none;border-radius:7px;font-weight:700;cursor:pointer;font-size:12px;transition:.18s;display:inline-flex;align-items:center;gap:5px}
.btn-green{background:transparent;border:1px solid var(--neon);color:var(--neon)}
.btn-green:hover{background:var(--neon);color:#000}
.search{background:#07102a;border:1px solid var(--border);border-radius:8px;padding:9px 14px;color:#fff;font-size:13px;outline:none;width:260px}
.search:focus{border-color:var(--blue)}
.table-wrap{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden}
table{width:100%;border-collapse:collapse}
thead tr{background:#07102a}
th{padding:11px 16px;text-align:left;font-size:10px;color:#556;text-transform:uppercase;letter-spacing:.5px}
td{padding:11px 16px;border-top:1px solid var(--border);font-size:13px;color:#ccc;vertical-align:middle}
tr:hover td{background:rgba(51,181,255,.03)}
.avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#1e2f5a,#0b1632);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--blue);flex-shrink:0}
.user-info{display:flex;align-items:center;gap:10px}
.user-name{font-weight:600;color:#fff}
.user-email{font-size:11px;color:#556;margin-top:1px}
.role-sel{background:#07102a;border:1px solid var(--border);border-radius:6px;padding:5px 8px;color:#fff;font-size:12px;outline:none;cursor:pointer;transition:.2s}
.role-sel:focus{border-color:var(--blue)}
.toggle{position:relative;display:inline-block;width:44px;height:24px;flex-shrink:0}
.toggle input{opacity:0;width:0;height:0}
.slider{position:absolute;inset:0;background:#1e2f5a;border-radius:24px;cursor:pointer;transition:.3s}
.slider:before{content:'';position:absolute;width:18px;height:18px;left:3px;bottom:3px;background:#556;border-radius:50%;transition:.3s}
input:checked+.slider{background:rgba(51,255,136,.25);border:1px solid var(--neon)}
input:checked+.slider:before{transform:translateX(20px);background:var(--neon)}
.del-btn{background:none;border:none;color:#3a4a6a;cursor:pointer;font-size:14px;padding:4px 8px;border-radius:6px;transition:.2s}
.del-btn:hover{color:var(--danger);background:rgba(255,87,51,.1)}
.empty{text-align:center;padding:48px;color:#3a4a6a;font-size:13px}
.count{font-size:12px;color:#556;padding:10px 16px;border-bottom:1px solid var(--border)}

@media(max-width:640px){
  .user-email,.col-role,.col-date{display:none}
  .search{width:100%}
}
</style>

@php
try {
    $users = DB::table('users')->orderByDesc('created_at')->get();
} catch(\Exception $e) {
    $users = collect();
}
@endphp

<div class="pg-header">
    <div class="pg-title"><i class="fa-solid fa-users" style="color:var(--blue);margin-right:10px"></i>Utilisateurs</div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <input class="search" type="text" id="search" placeholder="Rechercher…" oninput="filtrer()">
        <button class="btn btn-green" onclick="ouvrirModal()"><i class="fa-solid fa-user-plus"></i> Ajouter</button>
    </div>
</div>

<div class="table-wrap">
    <div class="count" id="compteur">{{ $users->count() }} utilisateur(s)</div>
    <table>
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th class="col-role">Rôle</th>
                <th>Actif</th>
                <th class="col-date">Inscription</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="tbody">
        @forelse($users as $u)
        @php $actif = ($u->validation_status ?? '') === 'valide'; @endphp
        <tr id="row_{{ $u->id }}" data-search="{{ strtolower(($u->prenom ?? '').' '.($u->nom ?? '').' '.($u->email ?? '')) }}">
            <td>
                <div class="user-info">
                    <div class="avatar">{{ strtoupper(substr($u->prenom ?? $u->nom ?? '?', 0, 1)) }}</div>
                    <div>
                        <div class="user-name">{{ $u->prenom ?? '' }} {{ $u->nom ?? '—' }}</div>
                        <div class="user-email">{{ $u->email }}</div>
                    </div>
                </div>
            </td>
            <td class="col-role">
                <select class="role-sel" onchange="changerRole({{ $u->id }}, this.value)" {{ ($u->id == 1 || ($u->role ?? '') === 'superadmin') ? 'disabled' : '' }}>
                    <option value="utilisateur" {{ ($u->role ?? '') === 'utilisateur' ? 'selected' : '' }}>Utilisateur</option>
                    <option value="technicien"  {{ ($u->role ?? '') === 'technicien'  ? 'selected' : '' }}>Technicien</option>
                    <option value="admin"       {{ ($u->role ?? '') === 'admin'       ? 'selected' : '' }}>Administrateur</option>
                    <option value="invite"      {{ ($u->role ?? '') === 'invite'      ? 'selected' : '' }}>Invité</option>
                </select>
            </td>
            <td>
                <label class="toggle">
                    <input type="checkbox" {{ $actif ? 'checked' : '' }} onchange="toggleActif({{ $u->id }}, this.checked)" {{ ($u->id == 1 || ($u->role ?? '') === 'superadmin') ? 'disabled' : '' }}>
                    <span class="slider"></span>
                </label>
            </td>
            <td class="col-date" style="font-size:11px;color:#556">{{ \Carbon\Carbon::parse($u->created_at)->format('d/m/Y') }}</td>
            <td>
                @if($u->id != 1 && ($u->role ?? '') !== 'superadmin')
                <button class="del-btn" onclick="supprimer({{ $u->id }}, '{{ addslashes(($u->prenom ?? '').' '.($u->nom ?? '')) }}')" title="Supprimer"><i class="fa-solid fa-trash-can"></i></button>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="empty">Aucun utilisateur enregistré.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Modal ajouter --}}
<div id="modal" style="display:none;position:fixed;inset:0;background:rgba(2,5,18,.88);backdrop-filter:blur(8px);z-index:10000;align-items:center;justify-content:center">
  <div style="background:#0a1428;border:1px solid var(--border);border-radius:16px;padding:28px;max-width:400px;width:94%">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <div style="font-size:15px;font-weight:800;color:#fff"><i class="fa-solid fa-user-plus" style="color:var(--neon);margin-right:8px"></i>Nouvel utilisateur</div>
      <button onclick="fermerModal()" style="background:none;border:none;color:#556;font-size:20px;cursor:pointer">×</button>
    </div>
    <div id="modalMsg" style="display:none;margin-bottom:14px;padding:10px 14px;border-radius:8px;font-size:13px;font-weight:600"></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px">
      <div>
        <label style="font-size:11px;color:#8899cc;display:block;margin-bottom:4px">Prénom</label>
        <input id="f_prenom" type="text" placeholder="Prénom" style="width:100%;background:#07102a;border:1px solid var(--border);border-radius:7px;padding:9px 11px;color:#fff;font-size:13px;outline:none">
      </div>
      <div>
        <label style="font-size:11px;color:#8899cc;display:block;margin-bottom:4px">Nom</label>
        <input id="f_nom" type="text" placeholder="Nom" style="width:100%;background:#07102a;border:1px solid var(--border);border-radius:7px;padding:9px 11px;color:#fff;font-size:13px;outline:none">
      </div>
    </div>
    <div style="margin-bottom:10px">
      <label style="font-size:11px;color:#8899cc;display:block;margin-bottom:4px">Email</label>
      <input id="f_email" type="email" placeholder="email@organisation.com" style="width:100%;background:#07102a;border:1px solid var(--border);border-radius:7px;padding:9px 11px;color:#fff;font-size:13px;outline:none">
    </div>
    <div style="margin-bottom:20px">
      <label style="font-size:11px;color:#8899cc;display:block;margin-bottom:4px">Rôle</label>
      <select id="f_role" style="width:100%;background:#07102a;border:1px solid var(--border);border-radius:7px;padding:9px 11px;color:#fff;font-size:13px;outline:none">
        <option value="utilisateur">Utilisateur</option>
        <option value="technicien">Technicien</option>
        <option value="admin">Administrateur</option>
        <option value="invite">Invité</option>
      </select>
    </div>
    <p style="font-size:11px;color:#3a4a6a;margin-bottom:18px">Un mot de passe temporaire sera envoyé par email à l'utilisateur.</p>
    <div style="display:flex;gap:10px">
      <button onclick="fermerModal()" style="flex:1;background:rgba(18,30,68,.65);border:1px solid var(--border);color:#7788aa;padding:11px;border-radius:9px;font-weight:700;cursor:pointer;font-size:13px">Annuler</button>
      <button id="f_btn" onclick="creer()" style="flex:2;background:rgba(51,255,136,.1);border:1px solid rgba(51,255,136,.35);color:var(--neon);padding:11px;border-radius:9px;font-weight:700;cursor:pointer;font-size:13px"><i class="fa-solid fa-paper-plane"></i> Créer</button>
    </div>
  </div>
</div>

<script>
function filtrer() {
    var q = document.getElementById('search').value.toLowerCase();
    var rows = document.querySelectorAll('#tbody tr[id^="row_"]');
    var n = 0;
    rows.forEach(function(r) {
        var show = !q || r.dataset.search.includes(q);
        r.style.display = show ? '' : 'none';
        if (show) n++;
    });
    document.getElementById('compteur').textContent = n + ' utilisateur(s)';
}

function changerRole(id, role) {
    csrfFetch('/user/'+id+'/modifier', {method:'POST', body:JSON.stringify({role:role})})
        .then(function(r){return r.json();})
        .then(function(d){ notify(d.success ? 'Rôle mis à jour.' : (d.error||'Erreur.'), d.success?'s':'e'); })
        .catch(function(){ notify('Erreur réseau.','e'); });
}

function toggleActif(id, actif) {
    var status = actif ? 'valide' : 'bloque';
    csrfFetch('/user/'+id+'/statut', {method:'POST', body:JSON.stringify({status:status})})
        .then(function(r){return r.json();})
        .then(function(d){ notify(d.success ? (actif?'Compte activé.':'Compte bloqué.') : (d.error||'Erreur.'), d.success?'s':'e'); })
        .catch(function(){ notify('Erreur réseau.','e'); });
}

function supprimer(id, nom) {
    confirmDlg('Supprimer '+nom.trim()+' ?', 'Cette action est irréversible.', {type:'danger', confirmText:'Supprimer'})
    .then(function(ok) {
        if (!ok) return;
        csrfFetch('/user/'+id, {method:'DELETE'})
            .then(function(r){return r.json();})
            .then(function(d) {
                if (d.success) {
                    var row = document.getElementById('row_'+id);
                    if (row) { row.style.opacity='0'; row.style.transition='.3s'; setTimeout(function(){row.remove(); filtrer();},300); }
                    notify('Utilisateur supprimé.','s');
                } else { notify(d.error||'Erreur.','e'); }
            })
            .catch(function(){ notify('Erreur réseau.','e'); });
    });
}

function ouvrirModal() {
    document.getElementById('modalMsg').style.display='none';
    document.getElementById('f_prenom').value='';
    document.getElementById('f_nom').value='';
    document.getElementById('f_email').value='';
    document.getElementById('f_role').value='utilisateur';
    document.getElementById('modal').style.display='flex';
}
function fermerModal() { document.getElementById('modal').style.display='none'; }

function creer() {
    var btn=document.getElementById('f_btn');
    var prenom=document.getElementById('f_prenom').value.trim();
    var nom=document.getElementById('f_nom').value.trim();
    var email=document.getElementById('f_email').value.trim();
    var role=document.getElementById('f_role').value;
    if (!prenom||!nom||!email){msgModal('Tous les champs sont obligatoires.','e');return;}
    btnLoad(btn,true);
    csrfFetch('/user/creer',{method:'POST',body:JSON.stringify({prenom:prenom,nom:nom,email:email,role:role})})
        .then(function(r){return r.json();})
        .then(function(d){
            btnLoad(btn,false);
            if(d.success){msgModal(d.message,'s');setTimeout(function(){fermerModal();window.location.reload();},1800);}
            else{msgModal(d.error||'Erreur.','e');}
        })
        .catch(function(){btnLoad(btn,false);msgModal('Erreur réseau.','e');});
}
function msgModal(txt,type){
    var el=document.getElementById('modalMsg');
    var bg={s:'rgba(51,255,136,.1)',e:'rgba(255,87,51,.1)',w:'rgba(255,214,51,.1)'};
    var bd={s:'rgba(51,255,136,.3)',e:'rgba(255,87,51,.3)',w:'rgba(255,214,51,.3)'};
    var cl={s:'#33ff88',e:'#ff5733',w:'#ffd633'};
    el.style.background=bg[type];el.style.border='1px solid '+bd[type];el.style.color=cl[type];
    el.textContent=txt;el.style.display='block';
}
document.getElementById('modal').addEventListener('click',function(e){if(e.target===this)fermerModal();});
</script>
@endsection
