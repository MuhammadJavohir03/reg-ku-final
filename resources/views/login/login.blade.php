<x-layouts.sidebar>
    <x-slot:title>
        Kirish
    </x-slot:title>

    <style>
        :root {
            --lg-accent: #8B7CF6;
            --lg-accent-2: #6C5CE7;
            --lg-ink: #1b1830;
            --lg-muted: #857fa8;
            --lg-glass: rgba(255, 255, 255, 0.5);
            --lg-glass-strong: rgba(255, 255, 255, 0.7);
            --lg-glass-soft: rgba(255, 255, 255, 0.3);
            --lg-glass-border: rgba(255, 255, 255, 0.6);
        }

        /* ============ FON — to'liq ekran, harakatlanuvchi rangli tuman ============ */
        .lg-scene {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            background: #ffffff80;
        }

        .lg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.55;
        }

        .lg-blob-1 {
            width: 46vw;
            height: 46vw;
            background: radial-gradient(circle, #8B7CF6, transparent 70%);
            top: -12%;
            left: -8%;
            animation: lg-drift 16s ease-in-out infinite;
        }

        .lg-blob-2 {
            width: 40vw;
            height: 40vw;
            background: radial-gradient(circle, #6C5CE7, transparent 70%);
            bottom: -14%;
            right: -6%;
            animation: lg-drift 20s ease-in-out infinite reverse;
        }

        .lg-blob-3 {
            width: 30vw;
            height: 30vw;
            background: radial-gradient(circle, #10b981, transparent 70%);
            bottom: 10%;
            left: 18%;
            opacity: 0.28;
            animation: lg-drift 24s ease-in-out infinite;
        }

        .lg-grid-dots {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 26px 26px;
        }

        @keyframes lg-drift {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(4%, -6%) scale(1.08); }
        }

        /* ============ MARKAZIY JOYLASHUV ============ */
        .lg-stage {
            min-height: calc(100vh - 32px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            font-family: 'Poppins', sans-serif;
        }

        .lg-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 26px;
        }

        .lg-brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            background: linear-gradient(135deg, var(--lg-accent), var(--lg-accent-2));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(108, 92, 231, 0.4);
        }

        .lg-brand-icon i {
            font-size: 22px;
            color: #fff;
        }

        .lg-brand span {
            font-size: 15px;
            font-weight: 700;
            color: #000000;
            letter-spacing: 0.01em;
        }

        /* ============ SUZUVCHI SHISHA KARTA ============ */
        .lg-card {
            width: 100%;
            max-width: 420px;
            background: var(--lg-glass);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid var(--lg-glass-border);
            border-radius: 26px;
            box-shadow: 0 30px 70px rgba(16, 14, 36, 0.35);
            padding: 40px 34px;
        }

        .lg-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--lg-ink);
            margin-bottom: 4px;
            text-align: center;
        }

        .lg-sub {
            color: rgb(0, 0, 0);
            font-size: 13px;
            margin-bottom: 26px;
            text-align: center;
        }

        .lg-field {
            margin-bottom: 16px;
        }

        .lg-field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #59567a;
            margin-bottom: 6px;
        }

        .lg-input-box {
            position: relative;
        }

        .lg-input-box i.lg-input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9b97bd;
            font-size: 16px;
        }

        .lg-input {
            width: 100%;
            border: 1.5px solid rgba(0, 0, 0, 0.137);
            background: var(--lg-glass-soft);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 11px 14px 11px 40px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            color: var(--lg-ink);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .lg-input:focus {
            outline: none;
            border-color: var(--lg-accent);
            background: var(--lg-glass-strong);
            box-shadow: 0 0 0 4px rgba(139, 124, 246, 0.16);
        }

        .lg-input-box .lg-eye {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9b97bd;
            font-size: 17px;
        }

        .lg-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .lg-toggle {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            user-select: none;
        }

        .lg-toggle input {
            display: none;
        }

        .lg-toggle .track {
            width: 36px;
            height: 20px;
            border-radius: 999px;
            background: rgba(139, 124, 246, 0.18);
            border: 1px solid var(--lg-glass-border);
            position: relative;
            transition: background 0.25s ease;
            flex-shrink: 0;
        }

        .lg-toggle .track::after {
            content: '';
            position: absolute;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #fff;
            top: 2px;
            left: 2px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.18);
            transition: transform 0.25s ease;
        }

        .lg-toggle input:checked + .track {
            background: linear-gradient(135deg, var(--lg-accent), var(--lg-accent-2));
        }

        .lg-toggle input:checked + .track::after {
            transform: translateX(16px);
        }

        .lg-toggle span.lbl {
            font-size: 12.5px;
            color: #59567a;
            font-weight: 500;
        }

        .lg-forgot {
            font-size: 12px;
            color: var(--lg-accent-2);
            font-weight: 600;
            text-decoration: none;
        }

        .lg-submit {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            background: linear-gradient(135deg, var(--lg-accent), var(--lg-accent-2));
            color: #fff;
            font-size: 14.5px;
            font-weight: 600;
            padding: 13px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            box-shadow: 0 12px 26px rgba(108, 92, 231, 0.35);
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
        }

        .lg-submit:hover {
            transform: translateY(-2px);
            filter: brightness(1.06);
        }

        .lg-submit:active {
            transform: translateY(0);
        }

        .lg-submit i {
            font-size: 18px;
        }

        /* ============ STATISTIKA CHIPLARI (karta ostida) ============ */
        .lg-chips {
            display: flex;
            gap: 10px;
            margin-top: 22px;
            width: 100%;
            max-width: 420px;
        }

        .lg-chip {
            flex: 1;
            text-align: center;
            padding: 12px 8px;
            background: rgba(255, 255, 255, 0.452);
            box-shadow: 0 15px 35px rgba(16, 14, 36, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 14px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .lg-chip strong {
            color: #00000085;
            display: block;
            font-size: 18px;
            font-weight: 700;
            background: linear-gradient(135deg, #fff, #d8d5f5);
            background-clip: text;
        }

        .lg-chip span {
            font-size: 10.5px;
            color: #00000085;
        }

        /* ============ AYLANUVCHI IQTIBOS ============ */
        .lg-quote-wrap {
            margin-top: 20px;
            min-height: 20px;
        }

        .lg-quote {
            font-size: 12.5px;
            color: #cfccec;
            text-align: center;
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        @media (max-width: 480px) {
            .lg-card {
                padding: 32px 22px;
            }

            .lg-chips {
                flex-wrap: wrap;
            }
        }
    </style>

    <div class="lg-scene">
        <div class="lg-grid-dots"></div>
        <div class="lg-blob lg-blob-1"></div>
        <div class="lg-blob lg-blob-2"></div>
        <div class="lg-blob lg-blob-3"></div>
    </div>

    <div class="lg-stage">

        <div class="lg-brand">
            <div class="lg-brand-icon"><i class='bx bxs-graduation'></i></div>
            <span>Registrator Ofisi</span>
        </div>

        <div class="lg-card">
            <div class="lg-title">Xush kelibsiz</div>
            <div class="lg-sub">Davom etish uchun hisobingizga kiring</div>

            <form method="POST" action="{{ route('authenticate') }}">
                @csrf

                <div class="lg-field">
                    <label>Email</label>
                    <div class="lg-input-box">
                        <i class='bx bx-envelope lg-input-icon'></i>
                        <input class="lg-input" type="email" name="email" placeholder="example@mail.com" required>
                    </div>
                </div>

                <div class="lg-field">
                    <label>Parol</label>
                    <div class="lg-input-box">
                        <i class='bx bx-lock-alt lg-input-icon'></i>
                        <input class="lg-input" type="password" id="password" name="password" required>
                        <i class='bx bx-show lg-eye' id="eye" onclick="togglePassword()"></i>
                    </div>
                </div>

                <div class="lg-row">
                    <label class="lg-toggle">
                        <input type="checkbox" name="remember">
                        <span class="track"></span>
                        <span class="lbl">Eslab qolish</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="lg-forgot">Parolni unutdingizmi?</a>
                    @endif
                </div>

                <button type="submit" class="lg-submit">
                    <i class='bx bx-log-in'></i> Kirish
                </button>

            </form>
        </div>

        <div class="lg-chips">
            <div class="lg-chip">
                <strong>{{ $subjectCounts['subject'] }}</strong>
                <span>Fan</span>
            </div>
            <div class="lg-chip">
                <strong>{{ $userCounts['talaba'] }}</strong>
                <span>Talaba</span>
            </div>
            <div class="lg-chip">
                <strong>{{ $userCounts['teacher'] }}</strong>
                <span>O'qituvchi</span>
            </div>
        </div>

        <div class="lg-quote-wrap">
            <div class="lg-quote" id="quote">📚 Bilim — kelajak poydevori.</div>
        </div>

    </div>

    <script>
        function togglePassword() {
            let p = document.getElementById("password");
            let e = document.getElementById("eye");

            if (p.type === "password") {
                p.type = "text";
                e.className = "bx bx-hide lg-eye";
            } else {
                p.type = "password";
                e.className = "bx bx-show lg-eye";
            }
        }

        const quotes = [
            "📚 Bilim — kelajak poydevori.",
            "🎓 Har bir test sizni maqsadingizga yaqinlashtiradi.",
            "🚀 O'qish muvaffaqiyat kaliti.",
            "💡 Bugungi bilim — ertangi muvaffaqiyat."
        ];

        let qi = 0;
        const quoteEl = document.getElementById("quote");

        setInterval(function() {
            qi = (qi + 1) % quotes.length;
            quoteEl.style.opacity = 0;
            quoteEl.style.transform = "translateY(6px)";
            setTimeout(function() {
                quoteEl.textContent = quotes[qi];
                quoteEl.style.opacity = 1;
                quoteEl.style.transform = "translateY(0)";
            }, 350);
        }, 3200);
    </script>

</x-layouts.sidebar>