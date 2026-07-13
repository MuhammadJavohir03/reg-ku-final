<x-layouts.sidebar>
    <x-slot:title>{{ $bolim->nomi }} — Fanlar</x-slot:title>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
            <a href="{{ route('mini_maktab.index') }}" class="ar-btn">
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
                        <th style="width:140px;">Mavzular</th>
                        <th style="width:100px;">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fanlar as $index => $row)
                        @php
                            $fan = $row->subject;
                            $arizalar = \App\Models\mini_semestr::where('bolim_id', $bolim->id)
                                ->where('subject_id', $fan->id)
                                ->count();
                            $mavzuCount = \App\Models\MsMavzu::where('bolim_id', $bolim->id)
                                ->where('subject_id', $fan->id)
                                ->count();
                        @endphp
                        <tr style="cursor:pointer;"
                            onclick="window.location='{{ route('mini_maktab.mavzular', [$bolim->id, $fan->id]) }}'">
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
                                <span style="font-size:13px; color:#3C3489; font-weight:500;">
                                    {{ $mavzuCount }} ta mavzu
                                </span>
                            </td>
                            <td onclick="event.stopPropagation()">
                                <a href="{{ route('mini_maktab.mavzular', [$bolim->id, $fan->id]) }}" class="ar-btn">
                                    <i class="bx bx-cog"></i> Boshqarish
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.sidebar>
