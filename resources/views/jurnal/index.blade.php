<x-layouts.sidebar>
    <x-slot:title>Baholar jurnali</x-slot:title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="oz-wrap jr-page">

        <div class="jr-title">Baholar jurnali</div>

        <div class="jr-layout">

            {{-- ================= FILTR PANELI ================= --}}
            <aside class="jr-filter">
                <div class="jr-filter-head">
                    <h4>Filtr</h4>
                </div>

                <div class="jr-field">
                    <label for="bolim_id_search">Bo'lim</label>
                    <div class="jr-ss">
                        <div style="position:relative;">
                            <i class="bx bx-search"
                                style="position:absolute; left:10px; top:50%;
                                transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                            <input type="text" id="bolim_id_search" class="jr-select" style="padding-left:34px;"
                                placeholder="Bo'limni qidirish..." autocomplete="off">
                        </div>
                        <div id="bolim_id_results" class="search-dropdown">
                            @foreach ($bolimlar as $bolim)
                                <div class="search-item" data-id="{{ $bolim->id }}" data-name="{{ $bolim->nomi }}">
                                    {{ $bolim->nomi }}
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" id="bolim_id">
                    </div>
                </div>

                <div class="jr-field">
                    <label for="school_type_search">Maktab turi</label>
                    <div class="jr-ss">
                        <div style="position:relative;">
                            <i class="bx bx-search"
                                style="position:absolute; left:10px; top:50%;
                                transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                            <input type="text" id="school_type_search" class="jr-select" style="padding-left:34px;"
                                placeholder="Avval bo'limni tanlang" autocomplete="off" disabled>
                        </div>
                        <div id="school_type_results" class="search-dropdown">
                            <div class="search-item" data-id="free" data-name="Bepul maktab">Bepul maktab</div>
                            <div class="search-item" data-id="mini" data-name="Mini Semestr">Mini Semestr</div>
                        </div>
                        <input type="hidden" id="school_type">
                    </div>
                </div>

                <div class="jr-field">
                    <label for="subject_id_search">Fan</label>
                    <div class="jr-ss">
                        <div style="position:relative;">
                            <i class="bx bx-search"
                                style="position:absolute; left:10px; top:50%;
                                transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                            <input type="text" id="subject_id_search" class="jr-select" style="padding-left:34px;"
                                placeholder="Avval maktab turini tanlang" autocomplete="off" disabled>
                        </div>
                        <div id="subject_id_results" class="search-dropdown"></div>
                        <input type="hidden" id="subject_id">
                    </div>
                    <div id="subjectTeacherInfo" style="display:none; margin-top:6px; font-size:12px; color:#555;">
                        <i class="bx bx-user" style="color:#3C3489;"></i>
                        <span id="subjectTeacherName"></span>
                    </div>
                </div>

                <div class="jr-field">
                    <label for="guruh_filter_search">Guruh</label>
                    <div class="jr-ss">
                        <div style="position:relative;">
                            <i class="bx bx-search"
                                style="position:absolute; left:10px; top:50%;
                                transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                            <input type="text" id="guruh_filter_search" class="jr-select" style="padding-left:34px;"
                                placeholder="Avval fanni tanlang" autocomplete="off" disabled>
                        </div>
                        <div id="guruh_filter_results" class="search-dropdown"></div>
                        <input type="hidden" id="guruh_filter">
                    </div>
                </div>

                <hr class="jr-hr">
                <div id="debugBox" style="font-size:11px;color:#c0392b;white-space:pre-wrap;"></div>
            </aside>

            {{-- ================= JADVAL ================= --}}
            <div class="jr-main-card">
                <div class="jr-topbar">
                    <h2 id="subjectTitle">Fan tanlanmagan</h2>
                    <div class="jr-top-actions">
                        <button id="exportBtn" class="jr-export-btn" disabled title="Avval bo'lim, maktab turi va fanni tanlang">
                            <i class="fas fa-file-excel"></i> <span id="exportBtnLabel">Excel'ga yuklash</span>
                        </button>
                        <div class="jr-page-select">
                            Ko'rsatish:
                            <select id="pageSize">
                                <option value="25" selected>25</option>
                                <option value="50">50</option>
                                <option value="75">75</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="jr-legend">
                    <span><i class="dotbox" style="background:#e9f8ee;border:1px solid #1f9d55;"></i> A'lo
                        (&ge;4.5)</span>
                    <span><i class="dotbox" style="background:#fdf3df;border:1px solid #b7791f;"></i> Qoniqarli
                        (3&ndash;4.49)</span>
                    <span><i class="dotbox" style="background:#fdecea;border:1px solid #c0392b;"></i> Past
                        (&lt;3)</span>
                    <span><i class="dotbox" style="background:#f6f6f8;border:1px solid #ccc;"></i> Baholanmagan</span>
                    <span><i class="grade-flag" style="position:static;">!</i> Qo'lda o'zgartirilgan (bosing - tarixni
                        ko'rish)</span>
                </div>

                <div class="jr-table-card">
                    <div class="jr-table-scroll">
                        <table class="jr-table">
                            <thead>
                                <tr id="theadRow">
                                    <th class="jr-freeze1">Talaba</th>
                                    <th class="jr-freeze2">Guruh</th>
                                    <th>Yakuniy baho</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                <tr>
                                    <td colspan="3" style="text-align:center;color:#999;padding:24px;">
                                        Bo'lim, maktab turi va fanni tanlang
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="jr-footer">
                        <span id="rangeInfo"></span>
                        <div class="jr-pager" id="pager"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="jr-pop-modern" id="pop">

        <div class="jr-pop-header">

            <div class="jr-pop-icon">
                <i class="fas fa-pen"></i>
            </div>

            <div class="jr-pop-title">
                <h5 id="popTitle">Bahoni tahrirlash</h5>
                <span id="popSubTitle">Talaba bahosi</span>
            </div>

        </div>


        <div class="pop-body">

            <label>Bahoni kiriting</label>

            <div class="input-row">

                <input id="manualVal" class="grade-input" type="number" step="0.01" placeholder="0.00">

                <div class="max-box">

                    <i class="fas fa-star"></i>

                    <small>Maksimal</small>

                    <b id="maxGrade">40</b>

                </div>

            </div>


            <div class="quick-values">

                <button data-val="5">5</button>

                <button data-val="10">10</button>

                <button data-val="20">20</button>

                <button data-val="40">40</button>

            </div>


            <div class="pop-info">

                <i class="fas fa-lightbulb"></i>

                <span>
                    Bo'sh qoldirilsa avtomatik hisoblash ishlaydi.
                </span>

            </div>

        </div>


        <div class="pop-footer">

            <button class="btn-save" id="applyVal">

                <i class="fas fa-check"></i>

                Saqlash

            </button>

        </div>

    </div>

    <div class="jr-pop history-card" id="historyPop">

        <div class="history-header">
            <div>
                <h5>🕘 O'zgartirishlar tarixi</h5>
                <small>Kim, qachon va qanday o'zgartirgan</small>
            </div>

            <button class="close-history" onclick="document.getElementById('historyPop').classList.remove('show')">
                ✕
            </button>
        </div>

        <div id="historyBody" class="history-body"></div>

    </div>

    <style>
        .jr-select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #d7dbe0;
            border-radius: 8px;
            background: #fff;
            font-size: 14px;
        }

        .jr-select:disabled {
            background: #f3f4f6;
            color: #9aa0a6;
            cursor: not-allowed;
        }

        .jr-field {
            margin-bottom: 14px;
        }

        .jr-field label {
            display: block;
            margin-bottom: 4px;
            font-size: 13px;
            color: #555;
        }

        .jr-ss {
            position: relative;
        }

        .jr-ss .search-dropdown {
            display: none;
            position: absolute;
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            max-height: 220px;
            overflow-y: auto;
            width: 100%;
            margin-top: 4px;
        }

        .jr-ss .search-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            cursor: pointer;
            border-bottom: 1px solid #f5f5f5;
            transition: background 0.15s;
            font-size: 13px;
            color: #333;
        }

        .jr-ss .search-item:hover {
            background: #EEEDFE;
        }

        .jr-topic-th {
            font-size: 11px;
            text-align: center;
            min-width: 70px;
        }

        .jr-topic-th .tur-tag {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 20px;
            margin-bottom: 3px;
        }

        .jr-summary-th {
            background: #f7f7fb;
        }

        .jr-cell-sm {
            min-width: 60px;
        }

        .jr-cell {
            position: relative;
        }

        .grade-flag {
            position: absolute;
            top: -6px;
            right: -6px;
            display: inline-block;
            width: 15px;
            height: 15px;
            line-height: 15px;
            text-align: center;
            border-radius: 50%;
            background: #2563eb;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            cursor: pointer;
            z-index: 2;
        }

        .hist-row {
            padding: 8px 10px;
            font-size: 12px;
        }

        .hist-date {
            color: #888;
            margin-left: 6px;
        }

        .hist-ip {
            color: #888;
            margin-top: 2px;
        }

        .hist-sep {
            margin: 0;
            border: none;
            border-top: 1px solid #eee;
        }

        .jr-export-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border: 1px solid #1f9d55;
            background: #1f9d55;
            color: #fff;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s ease;
        }

        .jr-export-btn:hover:not(:disabled) {
            background: #17803f;
        }

        .jr-export-btn:disabled {
            background: #f0f0f0;
            border-color: #d7dbe0;
            color: #999;
            cursor: not-allowed;
        }

        /* =============================================
           BAHO TAHRIRLASH POPUP — accent dizayn
           ============================================= */
        .jr-pop-modern {
            position: fixed;
            display: none;
            z-index: 1200;
            width: 260px;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(34, 31, 61, 0.18);
            border: 1px solid #edecf5;
            font-family: 'Poppins', sans-serif;
        }

        .jr-pop-modern.show {
            display: block;
        }

        .jr-pop-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            background: linear-gradient(135deg, #7C6CF5, #5B4FE0);
        }

        .jr-pop-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 15px;
            flex-shrink: 0;
        }

        .jr-pop-title h5 {
            margin: 0;
            font-size: 13.5px;
            font-weight: 700;
            color: #fff;
        }

        .jr-pop-title span {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.75);
        }

        .pop-body {
            padding: 16px 18px 6px;
        }

        .pop-body>label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #59567a;
            margin-bottom: 8px;
        }

        .input-row {
            display: flex;
            align-items: stretch;
            gap: 8px;
            margin-bottom: 12px;
        }

        .grade-input {
            flex: 1;
            min-width: 0;
            border: 1.5px solid #ece9f7;
            background: #fbfaff;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 15px;
            font-weight: 600;
            color: #221f3d;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .grade-input:focus {
            outline: none;
            border-color: #7C6CF5;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(124, 108, 245, 0.12);
        }

        .max-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1px;
            min-width: 62px;
            background: #f6f5ff;
            border-radius: 10px;
            padding: 4px 8px;
            border: 1px solid #edecf5;
        }

        .max-box i {
            font-size: 11px;
            color: #f5a623;
        }

        .max-box small {
            font-size: 9px;
            color: #9490b8;
        }

        .max-box b {
            font-size: 13px;
            color: #5B4FE0;
        }

        .quick-values {
            display: flex;
            gap: 6px;
            margin-bottom: 12px;
        }

        .quick-values button {
            flex: 1;
            border: 1px solid #edecf5;
            background: #faf9ff;
            color: #5B4FE0;
            font-size: 12.5px;
            font-weight: 700;
            padding: 7px 0;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }

        .quick-values button:hover {
            background: #7C6CF5;
            border-color: #7C6CF5;
            color: #fff;
        }

        .pop-info {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            background: #f6f5ff;
            border-radius: 8px;
            padding: 8px 10px;
            margin-bottom: 14px;
        }

        .pop-info i {
            font-size: 12px;
            color: #f5a623;
            margin-top: 1px;
        }

        .pop-info span {
            font-size: 11px;
            color: #8b87a8;
            line-height: 1.4;
        }

        .pop-footer {
            padding: 0 18px 18px;
        }

        .btn-save {
            width: 100%;
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, #7C6CF5, #5B4FE0);
            color: #fff;
            font-size: 13.5px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            padding: 11px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 18px rgba(124, 108, 245, 0.3);
            transition: transform 0.15s ease, filter 0.15s ease;
        }

        .btn-save:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
        }

        .btn-save:active {
            transform: translateY(0);
        }

        /* =============================================
           TARIX POPUP
           ============================================= */
        .history-card {
            position: fixed;
            display: none;
            z-index: 1200;
            width: 300px;
            max-height: 320px;
            overflow-y: auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 18px 40px rgba(34, 31, 61, 0.18);
            border: 1px solid #edecf5;
            font-family: 'Poppins', sans-serif;
        }

        .history-card.show {
            display: block;
        }

        .history-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            padding: 14px 16px;
            background: #faf9ff;
            border-bottom: 1px solid #edecf5;
            position: sticky;
            top: 0;
        }

        .history-header h5 {
            margin: 0 0 2px;
            font-size: 13px;
            color: #221f3d;
        }

        .history-header small {
            font-size: 11px;
            color: #9490b8;
        }

        .close-history {
            border: none;
            background: #f0eefb;
            color: #5B4FE0;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            font-size: 11px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .history-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f4f3fa;
            font-size: 12px;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-top {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .history-top .avatar {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            background: linear-gradient(135deg, #7C6CF5, #5B4FE0);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 11px;
            flex-shrink: 0;
        }

        .history-info strong {
            display: block;
            font-size: 12px;
            color: #221f3d;
        }

        .history-info span {
            font-size: 10.5px;
            color: #9490b8;
        }

        .history-grade {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .history-grade .old {
            color: #b0acc9;
            text-decoration: line-through;
        }

        .history-grade .new {
            color: #5B4FE0;
        }

        .history-grade i {
            color: #b0acc9;
            font-size: 13px;
        }

        .history-ip {
            font-size: 10.5px;
            color: #b0acc9;
        }
    </style>

    <script>
        (function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const ROUTES = {
                subjects: "{{ route('jurnal.subjects') }}",
                topics: "{{ route('jurnal.topics') }}",
                students: "{{ route('jurnal.students') }}",
                gradeUpdate: "{{ route('jurnal.grade.update') }}",
                topicGradeUpdate: "{{ route('jurnal.topic.grade.update') }}",
                gradeHistory: "{{ route('jurnal.grade.history') }}",
                export: "{{ route('jurnal.export') }}",
            };

            const TUR_STYLE = {
                mavzu: {
                    bg: '#EEEDFE',
                    txt: '#3C3489',
                    label: 'Mavzu'
                },
                oraliq: {
                    bg: '#fff3cd',
                    txt: '#856404',
                    label: 'Oraliq'
                },
                yakuniy: {
                    bg: '#d1fae5',
                    txt: '#065f46',
                    label: 'Yakuniy'
                },
            };

            function debug(msg) {
                const box = document.getElementById('debugBox');
                if (box) box.textContent = msg;
                console.error('[Jurnal]', msg);
            }

            const state = {
                bolimId: null,
                type: null,
                subjectId: null,
                topics: [],
                students: [],
                groupFilter: null,
                page: 1,
                pageSize: 25,
            };

            // ================= QIDIRUVLI SELECT (search-select) =================
            // Har bir filtr uchun umumiy funksiya: matn input + natijalar ro'yxati +
            // yashirin input (haqiqiy qiymat) - subject.create sahifasidagi pattern bilan bir xil.
            function makeSearchSelect(searchId, resultsId, hiddenId) {
                const searchInput = document.getElementById(searchId);
                const resultsBox = document.getElementById(resultsId);
                const hiddenInput = document.getElementById(hiddenId);
                let onSelectCb = null;

                function bindItems() {
                    resultsBox.querySelectorAll('.search-item').forEach(item => {
                        item.onclick = function() {
                            searchInput.value = this.dataset.name;
                            hiddenInput.value = this.dataset.id;
                            resultsBox.style.display = 'none';
                            searchInput.style.borderColor = '#3C3489';
                            if (onSelectCb) onSelectCb(this.dataset.id, this.dataset.name);
                        };
                    });
                }

                searchInput.addEventListener('input', function() {
                    const val = this.value.toLowerCase().trim();
                    const items = resultsBox.querySelectorAll('.search-item');
                    let found = 0;

                    if (val.length > 0) {
                        resultsBox.style.display = 'block';
                        items.forEach(item => {
                            const name = item.getAttribute('data-name').toLowerCase();
                            const id = String(item.getAttribute('data-id')).toLowerCase();
                            const show = name.includes(val) || id === val;
                            item.style.display = show ? 'flex' : 'none';
                            if (show) found++;
                        });
                        if (found === 0) resultsBox.style.display = 'none';
                    } else {
                        resultsBox.style.display = 'none';
                    }

                    // Input butunlay tozalansa - tanlov ham bekor qilinadi (kaskad reset uchun)
                    if (val === '' && hiddenInput.value !== '') {
                        hiddenInput.value = '';
                        searchInput.style.borderColor = '';
                        if (onSelectCb) onSelectCb(null, '');
                    }
                });

                searchInput.addEventListener('focus', function() {
                    if (searchInput.disabled) return;
                    resultsBox.querySelectorAll('.search-item').forEach(i => i.style.display = 'flex');
                    if (resultsBox.querySelectorAll('.search-item').length) {
                        resultsBox.style.display = 'block';
                    }
                });

                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                        resultsBox.style.display = 'none';
                    }
                });

                bindItems();

                return {
                    // Ro'yxatni dinamik ma'lumot bilan qayta chizadi: [{id, name}, ...]
                    setItems(items) {
                        resultsBox.innerHTML = items.map(it =>
                            `<div class="search-item" data-id="${it.id}" data-name="${String(it.name).replace(/"/g, '&quot;')}">${it.name}</div>`
                        ).join('');
                        bindItems();
                    },
                    onSelect(cb) {
                        onSelectCb = cb;
                    },
                    reset(placeholder) {
                        searchInput.value = '';
                        hiddenInput.value = '';
                        if (placeholder !== undefined) searchInput.placeholder = placeholder;
                        searchInput.style.borderColor = '';
                        resultsBox.style.display = 'none';
                    },
                    disable() {
                        searchInput.disabled = true;
                    },
                    enable() {
                        searchInput.disabled = false;
                    },
                    getValue() {
                        return hiddenInput.value || null;
                    }
                };
            }

            const bolimSelect = makeSearchSelect('bolim_id_search', 'bolim_id_results', 'bolim_id');
            const typeSelect = makeSearchSelect('school_type_search', 'school_type_results', 'school_type');
            const subjectSelect = makeSearchSelect('subject_id_search', 'subject_id_results', 'subject_id');
            const groupFilterSelect = makeSearchSelect('guruh_filter_search', 'guruh_filter_results', 'guruh_filter');

            // Fan ro'yxatidagi qo'shimcha ma'lumotlarni (jumladan o'qituvchi nomini) saqlab qo'yamiz,
            // chunki makeSearchSelect faqat id/nomi bilan ishlaydi.
            let subjectsData = [];
            const subjectTeacherInfo = document.getElementById('subjectTeacherInfo');
            const subjectTeacherName = document.getElementById('subjectTeacherName');

            function updateSubjectTeacherInfo(subjectId) {
                const found = subjectsData.find(s => String(s.id) === String(subjectId));
                if (found && found.teacher_name) {
                    subjectTeacherName.textContent = "O'qituvchi: " + found.teacher_name;
                    subjectTeacherInfo.style.display = 'block';
                } else {
                    subjectTeacherInfo.style.display = 'none';
                }
            }
            const subjectTitle = document.getElementById('subjectTitle');
            const theadRow = document.getElementById('theadRow');
            const tbody = document.getElementById('tbody');
            const rangeInfo = document.getElementById('rangeInfo');
            const pager = document.getElementById('pager');
            const pageSizeSelect = document.getElementById('pageSize');
            const exportBtn = document.getElementById('exportBtn');

            const exportBtnLabel = document.getElementById('exportBtnLabel');

            function updateExportBtnState() {
                exportBtn.disabled = !(state.bolimId && state.type && state.subjectId);
                if (state.groupFilter) {
                    exportBtnLabel.textContent = `"${state.groupFilter}" guruhi uchun Excel`;
                } else {
                    exportBtnLabel.textContent = "Barcha guruhlar - Excel (ZIP)";
                }
            }

            exportBtn.addEventListener('click', function() {
                if (exportBtn.disabled) return;
                // Jadvalda "Guruh filtri" tanlangan bo'lsa - faqat o'sha guruh uchun bitta
                // Excel yuklanadi; tanlanmagan bo'lsa - fandagi barcha guruhlar ZIP qilib yuklanadi.
                const guruhParam = state.groupFilter ? `&guruh=${encodeURIComponent(state.groupFilter)}` : '';
                const url =
                    `${ROUTES.export}?bolim_id=${state.bolimId}&type=${state.type}&subject_id=${state.subjectId}${guruhParam}`;
                window.location.href = url;
            });

            // ================= 1) BO'LIM TANLASH =================
            bolimSelect.onSelect(function(id) {
                state.bolimId = id || null;
                resetAfterBolim();

                if (!state.bolimId) {
                    typeSelect.disable();
                    typeSelect.reset("Avval bo'limni tanlang");
                    subjectSelect.disable();
                    subjectSelect.reset('Avval maktab turini tanlang');
                    return;
                }
                typeSelect.enable();
                typeSelect.reset('Maktab turini tanlang yoki qidiring...');
                subjectSelect.disable();
                subjectSelect.reset('Avval maktab turini tanlang');
            });

            // ================= 2) MAKTAB TURI TANLASH =================
            typeSelect.onSelect(function(id) {
                state.type = id || null;
                resetAfterType();

                if (!state.bolimId || !state.type) {
                    subjectSelect.disable();
                    subjectSelect.reset('Avval maktab turini tanlang');
                    return;
                }

                subjectSelect.disable();
                subjectSelect.setItems([]);
                subjectSelect.reset('Yuklanmoqda...');

                fetch(`${ROUTES.subjects}?bolim_id=${state.bolimId}&type=${state.type}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error(`Server xatosi: ${res.status}`);
                        return res.json();
                    })
                    .then(data => {
                        subjectsData = data;
                        subjectTeacherInfo.style.display = 'none';

                        if (!data.length) {
                            subjectSelect.setItems([]);
                            subjectSelect.reset("Bu bo'limda fan topilmadi");
                            subjectSelect.disable();
                            return;
                        }
                        subjectSelect.setItems(data.map(item => ({
                            id: item.id,
                            name: item.nomi
                        })));
                        subjectSelect.reset('Fan qidirish...');
                        subjectSelect.enable();
                    })
                    .catch(err => debug('Fanlarni yuklashda xato: ' + err.message));
            });

            // ================= 3) FAN TANLASH =================
            subjectSelect.onSelect(function(id, name) {
                state.subjectId = id || null;
                state.page = 1;
                state.students = [];
                state.topics = [];
                resetGroupFilter();

                if (!state.subjectId) {
                    subjectTitle.textContent = 'Fan tanlanmagan';
                    subjectTeacherInfo.style.display = 'none';
                    renderTableHead();
                    renderStudents();
                    updateExportBtnState();
                    return;
                }

                subjectTitle.textContent = name;
                updateSubjectTeacherInfo(state.subjectId);
                updateExportBtnState();
                tbody.innerHTML =
                    '<tr><td colspan="10" style="text-align:center;color:#999;padding:24px;">Yuklanmoqda...</td></tr>';

                if (state.type === 'mini') {
                    fetch(`${ROUTES.topics}?bolim_id=${state.bolimId}&subject_id=${state.subjectId}`)
                        .then(res => res.json())
                        .then(topics => {
                            state.topics = topics;
                            renderTableHead();
                            loadStudents();
                        })
                        .catch(err => debug('Mavzularni yuklashda xato: ' + err.message));
                } else {
                    renderTableHead();
                    loadStudents();
                }
            });

            async function loadStudents() {
                const res = await fetch(
                    `${ROUTES.students}?bolim_id=${state.bolimId}&type=${state.type}&subject_id=${state.subjectId}`
                );

                const data = await res.json();

                state.students = data;
                populateGroupFilter();
                renderStudents();
            }

            function resetAfterBolim() {
                state.type = null;
                state.subjectId = null;
                state.students = [];
                state.topics = [];
                subjectTitle.textContent = 'Fan tanlanmagan';
                subjectTeacherInfo.style.display = 'none';
                resetGroupFilter();
                renderTableHead();
                renderStudents();
                updateExportBtnState();
            }

            function resetAfterType() {
                state.subjectId = null;
                state.students = [];
                state.topics = [];
                subjectTitle.textContent = 'Fan tanlanmagan';
                subjectTeacherInfo.style.display = 'none';
                resetGroupFilter();
                renderTableHead();
                renderStudents();
                updateExportBtnState();
            }

            // ================= GURUH FILTRI =================
            function resetGroupFilter() {
                state.groupFilter = null;
                groupFilterSelect.setItems([]);
                groupFilterSelect.reset('Avval fanni tanlang');
                groupFilterSelect.disable();
            }

            function populateGroupFilter() {
                const groups = Array.from(new Set(state.students.map(s => s.group).filter(Boolean))).sort();
                groupFilterSelect.setItems(groups.map(g => ({
                    id: g,
                    name: g
                })));
                groupFilterSelect.reset(groups.length ? "Barcha guruhlar (qidirish uchun yozing)" :
                    'Guruh topilmadi');
                groupFilterSelect.disable();
                if (groups.length) groupFilterSelect.enable();
                state.groupFilter = null;
            }

            groupFilterSelect.onSelect(function(id) {
                state.groupFilter = id || null;
                state.page = 1;
                updateExportBtnState();
                renderStudents();
            });

            function getFilteredStudents() {
                if (!state.groupFilter) return state.students;
                return state.students.filter(s => s.group === state.groupFilter);
            }

            // ================= JADVAL SARLAVHASI (dinamik) =================
            function renderTableHead() {
                if (state.type === 'mini') {
                    let html = `<th class="jr-freeze1">Talaba</th><th class="jr-freeze2">Guruh</th>`;
                    state.topics.forEach(t => {
                        const style = TUR_STYLE[t.tur] || {
                            bg: '#f0f0f0',
                            txt: '#444',
                            label: t.tur
                        };
                        html += `<th class="jr-topic-th">
                            <span class="tur-tag" style="background:${style.bg};color:${style.txt};">${style.label}</span><br>
                            ${t.nomi}
                        </th>`;
                    });
                    html += `
                        <th class="jr-summary-th">Joriy</th>
                        <th class="jr-summary-th">Oraliq</th>
                        <th class="jr-summary-th">Joriy+Oraliq</th>
                        <th class="jr-summary-th">Yakuniy</th>
                        <th class="jr-summary-th">Umumiy</th>
                    `;
                    theadRow.innerHTML = html;
                } else {
                    theadRow.innerHTML =
                        `<th class="jr-freeze1">Talaba</th><th class="jr-freeze2">Guruh</th><th>Yakuniy baho</th>`;
                }
            }

            // ================= JADVAL TANASI =================
            function gradeClass(v) {
                if (v === null || v === undefined || v === '') return 'empty';
                v = parseFloat(v);
                if (v >= 10) return 'good';
                if (v >= 5) return 'warn';
                return 'low';
            }

            function initials(n) {
                const p = (n || '—').trim().split(' ');
                return ((p[0]?.[0] || '') + (p[1]?.[0] || '')).toUpperCase();
            }

            function cellHtml(value, dataAttrs, edited, editable = true) {
                const cls = gradeClass(value);
                const disp = (value === null || value === undefined || value === '') ? '—' : parseFloat(value).toFixed(
                    2);

                const flag = edited ?
                    `<i class="grade-flag" title="O'zgartirish tarixi" onclick="window.__openHistory(this,event)">!</i>` :
                    '';

                const click = editable ?
                    `onclick="window.__openPop(this,event)"` :
                    '';

                return `
        <td class="jr-cell-sm">
            <div class="jr-cell ${cls}" ${dataAttrs} ${click}>
                <span>${disp}</span>
                ${flag}
                ${editable ? "<i class='bx bx-chevron-down'></i>" : ""}
            </div>
        </td>
    `;
            }

            // joriy_oraliq / umumiy ni mahalliy holatda avtomatik qayta hisoblaydi
            // (faqat qo'lda o'zgartirilmagan bo'lsa)
            function recomputeDerived(s) {
                if (!s.joriy_oraliq_manual) {
                    s.joriy_oraliq = (s.joriy_baho !== null && s.joriy_baho !== undefined &&
                            s.oraliq_baho !== null && s.oraliq_baho !== undefined) ?
                        (parseFloat(s.joriy_baho) + parseFloat(s.oraliq_baho)) : null;
                }
                if (!s.umumiy_manual) {
                    s.umumiy = (s.joriy_oraliq !== null && s.joriy_oraliq !== undefined &&
                            s.yakuniy_baho !== null && s.yakuniy_baho !== undefined) ?
                        (parseFloat(s.joriy_oraliq) + parseFloat(s.yakuniy_baho)) : null;
                }
            }

            function renderStudents() {
                const list = getFilteredStudents();
                const colCount = state.type === 'mini' ? (2 + state.topics.length + 5) : 3;

                if (!list.length) {
                    tbody.innerHTML =
                        `<tr><td colspan="${colCount}" style="text-align:center;color:#999;padding:24px;">${state.students.length ? 'Bu guruhda talaba topilmadi' : "Bo'lim, maktab turi va fanni tanlang"}</td></tr>`;
                    rangeInfo.textContent = '';
                    pager.innerHTML = '';
                    return;
                }

                const totalPages = Math.max(1, Math.ceil(list.length / state.pageSize));
                state.page = Math.min(state.page, totalPages);
                const start = (state.page - 1) * state.pageSize;
                const items = list.slice(start, start + state.pageSize);

                tbody.innerHTML = items.map(s => {
                    const head = `
                        <td class="jr-freeze1"><div class="jr-student">
                            <div class="jr-avatar">${initials(s.name)}</div>
                            <div><div class="name">${s.name}</div><div class="id">ID ${s.user_id}, <br>Talaba ID : ${s.talaba_id}</div></div>
                        </div></td>
                        <td class="jr-freeze2"><span class="jr-group-tag">${s.group}</span></td>`;

                    if (state.type === 'free') {
                        const cell = cellHtml(s.yakuniy_baho,
                            `data-kind="free" data-record="${s.record_id}"`,
                            !!s.yakuniy_baho_edited);
                        return `<tr>${head}${cell}</tr>`;
                    }

                    let row = head;

                    state.topics.forEach(t => {
                        const val = s.topics ? s.topics[t.id] : null;
                        const isEdited = (s.edited_topics || []).map(String).includes(String(t.id));

                        row += cellHtml(
                            val,
                            `data-kind="topic" data-record="${s.record_id}" data-user="${s.user_id}" data-mavzu="${t.id}"`,
                            isEdited,
                            true
                        );
                    });

                    ['joriy_baho', 'oraliq_baho', 'joriy_oraliq', 'yakuniy_baho', 'umumiy'].forEach(field => {

                        let edited;

                        if (field === 'joriy_oraliq')
                            edited = !!s.joriy_oraliq_manual;
                        else if (field === 'umumiy')
                            edited = !!s.umumiy_manual;
                        else
                            edited = !!s[field + '_edited'];

                        // Faqat shu uchta ustun edit qilinadi
                        const editable = ['joriy_baho', 'oraliq_baho', 'yakuniy_baho'].includes(field);

                        row += cellHtml(
                            s[field],
                            `data-kind="summary" data-record="${s.record_id}" data-field="${field}"`,
                            edited,
                            editable
                        );
                    });
                    return `<tr>${row}</tr>`;
                }).join('');

                rangeInfo.textContent =
                    `${start + 1}–${Math.min(start + state.pageSize, list.length)} / ${list.length} talaba`;
                renderPager(totalPages);
            }

            function renderPager(totalPages) {
                pager.innerHTML = `<button ${state.page <= 1 ? 'disabled' : ''} id="pPrev">‹</button>
                    <span class="num">${state.page} / ${totalPages}</span>
                    <button ${state.page >= totalPages ? 'disabled' : ''} id="pNext">›</button>`;
                document.getElementById('pPrev')?.addEventListener('click', () => {
                    state.page--;
                    renderStudents();
                });
                document.getElementById('pNext')?.addEventListener('click', () => {
                    state.page++;
                    renderStudents();
                });
            }

            pageSizeSelect.addEventListener('change', function() {
                state.pageSize = parseInt(this.value);
                state.page = 1;
                renderStudents();
            });

            const pop = document.getElementById('pop');
            const manualInput = document.getElementById('manualVal');
            let activeCellData = null;

            // Tez tanlash tugmalari
            document
                .querySelectorAll(".quick-values button")
                .forEach(btn => {

                    btn.onclick = function() {

                        manualInput.value = this.dataset.val;

                        manualInput.focus();

                    };

                });

            window.__openPop = function(el, e) {
                e.stopPropagation();
                historyPop.classList.remove('show');
                activeCellData = {
                    ...el.dataset
                };

                const currentValue = el.querySelector("span").innerText.trim();
                manualInput.value = currentValue === "—" ? "" : currentValue;

                const td = el.closest("td");
                const th = document.querySelectorAll("#theadRow th")[td.cellIndex];
                document.getElementById("popTitle").textContent = th.innerText.trim();
                document.getElementById("popSubTitle").textContent = "Bahoni kiriting";

                // AVVAL ko'rsatamiz — shundan keyingina o'lchov to'g'ri ishlaydi
                pop.classList.add("show");

                const r = el.getBoundingClientRect();
                let left = Math.min(r.left, window.innerWidth - pop.offsetWidth - 20);
                left = Math.max(10, left); // chap chetdan ham chiqib ketmasin
                pop.style.top = (r.bottom + 8) + "px";
                pop.style.left = left + "px";

                setTimeout(() => {
                    manualInput.focus();
                    manualInput.select();
                }, 80);
            }

            function closePop() {
                pop.classList.remove('show');
                activeCellData = null;
            }

            function submitGrade(value) {
                if (!activeCellData) return;

                if (activeCellData.kind === 'topic') {
                    fetch(ROUTES.topicGradeUpdate, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                user_id: activeCellData.user,
                                mavzu_id: activeCellData.mavzu,
                                baho: value,
                            }),
                        })
                        .then(res => {
                            if (!res.ok) throw new Error(`Xato: ${res.status}`);
                            return res.json();
                        })
                        .then(() => {
                            closePop();
                            loadStudents();
                        })
                        .catch(err => debug(err.message));
                    return;
                }

                const field = activeCellData.kind === 'free' ? 'yakuniy_baho' : activeCellData.field;

                fetch(ROUTES.gradeUpdate, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            type: state.type,
                            record_id: activeCellData.record,
                            field: field,
                            value: value,
                        }),
                    })
                    .then(res => {
                        if (!res.ok) throw new Error(`Xato: ${res.status}`);
                        return res.json();
                    })
                    .then(async () => {
                        await loadStudents();
                        closePop();
                    })
                    .catch(err => debug(err.message));
            }

            // "Saqlash" — agar maydon bo'sh qoldirilsa, avtomatik hisoblash uchun
            // qiymat null yuboriladi (avvalgi "Tozalash" tugmasi vazifasini bajaradi)
            document.getElementById('applyVal').addEventListener('click', function() {
                const raw = manualInput.value.trim();

                if (raw === '') {
                    submitGrade(null);
                    return;
                }

                const v = parseFloat(raw.replace(',', '.'));
                if (isNaN(v) || v < 0 || v > 100) return;
                submitGrade(v);
            });

            // ================= TARIX POPUP =================
            const historyPop = document.getElementById('historyPop');
            const historyBody = document.getElementById('historyBody');

            window.__openHistory = function(el, e) {
                e.stopPropagation();
                pop.classList.remove('show');

                const cellEl = el.closest('.jr-cell');
                const params = new URLSearchParams(cellEl.dataset);

                const r = el.getBoundingClientRect();
                historyPop.style.top = (r.bottom + 6) + 'px';
                historyPop.style.left = Math.min(r.left, window.innerWidth - 340) + 'px';
                historyBody.innerHTML = '<div style="padding:10px;color:#999;">Yuklanmoqda...</div>';
                historyPop.classList.add('show');

                fetch(`${ROUTES.gradeHistory}?${params.toString()}`)
                    .then(res => res.json())
                    .then(rows => {
                        if (!rows.length) {
                            historyBody.innerHTML =
                                '<div style="padding:10px;color:#999;">Tarix topilmadi</div>';
                            return;
                        }
                        historyBody.innerHTML = rows.map(h => `

<div class="history-item">

    <div class="history-top">

        <div class="avatar">
            ${h.admin.charAt(0).toUpperCase()}
        </div>

        <div class="history-info">
            <strong>${h.admin}</strong>
            <span>${h.created_at}</span>
        </div>

    </div>

    <div class="history-grade">
        <span class="old">${h.old_value ?? '—'}</span>

        <i class='bx bx-right-arrow-alt'></i>

        <span class="new">${h.new_value ?? '—'}</span>
    </div>

    <div class="history-ip">
        🌐 IP: ${h.ip_address ?? '—'}
    </div>

</div>

`).join('');
                    })
                    .catch(err => {
                        historyBody.innerHTML =
                            '<div style="padding:10px;color:#c0392b;">Xato: tarixni yuklab bo\'lmadi</div>';
                    });
            };

            document.addEventListener('click', function(e) {
                if (!pop.contains(e.target) && !e.target.closest('.jr-cell')) closePop();
                if (!historyPop.contains(e.target) && !e.target.classList.contains('grade-flag')) {
                    historyPop.classList.remove('show');
                }
            });

            renderTableHead();
            renderStudents();
        })();
    </script>

</x-layouts.sidebar>