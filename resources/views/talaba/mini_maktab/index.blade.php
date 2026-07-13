<x-layouts.sidebar>
    <x-slot:title>Mini Semestr</x-slot:title>

    <div class="oz-wrap">
        <div class="oz-title">Mini Semestr</div>

        @forelse ($fanlar as $ariza)
            <div style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:18px; margin-bottom:10px; cursor:pointer;"
                onclick="window.location='{{ route('talaba.mini_maktab.mavzular', $ariza->id) }}'">
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
                                {{ $ariza->bolims->nomi ?? '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- STATISTIKA --}}
                    <div style="display:flex; gap:20px; align-items:center; flex-wrap:wrap;">
                        <div style="text-align:center;">
                            <p style="font-size:11px; color:#aaa; margin:0;">Joriy</p>
                            <p style="font-size:14px; font-weight:600; color:#3C3489; margin:0;">
                                {{ $ariza->joriy_baho ?? 0 }}
                            </p>
                        </div>
                        <div style="text-align:center;">
                            <p style="font-size:11px; color:#aaa; margin:0;">Oraliq</p>
                            <p style="font-size:14px; font-weight:600; color:#856404; margin:0;">
                                {{ $ariza->oraliq_baho ?? 0 }}
                            </p>
                        </div>
                        <div style="text-align:center;">
                            <p style="font-size:11px; color:#aaa; margin:0;">Yakuniy</p>
                            <p style="font-size:14px; font-weight:600; color:#065f46; margin:0;">
                                {{ $ariza->yakuniy_baho ?? 0 }}
                            </p>
                        </div>
                        <div style="text-align:center;">
                            <p style="font-size:11px; color:#aaa; margin:0;">Umumiy</p>
                            <p style="font-size:14px; font-weight:700; color:#27500A; margin:0;">
                                {{ $ariza->umumiy ?? 0 }}
                            </p>
                        </div>
                    </div>

                    {{-- TUGMA --}}
                    <a href="{{ route('talaba.mini_maktab.mavzular', $ariza->id) }}" class="ar-btn ar-btn-ok"
                        onclick="event.stopPropagation()">
                        <i class="bx bx-folder-open"></i> Mavzularni ko'rish
                    </a>

                </div>
            </div>
        @empty
            <div style="text-align:center; padding:3rem; color:#888;">
                <i class="bx bx-book-open" style="font-size:48px; display:block; margin-bottom:12px; color:#ddd;"></i>
                <p style="font-size:14px;">Sizga biriktirilgan fanlar yo'q</p>
            </div>
        @endforelse
    </div>
</x-layouts.sidebar>
