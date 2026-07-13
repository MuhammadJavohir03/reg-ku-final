<x-layouts.sidebar>
    <x-slot:title>Talaba urinishlari</x-slot:title>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
            <a href="{{ route('mini_maktab.mavzular', [$bolim->id, $subject->id]) }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i>
            </a>
            <div>
                <div style="font-size:18px; font-weight:600;">
                    {{ $user->name ?? ($user['To‘liq_ismi'] ?? 'Talaba') }}
                </div>
                <div style="font-size:12px; color:#888;">
                    {{ $bolim->nomi ?? '' }} — {{ $subject->nomi ?? '' }}
                    @if (isset($material) && $material->mavzu)
                        · {{ $material->mavzu->nomi }}
                        <span style="font-size:11px; font-weight:600; color:#3C3489;">
                            ({{ ucfirst($material->mavzu->tur) }})
                        </span>
                    @endif
                    bo'yicha urinishlar
                </div>
            </div>
        </div>

        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:60px;">Urinish</th>
                        <th>Boshlanish vaqti</th>
                        <th>Tugash vaqti</th>
                        <th style="width:100px;">Holat</th>
                        <th style="width:120px;">To'g'ri javob</th>
                        <th style="width:100px;">Ball</th>
                        <th style="width:100px;">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sessions as $i => $session)
                        <tr>
                            <td class="ar-id">{{ $i + 1 }}-urinish</td>
                            <td style="font-size:13px;">{{ $session->boshlanish_vaqti ?? '—' }}</td>
                            <td style="font-size:13px;">{{ $session->tugash_vaqti ?? '—' }}</td>
                            <td>
                                @if ($session->status === 'active')
                                    <span class="ar-badge" style="background:#fff3cd; color:#856404;">Jarayonda</span>
                                @elseif ($session->status === 'expired')
                                    <span class="ar-badge" style="background:#fee2e2; color:#b91c1c;">Vaqti tugagan</span>
                                @else
                                    <span class="ar-badge ar-badge-ok">Yakunlangan</span>
                                @endif
                            </td>
                            <td style="font-size:13px;">
                                {{ $session->togri_soni ?? 0 }} / {{ $session->jami_soni ?? 0 }}
                            </td>
                            <td style="font-size:13px; font-weight:600;">
                                {{ $session->ball ?? 0 }}
                            </td>
                            <td>
                                @if ($session->status !== 'active')
                                    <a href="{{ route('mini_maktab.harakat', [$bolim->id, $subject->id, $user->id, $session->id]) }}"
                                        style="background:#3C3489; color:#fff; padding:6px 12px;
                                              border-radius:8px; font-size:12px; text-decoration:none;">
                                        Ko'rish
                                    </a>
                                @else
                                    <span style="font-size:12px; color:#bbb;">Hali tugamagan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:30px; text-align:center; color:#888;">
                                Bu talaba hali bu testni ishlamagan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-layouts.sidebar>