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
                    <label for="bolim_id">Bo'lim</label>
                    <select id="bolim_id" class="jr-select">
                        <option value="">Bo'limni tanlang</option>
                        @foreach ($bolimlar as $bolim)
                            <option value="{{ $bolim->id }}">{{ $bolim->nomi }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="jr-field">
                    <label for="school_type">Maktab turi</label>
                    <select id="school_type" class="jr-select" disabled>
                        <option value="">Avval bo'limni tanlang</option>
                        <option value="free">Bepul maktab</option>
                        <option value="mini">Mini Semestr</option>
                    </select>
                </div>

                <div class="jr-field">
                    <label for="subject_id">Fan</label>
                    <select id="subject_id" class="jr-select" disabled>
                        <option value="">Avval maktab turini tanlang</option>
                    </select>
                </div>

                <div class="jr-field">
                    <label for="guruh_filter">Guruh</label>
                    <select id="guruh_filter" class="jr-select" disabled>
                        <option value="">Avval fanni tanlang</option>
                    </select>
                </div>

                <hr class="jr-hr">
                <div id="debugBox" style="font-size:11px;color:#c0392b;white-space:pre-wrap;"></div>
            </aside>

            {{-- ================= JADVAL ================= --}}
            <div class="jr-main-card">
                <div class="jr-topbar">
                    <h2 id="subjectTitle">Fan tanlanmagan</h2>
                    <div class="jr-top-actions">
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

                <input id="manualVal" class="grade-input" type="number" step="0.01" placeholder="0.00">>

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

            <button class="btn-clear" id="clearVal">

                <i class="fas fa-rotate-left"></i>

                Tozalash

            </button>


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

        /* #historyPop {
            min-width: 260px;
            max-width: 320px;
            max-height: 280px;
            overflow-y: auto;
        } */

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

            const bolimSelect = document.getElementById('bolim_id');
            const typeSelect = document.getElementById('school_type');
            const subjectSelect = document.getElementById('subject_id');
            const groupFilterSelect = document.getElementById('guruh_filter');
            const subjectTitle = document.getElementById('subjectTitle');
            const theadRow = document.getElementById('theadRow');
            const tbody = document.getElementById('tbody');
            const rangeInfo = document.getElementById('rangeInfo');
            const pager = document.getElementById('pager');
            const pageSizeSelect = document.getElementById('pageSize');

            // ================= 1) BO'LIM TANLASH =================
            bolimSelect.addEventListener('change', function() {
                state.bolimId = this.value || null;
                resetAfterBolim();

                if (!state.bolimId) {
                    typeSelect.disabled = true;
                    typeSelect.value = '';
                    subjectSelect.disabled = true;
                    subjectSelect.innerHTML = '<option value="">Avval maktab turini tanlang</option>';
                    return;
                }
                typeSelect.disabled = false;
                typeSelect.value = '';
                subjectSelect.disabled = true;
                subjectSelect.innerHTML = '<option value="">Avval maktab turini tanlang</option>';
            });

            // ================= 2) MAKTAB TURI TANLASH =================
            typeSelect.addEventListener('change', function() {
                state.type = this.value || null;
                resetAfterType();

                if (!state.bolimId || !state.type) {
                    subjectSelect.disabled = true;
                    subjectSelect.innerHTML = '<option value="">Avval maktab turini tanlang</option>';
                    return;
                }

                subjectSelect.disabled = true;
                subjectSelect.innerHTML = '<option value="">Yuklanmoqda...</option>';

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
                        if (!data.length) {
                            subjectSelect.innerHTML =
                                '<option value="">Bu bo\'limda fan topilmadi</option>';
                            subjectSelect.disabled = true;
                            return;
                        }
                        let html = '<option value="">Fan tanlang</option>';
                        data.forEach(item => html += `<option value="${item.id}">${item.nomi}</option>`);
                        subjectSelect.innerHTML = html;
                        subjectSelect.disabled = false;
                    })
                    .catch(err => debug('Fanlarni yuklashda xato: ' + err.message));
            });

            // ================= 3) FAN TANLASH =================
            subjectSelect.addEventListener('change', function() {
                state.subjectId = this.value || null;
                state.page = 1;
                state.students = [];
                state.topics = [];
                resetGroupFilter();

                if (!state.subjectId) {
                    subjectTitle.textContent = 'Fan tanlanmagan';
                    renderTableHead();
                    renderStudents();
                    return;
                }

                subjectTitle.textContent = this.options[this.selectedIndex].text;
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
                resetGroupFilter();
                renderTableHead();
                renderStudents();
            }

            function resetAfterType() {
                state.subjectId = null;
                state.students = [];
                state.topics = [];
                subjectTitle.textContent = 'Fan tanlanmagan';
                resetGroupFilter();
                renderTableHead();
                renderStudents();
            }

            // ================= GURUH FILTRI =================
            function resetGroupFilter() {
                state.groupFilter = null;
                groupFilterSelect.disabled = true;
                groupFilterSelect.innerHTML = '<option value="">Avval fanni tanlang</option>';
            }

            function populateGroupFilter() {
                const groups = Array.from(new Set(state.students.map(s => s.group).filter(Boolean))).sort();
                let html = '<option value="">Barcha guruhlar</option>';
                groups.forEach(g => html += `<option value="${g}">${g}</option>`);
                groupFilterSelect.innerHTML = html;
                groupFilterSelect.disabled = groups.length === 0;
                state.groupFilter = null;
            }

            groupFilterSelect.addEventListener('change', function() {
                state.groupFilter = this.value || null;
                state.page = 1;
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

            document.getElementById('applyVal').addEventListener('click', function() {
                const v = parseFloat(manualInput.value.replace(',', '.'));
                if (isNaN(v) || v < 0 || v > 100) return;
                submitGrade(v);
            });

            document.getElementById('clearVal').addEventListener('click', function() {
                submitGrade(null);
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
