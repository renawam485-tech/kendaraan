<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drivora — Sistem Manajemen Kendaraan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,700&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --blue:    #2563eb;
            --blue-dk: #1d4ed8;
            --blue-lt: #eff6ff;
            --indigo:  #4f46e5;
            --gray-50: #f8fafc;
            --gray-100:#f1f5f9;
            --gray-200:#e2e8f0;
            --gray-400:#94a3b8;
            --gray-500:#64748b;
            --gray-700:#334155;
            --gray-900:#0f172a;
            --white:   #ffffff;
            --mw:      min(1280px, 92vw);
            --px:      clamp(16px, 3.5vw, 52px);
        }

        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--white);
            color: var(--gray-900);
            overflow-x: hidden;
        }

        /* ── LOADER ── */
        #loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 18px;
            transition: opacity .45s ease, visibility .45s;
        }
        #loader.hide {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .ld-logo {
            display: flex;
            align-items: center;
            gap: 11px;
            animation: ldIn .5s cubic-bezier(.22,1,.36,1) both;
        }
        .ld-name {
            font-size: 22px;
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: -.5px;
        }
        .ld-bar {
            width: 140px;
            height: 2px;
            background: var(--gray-200);
            border-radius: 2px;
            overflow: hidden;
        }
        .ld-fill {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, var(--blue), var(--indigo));
            border-radius: 2px;
            animation: ldBar .65s ease forwards .2s;
        }
        @keyframes ldBar { to { width: 100%; } }
        @keyframes ldIn  { from { opacity:0; transform:scale(.9) translateY(6px); } to { opacity:1; transform:none; } }

        /* ── REVEAL ── */
        [data-r] {
            opacity: 0;
            transition: opacity .65s cubic-bezier(.22,1,.36,1),
                        transform .65s cubic-bezier(.22,1,.36,1);
        }
        [data-r="up"]    { transform: translateY(24px); }
        [data-r="left"]  { transform: translateX(-22px); }
        [data-r="right"] { transform: translateX(22px); }
        [data-r="scale"] { transform: scale(.95) translateY(12px); }
        [data-r].on      { opacity: 1; transform: none; }
        [data-d="1"] { transition-delay: .07s; }
        [data-d="2"] { transition-delay: .14s; }
        [data-d="3"] { transition-delay: .21s; }
        [data-d="4"] { transition-delay: .28s; }
        [data-d="5"] { transition-delay: .35s; }
        [data-d="6"] { transition-delay: .42s; }

        /* ── NAVBAR ── */
        .nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 200;
            height: clamp(56px, 4.5vw, 66px);
            display: flex;
            align-items: center;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--gray-200);
            transform: translateY(-100%);
            animation: navDrop .5s cubic-bezier(.22,1,.36,1) .55s forwards;
        }
        @keyframes navDrop { to { transform: translateY(0); } }
        .nav-in {
            max-width: var(--mw);
            margin: 0 auto;
            padding: 0 var(--px);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo { display:flex; align-items:center; gap:9px; text-decoration:none; }
        .logo-name { font-size:clamp(15px,1.25vw,18px); font-weight:800; color:var(--gray-900); letter-spacing:-.4px; }
        .nav-links { display:flex; align-items:center; gap:2px; list-style:none; }
        .nav-links a {
            padding: 7px 13px;
            border-radius: 7px;
            font-size: clamp(13px,1vw,14px);
            font-weight: 500;
            color: var(--gray-500);
            text-decoration: none;
            transition: all .14s;
        }
        .nav-links a:hover { color:var(--gray-900); background:var(--gray-100); }
        .btn-nav {
            padding: clamp(7px,.6vw,9px) clamp(16px,1.4vw,20px);
            background: var(--blue);
            border-radius: 8px;
            font-size: clamp(13px,1vw,14px);
            font-weight: 600;
            color: white;
            text-decoration: none;
            transition: all .18s;
        }
        .btn-nav:hover { background:var(--blue-dk); transform:translateY(-1px); box-shadow:0 4px 14px rgba(37,99,235,.3); }
        .burger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 4px;
            outline: none;
        }
        .burger span { display:block; width:22px; height:2px; background:var(--gray-500); border-radius:2px; transition:all .3s cubic-bezier(.22,1,.36,1); }
        .burger.open span:nth-child(1) { transform:translateY(7px) rotate(45deg); }
        .burger.open span:nth-child(2) { opacity:0; transform:scaleX(0); }
        .burger.open span:nth-child(3) { transform:translateY(-7px) rotate(-45deg); }
        /* Backdrop */
        .mob-backdrop {
            position: fixed;
            inset: 0;
            z-index: 198;
            background: rgba(15,23,42,.35);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            opacity: 0;
            visibility: hidden;
            transition: opacity .32s, visibility .32s;
        }
        .mob-backdrop.open { opacity:1; visibility:visible; }

        /* Sidebar drawer */
        .mob-menu {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: min(300px, 82vw);
            background: white;
            border-left: 1px solid var(--gray-200);
            box-shadow: -12px 0 48px rgba(0,0,0,.12);
            z-index: 199;
            transform: translateX(100%);
            transition: transform .36s cubic-bezier(.22,1,.36,1);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .mob-menu.open { transform: translateX(0); }

        /* Drawer header */
        .mob-menu-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 20px 16px;
            border-bottom: 1px solid var(--gray-100);
            flex-shrink: 0;
        }
        .mob-menu-head .logo-name { font-size:16px; font-weight:800; color:var(--gray-900); letter-spacing:-.4px; }
        .mob-close {
            width: 32px; height: 32px;
            background: var(--gray-100);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .14s;
        }
        .mob-close:hover { background: var(--gray-200); }
        .mob-close svg { width:15px; height:15px; color:var(--gray-500); }

        /* Nav links */
        .mob-nav { padding: 10px 12px; flex: 1; }
        .mob-menu a.mob-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-600);
            text-decoration: none;
            transition: all .14s;
            border-bottom: none;
        }
        .mob-menu a.mob-link:hover { color:var(--blue); background:var(--blue-lt); }
        .mob-link-dot { width:6px; height:6px; background:var(--gray-300); border-radius:50%; flex-shrink:0; transition:background .14s; }
        .mob-menu a.mob-link:hover .mob-link-dot { background:var(--blue); }

        /* CTA at bottom */
        .mob-footer { padding: 14px 20px 24px; flex-shrink:0; border-top: 1px solid var(--gray-100); }
        .mob-menu .mob-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px;
            background: var(--blue);
            border-radius: 10px;
            font-weight: 700;
            color: white;
            text-decoration: none;
            font-size: 14px;
            border-bottom: none;
            transition: background .18s, transform .18s, box-shadow .18s;
            box-shadow: 0 4px 16px rgba(37,99,235,.28);
        }
        .mob-menu .mob-btn:hover { background:var(--blue-dk); transform:translateY(-1px); box-shadow:0 6px 20px rgba(37,99,235,.36); }

        /* ── HERO — CENTERED ── */
        .hero {
            min-height: 100svh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: clamp(80px, 10vh, 120px);
            padding-bottom: clamp(64px, 8vh, 100px);
            background: linear-gradient(155deg, #f0f7ff 0%, #ffffff 55%, #f5f3ff 100%);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        /* Decorative blobs */
        .hero::before {
            content: '';
            position: absolute;
            top: -100px; right: -180px;
            width: clamp(360px, 40vw, 680px);
            height: clamp(360px, 40vw, 680px);
            background: radial-gradient(circle, rgba(37,99,235,.09) 0%, transparent 68%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -100px; left: -100px;
            width: clamp(280px, 30vw, 480px);
            height: clamp(280px, 30vw, 480px);
            background: radial-gradient(circle, rgba(79,70,229,.06) 0%, transparent 68%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Extra decorative orb top-left */
        .hero-orb {
            position: absolute;
            top: 18%; left: -60px;
            width: clamp(180px, 16vw, 300px);
            height: clamp(180px, 16vw, 300px);
            background: radial-gradient(circle, rgba(37,99,235,.05) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-in {
            max-width: min(860px, 90vw);
            margin: 0 auto;
            padding: 0 var(--px);
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Heading */
        .hero h1 {
            font-size: clamp(32px, 5.5vw, 72px);
            font-weight: 800;
            line-height: 1.06;
            letter-spacing: clamp(-1.2px, -.22vw, -3px);
            color: var(--gray-900);
            margin-bottom: clamp(16px, 1.6vw, 24px);
            max-width: min(800px, 100%);
        }
        .hero h1 em {
            font-style: normal;
            background: linear-gradient(130deg, var(--blue), var(--indigo));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Description */
        .hero-desc {
            font-size: clamp(15px, 1.2vw, 18px);
            color: var(--gray-500);
            line-height: 1.78;
            margin-bottom: clamp(30px, 3.2vw, 44px);
            max-width: min(560px, 100%);
        }

        /* Buttons */
        .hero-btns {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: clamp(10px, 1vw, 16px);
            flex-wrap: wrap;
        }
        .btn-main {
            padding: clamp(12px, 1vw, 15px) clamp(24px, 2.2vw, 32px);
            background: var(--blue);
            color: white;
            border-radius: 10px;
            font-size: clamp(14px, 1.05vw, 16px);
            font-weight: 700;
            text-decoration: none;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 22px rgba(37,99,235,.28);
        }
        .btn-main:hover { background:var(--blue-dk); transform:translateY(-2px); box-shadow:0 8px 28px rgba(37,99,235,.38); }
        .btn-ghost {
            padding: clamp(12px, 1vw, 15px) clamp(22px, 2vw, 28px);
            font-size: clamp(14px, 1.05vw, 16px);
            font-weight: 600;
            color: var(--gray-500);
            text-decoration: none;
            border: 1.5px solid var(--gray-200);
            border-radius: 10px;
            transition: all .18s;
            background: white;
        }
        .btn-ghost:hover { color:var(--blue); border-color:var(--blue); background:var(--blue-lt); }

        /* Trusted / meta info strip */
        .hero-meta {
            margin-top: clamp(40px, 4.5vw, 60px);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: clamp(20px, 3vw, 40px);
            flex-wrap: wrap;
        }
        .hero-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: clamp(12px, .9vw, 14px);
            color: var(--gray-400);
            font-weight: 500;
        }
        .hero-meta-item svg {
            width: 15px; height: 15px;
            color: var(--blue);
            flex-shrink: 0;
        }
        .hero-meta-div {
            width: 1px; height: 18px;
            background: var(--gray-200);
        }

        /* ── SECTION BASE ── */
        .sec     { padding: clamp(64px,7vw,104px) 0; }
        .sec-alt { padding: clamp(64px,7vw,104px) 0; background: var(--gray-50); }
        .sec-in  { max-width:var(--mw); margin:0 auto; padding:0 var(--px); }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: clamp(10px,.78vw,11px);
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--blue);
            margin-bottom: clamp(10px,1vw,13px);
        }
        .eyebrow::before { content:''; width:14px; height:2px; background:var(--blue); border-radius:2px; }
        .sec-h { font-size:clamp(24px,3vw,40px); font-weight:800; letter-spacing:clamp(-.5px,-.07vw,-1.2px); color:var(--gray-900); margin-bottom:13px; line-height:1.12; }
        .sec-p  { font-size:clamp(13px,1.05vw,16px); color:var(--gray-500); line-height:1.75; max-width:min(560px,100%); }

        /* ── FEATURES ── */
        .feat-hd { text-align:center; margin-bottom:clamp(36px,4vw,54px); }
        .feat-hd .sec-p { margin:0 auto; }
        .feat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:clamp(14px,1.5vw,22px); }
        .feat-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: clamp(12px,.95vw,16px);
            padding: clamp(20px,1.9vw,28px);
            transition: transform .22s, box-shadow .22s, border-color .22s;
            position: relative;
            overflow: hidden;
            cursor: default;
        }
        .feat-card::after { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--blue),var(--indigo)); opacity:0; transition:opacity .22s; }
        .feat-card:hover { transform:translateY(-4px); box-shadow:0 12px 32px rgba(0,0,0,.08); border-color:var(--blue); }
        .feat-card:hover::after { opacity:1; }
        .feat-ico { width:clamp(40px,3.2vw,48px); height:clamp(40px,3.2vw,48px); background:var(--blue-lt); border-radius:clamp(10px,.88vw,12px); display:flex; align-items:center; justify-content:center; margin-bottom:clamp(14px,1.3vw,18px); }
        .feat-ico svg { width:48%; height:48%; color:var(--blue); }
        .feat-t { font-size:clamp(13px,1.05vw,15px); font-weight:700; color:var(--gray-900); margin-bottom:7px; }
        .feat-d { font-size:clamp(12px,.92vw,14px); color:var(--gray-500); line-height:1.68; }

        /* ── HOW IT WORKS ── */
        .how-grid { display:grid; grid-template-columns:1fr 1fr; gap:clamp(40px,5.5vw,96px); align-items:center; }
        .steps { display:flex; flex-direction:column; }
        .step { display:flex; gap:clamp(14px,1.4vw,20px); padding:clamp(16px,1.5vw,22px) 0; position:relative; }
        .step:not(:last-child)::after { content:''; position:absolute; left:clamp(17px,1.5vw,20px); top:clamp(52px,5vw,58px); bottom:0; width:2px; background:var(--gray-100); }
        .step-n { width:clamp(36px,3vw,42px); height:clamp(36px,3vw,42px); background:var(--blue); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:clamp(13px,1.05vw,14px); font-weight:700; flex-shrink:0; z-index:1; position:relative; }
        .step-c { padding-top:clamp(6px,.6vw,9px); }
        .step-t { font-size:clamp(13px,1.05vw,15px); font-weight:700; color:var(--gray-900); margin-bottom:4px; }
        .step-d { font-size:clamp(12px,.92vw,14px); color:var(--gray-500); line-height:1.65; }
        .flow-col { display:flex; flex-direction:column; gap:clamp(8px,.8vw,11px); }
        .flow-card { background:white; border:1px solid var(--gray-200); border-radius:clamp(10px,.88vw,12px); padding:clamp(12px,1.1vw,16px) clamp(14px,1.3vw,20px); display:flex; align-items:center; gap:clamp(10px,1vw,14px); box-shadow:0 2px 8px rgba(0,0,0,.04); transition:all .2s; }
        .flow-card:hover { border-color:var(--blue); box-shadow:0 4px 16px rgba(37,99,235,.1); }
        .flow-ico { width:clamp(30px,2.5vw,36px); height:clamp(30px,2.5vw,36px); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .flow-ico svg { width:54%; height:54%; }
        .flow-t { font-size:clamp(12px,.98vw,13px); font-weight:600; color:var(--gray-700); }
        .flow-s { font-size:clamp(10px,.78vw,11px); color:var(--gray-400); margin-top:2px; }
        .flow-arr { text-align:center; color:var(--gray-300); font-size:18px; line-height:1; }

        /* ── ROLES ── */
        .roles-hd { text-align:center; margin-bottom:clamp(32px,4vw,48px); }
        .roles-hd .sec-p { margin:0 auto; }
        .roles-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:clamp(14px,1.5vw,20px); }
        .role-card { background:white; border:1px solid var(--gray-200); border-radius:clamp(12px,.95vw,16px); padding:clamp(22px,2.1vw,28px) clamp(18px,1.7vw,24px); text-align:center; transition:all .22s; }
        .role-card:hover { transform:translateY(-4px); box-shadow:0 12px 32px rgba(0,0,0,.08); border-color:var(--blue); }
        .role-av { width:clamp(46px,4vw,56px); height:clamp(46px,4vw,56px); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto clamp(12px,1.1vw,16px); }
        .role-av svg { width:48%; height:48%; }
        .role-n { font-size:clamp(13px,1.05vw,15px); font-weight:700; color:var(--gray-900); margin-bottom:clamp(10px,1vw,12px); }
        .role-perms { list-style:none; text-align:left; display:flex; flex-direction:column; gap:clamp(5px,.5vw,7px); }
        .role-perms li { font-size:clamp(11px,.88vw,13px); color:var(--gray-500); display:flex; align-items:flex-start; gap:6px; }
        .role-perms li::before { content:'✓'; color:var(--blue); font-weight:700; font-size:11px; flex-shrink:0; margin-top:1px; }

        /* ── CTA ── */
        .cta-wrap {
            padding: clamp(60px,7vw,100px) 0;
            background: linear-gradient(135deg,var(--blue) 0%,var(--indigo) 100%);
            position: relative;
            overflow: hidden;
        }
        .cta-wrap::before { content:''; position:absolute; top:-100px; right:-100px; width:400px; height:400px; background:radial-gradient(circle,rgba(255,255,255,.08),transparent 70%); border-radius:50%; }
        .cta-wrap::after  { content:''; position:absolute; bottom:-80px; left:-80px; width:320px; height:320px; background:radial-gradient(circle,rgba(255,255,255,.06),transparent 70%); border-radius:50%; }
        .cta-in { max-width:var(--mw); margin:0 auto; padding:0 var(--px); text-align:center; position:relative; z-index:1; }
        .cta-in h2 { font-size:clamp(24px,3.3vw,42px); font-weight:800; color:white; letter-spacing:-1px; margin-bottom:13px; }
        .cta-in p  { font-size:clamp(13px,1.05vw,16px); color:rgba(255,255,255,.76); margin-bottom:clamp(28px,3vw,38px); }
        .cta-btns { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }
        .btn-cta { padding:clamp(11px,.95vw,14px) clamp(22px,2vw,28px); background:white; color:var(--blue); border-radius:10px; font-size:clamp(14px,1.05vw,15px); font-weight:700; text-decoration:none; transition:all .2s; display:inline-flex; align-items:center; gap:8px; }
        .btn-cta:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.15); }
        .btn-cta-ol { padding:clamp(11px,.95vw,14px) clamp(22px,2vw,28px); color:rgba(255,255,255,.88); border:1.5px solid rgba(255,255,255,.36); border-radius:10px; font-size:clamp(14px,1.05vw,15px); font-weight:600; text-decoration:none; transition:all .2s; }
        .btn-cta-ol:hover { background:rgba(255,255,255,.1); color:white; }

        /* ── FOOTER ── */
        footer { background:white; border-top:1px solid var(--gray-200); }
        .f-top { height:3px; background:linear-gradient(90deg,var(--blue),var(--indigo),var(--blue)); }
        .f-in { max-width:var(--mw); margin:0 auto; padding:clamp(36px,4vw,56px) var(--px) clamp(20px,3vw,34px); }
        .f-grid { display:grid; grid-template-columns:1.5fr 1fr 1fr; gap:clamp(28px,3.8vw,56px); margin-bottom:clamp(28px,3.2vw,44px); }
        .f-desc { font-size:clamp(12px,.95vw,14px); color:var(--gray-500); line-height:1.75; margin-top:clamp(10px,1vw,14px); }
        .f-col-t { font-size:clamp(9px,.72vw,10px); font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:var(--gray-400); margin-bottom:clamp(12px,1.1vw,16px); }
        .f-links { list-style:none; display:flex; flex-direction:column; gap:clamp(7px,.65vw,10px); }
        .f-links a { font-size:clamp(12px,.95vw,14px); color:var(--gray-500); text-decoration:none; display:flex; align-items:center; gap:7px; transition:color .14s; }
        .f-links a::before { content:''; width:4px; height:4px; background:var(--blue); border-radius:50%; opacity:.45; flex-shrink:0; }
        .f-links a:hover { color:var(--blue); }
        .f-links a:hover::before { opacity:1; }
        .f-contact { font-size:clamp(12px,.95vw,14px); color:var(--gray-500); line-height:1.72; }
        .f-email { display:inline-flex; align-items:center; gap:6px; color:var(--blue); text-decoration:none; font-size:clamp(12px,.95vw,14px); font-weight:500; margin-top:6px; transition:color .14s; }
        .f-email:hover { color:var(--blue-dk); }
        .f-bot { border-top:1px solid var(--gray-100); padding-top:clamp(18px,1.9vw,26px); display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
        .f-copy { font-size:clamp(11px,.82vw,12px); color:var(--gray-400); }
        .f-mark { display:inline-flex; align-items:center; gap:6px; background:var(--blue-lt); border:1px solid #bfdbfe; border-radius:100px; padding:4px 12px; }
        .f-mark span { font-size:clamp(10px,.78vw,11px); font-weight:600; color:var(--blue); }

        /* ── RESPONSIVE ── */
        /* 4K / ultra-wide */
        @media(min-width:2560px) {
            :root { --mw:min(2100px,88vw); --px:clamp(52px,3.5vw,96px); }
            .nav { height:76px; }
            .hero h1 { font-size: clamp(64px, 4.2vw, 96px); }
            .hero-desc { font-size: clamp(18px, 1.2vw, 22px); }
            .sec, .sec-alt { padding:130px 0; }
        }
        /* 1920–2559 */
        @media(min-width:1921px) and (max-width:2559px) {
            :root { --mw:min(1620px,90vw); }
            .nav { height:70px; }
            .hero h1 { font-size: clamp(52px, 4.5vw, 80px); }
        }
        /* 1440–1920 — comfortable large desktop */
        @media(min-width:1440px) and (max-width:1920px) {
            .hero h1 { font-size: clamp(48px, 4.8vw, 72px); }
            .hero-desc { font-size: clamp(16px, 1.1vw, 19px); max-width: min(600px, 100%); }
        }
        /* Tablet landscape */
        @media(max-width:1100px) {
            .feat-grid { grid-template-columns:repeat(2,1fr); }
        }
        /* Tablet portrait */
        @media(max-width:860px) {
            .how-grid { grid-template-columns:1fr; gap:clamp(32px,5vw,52px); }
            .flow-col { display:none; }
            .roles-grid { grid-template-columns:repeat(2,1fr); }
            .f-grid { grid-template-columns:1fr 1fr; }
            .nav-links { display:none; }
            .burger { display:flex; }
            .hero h1 { font-size: clamp(30px, 6.5vw, 52px); }
            .hero-meta-div { display:none; }
            .hero-meta { gap: 14px; }
        }
        /* Mobile */
        @media(max-width:520px) {
            .feat-grid { grid-template-columns:1fr; }
            .roles-grid { grid-template-columns:1fr; }
            .f-grid { grid-template-columns:1fr; }
            .hero-btns { flex-direction:column; align-items:center; width:100%; }
            .btn-main, .btn-ghost { width:100%; justify-content:center; text-align:center; }
            .cta-btns { flex-direction:column; align-items:center; }
            .btn-cta { width:100%; justify-content:center; }
        }
        /* Very small */
        @media(max-width:360px) {
            :root { --px:14px; }
            .hero h1 { font-size:26px; }
        }
        /* Landscape phone */
        @media(max-height:500px) and (orientation:landscape) {
            .hero { min-height:auto; padding-top:72px; padding-bottom:48px; }
        }
    </style>
</head>

<body>

    <!-- LOADER -->
    <div id="loader">
        <div class="ld-logo">
            <span class="ld-name">Drivora</span>
        </div>
        <div class="ld-bar">
            <div class="ld-fill"></div>
        </div>
    </div>

    <!-- NAVBAR -->
    <nav class="nav">
        <div class="nav-in">
            <a href="#" class="logo">
                <span class="logo-name">Drivora</span>
            </a>
            <ul class="nav-links">
                <li><a href="#fitur">Fitur</a></li>
                <li><a href="#cara-kerja">Cara Kerja</a></li>
                <li><a href="#peran">Peran</a></li>
            </ul>
            <div style="display:flex;align-items:center;gap:12px">
                <a href="{{ route('login') }}" class="btn-nav">Masuk</a>
                <button class="burger" id="burger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- MOBILE BACKDROP -->
    <div class="mob-backdrop" id="mobBackdrop" onclick="closeMob()"></div>

    <!-- MOBILE SIDEBAR -->
    <div class="mob-menu" id="mobMenu">
        <div class="mob-menu-head">
            <span class="logo-name">Drivora</span>
            <button class="mob-close" onclick="closeMob()" aria-label="Tutup menu">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <nav class="mob-nav">
            <a href="#fitur" class="mob-link" onclick="closeMob()">
                <span class="mob-link-dot"></span>Fitur
            </a>
            <a href="#cara-kerja" class="mob-link" onclick="closeMob()">
                <span class="mob-link-dot"></span>Cara Kerja
            </a>
            <a href="#peran" class="mob-link" onclick="closeMob()">
                <span class="mob-link-dot"></span>Peran
            </a>
        </nav>
        <div class="mob-footer">
            <a href="{{ route('login') }}" class="mob-btn">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
                Masuk ke Sistem
            </a>
        </div>
    </div>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-orb"></div>
        <div class="hero-in">

            <!-- Heading -->
            <h1 data-r="up" data-d="2">
                Kelola Armada <em>Lebih Cerdas,</em><br>Lebih Efisien
            </h1>

            <!-- Description -->
            <p class="hero-desc" data-r="up" data-d="3">
                Drivora mempermudah pengajuan, persetujuan, dan penugasan kendaraan operasional
                dalam satu platform terintegrasi — transparan dan dapat dilacak secara real-time.
            </p>

            <!-- CTA Buttons -->
            <div class="hero-btns" data-r="up" data-d="4">
                <a href="{{ route('login') }}" class="btn-main">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    Masuk
                </a>
                <a href="#fitur" class="btn-ghost">Pelajari Fitur →</a>
            </div>

            <!-- Meta strip -->
            <div class="hero-meta" data-r="up" data-d="5">
                <div class="hero-meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Alur persetujuan terstruktur
                </div>
                <div class="hero-meta-div"></div>
                <div class="hero-meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Pantau unit real-time
                </div>
                <div class="hero-meta-div"></div>
                <div class="hero-meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Riwayat &amp; arsip lengkap
                </div>
            </div>

        </div>
    </section>

    <!-- FITUR -->
    <section class="sec-alt" id="fitur">
        <div class="sec-in">
            <div class="feat-hd">
                <div class="eyebrow" data-r="up">Fitur Utama</div>
                <h2 class="sec-h" data-r="up" data-d="1">Semua yang Dibutuhkan dalam Satu Platform</h2>
                <p class="sec-p" data-r="up" data-d="2">Dirancang khusus untuk kebutuhan operasional internal — dari pengajuan hingga pengembalian kendaraan.</p>
            </div>
            <div class="feat-grid">
                <div class="feat-card" data-r="up" data-d="1">
                    <div class="feat-ico"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                    <div class="feat-t">Pengajuan Mudah</div>
                    <div class="feat-d">Staff mengajukan peminjaman dengan mengisi tujuan, waktu, dan kebutuhan kendaraan. Kode booking otomatis dibuat untuk setiap pengajuan.</div>
                </div>
                <div class="feat-card" data-r="up" data-d="2">
                    <div class="feat-ico"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <div class="feat-t">Alur Persetujuan</div>
                    <div class="feat-d">Atasan menerima notifikasi dan dapat menyetujui atau menolak pengajuan dengan catatan — semua tercatat dalam audit trail.</div>
                </div>
                <div class="feat-card" data-r="up" data-d="3">
                    <div class="feat-ico"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM13 16l2-5h4l2 5H13z"/></svg></div>
                    <div class="feat-t">Dispatcher Cerdas</div>
                    <div class="feat-d">Admin GA menugaskan unit internal atau mengkoordinasikan vendor sewa luar langsung dari dashboard dengan tampilan yang intuitif.</div>
                </div>
                <div class="feat-card" data-r="up" data-d="1">
                    <div class="feat-ico"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></div>
                    <div class="feat-t">Pantau Real-time</div>
                    <div class="feat-d">Monitor unit yang sedang beroperasi secara langsung. Admin dapat menandai perjalanan selesai dan mencatat kondisi pengembalian.</div>
                </div>
                <div class="feat-card" data-r="up" data-d="2">
                    <div class="feat-ico"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg></div>
                    <div class="feat-t">Kelola Armada</div>
                    <div class="feat-d">Data lengkap kendaraan, status aset (tersedia, perawatan, disposal), dan riwayat penggunaan tersimpan rapi dan mudah dicari.</div>
                </div>
                <div class="feat-card" data-r="up" data-d="3">
                    <div class="feat-ico"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
                    <div class="feat-t">Riwayat Lengkap</div>
                    <div class="feat-d">Arsip seluruh perjalanan dengan filter pencarian berdasarkan kode, tanggal, unit, dan status — untuk keperluan pelaporan dan audit.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CARA KERJA -->
    <section class="sec" id="cara-kerja">
        <div class="sec-in">
            <div class="how-grid">
                <div>
                    <div class="eyebrow" data-r="up">Cara Kerja</div>
                    <h2 class="sec-h" data-r="up" data-d="1">Dari Pengajuan ke Perjalanan dalam 4 Langkah</h2>
                    <p class="sec-p" style="margin-bottom:clamp(28px,3vw,40px)" data-r="up" data-d="2">Proses yang terstruktur memastikan setiap peminjaman kendaraan berjalan tertib dan terdokumentasi.</p>
                    <div class="steps">
                        <div class="step" data-r="left" data-d="1">
                            <div class="step-n">1</div>
                            <div class="step-c">
                                <div class="step-t">Staff Mengajukan Permohonan</div>
                                <div class="step-d">Isi form pengajuan dengan tujuan, waktu keberangkatan, jumlah penumpang, dan kebutuhan kendaraan.</div>
                            </div>
                        </div>
                        <div class="step" data-r="left" data-d="2">
                            <div class="step-n">2</div>
                            <div class="step-c">
                                <div class="step-t">Atasan Memberikan Persetujuan</div>
                                <div class="step-d">Approver menerima tugas, mereview detail perjalanan, dan memutuskan untuk menyetujui atau menolak.</div>
                            </div>
                        </div>
                        <div class="step" data-r="left" data-d="3">
                            <div class="step-n">3</div>
                            <div class="step-c">
                                <div class="step-t">Admin GA Menugaskan Unit</div>
                                <div class="step-d">Admin memilih kendaraan dinas atau mengkoordinasikan vendor sewa luar, lalu menyiapkan unit untuk berangkat.</div>
                            </div>
                        </div>
                        <div class="step" data-r="left" data-d="4">
                            <div class="step-n">4</div>
                            <div class="step-c">
                                <div class="step-t">Perjalanan Selesai &amp; Tercatat</div>
                                <div class="step-d">Setelah kembali, admin menandai perjalanan selesai. Semua data masuk ke arsip riwayat secara otomatis.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flow-col">
                    <div class="flow-card" data-r="right" data-d="1">
                        <div class="flow-ico" style="background:#eff6ff"><svg fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                        <div>
                            <div class="flow-t">Staff</div>
                            <div class="flow-s">Mengajukan permohonan peminjaman</div>
                        </div>
                    </div>
                    <div class="flow-arr" data-r="right" data-d="2">↓</div>
                    <div class="flow-card" data-r="right" data-d="3">
                        <div class="flow-ico" style="background:#f5f3ff"><svg fill="none" stroke="#4f46e5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                        <div>
                            <div class="flow-t">Approver</div>
                            <div class="flow-s">Menyetujui atau menolak permohonan</div>
                        </div>
                    </div>
                    <div class="flow-arr" data-r="right" data-d="3">↓</div>
                    <div class="flow-card" data-r="right" data-d="4">
                        <div class="flow-ico" style="background:#eff6ff"><svg fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                        <div>
                            <div class="flow-t">Admin GA</div>
                            <div class="flow-s">Menugaskan unit &amp; menyiapkan keberangkatan</div>
                        </div>
                    </div>
                    <div class="flow-arr" data-r="right" data-d="4">↓</div>
                    <div class="flow-card" data-r="right" data-d="5">
                        <div class="flow-ico" style="background:#f0fdf4"><svg fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                        <div>
                            <div class="flow-t">Selesai &amp; Tercatat</div>
                            <div class="flow-s">Data masuk ke arsip riwayat perjalanan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PERAN -->
    <section class="sec-alt" id="peran">
        <div class="sec-in">
            <div class="roles-hd">
                <div class="eyebrow" data-r="up">Peran Pengguna</div>
                <h2 class="sec-h" data-r="up" data-d="1">Satu Sistem, Tiga Peran Berbeda</h2>
                <p class="sec-p" data-r="up" data-d="2">Setiap pengguna memiliki akses dan tanggung jawab yang disesuaikan dengan perannya dalam organisasi.</p>
            </div>
            <div class="roles-grid">
                <div class="role-card" data-r="up" data-d="2">
                    <div class="role-av" style="background:#eff6ff"><svg fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                    <div class="role-n">Staff</div>
                    <ul class="role-perms">
                        <li>Mengajukan peminjaman kendaraan</li>
                        <li>Memantau status pengajuan</li>
                        <li>Membatalkan pengajuan aktif</li>
                        <li>Melihat riwayat perjalanan sendiri</li>
                    </ul>
                </div>
                <div class="role-card" data-r="up" data-d="4">
                    <div class="role-av" style="background:#f5f3ff"><svg fill="none" stroke="#4f46e5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <div class="role-n">Approver</div>
                    <ul class="role-perms">
                        <li>Menerima tugas persetujuan</li>
                        <li>Menyetujui atau menolak pengajuan</li>
                        <li>Menambahkan catatan keputusan</li>
                        <li>Melihat riwayat persetujuan sendiri</li>
                    </ul>
                </div>
                <div class="role-card" data-r="up" data-d="6">
                    <div class="role-av" style="background:#eff6ff"><svg fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                    <div class="role-n">Admin GA</div>
                    <ul class="role-perms">
                        <li>Menugaskan unit kendaraan</li>
                        <li>Koordinasi vendor sewa luar</li>
                        <li>Monitor unit aktif real-time</li>
                        <li>Kelola data kendaraan &amp; user</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-wrap">
        <div class="cta-in">
            <h2 data-r="up">Siap Mulai Menggunakan Drivora?</h2>
            <p data-r="up" data-d="1">Masuk dengan akun yang sudah disiapkan oleh Admin GA instansi Anda.</p>
            <div class="cta-btns" data-r="up" data-d="2">
                <a href="{{ route('login') }}" class="btn-cta">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    Masuk Sekarang
                </a>
                <a href="#fitur" class="btn-cta-ol">Pelajari Fitur</a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="f-top"></div>
        <div class="f-in">
            <div class="f-grid">
                <div data-r="up">
                    <a href="#" class="logo"><span class="logo-name">Drivora</span></a>
                    <p class="f-desc">Sistem pengelolaan peminjaman kendaraan operasional yang efisien, transparan, dan terintegrasi untuk kebutuhan internal instansi.</p>
                </div>
                <div data-r="up" data-d="1">
                    <div class="f-col-t">Navigasi</div>
                    <ul class="f-links">
                        <li><a href="#fitur">Fitur Utama</a></li>
                        <li><a href="#cara-kerja">Cara Kerja</a></li>
                        <li><a href="#peran">Peran Pengguna</a></li>
                        <li><a href="{{ route('login') }}">Masuk</a></li>
                    </ul>
                </div>
                <div data-r="up" data-d="2">
                    <div class="f-col-t">Bantuan</div>
                    <div class="f-contact">Butuh bantuan teknis? Hubungi IT Support kami:</div>
                    <a href="mailto:support@drivora.id" class="f-email">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        support@drivora.id
                    </a>
                </div>
            </div>
            <div class="f-bot">
                <div class="f-copy">&copy; {{ date('Y') }} Drivora. Unit Pengelolaan Kendaraan.</div>
                <div class="f-mark">
                    <span style="width:5px;height:5px;background:#3b82f6;border-radius:50%;display:inline-block"></span>
                    <span>Internal Use Only</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            var loader  = document.getElementById('loader');
            var burger  = document.getElementById('burger');
            var mobMenu = document.getElementById('mobMenu');
            var nav     = document.querySelector('.nav');

            /* ── Loader dismiss ── */
            function init() {
                loader.classList.add('hide');
                document.querySelectorAll('.hero [data-r]').forEach(function (el) {
                    el.classList.add('on');
                });
            }
            if (document.readyState === 'complete') {
                setTimeout(init, 700);
            } else {
                window.addEventListener('load', function () { setTimeout(init, 700); });
            }

            /* ── Burger ── */
            var mobBackdrop = document.getElementById('mobBackdrop');
            burger.addEventListener('click', function () {
                var open = mobMenu.classList.toggle('open');
                burger.classList.toggle('open', open);
                mobBackdrop.classList.toggle('open', open);
                document.body.style.overflow = open ? 'hidden' : '';
            });
            window.closeMob = function () {
                mobMenu.classList.remove('open');
                burger.classList.remove('open');
                mobBackdrop.classList.remove('open');
                document.body.style.overflow = '';
            };

            /* ── Navbar on scroll ── */
            window.addEventListener('scroll', function () {
                if (window.scrollY > 40) {
                    nav.style.background  = 'rgba(255,255,255,0.97)';
                    nav.style.boxShadow   = '0 1px 12px rgba(0,0,0,0.07)';
                } else {
                    nav.style.background  = 'rgba(255,255,255,0.92)';
                    nav.style.boxShadow   = 'none';
                }
            }, { passive: true });

            /* ── Scroll reveal ── */
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) {
                        e.target.classList.add('on');
                        io.unobserve(e.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -36px 0px' });

            document.querySelectorAll('[data-r]').forEach(function (el) {
                if (!el.closest('.hero')) io.observe(el);
            });
        })();
    </script>
</body>
</html>