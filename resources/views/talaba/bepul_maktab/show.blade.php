<x-layouts.sidebar>
    <x-slot:title>{{ $subject->nomi }}</x-slot:title>

    <div class="oz-wrap">

        <div class="oz-toolbar">
            <a href="{{ route('talaba.bepul_maktab.fanlar', $bolim->id) }}" class="ar-btn">← Orqaga</a>
            <span class="oz-title">{{ $subject->nomi }}</span>
        </div>

        @if($settings)
            {{-- TEST MA'LUMOTLARI --}}
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); gap:10px;">
                <div class="oz-card oz-card-info">
                    <div class="oz-card-label">Vaqt limiti</div>
                    <div class="oz-card-val">{{ $settings->vaqt_daqiqa }}</div>
                    <div class="oz-card-sub">daqiqa</div>
                </div>
                <div class="oz-card oz-card-success">
                    <div class="oz-card-label">Savol soni</div>
                    <div class="oz-card-val">{{ $settings->savol_soni }}</div>
                    <div class="oz-card-sub">ta random savol</div>
                </div>
                <div class="oz-card oz-card-success">
                    <div class="oz-card-label">Har bir to'g'ri javob</div>
                    <div class="oz-card-val">{{ $settings->ball_per_savol }}</div>
                    <div class="oz-card-sub">ball</div>
                </div>
                <div class="oz-card oz-card-info">
                    <div class="oz-card-label">Urinish huquqi</div>
                    <div class="oz-card-val">{{ $urinishSoni }} / {{ $settings->urinish_soni }}</div>
                    <div class="oz-card-sub">ishlatilgan</div>
                </div>
            </div>

            {{-- VAQT --}}
            <div style="display:flex; gap:12px; flex-wrap:wrap; font-size:13px; color:#888;">
                @if($settings->boshlanish_vaqti)
                    <span>🕐 Boshlanish: <strong style="color:#333;">{{ $settings->boshlanish_vaqti->format('d.m.Y H:i') }}</strong></span>
                @endif
                @if($settings->tugash_vaqti)
                    <span>🕐 Tugash: <strong style="color:#333;">{{ $settings->tugash_vaqti->format('d.m.Y H:i') }}</strong></span>
                @endif
            </div>

            {{-- TEST BOSHLASH --}}
            @if($jarayonda)
                <div style="padding:1rem; background:#FAEEDA; border-radius:8px; font-size:13px; color:#633806;">
                    Tugallanmagan test mavjud!
                    <a href="{{ route('talaba.bepul_maktab.test', $jarayonda->id) }}" class="ar-btn ar-btn-ok" style="margin-left:8px;">
                        Davom ettirish →
                    </a>
                </div>
            @elseif($urinishSoni >= $settings->urinish_soni)
                <div style="padding:1rem; background:#FCEBEB; border-radius:8px; font-size:13px; color:#791F1F;">
                    Urinish huquqingiz tugagan.
                </div>
            @else
                <form action="{{ route('talaba.bepul_maktab.boshlash', [$bolim->id, $subject->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="ar-btn ar-btn-ok" style="padding:10px 24px; font-size:14px;">
                        ▶ Testni boshlash
                    </button>
                </form>
            @endif

            {{-- OLDINGI URINISHLAR --}}
            @if($urinishlar->count() > 0)
                <div class="oz-title" style="margin-top:1rem;">Oldingi urinishlar</div>
                <div class="arizalar-table-wrap">
                    <table class="arizalar-table">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>Boshlangan vaqt</th>
                                <th>Tugagan vaqt</th>
                                <th>Ball</th>
                                <th>Holat</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($urinishlar as $i => $urinish)
                                <tr>
                                    <td class="ar-id">{{ $i + 1 }}</td>
                                    <td>{{ $urinish->boshlangan_vaqt->format('d.m.Y H:i') }}</td>
                                    <td>{{ $urinish->tugagan_vaqt?->format('d.m.Y H:i') ?? '—' }}</td>
                                    <td style="font-weight:600; color:#27500A;">{{ $urinish->ball }}</td>
                                    <td>
                                        @if($urinish->holat === 'tugagan')
                                            <span class="ar-badge ar-badge-ok">Tugagan</span>
                                        @else
                                            <span class="ar-badge ar-badge-warn">Jarayonda</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($urinish->holat === 'tugagan')
                                            <a href="{{ route('talaba.bepul_maktab.natija', $urinish->id) }}" class="ar-btn">
                                                Natija →
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        @else
            <div style="text-align:center; padding:3rem; color:#888;">
                Bu fan uchun test hali sozlanmagan
            </div>
        @endif

    </div>
</x-layouts.sidebar>