<x-layouts.sidebar>
    <x-slot:title>{{ $subject->nomi }} — Mavzular</x-slot:title>

    <div class="oz-wrap">

        {{-- HEADER --}}
        <div
            style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <a href="{{ route('mini_maktab.fanlar', $bolim->id) }}" class="ar-btn">
                    <i class="bx bx-arrow-back"></i>
                </a>
                <div>
                    <div class="oz-title" style="margin:0;">{{ $subject->nomi }}</div>
                    <div style="font-size:12px; color:#888;">{{ $bolim->nomi }}</div>
                </div>
            </div>
            <button onclick="document.getElementById('mavzu-modal').style.display='flex'" class="ar-btn"
                style="background:#3C3489; color:#fff; gap:6px;">
                <i class="bx bx-plus"></i> Yangi bo'lim qo'shish
            </button>
        </div>

        {{-- ══════════════════════════════════════════
             MAVZULAR GURUHLARI: mavzu | oraliq | yakuniy
        ══════════════════════════════════════════ --}}

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
            <div style="margin-bottom:28px;">
                {{-- Tur sarlavhasi --}}
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <div
                            style="width:30px; height:30px; border-radius:7px; background:{{ $tur['rang'] }};
                             display:flex; align-items:center; justify-content:center;">
                            <i class="bx {{ $tur['icon'] }}" style="color:{{ $tur['txt'] }};"></i>
                        </div>
                        <span
                            style="font-size:15px; font-weight:600; color:{{ $tur['txt'] }};">{{ $tur['nomi'] }}</span>
                        <span style="font-size:12px; color:#aaa;">({{ ($mavzular[$turKey] ?? collect())->count() }}
                            ta)</span>
                    </div>
                </div>

                {{-- Tur ichidagi mavzular --}}
                @if (($mavzular[$turKey] ?? collect())->isEmpty())
                    <div
                        style="font-size:13px; color:#bbb; padding:10px 14px; background:#fafafa;
                         border:1px dashed #e0e0e0; border-radius:8px;">
                        Hozircha {{ strtolower($tur['nomi']) }} yo'q
                    </div>
                @else
                    <div class="arizalar-table-wrap">
                        <table class="arizalar-table">
                            <thead>
                                <tr>
                                    <th style="width:50px;">№</th>
                                    <th>Nomi</th>
                                    <th style="width:120px;">Materiallar</th>
                                    <th style="width:120px;">Amal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mavzular[$turKey] as $i => $mavzu)
                                    <tr style="cursor:pointer;"
                                        onclick="window.location='{{ route('mini_maktab.mavzu.show', [$bolim->id, $subject->id, $mavzu->id]) }}'">
                                        <td class="ar-id">{{ $i + 1 }}</td>
                                        <td>
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <div
                                                    style="width:28px; height:28px; border-radius:6px;
                                                     background:{{ $tur['rang'] }};
                                                     display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                                    <i class="bx {{ $tur['icon'] }}"
                                                        style="font-size:13px; color:{{ $tur['txt'] }};"></i>
                                                </div>
                                                <span
                                                    style="font-size:14px; font-weight:500;">{{ $mavzu->nomi }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-size:13px; color:#888;">{{ $mavzu->materiallar_count }}
                                                ta</span>
                                        </td>
                                        <td onclick="event.stopPropagation()"
                                            style="display:flex; gap:6px; flex-wrap:wrap;">
                                            <a href="{{ route('mini_maktab.mavzu.show', [$bolim->id, $subject->id, $mavzu->id]) }}"
                                                class="ar-btn" style="font-size:12px;">
                                                <i class="bx bx-folder-open"></i> Ochish
                                            </a>
                                            <form action="{{ route('mini_maktab.mavzu.ochir', $mavzu->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Mavzu va barcha materiallari o\'chiriladimi?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="ar-btn"
                                                    style="background:#fee2e2; color:#b91c1c; border:none; cursor:pointer; font-size:12px;">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach

        @php
            // Natijalar tanlash oynasi uchun: shu fandagi barcha faol testlar (mavzu/oraliq/yakuniy)
            $barchaTestMateriallari = collect();
            foreach ($mavzular as $turKeyX => $mavzularRoyxati) {
                foreach ($mavzularRoyxati as $mavzuX) {
                    foreach ($mavzuX->materiallar()->where('tur', 'test')->where('faol', 1)->get() as $materialX) {
                        $barchaTestMateriallari->push((object) [
                            'id'   => $materialX->id,
                            'nomi' => $mavzuX->nomi,
                            'tur'  => $mavzuX->tur,
                        ]);
                    }
                }
            }
        @endphp

        {{-- ══════════════════════════════════════════
             TALABALAR JADVALI
        ══════════════════════════════════════════ --}}
        <div style="margin-top:32px;">
            <div
                style="font-size:15px; font-weight:600; margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                <i class="bx bx-group" style="color:#3C3489;"></i> Talabalar
                <div style="margin-left:auto; display:flex; gap:6px;">
                    <form action="{{ route('mini_maktab.status.all', [$bolim->id, $subject->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="1">
                        <button type="submit" class="ar-btn"
                            style="background:#d1fae5; color:#065f46; font-size:12px; border:none; cursor:pointer;">
                            ✔ Barchasini aktiv
                        </button>
                    </form>
                    <form action="{{ route('mini_maktab.status.all', [$bolim->id, $subject->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="0">
                        <button type="submit" class="ar-btn"
                            style="background:#fee2e2; color:#b91c1c; font-size:12px; border:none; cursor:pointer;">
                            ✕ Barchasini blok
                        </button>
                    </form>
                </div>
            </div>

            <div style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:18px; margin-top:16px;">

    <p style="font-size:13px; font-weight:600; color:#333; margin:0 0 12px;">
        <i class="bx bx-group" style="color:#3C3489;"></i>
        Talabalar — {{ $talabalar->total() }} ta ishtirokchi
    </p>

    @php
        $aktiv = $talabalar->where('status', 1)->count();
        $blok = $talabalar->where('status', 0)->count();
    @endphp

    <div style="display:flex; gap:10px; margin-bottom:15px;">

        <div style="flex:1; background:#eaf3de; padding:10px; border-radius:10px;">
            <div style="font-size:20px; font-weight:700; color:#27500A;">
                {{ $aktiv }}
            </div>
            <div style="font-size:12px; color:#27500A;">Aktiv</div>
        </div>

        <div style="flex:1; background:#fdecec; padding:10px; border-radius:10px;">
            <div style="font-size:20px; font-weight:700; color:#b91c1c;">
                {{ $blok }}
            </div>
            <div style="font-size:12px; color:#b91c1c;">Bloklangan</div>
        </div>

    </div>

    <div class="arizalar-table-wrap">
        <table class="arizalar-table">

            <thead>
                <tr>
                    <th style="width:60px;">№</th>
                    <th>Talaba</th>
                    <th>Kurs</th>
                    <th>Guruh</th>
                    <th>Status</th>
                    <th style="width:170px;">Amal</th>
                </tr>
            </thead>

            <tbody>

                @forelse($talabalar as $ariza)

                    <tr>

                        {{-- № --}}
                        <td class="ar-id text-center">
                            {{ $talabalar->firstItem() + $loop->index }}
                        </td>

                        {{-- Talaba --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">

                                <div class="ar-avatar">
                                    {{ mb_substr($ariza->user['To‘liq_ismi'] ?? 'N',0,2) }}
                                </div>

                                <div>
                                    <div style="font-weight:600;">
                                        {{ $ariza->user['To‘liq_ismi'] ?? '—' }}
                                    </div>

                                    <small style="color:#888;">
                                        {{ $ariza->user->email ?? '—' }}
                                    </small>
                                </div>

                            </div>
                        </td>

                        {{-- Kurs --}}
                        <td class="text-center">
                            {{ $ariza->user->Kurs ?? '—' }}-kurs
                        </td>

                        {{-- Guruh --}}
                        <td class="text-center">
                            {{ $ariza->user->Guruh ?? '—' }}
                        </td>

                        {{-- Status --}}
                        <td class="text-center">

                            @if($ariza->status)

                                <span class="ar-badge"
                                    style="background:#d4edda;color:#155724;">
                                    Aktiv
                                </span>

                            @else

                                <span class="ar-badge"
                                    style="background:#fde2e2;color:#b91c1c;">
                                    Bloklangan
                                </span>

                            @endif

                        </td>

                        {{-- Amal --}}
                        <td>

                            <div style="display:flex;gap:8px;">

                                {{-- Natijalar (test tanlash orqali) --}}
                                <button type="button" class="natija-btn"
                                    data-user="{{ $ariza->user->id }}"
                                    data-user-name="{{ $ariza->user['To‘liq_ismi'] ?? '—' }}"
                                    style="background:#EEEDFE; color:#3C3489; border:none;
                                        padding:6px 12px; border-radius:8px; cursor:pointer; font-size:12px;">
                                    <i class="bx bx-bar-chart-alt-2"></i> Natijalar
                                </button>

                                <form action="{{ route('mini_maktab.status.toggle',$ariza->id) }}"
                                    method="POST">

                                    @csrf

                                    <button type="submit"
                                        style="background:{{ $ariza->status ? '#ef4444' : '#10b981' }};
                                        color:#fff;
                                        border:none;
                                        padding:6px 12px;
                                        border-radius:8px;
                                        cursor:pointer;
                                        font-size:12px;">

                                        <i class="bx {{ $ariza->status ? 'bx-block' : 'bx-check-circle' }}"></i>

                                        {{ $ariza->status ? 'Bloklash' : 'Aktivlash' }}

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6"
                            style="padding:35px;text-align:center;color:#888;">
                            Talabalar topilmadi.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>
    </div>

</div>
            <div class="ar-pagination">{{ $talabalar->links() }}</div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         MAVZU YARATISH MODALI
    ══════════════════════════════════════════ --}}
    <div id="mavzu-modal"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);
               z-index:9999; align-items:center; justify-content:center;">
        <div
            style="background:#fff; border-radius:14px; padding:28px; width:100%; max-width:420px; box-shadow:0 8px 32px rgba(0,0,0,.18);">
            <div style="font-size:16px; font-weight:600; margin-bottom:18px;">Yangi bo'lim qo'shish</div>

            <form action="{{ route('mini_maktab.mavzu.yarat', [$bolim->id, $subject->id]) }}" method="POST">
                @csrf

                <div style="margin-bottom:14px;">
                    <label style="font-size:13px; font-weight:500; display:block; margin-bottom:6px;">Bo'lim
                        turi</label>
                    <select name="tur" required
                        style="width:100%; padding:9px 12px; border:1px solid #e0e0e0; border-radius:8px; font-size:13px;">
                        <option value="mavzu">📘 Mavzu</option>
                        <option value="oraliq">📝 Oraliq nazorat</option>
                        <option value="yakuniy">🏆 Yakuniy nazorat</option>
                    </select>
                </div>

                <div style="margin-bottom:20px;">
                    <label style="font-size:13px; font-weight:500; display:block; margin-bottom:6px;">Nomi</label>
                    <input type="text" name="nomi" required placeholder="Bo'lim nomini kiriting..."
                        style="width:100%; padding:9px 12px; border:1px solid #e0e0e0; border-radius:8px; font-size:13px; box-sizing:border-box;">
                </div>

                <div style="display:flex; gap:10px;">
                    <button type="button" onclick="document.getElementById('mavzu-modal').style.display='none'"
                        style="flex:1; padding:10px; border:1px solid #e0e0e0; border-radius:8px;
                               background:#fff; cursor:pointer; font-size:13px;">
                        Bekor
                    </button>
                    <button type="submit"
                        style="flex:1; padding:10px; border:none; border-radius:8px;
                               background:#3C3489; color:#fff; cursor:pointer; font-size:13px; font-weight:500;">
                        Yaratish
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         NATIJALAR: TEST TANLASH MODALI
    ══════════════════════════════════════════ --}}
    <div id="natija-tanlash-modal"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);
               z-index:9999; align-items:center; justify-content:center; padding:16px;">
        <div style="background:#fff; border-radius:14px; padding:24px; width:100%; max-width:420px;
             box-shadow:0 8px 32px rgba(0,0,0,.18);">

            <div style="font-size:16px; font-weight:600; margin-bottom:4px; display:flex; align-items:center; gap:8px;">
                <i class="bx bx-bar-chart-alt-2" style="color:#3C3489;"></i> Natijalar
            </div>
            <div style="font-size:12px; color:#888; margin-bottom:16px;">
                <span id="natija-user-name">—</span> uchun qaysi testni ko'rmoqchisiz?
            </div>

            @php
                $turRanglar = [
                    'mavzu'   => ['bg' => '#EEEDFE', 'txt' => '#3C3489', 'icon' => 'bx-book-content'],
                    'oraliq'  => ['bg' => '#fff3cd', 'txt' => '#856404', 'icon' => 'bx-notepad'],
                    'yakuniy' => ['bg' => '#d1fae5', 'txt' => '#065f46', 'icon' => 'bx-medal'],
                ];
            @endphp

            <div style="display:flex; flex-direction:column; gap:8px; max-height:300px; overflow-y:auto;">
                @forelse ($barchaTestMateriallari as $material)
                    @php $r = $turRanglar[$material->tur] ?? ['bg' => '#f0f0f0', 'txt' => '#444', 'icon' => 'bx-file']; @endphp
                    <a href="#" class="natija-mavzu-link"
                        data-route="{{ route('mini_maktab.talaba.sessions', [$bolim->id, $subject->id, '__USER__', $material->id]) }}"
                        style="display:flex; align-items:center; gap:10px; padding:10px 12px;
                               border:1px solid #eee; border-radius:10px; text-decoration:none; color:#333;">
                        <div style="width:32px; height:32px; border-radius:8px; background:{{ $r['bg'] }};
                             display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="bx {{ $r['icon'] }}" style="color:{{ $r['txt'] }}; font-size:15px;"></i>
                        </div>
                        <div>
                            <div style="font-size:13px; font-weight:600;">{{ $material->nomi }}</div>
                            <div style="font-size:11px; color:{{ $r['txt'] }}; font-weight:500;">
                                {{ ucfirst($material->tur) }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="font-size:13px; color:#bbb; text-align:center; padding:16px;">
                        Bu fanda hali testlar yo'q.
                    </div>
                @endforelse
            </div>

            <button type="button" onclick="document.getElementById('natija-tanlash-modal').style.display='none'"
                style="width:100%; margin-top:16px; padding:10px; border:1px solid #e0e0e0; border-radius:8px;
                       background:#fff; cursor:pointer; font-size:13px;">
                Yopish
            </button>
        </div>
    </div>

    <script>
        let tanlanganUser = null;

        document.querySelectorAll('.natija-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                tanlanganUser = this.dataset.user;
                document.getElementById('natija-user-name').textContent = this.dataset.userName;
                document.getElementById('natija-tanlash-modal').style.display = 'flex';
            });
        });

        document.querySelectorAll('.natija-mavzu-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                if (!tanlanganUser) return;
                window.location = this.dataset.route.replace('__USER__', tanlanganUser);
            });
        });

        // Backdrop bosilsa yopiladi
        document.getElementById('natija-tanlash-modal').addEventListener('click', function (e) {
            if (e.target === this) this.style.display = 'none';
        });
    </script>

</x-layouts.sidebar>