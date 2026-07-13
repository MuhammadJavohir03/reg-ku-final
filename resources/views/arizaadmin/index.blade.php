<x-layouts.sidebar>
    <x-slot:title>Arizalar</x-slot:title>

    <!-- ARIZA_ADMIN_VIEW_V3 -->
    <div class="oz-wrap ariza-page">

        <div class="oz-title" style="margin-bottom:16px;">Arizalar</div>

        {{-- ===================== YANGI ARIZA YARATISH PANELI ===================== --}}
        <div class="ariza-card">
            <div class="ariza-card-head">
                <div class="ariza-card-head-icon"><i class="bx bx-plus-circle"></i></div>
                <div>
                    <div class="ariza-card-title">Yangi ariza yaratish</div>
                    <div class="ariza-card-subtitle">Qadamlarni ketma-ket bajaring</div>
                </div>
            </div>

            {{-- STEPPER --}}
            <div class="ariza-stepper" id="arizaStepper">
                <div class="ariza-stepper-dot" data-step="1"><span class="num">1</span><i class="bx bx-check"></i></div>
                <div class="ariza-stepper-line" data-line="1"></div>
                <div class="ariza-stepper-dot" data-step="2"><span class="num">2</span><i class="bx bx-check"></i></div>
                <div class="ariza-stepper-line" data-line="2"></div>
                <div class="ariza-stepper-dot" data-step="3"><span class="num">3</span><i class="bx bx-check"></i></div>
                <div class="ariza-stepper-line" data-line="3"></div>
                <div class="ariza-stepper-dot" data-step="4"><span class="num">4</span><i class="bx bx-check"></i></div>
            </div>

            {{-- 1-QADAM: BO'LIM --}}
            <div class="ariza-step" id="step-1">
                <div class="ariza-step-label">
                    <span class="ariza-step-badge">1</span> Bo'limni tanlang
                </div>
                <select id="bolimSelect" class="ariza-input">
                    <option value="">Tanlang</option>
                    @foreach ($bolimlar as $bolim)
                        <option value="{{ $bolim->id }}">{{ $bolim->nomi }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 2-QADAM: TALABA --}}
            <div class="ariza-step is-locked" id="step-2">
                <div class="ariza-step-label">
                    <span class="ariza-step-badge">2</span> Talaba (ID, ism yoki email)
                </div>
                <div class="ariza-search-wrap">
                    <i class="bx bx-search ariza-search-icon"></i>
                    <input type="text" id="talabaSearch" class="ariza-input ariza-input-icon" placeholder="Masalan: 245 yoki Aziz Aliyev" autocomplete="off">
                </div>

                <div id="userResults" class="ariza-dropdown"></div>

                <div id="selectedUser" class="ariza-selected-user">
                    <div class="ariza-avatar" id="selectedUserAvatar">?</div>
                    <div style="min-width:0;">
                        <div class="ariza-selected-name" id="selectedUserName"></div>
                        <div class="ariza-selected-email" id="selectedUserEmail"></div>
                    </div>
                    <button type="button" id="clearUser" class="ariza-icon-btn" title="O'zgartirish">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
            </div>

            {{-- 3-QADAM: FAN --}}
            <div class="ariza-step is-locked" id="step-3">
                <div class="ariza-step-label">
                    <span class="ariza-step-badge">3</span> Fanni tanlang
                </div>
                <select id="fanSelect" class="ariza-input">
                    <option value="">Tanlang</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->nomi }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 4-QADAM: MAKTAB TURI --}}
            <div class="ariza-step is-locked" id="step-4">
                <div class="ariza-step-label">
                    <span class="ariza-step-badge">4</span> Maktab turini tanlang
                </div>
                <div class="ariza-toggle-group" id="maktabToggle">
                    <button type="button" class="ariza-toggle-btn" data-val="mini">
                        <i class="bx bx-calendar-week"></i> Mini semestr
                    </button>
                    <button type="button" class="ariza-toggle-btn" data-val="free">
                        <i class="bx bx-sun"></i> Free semestr
                    </button>
                </div>
            </div>

            {{-- BAHOLAR OLDINDAN KO'RISH --}}
            <div id="bahoPreview" class="ariza-preview">
                <div class="ariza-step-label" style="margin-bottom:12px;">
                    <i class='bx bx-line-chart' style="color:#6366f1; font-size:16px;"></i> Gradesdagi baholar
                </div>
                <div id="bahoPreviewContent"></div>
            </div>

            <button type="button" id="arizaYaratishBtn" class="ariza-submit-btn" disabled>
                <i class="bx bx-send"></i> Ariza yaratish
            </button>
        </div>

        {{-- ===================== MAVJUD ARIZALAR JADVALI ===================== --}}
        <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px; flex-wrap:wrap;">
            <form method="GET" action="{{ route('ariza_admin.index') }}" class="ariza-search-wrap" style="max-width:460px; width:100%;">
                <i class="bx bx-search ariza-search-icon"></i>
                <input type="text" name="q" value="{{ $q }}" class="ariza-input ariza-input-icon"
                       placeholder="Talaba, fan yoki bo'lim bo'yicha qidirish" autocomplete="off">
                @if ($q !== '')
                    <a href="{{ route('ariza_admin.index') }}" class="ariza-icon-btn" style="position:absolute; right:8px; top:50%; transform:translateY(-50%);" title="Tozalash">
                        <i class="bx bx-x"></i>
                    </a>
                @endif
            </form>

            <div class="oz-title" style="font-size:16px; margin-bottom:0;">Barcha arizalar</div>
        </div>

        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
    <thead>
        <tr>
            <th style="width:60px;text-align:center;">№</th>
            <th style="min-width:320px;">Talaba</th>
            <th style="min-width:220px;">Fan</th>
            <th style="min-width:220px;">Bo'lim</th>
            <th style="width:120px;text-align:center;">Maktab turi</th>
            <th style="width:90px;text-align:center;">Umumiy</th>
            <th style="width:130px;text-align:center;">Status</th>
            <th style="width:140px;text-align:center;">Sana</th>
            <th style="width:90px;text-align:center;">Amal</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($arizalar as $index => $ariza)
            <tr>

                {{-- № --}}
                <td class="text-center fw-bold">
                    {{ $arizalar->firstItem() + $index }}
                </td>

                {{-- Talaba --}}
                <td>
                    <div style="font-weight:600;font-size:15px;">
                        {{ $ariza->user['To‘liq_ismi'] ?? $ariza->user->ism_familiya ?? '—' }}
                    </div>

                    <div style="font-size:12px;color:#6c757d;">
                        ID: {{ $ariza->user['Talaba_ID'] ?? '-' }}
                    </div>

                    <div style="font-size:12px;color:#6c757d;">
                        {{ $ariza->user['email'] ?? '' }}
                    </div>
                </td>

                {{-- Fan --}}
                <td>
                    {{ $ariza->subject->nomi ?? '—' }}
                </td>

                {{-- Bo'lim --}}
                <td>
                    {{ $ariza->bolim->nomi ?? '—' }}
                </td>

                {{-- Maktab turi --}}
                <td class="text-center">
                    @if($ariza->maktab_turi == 'Mini')
                        <span class="badge bg-primary">
                            Mini Semestr
                        </span>
                    @else
                        <span class="badge bg-success">
                            Bepul Imkoniyat
                        </span>
                    @endif
                </td>

                {{-- Umumiy --}}
                <td class="text-center">
                    <span style="font-size:18px;font-weight:700;">
                        {{ $ariza->umumiy }}
                    </span>
                </td>

                {{-- Status --}}
                <td class="text-center">
                    @if ((int) $ariza->status === 1)
                        <span class="ar-badge ar-badge-ok">
                            <i class="bx bx-check-circle"></i>
                            Active
                        </span>
                    @else
                        <span class="ar-badge" style="background:#fdecea;color:#c0392b;">
                            <i class="bx bx-block"></i>
                            Block
                        </span>
                    @endif
                </td>

                {{-- Sana --}}
                <td class="text-center">
                    <div>
                        {{ optional($ariza->created_at)->format('d.m.Y') }}
                    </div>

                    <small class="text-muted">
                        {{ optional($ariza->created_at)->format('H:i') }}
                    </small>
                </td>

                {{-- Amal --}}
                <td class="text-center">
                    <form action="{{ route('ariza_admin.destroy', $ariza->id) }}"
                        method="POST"
                        onsubmit="return confirm('Arizani o\'chirishni tasdiqlaysizmi?');">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            class="ar-btn"
                            style="color:#dc3545;"
                            title="O'chirish">

                            <i class="bx bx-trash"></i>

                        </button>

                    </form>
                </td>

            </tr>
        @empty

            <tr>
                <td colspan="9"
                    style="text-align:center;padding:35px;color:#888;">
                    Arizalar topilmadi.
                </td>
            </tr>

        @endforelse
    </tbody>
</table>
        </div>

        @if ($arizalar->hasPages())
            <div style="margin-top:16px;">{{ $arizalar->links() }}</div>
        @endif
    </div>

    <style>
        .ariza-page { --ariza-primary:#4f46e5; --ariza-primary-light:#eef2ff; --ariza-success:#16a34a; --ariza-success-light:#f0fdf4; --ariza-danger:#dc2626; --ariza-danger-light:#fef2f2; --ariza-warn:#d97706; --ariza-warn-light:#fffbeb; --ariza-border:#e5e7eb; --ariza-text-muted:#8a8f98; }

        .ariza-card {
            background:#fff;
            border:1px solid var(--ariza-border);
            border-radius:18px;
            padding:28px;
            margin-bottom:28px;
            box-shadow:0 1px 3px rgba(16,24,40,.04), 0 8px 24px rgba(16,24,40,.03);
        }
        .ariza-card-head { display:flex; align-items:center; gap:14px; margin-bottom:26px; }
        .ariza-card-head-icon {
            width:42px; height:42px; border-radius:12px;
            background:linear-gradient(135deg,#6366f1,#4338ca);
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:20px; flex-shrink:0;
            box-shadow:0 6px 14px rgba(79,70,229,.28);
        }
        .ariza-card-title { font-size:16px; font-weight:700; color:#1f2430; }
        .ariza-card-subtitle { font-size:12.5px; color:var(--ariza-text-muted); margin-top:2px; }

        .ariza-stepper { display:flex; align-items:center; margin-bottom:30px; padding:0 4px; }
        .ariza-stepper-dot {
            width:30px; height:30px; border-radius:50%;
            background:#f1f2f6; color:#9aa0ac;
            display:flex; align-items:center; justify-content:center;
            font-size:13px; font-weight:700; flex-shrink:0;
            transition:all .35s ease; position:relative;
        }
        .ariza-stepper-dot .num { transition:opacity .2s ease; }
        .ariza-stepper-dot i { position:absolute; opacity:0; font-size:16px; transition:opacity .2s ease; }
        .ariza-stepper-dot.is-active { background:var(--ariza-primary); color:#fff; box-shadow:0 0 0 5px rgba(79,70,229,.15); }
        .ariza-stepper-dot.is-done { background:var(--ariza-success); color:#fff; }
        .ariza-stepper-dot.is-done .num { opacity:0; }
        .ariza-stepper-dot.is-done i { opacity:1; }
        .ariza-stepper-line { flex:1; height:2px; background:#eef0f3; margin:0 6px; border-radius:2px; transition:background .35s ease; }
        .ariza-stepper-line.is-done { background:var(--ariza-success); }

        .ariza-step { padding:18px 0; border-top:1px solid #f1f2f6; transition:opacity .35s ease, filter .35s ease; }
        #step-1.ariza-step { border-top:none; padding-top:0; }
        .ariza-step.is-locked { opacity:.4; filter:grayscale(.4); pointer-events:none; user-select:none; }
        .ariza-step-label { font-size:13.5px; font-weight:600; color:#3a3f4b; margin-bottom:10px; display:flex; align-items:center; gap:8px; }
        .ariza-step-badge {
            width:20px; height:20px; border-radius:6px; background:var(--ariza-primary-light); color:var(--ariza-primary);
            font-size:11px; display:inline-flex; align-items:center; justify-content:center; font-weight:700;
        }

        .ariza-input {
            width:100%; max-width:380px; padding:11px 14px;
            border:1.5px solid var(--ariza-border); border-radius:10px;
            font-size:13.5px; color:#1f2430; background:#fff;
            transition:border-color .2s ease, box-shadow .2s ease;
            outline:none;
        }
        .ariza-input:focus { border-color:var(--ariza-primary); box-shadow:0 0 0 3px rgba(79,70,229,.12); }
        .ariza-input-icon { padding-left:38px; }

        .ariza-search-wrap { position:relative; max-width:380px; }
        .ariza-search-icon { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:#9aa0ac; font-size:16px; }

        .ariza-dropdown {
            display:none; margin-top:8px; max-width:380px;
            border:1px solid var(--ariza-border); border-radius:12px;
            max-height:230px; overflow:auto; box-shadow:0 10px 24px rgba(16,24,40,.08);
            background:#fff;
        }
        .user-result-item { padding:10px 14px; cursor:pointer; font-size:13px; transition:background .15s ease; border-bottom:1px solid #f5f6f8; }
        .user-result-item:last-child { border-bottom:none; }
        .user-result-item:hover { background:var(--ariza-primary-light); }
        .user-result-item .urn { font-weight:600; color:#1f2430; }
        .user-result-item .ure { color:var(--ariza-text-muted); font-size:12px; margin-top:1px; }

        .ariza-selected-user {
            display:none; align-items:center; gap:12px; margin-top:14px;
            max-width:380px; background:var(--ariza-primary-light); border-radius:12px; padding:10px 14px;
        }
        .ariza-avatar {
            width:36px; height:36px; border-radius:50%; flex-shrink:0;
            background:linear-gradient(135deg,#818cf8,#4f46e5); color:#fff;
            display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px;
        }
        .ariza-selected-name { font-weight:600; font-size:13.5px; color:#1f2430; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .ariza-selected-email { font-size:12px; color:var(--ariza-text-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .ariza-icon-btn {
            margin-left:auto; width:26px; height:26px; border-radius:8px; border:none;
            background:#fff; color:#6b7280; display:flex; align-items:center; justify-content:center;
            cursor:pointer; flex-shrink:0; transition:background .15s ease;
        }
        .ariza-icon-btn:hover { background:#e5e7eb; color:#dc2626; }

        .ariza-toggle-group { display:flex; gap:10px; flex-wrap:wrap; }
        .ariza-toggle-btn {
            display:flex; align-items:center; gap:8px; padding:11px 18px;
            border:1.5px solid var(--ariza-border); border-radius:10px; background:#fff;
            font-size:13.5px; font-weight:600; color:#4b5262; cursor:pointer;
            transition:all .2s ease;
        }
        .ariza-toggle-btn i { font-size:16px; color:#9aa0ac; transition:color .2s ease; }
        .ariza-toggle-btn:hover { border-color:#c7ccf7; }
        .ariza-toggle-btn.is-selected {
            border-color:var(--ariza-primary); background:var(--ariza-primary-light); color:var(--ariza-primary);
        }
        .ariza-toggle-btn.is-selected i { color:var(--ariza-primary); }

        .ariza-preview {
            display:none; margin-top:8px; padding-top:20px; border-top:1px solid #f1f2f6;
            animation:arizaFadeIn .3s ease;
        }
        @keyframes arizaFadeIn { from { opacity:0; transform:translateY(4px);} to { opacity:1; transform:translateY(0);} }

        .ariza-grade-grid { display:flex; gap:12px; flex-wrap:wrap; }
        .ariza-grade-chip {
            min-width:110px; padding:12px 16px; border-radius:12px;
            background:#f8f9fb; border:1px solid #f0f1f4;
        }
        .ariza-grade-chip .label { font-size:11.5px; color:var(--ariza-text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.3px; }
        .ariza-grade-chip .value { font-size:19px; font-weight:700; color:#1f2430; margin-top:4px; }
        .ariza-grade-chip.is-total { background:var(--ariza-primary-light); border-color:#dfe3fb; }
        .ariza-grade-chip.is-total .value { color:var(--ariza-primary); }
        .ariza-grade-chip.is-low .value { color:var(--ariza-danger); }
        .ariza-grade-chip.is-ok .value { color:var(--ariza-success); }

        .ariza-empty-grade {
            display:flex; align-items:flex-start; gap:12px;
            background:var(--ariza-warn-light); border:1px solid #fde68a; color:#92620a;
            padding:14px 16px; border-radius:12px; font-size:13px; line-height:1.5;
        }
        .ariza-empty-grade i { font-size:20px; flex-shrink:0; margin-top:1px; }

        .ariza-submit-btn {
            display:inline-flex; align-items:center; gap:8px; margin-top:22px;
            padding:12px 24px; border:none; border-radius:11px;
            background:linear-gradient(135deg,#6366f1,#4338ca); color:#fff;
            font-size:14px; font-weight:700; cursor:pointer;
            box-shadow:0 8px 18px rgba(79,70,229,.28);
            transition:transform .15s ease, box-shadow .15s ease, opacity .2s ease;
        }
        .ariza-submit-btn:hover:not(:disabled) { transform:translateY(-1px); box-shadow:0 10px 22px rgba(79,70,229,.35); }
        .ariza-submit-btn:disabled { opacity:.4; cursor:not-allowed; box-shadow:none; }
    </style>

    <script>
    console.log('[ARIZA_ADMIN_V3] script bloki topildi va ishga tushdi');
    try {
        (function () {
            const bolimSelect     = document.getElementById('bolimSelect');
            const step2           = document.getElementById('step-2');
            const searchInput     = document.getElementById('talabaSearch');
            const userResults     = document.getElementById('userResults');
            const selectedUser    = document.getElementById('selectedUser');
            const selectedAvatar  = document.getElementById('selectedUserAvatar');
            const selectedName    = document.getElementById('selectedUserName');
            const selectedEmail   = document.getElementById('selectedUserEmail');
            const clearUserBtn    = document.getElementById('clearUser');
            const step3           = document.getElementById('step-3');
            const fanSelect       = document.getElementById('fanSelect');
            const step4           = document.getElementById('step-4');
            const maktabToggle    = document.getElementById('maktabToggle');
            const bahoPreview     = document.getElementById('bahoPreview');
            const bahoPreviewCont = document.getElementById('bahoPreviewContent');
            const createBtn       = document.getElementById('arizaYaratishBtn');
            const stepper         = document.getElementById('arizaStepper');

            const requiredEls = { bolimSelect, step2, searchInput, userResults, selectedUser, selectedAvatar, selectedName, selectedEmail, clearUserBtn, step3, fanSelect, step4, maktabToggle, bahoPreview, bahoPreviewCont, createBtn, stepper };
            for (const key in requiredEls) {
                if (!requiredEls[key]) {
                    console.error('[ARIZA_ADMIN_V3] Element topilmadi:', key, '— HTML idlari mos kelmayapti.');
                    return;
                }
            }
            console.log('[ARIZA_ADMIN_V3] Barcha elementlar topildi, event listenerlar ulanmoqda...');

            let currentUserId = null;
            let currentMaktabTuri = null;
            let searchTimer = null;

            function setStepper(activeStep) {
                [1, 2, 3, 4].forEach(function (n) {
                    const dot = stepper.querySelector('[data-step="' + n + '"]');
                    dot.classList.remove('is-active', 'is-done');
                    if (n < activeStep) dot.classList.add('is-done');
                    else if (n === activeStep) dot.classList.add('is-active');
                });
                [1, 2, 3].forEach(function (n) {
                    const line = stepper.querySelector('[data-line="' + n + '"]');
                    line.classList.toggle('is-done', n < activeStep);
                });
            }

            function unlock(stepEl) { stepEl.classList.remove('is-locked'); }
            function lock(stepEl) { stepEl.classList.add('is-locked'); }

            function initials(name) {
                if (!name) return '?';
                const parts = name.trim().split(/\s+/);
                return ((parts[0] && parts[0][0] || '') + (parts[1] && parts[1][0] || '')).toUpperCase() || '?';
            }

            function resetFromUser() {
                currentUserId = null;
                selectedUser.style.display = 'none';
                searchInput.value = '';
                userResults.style.display = 'none';
                resetFromFan();
            }

            function resetFromFan() {
                fanSelect.value = '';
                lock(step3);
                resetFromMaktab();
            }

            function resetFromMaktab() {
                currentMaktabTuri = null;
                maktabToggle.querySelectorAll('.ariza-toggle-btn').forEach(function (b) { b.classList.remove('is-selected'); });
                lock(step4);
                bahoPreview.style.display = 'none';
                bahoPreviewCont.innerHTML = '';
                createBtn.disabled = true;
            }

            function updateStepperState() {
                let n = 1;
                if (bolimSelect.value) n = 2;
                if (bolimSelect.value && currentUserId) n = 3;
                if (bolimSelect.value && currentUserId && fanSelect.value) n = 4;
                if (bolimSelect.value && currentUserId && fanSelect.value && currentMaktabTuri) n = 5;
                setStepper(n);
            }

            bolimSelect.addEventListener('change', function () {
                console.log('[ARIZA_ADMIN_V3] bolim change:', this.value);
                resetFromUser();
                if (this.value) unlock(step2); else lock(step2);
                updateStepperState();
            });

            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                const q = this.value.trim();

                if (q.length < 1) {
                    userResults.style.display = 'none';
                    userResults.innerHTML = '';
                    return;
                }

                searchTimer = setTimeout(function () { searchUsers(q); }, 350);
            });

            function searchUsers(q) {
                fetch(`{{ route('ariza_admin.search_user') }}?q=${encodeURIComponent(q)}`)
                    .then(function (r) { return r.json(); })
                    .then(function (data) { renderUserResults(data.users || []); })
                    .catch(function () {
                        userResults.innerHTML = '<div style="padding:12px; color:#dc2626; font-size:13px;">Xatolik yuz berdi</div>';
                        userResults.style.display = 'block';
                    });
            }

            function renderUserResults(users) {
                if (!users.length) {
                    userResults.innerHTML = '<div style="padding:12px; color:#9aa0ac; font-size:13px; text-align:center;">Topilmadi</div>';
                    userResults.style.display = 'block';
                    return;
                }

                userResults.innerHTML = users.map(function (u) {
                    return '<div class="user-result-item" data-id="' + u.id + '" data-name="' + (u.name || '') + '" data-email="' + (u.email || '') + '">'
                        + '<div class="urn">' + (u.name || '—') + ' <span style="color:#c2c6ce;">#' + u.id + '</span></div>'
                        + '<div class="ure">' + (u.email || '') + '</div>'
                        + '</div>';
                }).join('');

                userResults.style.display = 'block';

                userResults.querySelectorAll('.user-result-item').forEach(function (item) {
                    item.addEventListener('click', function () {
                        selectUser(this.dataset.id, this.dataset.name, this.dataset.email);
                    });
                });
            }

            function selectUser(id, name, email) {
                currentUserId = id;
                selectedAvatar.textContent = initials(name);
                selectedName.textContent = name || '—';
                selectedEmail.textContent = email || '';
                selectedUser.style.display = 'flex';
                userResults.style.display = 'none';
                searchInput.value = '';

                resetFromFan();
                unlock(step3);
                updateStepperState();
            }

            clearUserBtn.addEventListener('click', function () {
                resetFromUser();
                lock(step3);
                updateStepperState();
            });

            fanSelect.addEventListener('change', function () {
                console.log('[ARIZA_ADMIN_V3] fan change:', this.value);
                resetFromMaktab();
                if (this.value) unlock(step4); else lock(step4);
                updateStepperState();
            });

            maktabToggle.querySelectorAll('.ariza-toggle-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    maktabToggle.querySelectorAll('.ariza-toggle-btn').forEach(function (b) { b.classList.remove('is-selected'); });
                    this.classList.add('is-selected');
                    currentMaktabTuri = this.dataset.val;

                    bahoPreview.style.display = 'none';
                    bahoPreviewCont.innerHTML = '';
                    createBtn.disabled = true;
                    updateStepperState();

                    if (currentUserId && fanSelect.value) {
                        checkGrade(currentUserId, fanSelect.value);
                    }
                });
            });

            function checkGrade(userId, subjectId) {
                bahoPreview.style.display = 'block';
                bahoPreviewCont.innerHTML = '<div style="color:#9aa0ac; font-size:13px; padding:6px 0;">Tekshirilmoqda...</div>';

                fetch(`{{ route('ariza_admin.check_grade') }}?user_id=${userId}&subject_id=${subjectId}`)
                    .then(function (r) { return r.json(); })
                    .then(function (data) { renderBahoPreview(data); })
                    .catch(function () {
                        bahoPreviewCont.innerHTML = '<div style="color:#dc2626; font-size:13px; padding:6px 0;">Xatolik yuz berdi</div>';
                    });
            }

            function chip(label, value, extraClass) {
                return '<div class="ariza-grade-chip' + (extraClass ? ' ' + extraClass : '') + '">'
                    + '<div class="label">' + label + '</div>'
                    + '<div class="value">' + (value == null ? 0 : value) + '</div>'
                    + '</div>';
            }

            function renderBahoPreview(data) {
                if (!data.exists) {
                    bahoPreviewCont.innerHTML = '<div class="ariza-empty-grade">'
                        + '<i class="bx bx-error-circle"></i>'
                        + '<div>Import grade bookda baholar mavjud emas. Ariza barcha baholari <b>0</b> qilib yaratiladi.</div>'
                        + '</div>';
                    createBtn.disabled = false;
                    return;
                }

                const b = data.baho;
                const umumiy = b.umumiy == null ? 0 : b.umumiy;
                const totalClass = umumiy < 60 ? 'is-low' : 'is-ok';

                bahoPreviewCont.innerHTML = '<div class="ariza-grade-grid">'
                    + chip('Joriy', b.joriy_baho)
                    + chip('Oraliq', b.oraliq_baho)
                    + chip('Joriy oraliq', b.joriy_oraliq)
                    + chip('Yakuniy', b.yakuniy_baho)
                    + chip('Umumiy', b.umumiy, 'is-total ' + totalClass)
                    + '</div>';

                createBtn.disabled = false;
            }

            createBtn.addEventListener('click', function () {
                if (!bolimSelect.value || !currentUserId || !fanSelect.value || !currentMaktabTuri) {
                    alert("Iltimos bo'lim, talaba, fan va maktab turini tanlang");
                    return;
                }

                createBtn.disabled = true;

                fetch(`{{ route('ariza_admin.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        user_id: currentUserId,
                        subject_id: fanSelect.value,
                        bolim_id: bolimSelect.value,
                        maktab_turi: currentMaktabTuri,
                    }),
                })
                .then(function (r) {
                    if (!r.ok) {
                        return r.json().catch(function () { return {}; }).then(function (err) {
                            throw new Error(err.message || 'xatolik');
                        });
                    }
                    window.location.reload();
                })
                .catch(function (e) {
                    alert(e.message || 'Ariza yaratishda xatolik yuz berdi');
                    createBtn.disabled = false;
                });
            });

            document.addEventListener('click', function (e) {
                if (!userResults.contains(e.target) && e.target !== searchInput) {
                    userResults.style.display = 'none';
                }
            });

            updateStepperState();
            console.log('[ARIZA_ADMIN_V3] Muvaffaqiyatli ishga tushdi, tayyor.');
        })();
    } catch (e) {
        console.error('[ARIZA_ADMIN_V3] KUTILMAGAN XATOLIK:', e);
    }
    </script>
</x-layouts.sidebar>