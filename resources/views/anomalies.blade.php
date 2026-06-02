@extends('layouts.app')

@section('content')
<style>
:root{--neon:#33ff88;--blue:#33b5ff;--warn:#ffd633;--danger:#ff5733;--card:#0e1a38;--border:#1e2f5a}
.pg-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;flex-wrap:wrap;gap:12px}
.pg-title{font-size:22px;font-weight:700;color:var(--danger)}
.btn{padding:8px 15px;border:none;border-radius:7px;font-weight:700;cursor:pointer;font-size:12px;transition:.18s;display:inline-flex;align-items:center;gap:5px;white-space:nowrap}
.btn:active{transform:scale(.96)}
.btn-neon{background:transparent;border:1px solid var(--neon);color:var(--neon)}
.btn-neon:hover{background:var(--neon);color:#000}
.btn-red{background:transparent;border:1px solid var(--danger);color:var(--danger)}
.btn-red:hover{background:var(--danger);color:#fff}
.btn-gray{background:transparent;border:1px solid #2a3a5a;color:#666}
.btn-gray:hover{border-color:#aaa;color:#aaa}

.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px}
.scard{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:14px;text-align:center}
.scard .v{font-size:24px;font-weight:800;margin-bottom:3px}
.scard .l{font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px}
.scard.r .v{color:var(--danger)}.scard.w .v{color:var(--warn)}.scard.g .v{color:var(--neon)}.scard.b .v{color:var(--blue)}

.anomalies-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:14px;margin-bottom:22px}
.anomaly-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;transition:.2s;position:relative;overflow:hidden}
.anomaly-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px}
.anomaly-card.critique::before{background:var(--danger);box-shadow:0 0 10px var(--danger)}
.anomaly-card.warning::before{background:var(--warn);box-shadow:0 0 10px var(--warn)}
.anomaly-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.3)}
.anomaly-card.resolved{opacity:.45}
.a-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;padding-left:12px}
.a-title{font-size:14px;font-weight:700;color:#fff}
.a-time{font-size:10px;color:#555;white-space:nowrap}
.badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;text-transform:uppercase;margin-top:2px}
.badge-critique{background:rgba(255,87,51,.12);color:var(--danger);border:1px solid rgba(255,87,51,.25)}
.badge-warning{background:rgba(255,214,51,.12);color:var(--warn);border:1px solid rgba(255,214,51,.25)}
.badge-resolved{background:rgba(51,255,136,.08);color:#33ff88aa;border:1px solid rgba(51,255,136,.15)}
.a-body{padding-left:12px;margin-bottom:12px}
.a-msg{font-size:12px;color:#aaa;margin-bottom:8px}
.a-solution{font-size:11px;color:#556;background:#07102a;border-radius:7px;padding:8px 10px;border-left:3px solid var(--neon)}
.a-solution strong{color:var(--neon)}
.a-footer{display:flex;gap:7px;padding-left:12px;flex-wrap:wrap}
.empty-state{text-align:center;padding:60px 20px;color:#555}
.empty-state .icon{font-size:48px;margin-bottom:12px}

.sensors-alert{background:var(--card);border:1px solid rgba(255,87,51,.3);border-radius:14px;padding:18px;margin-bottom:20px}
.sensors-alert h3{font-size:14px;font-weight:700;color:var(--danger);margin-bottom:12px}
.sensor-rows{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:8px}
.sensor-row{background:#07102a;border-radius:8px;padding:10px 12px;border:1px solid var(--border)}
.sensor-row .sname{font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px}
.sensor-row .sval{font-size:18px;font-weight:800}
.sensor-row .sval.ok{color:var(--neon)}.sensor-row .sval.warn{color:var(--warn)}.sensor-row .sval.crit{color:var(--danger)}
.sensor-row .sunit{font-size:10px;color:#666}

@media(max-width:768px){.stats-row{grid-template-columns:repeat(2,1fr)}.anomalies-grid{grid-template-columns:1fr}}
</style>

<div class="pg-header">
    <div class="pg-title">&#9888; Anomalies &amp; Solutions</div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <button class="btn btn-neon" onclick="loadAnomalies()">&#8635; Actualiser</button>
        <button class="btn btn-gray" onclick="marquerTout()">&#10003; Tout résoudre</button>
        <a href="/rapports/export?type=alertes&format=csv&debut={{ now()->subDays(30)->toDateString() }}&fin={{ now()->toDateString() }}" class="btn btn-gray" style="text-decoration:none">&#8595; Export CSV</a>
        <button class="btn btn-gray" onclick="window.open('/rapports/print?type=alertes&debut={{ now()->subDays(30)->toDateString() }}&fin={{ now()->toDateString() }}','_blank')">&#128438; PDF</button>
    </div>
</div>

<!-- Compteurs -->
<div class="stats-row">
    <div class="scard r"><div class="v" id="stCrit">—</div><div class="l">Critiques</div></div>
    <div class="scard w"><div class="v" id="stWarn">—</div><div class="l">Warnings</div></div>
    <div class="scard g"><div class="v" id="stResolved">—</div><div class="l">Résolues</div></div>
    <div class="scard b"><div class="v" id="stTotal">—</div><div class="l">Total</div></div>
</div>

<!-- Capteurs en état anormal (temps réel) -->
<div class="sensors-alert" id="sensorAlerts" style="display:none">
    <h3>&#128308; Capteurs en état anormal (temps réel)</h3>
    <div class="sensor-rows" id="sensorRows"></div>
</div>

<!-- Grille des anomalies -->
<div id="anomaliesGrid" class="anomalies-grid">
    <div style="grid-column:1/-1;text-align:center;padding:40px;color:#33b5ff">
        Chargement des anomalies...
    </div>
</div>

<script>
var SOLUTIONS = {
    temperature: {
        warning:  'Verifier la ventilation et la climatisation. Surveiller la montee thermique.',
        critique: 'URGENT : Refroidissement urgence. Reduire la charge des serveurs.'
    },
    humidite: {
        warning:  'Verifier le systeme de climatisation. Surveiller l\'hygrometre.',
        critique: 'URGENT : Risque condensation. Activer deshumidification urgence.'
    },
    gaz: {
        warning:  'Ventiler la salle. Verifier les equipements suspects.',
        critique: 'URGENT : Evacuation possible. Verifier les detecteurs CO2. Appeler secours.'
    },
    pir: {
        warning:  'Verifier les acces physiques. Consulter les cameras. Consigner l\'incident.',
        critique: 'URGENT : Intrusion possible. Prevenir la securite immediatement.'
    }
};
var ICONS = { temperature:'🌡️', humidite:'💧', gaz:'☁️', pir:'🚨' };

function loadAnomalies() {
    var grid = document.getElementById('anomaliesGrid');
    if (!grid) return;

    // Charger seuils + alertes en parallèle
    var pSeuils  = fetch('/api/seuils').then(function(r){ return r.ok ? r.json() : {}; }).catch(function(){ return {}; });
    var pAlerts  = fetch('/api/alertes-recentes?limit=200').then(function(r){ return r.ok ? r.json() : []; }).catch(function(){ return []; });
    var pLive    = fetch('/api/dashboard-data').then(function(r){ return r.ok ? r.json() : {}; }).catch(function(){ return {}; });

    Promise.all([pSeuils, pAlerts, pLive]).then(function(results) {
        var seuils  = results[0] || {};
        var alertes = Array.isArray(results[1]) ? results[1] : [];
        var live    = results[2] || {};

        // Compteurs
        var crit     = 0, warn = 0, resolved = 0;
        for (var i = 0; i < alertes.length; i++) {
            if (alertes[i].niveau === 'critique') crit++;
            if (alertes[i].niveau === 'warning')  warn++;
            if (alertes[i].resolu == 1)            resolved++;
        }
        var elCrit = document.getElementById('stCrit');
        var elWarn = document.getElementById('stWarn');
        var elRes  = document.getElementById('stResolved');
        var elTot  = document.getElementById('stTotal');
        if (elCrit) elCrit.textContent = crit;
        if (elWarn) elWarn.textContent = warn;
        if (elRes)  elRes.textContent  = resolved;
        if (elTot)  elTot.textContent  = alertes.length;

        // Capteurs en temps réel
        var capteurNames = ['temperature','humidite','gaz'];
        var liveAlerts = [];
        for (var j = 0; j < capteurNames.length; j++) {
            var k = capteurNames[j];
            var val = parseFloat(live[k]);
            if (isNaN(val)) continue;
            var s = seuils[k];
            if (!s) continue;
            var cls = val >= s.critique ? 'crit' : (val >= s.warning ? 'warn' : 'ok');
            if (cls !== 'ok') liveAlerts.push({ k:k, v:val, cls:cls });
        }
        if (live.pir || live.pir_detecte) liveAlerts.push({ k:'pir', v:'Detecte', cls:'crit' });
        var box  = document.getElementById('sensorAlerts');
        var rows = document.getElementById('sensorRows');
        if (box && rows) {
            if (liveAlerts.length) {
                box.style.display = '';
                var liveHtml = '';
                var liveLabels = {temperature:'Temperature',humidite:'Humidite',gaz:'Qualite air',pir:'Intrusion PIR'};
                for (var m = 0; m < liveAlerts.length; m++) {
                    var a = liveAlerts[m];
                    liveHtml += '<div class="sensor-row">'
                        + '<div class="sname">' + (ICONS[a.k]||'') + ' ' + (liveLabels[a.k]||a.k) + '</div>'
                        + '<div class="sval ' + a.cls + '">' + a.v + '</div>'
                        + '<div class="sunit">— ' + a.cls.toUpperCase() + '</div>'
                        + '</div>';
                }
                rows.innerHTML = liveHtml;
            } else {
                box.style.display = 'none';
            }
        }

        // Grille des anomalies
        renderAnomalies(alertes);
    }).catch(function(err) {
        var grid = document.getElementById('anomaliesGrid');
        if (grid) grid.innerHTML = '<div class="empty-state"><div class="icon">⚠️</div><p>Erreur de chargement. Veuillez actualiser.</p></div>';
    });
}

function renderAnomalies(alertes) {
    var grid = document.getElementById('anomaliesGrid');
    if (!grid) return;

    if (!alertes || alertes.length === 0) {
        grid.innerHTML = '<div class="empty-state"><div class="icon">✅</div><p>Aucune anomalie détectée.<br>Tous les systèmes sont opérationnels.</p></div>';
        return;
    }

    var html = '';
    for (var i = 0; i < alertes.length; i++) {
        var a = alertes[i];
        var d = '';
        try { d = new Date(a.created_at).toLocaleString('fr-FR'); } catch(e) { d = a.created_at || ''; }

        var type = String(a.type || '').toLowerCase();
        var msg  = String(a.message || '');
        var msgL = msg.toLowerCase();

        var sKey = null;
        if (type === 'pir' || msgL.indexOf('mouvement') >= 0 || msgL.indexOf('intrusion') >= 0) sKey = 'pir';
        else if (type === 'temperature' || msgL.indexOf('temp') >= 0 || msgL.indexOf('surchauffe') >= 0) sKey = 'temperature';
        else if (type === 'humidite' || msgL.indexOf('humid') >= 0) sKey = 'humidite';
        else if (type === 'gaz' || msgL.indexOf('gaz') >= 0 || msgL.indexOf('air') >= 0) sKey = 'gaz';

        var niveau = String(a.niveau || 'warning');
        var solution = (sKey && SOLUTIONS[sKey] && SOLUTIONS[sKey][niveau])
            ? SOLUTIONS[sKey][niveau]
            : 'Analyser et résoudre l\'anomalie. Contacter l\'équipe technique si nécessaire.';
        var icon = sKey ? (ICONS[sKey] || '⚠️') : '⚠️';
        var isResolved = (a.resolu == 1);
        var shortMsg = msg.length > 70 ? msg.substring(0, 70) + '...' : msg;

        html += '<div class="anomaly-card ' + niveau + (isResolved ? ' resolved' : '') + '" id="anom_' + a.id + '">'
            + '<div class="a-header">'
            +   '<div>'
            +     '<div class="a-title">' + icon + ' ' + shortMsg + '</div>'
            +     '<span class="badge badge-' + niveau + '">' + niveau + '</span>'
            +     (isResolved ? '&nbsp;<span class="badge badge-resolved">Résolu</span>' : '')
            +   '</div>'
            +   '<div class="a-time">' + d + '</div>'
            + '</div>'
            + '<div class="a-body">'
            +   '<div class="a-msg">' + msg + '</div>'
            +   '<div class="a-solution"><strong>Solution :</strong> ' + solution + '</div>'
            + '</div>'
            + '<div class="a-footer">'
            + (!isResolved ? '<button class="btn btn-neon" onclick="resoudre(this,' + a.id + ')" style="font-size:11px;padding:6px 12px">&#10003; Résoudre</button>' : '')
            + '<button class="btn btn-red" onclick="supprimerAnom(this,' + a.id + ')" style="font-size:11px;padding:6px 12px">&#10005; Supprimer</button>'
            + '</div>'
            + '</div>';
    }
    grid.innerHTML = html;
}

function resoudre(btn, id) {
    btnLoad(btn);
    csrfFetch('/api/alertes/lire', { method:'POST', body:JSON.stringify({id:id}) })
        .then(function(r){ return r.json(); })
        .then(function() {
            btnLoad(btn, false);
            var card = document.getElementById('anom_' + id);
            if (card) card.classList.add('resolved');
            notify('Anomalie marquée comme résolue.', 's');
            loadAnomalies();
        })
        .catch(function(){ btnLoad(btn, false); notify('Erreur réseau.', 'e'); });
}

function supprimerAnom(btn, id) {
    confirmDlg(
        'Supprimer cette anomalie ?',
        'Cette anomalie sera définitivement retirée de l\'historique.',
        { type:'danger', icon:'🗑️', confirmText:'Supprimer' }
    ).then(function(ok) {
        if (!ok) return;
        btnLoad(btn);
        csrfFetch('/alerte/' + id, { method:'DELETE' })
            .then(function(r){ return r.json(); })
            .then(function(d) {
                if (d.success) {
                    var card = document.getElementById('anom_' + id);
                    if (card) {
                        card.style.opacity = '0';
                        card.style.transition = 'opacity .25s';
                        setTimeout(function(){ card.remove(); }, 260);
                    }
                    notify('Anomalie supprimée.', 's');
                } else { btnLoad(btn, false); notify('Erreur.', 'e'); }
            })
            .catch(function(){ btnLoad(btn, false); notify('Erreur réseau.', 'e'); });
    });
}

function marquerTout() {
    csrfFetch('/api/alertes/lire', { method:'POST', body:JSON.stringify({id:'all'}) })
        .then(function(){ notify('Toutes les anomalies marquées comme résolues.', 's'); loadAnomalies(); })
        .catch(function(){ notify('Erreur.', 'e'); });
}

// Chargement initial + rafraîchissement automatique
loadAnomalies();
setInterval(loadAnomalies, 10000);
</script>
@endsection
