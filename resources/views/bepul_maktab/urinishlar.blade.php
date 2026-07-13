<x-layouts.sidebar>
    <x-slot:title>Talaba urinishlari</x-slot:title>

    <div class="oz-wrap">

        <div style="margin-bottom:16px;">
            <div style="font-size:18px; font-weight:600;">
                {{ $user->name ?? 'Talaba' }}
            </div>
            <div style="font-size:12px; color:#888;">
                {{ $bolim->nomi ?? '' }} — {{ $subject->nomi ?? '' }} bo'yicha urinishlar
            </div>
        </div>

        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:60px;">Urinish</th>
                        <th>Boshlanish vaqti</th>
                        <th>Tugash vaqti</th>
                        <th style="width:120px;">To'g'ri javob</th>
                        <th style="width:100px;">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessions as $i => $session)
                        <tr>
                            <td class="ar-id">{{ $i + 1 }}-urinish</td>
                            <td style="font-size:13px;">{{ $session->boshlanish_vaqti ?? '—' }}</td>
                            <td style="font-size:13px;">{{ $session->tugash_vaqti ?? '—' }}</td>
                            <td style="font-size:13px;">
                                {{ $session->togri_soni ?? 0 }} / {{ $session->jami_soni ?? 0 }}
                            </td>
                            <td>
                                <a href="{{ route('bepul_maktab.harakat', [$bolim->id, $subject->id, $user->id, $session->id]) }}"
                                    style="background:#3C3489; color:#fff; padding:6px 12px;
                                          border-radius:8px; font-size:12px; text-decoration:none;">
                                    Ko'rish
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-layouts.sidebar>
