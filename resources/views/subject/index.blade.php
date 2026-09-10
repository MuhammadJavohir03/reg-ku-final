<x-layouts.sidebar>
    <x-slot:title>Fanlar</x-slot:title>

    <link rel="stylesheet" href="{{ asset('css/jadvallar.css') }}">

    <div class="oz-wrap">

        {{-- HEADER --}}
        <div
            style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
            <div class="oz-title" style="margin:0;">Fanlar katalogida ({{ $subjectCounts['subject'] }} ta fan mavjud)
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('subject.create') }}" class="ar-btn ar-btn-ok">
                    <i class="bx bx-plus"></i> Yangi fan
                </a>
                <a href="{{ route('category.create') }}" class="ar-btn">
                    <i class="bx bx-plus"></i> Yangi yo'nalish
                </a>
                <a href="{{ route('subject.biriktirish') }}" class="ar-btn">
                    <i class="bx bx-plus"></i> Biriktirish
                </a>
                <button type="button" id="exportAllVedomostBtn" class="ar-btn ar-btn-ok">
                    <i class="bx bx-archive"></i> Vedomostga export
                </button>
                <a href="{{ route('mudir.index') }}" class="ar-btn">
                    <i class="bx bx-id-card"></i> Mudirlar
                </a>

                <form id="bepulSyncForm" action="{{ route('grades.bepul.import') }}" method="POST"
                    enctype="multipart/form-data" style="display:inline;">
                    @csrf
                    <label class="ar-btn ar-btn-ok color-white" title="GPA Excel dan bepul ustunini sinxronlash"
                        style="cursor:pointer; margin:0;">
                        <i class="bx bx-sync color-white" id="bepulSyncIcon"></i>
                        <span id="bepulSyncLabel">Bepul sinxronlash</span>
                        <input type="file" name="bepul_excel" id="bepulExcelInput" accept=".xlsx,.xls"
                            style="display:none;">
                    </label>
                </form>
            </div>

        </div>

        {{-- BARCHA FANLARNI VEDOMOSTGA EXPORT QILISH: progress ko'rsatkichi --}}
        <div id="exportAllProgressWrap" style="display:none; align-items:center; gap:12px; margin-bottom:16px;">
            <div style="position:relative; width:36px; height:36px; flex-shrink:0;">
                <svg width="36" height="36" style="transform:rotate(-90deg);">
                    <circle cx="18" cy="18" r="15" fill="none" stroke="#e5e7eb" stroke-width="3" />
                    <circle id="exportAllProgressCircle" cx="18" cy="18" r="15" fill="none"
                        stroke="#217346" stroke-width="3" stroke-dasharray="94.2" stroke-dashoffset="94.2"
                        style="transition:stroke-dashoffset 0.2s;" />
                </svg>
                <span id="exportAllProgressPct"
                    style="position:absolute;top:50%;left:50%;
                    transform:translate(-50%,-50%); font-size:9px;font-weight:700;color:#217346;">0%</span>
            </div>
            <span id="exportAllProgressText" style="font-size:13px; color:#555;">Tayyorlanmoqda...</span>
        </div>

        {{-- QIDIRUV --}}
        <form action="{{ route('subject.index') }}" method="GET"
            style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:16px;">
            <div style="position:relative; flex:1; min-width:200px;">
                <i class="bx bx-search"
                    style="position:absolute; left:10px; top:50%;
                    transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                <input type="text" name="search" class="arizalar-search" style="width:100%; padding-left:34px;"
                    placeholder="Fan va o'qituvchi boyicha qidirish..." value="{{ request('search') }}">
            </div>
            @if (request('search'))
                <a href="{{ route('subject.index') }}" class="ar-btn ar-btn-rej">✕</a>
            @endif

            {{-- Yo'nalish (category) bo'yicha filter: yozib qidiradigan dropdown --}}
            <div style="position:relative; width:220px;">
                <i class="bx bx-search"
                    style="position:absolute; left:10px; top:50%;
                    transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                <input type="text" id="filter_category_search" class="arizalar-search"
                    style="width:100%; padding-left:34px;" placeholder="Yo'nalish bo'yicha qidirish..."
                    autocomplete="off"
                    value="{{ optional($categories->firstWhere('id', request('category_id')))->nomi ?? optional($categories->firstWhere('id', request('category_id')))->guruh }}">

                <div id="filter_category_results" class="search-dropdown">
                    <div class="search-item" data-id="" data-name="Barcha yo'nalishlar">
                        Barcha yo'nalishlar
                    </div>
                    @foreach ($categories as $cat)
                        <div class="search-item" data-id="{{ $cat->id }}"
                            data-name="{{ $cat->nomi ?? $cat->guruh }}">
                            {{ $cat->nomi ?? $cat->guruh }}
                        </div>
                    @endforeach
                </div>

                <input type="hidden" name="category_id" id="filter_hidden_category_id"
                    value="{{ request('category_id') }}">
            </div>

            {{-- Kursi bo'yicha filter --}}
            <select name="kurs" class="arizalar-search" style="width:120px;" onchange="this.form.submit()">
                <option value="">Barcha kurslar</option>
                @for ($k = 1; $k <= 4; $k++)
                    <option value="{{ $k }}" {{ request('kurs') == $k ? 'selected' : '' }}>
                        {{ $k }}-kurs</option>
                @endfor
            </select>

            {{-- Semestr bo'yicha filter --}}
            <select name="semster" class="arizalar-search" style="width:130px;" onchange="this.form.submit()">
                <option value="">Barcha semestrlar</option>
                @for ($s = 1; $s <= 8; $s++)
                    <option value="{{ $s }}" {{ request('semster') == $s ? 'selected' : '' }}>
                        {{ $s }}-semestr</option>
                @endfor
            </select>

            <select name="page_size" class="arizalar-search" style="width:130px;" onchange="this.form.submit()">
                <option value="10" {{ request('page_size') == 10 ? 'selected' : '' }}>10 ta</option>
                <option value="200" {{ request('page_size') == 200 ? 'selected' : '' }}>200 ta</option>
                <option value="500" {{ request('page_size') == 500 ? 'selected' : '' }}>500 ta</option>
                <option value="600" {{ request('page_size') == 600 ? 'selected' : '' }}>600 ta</option>
                <option value="1000" {{ request('page_size') == 1000 ? 'selected' : '' }}>1000 ta</option>

            </select>
            <button type="submit" class="ar-btn ar-btn-ok">
                <i class="bx bx-search"></i> Qidirish
            </button>
            @if (request('category_id') || request('kurs') || request('semster'))
                <a href="{{ route('subject.index') }}" class="ar-btn ar-btn-rej" title="Filterlarni tozalash">
                    <i class="bx bx-filter-alt"></i> Filterni tozalash
                </a>
            @endif
        </form>

        {{-- FANLAR JADVALI --}}
        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:60px;">ID</th>
                        <th style="width:300px">Fan nomi</th>
                        <th style="width:200px;">O'qituvchi</th>
                        <th style="width:110px;">O'quv yili</th>
                        <th style="width:60px;">Yo'nalish</th>
                        <th style="width:40px;">Krediti</th>
                        <th style="width:80px;">Semestr</th>
                        <th style="width:80px;">Holat</th>
                        <th style="width:195px;">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subjects as $subject)
                        @php
                            $teacher = $subject->teacher['To‘liq_ismi'] ?? 'Tayinlanmagan';
                        @endphp
                        <tr>
                            <td class="ar-id">#{{ $subject->id }}</td>

                            {{-- Fan nomi --}}
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span class="ar-dot"
                                        style="background:{{ $subject->grades_exists ? '#10b981' : '#c9c7db' }};"></span>
                                    <span style="font-size:14px; font-weight:500;">{{ $subject->nomi }}</span>
                                    @if ($subject->grades_exists)
                                        <span class="ar-badge ar-badge-ok">
                                            <i class="fas fa-circle-check"></i> Natija bor
                                        </span>
                                    @endif
                                </div>
                            </td>
                            {{-- O'qituvchi --}}
                            <td>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <div class="ar-avatar" style="width:28px; height:28px; font-size:11px;">
                                        {{ mb_substr($teacher, 0, 2) }}
                                    </div>
                                    <span style="font-size:12px; color:#555;">{{ $teacher }}</span>
                                </div>
                            </td>

                            {{-- O'quv yili --}}
                            <td style="font-size:13px; color:#555;">
                                {{ $subject->oquv_yili->nomi ?? 'Ko\'rsatilmagan' }}
                            </td>

                            {{-- Yo'nalish / Kategoriya --}}
                            <td style="text-align:center;">
                                <span class="ar-badge ar-badge-accent">
                                    {{ $subject->category->guruh }}
                                </span>
                            </td>

                            <td style="font-size:13px; color:#888;">
                                {{ $subject->kredit ?? 'Umumiy' }}
                            </td>

                            {{-- Semestr --}}
                            <td style="text-align:center;">
                                <span class="ar-badge ar-badge-accent">
                                    {{ $subject->semster }}-semestr
                                </span>
                            </td>


                            {{-- Holat --}}
                            <td>
                                @if ($subject->grades_exists)
                                    <span class="ar-badge ar-badge-ok">Baholangan</span>
                                @else
                                    <span class="ar-badge ar-badge-muted">Baholanmagan</span>
                                @endif
                            </td>

                            {{-- Amallar --}}
                            <td onclick="event.stopPropagation()">
                                <div style="display:flex; align-items:center; gap:4px; flex-wrap:wrap;">

                                    {{-- KO'RISH --}}
                                    <a href="{{ route('grades.index', $subject->id) }}" class="ar-btn"
                                        title="Baholarni ko'rish" style="color:#868400; padding:5px 8px;">
                                        <i class="bx bx-show"></i>
                                    </a>

                                    {{-- Vedmostga eksport qilish --}}
                                    <a href="{{ route('grades.vedomost.form', $subject->id) }}" class="ar-btn"
                                        target="_blank" title="Vedomostga eksport" style="padding:5px 8px;">
                                        <i class="bx bx-spreadsheet" style="color:#277eff;"></i>
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
                                                <div
                                                    style="position:relative; width:28px; height:28px; flex-shrink:0;">
                                                    <svg width="28" height="28"
                                                        style="transform:rotate(-90deg);">
                                                        <circle cx="14" cy="14" r="11" fill="none"
                                                            stroke="#e5e7eb" stroke-width="2.5" />
                                                        <circle class="circle-bar" cx="14" cy="14"
                                                            r="11" fill="none" stroke="#217346" stroke-width="2.5"
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

                                    {{-- HEMIS IMPORT (PDF) --}}
                                    <form action="{{ route('grades.hemis.import', $subject->id) }}" method="POST"
                                        enctype="multipart/form-data" style="display:inline-flex; align-items:center;"
                                        class="grade-import-form">
                                        @csrf
                                        <label class="ar-btn" title="Hemis qaydnomasidan import (PDF)"
                                            style="cursor:pointer; margin:0; padding:5px 8px;">
                                            <i class="bx bx-cloud-upload import-icon" style="color:#0f766e;"></i>
                                            <div class="row-progress"
                                                style="display:none; align-items:center; gap:4px;">
                                                <div
                                                    style="position:relative; width:28px; height:28px; flex-shrink:0;">
                                                    <svg width="28" height="28"
                                                        style="transform:rotate(-90deg);">
                                                        <circle cx="14" cy="14" r="11" fill="none"
                                                            stroke="#e5e7eb" stroke-width="2.5" />
                                                        <circle class="circle-bar" cx="14" cy="14"
                                                            r="11" fill="none" stroke="#0f766e" stroke-width="2.5"
                                                            stroke-dasharray="69.1" stroke-dashoffset="69.1"
                                                            style="transition:stroke-dashoffset 0.3s;" />
                                                    </svg>
                                                    <span class="circle-pct"
                                                        style="position:absolute;top:50%;left:50%;
                                                        transform:translate(-50%,-50%);
                                                        font-size:7px;font-weight:700;color:#0f766e;">0%</span>
                                                </div>
                                            </div>
                                            <input type="file" name="hemis_pdf" accept=".pdf"
                                                style="display:none;">
                                        </label>
                                    </form>


                                    {{-- NUSXALASH --}}
                                    <button type="button" class="ar-btn" style="padding:5px 8px;"
                                        title="Fanni nusxalash (yangi o'qituvchi bilan)"
                                        onclick="openDuplicateModal({{ $subject->id }}, {{ \Illuminate\Support\Js::from($subject->nomi) }})">
                                        <i class="bx bx-copy-alt" style="color:#f59e0b;"></i>
                                    </button>

                                    {{-- TAHRIRLASH --}}
                                    <a href="{{ route('subject.edit', $subject->id) }}" class="ar-btn"
                                        style="padding:5px 8px;" title="Tahrirlash">
                                        <i class="bx bx-edit"></i>
                                    </a>

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
                                    {{-- O'CHIRISH --}}
                                    <form action="{{ route('subject.destroy', $subject->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button class="ar-btn ar-btn-rej" style="padding:5px 8px;" title="O'chirish"
                                            onclick="return confirm('Ochirilsinmi?')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        - {{ $subject->students_count ?? 0 }}
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center; padding:2rem; color:#888;">
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

        {{-- ================= NUSXALASH MODALI ================= --}}
        <div id="duplicateModalOverlay" class="dup-modal-overlay">
            <div class="dup-modal">
                <div class="dup-modal-header">
                    <h4><i class="bx bx-copy-alt" style="color:#f59e0b;"></i> Fanni nusxalash</h4>
                    <button type="button" class="dup-modal-close" onclick="closeDuplicateModal()">&times;</button>
                </div>

                <p style="font-size:13px; color:#666; margin:0 0 16px;">
                    "<b id="dupSubjectName"></b>" fani <u>hamma parametri bilan bir xil</u> holda nusxalanadi,
                    faqat quyida tanlagan o'qituvchi biriktiriladi.
                </p>

                <form id="duplicateForm" method="POST">
                    @csrf

                    <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Yangi
                        o'qituvchi</label>
                    <div style="position:relative;">
                        <div style="position:relative;">
                            <i class="bx bx-search"
                                style="position:absolute; left:10px; top:50%;
                                transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                            <input type="text" id="dup_teacher_search" class="arizalar-search"
                                style="width:100%; padding-left:34px;" placeholder="ID yoki ismni yozing..."
                                autocomplete="off" required>
                        </div>

                        <div id="dup_teacher_results" class="search-dropdown">
                            @foreach ($teachers as $teacher)
                                <div class="search-item" data-id="{{ $teacher->id }}"
                                    data-name="{{ $teacher['To‘liq_ismi'] }}">
                                    <span
                                        style="background:#EEEDFE; color:#3C3489; padding:2px 8px;
                                        border-radius:6px; font-size:11px; font-weight:700; flex-shrink:0;">
                                        #{{ $teacher->id }}
                                    </span>
                                    <span style="font-size:13px; color:#333; font-weight:500;">
                                        {{ $teacher['To‘liq_ismi'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" name="teacher_id" id="dup_hidden_teacher_id" required>
                    </div>

                    <div style="display:flex; gap:8px; margin-top:18px;">
                        <button type="submit" id="dupSubmitBtn" class="ar-btn ar-btn-ok"
                            style="flex:1; justify-content:center;" disabled>
                            <i class="bx bx-copy-alt"></i> Nusxalash
                        </button>
                        <button type="button" class="ar-btn" onclick="closeDuplicateModal()">Bekor qilish</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <style>
        .dup-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(20, 20, 30, 0.4);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .dup-modal-overlay.show {
            display: flex;
        }

        .dup-modal {
            background: #fff;
            border-radius: 14px;
            padding: 22px;
            width: 380px;
            max-width: 92vw;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.22);
        }

        .dup-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .dup-modal-header h4 {
            margin: 0;
            font-size: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .dup-modal-close {
            background: none;
            border: none;
            font-size: 22px;
            line-height: 1;
            color: #999;
            cursor: pointer;
        }

        .dup-modal-close:hover {
            color: #333;
        }
    </style>

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
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(response) {
                        return response.json().then(function(data) {
                            return {
                                ok: response.ok,
                                data: data
                            };
                        }).catch(function() {
                            return {
                                ok: response.ok,
                                data: null
                            };
                        });
                    })
                    .then(function(result) {
                        clearInterval(fakeInterval);
                        circleBar.setAttribute('stroke-dashoffset', 0);
                        circlePct.textContent = '100%';

                        if (result.data && result.data.message) {
                            alert(result.data.message);
                        } else if (!result.ok) {
                            alert('Xatolik yuz berdi!');
                        }

                        setTimeout(function() {
                            location.reload();
                        }, 600);
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

    {{-- BEPUL SINXRONLASH --}}
    <script>
        (function() {
            var input = document.getElementById('bepulExcelInput');
            var form = document.getElementById('bepulSyncForm');
            var icon = document.getElementById('bepulSyncIcon');
            var label = document.getElementById('bepulSyncLabel');
            if (!input || !form) return;

            input.addEventListener('change', function() {
                if (!this.files[0]) return;

                // AVVAL FormData — keyin disabled
                var formData = new FormData(form);

                icon.className = 'bx bx-loader-alt bx-spin';
                label.textContent = 'Yuklanmoqda...';
                input.disabled = true;

                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(response) {
                        return response.json().then(function(data) {
                            return {
                                ok: response.ok,
                                data: data
                            };
                        }).catch(function() {
                            return {
                                ok: response.ok,
                                data: null
                            };
                        });
                    })
                    .then(function(result) {
                        if (result.data && result.data.message) {
                            alert(result.data.message);
                        } else if (!result.ok) {
                            alert((result.data && result.data.message) ? result.data.message :
                                'Xatolik yuz berdi!');
                        }
                        location.reload();
                    })
                    .catch(function() {
                        alert('Xatolik yuz berdi!');
                        icon.className = 'bx bx-sync';
                        label.textContent = 'Bepul sinxronlash';
                        input.disabled = false;
                    });
            });
        })();
    </script>

    <script>
        // Route helperi ":id" ni URL-encode qilib qo'yishi mumkinligi uchun,
        // shablonni SONLI placeholder (999999) bilan yasab, keyin JS orqali almashtiramiz.
        const duplicateUrlTemplate = "{{ route('subject.duplicate', 999999) }}";

        const dupOverlay = document.getElementById('duplicateModalOverlay');
        const dupForm = document.getElementById('duplicateForm');
        const dupSubjectName = document.getElementById('dupSubjectName');
        const dupTeacherSearch = document.getElementById('dup_teacher_search');
        const dupHiddenTeacherId = document.getElementById('dup_hidden_teacher_id');
        const dupTeacherResults = document.getElementById('dup_teacher_results');
        const dupSubmitBtn = document.getElementById('dupSubmitBtn');

        function openDuplicateModal(subjectId, subjectName) {
            dupForm.action = duplicateUrlTemplate.replace('999999', subjectId);
            dupSubjectName.textContent = subjectName;

            // Har safar oyna ochilganda tanlovni tozalaymiz
            dupTeacherSearch.value = '';
            dupHiddenTeacherId.value = '';
            dupTeacherSearch.style.borderColor = '';
            dupSubmitBtn.disabled = true;

            dupOverlay.classList.add('show');
            setTimeout(() => dupTeacherSearch.focus(), 50);
        }

        function closeDuplicateModal() {
            dupOverlay.classList.remove('show');
        }

        dupOverlay.addEventListener('click', function(e) {
            if (e.target === dupOverlay) closeDuplicateModal();
        });

        // "O'qituvchi" qidiruvli dropdown - create.blade.php dagi bilan bir xil mantiq
        (function() {
            const items = dupTeacherResults.querySelectorAll('.search-item');

            dupTeacherSearch.addEventListener('input', function() {
                const val = this.value.toLowerCase().trim();
                let found = 0;

                dupHiddenTeacherId.value = '';
                dupSubmitBtn.disabled = true;

                if (val.length > 0) {
                    dupTeacherResults.style.display = 'block';
                    items.forEach(item => {
                        const name = item.getAttribute('data-name').toLowerCase();
                        const id = item.getAttribute('data-id');
                        const show = name.includes(val) || id === val;
                        item.style.display = show ? 'flex' : 'none';
                        if (show) found++;
                    });
                    if (found === 0) dupTeacherResults.style.display = 'none';
                } else {
                    dupTeacherResults.style.display = 'none';
                }
            });

            dupTeacherSearch.addEventListener('focus', function() {
                if (this.value.trim().length > 0) {
                    dupTeacherResults.style.display = 'block';
                }
            });

            items.forEach(item => {
                item.addEventListener('click', function() {
                    dupTeacherSearch.value = this.dataset.name;
                    dupHiddenTeacherId.value = this.dataset.id;
                    dupTeacherResults.style.display = 'none';
                    dupTeacherSearch.style.borderColor = '#3C3489';
                    dupSubmitBtn.disabled = false;
                });
            });

            document.addEventListener('click', function(e) {
                if (!dupTeacherSearch.contains(e.target) && !dupTeacherResults.contains(e.target)) {
                    dupTeacherResults.style.display = 'none';
                }
            });
        })();

        dupForm.addEventListener('submit', function(e) {
            if (!dupHiddenTeacherId.value) {
                e.preventDefault();
                alert("Iltimos, avval yangi o'qituvchini tanlang.");
            }
        });
    </script>

    {{-- Yo'nalish (category) bo'yicha yozib-qidiradigan filter --}}
    <script>
        (function() {
            const catSearchForm = document.getElementById('filter_category_search').closest('form');
            const catSearch = document.getElementById('filter_category_search');
            const catResults = document.getElementById('filter_category_results');
            const catHidden = document.getElementById('filter_hidden_category_id');
            const catItems = catResults.querySelectorAll('.search-item');

            catSearch.addEventListener('input', function() {
                const val = this.value.toLowerCase().trim();
                let found = 0;

                catResults.style.display = 'block';
                catItems.forEach(item => {
                    if (item.dataset.id === '') return; // "Barcha yo'nalishlar" doim ko'rinadi
                    const name = item.getAttribute('data-name').toLowerCase();
                    const show = val.length === 0 || name.includes(val);
                    item.style.display = show ? 'flex' : 'none';
                    if (show) found++;
                });

                if (val.length > 0 && found === 0) {
                    catResults.style.display = 'none';
                }
            });

            catSearch.addEventListener('focus', function() {
                catResults.style.display = 'block';
            });

            catItems.forEach(item => {
                item.addEventListener('click', function() {
                    catSearch.value = this.dataset.id === '' ? '' : this.dataset.name;
                    catHidden.value = this.dataset.id;
                    catResults.style.display = 'none';
                    catSearchForm.submit();
                });
            });

            document.addEventListener('click', function(e) {
                if (!catSearch.contains(e.target) && !catResults.contains(e.target)) {
                    catResults.style.display = 'none';
                }
            });
        })();
    </script>

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

    {{-- BARCHA FANLARNI VEDOMOSTGA EXPORT QILISH (haqiqiy progress bilan) --}}
    <script>
        (function() {
            const exportAllBtn = document.getElementById('exportAllVedomostBtn');
            const progressWrap = document.getElementById('exportAllProgressWrap');
            const progressCircle = document.getElementById('exportAllProgressCircle');
            const progressPct = document.getElementById('exportAllProgressPct');
            const progressText = document.getElementById('exportAllProgressText');
            const circumference = 94.2;
            const csrfToken = '{{ csrf_token() }}';

            if (!exportAllBtn) return;

            function setProgress(done, total, label) {
                const pct = total > 0 ? Math.round((done / total) * 100) : 0;
                const offset = circumference - (pct / 100) * circumference;
                progressCircle.setAttribute('stroke-dashoffset', offset);
                progressPct.textContent = pct + '%';
                progressText.textContent = `${done} / ${total} fan tekshirildi` + (label ? ` \u2014 ${label}` : '');
            }

            exportAllBtn.addEventListener('click', async function() {
                const filtered =
                    {{ request('search') || request('category_id') || request('kurs') || request('semster') ? 'true' : 'false' }};
                const confirmMsg = filtered ?
                    "Joriy filterga mos fanlar bo'yicha vedomostlar bitta ZIP qilib eksport qilinadi. Davom etasizmi?" :
                    "Baholari mavjud BARCHA fanlar bo'yicha vedomostlar bitta ZIP qilib eksport qilinadi. Bu bir necha daqiqa vaqt olishi mumkin. Davom etasizmi?";

                if (!confirm(confirmMsg)) return;

                exportAllBtn.disabled = true;
                progressWrap.style.display = 'flex';
                setProgress(0, 1, "Fanlar ro'yxati olinmoqda...");

                try {
                    // 1-BOSQICH: filterga mos fanlar ro'yxatini va batch_id ni olamiz
                    const startParams = new URLSearchParams(window.location.search);
                    const startResp = await fetch("{{ route('vedomost.exportAll.start') }}?" + startParams
                        .toString(), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                    if (!startResp.ok) {
                        const err = await startResp.json().catch(() => ({}));
                        throw new Error(err.message || ('Server xatosi (kod: ' + startResp.status + ')'));
                    }

                    const startData = await startResp.json();
                    const batch = startData.batch;
                    const subjects = startData.subjects;
                    const total = startData.total;

                    let done = 0;
                    let exportedCount = 0;

                    setProgress(0, total, 'Boshlanmoqda...');

                    // 2-BOSQICH: har bir fan ketma-ket ishlanadi - shu orqali haqiqiy progress ko'rinadi
                    for (const subj of subjects) {
                        setProgress(done, total, subj.nomi);

                        const stepUrl = "{{ url('/vedomost/export-all') }}/" + batch + "/step/" + subj.id;
                        const stepResp = await fetch(stepUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!stepResp.ok) {
                            const err = await stepResp.json().catch(() => ({}));
                            throw new Error(err.message || (
                                `"${subj.nomi}" fanida xatolik (kod: ${stepResp.status})`));
                        }

                        const stepData = await stepResp.json();
                        if (stepData.exported) exportedCount++;

                        done++;
                        setProgress(done, total, subj.nomi);
                    }

                    // 3-BOSQICH: barcha fan-ziplarni bitta umumiy ZIP qilib yuklab olamiz
                    progressText.textContent = `${done} / ${total} fan tayyor. Umumiy ZIP yig'ilmoqda...`;

                    const finishResp = await fetch("{{ url('/vedomost/export-all') }}/" + batch +
                        "/finish", {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                    if (!finishResp.ok) {
                        const err = await finishResp.json().catch(() => ({}));
                        throw new Error(err.message || ('Server xatosi (kod: ' + finishResp.status + ')'));
                    }

                    const blob = await finishResp.blob();
                    progressCircle.setAttribute('stroke-dashoffset', 0);
                    progressPct.textContent = '100%';
                    progressText.textContent =
                        `Tayyor: ${exportedCount} ta fan eksport qilindi. Yuklab olinmoqda...`;

                    const blobUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = blobUrl;
                    a.download = 'Barcha_vedomostlar.zip';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(blobUrl);

                    setTimeout(function() {
                        progressWrap.style.display = 'none';
                    }, 2000);
                } catch (err) {
                    progressText.textContent = 'Xatolik yuz berdi!';
                    alert('Xatolik: ' + err.message);
                } finally {
                    exportAllBtn.disabled = false;
                }
            });
        })();
    </script>

</x-layouts.sidebar>
