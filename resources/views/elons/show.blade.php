<x-layouts.sidebar>

    <x-slot:title>
        E'lon tafsilotlari
    </x-slot:title>

    <style>
        .elon-show-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .elon-show-stats {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }

        @media (max-width: 768px) {
            .elon-show-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .elon-show-stats {
                flex-wrap: wrap;
            }

            .elon-show-stats > div {
                min-width: calc(50% - 6px);
            }
        }
    </style>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
            <a href="{{ route('elons.index') }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i> Orqaga
            </a>

            @if (auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('elons.edit', $elon->id) }}" class="ar-btn">
                    <i class="bx bx-edit"></i> Tahrirlash
                </a>

                <form action="{{ route('elons.destroy', $elon->id) }}" method="POST"
                    onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ar-btn ar-btn-rej">
                        <i class="bx bx-trash"></i> O'chirish
                    </button>
                </form>
            @endif
        </div>

        <div class="elon-show-grid">

            {{-- CHAP TOMON: Rasm --}}
            <div>
                <div style="border-radius:12px; overflow:hidden; border:1px solid #f0f0f0; position:relative;">
                    <div style="aspect-ratio:4/3; background:#f5f5f5;">
                        <img src="{{ asset('storage/' . ($elon->photo ?? 'elons/default.png')) }}"
                            alt="E'lon rasmi" style="width:100%; height:100%; object-fit:cover; display:block;">
                    </div>

                    <div style="position:absolute; top:12px; right:12px;">
                        <span class="ar-badge" style="background:#d4edda; color:#155724;">
                            {{ $elon->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <div class="elon-show-stats">
                    <div style="flex:1; background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:14px; text-align:center;">
                        <i class="bx bx-id-card" style="font-size:20px; color:#3C3489;"></i>
                        <div style="font-weight:700; margin-top:4px;">{{ $elon->id }}</div>
                        <small style="color:#888;">E'lon ID</small>
                    </div>

                    <div style="flex:1; background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:14px; text-align:center;">
                        <i class="bx bx-calendar-event" style="font-size:20px; color:#10b981;"></i>
                        <div style="font-weight:700; margin-top:4px;">{{ $elon->created_at->format('d.m.Y') }}</div>
                        <small style="color:#888;">Sana</small>
                    </div>

                    <div style="flex:1; background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:14px; text-align:center;">
                        <i class="bx bx-map" style="font-size:20px; color:#ef4444;"></i>
                        <div style="font-weight:700; margin-top:4px;">{{ $elon->kurs }} - Kurs</div>
                        <small style="color:#888;">{{ $elon->category->nomi ?? 'Umumiy' }}</small>
                    </div>
                </div>
            </div>

            {{-- O'NG TOMON: Ma'lumotlar --}}
            <div>
                <div style="font-size:12px; color:#3C3489; margin-bottom:10px;">
                    <a href="{{ route('elons.index') }}" style="color:#3C3489; text-decoration:none;">E'lonlar</a>
                    <span style="color:#ccc;"> / </span>
                    <span>{{ $elon->category->nomi ?? 'Umumiy' }}</span>
                </div>

                <h1 style="font-size:26px; font-weight:700; color:#222; margin-bottom:16px;">
                    {{ $elon->title }}
                </h1>

                <div style="background:#EEEDFE; border-left:4px solid #3C3489; padding:16px; border-radius:10px; margin-bottom:20px;">
                    <div style="color:#3C3489; font-weight:700; font-size:15px;">
                        Yo'nalish: {{ $elon->category->nomi ?? 'Umumiy' }}
                    </div>
                    <div style="color:#3C3489; font-weight:700; font-size:15px; margin-top:4px;">
                        E'lon beruvchi: {{ $elon->admin['To‘liq_ismi'] ?? '—' }}
                    </div>
                    <p style="margin:8px 0 0; color:#444; opacity:.85;">{{ $elon->short_content }}</p>
                </div>

                <div>
                    <h5 style="font-weight:700; margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid #f0f0f0;">
                        E'lon tavsifi:
                    </h5>
                    <p style="color:#666; line-height:1.8;">
                        {{ $elon->full_content }}
                    </p>
                </div>
            </div>

        </div>

    </div>

</x-layouts.sidebar>