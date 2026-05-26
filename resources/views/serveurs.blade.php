@extends('layouts.app')

@section('content')
<style>
:root{--neon:#33ff88;--blue:#33b5ff;--warn:#ffd633;--danger:#ff5733;--bg:#060d1f;--card:#0e1a38;--border:#1e2f5a;}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.pg-title{font-size:22px;font-weight:700;color:var(--blue)}
.pg-title span{color:#fff;font-size:14px;font-weight:400;margin-left:8px;opacity:.7}
.btn{padding:10px 20px;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px;transition:.2s}
.btn-neon{background:transparent;border:1px solid var(--neon);color:var(--neon)}
.btn-neon:hover{background:var(--neon);color:#000}
.btn-danger{background:transparent;border:1px solid var(--danger);color:var(--danger)}
.btn-danger:hover{background:var(--danger);color:#fff}
.btn-blue{background:transparent;border:1px solid var(--blue);color:var(--blue)}
.btn-blue:hover{background:var(--blue);color:#000}

.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px}
.stat-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:18px 20px;text-align:center}
.stat-card .val{font-size:34px;font-weight:800;margin-bottom:4px}
.stat-card .lbl{font-size:12px;color:#aaa;text-transform:uppercase;letter-spacing:.5px}
.stat-card.green .val{color:var(--neon)}
.stat-card.blue  .val{color:var(--blue)}
.stat-card.warn  .val{color:var(--warn)}
.stat-card.red   .val{color:var(--danger)}

.alert{padding:12px 16px;border-radius:8px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:8px}
.alert-success{background:rgba(51,255,136,.1);border:1px solid var(--neon);color:var(--neon)}

.table-card{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:28px}
.table-toolbar{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:10px}
.table-toolbar .title{font-size:15px;font-weight:700;color:#fff}
.search-box{background:#07102a;border:1px solid var(--border);border-radius:8px;padding:8px 14px;color:#fff;font-size:13px;outline:none;width:240px}
.search-box:focus{border-color:var(--blue)}
table{width:100%;border-collapse:collapse}
thead tr{background:#07102a}
th{padding:12px 16px;text-align:left;font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
td{padding:12px 16px;border-top:1px solid var(--border);font-size:13px;color:#ccc}
tr:hover td{background:rgba(51,181,255,.04)}
.td-name{color:#fff;font-weight:600}
.td-ip{font-family:monospace;color:var(--blue);font-size:12px}
.badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase}
.badge-en_ligne{background:rgba(51,255,136,.12);color:var(--neon);border:1px solid rgba(51,255,136,.3)}
.badge-hors_ligne{background:rgba(255,87,51,.12);color:var(--danger);border:1px solid rgba(255,87,51,.3)}
.badge-maintenance{background:rgba(255,214,51,.12);color:var(--warn);border:1px solid rgba(255,214,51,.3)}
.td-actions{display:flex;gap:6px;white-space:nowrap}
.td-actions .btn{padding:5px 12px;font-size:11px}
.empty-row td{text-align:center;padding:40px;color:#555}

/* modal */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:1000;align-items:center;justify-content:center}
.modal-overlay.open{display:flex}
.modal{background:#0b1632;border:1px solid var(--border);border-radius:16px;padding:28px;width:min(680px,95vw);max-height:90vh;overflow-y:auto}
.modal h3{font-size:16px;font-weight:700;color:var(--neon);margin-bottom:20px}
.modal-close{float:right;background:none;border:none;color:#aaa;font-size:20px;cursor:pointer;margin-top:-4px}
.modal-close:hover{color:#fff}
.form-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.form-group{display:flex;flex-direction:column;gap:6px}
.form-group label{font-size:12px;color:#aaa;font-weight:600;text-transform:uppercase;letter-spacing:.4px}
.form-group input,.form-group select,.form-group textarea{background:#07102a;border:1px solid var(--border);border-radius:8px;padding:10px 12px;color:#fff;font-size:13px;outline:none;transition:.2s}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--neon);box-shadow:0 0 0 2px rgba(51,255,136,.1)}
.form-group select option{background:#0e1a38}
.form-group textarea{resize:vertical;min-height:72px}
.full-span{grid-column:1/-1}
.span2{grid-column:span 2}
.form-actions{display:flex;gap:10px;margin-top:18px;justify-content:flex-end}

@media(max-width:768px){
.stats-row{grid-template-columns:repeat(2,1fr)}
.form-grid{grid-template-columns:1fr}
table{display:block;overflow-x:auto}
}
</style>

<div class="pg-header">
    <div>
        <div class="pg-title">Serveurs <span>Gestion de l'infrastructure</span></div>
    </div>
    <button class="btn btn-neon" onclick="document.getElementById('addModal').classList.add('open')">+ Ajouter un serveur</button>
</div>

@if(session('success_srv'))
<div class="alert alert-success">&#10003; {{ session('success_srv') }}</div>
@endif

<!-- Stats -->
<div class="stats-row">
    <div class="stat-card blue"><div class="val">{{ $stats['total'] }}</div><div class="lbl">Total</div></div>
    <div class="stat-card green"><div class="val">{{ $stats['en_ligne'] }}</div><div class="lbl">En ligne</div></div>
    <div class="stat-card red"><div class="val">{{ $stats['hors_ligne'] }}</div><div class="lbl">Hors ligne</div></div>
    <div class="stat-card warn"><div class="val">{{ $stats['maintenance'] }}</div><div class="lbl">Maintenance</div></div>
</div>

<!-- Table -->
<div class="table-card">
    <div class="table-toolbar">
        <div class="title">Liste des serveurs</div>
        <input type="text" class="search-box" id="searchInput" placeholder="Rechercher..." oninput="filterTable()">
    </div>
    <table id="serveursTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Type</th>
                <th>Adresse IP</th>
                <th>Salle</th>
                <th>OS</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($serveurs as $srv)
        <tr>
            <td style="color:#555;font-size:11px">#{{ $srv->id }}</td>
            <td class="td-name">
                {{ $srv->nom }}
                @if($srv->nom_domaine)<div style="font-size:11px;color:#666;margin-top:2px">{{ $srv->nom_domaine }}</div>@endif
            </td>
            <td><span style="color:var(--blue);font-size:12px">{{ $srv->type }}</span></td>
            <td class="td-ip">{{ $srv->adresse_ip ?? '—' }}</td>
            <td>
                @php $salle = $salles->firstWhere('id', $srv->salle_id) @endphp
                {{ $salle ? $salle->nom : '—' }}
            </td>
            <td style="font-size:12px">{{ $srv->os ?? '—' }}</td>
            <td><span class="badge badge-{{ $srv->statut }}">{{ str_replace('_',' ',$srv->statut) }}</span></td>
            <td class="td-actions">
                <button class="btn" style="background:transparent;border:1px solid #33ff8866;color:#33ff8899;padding:5px 10px;font-size:11px;border-radius:7px" title="Ping {{ $srv->adresse_ip ?? 'aucune IP' }}" onclick="pingServer(this,{{ $srv->id }})">Ping</button>
                <button class="btn btn-blue" onclick="openEdit({{ $srv->id }},
                    '{{ addslashes($srv->nom) }}',
                    '{{ addslashes($srv->type) }}',
                    '{{ addslashes($srv->adresse_ip ?? '') }}',
                    '{{ addslashes($srv->nom_domaine ?? '') }}',
                    '{{ addslashes($srv->localisation ?? '') }}',
                    '{{ $srv->salle_id ?? '' }}',
                    '{{ addslashes($srv->responsable ?? '') }}',
                    '{{ addslashes($srv->os ?? '') }}',
                    '{{ addslashes($srv->ram ?? '') }}',
                    '{{ addslashes($srv->cpu ?? '') }}',
                    '{{ addslashes($srv->stockage ?? '') }}',
                    '{{ $srv->statut }}',
                    '{{ $srv->date_installation ?? '' }}',
                    '{{ addslashes($srv->notes ?? '') }}'
                )">Modifier</button>
                <form method="POST" action="/serveurs/{{ $srv->id }}" id="del-srv-{{ $srv->id }}" style="margin:0">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-danger" onclick="delServeur(this,{{ $srv->id }})">Supprimer</button>
                </form>
            </td>
        </tr>
        @empty
        <tr class="empty-row"><td colspan="8">Aucun serveur enregistré. Cliquez sur <strong>+ Ajouter un serveur</strong> pour commencer.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div class="modal-overlay" id="addModal" onclick="if(event.target===this)this.classList.remove('open')">
<div class="modal">
    <h3>&#10010; Nouveau Serveur <button class="modal-close" onclick="document.getElementById('addModal').classList.remove('open')">&#215;</button></h3>
    <form method="POST" action="/serveurs">
        @csrf
        <div class="form-grid">
            <div class="form-group span2">
                <label>Nom du serveur *</label>
                <input type="text" name="nom" required placeholder="SRV-PROD-01">
            </div>
            <div class="form-group">
                <label>Type *</label>
                <select name="type" required>
                    <option value="">-- Choisir --</option>
                    <optgroup label="Web & App">
                        <option>Serveur Web Apache</option>
                        <option>Serveur Web Nginx</option>
                        <option>Serveur d'Application</option>
                        <option>Serveur de Cache (Redis)</option>
                        <option>Serveur de Cache (Memcached)</option>
                        <option>Serveur d'API REST</option>
                        <option>Serveur GraphQL</option>
                        <option>Serveur Proxy Inverse</option>
                        <option>Serveur Load Balancer</option>
                    </optgroup>
                    <optgroup label="Base de données">
                        <option>Serveur BD MySQL</option>
                        <option>Serveur BD PostgreSQL</option>
                        <option>Serveur BD MariaDB</option>
                        <option>Serveur BD MongoDB</option>
                        <option>Serveur BD Oracle</option>
                        <option>Serveur BD SQL Server</option>
                        <option>Serveur BD Redis</option>
                        <option>Serveur BD Elasticsearch</option>
                    </optgroup>
                    <optgroup label="Infrastructure">
                        <option>Serveur DNS</option>
                        <option>Serveur DHCP</option>
                        <option>Serveur NTP</option>
                        <option>Serveur LDAP / Active Directory</option>
                        <option>Serveur VPN</option>
                        <option>Serveur Firewall</option>
                        <option>Serveur Backup</option>
                        <option>Serveur NAS / SAN</option>
                        <option>Serveur FTP / SFTP</option>
                    </optgroup>
                    <optgroup label="Communication">
                        <option>Serveur Mail (SMTP/IMAP)</option>
                        <option>Serveur de Messagerie</option>
                        <option>Serveur VoIP</option>
                        <option>Serveur XMPP</option>
                    </optgroup>
                    <optgroup label="Surveillance & DevOps">
                        <option>Serveur de Monitoring</option>
                        <option>Serveur CI/CD</option>
                        <option>Serveur de Logs</option>
                        <option>Serveur de Métriques</option>
                        <option>Serveur Git</option>
                    </optgroup>
                    <optgroup label="Cloud & Virtualisation">
                        <option>Hyperviseur (VMware)</option>
                        <option>Hyperviseur (Proxmox)</option>
                        <option>Conteneur Docker</option>
                        <option>Cluster Kubernetes</option>
                    </optgroup>
                    <option>Autre</option>
                </select>
            </div>
            <div class="form-group">
                <label>Adresse IP</label>
                <input type="text" name="adresse_ip" placeholder="192.168.1.10">
            </div>
            <div class="form-group">
                <label>Nom de domaine</label>
                <input type="text" name="nom_domaine" placeholder="srv01.example.com">
            </div>
            <div class="form-group">
                <label>Salle</label>
                <select name="salle_id">
                    <option value="">-- Aucune --</option>
                    @foreach($salles as $salle)
                    <option value="{{ $salle->id }}">{{ $salle->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Responsable</label>
                <input type="text" name="responsable" placeholder="Nom du responsable">
            </div>
            <div class="form-group">
                <label>Localisation</label>
                <input type="text" name="localisation" placeholder="Rack A, Baie 3">
            </div>
            <div class="form-group">
                <label>Système d'exploitation</label>
                <input type="text" name="os" placeholder="Ubuntu 22.04 LTS">
            </div>
            <div class="form-group">
                <label>RAM</label>
                <input type="text" name="ram" placeholder="32 Go DDR4">
            </div>
            <div class="form-group">
                <label>CPU</label>
                <input type="text" name="cpu" placeholder="Intel Xeon E5-2690">
            </div>
            <div class="form-group">
                <label>Stockage</label>
                <input type="text" name="stockage" placeholder="2 To SSD NVMe">
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select name="statut">
                    <option value="en_ligne">En ligne</option>
                    <option value="hors_ligne">Hors ligne</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="form-group">
                <label>Date d'installation</label>
                <input type="date" name="date_installation">
            </div>
            <div class="form-group full-span">
                <label>Notes</label>
                <textarea name="notes" placeholder="Informations supplémentaires..."></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" class="btn btn-danger" onclick="document.getElementById('addModal').classList.remove('open')">Annuler</button>
            <button type="submit" class="btn btn-neon">Enregistrer</button>
        </div>
    </form>
</div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal" onclick="if(event.target===this)this.classList.remove('open')">
<div class="modal">
    <h3>&#9998; Modifier le Serveur <button class="modal-close" onclick="document.getElementById('editModal').classList.remove('open')">&#215;</button></h3>
    <form method="POST" id="editForm" action="#">
        @csrf
        <div class="form-grid">
            <div class="form-group span2">
                <label>Nom du serveur *</label>
                <input type="text" name="nom" id="e_nom" required>
            </div>
            <div class="form-group">
                <label>Type *</label>
                <input type="text" name="type" id="e_type" required>
            </div>
            <div class="form-group">
                <label>Adresse IP</label>
                <input type="text" name="adresse_ip" id="e_ip">
            </div>
            <div class="form-group">
                <label>Nom de domaine</label>
                <input type="text" name="nom_domaine" id="e_domaine">
            </div>
            <div class="form-group">
                <label>Salle</label>
                <select name="salle_id" id="e_salle">
                    <option value="">-- Aucune --</option>
                    @foreach($salles as $salle)
                    <option value="{{ $salle->id }}">{{ $salle->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Responsable</label>
                <input type="text" name="responsable" id="e_resp">
            </div>
            <div class="form-group">
                <label>Localisation</label>
                <input type="text" name="localisation" id="e_loc">
            </div>
            <div class="form-group">
                <label>OS</label>
                <input type="text" name="os" id="e_os">
            </div>
            <div class="form-group">
                <label>RAM</label>
                <input type="text" name="ram" id="e_ram">
            </div>
            <div class="form-group">
                <label>CPU</label>
                <input type="text" name="cpu" id="e_cpu">
            </div>
            <div class="form-group">
                <label>Stockage</label>
                <input type="text" name="stockage" id="e_stockage">
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select name="statut" id="e_statut">
                    <option value="en_ligne">En ligne</option>
                    <option value="hors_ligne">Hors ligne</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="form-group">
                <label>Date d'installation</label>
                <input type="date" name="date_installation" id="e_date">
            </div>
            <div class="form-group full-span">
                <label>Notes</label>
                <textarea name="notes" id="e_notes"></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" class="btn btn-danger" onclick="document.getElementById('editModal').classList.remove('open')">Annuler</button>
            <button type="submit" class="btn btn-neon">Mettre à jour</button>
        </div>
    </form>
</div>
</div>

<script>
function delServeur(btn, id) {
    confirmDlg('Supprimer ce serveur ?','Ce serveur sera définitivement supprimé de l\'inventaire. Toutes ses données associées seront perdues. Cette action est irréversible.',{type:'danger',icon:'🖥️',confirmText:'Supprimer le serveur'}).then(function(ok) {
        if (ok) { btnLoad(btn); document.getElementById('del-srv-'+id).submit(); }
    });
}

function pingServer(btn, id) {
    var orig = btn.innerHTML;
    btn.innerHTML = '<span class="_spin-ico" style="width:11px;height:11px;border-width:1.5px"></span>';
    btn.disabled = true;
    fetch('/serveur/'+id+'/ping', {headers:{'Accept':'application/json'}})
        .then(function(r){return r.json();})
        .then(function(d) {
            btn.innerHTML = orig;
            btn.disabled = false;
            if (d.reachable) {
                btn.style.borderColor='#33ff88'; btn.style.color='#33ff88';
                notify('✓ '+d.msg, 's', 4000);
            } else {
                btn.style.borderColor='#ff5733'; btn.style.color='#ff5733';
                notify('✗ '+d.msg, 'e', 4000);
            }
            setTimeout(function(){btn.style.borderColor='';btn.style.color='';}, 6000);
        })
        .catch(function() {
            btn.innerHTML = orig; btn.disabled = false;
            notify('Erreur de connexion au serveur.', 'e');
        });
}

function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#serveursTable tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}

function setVal(id, val) { const el = document.getElementById(id); if (el) el.value = val; }
function setSel(id, val) {
    const sel = document.getElementById(id);
    if (!sel) return;
    for (let i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value == val) { sel.selectedIndex = i; break; }
    }
}

function openEdit(id, nom, type, ip, domaine, loc, salle_id, resp, os, ram, cpu, stockage, statut, date, notes) {
    document.getElementById('editForm').action = '/serveurs/' + id;
    setVal('e_nom', nom); setVal('e_type', type); setVal('e_ip', ip);
    setVal('e_domaine', domaine); setVal('e_loc', loc);
    setVal('e_resp', resp); setVal('e_os', os); setVal('e_ram', ram);
    setVal('e_cpu', cpu); setVal('e_stockage', stockage);
    setVal('e_date', date); setVal('e_notes', notes);
    setSel('e_salle', salle_id); setSel('e_statut', statut);
    document.getElementById('editModal').classList.add('open');
}
</script>
@endsection
