<x-layouts.sidebar>
    <x-slot:title>{{ $mavzu->nomi }}</x-slot:title>

    <div class="oz-wrap">

        {{-- HEADER --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <a href="{{ route('mini_maktab.mavzular', [$bolim->id, $subject->id]) }}" class="ar-btn">
                    <i class="bx bx-arrow-back"></i>
                </a>
                <div>
                    @php
                        $turRangi = match($mavzu->tur) {
                            'mavzu'   => ['bg' => '#EEEDFE', 'txt' => '#3C3489', 'label' => 'Mavzu'],
                            'oraliq'  => ['bg' => '#fff3cd', 'txt' => '#856404', 'label' => 'Oraliq'],
                            'yakuniy' => ['bg' => '#d1fae5', 'txt' => '#065f46', 'label' => 'Yakuniy'],
                            default   => ['bg' => '#f0f0f0', 'txt' => '#444',    'label' => ucfirst($mavzu->tur)],
                        };
                    @endphp
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:11px; font-weight:600; padding:3px 9px; border-radius:20px;
                            background:{{ $turRangi['bg'] }}; color:{{ $turRangi['txt'] }};">
                            {{ $turRangi['label'] }}
                        </span>
                        <div class="oz-title" style="margin:0;">{{ $mavzu->nomi }}</div>
                    </div>
                    <div style="font-size:12px; color:#888; margin-top:2px;">
                        {{ $subject->nomi }} · {{ $bolim->nomi }}
                    </div>
                </div>
            </div>

            {{-- Material qo'shish tugmalari --}}
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <button onclick="openModal('test')"
                    style="display:flex; align-items:center; gap:6px; padding:8px 14px;
                           border-radius:8px; border:none; cursor:pointer; font-size:13px;
                           background:#EEEDFE; color:#3C3489; font-weight:500;">
                    <i class="bx bx-clipboard"></i> Test qo'shish
                </button>
                <button onclick="openModal('video')"
                    style="display:flex; align-items:center; gap:6px; padding:8px 14px;
                           border-radius:8px; border:none; cursor:pointer; font-size:13px;
                           background:#fce7f3; color:#9d174d; font-weight:500;">
                    <i class="bx bx-video"></i> Video qo'shish
                </button>
                <button onclick="openModal('pdf')"
                    style="display:flex; align-items:center; gap:6px; padding:8px 14px;
                           border-radius:8px; border:none; cursor:pointer; font-size:13px;
                           background:#fee2e2; color:#b91c1c; font-weight:500;">
                    <i class="bx bx-file-pdf"></i> PDF qo'shish
                </button>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             MATERIALLAR RO'YXATI
        ══════════════════════════════════════════ --}}
        @if($materiallar->isEmpty())
            <div style="text-align:center; padding:48px 20px; background:#fafafa;
                 border:1px dashed #e0e0e0; border-radius:12px; color:#bbb; font-size:14px;">
                <i class="bx bx-folder-open" style="font-size:40px; margin-bottom:10px; display:block;"></i>
                Hozircha material yo'q. Yuqoridagi tugmalardan birini bosing.
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:12px;">
                @foreach($materiallar as $m)
                    <div style="background:#fff; border:1px solid #eee; border-radius:12px; overflow:hidden;">

                        {{-- Material sarlavhasi --}}
                        <div style="display:flex; align-items:center; justify-content:space-between;
                             padding:14px 16px; border-bottom:{{ $m->tur === 'test' ? '1px solid #f0f0f0' : 'none' }};">
                            <div style="display:flex; align-items:center; gap:10px;">
                                @php
                                    $mRangi = match($m->tur) {
                                        'test'  => ['bg' => '#EEEDFE', 'txt' => '#3C3489', 'icon' => 'bx-clipboard', 'label' => 'TEST'],
                                        'video' => ['bg' => '#fce7f3', 'txt' => '#9d174d', 'icon' => 'bx-video',     'label' => 'VIDEO'],
                                        'pdf'   => ['bg' => '#fee2e2', 'txt' => '#b91c1c', 'icon' => 'bx-file-pdf',  'label' => 'PDF'],
                                        default => ['bg' => '#f0f0f0', 'txt' => '#444',    'icon' => 'bx-file',      'label' => strtoupper($m->tur)],
                                    };
                                @endphp
                                <div style="width:36px; height:36px; border-radius:8px; background:{{ $mRangi['bg'] }};
                                     display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <i class="bx {{ $mRangi['icon'] }}" style="font-size:17px; color:{{ $mRangi['txt'] }};"></i>
                                </div>
                                <div>
                                    <div style="font-size:14px; font-weight:600;">{{ $m->nomi }}</div>
                                    <div style="font-size:11px; font-weight:600; color:{{ $mRangi['txt'] }};">
                                        {{ $mRangi['label'] }}
                                        @if($m->tur === 'video' && $m->video_size) · {{ $m->video_size }} @endif
                                        @if($m->tur === 'pdf' && $m->pdf_size) · {{ $m->pdf_size }} @endif
                                        @if($m->tur === 'pdf' && $m->pdf_sahifalar) · {{ $m->pdf_sahifalar }} sahifa @endif
                                        @if($m->tur === 'test' && $m->bank) · {{ $m->bank->nomi }} @endif
                                    </div>
                                </div>
                            </div>

                            <div style="display:flex; align-items:center; gap:8px;">
                                {{-- Video / PDF yuklab olish --}}
                                @if($m->tur === 'video' && $m->videoUrl())
                                    <a href="{{ $m->videoUrl() }}" target="_blank" class="ar-btn" style="font-size:12px;">
                                        <i class="bx bx-play-circle"></i> Ko'rish
                                    </a>
                                @endif
                                @if($m->tur === 'pdf' && $m->pdfUrl())
                                    <a href="{{ $m->pdfUrl() }}" target="_blank" class="ar-btn" style="font-size:12px;">
                                        <i class="bx bx-show"></i> Ko'rish
                                    </a>
                                @endif

                                {{-- O'chirish --}}
                                <form action="{{ route('mini_maktab.material.ochir', $m->id) }}" method="POST"
                                    onsubmit="return confirm('Material o\'chirilsinmi?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ar-btn"
                                        style="background:#fee2e2; color:#b91c1c; border:none; cursor:pointer; font-size:12px;">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- TEST sozlamalari bo'limi --}}
                        @if($m->tur === 'test')
                            <div style="padding:14px 16px; background:#fafbff;">
                                <form action="{{ route('mini_maktab.material.test_sozlama', $m->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap:12px;">

                                        <div>
                                            <label style="font-size:11px; color:#888; font-weight:500;">Savol banki</label>
                                            <select name="bank_id" required
                                                style="width:100%; margin-top:4px; padding:7px 10px;
                                                       border:1px solid #e0e0e0; border-radius:7px; font-size:13px;">
                                                @foreach($banklar as $bank)
                                                    <option value="{{ $bank->id }}" {{ $m->bank_id == $bank->id ? 'selected' : '' }}>
                                                        {{ $bank->nomi }} ({{ $bank->questions_count }} savol)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label style="font-size:11px; color:#888; font-weight:500;">Savollar soni</label>
                                            <input type="number" name="savollar_soni" min="1"
                                                value="{{ $m->savollar_soni }}" required
                                                style="width:100%; margin-top:4px; padding:7px 10px;
                                                       border:1px solid #e0e0e0; border-radius:7px; font-size:13px; box-sizing:border-box;">
                                        </div>

                                        <div>
                                            <label style="font-size:11px; color:#888; font-weight:500;">Vaqt (daqiqa)</label>
                                            <input type="number" name="vaqt_limit" min="1" max="180"
                                                value="{{ $m->vaqt_limit }}" required
                                                style="width:100%; margin-top:4px; padding:7px 10px;
                                                       border:1px solid #e0e0e0; border-radius:7px; font-size:13px; box-sizing:border-box;">
                                        </div>

                                        <div>
                                            <label style="font-size:11px; color:#888; font-weight:500;">Urinish</label>
                                            <input type="number" name="urinish" min="1" max="10"
                                                value="{{ $m->urinish }}" required
                                                style="width:100%; margin-top:4px; padding:7px 10px;
                                                       border:1px solid #e0e0e0; border-radius:7px; font-size:13px; box-sizing:border-box;">
                                        </div>

                                        <div>
                                            <label style="font-size:11px; color:#888; font-weight:500;">Savol balli</label>
                                            <input type="number" name="ball" min="1"
                                                value="{{ optional($m->bank?->questions->first())->ball ?? 1 }}" required
                                                style="width:100%; margin-top:4px; padding:7px 10px;
                                                       border:1px solid #e0e0e0; border-radius:7px; font-size:13px; box-sizing:border-box;">
                                        </div>

                                        <div>
                                            <label style="font-size:11px; color:#888; font-weight:500;">Boshlanish vaqti</label>
                                            <input type="datetime-local" name="boshlanish_vaqti"
                                                value="{{ $m->boshlanish_vaqti?->format('Y-m-d\TH:i') }}"
                                                style="width:100%; margin-top:4px; padding:7px 10px;
                                                       border:1px solid #e0e0e0; border-radius:7px; font-size:13px; box-sizing:border-box;">
                                        </div>

                                        <div>
                                            <label style="font-size:11px; color:#888; font-weight:500;">Tugash vaqti</label>
                                            <input type="datetime-local" name="tugash_vaqti"
                                                value="{{ $m->tugash_vaqti?->format('Y-m-d\TH:i') }}"
                                                style="width:100%; margin-top:4px; padding:7px 10px;
                                                       border:1px solid #e0e0e0; border-radius:7px; font-size:13px; box-sizing:border-box;">
                                        </div>

                                    </div>

                                    <div style="margin-top:12px; text-align:right;">
                                        <button type="submit"
                                            style="padding:8px 20px; background:#3C3489; color:#fff;
                                                   border:none; border-radius:8px; cursor:pointer; font-size:13px;">
                                            <i class="bx bx-save"></i> Saqlash
                                        </button>
                                    </div>
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
    <div id="modal-test"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);
               z-index:9999; align-items:center; justify-content:center; padding:16px;">
        <div style="background:#fff; border-radius:14px; padding:28px; width:100%;
             max-width:520px; max-height:90vh; overflow-y:auto; box-shadow:0 8px 32px rgba(0,0,0,.18);">
            <div style="font-size:16px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <i class="bx bx-clipboard" style="color:#3C3489;"></i> Test qo'shish
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
                            @foreach($banklar as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->nomi }} - ({{ $bank->tur }}) - ({{ $bank->questions_count }} savol)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="ms-label">Savollar soni</label>
                        <input type="number" name="savollar_soni" min="1" value="20" required class="ms-input">
                    </div>
                    <div>
                        <label class="ms-label">Vaqt (daqiqa)</label>
                        <input type="number" name="vaqt_limit" min="1" max="180" value="30" required class="ms-input">
                    </div>
                    <div>
                        <label class="ms-label">Urinish soni</label>
                        <input type="number" name="urinish" min="1" max="10" value="1" required class="ms-input">
                    </div>
                    <div>
                        <label class="ms-label">Savol balli</label>
                        <input type="number" name="ball" min="1" value="1" required class="ms-input">
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
    <div id="modal-video"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);
               z-index:9999; align-items:center; justify-content:center; padding:16px;">
        <div style="background:#fff; border-radius:14px; padding:28px; width:100%;
             max-width:440px; box-shadow:0 8px 32px rgba(0,0,0,.18);">
            <div style="font-size:16px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <i class="bx bx-video" style="color:#9d174d;"></i> Video darslik qo'shish
            </div>

            <form action="{{ route('mini_maktab.material.qosh', $mavzu->id) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tur" value="video">

                <div style="margin-bottom:14px;">
                    <label class="ms-label">Video nomi</label>
                    <input type="text" name="nomi" required placeholder="Masalan: 1-dars kirish" class="ms-input">
                </div>

                <div style="margin-bottom:14px;">
                    <label class="ms-label">Video fayl (MP4, MOV, AVI, WEBM — max 500MB)</label>
                    <div id="video-drop-zone"
                        style="border:2px dashed #d0c9ff; border-radius:10px; padding:28px 16px;
                               text-align:center; cursor:pointer; background:#fafbff; transition:.2s;"
                        onclick="document.getElementById('video-file-input').click()"
                        ondragover="event.preventDefault(); this.style.background='#EEEDFE';"
                        ondragleave="this.style.background='#fafbff';"
                        ondrop="handleFileDrop(event,'video')">
                        <i class="bx bx-cloud-upload" style="font-size:32px; color:#3C3489; display:block; margin-bottom:6px;"></i>
                        <span id="video-drop-label" style="font-size:13px; color:#888;">
                            Faylni bu yerga tashlang yoki <b style="color:#3C3489;">tanlang</b>
                        </span>
                    </div>
                    <input type="file" id="video-file-input" name="video"
                           accept="video/mp4,video/quicktime,video/x-msvideo,video/webm"
                           style="display:none;" onchange="showFileName(this,'video-drop-label')">
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="button" onclick="closeModal('video')" class="ms-btn-cancel">Bekor</button>
                    <button type="submit" class="ms-btn-ok" style="background:#9d174d;">
                        <i class="bx bx-upload"></i> Yuklash
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- PDF MODAL --}}
    <div id="modal-pdf"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);
               z-index:9999; align-items:center; justify-content:center; padding:16px;">
        <div style="background:#fff; border-radius:14px; padding:28px; width:100%;
             max-width:440px; box-shadow:0 8px 32px rgba(0,0,0,.18);">
            <div style="font-size:16px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <i class="bx bx-file-pdf" style="color:#b91c1c;"></i> PDF maruza qo'shish
            </div>

            <form action="{{ route('mini_maktab.material.qosh', $mavzu->id) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tur" value="pdf">

                <div style="margin-bottom:14px;">
                    <label class="ms-label">Maruza nomi</label>
                    <input type="text" name="nomi" required placeholder="Masalan: 1-mavzu maruzasi" class="ms-input">
                </div>

                <div style="margin-bottom:14px;">
                    <label class="ms-label">PDF fayl (max 50MB)</label>
                    <div id="pdf-drop-zone"
                        style="border:2px dashed #fca5a5; border-radius:10px; padding:28px 16px;
                               text-align:center; cursor:pointer; background:#fff9f9; transition:.2s;"
                        onclick="document.getElementById('pdf-file-input').click()"
                        ondragover="event.preventDefault(); this.style.background='#fee2e2';"
                        ondragleave="this.style.background='#fff9f9';"
                        ondrop="handleFileDrop(event,'pdf')">
                        <i class="bx bx-file-pdf" style="font-size:32px; color:#b91c1c; display:block; margin-bottom:6px;"></i>
                        <span id="pdf-drop-label" style="font-size:13px; color:#888;">
                            Faylni bu yerga tashlang yoki <b style="color:#b91c1c;">tanlang</b>
                        </span>
                    </div>
                    <input type="file" id="pdf-file-input" name="pdf"
                           accept="application/pdf"
                           style="display:none;" onchange="showFileName(this,'pdf-drop-label')">
                </div>

                <div style="margin-bottom:14px;">
                    <label class="ms-label">Sahifalar soni (ixtiyoriy)</label>
                    <input type="number" name="pdf_sahifalar" min="1" class="ms-input" placeholder="Masalan: 24">
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="button" onclick="closeModal('pdf')" class="ms-btn-cancel">Bekor</button>
                    <button type="submit" class="ms-btn-ok" style="background:#b91c1c;">
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
            color: #666;
            display: block;
            margin-bottom: 5px;
        }
        .ms-input {
            width: 100%;
            padding: 8px 11px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 13px;
            box-sizing: border-box;
            outline: none;
            transition: border .15s;
        }
        .ms-input:focus { border-color: #3C3489; }
        .ms-btn-cancel {
            flex: 1;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            font-size: 13px;
        }
        .ms-btn-ok {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background: #3C3489;
            color: #fff;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
    </style>

    <script>
        function openModal(tur) {
            document.getElementById('modal-' + tur).style.display = 'flex';
        }
        function closeModal(tur) {
            document.getElementById('modal-' + tur).style.display = 'none';
        }

        // ESC bilan yopish
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                ['test', 'video', 'pdf'].forEach(closeModal);
            }
        });

        // Backdrop click bilan yopish
        ['modal-test', 'modal-video', 'modal-pdf'].forEach(function(id) {
            document.getElementById(id).addEventListener('click', function(e) {
                if (e.target === this) closeModal(id.replace('modal-', ''));
            });
        });

        // Fayl tanlanganda nomini ko'rsatish
        function showFileName(input, labelId) {
            const label = document.getElementById(labelId);
            if (input.files && input.files[0]) {
                const f = input.files[0];
                const mb = (f.size / 1048576).toFixed(1);
                label.innerHTML = '✔ <b>' + f.name + '</b> (' + mb + ' MB)';
            }
        }

        // Drag & drop
        function handleFileDrop(event, type) {
            event.preventDefault();
            const zone  = document.getElementById(type + '-drop-zone');
            const input = document.getElementById(type + '-file-input');
            const label = document.getElementById(type + '-drop-label');
            zone.style.background = type === 'video' ? '#fafbff' : '#fff9f9';

            const dt = event.dataTransfer;
            if (dt.files && dt.files[0]) {
                // Faylni input ga o'rnatish
                const dT = new DataTransfer();
                dT.items.add(dt.files[0]);
                input.files = dT.files;
                showFileName(input, type + '-drop-label');
            }
        }

        // Agar @old('tur') bo'lsa modal ni qayta ochish (validation xatosi bo'lganda)
        @if(old('tur'))
            openModal('{{ old('tur') }}');
        @endif
    </script>

</x-layouts.sidebar>