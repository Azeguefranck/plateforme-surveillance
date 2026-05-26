<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="mobile-web-app-capable" content="no">
<meta name="apple-mobile-web-app-capable" content="no">
<title>Surveillance des Salles Serveurs</title>
<link rel="stylesheet" href="/css/noselect.css">
<style>

/* ═══════════════════════════════════════════════
   RESET & BASE
═══════════════════════════════════════════════ */
*, *::before, *::after {
    margin: 0; padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

html, body {
    overflow: hidden;
    height: 100vh;
    width: 100%;
    background: #020912;
    color: white;
}

/* ═══════════════════════════════════════════════
   BACKGROUND
═══════════════════════════════════════════════ */
.bg {
    position: fixed;
    inset: 0;
    background:
        radial-gradient(ellipse 60% 80% at 15% 50%, rgba(0,40,100,0.45) 0%, transparent 70%),
        radial-gradient(ellipse 50% 60% at 85% 20%, rgba(0,80,40,0.3) 0%, transparent 60%),
        linear-gradient(160deg, #020912 0%, #040e20 50%, #020912 100%);
    z-index: 0;
}

.grid-bg {
    position: fixed;
    inset: 0;
    background-image:
        linear-gradient(rgba(0,255,65,0.035) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,255,65,0.035) 1px, transparent 1px);
    background-size: 55px 55px;
    animation: gridMove 25s linear infinite;
    z-index: 1;
}

@keyframes gridMove {
    from { transform: translateY(0); }
    to   { transform: translateY(55px); }
}

.scan-line {
    position: fixed;
    left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent 0%, rgba(0,255,65,0.5) 50%, transparent 100%);
    animation: scanDown 6s linear infinite;
    z-index: 2;
}

@keyframes scanDown {
    0%   { top: 0;     opacity: 0; }
    5%   { opacity: 1; }
    95%  { opacity: 1; }
    100% { top: 100vh; opacity: 0; }
}

#particles {
    position: fixed;
    inset: 0;
    z-index: 1;
    overflow: hidden;
    pointer-events: none;
}

.pt {
    position: absolute;
    border-radius: 50%;
    background: rgba(0,255,65,0.7);
    animation: ptFloat linear infinite;
}

@keyframes ptFloat {
    0%   { transform: translateY(100vh) translateX(0); opacity: 0; }
    8%   { opacity: 1; }
    92%  { opacity: 1; }
    100% { transform: translateY(-30px) translateX(var(--dx)); opacity: 0; }
}

/* ═══════════════════════════════════════════════
   PAGE LAYOUT
═══════════════════════════════════════════════ */
.page {
    position: relative;
    z-index: 10;
    height: 100vh;
    display: flex;
    flex-direction: column;
}

/* ─── NAVBAR ─── */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 44px;
    border-bottom: 1px solid rgba(0,255,65,0.1);
    backdrop-filter: blur(10px);
    animation: fadeDown .7s ease forwards;
    flex-shrink: 0;
}

@keyframes fadeDown {
    from { opacity: 0; transform: translateY(-16px); }
    to   { opacity: 1; transform: translateY(0); }
}

.logo {
    font-size: 21px;
    font-weight: bold;
    color: #39ff14;
    letter-spacing: 3px;
    text-decoration: none;
    text-shadow: 0 0 18px rgba(57,255,20,0.45);
}

.nav-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    color: rgba(57,255,20,0.7);
    letter-spacing: 2px;
}

.dot-live {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #39ff14;
    box-shadow: 0 0 8px rgba(57,255,20,0.9);
    animation: blink 1.8s ease-in-out infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.3; }
}

/* ─── HERO ─── */
.hero {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 56px;
    padding: 16px 60px;
    animation: fadeUp .9s ease .25s both;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* LEFT */
.hero-left {
    flex: 0 0 auto;
    max-width: 460px;
}

.hero-tag {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 11px;
    letter-spacing: 4px;
    color: rgba(57,255,20,0.75);
    text-transform: uppercase;
    margin-bottom: 18px;
}

.hero-tag::before {
    content: '';
    width: 28px; height: 1px;
    background: #39ff14;
    flex-shrink: 0;
}

.hero-title {
    font-size: 50px;
    font-weight: 900;
    line-height: 1.1;
    color: #f0f4ff;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 32px;
}

.hero-title .neon {
    color: #39ff14;
    text-shadow: 0 0 28px rgba(57,255,20,0.35);
    display: block;
}

.hero-btns {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

/* Bouton Créer un compte */
.btn-create {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 30px;
    background: linear-gradient(135deg, #39ff14 0%, #22c55e 100%);
    color: #020912;
    font-size: 14px;
    font-weight: bold;
    border-radius: 50px;
    text-decoration: none;
    letter-spacing: .5px;
    transition: .3s;
    box-shadow: 0 0 24px rgba(57,255,20,0.35), inset 0 1px 0 rgba(255,255,255,0.25);
    position: relative;
    overflow: hidden;
}

.btn-create::after {
    content: '';
    position: absolute;
    top: -50%; left: -60%;
    width: 35%; height: 200%;
    background: rgba(255,255,255,0.22);
    transform: skewX(-20deg);
    transition: left .5s;
}

.btn-create:hover::after { left: 130%; }
.btn-create:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 40px rgba(57,255,20,0.55), inset 0 1px 0 rgba(255,255,255,0.25);
    color: #020912;
}

/* Bouton Se connecter */
.btn-login {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 30px;
    background: rgba(20,50,110,0.35);
    color: #c0d4ff;
    font-size: 14px;
    font-weight: bold;
    border-radius: 50px;
    text-decoration: none;
    letter-spacing: .5px;
    border: 1.5px solid rgba(60,120,220,0.5);
    backdrop-filter: blur(8px);
    transition: .3s;
    box-shadow: 0 0 18px rgba(30,80,200,0.15);
}

.btn-login:hover {
    background: rgba(30,80,200,0.45);
    border-color: rgba(100,160,255,0.85);
    box-shadow: 0 0 28px rgba(30,80,200,0.45);
    transform: translateY(-2px);
    color: white;
}

/* RIGHT: Server Dashboard Visual */
.hero-right {
    flex: 1;
    max-width: 500px;
    animation: fadeUp .9s ease .45s both;
    position: relative;
}

.halo {
    position: absolute;
    inset: -30px;
    background: radial-gradient(ellipse at center, rgba(0,255,65,0.07) 0%, transparent 70%);
    pointer-events: none;
}

.rack-card {
    background: linear-gradient(160deg, #050f20 0%, #030b18 100%);
    border: 1px solid rgba(0,255,65,0.18);
    border-radius: 18px;
    padding: 18px 20px;
    position: relative;
    overflow: hidden;
    box-shadow:
        0 25px 60px rgba(0,0,0,0.6),
        inset 0 1px 0 rgba(255,255,255,0.04),
        0 0 0 1px rgba(0,255,65,0.04);
}

.rack-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(0,255,65,0.5), transparent);
}

/* Corner brackets */
.corner { position:absolute; width:14px; height:14px; border-color:rgba(0,255,65,0.45); border-style:solid; }
.c-tl { top:7px; left:7px;  border-width:1px 0 0 1px; }
.c-tr { top:7px; right:7px; border-width:1px 1px 0 0; }
.c-bl { bottom:7px; left:7px;  border-width:0 0 1px 1px; }
.c-br { bottom:7px; right:7px; border-width:0 1px 1px 0; }

.rack-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(0,255,65,0.09);
}

.rack-title-txt {
    font-size: 10px;
    letter-spacing: 3px;
    color: rgba(57,255,20,0.65);
}

.rack-clock {
    font-size: 11px;
    font-family: monospace;
    color: #4a9fc4;
}

/* Server rows */
.srv {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    margin-bottom: 7px;
    background: rgba(0,255,65,0.025);
    border: 1px solid rgba(0,255,65,0.07);
    border-radius: 7px;
    transition: border-color .3s;
}

.srv:hover { border-color: rgba(0,255,65,0.18); }

.srv-id {
    font-size: 9px;
    color: #2d4060;
    font-family: monospace;
    width: 32px;
}

.leds { display:flex; gap:4px; }

.led {
    width: 6px; height: 6px;
    border-radius: 50%;
}

.led-g { background:#39ff14; box-shadow:0 0 5px rgba(57,255,20,0.8); animation:ledp 1.6s ease-in-out infinite; }
.led-b { background:#4a9fc4; box-shadow:0 0 5px rgba(74,159,196,0.7); animation:ledp 2.2s ease-in-out infinite .4s; }
.led-o { background:#f59e0b; box-shadow:0 0 5px rgba(245,158,11,0.7); }
.led-x { background:#1a2840; }

@keyframes ledp { 0%,100%{opacity:1;} 50%{opacity:0.25;} }

.bar-wrap {
    flex: 1;
    height: 4px;
    background: rgba(255,255,255,0.05);
    border-radius: 4px;
    overflow: hidden;
}

.bar {
    height: 100%;
    border-radius: 4px;
}

.bar-g { background:linear-gradient(90deg,#39ff14,#22c55e); box-shadow:0 0 7px rgba(57,255,20,0.4); }
.bar-b { background:linear-gradient(90deg,#4a9fc4,#2e6fa3); box-shadow:0 0 7px rgba(74,159,196,0.4); }
.bar-o { background:linear-gradient(90deg,#f59e0b,#d97706); box-shadow:0 0 7px rgba(245,158,11,0.4); }

.srv-tmp {
    font-size: 10px;
    font-family: monospace;
    width: 36px;
    text-align: right;
}

.srv-st {
    font-size: 9px;
    letter-spacing: .8px;
    width: 38px;
    text-align: right;
    opacity: .75;
}

/* Bottom grid */
.rack-foot {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid rgba(0,255,65,0.08);
}

.fstat-val {
    font-size: 17px;
    font-weight: bold;
    font-family: monospace;
    text-shadow: 0 0 10px currentColor;
}

.fstat-lbl {
    font-size: 9px;
    color: #2d4060;
    letter-spacing: 1px;
    margin-top: 2px;
    text-transform: uppercase;
}

.fstat { text-align: center; }

/* Active alert */
.rack-alert {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    padding: 7px 11px;
    background: rgba(57,255,20,0.03);
    border: 1px solid rgba(57,255,20,0.1);
    border-radius: 6px;
}

.adot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #39ff14;
    box-shadow: 0 0 8px rgba(57,255,20,0.9);
    animation: blink 1.5s ease-in-out infinite;
    flex-shrink: 0;
}

.atxt { font-size: 9px; color: rgba(57,255,20,0.7); letter-spacing: 1.5px; }

/* ═══════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════ */

/* Tablette landscape */
@media (max-width: 1100px) {
    .hero { gap: 36px; padding: 14px 36px; }
    .hero-title { font-size: 40px; }
    .hero-right { max-width: 420px; }
}

/* Tablette portrait / petit laptop */
@media (max-width: 860px) {
    html, body { overflow-y: auto; }

    .hero {
        flex-direction: column;
        padding: 20px 28px 28px;
        gap: 24px;
        justify-content: flex-start;
    }

    .hero-left {
        max-width: 100%;
        text-align: center;
        order: 1;
    }

    .hero-tag { justify-content: center; }
    .hero-btns { justify-content: center; }

    .hero-right {
        max-width: 100%;
        width: 100%;
        order: 2;
    }

    .hero-title { font-size: 34px; }
}

/* Mobile */
@media (max-width: 520px) {
    .navbar { padding: 14px 20px; }
    .logo { font-size: 17px; letter-spacing: 2px; }
    .nav-indicator { display: none; }
    .hero { padding: 16px 16px 24px; gap: 20px; }
    .hero-title { font-size: 27px; letter-spacing: .5px; }
    .hero-tag { font-size: 10px; letter-spacing: 3px; }
    .btn-create, .btn-login { padding: 12px 22px; font-size: 13px; }
    .rack-card { padding: 14px 14px; }
}

/* Très petit mobile */
@media (max-width: 360px) {
    .hero-title { font-size: 23px; }
    .hero-btns { flex-direction: column; align-items: stretch; }
    .btn-create, .btn-login { text-align: center; justify-content: center; }
}

/* ═══════════════════════════════════════════════
   BAR ANIMATIONS
═══════════════════════════════════════════════ */
@keyframes barAnim1 { 0%,100%{width:72%} 50%{width:67%} }
@keyframes barAnim2 { 0%,100%{width:58%} 50%{width:64%} }
@keyframes barAnim3 { 0%,100%{width:45%} 50%{width:42%} }
@keyframes barAnim4 { 0%,100%{width:63%} 50%{width:68%} }

</style>
</head>
<body>

<!-- BACKGROUNDS -->
<div class="bg"></div>
<div class="grid-bg"></div>
<div class="scan-line"></div>
<div id="particles"></div>

<!-- PAGE -->
<div class="page">

    <!-- NAVBAR -->
    <nav class="navbar">
        <a class="logo" href="/accueil">SURVEILLANCE</a>
        <div class="nav-indicator">
            <div class="dot-live"></div>
            SYSTÈME ACTIF
        </div>
    </nav>

    <!-- HERO -->
    <div class="hero">

        <!-- LEFT -->
        <div class="hero-left">
            <div class="hero-tag">IoT &nbsp;·&nbsp; Temps Réel &nbsp;·&nbsp; Sécurisé</div>
            <h1 class="hero-title">
                SURVEILLANCE DES
                <span class="neon">SALLES SERVEURS</span>
            </h1>
            <div class="hero-btns">
                <a href="/login" class="btn-login">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                    Se connecter
                </a>
            </div>
        </div>

        <!-- RIGHT : Datacenter Visual -->
        <div class="hero-right">
            <div class="halo"></div>
            <div class="rack-card">
                <div class="corner c-tl"></div>
                <div class="corner c-tr"></div>
                <div class="corner c-bl"></div>
                <div class="corner c-br"></div>

                <div class="rack-head">
                    <div class="rack-title-txt">DATACENTER &nbsp;·&nbsp; SALLE A</div>
                    <div class="rack-clock" id="rtime">00:00:00</div>
                </div>

                <!-- SRV-01 -->
                <div class="srv">
                    <div class="srv-id">SRV-01</div>
                    <div class="leds">
                        <div class="led led-g"></div>
                        <div class="led led-b"></div>
                        <div class="led led-x"></div>
                    </div>
                    <div class="bar-wrap">
                        <div class="bar bar-g" style="width:72%;animation:barAnim1 3.2s ease-in-out infinite;"></div>
                    </div>
                    <div class="srv-tmp" style="color:#39ff14;">24°C</div>
                    <div class="srv-st" style="color:#39ff14;">ONLINE</div>
                </div>

                <!-- SRV-02 -->
                <div class="srv">
                    <div class="srv-id">SRV-02</div>
                    <div class="leds">
                        <div class="led led-g"></div>
                        <div class="led led-g"></div>
                        <div class="led led-b"></div>
                    </div>
                    <div class="bar-wrap">
                        <div class="bar bar-b" style="width:58%;animation:barAnim2 4.1s ease-in-out infinite;"></div>
                    </div>
                    <div class="srv-tmp" style="color:#4a9fc4;">22°C</div>
                    <div class="srv-st" style="color:#4a9fc4;">ONLINE</div>
                </div>

                <!-- SRV-03 -->
                <div class="srv">
                    <div class="srv-id">SRV-03</div>
                    <div class="leds">
                        <div class="led led-g"></div>
                        <div class="led led-o"></div>
                        <div class="led led-x"></div>
                    </div>
                    <div class="bar-wrap">
                        <div class="bar bar-o" style="width:89%;"></div>
                    </div>
                    <div class="srv-tmp" style="color:#f59e0b;">38°C</div>
                    <div class="srv-st" style="color:#f59e0b;">WARN</div>
                </div>

                <!-- SRV-04 -->
                <div class="srv">
                    <div class="srv-id">SRV-04</div>
                    <div class="leds">
                        <div class="led led-g"></div>
                        <div class="led led-b"></div>
                        <div class="led led-g"></div>
                    </div>
                    <div class="bar-wrap">
                        <div class="bar bar-g" style="width:45%;animation:barAnim3 5s ease-in-out infinite 1s;"></div>
                    </div>
                    <div class="srv-tmp" style="color:#39ff14;">21°C</div>
                    <div class="srv-st" style="color:#39ff14;">ONLINE</div>
                </div>

                <!-- SRV-05 -->
                <div class="srv">
                    <div class="srv-id">SRV-05</div>
                    <div class="leds">
                        <div class="led led-g"></div>
                        <div class="led led-g"></div>
                        <div class="led led-x"></div>
                    </div>
                    <div class="bar-wrap">
                        <div class="bar bar-g" style="width:63%;animation:barAnim4 3.7s ease-in-out infinite .5s;"></div>
                    </div>
                    <div class="srv-tmp" style="color:#39ff14;">26°C</div>
                    <div class="srv-st" style="color:#39ff14;">ONLINE</div>
                </div>

                <!-- FOOTER STATS -->
                <div class="rack-foot">
                    <div class="fstat">
                        <div class="fstat-val" style="color:#39ff14;">5/5</div>
                        <div class="fstat-lbl">ONLINE</div>
                    </div>
                    <div class="fstat">
                        <div class="fstat-val" style="color:#4a9fc4;">26°C</div>
                        <div class="fstat-lbl">TEMP MOY</div>
                    </div>
                    <div class="fstat">
                        <div class="fstat-val" style="color:#39ff14;">65%</div>
                        <div class="fstat-lbl">CPU MOY</div>
                    </div>
                </div>

                <!-- ALERT -->
                <div class="rack-alert">
                    <div class="adot"></div>
                    <div class="atxt">SURVEILLANCE ACTIVE &nbsp;·&nbsp; TOUS LES CAPTEURS OK</div>
                </div>

            </div>
        </div>

    </div>

</div>

<script>
// Horloge datacenter
(function clock(){
    const el = document.getElementById('rtime');
    function tick(){
        const n = new Date();
        el.textContent = [n.getHours(), n.getMinutes(), n.getSeconds()]
            .map(v => String(v).padStart(2,'0')).join(':');
    }
    tick();
    setInterval(tick, 1000);
})();

// Particules
(function(){
    const c = document.getElementById('particles');
    for(let i = 0; i < 28; i++){
        const p = document.createElement('div');
        p.className = 'pt';
        const sz  = Math.random() < 0.25 ? 3 : 2;
        const dur = 9 + Math.random() * 14;
        const del = Math.random() * 18;
        const dx  = ((Math.random() - 0.5) * 90).toFixed(0) + 'px';
        p.style.cssText =
            `left:${Math.random()*100}%;` +
            `width:${sz}px;height:${sz}px;` +
            `animation-duration:${dur}s;` +
            `animation-delay:${del}s;` +
            `--dx:${dx};` +
            `opacity:${(.3 + Math.random() * .6).toFixed(2)};`;
        c.appendChild(p);
    }
})();

// Bloquer "Ouvrir dans l'appli"
window.addEventListener('beforeinstallprompt', e => e.preventDefault());
</script>

</body>
</html>
