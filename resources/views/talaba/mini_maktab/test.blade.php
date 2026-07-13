<x-layouts.sidebar>
    <x-slot:title>Test — {{ $subject->nomi }}</x-slot:title>

    <style>
        .savol-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1.5px solid #ddd;
            background: #fff;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            color: #333;
        }

        .savol-btn:hover {
            background: #EEEDFE;
            border-color: #7F77DD;
        }

        .savol-btn.active {
            background: #3C3489;
            color: #fff;
            border-color: #3C3489;
        }

        .savol-btn.answered {
            background: #EAF3DE;
            border-color: #3B6D11;
            color: #27500A;
        }

        .savol-btn.answered.active {
            background: #27500A;
            color: #fff;
            border-color: #27500A;
        }

        .variant-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border: 1.5px solid #e5e5e5;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.15s;
            background: #fff;
        }

        .variant-label:hover {
            border-color: #7F77DD;
            background: #FAFAFE;
        }

        .variant-label.selected {
            border-color: #3C3489;
            background: #EEEDFE;
        }

        .savol-card {
            display: none;
        }

        .savol-card.faol {
            display: block;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        /* ============ OGOHLANTIRISH OVERLAY — POLICE CHIROQLARI ============ */
        #leave-warning-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: #1a1a1a;
            z-index: 99999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            padding: 24px;
            overflow: hidden;
        }

        #leave-warning-overlay.faol {
            display: flex;
        }

        /* Qizil-ko'k chaqnovchi fon qatlami */
        #leave-warning-overlay::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 0;
            animation: police-flash 0.5s infinite steps(1);
        }

        @keyframes police-flash {

            0%,
            49% {
                background: radial-gradient(circle at 30% 50%, rgba(220, 20, 20, 0.85) 0%, rgba(20, 0, 0, 0.95) 70%);
            }

            50%,
            100% {
                background: radial-gradient(circle at 70% 50%, rgba(20, 60, 220, 0.85) 0%, rgba(0, 0, 20, 0.95) 70%);
            }
        }

        /* Overlay ichidagi kontent chaqnovchi fon ustida bo'lishi uchun */
        #leave-warning-overlay>* {
            position: relative;
            z-index: 1;
        }

        .siren-ring {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: siren-ring-pulse 0.5s infinite, ring-color-flash 0.5s infinite steps(1);
        }

        @keyframes ring-color-flash {

            0%,
            49% {
                box-shadow: 0 0 30px 10px rgba(255, 30, 30, 0.6);
            }

            50%,
            100% {
                box-shadow: 0 0 30px 10px rgba(30, 80, 255, 0.6);
            }
        }

        .siren-icon {
            font-size: 48px;
            animation: siren-shake 0.4s infinite;
        }

        @keyframes siren-ring-pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, .35);
            }

            70% {
                box-shadow: 0 0 0 25px rgba(255, 255, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        @keyframes siren-shake {

            0%,
            100% {
                transform: rotate(-6deg);
            }

            50% {
                transform: rotate(6deg);
            }
        }

        #leave-warning-text {
            color: #fff;
            font-size: 24px;
            font-weight: 800;
            margin-top: 22px;
            letter-spacing: .3px;
            text-shadow: 0 0 12px rgba(0, 0, 0, .8);
        }

        #leave-warning-sub {
            color: #f0f0f0;
            font-size: 14px;
            margin-top: 8px;
            max-width: 380px;
            line-height: 1.6;
            text-shadow: 0 0 8px rgba(0, 0, 0, .8);
        }

        #leave-away-timer {
            color: #fff;
            font-size: 46px;
            font-weight: 800;
            margin-top: 20px;
            font-variant-numeric: tabular-nums;
            background: rgba(0, 0, 0, .35);
            padding: 8px 28px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, .2);
        }

        #leave-warning-count {
            margin-top: 16px;
            font-size: 13px;
            color: #ffdede;
            background: rgba(0, 0, 0, .35);
            padding: 6px 14px;
            border-radius: 20px;
        }

        #violation-badge {
            position: fixed;
            bottom: 16px;
            right: 16px;
            background: #791F1F;
            color: #fff;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            z-index: 200;
            display: none;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 14px rgba(121, 31, 31, .35);
        }

        #violation-badge.korinadi {
            display: flex;
        }
    </style>

    {{-- STICKY HEADER --}}
    <div
        style="position:sticky; top:0; z-index:50; background:#fff;
        border-bottom:1px solid #f0f0f0; padding:10px 0; margin-bottom:1rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">

            <div style="font-size:13px; font-weight:500; color:#333;">
                {{ $bolim->nomi }} — {{ $subject->nomi }}
            </div>

            {{-- TIMER --}}
            <div style="display:flex; align-items:center; gap:8px; background:#EEEDFE;
                padding:8px 16px; border-radius:10px;"
                id="timer-wrap">
                <span style="font-size:12px; color:#534AB7;">⏱ Qolgan vaqt:</span>
                <span id="timer"
                    style="font-size:20px; font-weight:700; color:#3C3489;
                    min-width:56px; text-align:center;">
                    {{ gmdate('i:s', (int) $qolganVaqt) }}
                </span>
            </div>

            {{-- PROGRESS --}}
            <div style="font-size:12px; color:#888;">
                <span id="javob-soni">0</span> / {{ $savollar->count() }} javoblandi
            </div>

        </div>

        {{-- SAVOL NAVIGATSIYA --}}
        <div style="display:flex; flex-wrap:wrap; gap:6px; margin-top:10px;">
            @foreach ($savollar as $i => $answer)
                <button type="button" class="savol-btn {{ $i === 0 ? 'active' : '' }}" id="nav-btn-{{ $i }}"
                    onclick="savolKorsat({{ $i }})">
                    {{ $i + 1 }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- TEST FORMA --}}
    <form action="{{ route('talaba.mini_maktab.yuborish', $attempt->id) }}" method="POST" id="test-form">
        @csrf

        @foreach ($savollar as $i => $answer)
            <div class="savol-card {{ $i === 0 ? 'faol' : '' }}" id="savol-{{ $i }}">
                <div
                    style="background:#fff; border:1px solid #f0f0f0; border-radius:12px;
                    padding:1.5rem; margin-bottom:1rem;">

                    <div style="font-size:11px; color:#aaa; margin-bottom:8px;">
                        {{ $i + 1 }} / {{ $savollar->count() }} savol
                    </div>

                    <div
                        style="font-size:15px; font-weight:500; color:#333;
                        margin-bottom:1.5rem; line-height:1.7;">
                        {{ $answer->question->savol }}
                    </div>

                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach (['1', '2', '3', '4', '5'] as $num)
                            @if ($answer->question->{'variant_' . $num})
                                <label class="variant-label" id="label-{{ $i }}-{{ $num }}"
                                    onclick="variantTanla({{ $i }}, '{{ $num }}')">
                                    <input type="radio" name="javob_{{ $answer->question_id }}"
                                        value="{{ $num }}"
                                        id="radio-{{ $i }}-{{ $num }}" style="display:none;">
                                    <span
                                        style="background:#EEEDFE; color:#3C3489; border-radius:6px;
                                        padding:3px 10px; font-size:12px; font-weight:700; flex-shrink:0;">
                                        {{ $num }}
                                    </span>
                                    <span>{{ $answer->question->{'variant_' . $num} }}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>

                </div>

                {{-- NAVIGATSIYA --}}
                <div style="display:flex; justify-content:space-between; margin-bottom:1rem;">
                    @if ($i > 0)
                        <button type="button" class="ar-btn" onclick="savolKorsat({{ $i - 1 }})">←
                            Oldingi</button>
                    @else
                        <div></div>
                    @endif

                    @if ($i < $savollar->count() - 1)
                        <button type="button" class="ar-btn ar-btn-ok"
                            onclick="savolKorsat({{ $i + 1 }})">Keyingi →</button>
                    @else
                        <button type="button" class="ar-btn ar-btn-ok" onclick="testniYakunlash()">
                            ✓ Testni yakunlash
                        </button>
                    @endif
                </div>
            </div>
        @endforeach

    </form>

    {{-- OGOHLANTIRISH OVERLAY --}}
    <div id="leave-warning-overlay">
        <div class="siren-ring">
            <div class="siren-icon">🚨</div>
        </div>
        <div id="leave-warning-text">DIQQAT! Siz testni tark etdingiz</div>
        <div id="leave-warning-sub">Darhol sahifaga qayting. Tark etishlar soni hisobga olinmoqda va bu urinishingizga
            ta'sir qilishi mumkin.</div>
        <div id="leave-away-timer">00:00</div>
        <div id="leave-warning-count">Jami tark etishlar: <span id="warning-count-inline">0</span> marta</div>
    </div>

    {{-- BUZILISHLAR HISOBI (kichik indikator) --}}
    <div id="violation-badge">
        ⚠️ Tark etishlar: <span id="violation-count">0</span> marta
    </div>

    <script>
        let joriySavol = 0;
        const jami = {{ $savollar->count() }};
        const javoblar = {};
        let testYakunlandi = false;

        function savolKorsat(index) {
            document.querySelectorAll('.savol-card').forEach(el => el.classList.remove('faol'));
            document.querySelectorAll('.savol-btn').forEach(el => el.classList.remove('active'));
            document.getElementById('savol-' + index).classList.add('faol');
            document.getElementById('nav-btn-' + index).classList.add('active');
            joriySavol = index;
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function variantTanla(savolIndex, num) {
            const radio = document.getElementById('radio-' + savolIndex + '-' + num);
            if (!radio) return;
            radio.checked = true;

            ['1', '2', '3', '4', '5'].forEach(n => {
                const label = document.getElementById('label-' + savolIndex + '-' + n);
                if (label) label.classList.remove('selected');
            });

            const selectedLabel = document.getElementById('label-' + savolIndex + '-' + num);
            if (selectedLabel) selectedLabel.classList.add('selected');

            const btn = document.getElementById('nav-btn-' + savolIndex);
            if (btn) btn.classList.add('answered');

            javoblar[savolIndex] = num;
            document.getElementById('javob-soni').textContent = Object.keys(javoblar).length;

            if (savolIndex < jami - 1) {
                setTimeout(() => savolKorsat(savolIndex + 1), 600);
            }
        }

        function timerFormat(sec) {
            const m = String(Math.floor(sec / 60)).padStart(2, '0');
            const s = String(sec % 60).padStart(2, '0');
            return m + ':' + s;
        }

        function testYuborish() {
            if (testYakunlandi) return;
            testYakunlandi = true;
            clearInterval(timerInterval);
            sirenToxtatish();
            document.getElementById('test-form').submit();
        }

        function testniYakunlash() {
            const javobBerilmagan = jami - Object.keys(javoblar).length;
            if (javobBerilmagan > 0) {
                if (!confirm(javobBerilmagan + ' ta savolga javob berilmagan. Baribir yakunlaysizmi?')) return;
            } else {
                if (!confirm('Testni yakunlashni tasdiqlaysizmi?')) return;
            }
            testYuborish();
        }

        // TIMER
        let seconds = {{ (int) $qolganVaqt }};
        const timerEl = document.getElementById('timer');
        const timerWrap = document.getElementById('timer-wrap');

        timerEl.textContent = timerFormat(seconds);

        const timerInterval = setInterval(() => {
            seconds--;

            if (seconds <= 0) {
                clearInterval(timerInterval);
                timerEl.textContent = '00:00';
                testYuborish();
                return;
            }

            timerEl.textContent = timerFormat(seconds);

            if (seconds <= 60) {
                timerEl.style.color = '#791F1F';
                timerWrap.style.background = '#FCEBEB';
            }

            if (seconds <= 30) {
                timerEl.style.animation = 'pulse 0.5s infinite';
            }

        }, 1000);

        // Sahifadan chiqishda beacon
        window.addEventListener('beforeunload', function() {
            if (!testYakunlandi) {
                navigator.sendBeacon(
                    '{{ route('talaba.mini_maktab.yuborish', $attempt->id) }}',
                    new URLSearchParams({
                        _token: '{{ csrf_token() }}'
                    })
                );
            }
        });

        // ============================================================
        // SIRENA OVOZI + TARK ETISH OGOHLANTIRISHI
        // ============================================================
        let audioCtx = null;
        let sirenOscillator = null;
        let sirenGain = null;
        let sirenLfoInterval = null;
        let audioResumeQilingan = false;
        let vahimaliRejim = false;

        // Brauzerlar audio kontekstni foydalanuvchi bosishidan oldin bloklaydi.
        function audioContextTayyorla() {
            if (audioResumeQilingan) return;
            audioCtx = audioCtx || new(window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            audioResumeQilingan = true;
        }

        document.addEventListener('click', audioContextTayyorla, {
            once: true
        });
        document.addEventListener('keydown', audioContextTayyorla, {
            once: true
        });

        function sirenBoshlash() {
            audioContextTayyorla();
            if (!audioCtx) return;
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            if (sirenOscillator) return; // allaqachon chalinyapti

            sirenOscillator = audioCtx.createOscillator();
            sirenGain = audioCtx.createGain();

            sirenOscillator.type = 'sawtooth'; // police sirenaga xos keskinroq tembr
            sirenGain.gain.value = 0.18;

            sirenOscillator.connect(sirenGain);
            sirenGain.connect(audioCtx.destination);
            sirenOscillator.start();

            // POLICE "WAIL" EFFEKTI — tekis ko'tarilib-tushadigan chastota
            const minFreq = 500;
            const maxFreq = 1200;
            const davomiylik = 1.1;

            function sirenaToTegishli() {
                if (!sirenOscillator) return;
                const now = audioCtx.currentTime;
                sirenOscillator.frequency.cancelScheduledValues(now);
                sirenOscillator.frequency.setValueAtTime(minFreq, now);
                sirenOscillator.frequency.linearRampToValueAtTime(maxFreq, now + davomiylik / 2);
                sirenOscillator.frequency.linearRampToValueAtTime(minFreq, now + davomiylik);
            }

            sirenaToTegishli();
            sirenLfoInterval = setInterval(sirenaToTegishli, davomiylik * 1000);
        }

        // 5 soniyadan keyin chaqiriladi — ovozni tezroq va keskinroq qiladi
        function sirenVahimaliQil() {
            if (!sirenOscillator || vahimaliRejim) return;
            vahimaliRejim = true;

            sirenGain.gain.setValueAtTime(0.32, audioCtx.currentTime);
            sirenOscillator.type = 'square'; // yanada keskin, bezovta qiluvchi tembr

            if (sirenLfoInterval) {
                clearInterval(sirenLfoInterval);
            }

            const minFreq = 400;
            const maxFreq = 1500;
            const davomiylik = 0.35; // tezroq tebranish = vahimaliroq

            function tezSirena() {
                if (!sirenOscillator) return;
                const now = audioCtx.currentTime;
                sirenOscillator.frequency.cancelScheduledValues(now);
                sirenOscillator.frequency.setValueAtTime(minFreq, now);
                sirenOscillator.frequency.linearRampToValueAtTime(maxFreq, now + davomiylik / 2);
                sirenOscillator.frequency.linearRampToValueAtTime(minFreq, now + davomiylik);
            }

            tezSirena();
            sirenLfoInterval = setInterval(tezSirena, davomiylik * 1000);
        }

        function sirenToxtatish() {
            if (sirenLfoInterval) {
                clearInterval(sirenLfoInterval);
                sirenLfoInterval = null;
            }
            if (sirenOscillator) {
                try {
                    sirenOscillator.stop();
                } catch (e) {}
                try {
                    sirenOscillator.disconnect();
                    sirenGain.disconnect();
                } catch (e) {}
                sirenOscillator = null;
                sirenGain = null;
            }
            vahimaliRejim = false;
        }

        // ---- Tark etish holati (bitta manba orqali boshqariladi) ----
        let tarkEtganHolat = false;
        let tarkEtishSoni = 0;
        let tarkEtganPayt = null;
        let awayInterval = null;

        const overlay = document.getElementById('leave-warning-overlay');
        const awayTimerEl = document.getElementById('leave-away-timer');
        const violationBadge = document.getElementById('violation-badge');
        const violationCountEl = document.getElementById('violation-count');
        const warningCountInline = document.getElementById('warning-count-inline');

        function formatAwayTime(sec) {
            const m = String(Math.floor(sec / 60)).padStart(2, '0');
            const s = String(sec % 60).padStart(2, '0');
            return m + ':' + s;
        }

        function testniTarkEtdi() {
            if (testYakunlandi) return;
            if (tarkEtganHolat) return; // qayta-qayta ishga tushmasin
            tarkEtganHolat = true;

            tarkEtishSoni++;

            violationCountEl.textContent = tarkEtishSoni;
            warningCountInline.textContent = tarkEtishSoni;
            violationBadge.classList.add('korinadi');

            // 3 MARTA TARK ETSA — TESTNI AVTOMATIK YAKUNLASH
            if (tarkEtishSoni >= 3) {
                overlay.classList.add('faol');
                document.getElementById('leave-warning-text').textContent = 'Test avtomatik yakunlanmoqda...';
                document.getElementById('leave-warning-sub').textContent =
                    'Siz sahifani 3 martadan ortiq tark etdingiz. Urinishingiz endi yopilmoqda.';
                document.getElementById('leave-away-timer').style.display = 'none';
                sirenBoshlash();

                setTimeout(() => {
                    testYuborish();
                }, 5000);

                return;
            }

            document.getElementById('leave-away-timer').style.display = '';
            document.getElementById('leave-warning-text').textContent = 'DIQQAT! Siz testni tark etdingiz';
            document.getElementById('leave-warning-sub').textContent =
                'Darhol sahifaga qayting. Tark etishlar soni hisobga olinmoqda va bu urinishingizga ta\'sir qilishi mumkin.';

            tarkEtganPayt = Date.now();
            overlay.classList.add('faol');
            sirenBoshlash();

            if (awayInterval) clearInterval(awayInterval);
            awayInterval = setInterval(() => {
                const awaySec = Math.floor((Date.now() - tarkEtganPayt) / 1000);
                awayTimerEl.textContent = formatAwayTime(awaySec);

                // 5 SONIYADAN OSHSA — OVOZ VAHIMALIROQ BO'LADI
                if (awaySec >= 5) {
                    sirenVahimaliQil();
                    document.getElementById('leave-warning-text').textContent = '⚠️ OXIRGI OGOHLANTIRISH!';
                    document.getElementById('leave-warning-sub').textContent =
                        'Darhol qayting! 10 soniyadan keyin test avtomatik yakunlanadi.';
                }

                // 10 SONIYAGA YETSA — TESTNI AVTOMATIK YAKUNLASH
                if (awaySec >= 10) {
                    clearInterval(awayInterval);
                    awayInterval = null;
                    document.getElementById('leave-warning-text').textContent = 'Test yakunlanmoqda...';
                    document.getElementById('leave-warning-sub').textContent =
                        'Vaqt tugadi. Urinishingiz yopilmoqda.';
                    setTimeout(() => {
                        testYuborish();
                    }, 800);
                }
            }, 1000);
        }

        function testgaQaytdi() {
            if (!tarkEtganHolat) return;
            tarkEtganHolat = false;

            overlay.classList.remove('faol');
            sirenToxtatish();

            if (awayInterval) {
                clearInterval(awayInterval);
                awayInterval = null;
            }
            awayTimerEl.textContent = '00:00';
        }

        // Tab yashirilganda / minimize qilinganda
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                testniTarkEtdi();
            } else {
                testgaQaytdi();
            }
        });

        // Boshqa oynaga fokus o'tganda
        window.addEventListener('blur', function() {
            setTimeout(() => {
                if (!document.hasFocus() && !document.hidden) {
                    testniTarkEtdi();
                }
            }, 50);
        });

        window.addEventListener('focus', function() {
            testgaQaytdi();
        });
    </script>

</x-layouts.sidebar>
