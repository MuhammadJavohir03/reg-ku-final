<x-layouts.sidebar>
    <x-slot:title>Fan natijalari</x-slot:title>

    <div class="oz-wrap">

        {{-- HEADER --}}
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
            <div>
                <div style="font-size:11px; color:#aaa; margin-bottom:2px;">
                    {{ $grades->first()?->subject?->category?->nomi ?? 'Yo\'nalish topilmadi' }}
                </div>
                <div class="oz-title" style="margin:0;">
                    {{ $grades->first()?->subject?->nomi ?? 'Fan topilmadi' }}
                </div>
            </div>

            <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">

                {{-- QIDIRUV --}}
                <form action="{{ url()->current() }}" method="GET"
                    style="display:flex; align-items:center; gap:6px;">
                    <div style="position:relative;">
                        <i class="bx bx-search" style="position:absolute; left:10px; top:50%;
                            transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                        <input type="text" name="search" class="arizalar-search"
                            style="padding-left:34px; width:200px;"
                            placeholder="Talaba ismi..."
                            value="{{ request('search') }}">
                    </div>
                    @if(request('search'))
                        <a href="{{ url()->current() }}" class="ar-btn ar-btn-rej">✕</a>
                    @endif
                </form>

                {{-- TOZALASH --}}
                <form action="{{ route('grades.clear', $grades->first()?->subject_id ?? 0) }}"
                    method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="ar-btn ar-btn-rej"
                        onclick="return confirm('Barcha baholar ochirisinmi?')">
                        <i class="bx bx-trash"></i> Tozalash
                    </button>
                </form>

                {{-- ORQAGA --}}
                <a href="{{ route('subject.index') }}" class="ar-btn">
                    <i class="bx bx-arrow-back"></i> Orqaga
                </a>

            </div>
        </div>

        {{-- JADVAL --}}
        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:50px;">№</th>
                        <th>Talaba</th>
                        <th style="width:100px;">Guruh</th>
                        <th style="width:80px; text-align:center;">Joriy</th>
                        <th style="width:80px; text-align:center;">Oraliq</th>
                        <th style="width:80px; text-align:center;">Reyting</th>
                        <th style="width:80px; text-align:center;">Yakuniy</th>
                        <th style="width:110px; text-align:center;">Umumiy ball</th>
                        <th style="width:140px;">Davomat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($grades as $index => $grade)
                        <tr>
                            <td class="ar-id">
                                {{ $grades->firstItem() + $index }}
                            </td>

                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div class="ar-avatar">
                                        {{ mb_substr($grade->user['To‘liq_ismi'] ?? 'N', 0, 2) }}
                                    </div>
                                    <span style="font-size:13px; font-weight:500;">
                                        {{ $grade->user['To‘liq_ismi'] ?? 'Noma\'lum' }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                <span style="background:#f5f5f5; color:#555; padding:3px 10px;
                                    border-radius:6px; font-size:12px; font-weight:600;">
                                    {{ $grade->user->Guruh ?? '—' }}
                                </span>
                            </td>

                            <td style="text-align:center; font-size:13px; font-weight:600; color:#333;">
                                {{ $grade->joriy_baho }}
                            </td>

                            <td style="text-align:center; font-size:13px; font-weight:600; color:#333;">
                                {{ $grade->oraliq_baho }}
                            </td>

                            <td style="text-align:center; font-size:13px; font-weight:600;
                                color: {{ $grade->joriy_oraliq >= 20 ? '#27500A' : '#ff0000' }}">
                                {{ $grade->joriy_oraliq }}
                            </td>

                            <td style="text-align:center; font-size:13px; font-weight:600; color:#333;">
                                {{ $grade->yakuniy_baho }}
                            </td>

                            <td style="text-align:center;">
                                @if($grade->umumiy > 70)
                                    <span class="ar-badge ar-badge-ok"
                                        style="font-size:13px; font-weight:700;">
                                        {{ $grade->umumiy }}
                                    </span>
                                @elseif($grade->umumiy >= 60)
                                    <span class="ar-badge"
                                        style="background:#fff3cd; color:#ff0000; font-size:13px; font-weight:700;">
                                        {{ $grade->umumiy }}
                                    </span>
                                @else
                                    <span class="ar-badge ar-badge-rej"
                                        style="font-size:13px; font-weight:700;">
                                        {{ $grade->umumiy }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="font-size:12px; font-weight:600; min-width:36px;
                                        color: {{ $grade->davomat > 33 ? '#791F1F' : ($grade->davomat > 15 ? '#856404' : '#27500A') }}">
                                        {{ $grade->davomat }}%
                                    </span>
                                    <div style="flex:1; height:6px; background:#f0f0f0; border-radius:4px; overflow:hidden;">
                                        <div style="height:100%; border-radius:4px;
                                            width:{{ min($grade->davomat, 100) }}%;
                                            background:{{ $grade->davomat > 33 ? '#ef4444' : ($grade->davomat > 15 ? '#f59e0b' : '#10b981') }};
                                            transition:width 0.3s;">
                                        </div>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:2.5rem; color:#888;">
                                <i class="bx bx-inbox" style="font-size:36px; display:block; margin-bottom:8px;"></i>
                                Hech qanday natija topilmadi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ar-pagination" style="margin-top:16px;">
            {{ $grades->withQueryString()->links() }}
        </div>

    </div>
</x-layouts.sidebar>