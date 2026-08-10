<x-layouts.sidebar>
    <x-slot:title>Mening natijalarim</x-slot:title>

    <div class="oz-wrap">

        @php
            $umumiyList = $grades->pluck('umumiy')->filter(fn($v) => $v !== null);
            $orta = $umumiyList->count() ? round($umumiyList->avg(), 1) : null;
            $ozlashtirgan = $grades->where('umumiy', '>=', 60)->count();
        @endphp

        <div class="grades-summary">
            <div class="grades-summary-item">
                <span class="grades-summary-value">{{ $grades->count() }}</span>
                <span class="grades-summary-label">Fanlar soni</span>
            </div>
            <div class="grades-summary-item">
                <span class="grades-summary-value">{{ $orta ?? '—' }}</span>
                <span class="grades-summary-label">O'rtacha umumiy baho</span>
            </div>
            <div class="grades-summary-item">
                <span class="grades-summary-value">{{ $ozlashtirgan }} / {{ $grades->count() }}</span>
                <span class="grades-summary-label">O'zlashtirilgan fanlar</span>
            </div>
        </div>

        @if ($grades->isEmpty())
            <div class="oz-empty">
                <i class="bx bx-file-blank"></i>
                Hozircha sizning natijalaringiz kiritilmagan
            </div>
        @else
            <div class="grades-grid">
                @foreach ($grades as $grade)
                    @php
                        $subject = $grade->subject;
                        $teacher = $subject?->teacher;
                        $passed = (int) $grade->umumiy >= 60;
                    @endphp

                    <div class="grade-card">
                        <div class="grade-card-top">
                            <div class="grade-subject-name">
                                {{ $subject->nomi ?? "Noma'lum fan" }}
                            </div>

                            @if ($passed)
                                <span class="ar-badge ar-badge-ok">
                                    <span class="ar-dot" style="background:currentColor;"></span>
                                    O'zlashtirilgan
                                </span>
                            @else
                                <span class="ar-badge"
                                    style="background:var(--jd-danger-soft); color:var(--jd-danger); border:1px solid rgba(255,107,107,.3);">
                                    <span class="ar-dot" style="background:currentColor;"></span>
                                    O'zlashtirilmagan
                                </span>
                            @endif
                        </div>

                        @if ($subject?->semster)
                            @if ($subject->oquv_yili === null)
                                <span class="ar-badge ar-badge-accent text-danger">
                                    {{ $subject->semster }}-semestr -
                                    {{ $subject->oquv_yili->nomi ?? "Ko'rsatilmagan" }}
                                </span>
                            @else
                                <span class="ar-badge ar-badge-accent">
                                    {{ $subject->semster }}-semestr - {{ $subject->oquv_yili->nomi }}
                                </span>
                            @endif
                        @endif

                        <div class="grade-metrics">
                            <div class="grade-metric">
                                <span class="grade-metric-value">{{ $grade->joriy_baho }}</span>
                                <span class="grade-metric-label">Joriy</span>
                            </div>
                            <div class="grade-metric">
                                <span class="grade-metric-value">{{ $grade->oraliq_baho }}</span>
                                <span class="grade-metric-label">Oraliq</span>
                            </div>
                            <div class="grade-metric">
                                <span class="grade-metric-value">{{ $grade->joriy_oraliq }}</span>
                                <span class="grade-metric-label">J+O</span>
                            </div>
                            <div class="grade-metric">
                                <span class="grade-metric-value">{{ $grade->yakuniy_baho }}</span>
                                <span class="grade-metric-label">Yakuniy</span>
                            </div>
                            <div class="grade-metric grade-metric-main">
                                <span class="grade-metric-value">{{ $grade->umumiy }}</span>
                                <span class="grade-metric-label">Umumiy</span>
                            </div>
                            <div class="grade-metric">
                                <span class="grade-metric-value" style="font-size:13px;">{{ $grade->davomat }}</span>
                                <span class="grade-metric-label">Davomat</span>
                            </div>
                        </div>

                        <hr class="grade-divider">

                        <div class="grade-card-footer">
                            <div class="grade-teacher">
                                <div class="ar-avatar">
                                    {{ $teacher ? Str::of($teacher->{"To‘liq_ismi"} ?? '?')->substr(0, 1) : '?' }}
                                </div>
                                <div>
                                    <div class="grade-teacher-name">
                                        {{ $teacher->{"To‘liq_ismi"} ?? 'Biriktirilmagan' }}
                                    </div>
                                    <div class="grade-teacher-role">O'qituvchi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-layouts.sidebar>
