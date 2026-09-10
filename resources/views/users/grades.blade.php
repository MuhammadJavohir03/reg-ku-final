<x-layouts.sidebar>
    <x-slot:title>Talaba natijalari</x-slot:title>

    <div class="oz-wrap">

        <div class="oz-toolbar">
            <a href="{{ route('users.edit', $user->id) }}" class="ar-btn">← Orqaga</a>

            <div style="display:flex; align-items:center; gap:10px;">
                <div class="ar-avatar">
                    {{ Str::of($user->{"To‘liq_ismi"} ?? $user->email)->substr(0, 1) }}
                </div>
                <div>
                    <span class="oz-title">{{ $user->{"To‘liq_ismi"} ?? $user->email }}</span>
                </div>
                <br>
                <div>
                    <span class="oz-title">{{ $user->Guruh }}</span>
                </div>
            </div>
        </div>

        @php
            $umumiyList = $grades->pluck('umumiy')->filter(fn($v) => $v !== null);
            $orta = $umumiyList->count() ? round($umumiyList->avg(), 1) : null;
            $ozlashtirgan = $grades->where('umumiy', '>=', 60)->count();
        @endphp

        <!-- O'QUV YILI FILTERI -->
        <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
            <form method="GET" action="{{ route('users.grades', $user->id) }}"
                style="display: flex; gap: 10px; align-items: center;">
                <select name="oquv_yili_id" onchange="this.form.submit()" class="form-select"
                    style="padding: 8px 14px; border-radius: 8px; border: 1px solid #ddd; background-color: #fff; font-size: 14px; cursor: pointer;">
                    <option value="">-- Barcha o'quv yillari --</option>
                    @foreach ($oquvYillari as $oquvYili)
                        <option value="{{ $oquvYili->id }}" {{ $selectedOquvYili == $oquvYili->id ? 'selected' : '' }}>
                            {{ $oquvYili->nomi }}
                        </option>
                    @endforeach
                </select>

                @if ($selectedOquvYili)
                    <a href="{{ route('users.grades', $user->id) }}"
                        style="padding: 8px 12px; background: #ff7c7c; color: #ffffff; border-radius: 8px; text-decoration: none; font-size: 13px;">
                        ✕ Tozalash
                    </a>
                @endif
            </form>
        </div>

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
                Ushbu talaba uchun hali natijalar kiritilmagan
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
                            <div class="grade-metric">
                                <span class="grade-metric-value" style="font-size:13px;">{{ $grade->bepul ? 'Ha' : 'Yo‘q' }}</span>
                                <span class="grade-metric-label">Bepul</span>
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('select').forEach(select => {
                // Asl select elementini yashirish
                select.classList.add('glass-replaced');

                // Yangi custom wrapper yaratish
                const wrapper = document.createElement('div');
                wrapper.className = 'custom-glass-select';

                const trigger = document.createElement('div');
                trigger.className = 'glass-select-trigger';

                const selectedOption = select.options[select.selectedIndex];
                trigger.innerHTML = `<span>${selectedOption ? selectedOption.text : ''}</span>`;

                const menu = document.createElement('div');
                menu.className = 'glass-select-menu';

                // Option-larni o'qib custom menyuga o'tkazish
                Array.from(select.options).forEach((opt, idx) => {
                    const item = document.createElement('div');
                    item.className = 'glass-select-item' + (idx === select.selectedIndex ?
                        ' selected' : '');
                    item.textContent = opt.text;
                    item.dataset.value = opt.value;

                    item.addEventListener('click', (e) => {
                        e.stopPropagation();

                        // Select qiymatini almashtirish
                        select.value = opt.value;
                        select.dispatchEvent(new Event(
                            'change')); // Eventni ham ishga tushirish

                        // UI ni yangilash
                        trigger.querySelector('span').textContent = opt.text;
                        menu.querySelectorAll('.glass-select-item').forEach(i => i.classList
                            .remove('selected'));
                        item.classList.add('selected');
                        wrapper.classList.remove('open');
                    });

                    menu.appendChild(item);
                });

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    document.querySelectorAll('.custom-glass-select').forEach(w => {
                        if (w !== wrapper) w.classList.remove('open');
                    });
                    wrapper.classList.toggle('open');
                });

                wrapper.appendChild(trigger);
                wrapper.appendChild(menu);
                select.parentNode.insertBefore(wrapper, select.nextSibling);
            });

            // Tashqariga bosganda menyuni yopish
            document.addEventListener('click', () => {
                document.querySelectorAll('.custom-glass-select').forEach(w => w.classList.remove('open'));
            });
        });
    </script>
</x-layouts.sidebar>
