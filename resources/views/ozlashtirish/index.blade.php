<x-layouts.sidebar>
    <x-slot:title>O'zlashtirish</x-slot:title>

    <div class="oz-wrap">

        <form action="{{ route('ozlashtirish') }}" method="GET">
            <div class="oz-filters">
                <select name="category_id" onchange="this.form.submit()">
                    <option value="">Barcha yo'nalishlar</option>
                    @foreach ($yonalishlar as $y)
                        <option value="{{ $y->id }}" {{ request('category_id') == $y->id ? 'selected' : '' }}>
                            {{ $y->nomi }}
                        </option>
                    @endforeach
                </select>

                <select name="guruh" onchange="this.form.submit()">
                    <option value="">Barcha guruhlar</option>
                    @foreach ($guruhlar as $g)
                        <option value="{{ $g }}" {{ request('guruh') == $g ? 'selected' : '' }}>
                            {{ $g }}</option>
                    @endforeach
                </select>

                <select name="semster" onchange="this.form.submit()">
                    <option value="">Barcha semestrlar</option>
                    @foreach ($semestrlar as $s)
                        <option value="{{ $s }}" {{ request('semster') == $s ? 'selected' : '' }}>
                            {{ $s }}-semestr
                        </option>
                    @endforeach
                </select>

                <input type="text" name="search" placeholder="Ism bo'yicha..." value="{{ request('search') }}">
                <button type="submit">Qidirish</button>
            </div>
        </form>

        <div class="oz-stats">
            <div class="oz-card oz-card-success">
                <div class="oz-card-label">Muvaffaqiyatli</div>
                <div class="oz-card-val">{{ $muvaffaqiyatli }}</div>
                <div class="oz-card-sub">Guruhda jami: {{ $jami }}</div>
                <div class="oz-card-bar">
                    <div class="oz-card-bar-fill oz-bar-green"
                        style="width:{{ $jami > 0 ? round(($muvaffaqiyatli / $jami) * 100) : 0 }}%"></div>
                </div>
            </div>
            <div class="oz-card oz-card-danger">
                <div class="oz-card-label">Qarzdorlar</div>
                <div class="oz-card-val">{{ $qarzdorlar }}</div>
                <div class="oz-card-sub">Jami talabalarning {{ $jami > 0 ? round(($qarzdorlar / $jami) * 100) : 0 }}%
                </div>
                <div class="oz-card-bar">
                    <div class="oz-card-bar-fill oz-bar-red"
                        style="width:{{ $jami > 0 ? round(($qarzdorlar / $jami) * 100) : 0 }}%"></div>
                </div>
            </div>
            <div class="oz-card oz-card-info">
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
            <a href="{{ route('ozlashtirish.export', request()->query()) }}" class="oz-export-btn">↓ Excel export</a>
        </div>

        <div class="oz-table-wrap">
            <table class="oz-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="oz-th-name">Talaba ismi</th>
                        <th rowspan="2" class="oz-th-name">Guruh</th>
                        @foreach ($fanlar as $fan)
                            <th colspan="3" class="oz-fan-header">{{ $fan->nomi }}</th>
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
                    @foreach ($talabalar as $talaba)
                        @php
                            $qarzdor = false;

                            foreach ($fanlar as $fan) {
                                $g = $talaba->getMergedGrade($fan->id);

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
                                    $grade = $talaba->getMergedGrade($fan->id);
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
                    @endforeach
                </tbody>
            </table>
            <div class="ar-pagination">
                {{ $talabalar->withQueryString()->links() }}
            </div>
        </div>

    </div>
</x-layouts.sidebar>
