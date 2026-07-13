<x-layouts.sidebar>
    <x-slot:title>
        Kirish
    </x-slot:title>

    <style>
        .login-wrap {
            max-width: 1000px;
            margin: 30px auto;
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 30px;
            align-items: center;
        }

        .login-left {
            background: linear-gradient(135deg, #3C3489, #5b51c8);
            color: #fff;
            border-radius: 18px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            min-height: 520px;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            background: rgba(255, 255, 255, .08);
            border-radius: 50%;
            right: -60px;
            top: -60px;
        }

        .login-left::after {
            content: '';
            position: absolute;
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, .06);
            border-radius: 50%;
            left: -40px;
            bottom: -40px;
        }

        .login-left img {
            width: 90%;
            display: block;
            margin: auto;
            animation: float 4s ease-in-out infinite;
        }

        .login-card {
            background: #fff;
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, .08);
        }

        .login-title {
            font-size: 28px;
            font-weight: 700;
            color: #3C3489;
        }

        .login-sub {
            color: #777;
            margin-bottom: 25px;
        }

        .login-input {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 12px 15px;
            transition: .3s;
        }

        .login-input:focus {
            outline: none;
            border-color: #3C3489;
            box-shadow: 0 0 0 4px rgba(60, 52, 137, .12);
        }

        .password-box {
            position: relative;
        }

        .password-box i {
            position: absolute;
            right: 15px;
            top: 14px;
            cursor: pointer;
            color: #666;
        }

        .stats {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .stats div {
            flex: 1;
            background: rgba(255, 255, 255, .15);
            padding: 12px;
            border-radius: 12px;
            text-align: center;
        }

        .quote {
            margin-top: 20px;
            font-size: 14px;
            opacity: .9;
        }

        .student-img {
            animation: float 5s ease-in-out infinite;
        }

        .kirish-btn {
            width: 100%;
            font-size: 22px;
            /* text kattaligi */
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            /* icon va text orasida masofa */
        }

        .kirish-btn .icon {
            font-size: 28px;
            /* icon kattaligi */
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }

        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        @media(max-width:992px) {
            .login-left {
                display: none;
            }

            .login-wrap {
                grid-template-columns: 1fr;
            }

            .login-card {
                max-width: 430px;
                margin: auto;
            }
        }
    </style>

    <div class="login-wrap">

        <div class="login-left">

            <h2>Registrator Office</h2>

            <p>
                Talabalar uchun yagona axborot tizimi.
                Fanlar, testlar va arizalarni bir joydan boshqaring.
            </p>

            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135755.png">

            <div class="stats">
                <div>
                    <h4>{{ $subjectCounts['subject'] }}</h4>
                    Fan
                </div>

                <div>
                    <h4>{{ $userCounts['talaba'] }}</h4>
                    Talaba
                </div>

                <div>
                    <h4>{{ $userCounts['teacher'] }}</h4>
                    O'qituvchi
                </div>
            </div>

            <div class="quote" id="quote">
                📚 Bilim — kelajak poydevori.
            </div>

        </div>

        <div class="login-card">

            <div style="text-align:center;margin-bottom:25px;">
                <i class='bx bxs-graduation' style="font-size:70px;color:#3C3489;"></i>

                <div class="login-title">
                    Xush kelibsiz
                </div>

                <div class="login-sub">
                    Hisobingizga kiring
                </div>
            </div>

            <form method="POST" action="{{ route('authenticate') }}">
                @csrf

                <div class="mb-3">
                    <label>Email</label>

                    <input class="login-input" type="email" name="email" placeholder="example@mail.com" required>
                </div>

                <div class="mb-3">

                    <label>Parol</label>

                    <div class="password-box">

                        <input class="login-input" type="password" id="password" name="password" required>

                        <i class='bx bx-show' id="eye" onclick="togglePassword()"></i>

                    </div>

                </div>

                <div class="mb-4">
                    <label>
                        <input type="checkbox" name="remember">
                        Eslab qolish
                    </label>
                </div>

                <button class="ar-btn p-4 ar-btn-ok kirish-btn">
                    <i class='bx bx-log-in icon'></i>
                    Kirish
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
                e.className = "bx bx-hide";

            } else {

                p.type = "password";
                e.className = "bx bx-show";

            }

        }

        const quotes = [
            "📚 Bilim — kelajak poydevori.",
            "🎓 Har bir test sizni maqsadingizga yaqinlashtiradi.",
            "🚀 O'qish muvaffaqiyat kaliti.",
            "💡 Bugungi bilim — ertangi muvaffaqiyat."
        ];

        let i = 0;

        setInterval(function() {

            i = (i + 1) % quotes.length;

            document.getElementById("quote").innerHTML = quotes[i];

        }, 3000);
    </script>

</x-layouts.sidebar>
