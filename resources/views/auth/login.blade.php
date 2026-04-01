<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Drivora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue:     #2563eb;
            --blue-dk:  #1d4ed8;
            --gray-200: #e2e8f0;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-700: #334155;
            --gray-900: #0f172a;
            --expo: cubic-bezier(0.16, 1, 0.3, 1);
        }

        html, body { height: 100%; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a; /* gelap — cocok saat foto belum load */
            -webkit-font-smoothing: antialiased;
        }

        /* ── LAYOUT ──────────────────────────────── */
        .layout {
            display: flex;
            height: 100vh;
            min-height: 100vh;
            overflow: hidden;
        }

        /* ── PHOTO SIDE ──────────────────────────── */
        .photo-side {
            position: relative;
            flex: 1;
            overflow: hidden;
            display: none;
        }
        @media (min-width: 860px) { .photo-side { display: block; } }

        /*
         * Foto: TIDAK ada animasi delay — langsung visible dari frame pertama.
         * Browser decode & paint secepat mungkin.
         * Hanya ada subtle scale-down (1.03 → 1.0) yang berjalan paralel setelah paint,
         * bukan sebelumnya — jadi foto terlihat dulu, baru animate.
         */
        .photo-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transform: scale(1.03);
            animation: imgSettle 1.4s var(--expo) 0s forwards;
        }
        @keyframes imgSettle {
            to { transform: scale(1); }
        }

        /* Overlay — langsung ada, ikut foto */
        .photo-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg,
                    rgba(8,16,38,.50) 0%,
                    rgba(8,16,38,.16) 42%,
                    rgba(8,16,38,.80) 100%),
                linear-gradient(90deg,
                    transparent 55%,
                    rgba(248,250,252,.30) 100%);
        }

        /* Brand foto */
        .photo-brand {
            position: absolute;
            top: clamp(24px, 3vh, 40px);
            left: clamp(24px, 3vw, 44px);
            z-index: 4;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            opacity: 0;
            transform: translateY(-8px);
            animation: popIn .5s var(--expo) .2s forwards;
        }

        /* Quote */
        .photo-quote {
            position: absolute;
            bottom: clamp(32px, 5vh, 60px);
            left: clamp(24px, 3vw, 44px);
            z-index: 4;
            max-width: clamp(220px, 26vw, 380px);
            opacity: 0;
            transform: translateY(12px);
            animation: popIn .55s var(--expo) .28s forwards;
        }

        /* ── FORM SIDE ───────────────────────────── */
        /*
         * Form panel slide dari kanan — ini yang animate, bukan foto.
         * translateX + opacity = GPU-composited, zero layout shift.
         */
        .form-side {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: clamp(32px, 5vh, 60px) clamp(20px, 4vw, 56px);
            background: #f8fafc;
            overflow-y: auto;
            will-change: transform, opacity;
            opacity: 0;
            transform: translateX(40px);
            animation: panelIn .65s var(--expo) 0s forwards;
        }
        @media (min-width: 860px) {
            .form-side {
                width: clamp(360px, 38vw, 480px);
                flex-shrink: 0;
                border-left: 1px solid var(--gray-200);
            }
        }

        /* ── STAGGER FORM CARD ────────────────────── */
        .form-card { width: 100%; max-width: 360px; }

        .form-card > * {
            opacity: 0;
            transform: translateY(10px);
        }
        .form-card > *:nth-child(1) { animation: elIn .5s var(--expo) .18s forwards; }
        .form-card > *:nth-child(2) { animation: elIn .5s var(--expo) .25s forwards; }
        .form-card > *:nth-child(3) { animation: elIn .5s var(--expo) .32s forwards; }
        .form-card > *:nth-child(4) { animation: elIn .5s var(--expo) .39s forwards; }
        .form-card > *:nth-child(5) { animation: elIn .5s var(--expo) .46s forwards; }
        .form-card > *:nth-child(6) { animation: elIn .5s var(--expo) .53s forwards; }

        /* ── KEYFRAMES ───────────────────────────── */
        @keyframes panelIn { to { opacity: 1; transform: translateX(0); } }
        @keyframes popIn   { to { opacity: 1; transform: translateY(0); } }
        @keyframes elIn    { to { opacity: 1; transform: translateY(0); } }

        /* ── BRAND BOX ───────────────────────────── */
        .brand-name {
            font-size: clamp(15px, 1.3vw, 18px);
            font-weight: 800; color: #fff; letter-spacing: -.4px;
            text-shadow: 0 1px 10px rgba(0,0,0,.35);
        }

        /* Mobile brand */
        .mob-brand {
            display: flex; align-items: center; gap: 9px;
            text-decoration: none;
            margin-bottom: clamp(28px, 4vh, 44px);
        }
        @media (min-width: 860px) { .mob-brand { display: none; } }
        .mob-brand { width: 34px; height: 34px; border-radius: 9px; }
        .mob-brand .brand-name { font-size: 17px; font-weight: 800; color: var(--gray-900); letter-spacing: -.4px; text-shadow: none; }

        /* ── QUOTE ───────────────────────────────── */
        .quote-bar {
            width: 28px; height: 3px;
            background: var(--blue); border-radius: 2px;
            margin-bottom: 14px;
            box-shadow: 0 0 14px rgba(96, 165, 250, .7);
        }
        .quote-text {
            font-size: clamp(20px, 1.9vw, 28px);
            font-weight: 800; color: #fff;
            line-height: 1.26; letter-spacing: -.6px; margin-bottom: 10px;
            text-shadow: 0 2px 20px rgba(0,0,0,.55);
        }
        .quote-text em {
            font-style: normal;
            background: linear-gradient(90deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .quote-sub {
            font-size: clamp(12px, .9vw, 14px);
            color: rgba(255,255,255,.68); line-height: 1.65;
            text-shadow: 0 1px 8px rgba(0,0,0,.5);
        }

        /* ── FORM ELEMENTS ───────────────────────── */
        .form-hd { margin-bottom: clamp(24px, 3vh, 32px); }
        .form-title { font-size: clamp(22px, 2vw, 28px); font-weight: 800; color: var(--gray-900); letter-spacing: -.7px; margin-bottom: 5px; }
        .form-sub { font-size: clamp(12px, .95vw, 14px); color: var(--gray-500); }

        .alert-err {
            background: rgba(239,68,68,.07); border: 1px solid rgba(239,68,68,.2);
            color: #dc2626; border-radius: 10px; padding: 11px 13px; font-size: 13px;
            margin-bottom: 18px; display: flex; align-items: flex-start; gap: 8px;
        }
        .alert-err svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; }

        .form-group { margin-bottom: clamp(12px, 1.5vh, 16px); }
        .form-lbl { display: block; font-size: 11px; font-weight: 700; color: var(--gray-500); margin-bottom: 6px; letter-spacing: .5px; text-transform: uppercase; }
        .form-input {
            width: 100%;
            padding: clamp(10px, 1.2vh, 13px) clamp(12px, 1.1vw, 14px);
            border: 1.5px solid var(--gray-200); border-radius: 10px;
            font-size: clamp(13px, 1vw, 15px); font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--gray-900); background: #fff;
            transition: border-color .18s ease, box-shadow .18s ease;
            outline: none;
        }
        .form-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3.5px rgba(37,99,235,.13); }
        .form-input::placeholder { color: var(--gray-400); }
        .form-input.is-err { border-color: #ef4444; }
        .input-err { font-size: 12px; color: #dc2626; margin-top: 5px; }

        .inp-wrap { position: relative; }
        .toggle-pw {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: var(--gray-400);
            padding: 4px; display: flex; align-items: center; transition: color .15s;
        }
        .toggle-pw:hover { color: var(--gray-700); }
        .toggle-pw svg { width: 17px; height: 17px; }

        .form-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: clamp(18px, 2.5vh, 26px); }
        .remember { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .remember input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--blue); }
        .remember span { font-size: 13px; color: var(--gray-500); font-weight: 500; }

        .btn-submit {
            width: 100%;
            padding: clamp(11px, 1.4vh, 14px);
            background: var(--blue); color: #fff; border: none; border-radius: 10px;
            font-size: clamp(14px, 1.05vw, 15px); font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 20px rgba(37,99,235,.32);
            transition: background .2s ease, transform .15s ease, box-shadow .2s ease;
        }
        .btn-submit:hover { background: var(--blue-dk); transform: translateY(-2px); box-shadow: 0 8px 28px rgba(37,99,235,.42); }
        .btn-submit:active { transform: translateY(0); }

        .back-link {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            margin-top: clamp(14px, 2vh, 20px);
            font-size: 13px; color: var(--gray-400); text-decoration: none; transition: color .15s;
        }
        .back-link:hover { color: var(--gray-700); }
        .back-link svg { width: 13px; height: 13px; }

        .bot-badge {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            margin-top: clamp(18px, 2.5vh, 26px); padding-top: clamp(16px, 2vh, 22px);
            border-top: 1px solid var(--gray-200);
        }
        .bot-badge span { font-size: 11px; color: var(--gray-400); font-weight: 500; }
        .bd-dot { width: 4px; height: 4px; background: #3b82f6; border-radius: 50%; opacity: .45; }

        /* ── RESPONSIVE ──────────────────────────── */
        @media (max-width: 360px) { .form-side { padding: 24px 16px; } }

        @media (max-height: 560px) and (orientation: landscape) {
            .layout { height: auto; min-height: 100vh; }
            .form-side { overflow-y: auto; justify-content: flex-start; padding-top: 28px; }
            .photo-side { display: none; }
        }

        @media (min-width: 2560px) {
            .form-side { width: clamp(420px, 26vw, 580px); padding: clamp(52px, 6vh, 80px) clamp(52px, 5vw, 80px); }
            .photo-brand { left: clamp(52px, 4vw, 80px); top: clamp(36px, 3vh, 56px); }
            .photo-quote { left: clamp(52px, 4vw, 80px); bottom: clamp(44px, 4.5vh, 72px); max-width: clamp(280px, 20vw, 440px); }
            .form-card { max-width: 400px; }
        }
    </style>
</head>
<body>

<div class="layout">

    <!-- LEFT: Photo — tidak ada delay, langsung render -->
    <div class="photo-side">
        <img class="photo-img"
             src="{{ asset('images/car-login.jpg') }}"
             alt="" aria-hidden="true"
             loading="eager"
             fetchpriority="high"
             decoding="sync">
        <div class="photo-overlay"></div>

        <a href="{{ Route::has('landing') ? route('landing') : '/' }}" class="photo-brand">
            <span class="brand-name">Drivora</span>
        </a>

        <div class="photo-quote">
            <div class="quote-bar"></div>
            <div class="quote-text">Setiap perjalanan<br><em>tercatat dengan baik.</em></div>
            <div class="quote-sub">Sistem manajemen kendaraan operasional<br>yang efisien dan transparan.</div>
        </div>
    </div>

    <!-- RIGHT: Form — ini yang slide in -->
    <div class="form-side">

        <a href="{{ Route::has('landing') ? route('landing') : '/' }}" class="mob-brand">
            <span class="brand-name">Drivora</span>
        </a>

        <div class="form-card">

            <div class="form-hd">
                <h1 class="form-title">Selamat Datang</h1>
                <p class="form-sub">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            @if ($errors->any())
            <div class="alert-err">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>{{ $errors->first() }}</div>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-lbl">Email</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           autocomplete="email" required autofocus
                           class="form-input {{ $errors->has('email') ? 'is-err' : '' }}">
                    @error('email') <div class="input-err">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-lbl">Password</label>
                    <div class="inp-wrap">
                        <input id="password" type="password" name="password"
                                autocomplete="current-password" required
                                style="padding-right:44px"
                               class="form-input {{ $errors->has('password') ? 'is-err' : '' }}">
                        <button type="button" class="toggle-pw" onclick="togglePw()" tabindex="-1" aria-label="Tampilkan password">
                            <svg id="eye" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <div class="input-err">{{ $message }}</div> @enderror
                </div>

                <div class="form-row">
                    <label class="remember">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    Masuk
                </button>
            </form>

            @if (Route::has('landing'))
                <a href="{{ route('landing') }}" class="back-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            @else
                <a href="/" class="back-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            @endif

            <div class="bot-badge">
                <div class="bd-dot"></div>
                <span>Sistem Internal — Hanya untuk pengguna terdaftar</span>
                <div class="bd-dot"></div>
            </div>

        </div>
    </div>

</div>

<script>
    window.togglePw = function () {
        var inp = document.getElementById('password');
        var eye = document.getElementById('eye');
        if (inp.type === 'password') {
            inp.type = 'text';
            eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
        } else {
            inp.type = 'password';
            eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    };
</script>

</body>
</html>