<x-layouts.sidebar>
    <x-slot:title>Mudirni tahrirlash</x-slot:title>

    <div class="sf-wrap">

        <div class="sf-header">
            <a href="{{ route('mudir.index') }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i>
            </a>
            <div>
                <div class="oz-title" style="margin:0;">Mudirni tahrirlash</div>
                <div class="sf-subtitle">Kafedra mudiri haqida ma'lumotlarni tahrirlang</div>
            </div>
        </div>

        @if ($errors->any())
            <div class="sf-card" style="border-color:#f7d3d3; background:#fdeaea; padding:16px 20px;">
                <ul style="color:#c0392b; font-size:13px; margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('mudir.update', $mudir->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="sf-card">
                <p class="sf-section-title">
                    <i class="bx bx-id-card"></i> Mudir ma'lumotlari
                </p>

                <div class="sf-grid sf-grid-1" style="max-width:420px;">
                    <div class="sf-field">
                        <label class="sf-label">Mudir F.I.Sh. (Familiya Ism, to'liq)</label>
                        <input type="text" name="mudir" id="mudir_input" class="arizalar-search" style="width:100%;"
                            placeholder="Masalan: Baxtiyorjonov Muhammadjavohir"
                            value="{{ old('mudir', $mudir->mudir) }}" required>
                        <div class="sf-hint">Vedomostda avtomatik shunday ko'rinadi: <b id="mudir_preview">—</b></div>
                    </div>
                </div>
            </div>

            <div class="sf-card">
                <p class="sf-section-title">
                    <i class="bx bx-sitemap"></i> Tashkiliy ma'lumotlar
                </p>

                <div class="sf-grid sf-grid-2">

                    {{-- KAFEDRA: qidiruvli dropdown, joriy qiymat bilan to'ldirilgan --}}
                    <div class="sf-field">
                        <label class="sf-label">Kafedra</label>
                        <div style="position:relative;">
                            <i class="bx bx-search sf-search-icon"></i>
                            <input type="text" id="kafedra_search" class="arizalar-search sf-search-input"
                                placeholder="Qidirish..." autocomplete="off"
                                value="{{ $mudir->kafedra->nomi ?? '' }}">
                        </div>

                        <div id="kafedra_results" class="search-dropdown">
                            @foreach ($kafedralar as $kafedra)
                                <div class="search-item" data-id="{{ $kafedra->id }}" data-name="{{ $kafedra->nomi }}">
                                    {{ $kafedra->nomi }}
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" name="kafedra_id" id="hidden_kafedra_id"
                            value="{{ old('kafedra_id', $mudir->kafedra_id) }}">
                    </div>

                    {{-- O'QUV YILI: qidiruvli dropdown, joriy qiymat bilan to'ldirilgan --}}
                    <div class="sf-field">
                        <label class="sf-label">O'quv yili</label>
                        <div style="position:relative;">
                            <i class="bx bx-search sf-search-icon"></i>
                            <input type="text" id="oquv_yili_search" class="arizalar-search sf-search-input"
                                placeholder="Qidirish..." autocomplete="off"
                                value="{{ $mudir->oquvYili->nomi ?? '' }}">
                        </div>

                        <div id="oquv_yili_results" class="search-dropdown">
                            @foreach ($oquv_yillari as $oquv_yili)
                                <div class="search-item" data-id="{{ $oquv_yili->id }}"
                                    data-name="{{ $oquv_yili->nomi }}">
                                    {{ $oquv_yili->nomi }}
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" name="oquv_yili_id" id="hidden_oquv_yili_id"
                            value="{{ old('oquv_yili_id', $mudir->oquv_yili_id) }}">
                    </div>

                </div>
            </div>

            <div class="sf-actions">
                <button type="submit" class="ar-btn ar-btn-ok sf-submit-btn">
                    <i class="bx bx-save"></i> Saqlash
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
            transition: box-shadow 0.2s;
        }

        .sf-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
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

        .sf-grid-1 {
            grid-template-columns: 1fr;
        }

        .sf-grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        @media (max-width: 640px) {
            .sf-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .sf-field {
            position: relative;
        }

        .sf-label {
            font-size: 12px;
            font-weight: 600;
            color: #888;
            display: block;
            margin-bottom: 6px;
        }

        .sf-hint {
            font-size: 11.5px;
            color: #aaa;
            margin-top: 6px;
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
            display: flex;
            justify-content: flex-end;
            margin-top: 4px;
        }

        .sf-submit-btn {
            padding: 11px 32px;
            font-weight: 600;
            border-radius: 10px;
        }

        @media (max-width: 640px) {
            .sf-actions {
                justify-content: stretch;
            }

            .sf-submit-btn {
                width: 100%;
                justify-content: center;
            }
        }

        .search-dropdown {
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

        .search-item {
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

        .search-item:hover {
            background: #EEEDFE;
        }
    </style>

    <script>
        // Har bir qidiruvli dropdown uchun umumiy funksiya (create.blade.php bilan bir xil mantiq)
        function initSearchSelect(searchId, resultsId, hiddenId) {
            const searchInput = document.getElementById(searchId);
            const resultsBox = document.getElementById(resultsId);
            const hiddenInput = document.getElementById(hiddenId);
            const items = resultsBox.querySelectorAll('.search-item');

            searchInput.addEventListener('input', function() {
                const val = this.value.toLowerCase().trim();
                let found = 0;

                if (val.length > 0) {
                    resultsBox.style.display = 'block';
                    items.forEach(item => {
                        const name = item.getAttribute('data-name').toLowerCase();
                        const id = item.getAttribute('data-id');
                        const show = name.includes(val) || id === val;
                        item.style.display = show ? 'flex' : 'none';
                        if (show) found++;
                    });
                    if (found === 0) resultsBox.style.display = 'none';
                } else {
                    resultsBox.style.display = 'none';
                    hiddenInput.value = '';
                }
            });

            searchInput.addEventListener('focus', function() {
                if (this.value.trim().length > 0) {
                    resultsBox.style.display = 'block';
                }
            });

            items.forEach(item => {
                item.addEventListener('click', function() {
                    searchInput.value = this.dataset.name;
                    hiddenInput.value = this.dataset.id;
                    resultsBox.style.display = 'none';
                    searchInput.style.borderColor = '#3C3489';
                });
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.style.display = 'none';
                }
            });
        }

        initSearchSelect('kafedra_search', 'kafedra_results', 'hidden_kafedra_id');
        initSearchSelect('oquv_yili_search', 'oquv_yili_results', 'hidden_oquv_yili_id');

        // "Mansur Ikramov" -> "M.Ikramov" - Mudir::formatSignature() bilan bir xil mantiq,
        // faqat foydalanuvchiga oldindan ko'rsatish uchun (haqiqiy formatlash backendda bo'ladi)
        (function() {
            const input = document.getElementById('mudir_input');
            const preview = document.getElementById('mudir_preview');
            if (!input || !preview) return;

            function updatePreview() {
                const val = input.value.trim();
                if (!val) {
                    preview.textContent = '—';
                    return;
                }
                const parts = val.split(/\s+/);
                if (parts.length < 2) {
                    preview.textContent = val;
                    return;
                }
                const familiya = parts[0];
                const ism = parts[1];
                preview.textContent = ism.charAt(0).toUpperCase() + '.' + familiya;
            }

            input.addEventListener('input', updatePreview);
            updatePreview();
        })();
    </script>

</x-layouts.sidebar>