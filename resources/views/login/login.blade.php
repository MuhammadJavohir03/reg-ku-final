<x-layouts.sidebar>
    <x-slot:title>
        Kirish
    </x-slot:title>

    <style>
        :root {
            --lg-accent: #7C6CF5;
            --lg-accent-2: #5B4FE0;
            --lg-ink: #1b1830;
            --lg-muted: #8b87a8;
            --lg-bg-1: #14122a;
            --lg-bg-2: #1e1a3d;
        }

        .lg-wrap {
            min-height: calc(100vh - 32px);
            display: grid;
            grid-template-columns: 1.05fr 460px;
            gap: 0;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(20, 18, 42, 0.16);
            font-family: 'Poppins', sans-serif;
        }

        /* ============ LEFT / HERO ============ */
        .lg-hero {
            position: relative;
            background: linear-gradient(160deg, var(--lg-bg-1) 0%, var(--lg-bg-2) 55%, var(--lg-accent-2) 140%);
            color: #fff;
            padding: 52px 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .lg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(2px);
            opacity: 0.5;
        }

        .lg-orb-1 {
            width: 280px;
            height: 280px;
            background: radial-gradient(circle at 30% 30%, rgba(124, 108, 245, 0.55), transparent 70%);
            top: -90px;
            right: -70px;
            animation: lg-float 7s ease-in-out infinite;
        }

        .lg-orb-2 {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.12), transparent 70%);
            bottom: -60px;
            left: -50px;
            animation: lg-float 9s ease-in-out infinite reverse;
        }

        .lg-grid-dots {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px);
            background-size: 22px 22px;
            mask-image: radial-gradient(circle at 30% 20%, black, transparent 70%);
        }

        @keyframes lg-float {

            0%,
            100% {
                transform: translateY(0) translateX(0);
            }

            50% {
                transform: translateY(-18px) translateX(10px);
            }
        }

        .lg-hero-top {
            position: relative;
            z-index: 2;
        }

        .lg-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 500;
            color: #d8d5f5;
            margin-bottom: 22px;
        }

        .lg-badge i {
            color: #a9f5c7;
            font-size: 14px;
        }

        .lg-hero h1 {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.25;
            max-width: 420px;
            margin-bottom: 14px;
        }

        .lg-hero p {
            color: #b7b3dd;
            max-width: 380px;
            font-size: 14px;
            line-height: 1.7;
        }

        .lg-stats {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 12px;
            margin: 34px 0 26px;
        }

        .lg-stat {
            flex: 1;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 16px 12px;
            text-align: center;
            backdrop-filter: blur(6px);
        }

        .lg-stat h4 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 2px;
            background: linear-gradient(135deg, #fff, #d8d5f5);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .lg-stat span {
            font-size: 11.5px;
            color: #a29ecf;
        }

        .lg-quote-wrap {
            position: relative;
            z-index: 2;
            min-height: 22px;
        }

        .lg-quote {
            font-size: 13.5px;
            color: #cfccec;
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        /* ============ RIGHT / FORM ============ */
        .lg-card {
            background: #fff;
            padding: 52px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .lg-card-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--lg-accent), var(--lg-accent-2));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(124, 108, 245, 0.32);
            margin-bottom: 18px;
        }

        .lg-card-icon i {
            font-size: 26px;
            color: #fff;
        }

        .lg-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--lg-ink);
            margin-bottom: 4px;
        }

        .lg-sub {
            color: var(--lg-muted);
            font-size: 13.5px;
            margin-bottom: 28px;
        }

        .lg-field {
            margin-bottom: 18px;
        }

        .lg-field label {
            display: block;
            font-size: 12.5px;
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
            color: #b7b3d6;
            font-size: 17px;
        }

        .lg-input {
            width: 100%;
            border: 1.5px solid #ece9f7;
            background: #fbfaff;
            border-radius: 12px;
            padding: 12px 15px 12px 42px;
            font-size: 13.5px;
            font-family: 'Poppins', sans-serif;
            color: var(--lg-ink);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .lg-input:focus {
            outline: none;
            border-color: var(--lg-accent);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(124, 108, 245, 0.12);
        }

        .lg-input-box .lg-eye {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9b97bd;
            font-size: 18px;
        }

        .lg-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 26px;
        }

        /* Toggle switch — "Eslab qolish" */
        .lg-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
        }

        .lg-toggle input {
            display: none;
        }

        .lg-toggle .track {
            width: 40px;
            height: 22px;
            border-radius: 999px;
            background: #e6e4f5;
            position: relative;
            transition: background 0.25s ease;
            flex-shrink: 0;
        }

        .lg-toggle .track::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            top: 3px;
            left: 3px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.18);
            transition: transform 0.25s ease;
        }

        .lg-toggle input:checked+.track {
            background: linear-gradient(135deg, var(--lg-accent), var(--lg-accent-2));
        }

        .lg-toggle input:checked+.track::after {
            transform: translateX(18px);
        }

        .lg-toggle span.lbl {
            font-size: 13px;
            color: #59567a;
            font-weight: 500;
        }

        .lg-forgot {
            font-size: 12.5px;
            color: var(--lg-accent-2);
            font-weight: 600;
            text-decoration: none;
        }

        .lg-submit {
            width: 100%;
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, var(--lg-accent), var(--lg-accent-2));
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            padding: 14px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 12px 26px rgba(124, 108, 245, 0.32);
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
        }

        .lg-submit:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
        }

        .lg-submit:active {
            transform: translateY(0);
        }

        .lg-submit i {
            font-size: 19px;
        }

        @media (max-width: 992px) {
            .lg-wrap {
                grid-template-columns: 1fr;
                border-radius: 20px;
            }

            .lg-hero {
                display: none;
            }

            .lg-card {
                padding: 40px 26px;
            }
        }
    </style>

    <div class="lg-wrap">

        <div class="lg-hero">
            <div class="lg-grid-dots"></div>
            <div class="lg-orb lg-orb-1"></div>
            <div class="lg-orb lg-orb-2"></div>

            <div class="lg-hero-top">
                <div class="lg-badge"><i class='bx bxs-check-circle'></i> Yagona axborot tizimi</div>
                <h1>Registrator Ofisi bilan hammasi bir joyda</h1>
                <p>
                    Fanlar, testlar va arizalarni boshqaring, natijalarni real vaqtda kuzating —
                    talabalar va o'qituvchilar uchun yagona platforma.
                </p>
            </div>

            <div>
                <div class="lg-stats">
                    <div class="lg-stat">
                        <h4>{{ $subjectCounts['subject'] }}</h4>
                        <span>Fan</span>
                    </div>
                    <div class="lg-stat">
                        <h4>{{ $userCounts['talaba'] }}</h4>
                        <span>Talaba</span>
                    </div>
                    <div class="lg-stat">
                        <h4>{{ $userCounts['teacher'] }}</h4>
                        <span>O'qituvchi</span>
                    </div>
                </div>

                <div class="lg-quote-wrap">
                    <div class="lg-quote" id="quote">📚 Bilim — kelajak poydevori.</div>
                </div>
            </div>
        </div>

        <div class="lg-card">

            <div class="lg-card-icon">
                <i class='bx bxs-graduation'></i>
            </div>

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