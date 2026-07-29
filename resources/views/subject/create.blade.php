<x-layouts.sidebar>
    <x-slot:title>Fan yaratish</x-slot:title>

    <div class="oz-wrap" style="max-width:680px;">

        <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
            <a href="{{ route('subject.index') }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i>
            </a>
            <div class="oz-title" style="margin:0;">Yangi fan qo'shish</div>
        </div>

        <form action="{{ route('subject.store') }}" method="POST">
            @csrf

            <div
                style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:20px; margin-bottom:12px;">
                <p style="font-size:13px; font-weight:600; color:#333; margin:0 0 16px;">
                    <i class="bx bx-book" style="color:#3C3489;"></i> Fan ma'lumotlari
                </p>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">

                    <div style="grid-column:1/-1;">
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Fan nomi</label>
                        <input type="text" name="nomi" class="arizalar-search" style="width:100%;"
                            placeholder="Masalan: Matematika..." value="{{ old('nomi') }}" required>
                    </div>

                    <div style="grid-column:1/-1;">
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Ta'lim tili</label>
                        <input type="text" name="talim_tili" class="arizalar-search" style="width:100%;"
                            placeholder="Ta'lim tilini kiriting..." value="{{ old('talim_tili') }}" required>
                    </div>

                    {{-- KAFEDRA: qidiruvli dropdown --}}
                    <div style="position:relative;">
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Kafedra</label>
                        <div style="position:relative;">
                            <i class="bx bx-search"
                                style="position:absolute; left:10px; top:50%;
                                transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                            <input type="text" id="kafedra_search" class="arizalar-search"
                                style="width:100%; padding-left:34px;" placeholder="Qidirish..." autocomplete="off">
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
                    <div style="position:relative;">
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Fakultet</label>
                        <div style="position:relative;">
                            <i class="bx bx-search"
                                style="position:absolute; left:10px; top:50%;
                                transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                            <input type="text" id="fakultet_search" class="arizalar-search"
                                style="width:100%; padding-left:34px;" placeholder="Qidirish..." autocomplete="off">
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
                    <div style="position:relative;">
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">O'quv yili</label>
                        <div style="position:relative;">
                            <i class="bx bx-search"
                                style="position:absolute; left:10px; top:50%;
                                transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                            <input type="text" id="oquv_yili_search" class="arizalar-search"
                                style="width:100%; padding-left:34px;" placeholder="Qidirish..." autocomplete="off">
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
                    <div style="position:relative;">
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Yo'nalish</label>
                        <div style="position:relative;">
                            <i class="bx bx-search"
                                style="position:absolute; left:10px; top:50%;
                                transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                            <input type="text" id="category_search" class="arizalar-search"
                                style="width:100%; padding-left:34px;" placeholder="Qidirish..." autocomplete="off">
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
                    <div style="position:relative;">
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Dars turi</label>
                        <div style="position:relative;">
                            <i class="bx bx-search"
                                style="position:absolute; left:10px; top:50%;
                                transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                            <input type="text" id="lesson_type_search" class="arizalar-search"
                                style="width:100%; padding-left:34px;" placeholder="Qidirish..." autocomplete="off">
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

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Semestr</label>
                        <input type="number" name="semster" class="arizalar-search" style="width:100%;"
                            placeholder="1-8" min="1" max="8" value="{{ old('semster') }}" required>
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Kredit</label>
                        <input type="number" name="kredit" class="arizalar-search" style="width:100%;"
                            placeholder="1-10" min="1" max="10" value="{{ old('kredit') }}" required>
                    </div>

                    <div style="position:relative;">
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">O'qituvchi</label>
                        <div style="position:relative;">
                            <i class="bx bx-search"
                                style="position:absolute; left:10px; top:50%;
                                transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                            <input type="text" id="teacher_search" class="arizalar-search"
                                style="width:100%; padding-left:34px;" placeholder="ID yoki ismni yozing..."
                                autocomplete="off">
                        </div>

                        <div id="teacher_results" class="search-dropdown" style="width:280px;">
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

                        <input type="hidden" name="teacher_id" id="hidden_teacher_id">
                    </div>

                </div>
            </div>

            <button type="submit" class="ar-btn ar-btn-ok"
                style="width:100%; justify-content:center; padding:10px;">
                <i class="bx bx-save"></i> Saqlash
            </button>

        </form>
    </div>

    <style>
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
