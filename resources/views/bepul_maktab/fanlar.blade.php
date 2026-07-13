<x-layouts.sidebar>
    <x-slot:title>{{ $bolim->nomi }} — Fanlar</x-slot:title>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
            <a href="{{ route('bepul_maktab.index') }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i>
            </a>
            <div class="oz-title" style="margin:0;">{{ $bolim->nomi }} — Fanlar</div>
        </div>

        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:60px;">№</th>
                        <th>Fan nomi</th>
                        <th style="width:120px;">Arizalar</th>
                        <th style="width:140px;">Bank holati</th>
                        <th style="width:100px;">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fanlar as $index => $row)
                        @php
                            $fan = $row->subject;
                            $bank = \App\Models\QuestionBank::where('bolim_id', $bolim->id)
                                ->where('subject_id', $fan->id)
                                ->where('tur', 'free')
                                ->first();
                            $arizalar = \App\Models\free_semestr::where('bolim_id', $bolim->id)
                                ->where('subject_id', $fan->id)
                                ->count();
                        @endphp
                        <tr style="cursor:pointer;"
                            onclick="window.location='{{ route('bepul_maktab.sozlamalar', [$bolim->id, $fan->id]) }}'">
                            <td class="ar-id">{{ $index + 1 }}</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div
                                        style="width:34px; height:34px; border-radius:8px; background:#e6f4ea;
                                        display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i class="bx bx-book-open" style="font-size:16px; color:#10b981;"></i>
                                    </div>
                                    <span style="font-size:14px; font-weight:500;">{{ $fan->nomi }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:13px; color:#888;">{{ $arizalar }} ta ariza</span>
                            </td>
                            <td>
                                @if ($bank)
                                    <span class="ar-badge ar-badge-ok">
                                        <i class="bx bx-check-circle"></i> {{ $bank->nomi }}
                                    </span>
                                @else
                                    <span class="ar-badge" style="background:#fff3cd; color:#856404;">
                                        <i class="bx bx-error-circle"></i> Biriktirilmagan
                                    </span>
                                @endif
                            </td>
                            <td onclick="event.stopPropagation()">
                                <a href="{{ route('bepul_maktab.sozlamalar', [$bolim->id, $fan->id]) }}" class="ar-btn">
                                    <i class="bx bx-cog"></i> Sozlash
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.sidebar>
