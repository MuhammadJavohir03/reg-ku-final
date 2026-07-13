<x-layouts.sidebar>
    <x-slot:title>Natija</x-slot:title>

    <style>
        .natija-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }

        .natija-modal {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            width: 340px;
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
            width: 140px;
            height: 140px;
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
            font-size: 42px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        .ball-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 4px;
        }

        .natija-stat {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin: 1rem 0 1.5rem;
        }

        .natija-stat-item {
            text-align: center;
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
    <div class="natija-overlay" id="natija-modal">
        <div class="natija-modal">
            <div style="font-size:15px; font-weight:600; color:#333; margin-bottom:1.2rem;">
                Test yakunlandi! 🎉
            </div>

            <div class="ball-doira">
                <div class="ball-raqam">{{ $attempt->ball }}</div>
                <div class="ball-label">ball</div>
            </div>

            <div class="natija-stat">
                <div class="natija-stat-item">
                    <div class="natija-stat-val" style="color:#27500A;">{{ $togriSoni }}</div>
                    <div class="natija-stat-lbl">To'g'ri</div>
                </div>
                <div class="natija-stat-item">
                    <div class="natija-stat-val" style="color:#791F1F;">{{ $notogriSoni }}</div>
                    <div class="natija-stat-lbl">Noto'g'ri</div>
                </div>
                <div class="natija-stat-item">
                    <div class="natija-stat-val" style="color:#3C3489;">{{ $foiz }}%</div>
                    <div class="natija-stat-lbl">Foiz</div>
                </div>
                <div class="natija-stat-item">
                    <div class="natija-stat-val" style="color:#888;">{{ $maxBall }}</div>
                    <div class="natija-stat-lbl">Max ball</div>
                </div>
            </div>

            <button onclick="document.getElementById('natija-modal').style.display='none'" class="ar-btn ar-btn-ok"
                style="width:100%; justify-content:center; padding:10px;">
                Tahlilni ko'rish →
            </button>
        </div>
    </div>

    {{-- JAVOBLAR TAHLILI --}}
    <div class="oz-wrap">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
            <a href="{{ route('talaba.mini_maktab.index') }}" class="ar-btn">← Orqaga</a>
            <div class="oz-title" style="margin:0;">Javoblar tahlili</div>
        </div>

        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:50px;">№</th>
                        <th>Savol</th>
                        <th style="width:180px;">Sizning javobingiz</th>
                        <th style="width:180px;">To'g'ri javob</th>
                        <th style="width:70px;">Natija</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attempt->questionUsers as $i => $qu)
                        <tr>
                            <td class="ar-id">{{ $i + 1 }}</td>
                            <td style="white-space:normal; font-size:13px;">
                                {{ $qu->question->savol }}
                            </td>
                            <td style="font-size:13px;">
                                @if ($qu->tanlov)
                                    <span
                                        style="background:#EEEDFE; color:#3C3489; border-radius:4px;
                                        padding:1px 6px; font-size:11px; font-weight:700;">
                                        {{ $qu->tanlov }}
                                    </span>
                                    <span style="color: {{ $qu->status ? '#27500A' : '#791F1F' }}">
                                        {{ $qu->question->{'variant_' . $qu->tanlov} }}
                                    </span>
                                @else
                                    <span style="color:#aaa;">Javob berilmagan</span>
                                @endif
                            </td>
                            <td style="font-size:13px; color:#27500A; font-weight:500;">
                                <span
                                    style="background:#EAF3DE; color:#27500A; border-radius:4px;
                                    padding:1px 6px; font-size:11px; font-weight:700;">
                                    {{ $qu->question->togri_javob }}
                                </span>
                                {{ $qu->question->{'variant_' . $qu->question->togri_javob} }}
                            </td>
                            <td>
                                @if ($qu->status)
                                    <span class="ar-badge ar-badge-ok">✓</span>
                                @else
                                    <span class="ar-badge ar-badge-rej">✕</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.sidebar>
