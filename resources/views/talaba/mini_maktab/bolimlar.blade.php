<x-layouts.sidebar>
    <x-slot:title>Mini Semestr — Bo'limlar</x-slot:title>

    <div class="oz-wrap">
        <div class="oz-title">Bo'limlar</div>

        @forelse ($bolimlar as $bolim)
            @php $faol = (bool) $bolim->status; @endphp

            <div style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:18px; margin-bottom:10px;
                    {{ $faol ? 'cursor:pointer;' : 'opacity:.55; cursor:not-allowed;' }}"
                @if ($faol) onclick="window.location='{{ route('talaba.mini_maktab.fanlar', $bolim->id) }}'" @endif>

                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">

                    <div style="display:flex; align-items:center; gap:12px; flex:1;">
                        <div
                            style="width:44px; height:44px; border-radius:10px; background:#EEEDFE;
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="bx bx-buildings" style="font-size:22px; color:#3C3489;"></i>
                        </div>
                        <div>
                            <p style="font-size:15px; font-weight:600; color:#333; margin:0;">
                                {{ $bolim->nomi }}
                            </p>
                            <p style="font-size:12px; margin:2px 0 0; color:{{ $faol ? '#27500A' : '#999' }};">
                                {{ $faol ? 'Faol' : 'Hali faol emas' }}
                            </p>
                        </div>
                    </div>

                    @if ($faol)
                        <a href="{{ route('talaba.mini_maktab.fanlar', $bolim->id) }}" class="ar-btn ar-btn-ok"
                            onclick="event.stopPropagation()">
                            <i class="bx bx-folder-open"></i> Fanlarni ko'rish
                        </a>
                    @else
                        <span
                            style="font-size:11px; font-weight:700; color:#999; background:#f5f5f5;
                            border-radius:20px; padding:4px 10px;">
                            <i class="bx bx-lock-alt"></i> Yopiq
                        </span>
                    @endif

                </div>
            </div>
        @empty
            <div style="text-align:center; padding:3rem; color:#888;">
                <i class="bx bx-buildings" style="font-size:48px; display:block; margin-bottom:12px; color:#ddd;"></i>
                <p style="font-size:14px;">Sizga biriktirilgan bo'lim yo'q</p>
            </div>
        @endforelse
    </div>
</x-layouts.sidebar>
