<x-layouts.sidebar>
    <x-slot:title>Fanlar</x-slot:title>

    <div class="oz-wrap">

        {{-- HEADER --}}
        <div
            style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
            <div class="oz-title" style="margin:0;">Fanlar katalogi</div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('subject.create') }}" class="ar-btn ar-btn-ok">
                    <i class="bx bx-plus"></i> Yangi fan
                </a>
                <a href="{{ route('category.create') }}" class="ar-btn">
                    <i class="bx bx-plus"></i> Yangi yo'nalish
                </a>
            </div>
        </div>

        {{-- QIDIRUV --}}
        <form action="{{ route('subject.index') }}" method="GET"
            style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:16px;">
            <div style="position:relative; flex:1; min-width:200px;">
                <i class="bx bx-search"
                    style="position:absolute; left:10px; top:50%;
                    transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                <input type="text" name="search" class="arizalar-search" style="width:100%; padding-left:34px;"
                    placeholder="Fan nomi boyicha qidirish..." value="{{ request('search') }}">
            </div>
            @if (request('search'))
                <a href="{{ route('subject.index') }}" class="ar-btn ar-btn-rej">✕</a>
            @endif
            <select name="page_size" class="arizalar-search" style="width:130px;" onchange="this.form.submit()">
                <option value="10" {{ request('page_size') == 10 ? 'selected' : '' }}>10 ta</option>
                <option value="20" {{ request('page_size') == 20 ? 'selected' : '' }}>20 ta</option>
                <option value="50" {{ request('page_size') == 50 ? 'selected' : '' }}>50 ta</option>
                <option value="100" {{ request('page_size') == 100 ? 'selected' : '' }}>100 ta</option>
            </select>
            <button type="submit" class="ar-btn ar-btn-ok">
                <i class="bx bx-search"></i> Qidirish
            </button>
        </form>

        {{-- FANLAR JADVALI --}}
        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:50px;">№</th>
                        <th>Fan nomi</th>
                        <th style="width:140px;">Yo'nalish</th>
                        <th style="width:100px;">Semestr</th>
                        <th style="width:120px;">Turi</th>
                        <th style="width:180px;">O'qituvchi</th>
                        <th style="width:100px;">Holat</th>
                        <th style="width:160px;">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subjects as $index => $subject)
                        @php
                            $teacher = $subject->teacher->toliq_ismi ?? 'Tayinlanmagan';
                        @endphp
                        <tr>
                            <td class="ar-id">{{ $subjects->firstItem() + $index }}</td>

                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div
                                        style="width:8px; height:8px; border-radius:50%; flex-shrink:0;
                                        background:{{ $subject->grades_exists ? '#10b981' : '#a1a1a1' }};">
                                    </div>
                                    <span style="font-size:14px; font-weight:500;">{{ $subject->nomi }}</span>
                                    @if ($subject->grades_exists)
                                        <span
                                            style="background:#e6f4ea; color:#10b981; padding:2px 7px;
                                            border-radius:6px; font-size:11px; font-weight:700;
                                            border:1px solid #a7f3d0;">
                                            <i class="fas fa-circle-check"></i> Natija bor
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td style="font-size:13px; color:#888;">
                                {{ $subject->category->nomi ?? 'Umumiy' }}
                            </td>

                            <td style="font-size:13px; text-align:center;">
                                <span
                                    style="background:#EEEDFE; color:#3C3489; padding:3px 10px;
                                    border-radius:6px; font-size:12px; font-weight:600;">
                                    {{ $subject->semster }}-sem
                                </span>
                            </td>

                            <td style="font-size:13px; color:#888;">
                                {{ $subject->lesson_type->nomi ?? 'Dars' }}
                            </td>

                            <td>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <div class="ar-avatar"
                                        style="width:28px; height:28px; font-size:11px; flex-shrink:0;">
                                        {{ mb_substr($teacher, 0, 2) }}
                                    </div>
                                    <span style="font-size:12px; color:#555;">{{ $teacher }}</span>
                                </div>
                            </td>

                            <td>
                                @if ($subject->grades_exists)
                                    <span class="ar-badge ar-badge-ok">Baholangan</span>
                                @else
                                    <span class="ar-badge" style="background:#f5f5f5; color:#aaa;">Baholanmagan</span>
                                @endif
                            </td>

                            <td onclick="event.stopPropagation()">
                                <div style="display:flex; align-items:center; gap:4px; flex-wrap:wrap;">

                                    {{-- KO'RISH --}}
                                    <a href="{{ route('grades.index', $subject->id) }}" class="ar-btn"
                                        title="Baholarni ko'rish" style="padding:5px 8px;">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    {{-- Vedmostga eksport qilish tugmasi --}}
                                    <a href="{{ route('grades.vedomost.form', $subject->id) }}" class="ar-btn"
                                        target="_blank" title="Vedomostga eksport">
                                        <i class="bx bx-spreadsheet" style="color:#217346;"></i>
                                    </a>
                                    {{-- IMPORT --}}
                                    <form action="{{ route('grades.import', $subject->id) }}" method="POST"
                                        enctype="multipart/form-data" style="display:inline-flex; align-items:center;"
                                        class="grade-import-form">
                                        @csrf
                                        <label class="ar-btn" title="Excel import"
                                            style="cursor:pointer; margin:0; padding:5px 8px;">
                                            <i class="bx bx-import import-icon" style="color:#217346;"></i>
                                            <div class="row-progress"
                                                style="display:none; align-items:center; gap:4px;">
                                                <div style="position:relative; width:28px; height:28px; flex-shrink:0;">
                                                    <svg width="28" height="28"
                                                        style="transform:rotate(-90deg);">
                                                        <circle cx="14" cy="14" r="11" fill="none"
                                                            stroke="#e5e7eb" stroke-width="2.5" />
                                                        <circle class="circle-bar" cx="14" cy="14" r="11"
                                                            fill="none" stroke="#217346" stroke-width="2.5"
                                                            stroke-dasharray="69.1" stroke-dashoffset="69.1"
                                                            style="transition:stroke-dashoffset 0.3s;" />
                                                    </svg>
                                                    <span class="circle-pct"
                                                        style="position:absolute;top:50%;left:50%;
                                                        transform:translate(-50%,-50%);
                                                        font-size:7px;font-weight:700;color:#217346;">0%</span>
                                                </div>
                                            </div>
                                            <input type="file" name="excel_file" accept=".xlsx,.xls,.csv"
                                                style="display:none;">
                                        </label>
                                    </form>

                                    {{-- TOZALASH --}}
                                    @if ($subject->grades_exists)
                                        <form action="{{ route('grades.clear', $subject->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button class="ar-btn ar-btn-rej" style="padding:5px 8px;"
                                                title="Baholarni tozalash"
                                                onclick="return confirm('Barcha baholar ochirisinmi?')">
                                                <i class="bx bx-eraser"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- TAHRIRLASH --}}
                                    <a href="{{ route('subject.edit', $subject->id) }}" class="ar-btn"
                                        style="padding:5px 8px;" title="Tahrirlash">
                                        <i class="bx bx-edit"></i>
                                    </a>

                                    {{-- O'CHIRISH --}}
                                    <form action="{{ route('subject.destroy', $subject->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button class="ar-btn ar-btn-rej" style="padding:5px 8px;" title="O'chirish"
                                            onclick="return confirm('Ochirilsinmi?')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:2rem; color:#888;">
                                <i class="bx bx-book" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                                Fanlar topilmadi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ar-pagination" style="margin-top:16px;">
            {{ $subjects->withQueryString()->links() }}
        </div>

    </div>

    <script>
        document.querySelectorAll('.grade-import-form').forEach(function(form) {
            form.querySelector('input[type="file"]').addEventListener('change', function() {
                if (!this.files[0]) return;

                var circumference = 69.1;
                var circleBar = form.querySelector('.circle-bar');
                var circlePct = form.querySelector('.circle-pct');
                var rowProgress = form.querySelector('.row-progress');
                var importIcon = form.querySelector('.import-icon');
                var currentPct = 0;

                importIcon.style.display = 'none';
                rowProgress.style.display = 'inline-flex';

                var fakeInterval = setInterval(function() {
                    if (currentPct < 88) {
                        currentPct += 2;
                        var offset = circumference - (currentPct / 100) * circumference;
                        circleBar.setAttribute('stroke-dashoffset', offset);
                        circlePct.textContent = currentPct + '%';
                    }
                }, 250);

                fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function() {
                        clearInterval(fakeInterval);
                        circleBar.setAttribute('stroke-dashoffset', 0);
                        circlePct.textContent = '100%';
                        setTimeout(function() {
                            location.reload();
                        }, 800);
                    })
                    .catch(function() {
                        clearInterval(fakeInterval);
                        rowProgress.style.display = 'none';
                        importIcon.style.display = 'inline';
                        alert('Xatolik yuz berdi!');
                    });
            });
        });
    </script>

</x-layouts.sidebar>
