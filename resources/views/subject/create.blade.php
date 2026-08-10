<x-layouts.sidebar>
    <x-slot:title>Fan yaratish</x-slot:title>

    <div class="sf-wrap">

        <div class="sf-header">
            <a href="{{ route('subject.index') }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i>
            </a>
            <div>
                <div class="oz-title" style="margin:0;">Yangi fan qo'shish</div>
                <div class="sf-subtitle">Fan haqida barcha ma'lumotlarni to'ldiring</div>
            </div>
        </div>

        <form action="{{ route('subject.store') }}" method="POST">
            @csrf

            <div class="sf-card">
                <p class="sf-section-title">
                    <i class="bx bx-user"></i> O'qituvchi
                </p>

                <div class="sf-grid sf-grid-1" style="max-width:420px;">
                    <div class="sf-field">
                        <label class="sf-label">O'qituvchi</label>
                        <div style="position:relative;">
                            <i class="bx bx-search sf-search-icon"></i>
                            <input type="text" id="teacher_search" class="arizalar-search sf-search-input"
                                placeholder="ID yoki ismni yozing..." autocomplete="off">
                        </div>

                        <div id="teacher_results" class="search-dropdown" style="width:280px;">
                            @foreach ($teachers as $teacher)
                                <div class="search-item" data-id="{{ $teacher->id }}"
                                    data-name="{{ $teacher['To‘liq_ismi'] }}">
                                    <span class="sf-teacher-badge">
                                        #{{ $teacher->id }}
                                    </span>
                                    <span style="font-size:13px; color:#333; font-weight:500;">
                                        {{ $teacher['To‘liq_ismi'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" name="teacher_id" id="hidden_teacher_id">
                    </div>
                </div>
            </div>

            <div class="sf-card">
                <p class="sf-section-title">
                    <i class="bx bx-book"></i> Fan ma'lumotlari
                </p>

                <div class="sf-grid sf-grid-2">
                    <div>
                        <label class="sf-label">Fan nomi</label>
                        <input type="text" name="nomi" class="arizalar-search" style="width:100%;"
                            placeholder="Masalan: Matematika..." value="{{ old('nomi') }}" required>
                    </div>

                    <div>
                        <label class="sf-label">Ta'lim tili</label>
                        <input type="text" name="talim_tili" class="arizalar-search" style="width:100%;"
                            placeholder="Ta'lim tilini kiriting..." value="{{ old('talim_tili') }}" required>
                    </div>
                </div>
            </div>

            <div class="sf-card">
                <p class="sf-section-title">
                    <i class="bx bx-sitemap"></i> Tashkiliy ma'lumotlar
                </p>

                <div class="sf-grid sf-grid-3">

                    {{-- KAFEDRA: qidiruvli dropdown --}}
                    <div class="sf-field">
                        <label class="sf-label">Kafedra</label>
                        <div style="position:relative;">
                            <i class="bx bx-search sf-search-icon"></i>
                            <input type="text" id="kafedra_search" class="arizalar-search sf-search-input"
                                placeholder="Qidirish..." autocomplete="off">
                        </div>

                        <div id="kafedra_results" class="search-dropdown">
                            @foreach ($kafedralar as $kafedra)
                                <div class="search-item" data-id="{{ $kafedra->id }}" data-name="{{ $kafedra->nomi }}"
                                    data-fakultet="{{ $kafedra->fakultet_id }}">
                                    {{ $kafedra->nomi }}
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" name="kafedra_id" id="hidden_kafedra_id" value="{{ old('kafedra_id') }}">
                    </div>

                    {{-- FAKULTET: qidiruvli dropdown --}}
                    <div class="sf-field">
                        <label class="sf-label">Fakultet</label>
                        <div style="position:relative;">
                            <i class="bx bx-search sf-search-icon"></i>
                            <input type="text" id="fakultet_search" class="arizalar-search sf-search-input"
                                placeholder="Qidirish..." autocomplete="off">
                        </div>

                        <div id="fakultet_results" class="search-dropdown">
                            @foreach ($fakultetlar as $fakultet)
                                <div class="search-item" data-id="{{ $fakultet->id }}"
                                    data-name="{{ $fakultet->nomi }}">
                                    {{ $fakultet->nomi }}
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" name="fakultet_id" id="hidden_fakultet_id"
                            value="{{ old('fakultet_id') }}">
                    </div>

                    {{-- O'quv yili: qidiruvli dropdown --}}
                    <div class="sf-field">
                        <label class="sf-label">O'quv yili</label>
                        <div style="position:relative;">
                            <i class="bx bx-search sf-search-icon"></i>
                            <input type="text" id="oquv_yili_search" class="arizalar-search sf-search-input"
                                placeholder="Qidirish..." autocomplete="off">
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
                            value="{{ old('oquv_yili_id') }}">
                    </div>

                    {{-- category: qidiruvli dropdown --}}
                    <div class="sf-field">
                        <label class="sf-label">Yo'nalish</label>
                        <div style="position:relative;">
                            <i class="bx bx-search sf-search-icon"></i>
                            <input type="text" id="category_search" class="arizalar-search sf-search-input"
                                placeholder="Qidirish..." autocomplete="off">
                        </div>

                        <div id="category_results" class="search-dropdown">
                            @foreach ($categories as $category)
                                <div class="search-item" data-id="{{ $category->id }}"
                                    data-name="{{ $category->nomi }}">
                                    {{ $category->nomi }}
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" name="category_id" id="hidden_category_id"
                            value="{{ old('category_id') }}">
                    </div>

                    {{-- lesson type: qidiruvli dropdown --}}
                    <div class="sf-field">
                        <label class="sf-label">Dars turi</label>
                        <div style="position:relative;">
                            <i class="bx bx-search sf-search-icon"></i>
                            <input type="text" id="lesson_type_search" class="arizalar-search sf-search-input"
                                placeholder="Qidirish..." autocomplete="off">
                        </div>

                        <div id="lesson_type_results" class="search-dropdown">
                            @foreach ($lesson_types as $lesson_type)
                                <div class="search-item" data-id="{{ $lesson_type->id }}"
                                    data-name="{{ $lesson_type->nomi }}">
                                    {{ $lesson_type->nomi }}
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" name="lesson_type_id" id="hidden_lesson_type_id"
                            value="{{ old('lesson_type_id') }}">
                    </div>

                    <div class="sf-field">
                        <label class="sf-label">Semestr</label>
                        <input type="number" name="semster" class="arizalar-search" style="width:100%;"
                            placeholder="1-8" min="1" max="8" value="{{ old('semster') }}" required>
                    </div>

                    <div class="sf-field">
                        <label class="sf-label">Kredit</label>
                        <input type="number" name="kredit" class="arizalar-search" style="width:100%;"
                            placeholder="1-10" min="1" max="10" value="{{ old('kredit') }}" required>
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

        .sf-grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        @media (max-width: 1100px) {
            .sf-grid-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .sf-grid-2,
            .sf-grid-3 {
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

        .sf-teacher-badge {
            background: #EEEDFE;
            color: #3C3489;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
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
        // Har bir qidiruvli dropdown uchun umumiy funksiya:
        // searchId    - matn kiritish maydoni id'si
        // resultsId   - natijalar ro'yxati (dropdown) id'si
        // hiddenId    - yashirin input (haqiqiy qiymat yuboriladigan) id'si
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

                    // Agar kafedra tanlangan bo'lsa fakultetni avtomatik tanlash
                    if (searchId === 'kafedra_search') {

                        let fakultetId = this.dataset.fakultet;

                        let fakultetItem = document.querySelector(
                            '#fakultet_results .search-item[data-id="' + fakultetId + '"]'
                        );

                        if (fakultetItem) {
                            document.getElementById('fakultet_search').value =
                                fakultetItem.dataset.name;

                            document.getElementById('hidden_fakultet_id').value =
                                fakultetItem.dataset.id;
                        }
                    }
                });
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.style.display = 'none';
                }
            });
        }

        initSearchSelect('teacher_search', 'teacher_results', 'hidden_teacher_id');
        initSearchSelect('kafedra_search', 'kafedra_results', 'hidden_kafedra_id');
        initSearchSelect('fakultet_search', 'fakultet_results', 'hidden_fakultet_id');
        initSearchSelect('oquv_yili_search', 'oquv_yili_results', 'hidden_oquv_yili_id');
        initSearchSelect('category_search', 'category_results', 'hidden_category_id');
        initSearchSelect('lesson_type_search', 'lesson_type_results', 'hidden_lesson_type_id');
    </script>

</x-layouts.sidebar>