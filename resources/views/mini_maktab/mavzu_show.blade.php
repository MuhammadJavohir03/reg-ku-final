<x-layouts.sidebar>
    <x-slot:title>{{ $mavzu->nomi }}</x-slot:title>

    <div class="oz-wrap">

        {{-- HEADER --}}
        <div
            style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <a href="{{ route('mini_maktab.mavzular', [$bolim->id, $subject->id]) }}" class="ar-btn">
                    <i class="bx bx-arrow-back"></i>
                </a>
                <div>
                    @php
                        $turRangi = match ($mavzu->tur) {
                            'mavzu' => ['bg' => 'rgba(0,245,212,0.12)', 'txt' => '#00f5d4', 'label' => 'Mavzu'],
                            'oraliq' => ['bg' => 'rgba(255,193,7,0.15)', 'txt' => '#ffc107', 'label' => 'Oraliq'],
                            'yakuniy' => ['bg' => 'rgba(0,229,160,0.14)', 'txt' => '#00e5a0', 'label' => 'Yakuniy'],
                            default => [
                                'bg' => 'rgba(124,92,255,0.12)',
                                'txt' => '#9b97c0',
                                'label' => ucfirst($mavzu->tur),
                            ],
                        };
                    @endphp
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span
                            style="font-size:11px; font-weight:600; padding:3px 9px; border-radius:20px;
                            background:{{ $turRangi['bg'] }}; color:{{ $turRangi['txt'] }};">
                            {{ $turRangi['label'] }}
                        </span>
                        <div class="oz-title" style="margin:0;">{{ $mavzu->nomi }}</div>
                    </div>
                    <div style="font-size:12px; color:var(--jd-muted, #9b97c0); margin-top:2px;">
                        {{ $subject->nomi }} · {{ $bolim->nomi }}
                    </div>
                </div>
            </div>

            {{-- Material qo'shish tugmalari --}}
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <button onclick="openModal('test')"
                    style="display:flex; align-items:center; gap:6px; padding:8px 14px;
                           border-radius:8px; border:none; cursor:pointer; font-size:13px;
                           background:rgba(0,245,212,0.12); color:#00f5d4; font-weight:500;">
                    <i class="bx bx-clipboard"></i> Test qo'shish
                </button>
                <button onclick="openModal('video')"
                    style="display:flex; align-items:center; gap:6px; padding:8px 14px;
                           border-radius:8px; border:none; cursor:pointer; font-size:13px;
                           background:rgba(255,92,138,0.12); color:#ff5c8a; font-weight:500;">
                    <i class="bx bx-video"></i> Video qo'shish
                </button>
                <button onclick="openModal('pdf')"
                    style="display:flex; align-items:center; gap:6px; padding:8px 14px;
                           border-radius:8px; border:none; cursor:pointer; font-size:13px;
                           background:rgba(255,92,138,0.1); color:#ff5c8a; font-weight:500;">
                    <i class="bx bx-file-pdf"></i> PDF qo'shish
                </button>
                <button onclick="openModal('topshiriq')"
                    style="display:flex; align-items:center; gap:6px; padding:8px 14px;
                           border-radius:8px; border:none; cursor:pointer; font-size:13px;
                           background:rgba(123,44,191,0.18); color:#c084fc; font-weight:500;">
                    <i class="bx bx-task"></i> Topshiriq qo'shish
                </button>
            </div>
        </div>
        {{-- ══════════════════════════════════════════
             MATERIALLAR RO'YXATI
        ══════════════════════════════════════════ --}}
        @if ($materiallar->isEmpty())
            <div
                style="text-align:center; padding:48px 20px;
                 border:1px dashed rgba(124,92,255,0.3); border-radius:12px;
                 color:var(--jd-muted, #9b97c0); font-size:14px;
                 background:rgba(18,16,40,0.4);">
                <i class="bx bx-folder-open" style="font-size:40px; margin-bottom:10px; display:block;"></i>
                Hozircha material yo'q. Yuqoridagi tugmalardan birini bosing.
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:12px;">
                @foreach ($materiallar as $m)
                    @php
                        $mRangi = match ($m->tur) {
                            'test' => [
                                'bg' => 'rgba(0,245,212,0.12)',
                                'txt' => '#00f5d4',
                                'icon' => 'bx-clipboard',
                                'label' => 'TEST',
                            ],
                            'video' => [
                                'bg' => 'rgba(255,92,138,0.12)',
                                'txt' => '#ff5c8a',
                                'icon' => 'bx-video',
                                'label' => 'VIDEO',
                            ],
                            'pdf' => [
                                'bg' => 'rgba(255,92,138,0.1)',
                                'txt' => '#ff5c8a',
                                'icon' => 'bx-file-pdf',
                                'label' => 'PDF',
                            ],
                            'topshiriq' => [
                                'bg' => 'rgba(123,44,191,0.18)',
                                'txt' => '#c084fc',
                                'icon' => 'bx-task',
                                'label' => 'TOPSHIRIQ',
                            ],
                            default => [
                                'bg' => 'rgba(124,92,255,0.1)',
                                'txt' => '#9b97c0',
                                'icon' => 'bx-file',
                                'label' => strtoupper($m->tur),
                            ],
                        };
                    @endphp

                    <div
                        style="background:var(--jd-glass, rgba(18,16,40,0.7));
                         border:1px solid var(--jd-glass-border, rgba(124,92,255,0.28));
                         border-radius:12px; overflow:hidden;">

                        {{-- Material sarlavhasi --}}
                        <div
                            style="display:flex; align-items:center; justify-content:space-between;
                             padding:14px 16px;
                             border-bottom:{{ in_array($m->tur, ['test', 'topshiriq']) ? '1px solid rgba(124,92,255,0.12)' : 'none' }};">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div
                                    style="width:36px; height:36px; border-radius:8px; background:{{ $mRangi['bg'] }};
                                     display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <i class="bx {{ $mRangi['icon'] }}"
                                        style="font-size:17px; color:{{ $mRangi['txt'] }};"></i>
                                </div>
                                <div>
                                    <div style="font-size:14px; font-weight:600; color:var(--jd-ink, #e8e6ff);">
                                        {{ $m->nomi }}</div>
                                    <div style="font-size:11px; font-weight:600; color:{{ $mRangi['txt'] }};">
                                        {{ $mRangi['label'] }}
                                        @if ($m->tur === 'video' && $m->video_size)
                                            · {{ $m->video_size }}
                                        @endif
                                        @if (in_array($m->tur, ['pdf', 'topshiriq']) && $m->pdf_size)
                                            · {{ $m->pdf_size }}
                                        @endif
                                        @if ($m->tur === 'pdf' && $m->pdf_sahifalar)
                                            · {{ $m->pdf_sahifalar }} sahifa
                                        @endif
                                        @if ($m->tur === 'test' && $m->bank)
                                            · {{ $m->bank->nomi }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div style="display:flex; align-items:center; gap:8px;">
                                @if ($m->tur === 'video' && method_exists($m, 'videoUrl') && $m->videoUrl())
                                    <a href="{{ $m->videoUrl() }}" target="_blank" class="ar-btn"
                                        style="font-size:12px;">
                                        <i class="bx bx-play-circle"></i> Ko'rish
                                    </a>
                                @endif
                                @if (in_array($m->tur, ['pdf', 'topshiriq']) && method_exists($m, 'pdfUrl') && $m->pdfUrl())
                                    <a href="{{ $m->pdfUrl() }}" target="_blank" class="ar-btn"
                                        style="font-size:12px;">
                                        <i class="bx bx-show"></i> Ko'rish
                                    </a>
                                @elseif(in_array($m->tur, ['pdf', 'topshiriq']) && $m->pdf_path)
                                    <a href="{{ asset('storage/' . $m->pdf_path) }}" target="_blank" class="ar-btn"
                                        style="font-size:12px;">
                                        <i class="bx bx-show"></i> Ko'rish
                                    </a>
                                @endif

                                <form action="{{ route('mini_maktab.material.ochir', $m->id) }}" method="POST"
                                    onsubmit="return confirm('Material o\'chirilsinmi?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ar-btn"
                                        style="background:rgba(255,92,138,0.12); color:#ff5c8a; border:none; cursor:pointer; font-size:12px;">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- TEST sozlamalari --}}
                        @if ($m->tur === 'test')
                            <div style="padding:14px 16px; background:rgba(0,245,212,0.03);">
                                <form action="{{ route('mini_maktab.material.test_sozlama', $m->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div
                                        style="display:grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap:12px;">

                                        <div>
                                            <label
                                                style="font-size:11px; color:var(--jd-muted,#9b97c0); font-weight:500;">Savol
                                                banki</label>
                                            <select name="bank_id" required class="soz-input" style="margin-top:4px;">
                                                @foreach ($banklar as $bank)
                                                    <option value="{{ $bank->id }}"
                                                        {{ $m->bank_id == $bank->id ? 'selected' : '' }}>
                                                        {{ $bank->nomi }} ({{ $bank->questions_count }} savol)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label
                                                style="font-size:11px; color:var(--jd-muted,#9b97c0); font-weight:500;">Savollar
                                                soni</label>
                                            <input type="number" name="savollar_soni" min="1"
                                                value="{{ $m->savollar_soni }}" required class="soz-input"
                                                style="margin-top:4px;">
                                        </div>

                                        <div>
                                            <label
                                                style="font-size:11px; color:var(--jd-muted,#9b97c0); font-weight:500;">Vaqt
                                                (daqiqa)
                                            </label>
                                            <input type="number" name="vaqt_limit" min="1" max="180"
                                                value="{{ $m->vaqt_limit }}" required class="soz-input"
                                                style="margin-top:4px;">
                                        </div>

                                        <div>
                                            <label
                                                style="font-size:11px; color:var(--jd-muted,#9b97c0); font-weight:500;">Urinish</label>
                                            <input type="number" name="urinish" min="1" max="10"
                                                value="{{ $m->urinish }}" required class="soz-input"
                                                style="margin-top:4px;">
                                        </div>

                                        <div>
                                            <label
                                                style="font-size:11px; color:var(--jd-muted,#9b97c0); font-weight:500;">Savol
                                                balli</label>
                                            <input type="number" name="ball" min="1"
                                                value="{{ optional($m->bank?->questions->first())->ball ?? 1 }}"
                                                required class="soz-input" style="margin-top:4px;">
                                        </div>

                                        <div>
                                            <label
                                                style="font-size:11px; color:var(--jd-muted,#9b97c0); font-weight:500;">Boshlanish
                                                vaqti</label>
                                            <input type="datetime-local" name="boshlanish_vaqti"
                                                value="{{ $m->boshlanish_vaqti?->format('Y-m-d\TH:i') }}"
                                                class="soz-input" style="margin-top:4px;">
                                        </div>

                                        <div>
                                            <label
                                                style="font-size:11px; color:var(--jd-muted,#9b97c0); font-weight:500;">Tugash
                                                vaqti</label>
                                            <input type="datetime-local" name="tugash_vaqti"
                                                value="{{ $m->tugash_vaqti?->format('Y-m-d\TH:i') }}"
                                                class="soz-input" style="margin-top:4px;">
                                        </div>

                                    </div>

                                    <div style="margin-top:12px; text-align:right;">
                                        <button type="submit" class="ar-btn ar-btn-ok">
                                            <i class="bx bx-save"></i> Saqlash
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif

                        {{-- TOPSHIRIQ accordion: talabalar + baholash --}}
                        @if ($m->tur === 'topshiriq')
                            @php
                                $topMap = $topshiriqlarByMaterial[$m->id] ?? collect();
                            @endphp

                            <button type="button" class="ms-acc-toggle" onclick="toggleAcc(this)"
                                style="width:100%; display:flex; align-items:center; justify-content:space-between;
                                       padding:12px 16px; background:rgba(123,44,191,0.08); border:none;
                                       cursor:pointer; font-size:13px; color:var(--jd-ink,#e8e6ff);">
                                <span style="display:flex; align-items:center; gap:8px;">
                                    <i class="bx bx-group" style="color:#c084fc;"></i>
                                    Talabalar baholash
                                    <span style="font-size:11px; color:var(--jd-muted,#9b97c0);">
                                        ({{ ($talabalarList ?? collect())->count() }} ta)
                                    </span>
                                </span>
                                <i class="bx bx-chevron-down ms-acc-arrow"
                                    style="transition:transform .25s; color:var(--jd-muted,#9b97c0);"></i>
                            </button>

                            <div class="ms-acc-body"
                                style="max-height:0; overflow:hidden; transition:max-height .35s ease;">
                                <form action="{{ route('mini_maktab.topshiriq.baholar', $m->id) }}" method="POST">
                                    @csrf

                                    <div class="arizalar-table-wrap"
                                        style="border-radius:0; border:none; box-shadow:none;">
                                        <table class="arizalar-table" style="min-width:640px;">
                                            <thead>
                                                <tr>
                                                    <th>№</th>
                                                    <th>Talaba FIO</th>
                                                    <th>Talaba ID</th>
                                                    <th>Guruh</th>
                                                    <th>PDF</th>
                                                    <th>Status</th>
                                                    <th>Ball</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse(($talabalarList ?? collect()) as $i => $ariza)
                                                    @php
                                                        $t = $topMap->get($ariza->user_id);
                                                        $fio =
                                                            $ariza->user['To‘liq_ismi'] ?? ($ariza->user->name ?? '—');
                                                        $pdfUrl = null;
                                                        if ($t && $t->pdf_path) {
                                                            $pdfUrl = method_exists($t, 'pdfUrl')
                                                                ? $t->pdfUrl()
                                                                : asset('storage/' . $t->pdf_path);
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td class="ar-id">{{ $i + 1 }}</td>
                                                        <td>
                                                            <div style="display:flex; align-items:center; gap:8px;">
                                                                <div class="ar-avatar"
                                                                    style="width:28px;height:28px;font-size:10px;">
                                                                    {{ mb_substr($fio, 0, 2) }}
                                                                </div>
                                                                <span
                                                                    style="font-weight:500;">{{ $fio }}</span>
                                                            </div>
                                                        </td>
                                                        <td style="text-align:center;">
                                                            {{ $ariza->user->Talaba_ID ?? ' ' }}
                                                        </td>
                                                        <td style="text-align:center;">
                                                            {{ $ariza->user->Guruh ?? '—' }}</td>
                                                        <td>
                                                            @if ($pdfUrl)
                                                                <a href="{{ $pdfUrl }}" target="_blank"
                                                                    class="ar-btn"
                                                                    style="font-size:11px; padding:5px 10px;">
                                                                    <i class="bx bx-download"></i>
                                                                    {{ \Illuminate\Support\Str::limit(str_replace(' ', '_', $fio), 14) }}_pdf
                                                                </a>
                                                            @else
                                                                <span
                                                                    style="font-size:12px; color:var(--jd-muted,#6b6890);">—</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($t && $t->pdf_path)
                                                                <span class="ar-badge ar-badge-ok">Topshirilgan</span>
                                                            @else
                                                                <span
                                                                    class="ar-badge ar-badge-muted">Topshirilmagan</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="number"
                                                                name="ballar[{{ $ariza->user_id }}]"
                                                                value="{{ $t->ball ?? '' }}" min="0"
                                                                max="100" step="0.1" placeholder="—"
                                                                class="soz-input"
                                                                style="width:90px; padding:6px 8px; text-align:center; font-size:13px;">
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6"
                                                            style="padding:24px; text-align:center; color:var(--jd-muted,#9b97c0);">
                                                            Bu fanga ariza topshirgan talabalar yo'q.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    @if (($talabalarList ?? collect())->isNotEmpty())
                                        <div
                                            style="padding:12px 16px; text-align:right;
                                             border-top:1px solid rgba(124,92,255,0.12);">
                                            <button type="submit" class="ar-btn ar-btn-ok" style="gap:6px;">
                                                <i class="bx bx-save"></i> Baholarni saqlash
                                            </button>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif

    </div>

    {{-- ══════════════════════════════════════════
         MODALLAR
    ══════════════════════════════════════════ --}}

    {{-- TEST MODAL --}}
    <div id="modal-test" class="ms-modal-overlay">
        <div class="ms-modal-box" style="max-width:520px; max-height:90vh; overflow-y:auto;">
            <div class="ms-modal-title">
                <i class="bx bx-clipboard" style="color:#00f5d4;"></i> Test qo'shish
            </div>

            <form action="{{ route('mini_maktab.material.qosh', $mavzu->id) }}" method="POST">
                @csrf
                <input type="hidden" name="tur" value="test">

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div style="grid-column:1/-1;">
                        <label class="ms-label">Test nomi</label>
                        <input type="text" name="nomi" required placeholder="Masalan: 1-mavzu testi"
                            class="ms-input">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="ms-label">Savol banki</label>
                        <select name="bank_id" required class="ms-input">
                            <option value="">— Tanlang —</option>
                            @foreach ($banklar as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->nomi }} - ({{ $bank->tur }}) -
                                    ({{ $bank->questions_count }} savol)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="ms-label">Savollar soni</label>
                        <input type="number" name="savollar_soni" min="1" value="20" required
                            class="ms-input">
                    </div>
                    <div>
                        <label class="ms-label">Vaqt (daqiqa)</label>
                        <input type="number" name="vaqt_limit" min="1" max="180" value="30"
                            required class="ms-input">
                    </div>
                    <div>
                        <label class="ms-label">Urinish soni</label>
                        <input type="number" name="urinish" min="1" max="10" value="1" required
                            class="ms-input">
                    </div>
                    <div>
                        <label class="ms-label">Savol balli</label>
                        <input type="number" name="ball" min="1" value="1" required
                            class="ms-input">
                    </div>
                    <div>
                        <label class="ms-label">Boshlanish vaqti</label>
                        <input type="datetime-local" name="boshlanish_vaqti" class="ms-input">
                    </div>
                    <div>
                        <label class="ms-label">Tugash vaqti</label>
                        <input type="datetime-local" name="tugash_vaqti" class="ms-input">
                    </div>
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="button" onclick="closeModal('test')" class="ms-btn-cancel">Bekor</button>
                    <button type="submit" class="ms-btn-ok"><i class="bx bx-plus"></i> Qo'shish</button>
                </div>
            </form>
        </div>
    </div>

    {{-- VIDEO MODAL --}}
    <div id="modal-video" class="ms-modal-overlay">
        <div class="ms-modal-box" style="max-width:440px;">
            <div class="ms-modal-title">
                <i class="bx bx-video" style="color:#ff5c8a;"></i> Video darslik qo'shish
            </div>

            <form action="{{ route('mini_maktab.material.qosh', $mavzu->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tur" value="video">

                <div style="margin-bottom:14px;">
                    <label class="ms-label">Video nomi</label>
                    <input type="text" name="nomi" required placeholder="Masalan: 1-dars kirish"
                        class="ms-input">
                </div>

                <div style="margin-bottom:14px;">
                    <label class="ms-label">Video fayl (MP4, MOV, AVI, WEBM — max 500MB)</label>
                    <div id="video-drop-zone" class="ms-dropzone"
                        onclick="document.getElementById('video-file-input').click()"
                        ondragover="event.preventDefault(); this.classList.add('ms-drop-over');"
                        ondragleave="this.classList.remove('ms-drop-over');" ondrop="handleFileDrop(event,'video')">
                        <i class="bx bx-cloud-upload"
                            style="font-size:32px; color:#00f5d4; display:block; margin-bottom:6px;"></i>
                        <span id="video-drop-label" style="font-size:13px; color:var(--jd-muted,#9b97c0);">
                            Faylni bu yerga tashlang yoki <b style="color:#00f5d4;">tanlang</b>
                        </span>
                    </div>
                    <input type="file" id="video-file-input" name="video"
                        accept="video/mp4,video/quicktime,video/x-msvideo,video/webm" style="display:none;"
                        onchange="showFileName(this,'video-drop-label')">
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="button" onclick="closeModal('video')" class="ms-btn-cancel">Bekor</button>
                    <button type="submit" class="ms-btn-ok"
                        style="background:linear-gradient(135deg,#ff5c8a,#7b2cbf);">
                        <i class="bx bx-upload"></i> Yuklash
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- PDF MODAL --}}
    <div id="modal-pdf" class="ms-modal-overlay">
        <div class="ms-modal-box" style="max-width:440px;">
            <div class="ms-modal-title">
                <i class="bx bx-file-pdf" style="color:#ff5c8a;"></i> PDF maruza qo'shish
            </div>

            <form action="{{ route('mini_maktab.material.qosh', $mavzu->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tur" value="pdf">

                <div style="margin-bottom:14px;">
                    <label class="ms-label">Maruza nomi</label>
                    <input type="text" name="nomi" required placeholder="Masalan: 1-mavzu maruzasi"
                        class="ms-input">
                </div>

                <div style="margin-bottom:14px;">
                    <label class="ms-label">PDF fayl (max 50MB)</label>
                    <div id="pdf-drop-zone" class="ms-dropzone"
                        onclick="document.getElementById('pdf-file-input').click()"
                        ondragover="event.preventDefault(); this.classList.add('ms-drop-over');"
                        ondragleave="this.classList.remove('ms-drop-over');" ondrop="handleFileDrop(event,'pdf')">
                        <i class="bx bx-file-pdf"
                            style="font-size:32px; color:#ff5c8a; display:block; margin-bottom:6px;"></i>
                        <span id="pdf-drop-label" style="font-size:13px; color:var(--jd-muted,#9b97c0);">
                            Faylni bu yerga tashlang yoki <b style="color:#ff5c8a;">tanlang</b>
                        </span>
                    </div>
                    <input type="file" id="pdf-file-input" name="pdf" accept="application/pdf"
                        style="display:none;" onchange="showFileName(this,'pdf-drop-label')">
                </div>

                <div style="margin-bottom:14px;">
                    <label class="ms-label">Sahifalar soni (ixtiyoriy)</label>
                    <input type="number" name="pdf_sahifalar" min="1" class="ms-input"
                        placeholder="Masalan: 24">
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="button" onclick="closeModal('pdf')" class="ms-btn-cancel">Bekor</button>
                    <button type="submit" class="ms-btn-ok"
                        style="background:linear-gradient(135deg,#ff5c8a,#7b2cbf);">
                        <i class="bx bx-upload"></i> Yuklash
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TOPSHIRIQ MODAL --}}
    <div id="modal-topshiriq" class="ms-modal-overlay">
        <div class="ms-modal-box" style="max-width:440px;">
            <div class="ms-modal-title">
                <i class="bx bx-task" style="color:#c084fc;"></i> Topshiriq qo'shish
            </div>

            <form action="{{ route('mini_maktab.material.qosh', $mavzu->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tur" value="topshiriq">

                <div style="margin-bottom:14px;">
                    <label class="ms-label">Topshiriq nomi</label>
                    <input type="text" name="nomi" required placeholder="Masalan: 1-mavzu topshirig'i"
                        class="ms-input">
                </div>

                <div style="margin-bottom:14px;">
                    <label class="ms-label">Topshiriq PDF (max 50MB)</label>
                    <div id="topshiriq-drop-zone" class="ms-dropzone" style="border-color:rgba(123,44,191,0.45);"
                        onclick="document.getElementById('topshiriq-file-input').click()"
                        ondragover="event.preventDefault(); this.classList.add('ms-drop-over');"
                        ondragleave="this.classList.remove('ms-drop-over');"
                        ondrop="handleFileDrop(event,'topshiriq')">
                        <i class="bx bx-cloud-upload"
                            style="font-size:32px; color:#c084fc; display:block; margin-bottom:6px;"></i>
                        <span id="topshiriq-drop-label" style="font-size:13px; color:var(--jd-muted,#9b97c0);">
                            Faylni bu yerga tashlang yoki <b style="color:#c084fc;">tanlang</b>
                        </span>
                    </div>
                    <input type="file" id="topshiriq-file-input" name="pdf" accept="application/pdf"
                        style="display:none;" onchange="showFileName(this,'topshiriq-drop-label')">
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="button" onclick="closeModal('topshiriq')" class="ms-btn-cancel">Bekor</button>
                    <button type="submit" class="ms-btn-ok"
                        style="background:linear-gradient(135deg,#00f5d4,#7b2cbf); color:#0a0818;">
                        <i class="bx bx-upload"></i> Yuklash
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SHARED STYLES --}}
    <style>
        .ms-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--jd-muted, #9b97c0);
            display: block;
            margin-bottom: 5px;
        }

        .ms-input {
            width: 100%;
            padding: 8px 11px;
            border: 1px solid var(--jd-glass-border, rgba(124, 92, 255, 0.28));
            border-radius: 8px;
            font-size: 13px;
            box-sizing: border-box;
            outline: none;
            background: var(--jd-glass-soft, rgba(22, 18, 48, 0.5));
            color: var(--jd-ink, #e8e6ff);
            transition: border .15s;
        }

        .ms-input:focus {
            border-color: #00f5d4;
            box-shadow: 0 0 0 3px rgba(0, 245, 212, 0.12);
        }

        .ms-btn-cancel {
            flex: 1;
            padding: 10px;
            border: 1px solid var(--jd-glass-border, rgba(124, 92, 255, 0.28));
            border-radius: 8px;
            background: transparent;
            color: var(--jd-ink, #e8e6ff);
            cursor: pointer;
            font-size: 13px;
        }

        .ms-btn-ok {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(0, 245, 212, 0.9), rgba(123, 44, 191, 0.9));
            color: #0a0818;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .ms-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(5, 4, 15, 0.7);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .ms-modal-box {
            background: rgba(28, 24, 56, 0.95);
            border: 1px solid rgba(124, 92, 255, 0.28);
            border-radius: 16px;
            padding: 28px;
            width: 100%;
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.5);
        }

        .ms-modal-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--jd-ink, #e8e6ff);
        }

        .ms-dropzone {
            border: 2px dashed rgba(0, 245, 212, 0.4);
            border-radius: 12px;
            padding: 28px 16px;
            text-align: center;
            cursor: pointer;
            background: rgba(0, 245, 212, 0.06);
            transition: .2s;
        }

        .ms-dropzone.ms-drop-over {
            background: rgba(0, 245, 212, 0.14);
        }

        .ms-acc-body.ms-acc-open {
            max-height: 3000px !important;
        }

        .ms-acc-arrow.ms-acc-rot {
            transform: rotate(180deg);
        }
    </style>

    <script>
        function openModal(tur) {
            document.getElementById('modal-' + tur).style.display = 'flex';
        }

        function closeModal(tur) {
            document.getElementById('modal-' + tur).style.display = 'none';
        }

        function toggleAcc(btn) {
            var body = btn.nextElementSibling;
            body.classList.toggle('ms-acc-open');
            btn.querySelector('.ms-acc-arrow').classList.toggle('ms-acc-rot');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                ['test', 'video', 'pdf', 'topshiriq'].forEach(closeModal);
            }
        });

        ['modal-test', 'modal-video', 'modal-pdf', 'modal-topshiriq'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('click', function(e) {
                    if (e.target === this) closeModal(id.replace('modal-', ''));
                });
            }
        });

        function showFileName(input, labelId) {
            var label = document.getElementById(labelId);
            if (input.files && input.files[0]) {
                var f = input.files[0];
                var mb = (f.size / 1048576).toFixed(1);
                label.innerHTML = '✔ <b>' + f.name + '</b> (' + mb + ' MB)';
            }
        }

        function handleFileDrop(event, type) {
            event.preventDefault();
            var zone = document.getElementById(type + '-drop-zone');
            var input = document.getElementById(type + '-file-input');
            zone.classList.remove('ms-drop-over');

            var dt = event.dataTransfer;
            if (dt.files && dt.files[0]) {
                var dT = new DataTransfer();
                dT.items.add(dt.files[0]);
                input.files = dT.files;
                showFileName(input, type + '-drop-label');
            }
        }

        @if (old('tur'))
            openModal('{{ old('tur') }}');
        @endif
    </script>

</x-layouts.sidebar>
