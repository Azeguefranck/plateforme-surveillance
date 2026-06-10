<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Surveillance des Salles Serveurs</title>
<style>

*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

html, body {
  width:100%; height:100vh;
  overflow:hidden;
  font-family:'Segoe UI', Arial, sans-serif;
  background:#020c1a;
  color:#fff;
}

.bg-grid {
  position:fixed; inset:0; z-index:0;
  background-image:
    linear-gradient(rgba(0,255,136,.035) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0,255,136,.035) 1px, transparent 1px);
  background-size:50px 50px;
  animation:gridDrift 25s linear infinite;
}
@keyframes gridDrift {
  0%   { background-position:0 0,0 0; }
  100% { background-position:0 50px,50px 0; }
}

.bg-glow {
  position:fixed; inset:0; z-index:0;
  background:
    radial-gradient(ellipse 60% 50% at 50% 50%, rgba(0,255,136,.06) 0%, transparent 70%),
    radial-gradient(ellipse 80% 40% at 50% 100%, rgba(0,60,180,.12) 0%, transparent 60%);
}

.scan-line {
  position:fixed; left:0; right:0; height:1px; z-index:1;
  background:linear-gradient(90deg, transparent 0%, rgba(0,255,136,.5) 40%, rgba(0,255,136,.8) 50%, rgba(0,255,136,.5) 60%, transparent 100%);
  animation:scanDown 5s linear infinite;
  pointer-events:none;
}
@keyframes scanDown { 0%{top:-1px} 100%{top:100vh} }

.corner { position:fixed; width:50px; height:50px; opacity:.6; }
.corner-tl { top:18px; left:18px; border-top:2px solid #00ff88; border-left:2px solid #00ff88; border-radius:4px 0 0 0; }
.corner-tr { top:18px; right:18px; border-top:2px solid #00ff88; border-right:2px solid #00ff88; border-radius:0 4px 0 0; }
.corner-bl { bottom:18px; left:18px; border-bottom:2px solid #00ff88; border-left:2px solid #00ff88; border-radius:0 0 0 4px; }
.corner-br { bottom:18px; right:18px; border-bottom:2px solid #00ff88; border-right:2px solid #00ff88; border-radius:0 0 4px 0; }

.particles { position:fixed; inset:0; pointer-events:none; z-index:1; overflow:hidden; }
.p {
  position:absolute; border-radius:50%; background:#00ff88;
  animation:floatUp linear infinite;
}
@keyframes floatUp {
  0%   { transform:translateY(100vh) scale(0); opacity:0; }
  10%  { opacity:.35; }
  90%  { opacity:.15; }
  100% { transform:translateY(-30px) scale(1); opacity:0; }
}

.page {
  position:relative; z-index:2;
  width:100%; height:100vh;
  display:flex; flex-direction:column;
  align-items:center; justify-content:center;
  gap:0;
  padding:16px 20px;
}

.brand {
  display:flex; align-items:center; gap:12px;
  font-size:11px; letter-spacing:5px; color:#00ff88;
  text-transform:uppercase; font-weight:600;
  opacity:0; animation:fadeUp .7s .1s forwards;
  margin-bottom:8px;
}
.brand-line {
  width:35px; height:1px;
  background:linear-gradient(90deg, transparent, #00ff88);
}
.brand-line.r { transform:scaleX(-1); }

.main-title {
  font-size:clamp(20px, 4vw, 52px);
  font-weight:900; letter-spacing:2px;
  text-align:center; line-height:1.15;
  text-transform:uppercase;
  color:#ffffff;
  text-shadow:0 0 40px rgba(0,255,136,.35), 0 0 100px rgba(0,255,136,.1);
  opacity:0; animation:fadeUp .7s .25s forwards;
  margin-bottom:4px;
}
.main-title em { color:#00ff88; font-style:normal; }

.title-rule {
  width:100px; height:2px; margin:10px auto;
  background:linear-gradient(90deg, transparent, #00ff88 40%, #00ff88 60%, transparent);
  opacity:0; animation:fadeUp .7s .35s forwards;
}

.server-visual {
  width:min(680px, 92vw);
  height:min(185px, 26vh);
  position:relative;
  border-radius:12px; overflow:hidden;
  border:1px solid rgba(0,255,136,.12);
  background:linear-gradient(180deg, #071428 0%, #030910 100%);
  box-shadow:
    0 0 50px rgba(0,255,136,.07),
    0 20px 60px rgba(0,0,0,.6),
    inset 0 0 80px rgba(0,15,50,.9);
  opacity:0; animation:fadeUp .7s .45s forwards;
  margin:10px 0;
}

.sv-floor {
  position:absolute; bottom:0; left:0; right:0;
  height:12px;
  background:linear-gradient(180deg, rgba(0,255,136,.08) 0%, transparent 100%);
}
.sv-ceil {
  position:absolute; top:0; left:0; right:0;
  height:3px;
  background:linear-gradient(90deg, transparent 0%, rgba(0,80,255,.4) 30%, rgba(0,255,136,.5) 50%, rgba(0,80,255,.4) 70%, transparent 100%);
}

.racks {
  display:flex; align-items:flex-end;
  height:100%; padding:10px 14px 12px;
  gap:8px; justify-content:center;
}

.rack {
  flex:1; max-width:68px;
  background:linear-gradient(180deg, #0c1d3e 0%, #07101f 100%);
  border:1px solid #172a50;
  border-radius:4px 4px 0 0;
  border-bottom:none;
  display:flex; flex-direction:column;
  padding:7px 5px; gap:5px;
  position:relative;
}
.rack::before {
  content:''; position:absolute;
  top:0; left:50%; transform:translateX(-50%);
  width:28%; height:3px;
  background:#00ff88; border-radius:0 0 2px 2px;
  box-shadow:0 0 8px #00ff88, 0 0 16px rgba(0,255,136,.4);
}
.rack-glow {
  position:absolute; bottom:-2px; left:50%;
  transform:translateX(-50%);
  width:60%; height:5px; border-radius:50%;
  background:rgba(0,255,136,.35); filter:blur(6px);
}

.unit {
  height:7px; border-radius:2px;
  background:linear-gradient(90deg, #091428, #162848);
  position:relative; display:flex;
  align-items:center; padding:0 4px; gap:3px; overflow:hidden;
}
.unit::before {
  content:''; position:absolute;
  left:0; top:0; bottom:0; width:2px;
  background:#00ff88; opacity:.5;
}

.led {
  width:4px; height:4px; border-radius:50%;
  animation:ledBlink 1.2s ease-in-out infinite;
  flex-shrink:0;
}
.led.g { background:#00ff88; box-shadow:0 0 5px #00ff88; }
.led.b { background:#33b5ff; box-shadow:0 0 5px #33b5ff; }
.led.r { background:#ff4444; box-shadow:0 0 5px #ff4444; }
.led.y { background:#ffd633; box-shadow:0 0 5px #ffd633; }
@keyframes ledBlink { 0%,100%{opacity:1} 50%{opacity:.25} }

.sv-screens {
  position:absolute; top:10px; right:14px;
  display:flex; flex-direction:column; gap:6px;
}
.sv-screen {
  width:54px; height:32px;
  background:#040e1c; border:1px solid #0d2a50;
  border-radius:3px; overflow:hidden; position:relative;
}
.sv-screen-scan {
  position:absolute; left:0; right:0; height:1px;
  background:rgba(0,255,136,.6);
  animation:svScan 2s linear infinite;
}
@keyframes svScan { 0%{top:0} 100%{top:100%} }
.sv-screen-txt {
  font-size:5.5px; color:#00ff88; padding:3px 4px;
  line-height:1.5; opacity:.75; font-family:monospace;
}

.sv-cables {
  position:absolute; bottom:12px; left:14px;
  display:flex; gap:3px;
}
.cable {
  width:2px; border-radius:1px; opacity:.4;
}
.cable.g { background:#00ff88; height:20px; }
.cable.b { background:#33b5ff; height:14px; }
.cable.y { background:#ffd633; height:18px; }
.cable.r { background:#ff4444; height:12px; }

.buttons {
  display:flex; gap:16px; flex-wrap:wrap; justify-content:center;
  opacity:0; animation:fadeUp .7s .6s forwards;
  margin-top:10px;
}

.btn {
  padding:13px 34px; border-radius:8px;
  font-size:14px; font-weight:700; letter-spacing:1.5px;
  text-decoration:none; text-transform:uppercase;
  display:inline-flex; align-items:center; gap:9px;
  transition:all .3s ease; position:relative; overflow:hidden;
  cursor:pointer;
}
.btn::after {
  content:''; position:absolute; inset:0;
  background:linear-gradient(135deg, rgba(255,255,255,.06) 0%, transparent 100%);
  transform:translateY(-100%); transition:.3s;
}
.btn:hover::after { transform:translateY(0); }

.btn-register {
  background:transparent;
  border:2px solid #00ff88; color:#00ff88;
  box-shadow:0 0 18px rgba(0,255,136,.15), inset 0 0 18px rgba(0,255,136,.03);
}
.btn-register:hover {
  background:rgba(0,255,136,.07);
  box-shadow:0 0 32px rgba(0,255,136,.45), inset 0 0 28px rgba(0,255,136,.07);
  transform:translateY(-3px);
  color:#00ff88;
  text-shadow:0 0 8px rgba(0,255,136,.6);
}

.btn-login {
  background:linear-gradient(135deg, #08266a 0%, #0c3090 100%);
  border:2px solid #1a4aaa; color:#90b8ff;
  box-shadow:0 0 18px rgba(20,70,180,.25);
}
.btn-login:hover {
  background:linear-gradient(135deg, #0c3090 0%, #1040b0 100%);
  box-shadow:0 0 32px rgba(51,181,255,.35);
  transform:translateY(-3px);
  color:#c8dcff;
  border-color:#3370dd;
}

.btn-ico { font-size:15px; line-height:1; }

.status-strip {
  position:fixed; bottom:16px; left:0; right:0;
  display:flex; justify-content:center; gap:28px;
  z-index:10; flex-wrap:wrap; padding:0 20px;
  opacity:0; animation:fadeUp .7s .8s forwards;
}
.sdot-item {
  display:flex; align-items:center; gap:6px;
  font-size:10px; color:#3a5a7a;
  letter-spacing:.8px; text-transform:uppercase;
}
.sdot {
  width:6px; height:6px; border-radius:50%;
  animation:dotPulse 2s ease-in-out infinite;
}
.sdot.g { background:#00ff88; box-shadow:0 0 6px #00ff88; }
.sdot.b { background:#33b5ff; box-shadow:0 0 6px #33b5ff; animation-delay:.5s; }
.sdot.y { background:#ffd633; box-shadow:0 0 6px #ffd633; animation-delay:1s; }
@keyframes dotPulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.6)} }

@keyframes fadeUp {
  from { opacity:0; transform:translateY(16px); }
  to   { opacity:1; transform:translateY(0); }
}

@media(max-width:620px){
  .main-title { font-size:20px; letter-spacing:1px; }
  .server-visual { height:min(140px, 22vh); }
  .btn { padding:11px 22px; font-size:13px; letter-spacing:1px; }
  .sv-screens, .sv-cables { display:none; }
  .racks { gap:5px; padding:8px 10px 10px; }
  .rack { max-width:48px; padding:5px 3px; gap:3px; }
  .status-strip { gap:14px; }
  .corner { width:36px; height:36px; }
}
@media(max-width:400px){
  .main-title { font-size:17px; }
  .brand { font-size:9px; letter-spacing:3px; }
}

*{overflow-wrap:break-word;word-break:break-word}
img,video,iframe{max-width:100%;height:auto}
@media(max-width:640px){
body{overflow-x:hidden}
[class*="card"],[class*="box"],[class*="container"],[class*="panel"]{max-width:100%!important}
}

</style>
</head>
<body>

<div class="bg-grid"></div>
<div class="bg-glow"></div>
<div class="scan-line"></div>

<div class="corner corner-tl"></div>
<div class="corner corner-tr"></div>
<div class="corner corner-bl"></div>
<div class="corner corner-br"></div>

<div class="particles" id="particles"></div>

<div class="page">

  <h1 class="main-title">SURVEILLANCE DES PARAMÈTRES DES ÉQUIPEMENTS<br><em>D'UNE SALLE SERVEURS</em></h1>
  <div class="title-rule"></div>

  <div class="server-visual">
    <div class="sv-ceil"></div>

    <div class="racks">

      <div class="rack" style="height:92%">
        <div class="rack-glow"></div>
        <div class="unit"><div class="led g"></div><div class="led g"></div><div class="led b"></div></div>
        <div class="unit"><div class="led g"></div><div class="led b"></div></div>
        <div class="unit"><div class="led r" style="animation-delay:.3s"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g"></div><div class="led g"></div><div class="led y" style="animation-delay:.6s"></div></div>
        <div class="unit"><div class="led b"></div></div>
        <div class="unit"><div class="led g"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g" style="animation-delay:.2s"></div><div class="led b"></div></div>
        <div class="unit"><div class="led g"></div></div>
        <div class="unit"><div class="led y" style="animation-delay:.4s"></div><div class="led g"></div></div>
      </div>

      <div class="rack" style="height:80%">
        <div class="rack-glow"></div>
        <div class="unit"><div class="led g"></div><div class="led b" style="animation-delay:.1s"></div></div>
        <div class="unit"><div class="led g"></div><div class="led g"></div></div>
        <div class="unit"><div class="led b" style="animation-delay:.5s"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g"></div></div>
        <div class="unit"><div class="led g" style="animation-delay:.3s"></div><div class="led r"></div></div>
        <div class="unit"><div class="led b"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g"></div></div>
      </div>

      <div class="rack" style="height:100%">
        <div class="rack-glow"></div>
        <div class="unit"><div class="led g" style="animation-delay:.2s"></div><div class="led g"></div><div class="led y"></div></div>
        <div class="unit"><div class="led b"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g" style="animation-delay:.4s"></div></div>
        <div class="unit"><div class="led g"></div><div class="led g"></div></div>
        <div class="unit"><div class="led b" style="animation-delay:.1s"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g"></div><div class="led r" style="animation-delay:.8s"></div></div>
        <div class="unit"><div class="led g"></div><div class="led b"></div></div>
        <div class="unit"><div class="led g" style="animation-delay:.6s"></div></div>
        <div class="unit"><div class="led g"></div><div class="led g"></div></div>
        <div class="unit"><div class="led y" style="animation-delay:.3s"></div></div>
      </div>

      <div class="rack" style="height:86%">
        <div class="rack-glow"></div>
        <div class="unit"><div class="led g"></div><div class="led b"></div></div>
        <div class="unit"><div class="led g" style="animation-delay:.3s"></div><div class="led g"></div></div>
        <div class="unit"><div class="led y"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g"></div><div class="led b" style="animation-delay:.5s"></div></div>
        <div class="unit"><div class="led g"></div></div>
        <div class="unit"><div class="led g" style="animation-delay:.2s"></div><div class="led g"></div></div>
        <div class="unit"><div class="led b"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g"></div></div>
      </div>

      <div class="rack" style="height:95%">
        <div class="rack-glow"></div>
        <div class="unit"><div class="led g"></div><div class="led g" style="animation-delay:.4s"></div></div>
        <div class="unit"><div class="led b"></div><div class="led g"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g" style="animation-delay:.1s"></div></div>
        <div class="unit"><div class="led g"></div><div class="led r" style="animation-delay:.7s"></div></div>
        <div class="unit"><div class="led g"></div><div class="led b"></div></div>
        <div class="unit"><div class="led g" style="animation-delay:.3s"></div><div class="led g"></div></div>
        <div class="unit"><div class="led y"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g"></div></div>
        <div class="unit"><div class="led b" style="animation-delay:.6s"></div><div class="led g"></div></div>
      </div>

      <div class="rack" style="height:75%">
        <div class="rack-glow"></div>
        <div class="unit"><div class="led g" style="animation-delay:.5s"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g"></div><div class="led b"></div></div>
        <div class="unit"><div class="led g"></div><div class="led g" style="animation-delay:.2s"></div><div class="led y"></div></div>
        <div class="unit"><div class="led b" style="animation-delay:.4s"></div></div>
        <div class="unit"><div class="led g"></div><div class="led g"></div></div>
        <div class="unit"><div class="led r"></div><div class="led g" style="animation-delay:.6s"></div></div>
      </div>

      <div class="rack" style="height:88%">
        <div class="rack-glow"></div>
        <div class="unit"><div class="led g" style="animation-delay:.1s"></div><div class="led b"></div></div>
        <div class="unit"><div class="led g"></div><div class="led g"></div></div>
        <div class="unit"><div class="led y" style="animation-delay:.5s"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g"></div><div class="led b"></div></div>
        <div class="unit"><div class="led g" style="animation-delay:.3s"></div></div>
        <div class="unit"><div class="led g"></div><div class="led r" style="animation-delay:.7s"></div></div>
        <div class="unit"><div class="led b"></div><div class="led g"></div></div>
        <div class="unit"><div class="led g"></div></div>
      </div>

    </div>

    <div class="sv-screens">
      <div class="sv-screen">
        <div class="sv-screen-txt">TEMP: 24°C<br>HUM:  46%</div>
        <div class="sv-screen-scan"></div>
      </div>
      <div class="sv-screen">
        <div class="sv-screen-txt">CPU:  78%<br>GAZ:  182</div>
        <div class="sv-screen-scan" style="animation-delay:.9s"></div>
      </div>
      <div class="sv-screen">
        <div class="sv-screen-txt">I:  8.2A<br>P: 1804W</div>
        <div class="sv-screen-scan" style="animation-delay:1.7s"></div>
      </div>
    </div>

    <div class="sv-cables">
      <div class="cable g" style="height:22px"></div>
      <div class="cable b" style="height:16px"></div>
      <div class="cable y" style="height:19px"></div>
      <div class="cable r" style="height:13px"></div>
      <div class="cable g" style="height:25px"></div>
      <div class="cable b" style="height:18px"></div>
      <div class="cable g" style="height:20px"></div>
    </div>

    <div class="sv-floor"></div>
  </div>

  <div class="buttons">
    <a href="/login" class="btn btn-login">
      <span class="btn-ico">⊙</span> Se connecter
    </a>
  </div>

</div>

<div class="status-strip">
  <div class="sdot-item"><div class="sdot g"></div> Système actif</div>
  <div class="sdot-item"><div class="sdot b"></div> IoT connecté</div>
  <div class="sdot-item"><div class="sdot y"></div> Surveillance 24/7</div>
</div>

<script>
(function(){
  const c = document.getElementById('particles');
  for(let i = 0; i < 20; i++){
    const p = document.createElement('div');
    p.className = 'p';
    const s = Math.random() * 2.5 + .8;
    const colors = ['#00ff88','#33b5ff','#ffd633'];
    const col = colors[Math.floor(Math.random()*3)];
    p.style.cssText =
      `width:${s}px;height:${s}px;`+
      `left:${Math.random()*100}%;`+
      `background:${col};`+
      `animation-duration:${Math.random()*14+8}s;`+
      `animation-delay:${Math.random()*12}s;`;
    c.appendChild(p);
  }
})();
</script>

</body>
</html>
