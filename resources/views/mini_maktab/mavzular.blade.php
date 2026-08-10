<x-layouts.sidebar>
    <x-slot:title>{{ $guruh->nomi ?? $subject->nomi }} — Mavzular</x-slot:title>

    <div class="oz-wrap">

        {{-- ══════════════════════════════════════════
             HEADER
        ══════════════════════════════════════════ --}}
        <div
            style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <a href="{{ route('mini_maktab.fanlar', $bolim->id) }}" class="ar-btn">
                    <i class="bx bx-arrow-back"></i>
                </a>
                <div>
                    <div class="oz-title" style="margin:0;">{{ $guruh->nomi ?? $subject->nomi }}</div>
                    <div style="font-size:12px; color:#888;">{{ $bolim->nomi }}</div>
                </div>
            </div>

            @if ($tanlanganTeacherId)
                <button onclick="document.getElementById('mavzu-modal').style.display='flex'" class="ar-btn"
                    style="background:#3C3489; color:#fff; gap:6px;">
                    <i class="bx bx-plus"></i> Yangi bo'lim qo'shish
                </button>
            @endif
        </div>

        <style>
            .tb-card {
                background: var(--jd-glass);
                backdrop-filter: blur(var(--jd-blur));
                -webkit-backdrop-filter: blur(var(--jd-blur));
                border: 1px solid var(--jd-glass-border);
                border-radius: var(--jd-radius-lg);
                box-shadow: var(--jd-glass-shadow);
                padding: 18px 20px;
                margin-bottom: 20px;
            }

            .tb-head {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 14px;
                flex-wrap: wrap;
            }

            .tb-head-icon {
                width: 38px;
                height: 38px;
                border-radius: 11px;
                background: linear-gradient(135deg, var(--jd-accent), var(--jd-accent-2));
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                flex-shrink: 0;
                box-shadow: 0 4px 12px rgba(108, 92, 231, .32);
            }

            .tb-title {
                font-size: 14px;
                font-weight: 700;
                color: var(--jd-ink);
            }

            .tb-subtitle {
                font-size: 12px;
                color: var(--jd-muted);
                margin-top: 2px;
            }

            .tb-form {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                flex-wrap: wrap;
                margin-bottom: 6px;
            }

            .tb-field {
                flex: 1;
                min-width: 240px;
                position: relative;
            }

            .tb-search-icon {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #a9a4c9;
                font-size: 16px;
                pointer-events: none;
            }

            .tb-search-input {
                width: 100%;
                padding-left: 36px;
            }

            .tb-item-avatar {
                width: 24px;
                height: 24px;
                border-radius: 7px;
                background: var(--jd-accent-soft-2);
                color: var(--jd-accent-2);
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .tb-submit {
                flex-shrink: 0;
                height: 42px;
            }

            .tb-empty,
            .tb-empty-notice {
                font-size: 13px;
                color: var(--jd-muted);
                padding: 12px 14px;
                background: #fafafa;
                border: 1px dashed #e0e0e0;
                border-radius: 8px;
            }

            .tb-empty-notice {
                display: flex;
                align-items: center;
                gap: 8px;
                background: #fff8e6;
                border: 1px solid #ffe6a1;
                color: #8a6d1d;
                margin-bottom: 20px;
            }

            .tb-cards {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 14px;
            }

            .tb-teacher-card {
                display: flex;
                align-items: center;
                gap: 8px;
                min-width: 190px;
                background: #fff;
                border: 1px solid #eee;
                border-radius: 12px;
                padding: 12px 14px;
                transition: border-color .15s, background .15s;
            }

            .tb-teacher-card.is-active {
                background: var(--jd-accent-soft, #EEEDFE);
                border-color: var(--jd-accent, #3C3489);
            }

            .tb-teacher-card.is-full {
                border-color: #ef4444;
            }

            .tb-teacher-card-link {
                display: flex;
                align-items: center;
                gap: 10px;
                text-decoration: none;
                color: inherit;
                flex: 1;
                min-width: 0;
            }

            .tb-teacher-name {
                font-size: 13.5px;
                font-weight: 600;
                color: #212529;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .tb-teacher-count {
                font-size: 12px;
                color: #888;
                margin-top: 2px;
            }

            .tb-teacher-card.is-full .tb-teacher-count {
                color: #ef4444;
                font-weight: 600;
            }

            .tb-teacher-remove {
                background: none;
                border: none;
                color: #bbb;
                font-size: 14px;
                cursor: pointer;
                padding: 4px;
                flex-shrink: 0;
            }

            .tb-teacher-remove:hover {
                color: #ef4444;
            }

            @media (max-width: 640px) {

                .tb-form,
                .tb-cards {
                    flex-direction: column;
                    align-items: stretch;
                }

                .tb-submit {
                    justify-content: center;
                }
            }
        </style>

        {{-- ══════════════════════════════════════════
             O'QITUVCHILAR — guruh kartochkalari (faqat admin)
        ══════════════════════════════════════════ --}}
        @if (auth()->user()?->role === 'admin')
            @if (! $guruh)
                <div class="tb-empty-notice">
                    <i class='bx bx-info-circle'></i>
                    Bu fan hali "katta fan" guruhiga (subjects_to_subject) biriktirilmagan — o'qituvchi
                    kartochkalari faqat guruhlangan fanlar uchun ishlaydi.
                </div>
            @else
                <div class="tb-card">
                    <div class="tb-head">
                        <div class="tb-head-icon"><i class='bx bx-group'></i></div>
                        <div>
                            <div class="tb-title">
                                O'qituvchilar — {{ $guruh->teachers->count() }} ta biriktirilgan
                                @if ($guruh->teachers->count() < $kerakliTeacher)
                                    <span style="color:#ef4444;">(yana kamida
                                        {{ $kerakliTeacher - $guruh->teachers->count() }} ta kerak)</span>
                                @endif
                            </div>
                            <div class="tb-subtitle">
                                Jami {{ $jamiTalaba }} ta talaba · har biriga tavsiya etilgan limit — 30 ta
                                @if ($bolinmagan > 0)
                                    · <b style="color:#b45309;">{{ $bolinmagan }} ta talaba hali hech kimga
                                        biriktirilmagan</b>
                                @endif
                            </div>
                        </div>

                        @if ($guruh->teachers->isNotEmpty() && $bolinmagan > 0)
                            <form action="{{ route('mini_maktab.guruh.avto_taqsimla', $guruh->id) }}" method="POST"
                                style="margin-left:auto;">
                                @csrf
                                <input type="hidden" name="bolim_id" value="{{ $bolim->id }}">
                                <button type="submit" class="ar-btn ar-btn-ok">
                                    <i class="bx bx-shuffle"></i> Bo'shlarni avto-taqsimla
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Yangi o'qituvchi qidirib qo'shish --}}
                    <form action="{{ route('mini_maktab.guruh.teacher_qosh', $guruh->id) }}" method="POST"
                        class="tb-form" id="tb-teacher-add-form">
                        @csrf

                        <div class="tb-field">
                            <i class="bx bx-search tb-search-icon"></i>
                            <input type="text" id="tb_teacher_search" class="arizalar-search tb-search-input"
                                placeholder="O'qituvchini qidiring (ism yoki ID)..." autocomplete="off">

                            <div id="tb_teacher_results" class="search-dropdown">
                                @forelse ($oqituvchilar as $t)
                                    @php $tName = $t->name ?? ($t['To‘liq_ismi'] ?? ('ID ' . $t->id)); @endphp
                                    <div class="search-item" data-id="{{ $t->id }}" data-name="{{ $tName }}">
                                        <span class="tb-item-avatar">{{ mb_substr($tName, 0, 1) }}</span>
                                        <span>{{ $tName }}</span>
                                    </div>
                                @empty
                                    <div class="search-item" style="cursor:default; color:#a9a4c9;">
                                        O'qituvchilar topilmadi
                                    </div>
                                @endforelse
                            </div>

                            <input type="hidden" name="teacher_id" id="tb_hidden_teacher_id" required>
                        </div>

                        <input type="number" name="max_talaba" class="arizalar-search" style="width:110px;"
                            placeholder="30" value="30" min="1" max="200" title="Shu o'qituvchi uchun limit">

                        <button type="submit" class="ar-btn ar-btn-ok tb-submit">
                            <i class="bx bx-plus"></i> Qo'shish
                        </button>
                    </form>

                    {{-- Kartochkalar --}}
                    @if ($guruh->teachers->isEmpty())
                        <div class="tb-empty">Hali birorta o'qituvchi biriktirilmagan. Yuqoridan qidirib qo'shing.
                        </div>
                    @else
                        <div class="tb-cards">
                            @foreach ($guruh->teachers as $card)
                                @php
                                    $band = $bandSoni[$card->teacher_id] ?? 0;
                                    $tolgan = $band > $card->max_talaba;
                                    $tanlangan = $tanlanganTeacherId === $card->teacher_id;
                                @endphp
                                <div
                                    class="tb-teacher-card {{ $tanlangan ? 'is-active' : '' }} {{ $tolgan ? 'is-full' : '' }}">
                                    <a href="{{ request()->fullUrlWithQuery(['teacher_id' => $card->teacher_id]) }}"
                                        class="tb-teacher-card-link">
                                        <div class="tb-item-avatar" style="width:32px; height:32px; font-size:13px;">
                                            {{ mb_substr($card->teacher->name ?? ($card->teacher['To‘liq_ismi'] ?? '?'), 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="tb-teacher-name">
                                                {{ $card->teacher->name ?? ($card->teacher['To‘liq_ismi'] ?? 'ID ' . $card->teacher_id) }}
                                            </div>
                                            <div class="tb-teacher-count">
                                                {{ $band }} / {{ $card->max_talaba }} talaba
                                                @if ($tolgan)
                                                    ⚠️
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                    <form action="{{ route('mini_maktab.guruh.teacher_ochir', $card->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Bu o\'qituvchi va uning barcha talabalari bo\'shatilsinmi?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="tb-teacher-remove">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @endif

        {{-- ══════════════════════════════════════════
             MAVZULAR — faqat tanlangan o'qituvchi bo'yicha
        ══════════════════════════════════════════ --}}
        @if (! $tanlanganTeacherId)
            <div class="tb-empty-notice">
                <i class='bx bx-user-pin'></i>
                Mavzu va materiallarni ko'rish uchun yuqoridan o'qituvchini tanlang.
            </div>
        @else
            @php
                $turlar = [
                    'mavzu' => ['nomi' => 'Mavzular', 'icon' => 'bx-book-content', 'rang' => '#EEEDFE', 'txt' => '#3C3489'],
                    'oraliq' => ['nomi' => 'Oraliq nazorat', 'icon' => 'bx-notepad', 'rang' => '#fff3cd', 'txt' => '#856404'],
                    'yakuniy' => ['nomi' => 'Yakuniy nazorat', 'icon' => 'bx-medal', 'rang' => '#d1fae5', 'txt' => '#065f46'],
                ];
            @endphp

            @foreach ($turlar as $turKey => $tur)
                <div style="margin-bottom:28px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div
                                style="width:30px; height:30px; border-radius:7px; background:{{ $tur['rang'] }};
                                 display:flex; align-items:center; justify-content:center;">
                                <i class="bx {{ $tur['icon'] }}" style="color:{{ $tur['txt'] }};"></i>
                            </div>
                            <span
                                style="font-size:15px; font-weight:600; color:{{ $tur['txt'] }};">{{ $tur['nomi'] }}</span>
                            <span style="font-size:12px; color:#aaa;">
                                ({{ ($mavzular[$turKey] ?? collect())->count() }} ta)
                            </span>
                        </div>
                    </div>

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
                                                <span
                                                    style="font-size:13px; color:#888;">{{ $mavzu->materiallar_count }}
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
        @endif

        @php
            // Natijalar tanlash oynasi uchun: tanlangan o'qituvchining barcha faol testlari
            $barchaTestMateriallari = collect();
            foreach ($mavzular as $mavzularRoyxati) {
                foreach ($mavzularRoyxati as $mavzuX) {
                    foreach ($mavzuX->materiallar()->where('tur', 'test')->where('faol', 1)->get() as $materialX) {
                        $barchaTestMateriallari->push(
                            (object) ['id' => $materialX->id, 'nomi' => $mavzuX->nomi, 'tur' => $mavzuX->tur],
                        );
                    }
                }
            }
        @endphp

        {{-- ══════════════════════════════════════════
             TALABALAR JADVALI — guruh bo'yicha, filtrlar bilan
        ══════════════════════════════════════════ --}}
        <div style="margin-top:32px;">
            <div
                style="font-size:15px; font-weight:600; margin-bottom:10px; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <i class="bx bx-group" style="color:#3C3489;"></i> Talabalar
                @if (auth()->user()?->role === 'admin')
                    <div style="margin-left:auto; display:flex; gap:6px;">
                        <form action="{{ route('mini_maktab.status.all', [$bolim->id, $subject->id]) }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="status" value="1">
                            <button type="submit" class="ar-btn"
                                style="background:#d1fae5; color:#065f46; font-size:12px; border:none; cursor:pointer;">
                                ✔ Barchasini aktiv
                            </button>
                        </form>
                        <form action="{{ route('mini_maktab.status.all', [$bolim->id, $subject->id]) }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="status" value="0">
                            <button type="submit" class="ar-btn"
                                style="background:#fee2e2; color:#b91c1c; font-size:12px; border:none; cursor:pointer;">
                                ✕ Barchasini blok
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Filtrlar --}}
            <form method="GET" action="{{ route('mini_maktab.mavzular', [$bolim->id, $subject->id]) }}"
                style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px;">
                <input type="text" name="ism" class="arizalar-search" placeholder="Talaba ismi..."
                    value="{{ request('ism') }}">
                <input type="text" name="guruh_nomi" class="arizalar-search" style="width:140px;"
                    placeholder="Guruh" value="{{ request('guruh_nomi') }}">
                <input type="text" name="kurs" class="arizalar-search" style="width:100px;" placeholder="Kurs"
                    value="{{ request('kurs') }}">
                <input type="hidden" name="teacher_id" value="{{ request('teacher_id') }}">
                <button type="submit" class="ar-btn ar-btn-ok"><i class="bx bx-search"></i> Filtrlash</button>
                @if (request()->anyFilled(['ism', 'guruh_nomi', 'kurs', 'teacher_id']))
                    <a href="{{ route('mini_maktab.mavzular', [$bolim->id, $subject->id]) }}" class="ar-btn">✕
                        Tozalash</a>
                @endif
            </form>

            <div style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:18px;">

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
                        <div style="font-size:20px; font-weight:700; color:#27500A;">{{ $aktiv }}</div>
                        <div style="font-size:12px; color:#27500A;">Aktiv</div>
                    </div>
                    <div style="flex:1; background:#fdecec; padding:10px; border-radius:10px;">
                        <div style="font-size:20px; font-weight:700; color:#b91c1c;">{{ $blok }}</div>
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
                                <th>O'qituvchi</th>
                                <th>Status</th>
                                <th style="width:170px;">Amal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($talabalar as $ariza)
                                <tr>
                                    <td class="ar-id text-center">{{ $talabalar->firstItem() + $loop->index }}</td>

                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div class="ar-avatar">
                                                {{ mb_substr($ariza->user['To‘liq_ismi'] ?? 'N', 0, 2) }}
                                            </div>
                                            <div>
                                                <div style="font-weight:600;">
                                                    {{ $ariza->user['To‘liq_ismi'] ?? '—' }}
                                                </div>
                                                <small style="color:#888;">{{ $ariza->user->email ?? '—' }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">{{ $ariza->user->Kurs ?? '—' }}-kurs</td>
                                    <td class="text-center">{{ $ariza->user->Guruh ?? '—' }}</td>

                                    {{-- O'qituvchi --}}
                                    <td>
                                        @if (auth()->user()?->role === 'admin' && $guruh && $guruh->teachers->isNotEmpty())
                                            <select class="teacher-ozgartir arizalar-search"
                                                data-ariza="{{ $ariza->id }}" style="font-size:12px;">
                                                <option value="">— tanlanmagan —</option>
                                                @foreach ($guruh->teachers as $card)
                                                    <option value="{{ $card->teacher_id }}"
                                                        {{ $ariza->teacher_id == $card->teacher_id ? 'selected' : '' }}>
                                                        {{ $card->teacher->name ?? ($card->teacher['To‘liq_ismi'] ?? 'ID ' . $card->teacher_id) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="teacher-ozgartir-natija" style="font-size:11px;"></span>
                                        @else
                                            <span style="font-size:13px; color:#555;">
                                                {{ $ariza->teacher->name ?? ($ariza->teacher['To‘liq_ismi'] ?? '—') }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if ($ariza->status)
                                            <span class="ar-badge" style="background:#d4edda;color:#155724;">Aktiv</span>
                                        @else
                                            <span class="ar-badge"
                                                style="background:#fde2e2;color:#b91c1c;">Bloklangan</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div style="display:flex;gap:8px;">
                                            <button type="button" class="natija-btn" data-user="{{ $ariza->user->id }}"
                                                data-user-name="{{ $ariza->user['To‘liq_ismi'] ?? '—' }}"
                                                style="background:#EEEDFE; color:#3C3489; border:none;
                                                padding:6px 12px; border-radius:8px; cursor:pointer; font-size:12px;">
                                                <i class="bx bx-bar-chart-alt-2"></i> Natijalar
                                            </button>

                                            @if (auth()->user()?->role === 'admin')
                                                <form action="{{ route('mini_maktab.status.toggle', $ariza->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        style="background:{{ $ariza->status ? '#ef4444' : '#10b981' }};
                                                        color:#fff; border:none; padding:6px 12px; border-radius:8px;
                                                        cursor:pointer; font-size:12px;">
                                                        <i class="bx {{ $ariza->status ? 'bx-block' : 'bx-check-circle' }}"></i>
                                                        {{ $ariza->status ? 'Bloklash' : 'Aktivlash' }}
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding:35px;text-align:center;color:#888;">
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
                <input type="hidden" name="teacher_id" value="{{ $tanlanganTeacherId }}">

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
        <div
            style="background:#fff; border-radius:14px; padding:24px; width:100%; max-width:420px;
             box-shadow:0 8px 32px rgba(0,0,0,.18);">

            <div
                style="font-size:16px; font-weight:600; margin-bottom:4px; display:flex; align-items:center; gap:8px;">
                <i class="bx bx-bar-chart-alt-2" style="color:#3C3489;"></i> Natijalar
            </div>
            <div style="font-size:12px; color:#888; margin-bottom:16px;">
                <span id="natija-user-name">—</span> uchun qaysi testni ko'rmoqchisiz?
            </div>

            @php
                $turRanglar = [
                    'mavzu' => ['bg' => '#EEEDFE', 'txt' => '#3C3489', 'icon' => 'bx-book-content'],
                    'oraliq' => ['bg' => '#fff3cd', 'txt' => '#856404', 'icon' => 'bx-notepad'],
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
                        <div
                            style="width:32px; height:32px; border-radius:8px; background:{{ $r['bg'] }};
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

    {{-- ══════════════════════════════════════════
         SCRIPTLAR
    ══════════════════════════════════════════ --}}
    <script>
        // ── O'qituvchi qidirib qo'shish (search-as-you-type dropdown) ──
        (function() {
            const searchInput = document.getElementById('tb_teacher_search');
            if (!searchInput) return; // guruh yo'q yoki admin emas

            const resultsBox = document.getElementById('tb_teacher_results');
            const hiddenInput = document.getElementById('tb_hidden_teacher_id');
            const items = resultsBox.querySelectorAll('.search-item[data-id]');

            searchInput.addEventListener('input', function() {
                const val = this.value.toLowerCase().trim();
                let found = 0;

                if (val.length > 0) {
                    resultsBox.style.display = 'block';
                    items.forEach(item => {
                        const name = item.getAttribute('data-name').toLowerCase();
                        const id = item.getAttribute('data-id');
                        const show = name.includes(val) || id === val;
                        item.style.display = show ? 'flex' : 'none';
                        if (show) found++;
                    });
                    if (found === 0) resultsBox.style.display = 'none';
                } else {
                    resultsBox.style.display = 'none';
                    hiddenInput.value = '';
                }
            });

            searchInput.addEventListener('focus', function() {
                items.forEach(item => item.style.display = 'flex');
                resultsBox.style.display = 'block';
            });

            items.forEach(item => {
                item.addEventListener('click', function() {
                    searchInput.value = this.dataset.name;
                    hiddenInput.value = this.dataset.id;
                    resultsBox.style.display = 'none';
                });
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.style.display = 'none';
                }
            });
        })();

        // ── Natijalar modali ──
        let tanlanganUser = null;

        document.querySelectorAll('.natija-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                tanlanganUser = this.dataset.user;
                document.getElementById('natija-user-name').textContent = this.dataset.userName;
                document.getElementById('natija-tanlash-modal').style.display = 'flex';
            });
        });

        document.querySelectorAll('.natija-mavzu-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                if (!tanlanganUser) return;
                window.location = this.dataset.route.replace('__USER__', tanlanganUser);
            });
        });

        document.getElementById('natija-tanlash-modal').addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });

        // ── Talaba uchun o'qituvchini qo'lda o'zgartirish (AJAX, cheklovsiz) ──
        const teacherOzgartirTemplate = "{{ route('mini_maktab.ariza.teacher_ozgartir', ['ariza' => 999999999]) }}";

        document.querySelectorAll('.teacher-ozgartir').forEach(function(sel) {
            sel.addEventListener('change', function() {
                const url = teacherOzgartirTemplate.replace('999999999', this.dataset.ariza);
                const natijaEl = this.nextElementSibling;

                fetch(url, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            teacher_id: this.value || null
                        })
                    })
                    .then(r => r.json())
                    .then(function(data) {
                        if (!data.success) return;

                        if (natijaEl && natijaEl.classList.contains('teacher-ozgartir-natija')) {
                            natijaEl.textContent = data.teacher_id ? (data.band + ' / ' + data.max) : '';
                            natijaEl.style.color = data.oshib_ketdi ? '#ef4444' : '#888';
                        }

                        sel.style.borderColor = data.oshib_ketdi ? '#ef4444' : '';
                    })
                    .catch(function() {
                        alert('Xatolik yuz berdi, qaytadan urinib ko\'ring.');
                    });
            });
        });
    </script>

</x-layouts.sidebar>