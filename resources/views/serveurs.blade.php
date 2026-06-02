@extends('layouts.app')
@section('content')
<style>
/* ═══════════════════════════════════════════════════
   VARIABLES & RESET
═══════════════════════════════════════════════════ */
:root{
  --bg:        #05080f;
  --surface:   #0b1120;
  --surface2:  #0e1628;
  --border:    rgba(51,181,255,.12);
  --border2:   rgba(51,181,255,.22);
  --neon:      #00e5ff;
  --green:     #00e676;
  --amber:     #ffc400;
  --red:       #ff3d57;
  --purple:    #b388ff;
  --txt:       #cdd9f0;
  --txt2:      #6b82a8;
  --txt3:      #3d5070;
  --radius:    14px;
  --radius-lg: 20px;
  --shadow:    0 4px 32px rgba(0,0,0,.45);
  --shadow-lg: 0 8px 48px rgba(0,0,0,.6);
}
*{box-sizing:border-box;margin:0;padding:0}

/* ═══════════════════════════════════════════════════
   HEADER
═══════════════════════════════════════════════════ */
.hdr{
  display:flex;align-items:flex-start;justify-content:space-between;
  gap:16px;margin-bottom:28px;flex-wrap:wrap;
}
.hdr-left{display:flex;flex-direction:column;gap:4px}
.hdr-eyebrow{
  display:flex;align-items:center;gap:8px;
  font-size:11px;color:var(--neon);text-transform:uppercase;letter-spacing:1.5px;font-weight:700;
  margin-bottom:4px;
}
.hdr-eyebrow::before{
  content:'';width:18px;height:2px;background:linear-gradient(90deg,var(--neon),transparent);
  border-radius:2px;
}
.hdr-title{
  font-size:24px;font-weight:800;color:var(--green);letter-spacing:-.3px;line-height:1.15;
}
.hdr-title em{color:var(--green);font-style:normal}
.hdr-sub{font-size:12.5px;color:var(--txt2);margin-top:2px}
.hdr-right{display:flex;align-items:center;gap:12px;flex-wrap:wrap}

.clock-card{
  background:rgba(0,229,255,.06);border:1px solid rgba(0,229,255,.15);
  border-radius:12px;padding:8px 16px;text-align:right;min-width:130px;
}
.clock-time{font-size:17px;font-weight:700;color:var(--neon);font-variant-numeric:tabular-nums;letter-spacing:.5px}
.clock-date{font-size:10.5px;color:var(--txt2);margin-top:1px}

.btn-add{
  display:flex;align-items:center;gap:8px;
  padding:11px 22px;border-radius:12px;
  background:linear-gradient(135deg,#00b8d4,#00e676);
  color:#04111a;font-size:13.5px;font-weight:800;cursor:pointer;border:none;
  box-shadow:0 0 20px rgba(0,229,255,.3),0 4px 12px rgba(0,0,0,.4);
  transition:all .25s;letter-spacing:.3px;white-space:nowrap;
}
.btn-add:hover{
  transform:translateY(-2px);
  box-shadow:0 0 30px rgba(0,229,255,.5),0 6px 20px rgba(0,0,0,.5);
}
.btn-add i{font-size:14px}

/* ═══════════════════════════════════════════════════
   ALERT
═══════════════════════════════════════════════════ */
.alert{
  display:flex;align-items:center;gap:10px;
  padding:13px 18px;border-radius:12px;margin-bottom:22px;font-size:13px;font-weight:500;
  background:rgba(0,230,118,.08);border:1px solid rgba(0,230,118,.25);color:var(--green);
  animation:fadeSlide .4s ease;
}
@keyframes fadeSlide{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:none}}

/* ═══════════════════════════════════════════════════
   STAT CARDS
═══════════════════════════════════════════════════ */
.stats-row{
  display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:26px;
}
.stat-card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:16px 18px;
  display:flex;align-items:center;gap:14px;
  transition:all .25s;cursor:default;position:relative;overflow:hidden;
}
.stat-card::before{
  content:'';position:absolute;inset:0;opacity:0;
  transition:opacity .3s;
  background:linear-gradient(135deg,rgba(0,229,255,.04),transparent);
}
.stat-card:hover{border-color:var(--border2);transform:translateY(-2px);box-shadow:var(--shadow)}
.stat-card:hover::before{opacity:1}

.stat-icon{
  width:44px;height:44px;border-radius:11px;
  display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;
}
.si-total  {background:rgba(0,229,255,.1); color:var(--neon)}
.si-actif  {background:rgba(0,230,118,.1); color:var(--green)}
.si-maint  {background:rgba(255,196,0,.1); color:var(--amber)}
.si-panne  {background:rgba(255,61,87,.1); color:var(--red)}
.si-hors   {background:rgba(107,130,168,.1);color:var(--txt2)}

.stat-info{display:flex;flex-direction:column;gap:2px}
.stat-val{font-size:26px;font-weight:800;line-height:1;letter-spacing:-1px}
.sv-total{color:var(--neon)}
.sv-actif {color:var(--green)}
.sv-maint {color:var(--amber)}
.sv-panne {color:var(--red)}
.sv-hors  {color:var(--txt2)}
.stat-lbl{font-size:11px;color:var(--txt2);text-transform:uppercase;letter-spacing:.6px;font-weight:600}

/* ═══════════════════════════════════════════════════
   FILTER BAR
═══════════════════════════════════════════════════ */
.filter-bar{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius);padding:14px 18px;
  display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:20px;
}
.filter-bar .sep{width:1px;height:28px;background:var(--border);flex-shrink:0}

.search-wrap{position:relative;flex:1;min-width:220px}
.search-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--txt3);font-size:13px;pointer-events:none}
.search-wrap input{
  width:100%;background:var(--surface2);border:1px solid var(--border);
  color:var(--txt);padding:9px 12px 9px 36px;border-radius:10px;
  font-size:13px;outline:none;transition:.2s;
}
.search-wrap input:focus{border-color:var(--neon);box-shadow:0 0 0 3px rgba(0,229,255,.08)}
.search-wrap input::placeholder{color:var(--txt3)}

.flt-sel{
  background:var(--surface2);border:1px solid var(--border);
  color:var(--txt);padding:9px 32px 9px 12px;border-radius:10px;
  font-size:13px;outline:none;cursor:pointer;transition:.2s;
  appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%236b82a8' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:right 10px center;
}
.flt-sel:focus{border-color:var(--neon);box-shadow:0 0 0 3px rgba(0,229,255,.08)}

/* ═══════════════════════════════════════════════════
   TABLE CONTAINER
═══════════════════════════════════════════════════ */
.table-card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow);
}
.table-wrap{overflow-x:auto}

table{width:100%;border-collapse:collapse;font-size:13px}
thead{position:sticky;top:0;z-index:2}
thead th{
  background:rgba(5,8,15,.95);backdrop-filter:blur(8px);
  color:var(--txt3);text-transform:uppercase;letter-spacing:.8px;
  font-size:10.5px;font-weight:700;padding:13px 16px;
  border-bottom:1px solid var(--border);white-space:nowrap;
}
thead th:first-child{border-radius:0}

tbody tr{
  border-bottom:1px solid rgba(255,255,255,.03);
  transition:background .15s,box-shadow .15s;
}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{
  background:rgba(0,229,255,.03);
  box-shadow:inset 3px 0 0 var(--neon);
}
tbody td{padding:14px 16px;color:var(--txt);vertical-align:middle}

/* Column specialties */
.col-code{color:var(--txt3);font-family:'JetBrains Mono','Fira Code',monospace;font-size:11px}
.col-name .main{font-weight:700;color:#fff;font-size:13.5px}
.col-name .sub{font-size:11px;color:var(--txt3);margin-top:2px}
.col-cat .cat{color:var(--neon);font-size:12px;font-weight:600}
.col-cat .typ{font-size:11px;color:var(--txt3);margin-top:2px}
.col-brand .brd{font-weight:500}
.col-brand .mdl{font-size:11px;color:var(--txt3);margin-top:2px}
.col-ip{font-family:'JetBrains Mono','Fira Code',monospace;font-size:12px;color:#7ecfff}
.col-place .sal{font-weight:500}
.col-place .rck{font-size:11px;color:var(--txt3);margin-top:2px}

/* Empty state */
.empty-state{
  text-align:center;padding:60px 20px;
}
.empty-icon{
  width:72px;height:72px;border-radius:18px;
  background:rgba(0,229,255,.06);border:1px solid var(--border);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 16px;font-size:28px;color:var(--txt3);
}
.empty-title{font-size:15px;font-weight:700;color:var(--txt);margin-bottom:6px}
.empty-sub{font-size:13px;color:var(--txt3)}
.empty-sub strong{color:var(--neon);cursor:pointer}

/* ═══════════════════════════════════════════════════
   BADGES STATUT
═══════════════════════════════════════════════════ */
.badge{
  display:inline-flex;align-items:center;gap:5px;
  padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;
}
.badge::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0}
.badge-actif         {background:rgba(0,230,118,.12);color:#00e676;border:1px solid rgba(0,230,118,.25)}
.badge-inactif       {background:rgba(107,130,168,.1);color:#7e99c8;border:1px solid rgba(107,130,168,.2)}
.badge-maintenance   {background:rgba(255,196,0,.12);color:#ffc400;border:1px solid rgba(255,196,0,.25)}
.badge-en_panne      {background:rgba(255,61,87,.12);color:#ff3d57;border:1px solid rgba(255,61,87,.25)}
.badge-hors_ligne    {background:rgba(107,130,168,.1);color:#7e99c8;border:1px solid rgba(107,130,168,.2)}
.badge-deconnecte    {background:rgba(107,130,168,.1);color:#7e99c8;border:1px solid rgba(107,130,168,.2)}
.badge-surchauffe    {background:rgba(255,100,50,.15);color:#ff7043;border:1px solid rgba(255,100,50,.3)}
.badge-batterie_faible{background:rgba(255,196,0,.12);color:#ffca28;border:1px solid rgba(255,196,0,.25)}
.badge-critique      {background:rgba(255,23,68,.18);color:#ff1744;border:1px solid rgba(255,23,68,.35)}
.badge-en_reparation {background:rgba(0,229,255,.1);color:var(--neon);border:1px solid rgba(0,229,255,.2)}
.badge-desactive     {background:rgba(61,80,112,.1);color:var(--txt3);border:1px solid var(--border)}
.badge-test          {background:rgba(179,136,255,.12);color:#b388ff;border:1px solid rgba(179,136,255,.25)}
.badge-en_installation{background:rgba(0,230,118,.08);color:#69f0ae;border:1px solid rgba(0,230,118,.18)}
.badge-obsolete      {background:rgba(61,80,112,.1);color:var(--txt3);border:1px solid var(--border)}
.badge-remplace      {background:rgba(61,80,112,.1);color:var(--txt3);border:1px solid var(--border)}

/* Criticité */
.crit-pill{display:inline-block;padding:3px 9px;border-radius:6px;font-size:11px;font-weight:700}
.crit-faible  {background:rgba(0,230,118,.08);color:#4caf50}
.crit-normale {background:rgba(107,130,168,.08);color:var(--txt2)}
.crit-haute   {background:rgba(255,196,0,.1);color:#ffc400}
.crit-critique{background:rgba(255,23,68,.15);color:#ff1744}

/* ═══════════════════════════════════════════════════
   ACTION BUTTONS
═══════════════════════════════════════════════════ */
.actions{display:flex;gap:6px;flex-wrap:nowrap}
.btn-act{
  padding:6px 12px;border-radius:8px;font-size:11.5px;font-weight:600;
  cursor:pointer;border:1px solid transparent;transition:all .2s;
  display:flex;align-items:center;gap:5px;white-space:nowrap;
}
.btn-detail{
  background:rgba(0,229,255,.08);color:var(--neon);border-color:rgba(0,229,255,.2);
}
.btn-detail:hover{background:rgba(0,229,255,.16);border-color:rgba(0,229,255,.4)}
.btn-edit{
  background:rgba(0,230,118,.08);color:var(--green);border-color:rgba(0,230,118,.2);
}
.btn-edit:hover{background:rgba(0,230,118,.16);border-color:rgba(0,230,118,.4)}
.btn-del{
  background:rgba(255,61,87,.07);color:var(--red);border-color:rgba(255,61,87,.18);
}
.btn-del:hover{background:rgba(255,61,87,.16);border-color:rgba(255,61,87,.4)}

/* Pagination */
.pager{display:flex;justify-content:center;gap:5px;padding:16px;flex-wrap:wrap}
.pg-btn{
  padding:6px 13px;border-radius:8px;border:1px solid var(--border);
  background:var(--surface2);color:var(--txt2);cursor:pointer;font-size:12px;transition:.2s;
}
.pg-btn:hover,.pg-btn.active{border-color:var(--neon);color:var(--neon);background:rgba(0,229,255,.06)}

/* ═══════════════════════════════════════════════════
   MODAL
═══════════════════════════════════════════════════ */
.modal-overlay{
  display:none;position:fixed;inset:0;
  background:rgba(0,0,0,.8);backdrop-filter:blur(6px);
  z-index:999;overflow-y:auto;padding:24px 16px;
}
.modal-overlay.open{display:flex;align-items:flex-start;justify-content:center}
.modal{
  background:linear-gradient(160deg,#0b1525,#08111f);
  border:1px solid var(--border2);border-radius:22px;
  width:100%;max-width:880px;padding:30px;position:relative;
  box-shadow:0 24px 80px rgba(0,0,0,.7),0 0 0 1px rgba(255,255,255,.04);
  animation:modalIn .25s ease;
}
@keyframes modalIn{from{opacity:0;transform:translateY(16px) scale(.98)}to{opacity:1;transform:none}}

.modal-head{display:flex;align-items:center;gap:12px;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid var(--border)}
.modal-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px}
.mi-add {background:rgba(0,230,118,.12);color:var(--green)}
.mi-edit{background:rgba(0,229,255,.12);color:var(--neon)}
.mi-detail{background:rgba(179,136,255,.12);color:var(--purple)}
.modal-head h3{font-size:16px;font-weight:700;color:#fff;margin:0}
.modal-head p{font-size:12px;color:var(--txt2);margin-top:2px}
.modal-close{
  position:absolute;top:18px;right:20px;
  background:rgba(255,255,255,.05);border:1px solid var(--border);
  color:var(--txt2);font-size:18px;cursor:pointer;line-height:1;
  width:32px;height:32px;border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  transition:.2s;
}
.modal-close:hover{background:rgba(255,61,87,.15);color:var(--red);border-color:rgba(255,61,87,.3)}

/* Tabs */
.tabs{display:flex;gap:4px;margin-bottom:20px;flex-wrap:wrap;background:var(--surface2);padding:5px;border-radius:12px}
.tab{
  padding:7px 16px;border-radius:9px;font-size:12px;font-weight:600;
  cursor:pointer;border:none;color:var(--txt2);background:transparent;transition:.2s;
}
.tab.active{background:rgba(0,229,255,.12);color:var(--neon);box-shadow:0 0 12px rgba(0,229,255,.08)}
.tab:hover:not(.active){color:var(--txt);background:rgba(255,255,255,.04)}

.tab-panel{display:none}
.tab-panel.active{display:block}

/* Form */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-group{display:flex;flex-direction:column;gap:6px}
.form-group.span2{grid-column:span 2}
.form-group label{font-size:11px;color:var(--txt2);text-transform:uppercase;letter-spacing:.6px;font-weight:700}
.form-group input,.form-group select,.form-group textarea{
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);
  color:var(--txt);padding:10px 13px;border-radius:9px;font-size:13px;
  outline:none;width:100%;transition:.2s;
}
.form-group input::placeholder{color:var(--txt3)}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{
  border-color:var(--neon);background:rgba(0,229,255,.04);
  box-shadow:0 0 0 3px rgba(0,229,255,.07);
}
.form-group textarea{resize:vertical;min-height:80px}
.form-group select option{background:#0e1628;color:var(--txt)}

.form-actions{
  display:flex;justify-content:flex-end;gap:10px;
  margin-top:22px;padding-top:18px;border-top:1px solid var(--border);
}
.btn-cancel{
  padding:10px 22px;border-radius:10px;font-size:13px;font-weight:600;
  cursor:pointer;background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.1);color:var(--txt2);transition:.2s;
}
.btn-cancel:hover{background:rgba(255,61,87,.1);color:var(--red);border-color:rgba(255,61,87,.3)}
.btn-save{
  padding:10px 26px;border-radius:10px;font-size:13px;font-weight:700;
  cursor:pointer;border:none;
  background:linear-gradient(135deg,#00b8d4,#00e676);color:#04111a;
  box-shadow:0 0 16px rgba(0,229,255,.25);transition:all .2s;
}
.btn-save:hover{transform:translateY(-1px);box-shadow:0 0 24px rgba(0,229,255,.4)}

/* Detail modal */
.detail-section{margin-bottom:18px}
.detail-section-title{
  font-size:10.5px;color:var(--neon);text-transform:uppercase;
  letter-spacing:1px;font-weight:700;margin-bottom:10px;
  display:flex;align-items:center;gap:8px;
}
.detail-section-title::after{content:'';flex:1;height:1px;background:var(--border)}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.detail-item{background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:10px;padding:10px 14px}
.detail-key{font-size:10px;color:var(--txt3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;font-weight:600}
.detail-val{font-size:13px;color:var(--txt);font-weight:500}

/* ═══════════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════════ */
@media(max-width:1100px){
  .stats-row{grid-template-columns:repeat(3,1fr)}
}
@media(max-width:768px){
  .stats-row{grid-template-columns:repeat(2,1fr)}
  .form-grid{grid-template-columns:1fr}
  .form-group.span2{grid-column:span 1}
  .detail-grid{grid-template-columns:1fr}
  .hdr{flex-direction:column}
  .hdr-right{width:100%;justify-content:space-between}
  .clock-card{flex:1}
  .btn-add{flex:1;justify-content:center}
  .actions{flex-wrap:wrap}
}
@media(max-width:500px){
  .stats-row{grid-template-columns:1fr 1fr}
  .filter-bar{padding:12px}
}
/* ── Capteurs live par équipement ── */
.live-sensors{display:flex;gap:5px;flex-wrap:wrap;margin-top:4px}
.ls-dot{display:inline-flex;align-items:center;gap:2px;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;
        background:rgba(0,230,118,.08);border:1px solid rgba(0,230,118,.25);color:#00e676}
.ls-dot.warn{background:rgba(255,196,0,.1);border-color:rgba(255,196,0,.3);color:#ffc400}
.ls-dot.crit{background:rgba(255,61,87,.1);border-color:rgba(255,61,87,.3);color:#ff3d57}
.ls-dot.pir {background:rgba(255,61,87,.12);border-color:#ff3d57;color:#ff3d57;animation:pf .8s infinite}
@keyframes pf{0%,100%{opacity:1}50%{opacity:.3}}
</style>

@if(session('success_srv'))
<div class="alert"><i class="fa-solid fa-circle-check"></i> {{ session('success_srv') }}</div>
@endif

{{-- ══ EN-TÊTE ══ --}}
<div class="hdr">
  <div class="hdr-left">
    @if(isset($salleActive) && $salleActive)
    <div class="hdr-eyebrow">
      <a href="/salles" style="color:var(--neon);text-decoration:none;font-size:11px">
        <i class="fa-solid fa-arrow-left"></i> Retour aux Salles Serveurs
      </a>
    </div>
    <div class="hdr-title">Équipements — <em>{{ $salleActive->nom }}</em></div>
    <div class="hdr-sub">{{ $equipements->count() }} équipement(s) dans cette salle</div>
    @else
    <div class="hdr-title">Équipements <em>Salle Serveurs</em></div>
    <div class="hdr-sub">Inventaire complet &amp; supervision des équipements réseau et serveurs</div>
    @endif
  </div>
  <div class="hdr-right">
    <div class="clock-card">
      <div class="clock-time" id="clock-time">--:--:--</div>
      <div class="clock-date" id="clock-date">---</div>
    </div>
    <button class="btn-add" onclick="openAdd()">
      <i class="fa-solid fa-plus"></i> Ajouter un équipement
    </button>
  </div>
</div>

{{-- ══ STATISTIQUES ══ --}}
<div class="stats-row">
  <div class="stat-card">
    <div class="stat-icon si-total"><i class="fa-solid fa-layer-group"></i></div>
    <div class="stat-info"><div class="stat-val sv-total">{{ $stats['total'] }}</div><div class="stat-lbl">Total</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon si-actif"><i class="fa-solid fa-circle-check"></i></div>
    <div class="stat-info"><div class="stat-val sv-actif">{{ $stats['actif'] }}</div><div class="stat-lbl">Actifs</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon si-maint"><i class="fa-solid fa-wrench"></i></div>
    <div class="stat-info"><div class="stat-val sv-maint">{{ $stats['maintenance'] }}</div><div class="stat-lbl">Maintenance</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon si-panne"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div class="stat-info"><div class="stat-val sv-panne">{{ $stats['panne'] }}</div><div class="stat-lbl">Panne / Critique</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon si-hors"><i class="fa-solid fa-power-off"></i></div>
    <div class="stat-info"><div class="stat-val sv-hors">{{ $stats['hors_ligne'] }}</div><div class="stat-lbl">Hors ligne</div></div>
  </div>
</div>

{{-- ══ BARRE DE FILTRES ══ --}}
<div class="filter-bar">
  <div class="search-wrap">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" id="search" placeholder="Rechercher nom, marque, IP, série..." oninput="filtrer()">
  </div>
  <div class="sep"></div>
  <select class="flt-sel" id="f-categorie" onchange="filtrer()">
    <option value="">Toutes catégories</option>
    @foreach(['Serveur','Réseau','Stockage','Alimentation','Climatisation','Sécurité','Monitoring','Câblage','Téléphonie','Autre'] as $cat)
    <option>{{ $cat }}</option>
    @endforeach
  </select>
  <select class="flt-sel" id="f-statut" onchange="filtrer()">
    <option value="">Tous statuts</option>
    @foreach(['actif'=>'Actif','inactif'=>'Inactif','maintenance'=>'Maintenance','en_panne'=>'En panne','hors_ligne'=>'Hors ligne','deconnecte'=>'Déconnecté','surchauffe'=>'Surchauffe','batterie_faible'=>'Batterie faible','critique'=>'Critique','en_reparation'=>'En réparation','desactive'=>'Désactivé','test'=>'Test','en_installation'=>'En installation','obsolete'=>'Obsolète','remplace'=>'Remplacé'] as $val=>$lbl)
    <option value="{{ $val }}">{{ $lbl }}</option>
    @endforeach
  </select>
  <select class="flt-sel" id="f-salle" onchange="filtrer()">
    <option value="">Toutes les salles</option>
    @foreach($salles as $salle)
    <option value="{{ $salle->id }}" {{ (isset($salleActive) && $salleActive && $salleActive->id == $salle->id) ? 'selected' : '' }}>{{ $salle->nom }}</option>
    @endforeach
  </select>
</div>

{{-- ══ TABLEAU ══ --}}
<div class="table-card">
  <div class="table-wrap">
    <table id="equipTable">
      <thead>
        <tr>
          <th>Code</th>
          <th>Nom équipement</th>
          <th>Catégorie / Type</th>
          <th>Marque / Modèle</th>
          <th>Adresse IP</th>
          <th>Salle / Rack</th>
          <th>Criticité</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="tbody">
      @forelse($equipements as $e)
      <tr
        data-nom="{{ strtolower($e->nom) }}"
        data-marque="{{ strtolower($e->marque ?? '') }}"
        data-ip="{{ strtolower($e->adresse_ip ?? '') }}"
        data-serie="{{ strtolower($e->numero_serie ?? '') }}"
        data-code="{{ strtolower($e->code_inventaire ?? '') }}"
        data-categorie="{{ $e->categorie ?? '' }}"
        data-statut="{{ $e->statut }}"
        data-salle="{{ $e->salle_id ?? '' }}"
      >
        <td class="col-code">{{ $e->code_inventaire ?? '#'.$e->id }}</td>
        <td class="col-name">
          <div class="main">{{ $e->nom }}</div>
          @if($e->numero_serie)<div class="sub"><i class="fa-solid fa-barcode" style="font-size:9px"></i> {{ $e->numero_serie }}</div>@endif
        </td>
        <td class="col-cat">
          <div class="cat">{{ $e->categorie ?? '—' }}</div>
          <div class="typ">{{ $e->type }}</div>
        </td>
        <td class="col-brand">
          <div class="brd">{{ $e->marque ?? '—' }}</div>
          @if($e->modele)<div class="mdl">{{ $e->modele }}</div>@endif
        </td>
        <td class="col-ip">{{ $e->adresse_ip ?? '—' }}</td>
        <td class="col-place">
          @php $salle = $salles->firstWhere('id', $e->salle_id) @endphp
          <div class="sal">{{ $salle ? $salle->nom : '—' }}</div>
          @if($e->salle_id)
          <div class="live-sensors" id="ls-{{ $e->id }}" data-salle="{{ $e->salle_id }}" style="display:none">
            <span class="ls-dot" id="ls-t-{{ $e->id }}">—°C</span>
            <span class="ls-dot" id="ls-h-{{ $e->id }}">—%</span>
            <span class="ls-dot" id="ls-g-{{ $e->id }}">—ppm</span>
            <span class="ls-dot" id="ls-p-{{ $e->id }}"></span>
          </div>
          @endif
          @if($e->rack || $e->position_rack)<div class="rck"><i class="fa-solid fa-server" style="font-size:9px"></i> {{ $e->rack }}{{ $e->position_rack ? ' · U'.$e->position_rack : '' }}</div>@endif
        </td>
        <td>
          @php $crit = $e->criticite ?? 'normale' @endphp
          <span class="crit-pill crit-{{ $crit }}">{{ ucfirst($crit) }}</span>
        </td>
        <td>
          @php $st = $e->statut ?? 'actif' @endphp
          <span class="badge badge-{{ $st }}">{{ ucfirst(str_replace('_',' ',$st)) }}</span>
        </td>
        <td>
          <div class="actions">
            <button class="btn-act btn-detail" onclick="openDetail({{ $e->id }})"><i class="fa-solid fa-eye"></i> Détails</button>
            <button class="btn-act btn-edit"   onclick="openEdit({{ $e->id }})"><i class="fa-solid fa-pen"></i></button>
            <form method="POST" action="/equipements/{{ $e->id }}" id="del-{{ $e->id }}" style="margin:0">
              @csrf @method('DELETE')
              <button type="button" class="btn-act btn-del" onclick="confirmDel({{ $e->id }})"><i class="fa-solid fa-trash"></i></button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="9">
          <div class="empty-state">
            <div class="empty-icon"><i class="fa-solid fa-server"></i></div>
            <div class="empty-title">Aucun équipement enregistré</div>
            <div class="empty-sub">Commencez par <strong onclick="openAdd()">+ Ajouter un équipement</strong> pour gérer votre inventaire.</div>
          </div>
        </td>
      </tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="pager" id="pagination"></div>
</div>


{{-- ══ MODAL AJOUT ══ --}}
<div class="modal-overlay" id="addModal" onclick="if(event.target===this)this.classList.remove('open')">
<div class="modal">
  <button class="modal-close" onclick="document.getElementById('addModal').classList.remove('open')">&#215;</button>
  <div class="modal-head">
    <div class="modal-icon mi-add"><i class="fa-solid fa-plus"></i></div>
    <div><h3>Nouvel Équipement</h3><p>Remplissez les informations de l'équipement à ajouter</p></div>
  </div>
  <div class="tabs" id="add-tabs">
    <button class="tab active" onclick="switchTab('add',0,this)"><i class="fa-solid fa-info-circle"></i> Général</button>
    <button class="tab" onclick="switchTab('add',1,this)"><i class="fa-solid fa-network-wired"></i> Réseau</button>
    <button class="tab" onclick="switchTab('add',2,this)"><i class="fa-solid fa-microchip"></i> Matériel</button>
    <button class="tab" onclick="switchTab('add',3,this)"><i class="fa-solid fa-location-dot"></i> Emplacement</button>
    <button class="tab" onclick="switchTab('add',4,this)"><i class="fa-solid fa-wrench"></i> Maintenance</button>
    <button class="tab" onclick="switchTab('add',5,this)"><i class="fa-solid fa-note-sticky"></i> Notes</button>
  </div>
  <form method="POST" action="/equipements">
    @csrf
    <div class="tab-panel active" data-modal="add" data-idx="0">
      <div class="form-grid">
        <div class="form-group span2"><label>Nom équipement *</label><input type="text" name="nom" required placeholder="ex: SRV-PROD-01"></div>
        <div class="form-group"><label>Catégorie *</label>
          <select name="categorie" required>
            <option value="">-- Choisir --</option>
            @foreach(['Serveur','Réseau','Stockage','Alimentation','Climatisation','Sécurité','Monitoring','Câblage','Téléphonie','Autre'] as $cat)
            <option>{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group"><label>Type *</label>
          <select name="type" required>
            <option value="">-- Choisir --</option>
            @foreach(['Serveur','Serveur lame','Serveur rack','Serveur tour','Switch','Switch manageable','Switch PoE','Routeur','Firewall','Point d\'accès WiFi','Contrôleur WiFi','Modem','Passerelle réseau','Proxy','NAS','SAN','Baie de stockage','Onduleur (UPS)','PDU intelligent','Générateur électrique','Stabilisateur tension','Batterie secours','Climatisation','Climatisation précision','Ventilation serveur','Capteur température','Capteur humidité','Détecteur fumée','Détecteur incendie','Système extinction incendie','Caméra surveillance','Contrôle accès biométrique','Lecteur badge','Baie réseau','Rack serveur','Patch panel','Fibre optique','Convertisseur fibre','Câblage réseau','Imprimante réseau','Téléphone IP','PBX IP','KVM switch','Console administration','Écran monitoring','Smart TV supervision','Horloge réseau NTP','Appliance sécurité','IDS/IPS','Load balancer','VPN gateway','Serveur DNS','Serveur DHCP','Serveur Web','Serveur Mail','Serveur Base de données','Serveur Virtualisation','Hyperviseur','Raspberry Pi','Mini PC industriel','IoT Gateway'] as $t)
            <option>{{ $t }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group"><label>Marque</label><input type="text" name="marque" placeholder="Dell, Cisco, HP..."></div>
        <div class="form-group"><label>Modèle</label><input type="text" name="modele" placeholder="ex: PowerEdge R740"></div>
        <div class="form-group"><label>Numéro de série</label><input type="text" name="numero_serie" placeholder="ex: ABC123XYZ"></div>
        <div class="form-group"><label>Code inventaire</label><input type="text" name="code_inventaire" placeholder="ex: INV-2024-001"></div>
        <div class="form-group"><label>Statut</label>
          <select name="statut">
            @foreach(['actif'=>'Actif','inactif'=>'Inactif','maintenance'=>'Maintenance','en_panne'=>'En panne','hors_ligne'=>'Hors ligne','deconnecte'=>'Déconnecté','surchauffe'=>'Surchauffe','batterie_faible'=>'Batterie faible','critique'=>'Critique','en_reparation'=>'En réparation','desactive'=>'Désactivé','test'=>'Test','en_installation'=>'En installation','obsolete'=>'Obsolète','remplace'=>'Remplacé'] as $val=>$lbl)
            <option value="{{ $val }}">{{ $lbl }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group"><label>Criticité</label>
          <select name="criticite">
            <option value="faible">Faible</option>
            <option value="normale" selected>Normale</option>
            <option value="haute">Haute</option>
            <option value="critique">Critique</option>
          </select>
        </div>
        <div class="form-group"><label>Fournisseur</label><input type="text" name="fournisseur" placeholder="Nom du fournisseur"></div>
        <div class="form-group"><label>Garantie</label><input type="text" name="garantie" placeholder="ex: 3 ans jusqu'au 01/2027"></div>
        <div class="form-group"><label>Date d'installation</label><input type="date" name="date_installation"></div>
      </div>
    </div>
    <div class="tab-panel" data-modal="add" data-idx="1">
      <div class="form-grid">
        <div class="form-group"><label>Adresse IP</label><input type="text" name="adresse_ip" placeholder="192.168.1.10"></div>
        <div class="form-group"><label>Adresse MAC</label><input type="text" name="adresse_mac" placeholder="AA:BB:CC:DD:EE:FF"></div>
        <div class="form-group"><label>Masque réseau</label><input type="text" name="masque_reseau" placeholder="255.255.255.0"></div>
        <div class="form-group"><label>Passerelle</label><input type="text" name="passerelle" placeholder="192.168.1.1"></div>
        <div class="form-group"><label>VLAN</label><input type="text" name="vlan" placeholder="ex: VLAN 10"></div>
        <div class="form-group"><label>Port réseau</label><input type="text" name="port_reseau" placeholder="ex: Port 24 Switch A"></div>
        <div class="form-group"><label>Nombre de ports</label><input type="number" name="nombre_ports" placeholder="ex: 48"></div>
        <div class="form-group"><label>Débit réseau</label><input type="text" name="debit_reseau" placeholder="ex: 1 Gbps"></div>
      </div>
    </div>
    <div class="tab-panel" data-modal="add" data-idx="2">
      <div class="form-grid">
        <div class="form-group"><label>Processeur CPU</label><input type="text" name="cpu" placeholder="ex: Intel Xeon E5-2690"></div>
        <div class="form-group"><label>Mémoire RAM</label><input type="text" name="ram" placeholder="ex: 64 Go DDR4"></div>
        <div class="form-group"><label>Capacité stockage</label><input type="text" name="stockage" placeholder="ex: 2 To SSD NVMe"></div>
        <div class="form-group"><label>Système d'exploitation</label><input type="text" name="systeme_exploitation" placeholder="ex: Ubuntu 22.04"></div>
        <div class="form-group"><label>Version OS</label><input type="text" name="version_os" placeholder="ex: 22.04.3 LTS"></div>
        <div class="form-group"><label>Version Firmware</label><input type="text" name="version_firmware" placeholder="ex: v3.2.1"></div>
        <div class="form-group"><label>Consommation électrique (W)</label><input type="number" step="0.1" name="consommation_energetique" placeholder="ex: 450"></div>
        <div class="form-group"><label>Tension alimentation</label><input type="text" name="tension_alimentation" placeholder="ex: 220V AC"></div>
        <div class="form-group"><label>Autonomie batterie</label><input type="text" name="autonomie_batterie" placeholder="ex: 30 min"></div>
      </div>
    </div>
    <div class="tab-panel" data-modal="add" data-idx="3">
      <div class="form-grid">
        <div class="form-group"><label>Salle</label>
          <select name="salle_id" id="add_salle_id">
            <option value="">-- Aucune --</option>
            @foreach($salles as $salle)
            <option value="{{ $salle->id }}" {{ (isset($salleActive) && $salleActive && $salleActive->id == $salle->id) ? 'selected' : '' }}>{{ $salle->nom }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group"><label>Rack / Armoire</label><input type="text" name="rack" placeholder="ex: Rack A"></div>
        <div class="form-group"><label>Numéro rack</label><input type="text" name="numero_rack" placeholder="ex: R-01"></div>
        <div class="form-group"><label>Position rack (U)</label><input type="text" name="position_rack" placeholder="ex: U12 à U14"></div>
        <div class="form-group span2"><label>Localisation physique</label><input type="text" name="localisation_physique" placeholder="ex: Datacenter 2, Allée B, Baie 3"></div>
      </div>
    </div>
    <div class="tab-panel" data-modal="add" data-idx="4">
      <div class="form-grid">
        <div class="form-group"><label>Dernière maintenance</label><input type="date" name="derniere_maintenance"></div>
        <div class="form-group"><label>Prochaine maintenance</label><input type="date" name="prochaine_maintenance"></div>
        <div class="form-group"><label>Responsable</label><input type="text" name="responsable" placeholder="Nom du responsable"></div>
        <div class="form-group"><label>Technicien responsable</label><input type="text" name="technicien_responsable" placeholder="Nom du technicien"></div>
      </div>
    </div>
    <div class="tab-panel" data-modal="add" data-idx="5">
      <div class="form-grid">
        <div class="form-group span2"><label>Description</label><textarea name="description" placeholder="Description détaillée de l'équipement, observations, notes techniques..."></textarea></div>
      </div>
    </div>
    <div class="form-actions">
      <button type="button" class="btn-cancel" onclick="document.getElementById('addModal').classList.remove('open')">Annuler</button>
      <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
    </div>
  </form>
</div>
</div>


{{-- ══ MODAL MODIFICATION ══ --}}
<div class="modal-overlay" id="editModal" onclick="if(event.target===this)this.classList.remove('open')">
<div class="modal">
  <button class="modal-close" onclick="document.getElementById('editModal').classList.remove('open')">&#215;</button>
  <div class="modal-head">
    <div class="modal-icon mi-edit"><i class="fa-solid fa-pen-to-square"></i></div>
    <div><h3>Modifier l'équipement</h3><p>Mettez à jour les informations de cet équipement</p></div>
  </div>
  <div class="tabs" id="edit-tabs">
    <button class="tab active" onclick="switchTab('edit',0,this)"><i class="fa-solid fa-info-circle"></i> Général</button>
    <button class="tab" onclick="switchTab('edit',1,this)"><i class="fa-solid fa-network-wired"></i> Réseau</button>
    <button class="tab" onclick="switchTab('edit',2,this)"><i class="fa-solid fa-microchip"></i> Matériel</button>
    <button class="tab" onclick="switchTab('edit',3,this)"><i class="fa-solid fa-location-dot"></i> Emplacement</button>
    <button class="tab" onclick="switchTab('edit',4,this)"><i class="fa-solid fa-wrench"></i> Maintenance</button>
    <button class="tab" onclick="switchTab('edit',5,this)"><i class="fa-solid fa-note-sticky"></i> Notes</button>
  </div>
  <form method="POST" id="editForm" action="#">
    @csrf
    <div class="tab-panel active" data-modal="edit" data-idx="0">
      <div class="form-grid">
        <div class="form-group span2"><label>Nom équipement *</label><input type="text" name="nom" id="e_nom" required></div>
        <div class="form-group"><label>Catégorie</label>
          <select name="categorie" id="e_categorie">
            <option value="">-- Choisir --</option>
            @foreach(['Serveur','Réseau','Stockage','Alimentation','Climatisation','Sécurité','Monitoring','Câblage','Téléphonie','Autre'] as $cat)
            <option>{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group"><label>Type *</label><input type="text" name="type" id="e_type" required></div>
        <div class="form-group"><label>Marque</label><input type="text" name="marque" id="e_marque"></div>
        <div class="form-group"><label>Modèle</label><input type="text" name="modele" id="e_modele"></div>
        <div class="form-group"><label>Numéro de série</label><input type="text" name="numero_serie" id="e_serie"></div>
        <div class="form-group"><label>Code inventaire</label><input type="text" name="code_inventaire" id="e_code"></div>
        <div class="form-group"><label>Statut</label>
          <select name="statut" id="e_statut">
            @foreach(['actif'=>'Actif','inactif'=>'Inactif','maintenance'=>'Maintenance','en_panne'=>'En panne','hors_ligne'=>'Hors ligne','deconnecte'=>'Déconnecté','surchauffe'=>'Surchauffe','batterie_faible'=>'Batterie faible','critique'=>'Critique','en_reparation'=>'En réparation','desactive'=>'Désactivé','test'=>'Test','en_installation'=>'En installation','obsolete'=>'Obsolète','remplace'=>'Remplacé'] as $val=>$lbl)
            <option value="{{ $val }}">{{ $lbl }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group"><label>Criticité</label>
          <select name="criticite" id="e_criticite">
            <option value="faible">Faible</option>
            <option value="normale">Normale</option>
            <option value="haute">Haute</option>
            <option value="critique">Critique</option>
          </select>
        </div>
        <div class="form-group"><label>Fournisseur</label><input type="text" name="fournisseur" id="e_fournisseur"></div>
        <div class="form-group"><label>Garantie</label><input type="text" name="garantie" id="e_garantie"></div>
        <div class="form-group"><label>Date d'installation</label><input type="date" name="date_installation" id="e_date_install"></div>
      </div>
    </div>
    <div class="tab-panel" data-modal="edit" data-idx="1">
      <div class="form-grid">
        <div class="form-group"><label>Adresse IP</label><input type="text" name="adresse_ip" id="e_ip"></div>
        <div class="form-group"><label>Adresse MAC</label><input type="text" name="adresse_mac" id="e_mac"></div>
        <div class="form-group"><label>Masque réseau</label><input type="text" name="masque_reseau" id="e_masque"></div>
        <div class="form-group"><label>Passerelle</label><input type="text" name="passerelle" id="e_passerelle"></div>
        <div class="form-group"><label>VLAN</label><input type="text" name="vlan" id="e_vlan"></div>
        <div class="form-group"><label>Port réseau</label><input type="text" name="port_reseau" id="e_port"></div>
        <div class="form-group"><label>Nombre de ports</label><input type="number" name="nombre_ports" id="e_ports"></div>
        <div class="form-group"><label>Débit réseau</label><input type="text" name="debit_reseau" id="e_debit"></div>
      </div>
    </div>
    <div class="tab-panel" data-modal="edit" data-idx="2">
      <div class="form-grid">
        <div class="form-group"><label>Processeur CPU</label><input type="text" name="cpu" id="e_cpu"></div>
        <div class="form-group"><label>Mémoire RAM</label><input type="text" name="ram" id="e_ram"></div>
        <div class="form-group"><label>Capacité stockage</label><input type="text" name="stockage" id="e_stockage"></div>
        <div class="form-group"><label>Système d'exploitation</label><input type="text" name="systeme_exploitation" id="e_os"></div>
        <div class="form-group"><label>Version OS</label><input type="text" name="version_os" id="e_version_os"></div>
        <div class="form-group"><label>Version Firmware</label><input type="text" name="version_firmware" id="e_firmware"></div>
        <div class="form-group"><label>Consommation (W)</label><input type="number" step="0.1" name="consommation_energetique" id="e_conso"></div>
        <div class="form-group"><label>Tension alimentation</label><input type="text" name="tension_alimentation" id="e_tension"></div>
        <div class="form-group"><label>Autonomie batterie</label><input type="text" name="autonomie_batterie" id="e_autonomie"></div>
      </div>
    </div>
    <div class="tab-panel" data-modal="edit" data-idx="3">
      <div class="form-grid">
        <div class="form-group"><label>Salle</label>
          <select name="salle_id" id="e_salle">
            <option value="">-- Aucune --</option>
            @foreach($salles as $salle)
            <option value="{{ $salle->id }}">{{ $salle->nom }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group"><label>Rack / Armoire</label><input type="text" name="rack" id="e_rack"></div>
        <div class="form-group"><label>Numéro rack</label><input type="text" name="numero_rack" id="e_num_rack"></div>
        <div class="form-group"><label>Position rack (U)</label><input type="text" name="position_rack" id="e_pos_rack"></div>
        <div class="form-group span2"><label>Localisation physique</label><input type="text" name="localisation_physique" id="e_loc"></div>
      </div>
    </div>
    <div class="tab-panel" data-modal="edit" data-idx="4">
      <div class="form-grid">
        <div class="form-group"><label>Dernière maintenance</label><input type="date" name="derniere_maintenance" id="e_derniere_maint"></div>
        <div class="form-group"><label>Prochaine maintenance</label><input type="date" name="prochaine_maintenance" id="e_prochaine_maint"></div>
        <div class="form-group"><label>Responsable</label><input type="text" name="responsable" id="e_resp"></div>
        <div class="form-group"><label>Technicien responsable</label><input type="text" name="technicien_responsable" id="e_technicien"></div>
      </div>
    </div>
    <div class="tab-panel" data-modal="edit" data-idx="5">
      <div class="form-grid">
        <div class="form-group span2"><label>Description</label><textarea name="description" id="e_desc"></textarea></div>
      </div>
    </div>
    <div class="form-actions">
      <button type="button" class="btn-cancel" onclick="document.getElementById('editModal').classList.remove('open')">Annuler</button>
      <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Mettre à jour</button>
    </div>
  </form>
</div>
</div>


{{-- ══ MODAL DÉTAILS ══ --}}
<div class="modal-overlay" id="detailModal" onclick="if(event.target===this)this.classList.remove('open')">
<div class="modal">
  <button class="modal-close" onclick="document.getElementById('detailModal').classList.remove('open')">&#215;</button>
  <div class="modal-head">
    <div class="modal-icon mi-detail"><i class="fa-solid fa-circle-info"></i></div>
    <div><h3 id="d_titre">Détails équipement</h3><p>Fiche complète de l'équipement</p></div>
  </div>
  <div id="detail-content"></div>
</div>
</div>


<script>
const EQUIPEMENTS = @json($equipements->keyBy('id'));
const SALLES      = @json($salles->keyBy('id'));

/* ── Horloge ──────────────────────────────────── */
(function tick(){
  const now = new Date();
  const t = now.toLocaleTimeString('fr-FR');
  const d = now.toLocaleDateString('fr-FR',{weekday:'short',day:'2-digit',month:'short',year:'numeric'});
  document.getElementById('clock-time').textContent = t;
  document.getElementById('clock-date').textContent = d;
  setTimeout(tick, 1000);
})();

/* ── Filtrage ─────────────────────────────────── */
function filtrer() {
  const q    = document.getElementById('search').value.toLowerCase();
  const cat  = document.getElementById('f-categorie').value;
  const stat = document.getElementById('f-statut').value;
  const sale = document.getElementById('f-salle').value;
  document.querySelectorAll('#tbody tr:not(.empty-row)').forEach(tr => {
    if (!tr.dataset.nom) return;
    const ok =
      (!q    || ['nom','marque','ip','serie','code'].some(k => tr.dataset[k]?.includes(q))) &&
      (!cat  || tr.dataset.categorie === cat) &&
      (!stat || tr.dataset.statut    === stat) &&
      (!sale || tr.dataset.salle     === sale);
    tr.style.display = ok ? '' : 'none';
  });
}

/* ── Tabs ─────────────────────────────────────── */
function switchTab(modal, idx, btn) {
  document.querySelectorAll(`[data-modal="${modal}"]`).forEach(p => p.classList.remove('active'));
  document.querySelectorAll(`#${modal}-tabs .tab`).forEach(b => b.classList.remove('active'));
  document.querySelector(`[data-modal="${modal}"][data-idx="${idx}"]`).classList.add('active');
  btn.classList.add('active');
}

/* ── Helpers ──────────────────────────────────── */
function sv(id, val) { const el = document.getElementById(id); if (el) el.value = val ?? ''; }
function ss(id, val) {
  const el = document.getElementById(id); if (!el) return;
  for (let i=0; i<el.options.length; i++) if (el.options[i].value==val) { el.selectedIndex=i; break; }
}

/* ── Ouvrir ajout ─────────────────────────────── */
function openAdd() {
  switchTab('add', 0, document.querySelector('#add-tabs .tab'));
  document.getElementById('addModal').classList.add('open');
}

/* ── Ouvrir modification ──────────────────────── */
function openEdit(id) {
  const e = EQUIPEMENTS[id]; if (!e) return;
  document.getElementById('editForm').action = '/equipements/' + id;
  sv('e_nom', e.nom); sv('e_type', e.type); sv('e_marque', e.marque);
  sv('e_modele', e.modele); sv('e_serie', e.numero_serie); sv('e_code', e.code_inventaire);
  ss('e_categorie', e.categorie); ss('e_statut', e.statut); ss('e_criticite', e.criticite ?? 'normale');
  sv('e_fournisseur', e.fournisseur); sv('e_garantie', e.garantie);
  sv('e_date_install', e.date_installation ? e.date_installation.substring(0,10) : '');
  sv('e_ip', e.adresse_ip); sv('e_mac', e.adresse_mac); sv('e_masque', e.masque_reseau);
  sv('e_passerelle', e.passerelle); sv('e_vlan', e.vlan); sv('e_port', e.port_reseau);
  sv('e_ports', e.nombre_ports); sv('e_debit', e.debit_reseau);
  sv('e_cpu', e.cpu); sv('e_ram', e.ram); sv('e_stockage', e.stockage);
  sv('e_os', e.systeme_exploitation); sv('e_version_os', e.version_os);
  sv('e_firmware', e.version_firmware); sv('e_conso', e.consommation_energetique);
  sv('e_tension', e.tension_alimentation); sv('e_autonomie', e.autonomie_batterie);
  ss('e_salle', e.salle_id); sv('e_rack', e.rack); sv('e_num_rack', e.numero_rack);
  sv('e_pos_rack', e.position_rack); sv('e_loc', e.localisation_physique);
  sv('e_derniere_maint', e.derniere_maintenance ? e.derniere_maintenance.substring(0,10) : '');
  sv('e_prochaine_maint', e.prochaine_maintenance ? e.prochaine_maintenance.substring(0,10) : '');
  sv('e_resp', e.responsable); sv('e_technicien', e.technicien_responsable);
  sv('e_desc', e.description);
  switchTab('edit', 0, document.querySelector('#edit-tabs .tab'));
  document.getElementById('editModal').classList.add('open');
}

/* ── Ouvrir détails ───────────────────────────── */
function openDetail(id) {
  const e = EQUIPEMENTS[id]; if (!e) return;
  const salle = SALLES[e.salle_id] ? SALLES[e.salle_id].nom : '—';
  const f = v => v || '—';
  document.getElementById('d_titre').textContent = e.nom;
  document.getElementById('detail-content').innerHTML = `
    <div class="detail-section">
      <div class="detail-section-title"><i class="fa-solid fa-tag"></i> Identification</div>
      <div class="detail-grid">
        <div class="detail-item"><div class="detail-key">Catégorie</div><div class="detail-val">${f(e.categorie)}</div></div>
        <div class="detail-item"><div class="detail-key">Type</div><div class="detail-val">${f(e.type)}</div></div>
        <div class="detail-item"><div class="detail-key">Marque / Modèle</div><div class="detail-val">${f(e.marque)}${e.modele?' / '+e.modele:''}</div></div>
        <div class="detail-item"><div class="detail-key">N° Série</div><div class="detail-val">${f(e.numero_serie)}</div></div>
        <div class="detail-item"><div class="detail-key">Code inventaire</div><div class="detail-val">${f(e.code_inventaire)}</div></div>
        <div class="detail-item"><div class="detail-key">Statut / Criticité</div><div class="detail-val">${f(e.statut)} — ${f(e.criticite)}</div></div>
      </div>
    </div>
    <div class="detail-section">
      <div class="detail-section-title"><i class="fa-solid fa-network-wired"></i> Réseau</div>
      <div class="detail-grid">
        <div class="detail-item"><div class="detail-key">Adresse IP</div><div class="detail-val">${f(e.adresse_ip)}</div></div>
        <div class="detail-item"><div class="detail-key">Adresse MAC</div><div class="detail-val">${f(e.adresse_mac)}</div></div>
        <div class="detail-item"><div class="detail-key">VLAN</div><div class="detail-val">${f(e.vlan)}</div></div>
        <div class="detail-item"><div class="detail-key">Passerelle</div><div class="detail-val">${f(e.passerelle)}</div></div>
        <div class="detail-item"><div class="detail-key">Débit réseau</div><div class="detail-val">${f(e.debit_reseau)}</div></div>
        <div class="detail-item"><div class="detail-key">Nb. ports</div><div class="detail-val">${f(e.nombre_ports)}</div></div>
      </div>
    </div>
    <div class="detail-section">
      <div class="detail-section-title"><i class="fa-solid fa-microchip"></i> Matériel</div>
      <div class="detail-grid">
        <div class="detail-item"><div class="detail-key">CPU</div><div class="detail-val">${f(e.cpu)}</div></div>
        <div class="detail-item"><div class="detail-key">RAM</div><div class="detail-val">${f(e.ram)}</div></div>
        <div class="detail-item"><div class="detail-key">Stockage</div><div class="detail-val">${f(e.stockage)}</div></div>
        <div class="detail-item"><div class="detail-key">OS / Firmware</div><div class="detail-val">${f(e.systeme_exploitation)}${e.version_firmware?' — FW: '+e.version_firmware:''}</div></div>
        <div class="detail-item"><div class="detail-key">Consommation</div><div class="detail-val">${e.consommation_energetique?e.consommation_energetique+' W':'—'}</div></div>
        <div class="detail-item"><div class="detail-key">Tension / Autonomie</div><div class="detail-val">${f(e.tension_alimentation)}${e.autonomie_batterie?' — '+e.autonomie_batterie:''}</div></div>
      </div>
    </div>
    <div class="detail-section">
      <div class="detail-section-title"><i class="fa-solid fa-location-dot"></i> Emplacement &amp; Maintenance</div>
      <div class="detail-grid">
        <div class="detail-item"><div class="detail-key">Salle</div><div class="detail-val">${salle}</div></div>
        <div class="detail-item"><div class="detail-key">Rack / Position</div><div class="detail-val">${f(e.rack)}${e.position_rack?' · U'+e.position_rack:''}</div></div>
        <div class="detail-item"><div class="detail-key">Localisation</div><div class="detail-val">${f(e.localisation_physique)}</div></div>
        <div class="detail-item"><div class="detail-key">Fournisseur / Garantie</div><div class="detail-val">${f(e.fournisseur)}${e.garantie?' — '+e.garantie:''}</div></div>
        <div class="detail-item"><div class="detail-key">Dernière maintenance</div><div class="detail-val">${f(e.derniere_maintenance)}</div></div>
        <div class="detail-item"><div class="detail-key">Prochaine maintenance</div><div class="detail-val">${f(e.prochaine_maintenance)}</div></div>
        <div class="detail-item"><div class="detail-key">Responsable</div><div class="detail-val">${f(e.responsable)}</div></div>
        <div class="detail-item"><div class="detail-key">Technicien</div><div class="detail-val">${f(e.technicien_responsable)}</div></div>
      </div>
    </div>
    ${e.description?`<div class="detail-section"><div class="detail-section-title"><i class="fa-solid fa-note-sticky"></i> Notes</div><div class="detail-item" style="grid-column:span 2"><div class="detail-val">${e.description}</div></div></div>`:''}
  `;
  document.getElementById('detailModal').classList.add('open');
}

/* ── Suppression ──────────────────────────────── */
function confirmDel(id) {
  if (typeof confirmDlg === 'function') {
    confirmDlg('Supprimer cet équipement ?','Cet équipement sera définitivement supprimé. Cette action est irréversible.',
      {type:'danger',icon:'🖥️',confirmText:'Supprimer'}).then(ok => { if (ok) document.getElementById('del-'+id).submit(); });
  } else if (confirm('Supprimer cet équipement ?')) {
    document.getElementById('del-'+id).submit();
  }
}

/* ── Capteurs live par équipement ─────────────── */
const LS_S = { temperature:{warn:35,crit:40}, humidite:{warn:75,crit:85}, gaz:{warn:300,crit:500} };
function lsClass(cap, v) { return v >= LS_S[cap].crit ? 'crit' : v >= LS_S[cap].warn ? 'warn' : ''; }

function pollEquipLive() {
  fetch('/api/mesures-live')
    .then(r => r.ok ? r.json() : {})
    .then(data => {
      document.querySelectorAll('.live-sensors').forEach(div => {
        const sid  = div.dataset.salle;
        const eid  = div.id.replace('ls-', '');
        const d    = data[sid];
        if (!d) { div.style.display = 'none'; return; }
        div.style.display = '';
        const t = parseFloat(d.temperature)||0, h = parseFloat(d.humidite)||0, g = parseInt(d.gaz)||0;
        const pir = d.pir == 1 || d.pir === true;
        const set = (id, txt, cls) => {
          const el = document.getElementById(id);
          if (el) { el.textContent = txt; el.className = 'ls-dot' + (cls ? ' ' + cls : ''); }
        };
        set('ls-t-' + eid, t + '°C',   lsClass('temperature', t));
        set('ls-h-' + eid, h + '%',    lsClass('humidite',    h));
        set('ls-g-' + eid, g + 'ppm',  lsClass('gaz',         g));
        set('ls-p-' + eid, pir ? '🚶 MVT' : '', pir ? 'pir' : '');
      });
    })
    .catch(() => {});
}

pollEquipLive();
setInterval(pollEquipLive, 2000);
</script>
@endsection
