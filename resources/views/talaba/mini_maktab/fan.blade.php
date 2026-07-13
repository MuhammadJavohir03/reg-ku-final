<x-layouts.sidebar>
    <x-slot:title>{{ $mini->subject->nomi }}</x-slot:title>

    <div class="oz-wrap">

        <div class="oz-title">
            {{ $mini->subject->nomi }}
        </div>

        @forelse($mavzular as $mavzu)
            <div
                style="background:#fff;
                    border:1px solid #f0f0f0;
                    border-radius:12px;
                    padding:18px;
                    margin-bottom:12px;">

                <div
                    style="display:flex;
                        justify-content:space-between;
                        align-items:center;
                        flex-wrap:wrap;
                        gap:15px;">

                    {{-- Chap --}}
                    <div style="display:flex;align-items:center;gap:14px;flex:1;">

                        <div
                            style="
                        width:48px;
                        height:48px;
                        border-radius:12px;
                        background:#EEEDFE;
                        display:flex;
                        align-items:center;
                        justify-content:center;">

                            @if ($mavzu->tur == 'mavzu')
                                <i class="bx bx-book-content" style="font-size:24px;color:#3C3489;"></i>
                            @elseif($mavzu->tur == 'oraliq')
                                <i class="bx bx-edit-alt" style="font-size:24px;color:#ff9800;"></i>
                            @else
                                <i class="bx bx-award" style="font-size:24px;color:#2e7d32;"></i>
                            @endif

                        </div>

                        <div>

                            <div style="font-size:16px;font-weight:700;color:#333;">
                                {{ $mavzu->nomi }}
                            </div>

                            <div style="font-size:13px;color:#888;margin-top:3px;">

                                @if ($mavzu->tur == 'mavzu')
                                    Oddiy mavzu
                                @elseif($mavzu->tur == 'oraliq')
                                    Oraliq nazorat
                                @else
                                    Yakuniy nazorat
                                @endif

                            </div>

                        </div>

                    </div>

                    {{-- O'ng --}}
                    <div style="display:flex;align-items:center;gap:18px;">

                        <div style="text-align:center;">
                            <div style="font-size:11px;color:#999;">
                                Material
                            </div>

                            <div style="font-size:17px;font-weight:700;color:#3C3489;">
                                {{ $mavzu->materiallar_count }}
                            </div>
                        </div>

                        <a href="{{ route('talaba.mini_maktab.mavzu', $mavzu->id) }}" class="ar-btn ar-btn-ok">

                            <i class="bx bx-right-arrow-alt"></i>

                            Ochish

                        </a>

                    </div>

                </div>

            </div>

        @empty

            <div style="text-align:center;padding:60px 20px;color:#888;">

                <i class="bx bx-folder-open" style="font-size:60px;color:#ddd;"></i>

                <div style="margin-top:15px;">
                    Bu fan uchun mavzular mavjud emas.
                </div>

            </div>
        @endforelse

    </div>

</x-layouts.sidebar>
