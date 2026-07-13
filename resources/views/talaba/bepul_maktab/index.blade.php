<x-layouts.sidebar>
    <x-slot:title>Bepul Maktab</x-slot:title>

    <style>
        .natija-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .natija-modal {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            width: 320px;
            animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popIn {
            from {
                transform: scale(0.5);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .ball-doira {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3C3489, #7F77DD);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 32px rgba(60, 52, 137, 0.25);
        }

        .ball-raqam {
            font-size: 44px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        .ball-sublabel {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.75);
            margin-top: 4px;
        }

        .natija-stat {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 1rem 0 1.5rem;
        }

        .natija-stat-val {
            font-size: 20px;
            font-weight: 700;
        }

        .natija-stat-lbl {
            font-size: 11px;
            color: #aaa;
        }
    </style>

    {{-- NATIJA MODAL --}}
    @if (session('natija'))
        @php $n = session('natija'); @endphp
        <div class="natija-overlay" id="natija-modal">
            <div class="natija-modal">
                <div style="font-size:15px; font-weight:600; color:#333; margin-bottom:1.2rem;">
                    🎉 Test yakunlandi!
                </div>
                <div class="ball-doira">
                    <div class="ball-raqam">{{ $n['ball'] }}</div>
                    <div class="ball-sublabel">/ {{ $n['max_ball'] }} ball</div>
                </div>
                <div class="natija-stat">
                    <div>
                        <div class="natija-stat-val" style="color:#27500A;">{{ $n['togri'] }}</div>
                        <div class="natija-stat-lbl">To'g'ri</div>
                    </div>
                    <div>
                        <div class="natija-stat-val" style="color:#791F1F;">{{ $n['notogri'] }}</div>
                        <div class="natija-stat-lbl">Noto'g'ri</div>
                    </div>
                    <div>
                        <div class="natija-stat-val" style="color:#3C3489;">{{ $n['foiz'] }}%</div>
                        <div class="natija-stat-lbl">Foiz</div>
                    </div>
                </div>
                <button onclick="document.getElementById('natija-modal').style.display='none'" class="ar-btn ar-btn-ok"
                    style="width:100%; justify-content:center; padding:10px;">
                    Yopish ✕
                </button>
            </div>
        </div>
    @endif

    <div class="oz-wrap">
        <div class="oz-title">Bepul Maktab</div>

        @forelse ($fanlar as $ariza)
            @php
                $bank = \App\Models\QuestionBank::where('subject_id', $ariza->subject_id)
                    ->where('bolim_id', $ariza->bolim_id)
                    ->where('tur', 'free')
                    ->first();

                $ishlangan = $bank
                    ? \App\Models\TestSession::where('user_id', auth()->id())
                        ->where('bank_id', $bank->id)
                        ->whereIn('status', ['finished', 'expired'])
                        ->count()
                    : 0;

                $qolganUrinish = $bank ? max(0, $bank->urinish - $ishlangan) : 0;

                $activeSession = $bank
                    ? \App\Models\TestSession::where('user_id', auth()->id())
                        ->where('bank_id', $bank->id)
                        ->where('status', 'active')
                        ->where('tugash_vaqti', '>', now())
                        ->first()
                    : null;

                $oxirgiTest = $bank
                    ? \App\Models\TestSession::where('user_id', auth()->id())
                        ->where('bank_id', $bank->id)
                        ->whereIn('status', ['finished', 'expired'])
                        ->latest()
                        ->first()
                    : null;
            @endphp

            <div
                style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:18px; margin-bottom:10px;">
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">

                    {{-- FAN INFO --}}
                    <div style="display:flex; align-items:center; gap:12px; flex:1;">
                        <div
                            style="width:44px; height:44px; border-radius:10px; background:#EEEDFE;
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="bx bx-book-open" style="font-size:22px; color:#3C3489;"></i>
                        </div>
                        <div>
                            <p style="font-size:15px; font-weight:600; color:#333; margin:0;">
                                {{ $ariza->subject->nomi ?? '—' }}
                            </p>
                            <p style="font-size:12px; color:#888; margin:0;">
                                {{ $ariza->bolim->nomi ?? '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- STATISTIKA --}}
                    <div style="display:flex; gap:20px; align-items:center; flex-wrap:wrap;">
                        <div style="text-align:center;">
                            <p style="font-size:11px; color:#aaa; margin:0;">Urinish</p>
                            <p
                                style="font-size:14px; font-weight:600; margin:0;
                                color: {{ $qolganUrinish > 0 ? '#27500A' : '#791F1F' }}">
                                {{ $ishlangan }} / {{ $bank->urinish ?? '—' }}
                            </p>
                        </div>
                        <div style="text-align:center;">
                            <p style="font-size:11px; color:#aaa; margin:0;">Vaqt</p>
                            <p style="font-size:14px; font-weight:600; color:#3C3489; margin:0;">
                                {{ $bank->vaqt_limit ?? '—' }} daq
                            </p>
                        </div>
                        <div style="text-align:center;">
                            <p style="font-size:11px; color:#aaa; margin:0;">Savollar</p>
                            <p style="font-size:14px; font-weight:600; color:#333; margin:0;">
                                {{ $bank->savollar_soni ?? '—' }} ta
                            </p>
                        </div>
                        <div style="text-align:center;">
                            <p style="font-size:11px; color:#aaa; margin:0;">Yakuniy ball</p>
                            <p style="font-size:14px; font-weight:600; color:#27500A; margin:0;">
                                {{ $ariza->yakuniy_baho ?? '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- TUGMA --}}
                    @if (!$bank)
                        <span class="ar-badge" style="background:#f5f5f5; color:#888;">
                            Bank biriktirilmagan
                        </span>
                    @else
                        @php
                            $hozir = now();
                            $haliOchilmagan =
                                $bank->boshlanish_vaqti && $hozir->lt(\Carbon\Carbon::parse($bank->boshlanish_vaqti));
                            $muddatTugagan =
                                $bank->tugash_vaqti && $hozir->gt(\Carbon\Carbon::parse($bank->tugash_vaqti));
                        @endphp

                        @if ($haliOchilmagan)
                            <div style="text-align:center;">
                                <span class="ar-badge" style="background:#fff3cd; color:#856404;">
                                    <i class="bx bx-time"></i> Hali boshlanmagan
                                </span>
                                <p style="font-size:11px; color:#aaa; margin:4px 0 0;">
                                    {{ \Carbon\Carbon::parse($bank->boshlanish_vaqti)->format('d.m.Y - (H:i)') }} dan
                                </p>
                            </div>
                        @elseif ($muddatTugagan)
                            <div style="text-align:center;">
                                <span class="ar-badge ar-badge-rej">
                                    <i class="bx bx-lock"></i> Muddat tugagan
                                </span>
                                <p style="font-size:11px; color:#aaa; margin:4px 0 0;">
                                    {{ \Carbon\Carbon::parse($bank->tugash_vaqti)->format('d.m.Y - (H:i)') }} da tugagan
                                </p>
                            </div>
                        @elseif ($activeSession)
                            <a href="{{ route('talaba.bepul_maktab.test', $activeSession->id) }}"
                                class="ar-btn ar-btn-ok">
                                <i class="bx bx-play"></i> Davom etish
                            </a>
                        @elseif ($qolganUrinish <= 0)
                            <span class="ar-badge ar-badge-rej">Urinishlar tugadi</span>
                        @else
                            <form action="{{ route('talaba.bepul_maktab.boshlash', $ariza->subject_id) }}"
                                method="POST">
                                @csrf
                                <button type="submit" class="ar-btn ar-btn-ok"
                                    onclick="return confirm('Testni boshlaysizmi?')">
                                    <i class="bx bx-play-circle"></i>
                                    {{ $ishlangan > 0 ? 'Qayta boshlash' : 'Testni boshlash' }}
                                </button>
                            </form>
                        @endif
                    @endif

                </div>

                {{-- OXIRGI TEST --}}
                @if ($oxirgiTest)
                    <div
                        style="margin-top:12px; padding-top:12px; border-top:1px solid #f5f5f5;
                        display:flex; gap:16px; flex-wrap:wrap; align-items:center;">
                        <span style="font-size:12px; color:#aaa;">
                            Oxirgi test:
                            <strong style="color:#333;">
                                {{ $oxirgiTest->boshlanish_vaqti?->format('d.m.Y H:i') }}
                            </strong>
                        </span>
                        <span style="font-size:12px; color:#aaa;">
                            Ball:
                            <strong style="color:#27500A;">{{ $oxirgiTest->ball }}</strong>
                        </span>
                        <span style="font-size:12px; color:#aaa;">
                            Holat:
                            @if ($oxirgiTest->status === 'finished')
                                <strong style="color:#27500A;">Yakunlangan</strong>
                            @else
                                <strong style="color:#791F1F;">Tugagan</strong>
                            @endif
                        </span>
                    </div>
                @endif

            </div>

        @empty
            <div style="text-align:center; padding:3rem; color:#888;">
                <i class="bx bx-book-open" style="font-size:48px; display:block; margin-bottom:12px; color:#ddd;"></i>
                <p style="font-size:14px;">Sizga biriktirilgan fanlar yo'q</p>
            </div>
        @endforelse

    </div>
</x-layouts.sidebar>
