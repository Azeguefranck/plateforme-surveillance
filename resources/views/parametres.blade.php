@extends('layouts.app')

@section('content')

<style>
/* ─── BASE ─── */
.param-wrap{
    max-width:1200px;
    animation:pfadeIn .5s ease;
}
@keyframes pfadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}

/* ─── PAGE TITLE ─── */
.page-header{
    display:flex;align-items:center;gap:14px;
    margin-bottom:22px;
}
.page-header i{font-size:22px;color:#2fa84f;}
.page-header h1{font-size:20px;font-weight:bold;color:#e8edf8;margin:0;}
.page-header p{font-size:13px;color:#6b7fa0;margin:3px 0 0;}

/* ─── ALERT ─── */
.p-alert{
    display:flex;align-items:center;gap:10px;
    padding:12px 18px;border-radius:10px;
    font-size:13px;font-weight:bold;
    margin-bottom:20px;
}
.p-alert-ok {background:#052010;border:1px solid #2fa84f;color:#6ee7a0;}

/* ─── SECTION TITLE ─── */
.section-title{
    display:flex;align-items:center;gap:10px;
    font-size:13px;font-weight:bold;
    color:#2fa84f;letter-spacing:1px;
    text-transform:uppercase;
    margin:0 0 18px;
    padding-bottom:10px;
    border-bottom:1px solid #182640;
}
.section-title i{font-size:14px;}

/* ─── SEUILS GRID ─── */
.seuils-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    gap:18px;
    margin-bottom:18px;
}

/* ─── CARTE CAPTEUR ─── */
.capteur-card{
    background:#0d1a2e;
    border:1px solid #182640;
    border-radius:16px;
    padding:22px 24px;
    position:relative;
    overflow:hidden;
    transition:border-color .25s;
}
.capteur-card::before{
    content:'';position:absolute;top:0;left:0;right:0;
    height:2px;
}
.capteur-card:hover{border-color:rgba(47,168,79,0.25);}

.capteur-header{
    display:flex;align-items:center;gap:12px;
    margin-bottom:18px;
}
.capteur-icon{
    width:42px;height:42px;
    border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    font-size:18px;
    flex-shrink:0;
}
.capteur-name{font-size:15px;font-weight:bold;color:#e8edf8;}
.capteur-desc{font-size:12px;color:#6b7fa0;margin-top:2px;}

/* ─── CHAMPS SEUIL ─── */
.seuil-row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
}

.seuil-field{display:flex;flex-direction:column;gap:5px;}

.seuil-label{
    font-size:11px;font-weight:bold;
    color:#6b7fa0;letter-spacing:.5px;
    text-transform:uppercase;
    display:flex;align-items:center;gap:5px;
}

.seuil-dot{
    width:7px;height:7px;
    border-radius:50%;
    flex-shrink:0;
}

.seuil-input-wrap{position:relative;}

.seuil-input{
    width:100%;
    padding:10px 44px 10px 13px;
    background:#0a1525;
    border:1.5px solid #1e3050;
    border-radius:9px;
    font-size:14px;font-weight:bold;
    color:#d4dced;
    outline:none;
    transition:border-color .2s,box-shadow .2s;
    -moz-appearance:textfield;
}
.seuil-input::-webkit-outer-spin-button,
.seuil-input::-webkit-inner-spin-button{-webkit-appearance:none;}
.seuil-input:focus{
    border-color:#2fa84f;
    box-shadow:0 0 0 3px rgba(47,168,79,0.1);
}

.seuil-unit{
    position:absolute;right:12px;top:50%;
    transform:translateY(-50%);
    font-size:11px;font-weight:bold;color:#6b7fa0;
    pointer-events:none;
}

/* ─── SAVE BUTTON ─── */
.save-bar{
    display:flex;align-items:center;justify-content:space-between;
    background:#0d1a2e;border:1px solid #182640;
    border-radius:14px;padding:18px 24px;
    flex-wrap:wrap;gap:14px;
    margin-top:4px;
}
.save-hint{
    font-size:13px;color:#6b7fa0;
    display:flex;align-items:center;gap:8px;
}
.save-hint i{color:#2fa84f;}

.btn-save{
    display:inline-flex;align-items:center;gap:9px;
    padding:12px 28px;
    background:#2fa84f;color:#060c1a;
    border:none;border-radius:50px;
    font-size:14px;font-weight:bold;
    cursor:pointer;transition:.2s;
    letter-spacing:.3px;
}
.btn-save:hover{background:#249040;transform:translateY(-1px);box-shadow:0 4px 16px rgba(47,168,79,0.3);}

/* ─── RESPONSIVE ─── */
@media(max-width:700px){
    .seuils-grid{grid-template-columns:1fr;}
    .seuil-row{grid-template-columns:1fr;}
    .capteur-card{padding:16px 16px;}
    .save-bar{flex-direction:column;align-items:stretch;}
    .btn-save{justify-content:center;}
}
</style>

<div class="param-wrap">

    {{-- En-tête --}}
    <div class="page-header">
        <i class="fa-solid fa-gear"></i>
        <div>
            <h1>Paramètres système</h1>
            <p>Configuration des seuils capteurs IoT et des alertes automatiques</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-alert p-alert-ok">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="/parametres/save">
        @csrf

        {{-- ══ SEUILS CAPTEURS ══ --}}
        <div class="section-title">
            <i class="fa-solid fa-sliders"></i>
            Seuils des capteurs
        </div>

        <div class="seuils-grid">

            {{-- TEMPÉRATURE --}}
            <div class="capteur-card" style="border-top-color:#ef4444;">
                <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,#ef4444,transparent);"></div>
                <div class="capteur-header">
                    <div class="capteur-icon" style="background:rgba(239,68,68,0.12);color:#ef4444;">
                        <i class="fa-solid fa-temperature-high"></i>
                    </div>
                    <div>
                        <div class="capteur-name">Température</div>
                        <div class="capteur-desc">Capteur DHT22 · Salle serveurs</div>
                    </div>
                </div>
                <div class="seuil-row">
                    <div class="seuil-field">
                        <label class="seuil-label">
                            <div class="seuil-dot" style="background:#f59e0b;"></div>
                            Avertissement
                        </label>
                        <div class="seuil-input-wrap">
                            <input class="seuil-input" type="number" name="seuil_temp_warn"
                                   value="{{ $settings['seuil_temp_warn'] ?? 30 }}" min="0" max="100" step="1">
                            <span class="seuil-unit">°C</span>
                        </div>
                    </div>
                    <div class="seuil-field">
                        <label class="seuil-label">
                            <div class="seuil-dot" style="background:#ef4444;"></div>
                            Critique
                        </label>
                        <div class="seuil-input-wrap">
                            <input class="seuil-input" type="number" name="seuil_temp_crit"
                                   value="{{ $settings['seuil_temp_crit'] ?? 40 }}" min="0" max="100" step="1">
                            <span class="seuil-unit">°C</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HUMIDITÉ --}}
            <div class="capteur-card">
                <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,#3b82f6,transparent);"></div>
                <div class="capteur-header">
                    <div class="capteur-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6;">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <div>
                        <div class="capteur-name">Humidité</div>
                        <div class="capteur-desc">Capteur DHT22 · Taux HR</div>
                    </div>
                </div>
                <div class="seuil-row">
                    <div class="seuil-field">
                        <label class="seuil-label">
                            <div class="seuil-dot" style="background:#3b82f6;"></div>
                            Minimum
                        </label>
                        <div class="seuil-input-wrap">
                            <input class="seuil-input" type="number" name="seuil_hum_min"
                                   value="{{ $settings['seuil_hum_min'] ?? 30 }}" min="0" max="100" step="1">
                            <span class="seuil-unit">%</span>
                        </div>
                    </div>
                    <div class="seuil-field">
                        <label class="seuil-label">
                            <div class="seuil-dot" style="background:#ef4444;"></div>
                            Maximum
                        </label>
                        <div class="seuil-input-wrap">
                            <input class="seuil-input" type="number" name="seuil_hum_max"
                                   value="{{ $settings['seuil_hum_max'] ?? 80 }}" min="0" max="100" step="1">
                            <span class="seuil-unit">%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- GAZ --}}
            <div class="capteur-card">
                <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,#f59e0b,transparent);"></div>
                <div class="capteur-header">
                    <div class="capteur-icon" style="background:rgba(245,158,11,0.12);color:#f59e0b;">
                        <i class="fa-solid fa-smog"></i>
                    </div>
                    <div>
                        <div class="capteur-name">Gaz / Fumée</div>
                        <div class="capteur-desc">Capteur MQ135 · PPM</div>
                    </div>
                </div>
                <div class="seuil-row">
                    <div class="seuil-field">
                        <label class="seuil-label">
                            <div class="seuil-dot" style="background:#f59e0b;"></div>
                            Avertissement
                        </label>
                        <div class="seuil-input-wrap">
                            <input class="seuil-input" type="number" name="seuil_gaz_warn"
                                   value="{{ $settings['seuil_gaz_warn'] ?? 300 }}" min="0" step="10">
                            <span class="seuil-unit">ppm</span>
                        </div>
                    </div>
                    <div class="seuil-field">
                        <label class="seuil-label">
                            <div class="seuil-dot" style="background:#ef4444;"></div>
                            Critique
                        </label>
                        <div class="seuil-input-wrap">
                            <input class="seuil-input" type="number" name="seuil_gaz_crit"
                                   value="{{ $settings['seuil_gaz_crit'] ?? 500 }}" min="0" step="10">
                            <span class="seuil-unit">ppm</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COURANT --}}
            <div class="capteur-card">
                <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,#a855f7,transparent);"></div>
                <div class="capteur-header">
                    <div class="capteur-icon" style="background:rgba(168,85,247,0.12);color:#a855f7;">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <div class="capteur-name">Courant électrique</div>
                        <div class="capteur-desc">Capteur ACS712 · Ampères</div>
                    </div>
                </div>
                <div class="seuil-row">
                    <div class="seuil-field">
                        <label class="seuil-label">
                            <div class="seuil-dot" style="background:#f59e0b;"></div>
                            Avertissement
                        </label>
                        <div class="seuil-input-wrap">
                            <input class="seuil-input" type="number" name="seuil_cur_warn"
                                   value="{{ $settings['seuil_cur_warn'] ?? 10 }}" min="0" step="0.5">
                            <span class="seuil-unit">A</span>
                        </div>
                    </div>
                    <div class="seuil-field">
                        <label class="seuil-label">
                            <div class="seuil-dot" style="background:#ef4444;"></div>
                            Critique
                        </label>
                        <div class="seuil-input-wrap">
                            <input class="seuil-input" type="number" name="seuil_cur_crit"
                                   value="{{ $settings['seuil_cur_crit'] ?? 15 }}" min="0" step="0.5">
                            <span class="seuil-unit">A</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PUISSANCE --}}
            <div class="capteur-card">
                <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,#06b6d4,transparent);"></div>
                <div class="capteur-header">
                    <div class="capteur-icon" style="background:rgba(6,182,212,0.12);color:#06b6d4;">
                        <i class="fa-solid fa-plug-circle-bolt"></i>
                    </div>
                    <div>
                        <div class="capteur-name">Puissance électrique</div>
                        <div class="capteur-desc">Calcul P = V × I · Watts</div>
                    </div>
                </div>
                <div class="seuil-row">
                    <div class="seuil-field">
                        <label class="seuil-label">
                            <div class="seuil-dot" style="background:#f59e0b;"></div>
                            Avertissement
                        </label>
                        <div class="seuil-input-wrap">
                            <input class="seuil-input" type="number" name="seuil_pwr_warn"
                                   value="{{ $settings['seuil_pwr_warn'] ?? 1500 }}" min="0" step="50">
                            <span class="seuil-unit">W</span>
                        </div>
                    </div>
                    <div class="seuil-field">
                        <label class="seuil-label">
                            <div class="seuil-dot" style="background:#ef4444;"></div>
                            Critique
                        </label>
                        <div class="seuil-input-wrap">
                            <input class="seuil-input" type="number" name="seuil_pwr_crit"
                                   value="{{ $settings['seuil_pwr_crit'] ?? 2000 }}" min="0" step="50">
                            <span class="seuil-unit">W</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MOUVEMENT PIR --}}
            <div class="capteur-card">
                <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,#2fa84f,transparent);"></div>
                <div class="capteur-header">
                    <div class="capteur-icon" style="background:rgba(47,168,79,0.12);color:#2fa84f;">
                        <i class="fa-solid fa-person-walking"></i>
                    </div>
                    <div>
                        <div class="capteur-name">Mouvement PIR</div>
                        <div class="capteur-desc">Détecteur infrarouge · Intrusion</div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#0a1525;border:1px solid #182640;border-radius:9px;">
                    <div>
                        <div style="font-size:13px;font-weight:bold;color:#d4dced;">Alerte mouvement activée</div>
                        <div style="font-size:11px;color:#6b7fa0;margin-top:2px;">Envoyer SMS + email si mouvement détecté</div>
                    </div>
                    <label style="position:relative;width:42px;height:22px;flex-shrink:0;">
                        <input type="checkbox" name="seuil_pir" value="1" id="pirToggle"
                               {{ ($settings['seuil_pir'] ?? 1) ? 'checked' : '' }}
                               style="opacity:0;width:0;height:0;">
                        <span id="pirSlider" style="position:absolute;cursor:pointer;inset:0;background:#1e3050;border-radius:22px;transition:.3s;"
                              onclick="togglePir(this)"></span>
                        <span id="pirDot" style="position:absolute;width:16px;height:16px;left:3px;bottom:3px;background:#6b7fa0;border-radius:50%;transition:.3s;pointer-events:none;"></span>
                    </label>
                </div>
                <input type="hidden" name="seuil_pir" id="pirVal" value="{{ ($settings['seuil_pir'] ?? 1) ? 1 : 0 }}">
            </div>

        </div>

        {{-- ══ BARRE DE SAUVEGARDE ══ --}}
        <div class="save-bar">
            <div class="save-hint">
                <i class="fa-solid fa-circle-info"></i>
                Les seuils sont appliqués immédiatement aux alertes GSM, emails et dashboard temps réel.
            </div>
            <button type="submit" class="btn-save">
                <i class="fa-solid fa-floppy-disk"></i>
                Sauvegarder les seuils
            </button>
        </div>

    </form>

</div>

<script>
// Initialiser le toggle PIR au chargement
(function(){
    const chk = document.getElementById('pirToggle');
    const slider = document.getElementById('pirSlider');
    const dot = document.getElementById('pirDot');
    if(chk && chk.checked){
        slider.style.background = '#2fa84f';
        slider.style.boxShadow  = '0 0 8px rgba(47,168,79,0.4)';
        dot.style.transform     = 'translateX(20px)';
        dot.style.background    = 'white';
    }
})();

function togglePir(slider){
    const chk = document.getElementById('pirToggle');
    const dot = document.getElementById('pirDot');
    const val = document.getElementById('pirVal');
    chk.checked = !chk.checked;
    if(chk.checked){
        slider.style.background = '#2fa84f';
        slider.style.boxShadow  = '0 0 8px rgba(47,168,79,0.4)';
        dot.style.transform     = 'translateX(20px)';
        dot.style.background    = 'white';
        val.value = 1;
    } else {
        slider.style.background = '#1e3050';
        slider.style.boxShadow  = 'none';
        dot.style.transform     = 'translateX(0)';
        dot.style.background    = '#6b7fa0';
        val.value = 0;
    }
}

// Validation : seuil warn < seuil crit
document.querySelector('form').addEventListener('submit', function(e){
    const pairs = [
        ['seuil_temp_warn','seuil_temp_crit','Température'],
        ['seuil_gaz_warn', 'seuil_gaz_crit', 'Gaz'],
        ['seuil_cur_warn', 'seuil_cur_crit', 'Courant'],
        ['seuil_pwr_warn', 'seuil_pwr_crit', 'Puissance'],
    ];
    for(const [w,c,name] of pairs){
        const vw = parseFloat(document.querySelector(`[name="${w}"]`).value);
        const vc = parseFloat(document.querySelector(`[name="${c}"]`).value);
        if(vw >= vc){
            e.preventDefault();
            alert(`${name} : le seuil d'avertissement (${vw}) doit être inférieur au seuil critique (${vc}).`);
            return;
        }
    }
});
</script>

@endsection
