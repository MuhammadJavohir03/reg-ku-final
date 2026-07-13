<x-layouts.sidebar>
    <x-slot:title>{{ $miniSemestr->subject->nomi }} — Mavzular</x-slot:title>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
            <a href="{{ route('talaba.mini_maktab.index') }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i>
            </a>
            <div>
                <div class="oz-title" style="margin:0;">{{ $miniSemestr->subject->nomi }}</div>
                <span style="font-size:12px; color:#888;">{{ $miniSemestr->bolims->nomi ?? '—' }}</span>
            </div>
        </div>

        @php
            $turlar = [
                'mavzu' => ['nomi' => 'Mavzular', 'icon' => 'bx-book-content', 'rang' => '#EEEDFE', 'txt' => '#3C3489'],
                'oraliq' => [
                    'nomi' => 'Oraliq nazorat',
                    'icon' => 'bx-notepad',
                    'rang' => '#fff3cd',
                    'txt' => '#856404',
                ],
                'yakuniy' => [
                    'nomi' => 'Yakuniy nazorat',
                    'icon' => 'bx-medal',
                    'rang' => '#d1fae5',
                    'txt' => '#065f46',
                ],
            ];
        @endphp

        @foreach ($turlar as $turKey => $tur)
            @if (($mavzular[$turKey] ?? collect())->isNotEmpty())
                <div style="margin-bottom:28px;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                        <div
                            style="width:30px; height:30px; border-radius:7px; background:{{ $tur['rang'] }};
                             display:flex; align-items:center; justify-content:center;">
                            <i class="bx {{ $tur['icon'] }}" style="color:{{ $tur['txt'] }};"></i>
                        </div>
                        <span
                            style="font-size:15px; font-weight:600; color:{{ $tur['txt'] }};">{{ $tur['nomi'] }}</span>
                        <span style="font-size:12px; color:#aaa;">({{ $mavzular[$turKey]->count() }} ta)</span>
                    </div>

                    <div class="arizalar-table-wrap">
                        <table class="arizalar-table">
                            <thead>
                                <tr>
                                    <th style="width:50px;">№</th>
                                    <th>Nomi</th>
                                    <th style="width:120px;">Materiallar</th>
                                    @if ($turKey === 'mavzu')
                                        <th style="width:100px;">Ball</th>
                                    @endif
                                    <th style="width:100px;">Amal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mavzular[$turKey] as $i => $mavzu)
                                    <tr style="cursor:pointer;"
                                        onclick="window.location='{{ route('talaba.mini_maktab.mavzu.show', [$miniSemestr->id, $mavzu->id]) }}'">
                                        <td class="ar-id">{{ $i + 1 }}</td>
                                        <td>
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <div
                                                    style="width:28px; height:28px; border-radius:6px; background:{{ $tur['rang'] }};
                                                     display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                                    <i class="bx {{ $tur['icon'] }}"
                                                        style="font-size:13px; color:{{ $tur['txt'] }};"></i>
                                                </div>
                                                <span
                                                    style="font-size:14px; font-weight:500;">{{ $mavzu->nomi }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                style="font-size:13px; color:#888;">{{ $mavzu->materiallar->count() }}
                                                ta</span>
                                        </td>
                                        @if ($turKey === 'mavzu')
                                            <td>
                                                @if (isset($joriyBaholar[$mavzu->id]))
                                                    <span style="font-size:13px; font-weight:600; color:#27500A;">
                                                        {{ $joriyBaholar[$mavzu->id] }}
                                                    </span>
                                                @else
                                                    <span style="font-size:12px; color:#bbb;">—</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td onclick="event.stopPropagation()">
                                            <a href="{{ route('talaba.mini_maktab.mavzu.show', [$miniSemestr->id, $mavzu->id]) }}"
                                                class="ar-btn ar-btn-ok" style="font-size:12px;">
                                                Ochish →
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach

        @if ($mavzular->isEmpty())
            <div style="text-align:center; padding:3rem; color:#888;">
                Hozircha mavzular biriktirilmagan
            </div>
        @endif

    </div>
</x-layouts.sidebar>
