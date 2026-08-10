<x-layouts.sidebar>
    <x-slot:title>Fanlarni biriktirish</x-slot:title>

    <div class="sf-wrap">

        <div class="sf-header" style="justify-content:space-between; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:14px;">
                <a href="{{ route('subject.index') }}" class="ar-btn">
                    <i class="bx bx-arrow-back"></i>
                </a>
                <div>
                    <div class="oz-title" style="margin:0;">Fanlarni biriktirish {{$kattacount['kattacount']}}</div>
                    <div class="sf-subtitle">Bir xil nomdagi fanlarni bitta katta fanga (masalan: Falsafa) biriktirish</div>
                </div>
            </div>
            <form action="{{ route('subject.biriktirish.sync') }}" method="POST" style="margin:0;"
                  onsubmit="return confirm('Barcha bir xil nomdagi fanlar avtomatik biriktiriladi. Davom etasizmi?')">
                @csrf
                <button type="submit" class="ar-btn ar-btn-ok" style="padding:10px 18px;">
                    <i class="bx bx-refresh"></i> Sinxron qilish
                </button>
            </form>
        </div>

        {{-- Mavjud guruhlar --}}
        @if ($groups->count())
            <div class="sf-card" style="margin-bottom:16px;">
                <p class="sf-section-title">
                    <i class="bx bx-layer"></i> Mavjud guruhlar
                    <span style="font-weight:400; color:#aaa; font-size:12px; margin-left:8px;">
                        (Sinxron qilish — barcha bir xil nomlarni avtomatik guruhlaydi)
                    </span>
                </p>
                <div style="display:flex; flex-wrap:wrap; gap:8px;">
                    @foreach ($groups as $g)
                        <span style="background:#EEEDFE; color:#3C3489; padding:6px 12px; border-radius:8px; font-size:13px; font-weight:600;">
                            {{ $g->nomi }}
                            <span style="background:#3C3489; color:#fff; padding:1px 7px; border-radius:10px; font-size:11px; margin-left:4px;">
                                {{ $g->subjects_count }}
                            </span>
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('subject.biriktirish.store') }}" method="POST" id="biriktirishForm">
            @csrf

            <div class="sf-card">
                <p class="sf-section-title">
                    <i class="bx bx-link"></i> Yangi guruh yaratish / biriktirish
                </p>

                <div class="sf-grid sf-grid-2" style="margin-bottom:18px;">
                    <div>
                        <label class="sf-label">Katta fan nomi (masalan: Falsafa)</label>
                        <input type="text" name="nomi" id="group_nomi" class="arizalar-search" style="width:100%;"
                            placeholder="Falsafa, Matematika..." required value="{{ old('nomi') }}">
                    </div>
                    <div>
                        <label class="sf-label">Fanlarni qidirish</label>
                        <div style="position:relative;">
                            <i class="bx bx-search sf-search-icon"></i>
                            <input type="text" id="subject_search" class="arizalar-search sf-search-input"
                                placeholder="Fan nomi yozing..." autocomplete="off">
                        </div>
                    </div>
                </div>

                {{-- Natijalar jadvali --}}
                <div id="searchResultsWrap" style="display:none;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                        <span style="font-size:13px; color:#555;">
                            Topilgan: <b id="foundCount">0</b> ta fan
                        </span>
                        <div style="display:flex; gap:8px;">
                            <button type="button" id="selectAllBtn" class="ar-btn" style="padding:6px 12px; font-size:12px;">
                                <i class="bx bx-check-square"></i> Hammasini belgilash
                            </button>
                            <button type="button" id="deselectAllBtn" class="ar-btn" style="padding:6px 12px; font-size:12px;">
                                <i class="bx bx-square"></i> Belgilarini olish
                            </button>
                        </div>
                    </div>

                    <div class="arizalar-table-wrap" style="max-height:420px; overflow-y:auto;">
                        <table class="arizalar-table" id="resultsTable">
                            <thead>
                                <tr>
                                    <th style="width:40px;">
                                        <input type="checkbox" id="masterCheck">
                                    </th>
                                    <th style="width:60px;">ID</th>
                                    <th>Fan nomi</th>
                                    <th>O'qituvchi</th>
                                    <th>Yo'nalish</th>
                                    <th>O'quv yili</th>
                                    <th style="width:80px;">Semestr</th>
                                    <th style="width:90px;">Holat</th>
                                </tr>
                            </thead>
                            <tbody id="resultsBody">
                                {{-- JS to'ldiradi --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="emptyState" style="text-align:center; padding:2.5rem; color:#999; display:none;">
                    <i class="bx bx-search" style="font-size:36px; display:block; margin-bottom:8px;"></i>
                    Qidiruv natijasi topilmadi
                </div>

                <div id="initialHint" style="text-align:center; padding:2.5rem; color:#aaa;">
                    <i class="bx bx-info-circle" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                    Yuqorida fan nomini yozing — mos subject lar chiqadi
                </div>
            </div>

            <div class="sf-actions" style="display:flex; justify-content:flex-end; gap:10px;">
                <a href="{{ route('subject.index') }}" class="ar-btn">Bekor qilish</a>
                <button type="submit" id="submitBtn" class="ar-btn ar-btn-ok sf-submit-btn" disabled>
                    <i class="bx bx-link"></i> Hammasiga yuborish
                    <span id="selectedBadge" style="display:none; background:rgba(255,255,255,0.25); padding:2px 8px; border-radius:10px; margin-left:6px; font-size:12px;"></span>
                </button>
            </div>
        </form>
    </div>

    <style>
        .sf-wrap {
            width: 100%;
            margin: 0;
            padding: 0 4px;
            box-sizing: border-box;
        }

        .sf-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
        }

        .sf-subtitle {
            font-size: 13px;
            color: #999;
            margin-top: 2px;
        }

        .sf-card {
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 14px;
            padding: 22px 24px;
            margin-bottom: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .sf-section-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #333;
            margin: 0 0 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f2f2f2;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sf-section-title i {
            color: #3C3489;
            font-size: 16px;
        }

        .sf-grid {
            display: grid;
            gap: 16px 18px;
        }

        .sf-grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        @media (max-width: 640px) {
            .sf-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .sf-label {
            font-size: 12px;
            font-weight: 600;
            color: #888;
            display: block;
            margin-bottom: 6px;
        }

        .sf-search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 16px;
            pointer-events: none;
        }

        .sf-search-input {
            width: 100%;
            padding-left: 34px;
        }

        .sf-actions {
            margin-top: 4px;
        }

        .sf-submit-btn {
            padding: 11px 32px;
            font-weight: 600;
            border-radius: 10px;
        }

        .sf-submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .linked-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .free-badge {
            background: #ecfdf5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        #resultsBody tr.selected-row {
            background: #f5f3ff;
        }
    </style>

    <script>
        const searchInput = document.getElementById('subject_search');
        const resultsBody = document.getElementById('resultsBody');
        const resultsWrap = document.getElementById('searchResultsWrap');
        const emptyState = document.getElementById('emptyState');
        const initialHint = document.getElementById('initialHint');
        const foundCount = document.getElementById('foundCount');
        const submitBtn = document.getElementById('submitBtn');
        const selectedBadge = document.getElementById('selectedBadge');
        const masterCheck = document.getElementById('masterCheck');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const deselectAllBtn = document.getElementById('deselectAllBtn');
        const groupNomi = document.getElementById('group_nomi');

        let searchTimer = null;
        let currentResults = [];

        function updateSubmitState() {
            const checked = resultsBody.querySelectorAll('input.subject-check:checked');
            const count = checked.length;
            const hasName = groupNomi.value.trim().length > 0;

            submitBtn.disabled = !(count > 0 && hasName);

            if (count > 0) {
                selectedBadge.style.display = 'inline';
                selectedBadge.textContent = count + ' ta';
            } else {
                selectedBadge.style.display = 'none';
            }

            // Master checkbox holati
            const all = resultsBody.querySelectorAll('input.subject-check');
            masterCheck.checked = all.length > 0 && checked.length === all.length;
            masterCheck.indeterminate = checked.length > 0 && checked.length < all.length;
        }

        function renderResults(list) {
            currentResults = list;
            resultsBody.innerHTML = '';

            if (list.length === 0) {
                resultsWrap.style.display = 'none';
                emptyState.style.display = 'block';
                initialHint.style.display = 'none';
                updateSubmitState();
                return;
            }

            emptyState.style.display = 'none';
            initialHint.style.display = 'none';
            resultsWrap.style.display = 'block';
            foundCount.textContent = list.length;

            list.forEach(s => {
                const tr = document.createElement('tr');
                if (s.already_linked) tr.style.opacity = '0.65';

                tr.innerHTML = `
                    <td>
                        <input type="checkbox" class="subject-check" name="subject_ids[]" value="${s.id}"
                            ${s.already_linked ? '' : 'checked'}>
                    </td>
                    <td class="ar-id">#${s.id}</td>
                    <td style="font-weight:500;">${escapeHtml(s.nomi)}</td>
                    <td style="font-size:12px; color:#555;">${escapeHtml(s.teacher)}</td>
                    <td style="font-size:12px;">${escapeHtml(s.category)}</td>
                    <td style="font-size:12px; color:#555;">${escapeHtml(s.oquv_yili)}</td>
                    <td style="text-align:center;">
                        <span class="ar-badge ar-badge-accent">${s.semster}-sem</span>
                    </td>
                    <td>
                        ${s.already_linked
                            ? '<span class="linked-badge">Allaqachon birikkan</span>'
                            : '<span class="free-badge">Bo\'sh</span>'}
                    </td>
                `;
                resultsBody.appendChild(tr);
            });

            // Checkbox change
            resultsBody.querySelectorAll('input.subject-check').forEach(cb => {
                cb.addEventListener('change', function () {
                    this.closest('tr').classList.toggle('selected-row', this.checked);
                    updateSubmitState();
                });
                // default checked bo'lsa highlight
                if (cb.checked) cb.closest('tr').classList.add('selected-row');
            });

            updateSubmitState();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }

        function doSearch(q) {
            if (!q || q.length < 1) {
                resultsWrap.style.display = 'none';
                emptyState.style.display = 'none';
                initialHint.style.display = 'block';
                resultsBody.innerHTML = '';
                updateSubmitState();
                return;
            }

            fetch(`{{ route('subject.biriktirish.search') }}?q=${encodeURIComponent(q)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(r => r.json())
                .then(data => renderResults(data))
                .catch(() => {
                    resultsWrap.style.display = 'none';
                    emptyState.style.display = 'block';
                    initialHint.style.display = 'none';
                });
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            const val = this.value.trim();

            // Agar katta fan nomi hali bo'sh bo'lsa, qidiruv so'zini avtomatik yozamiz
            if (!groupNomi.value.trim() && val.length > 0) {
                groupNomi.value = val;
            }

            searchTimer = setTimeout(() => doSearch(val), 280);
        });

        groupNomi.addEventListener('input', updateSubmitState);

        masterCheck.addEventListener('change', function () {
            resultsBody.querySelectorAll('input.subject-check').forEach(cb => {
                cb.checked = masterCheck.checked;
                cb.closest('tr').classList.toggle('selected-row', cb.checked);
            });
            updateSubmitState();
        });

        selectAllBtn.addEventListener('click', function () {
            resultsBody.querySelectorAll('input.subject-check').forEach(cb => {
                cb.checked = true;
                cb.closest('tr').classList.add('selected-row');
            });
            updateSubmitState();
        });

        deselectAllBtn.addEventListener('click', function () {
            resultsBody.querySelectorAll('input.subject-check').forEach(cb => {
                cb.checked = false;
                cb.closest('tr').classList.remove('selected-row');
            });
            updateSubmitState();
        });

        // Form submit oldidan tekshirish
        document.getElementById('biriktirishForm').addEventListener('submit', function (e) {
            const checked = resultsBody.querySelectorAll('input.subject-check:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert("Iltimos, kamida bitta fanni belgilang.");
                return;
            }
            if (!groupNomi.value.trim()) {
                e.preventDefault();
                alert("Katta fan nomini kiriting.");
                return;
            }
        });
    </script>
</x-layouts.sidebar>