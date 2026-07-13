<x-layouts.sidebar>
    <x-slot:title>Bepul Maktab</x-slot:title>

    <div class="oz-wrap">
        <div class="oz-title">Bepul Maktab — Bolimlar</div>

        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:60px;">№</th>
                        <th>Bolim nomi</th>
                        <th style="width:120px;">Talabalar</th>
                        <th style="width:100px;">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bolimlar as $bolim)
                        <tr style="cursor:pointer;"
                            onclick="window.location='{{ route('bepul_maktab.fanlar', $bolim->id) }}'">
                            <td class="ar-id">{{ $loop->iteration }}</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div
                                        style="width:34px; height:34px; border-radius:8px; background:#EEEDFE;
                                        display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i class="bx bx-buildings" style="font-size:16px; color:#3C3489;"></i>
                                    </div>
                                    <span style="font-size:14px; font-weight:500;">{{ $bolim->nomi }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:13px; color:#888;">
                                    {{ $bolim->free_semestrs->count() ?? 0 }} ta
                                </span>
                            </td>
                            <td onclick="event.stopPropagation()">
                                <a href="{{ route('bepul_maktab.fanlar', $bolim->id) }}" class="ar-btn">
                                    <i class="bx bx-chevron-right"></i> Kirish
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="ar-pagination">
            {{ $bolimlar->links() }}
        </div>
    </div>
</x-layouts.sidebar>
