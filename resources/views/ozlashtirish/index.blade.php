<x-layouts.sidebar>
    <x-slot:title>O'zlashtirish</x-slot:title>

    <div class="oz-wrap">

        <form action="{{ route('ozlashtirish') }}" method="GET" id="oz-filter-form">
            <div class="oz-filters">

                {{-- Yo'nalish --}}
                <div class="oz-combo" data-combo>
                    <input type="text" class="arizalar-search oz-combo-input" autocomplete="off"
                        placeholder="Yo'nalishni tanlang..."
                        value="{{ optional($yonalishlar->firstWhere('id', request('category_id')))->nomi }}">
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    <div class="search-dropdown oz-combo-list">
                        <div class="search-item" data-value="">Barchasi</div>
                        @foreach ($yonalishlar as $y)
                            <div class="search-item" data-value="{{ $y->id }}">{{ $y->nomi }}</div>
                        @endforeach
                    </div>
                </div>

                {{-- Guruh --}}
                <div class="oz-combo {{ !request('category_id') ? 'is-disabled' : '' }}" data-combo
                    {{ !request('category_id') ? 'data-disabled' : '' }}>
                    <input type="text" class="arizalar-search oz-combo-input" autocomplete="off"
                        placeholder="Barcha guruhlar" {{ !request('category_id') ? 'readonly' : '' }}
                        value="{{ request('guruh') }}">
                    <input type="hidden" name="guruh" value="{{ request('guruh') }}">
                    <div class="search-dropdown oz-combo-list">
                        <div class="search-item" data-value="">Barcha guruhlar</div>
                        @foreach ($guruhlar as $g)
                            <div class="search-item" data-value="{{ $g }}">{{ $g }}</div>
                        @endforeach
                    </div>
                </div>

                {{-- Kurs --}}
                <div class="oz-combo {{ !request('category_id') ? 'is-disabled' : '' }}" data-combo
                    {{ !request('category_id') ? 'data-disabled' : '' }}>
                    <input type="text" class="arizalar-search oz-combo-input" autocomplete="off"
                        placeholder="Barcha kurslar" {{ !request('category_id') ? 'readonly' : '' }}
                        value="{{ request('kurs') }}">
                    <input type="hidden" name="kurs" value="{{ request('kurs') }}">
                    <div class="search-dropdown oz-combo-list">
                        <div class="search-item" data-value="">Barcha kurslar</div>
                        @foreach ($kurslar as $k)
                            <div class="search-item" data-value="{{ $k }}">{{ $k }}</div>
                        @endforeach
                    </div>
                </div>

                {{-- Semestr --}}
                <div class="oz-combo {{ !request('category_id') ? 'is-disabled' : '' }}" data-combo
                    {{ !request('category_id') ? 'data-disabled' : '' }}>
                    <input type="text" class="arizalar-search oz-combo-input" autocomplete="off"
                        placeholder="Barcha semestrlar" {{ !request('category_id') ? 'readonly' : '' }}
                        value="{{ request('semster') ? request('semster') . '-semestr' : '' }}">
                    <input type="hidden" name="semster" value="{{ request('semster') }}">
                    <div class="search-dropdown oz-combo-list">
                        <div class="search-item" data-value="">Barcha semestrlar</div>
                        @foreach ($semestrlar as $s)
                            <div class="search-item" data-value="{{ $s }}">{{ $s }}-semestr</div>
                        @endforeach
                    </div>
                </div>

                <input type="text" name="search" class="arizalar-search" placeholder="Ism bo'yicha..."
                    value="{{ request('search') }}" {{ !request('category_id') ? 'disabled' : '' }}>

                <button type="submit" class="ar-btn ar-btn-ok">
                    <i class="fa-solid fa-magnifying-glass"></i> Qidirish
                </button>
            </div>
        </form>

        @if ($yonalishTanlanmagan)
            {{-- Yo'nalish tanlanmagan holat --}}
            <div class="oz-empty-state">
                <i class="fa-solid fa-diagram-project"></i>
                <p>Iltimos, avval yuqoridan <strong>yo'nalishni</strong> tanlang. Natijalar shundan keyin
                    ko'rsatiladi.</p>
            </div>
        @else
            <div class="oz-stats">
                <div class="oz-card oz-card-success">
                    <div class="oz-card-icon"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="oz-card-label">Muvaffaqiyatli</div>
                    <div class="oz-card-val">{{ $muvaffaqiyatli }}</div>
                    <div class="oz-card-sub">Guruhda jami: {{ $jami }}</div>
                    <div class="oz-card-bar">
                        <div class="oz-card-bar-fill oz-bar-green"
                            style="width:{{ $jami > 0 ? round(($muvaffaqiyatli / $jami) * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div class="oz-card oz-card-danger">
                    <div class="oz-card-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div class="oz-card-label">
                        Qarzdorlar
                        @if ($qarzdorlar > 0)
                            <span class="oz-live-dot"></span>
                        @endif
                    </div>
                    <div class="oz-card-val">{{ $qarzdorlar }}</div>
                    <div class="oz-card-sub">Jami talabalarning
                        {{ $jami > 0 ? round(($qarzdorlar / $jami) * 100) : 0 }}%
                    </div>
                    <div class="oz-card-bar">
                        <div class="oz-card-bar-fill oz-bar-red"
                            style="width:{{ $jami > 0 ? round(($qarzdorlar / $jami) * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div class="oz-card oz-card-info">
                    <div class="oz-card-icon"><i class="fa-solid fa-users"></i></div>
                    <div class="oz-card-label">Guruhda jami</div>
                    <div class="oz-card-val">{{ $jami }}</div>
                    <div class="oz-card-sub">Faol talabalar</div>
                    <div class="oz-card-bar">
                        <div class="oz-card-bar-fill oz-bar-blue" style="width:100%"></div>
                    </div>
                </div>
            </div>

            <div class="oz-sub-stats">
                <div class="oz-sub-card">
                    <div class="oz-sub-label">J/O — Joriy oraliq (&lt; 20)</div>
                    <div class="oz-sub-val">{{ $joriyQizil }} ta</div>
                    <div class="oz-sub-row">
                        <span class="oz-green">O'tgan:
                            {{ $jami > 0 ? round((($jami - $joriyQizil) / $jami) * 100) : 0 }}%</span>
                        <span class="oz-red">Qizil: {{ $jami > 0 ? round(($joriyQizil / $jami) * 100) : 0 }}%</span>
                    </div>
                </div>
                <div class="oz-sub-card">
                    <div class="oz-sub-label">U — Umumiy (&lt; 60)</div>
                    <div class="oz-sub-val">{{ $umumiyQizil }} ta</div>
                    <div class="oz-sub-row">
                        <span class="oz-green">O'tgan:
                            {{ $jami > 0 ? round((($jami - $umumiyQizil) / $jami) * 100) : 0 }}%</span>
                        <span class="oz-red">Qizil: {{ $jami > 0 ? round(($umumiyQizil / $jami) * 100) : 0 }}%</span>
                    </div>
                </div>
                <div class="oz-sub-card">
                    <div class="oz-sub-label">D — Davomat (&gt;= 33%)</div>
                    <div class="oz-sub-val">{{ $davomatQizil }} ta</div>
                    <div class="oz-sub-row">
                        <span class="oz-green">Yaxshi:
                            {{ $jami > 0 ? round((($jami - $davomatQizil) / $jami) * 100) : 0 }}%</span>
                        <span class="oz-red">Qizil: {{ $jami > 0 ? round(($davomatQizil / $jami) * 100) : 0 }}%</span>
                    </div>
                </div>
            </div>

            <div class="oz-toolbar">
                <span class="oz-title">Natijalar jadvali</span>
                <a href="{{ route('ozlashtirish.export', request()->query()) }}" class="ar-btn ar-btn-ok oz-export-btn">
                    <i class="fa-solid fa-file-excel"></i> Excel export
                </a>
            </div>

            <div class="oz-table-wrap">
                <table class="oz-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="oz-th-name">Talaba ismi</th>
                            <th rowspan="2" class="oz-th-name">Guruh</th>
                            @foreach ($fanlar as $fan)
                                <th colspan="3" class="oz-fan-header">{{ $fan->nomi }} - {{ $fan->semster }}-Sem</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($fanlar as $fan)
                                <th class="oz-col-header">J/O</th>
                                <th class="oz-col-header">U</th>
                                <th class="oz-col-header">D</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($talabalar as $talaba)
                            @php
                                $qarzdor = false;

                                foreach ($fanlar as $fan) {
                                    $g = $talaba->getMergedGradeForGroup($fan->subject_ids);

                                    if (
                                        ($g->joriy_oraliq ?? 0) < 20 ||
                                        ($g->umumiy ?? 0) < 60 ||
                                        ($g->davomat ?? 0) >= 33
                                    ) {
                                        $qarzdor = true;
                                        break;
                                    }
                                }
                            @endphp
                            <tr class="{{ $qarzdor ? '' : 'oz-row-success' }}">
                                <td class="oz-row-header">{{ $talaba['To‘liq_ismi'] }}</td>
                                <td class="oz-row-header">{{ $talaba->Guruh }}</td>
                                @foreach ($fanlar as $fan)
                                    @php
                                        $grade = $talaba->getMergedGradeForGroup($fan->subject_ids);
                                    @endphp
                                    <td
                                        class="{{ $grade?->joriy_oraliq !== null && $grade->joriy_oraliq < 20 ? 'oz-val-bad' : 'oz-val-ok' }}">
                                        {{ $grade?->joriy_oraliq ?? '-' }}
                                    </td>
                                    <td
                                        class="{{ $grade?->umumiy !== null && $grade->umumiy < 60 ? 'oz-val-bad' : 'oz-val-ok' }}">
                                        {{ $grade?->umumiy ?? '-' }}
                                    </td>
                                    <td
                                        class="{{ $grade?->davomat !== null && $grade->davomat >= 33 ? 'oz-val-bad' : '' }}">
                                        {{ $grade?->davomat !== null ? $grade->davomat . '%' : '-' }}
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + $fanlar->count() * 3 }}">
                                    <div class="oz-empty">
                                        <i class="fa-solid fa-inbox"></i>
                                        Tanlangan filtrlar bo'yicha talaba topilmadi.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="ar-pagination">
                    {{ $talabalar->withQueryString()->links() }}
                </div>
            </div>
        @endif

    </div>

    <script>
        (function () {
            const form = document.getElementById('oz-filter-form');

            document.querySelectorAll('[data-combo]').forEach(function (combo) {
                const input  = combo.querySelector('.oz-combo-input');
                const hidden = combo.querySelector('input[type="hidden"]');
                const list   = combo.querySelector('.oz-combo-list');
                const items  = Array.from(list.querySelectorAll('.search-item'));

                function isDisabled() {
                    return combo.hasAttribute('data-disabled');
                }

                function openList() {
                    if (isDisabled()) return;
                    filterItems(input.value);
                    list.style.display = 'block';
                }

                function closeList() {
                    list.style.display = 'none';
                }

                function filterItems(term) {
                    const t = term.trim().toLowerCase();
                    items.forEach(function (item) {
                        const label = item.textContent.trim().toLowerCase();
                        const match = t === '' || label.includes(t);
                        item.style.display = match ? 'flex' : 'none';
                    });
                }

                input.addEventListener('focus', openList);
                input.addEventListener('click', openList);

                input.addEventListener('input', function () {
                    if (isDisabled()) return;
                    filterItems(input.value);
                    list.style.display = 'block';
                    hidden.value = '';
                });

                items.forEach(function (item) {
                    item.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        hidden.value = item.dataset.value;
                        input.value = item.dataset.value === '' ? '' : item.textContent.trim();
                        closeList();
                        form.requestSubmit();
                    });
                });

                document.addEventListener('click', function (e) {
                    if (!combo.contains(e.target)) closeList();
                });
            });
        })();
    </script>
</x-layouts.sidebar>