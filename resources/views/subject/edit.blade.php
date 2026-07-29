<x-layouts.sidebar>
    <x-slot:title>Fanlar tahrirlash</x-slot:title>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm custom-card">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="mb-4 fw-bold text-dark border-start border-primary border-4 ps-3">Fanlar tahrirlash
                        </h2>

                        <form action="{{ route('subject.update', $subject->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="custom-label">Fan nomi</label>
                                    <input type="text" name="nomi" class="form-control custom-input"
                                        placeholder="Matematika..." required value="{{ $subject->nomi }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="custom-label">Yo'nalish (Kategoriya)</label>
                                    <select name="category_id" class="form-select custom-input" required>
                                        <option value="" disabled>Yo'nalishni tanlang</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ $subject->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->nomi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- O'QITUVCHI: qidiruvli dropdown, joriy qiymat bilan to'ldirilgan --}}
                                <div class="col-md-6 position-relative">
                                    <label class="custom-label">O'qituvchi</label>
                                    <input type="text" id="teacher_search" class="form-control custom-input"
                                        placeholder="ID yoki ismni yozing..." autocomplete="off"
                                        value="{{ $subject->teacher['To‘liq_ismi'] ?? '' }}">

                                    <div id="teacher_results" class="search-dropdown">
                                        @foreach ($teachers as $teacher)
                                            <div class="search-item" data-id="{{ $teacher->id }}"
                                                data-name="{{ $teacher['To‘liq_ismi'] }}">
                                                <span class="search-item-badge">#{{ $teacher->id }}</span>
                                                <span>{{ $teacher['To‘liq_ismi'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <input type="hidden" name="teacher_id" id="hidden_teacher_id"
                                        value="{{ $subject->teacher_id }}">
                                </div>

                                {{-- KAFEDRA: qidiruvli dropdown, joriy qiymat bilan to'ldirilgan --}}
                                <div class="col-md-6 position-relative">
                                    <label class="custom-label">Kafedra</label>
                                    <input type="text" id="kafedra_search" class="form-control custom-input"
                                        placeholder="Qidirish..." autocomplete="off"
                                        value="{{ $subject->kafedra->nomi ?? '' }}">

                                    <div id="kafedra_results" class="search-dropdown">
                                        @foreach ($kafedralar as $kafedra)
                                            <div class="search-item" data-id="{{ $kafedra->id }}"
                                                data-name="{{ $kafedra->nomi }}">
                                                {{ $kafedra->nomi }}
                                            </div>
                                        @endforeach
                                    </div>

                                    <input type="hidden" name="kafedra_id" id="hidden_kafedra_id"
                                        value="{{ $subject->kafedra_id }}">
                                </div>

                                {{-- FAKULTET: qidiruvli dropdown, joriy qiymat bilan to'ldirilgan --}}
                                <div class="col-md-6 position-relative">
                                    <label class="custom-label">Fakultet</label>
                                    <input type="text" id="fakultet_search" class="form-control custom-input"
                                        placeholder="Qidirish..." autocomplete="off"
                                        value="{{ $subject->fakultet->nomi ?? '' }}">

                                    <div id="fakultet_results" class="search-dropdown">
                                        @foreach ($fakultetlar as $fakultet)
                                            <div class="search-item" data-id="{{ $fakultet->id }}"
                                                data-name="{{ $fakultet->nomi }}">
                                                {{ $fakultet->nomi }}
                                            </div>
                                        @endforeach
                                    </div>

                                    <input type="hidden" name="fakultet_id" id="hidden_fakultet_id"
                                        value="{{ $subject->fakultet_id }}">
                                </div>

                                {{-- O'quv yili: qidiruvli dropdown, joriy qiymat bilan to'ldirilgan --}}
                                <div class="col-md-6 position-relative">
                                    <label class="custom-label">O'quv yili</label>
                                    <input type="text" id="oquv_yili_search" class="form-control custom-input"
                                        placeholder="Qidirish..." autocomplete="off"
                                        value="{{ $subject->oquv_yili->nomi ?? '' }}">

                                    <div id="oquv_yili_results" class="search-dropdown">
                                        @foreach ($oquv_yillari as $oquv_yili)
                                            <div class="search-item" data-id="{{ $oquv_yili->id }}"
                                                data-name="{{ $oquv_yili->nomi }}">
                                                {{ $oquv_yili->nomi }}
                                            </div>
                                        @endforeach
                                    </div>

                                    <input type="hidden" name="oquv_yili_id" id="hidden_oquv_yili_id"
                                        value="{{ $subject->oquv_yili_id }}">
                                </div>

                                {{-- Ta'lim tili: qidiruvli dropdown, joriy qiymat bilan to'ldirilgan --}}
                                <div class="col-md-6">
                                    <label class="custom-label">Ta'lim tili</label>
                                    <input type="text" name="talim_tili" class="form-control custom-input"
                                        placeholder="Ta'lim tilini kiriting..." required value="{{ $subject->talim_tili }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="custom-label">Semestr</label>
                                    <input type="number" name="semster" class="form-control custom-input"
                                        placeholder="1-8" min="1" max="8" required value="{{ $subject->semster }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="custom-label">Kredit</label>
                                    <input type="number" name="kredit" class="form-control custom-input"
                                        placeholder="1-10" min="1" max="10" required value="{{ $subject->kredit }}">
                                </div>

                                <div class="col-12 mt-5">
                                    <button type="submit"
                                        class="btn btn-primary px-5 py-2 fw-bold rounded-pill shadow-sm">
                                        <i class="fas fa-save me-2"></i> Fanini saqlash
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Kartochka stili */
        .custom-card {
            border-radius: 15px;
            background-color: #ffffff;
        }

        /* Label stili */
        .custom-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #6c757d;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Input va Select stili */
        .custom-input {
            background-color: #f8f9fa !important;
            border: 1px solid #e9ecef !important;
            color: #212529 !important;
            padding: 12px 15px !important;
            border-radius: 10px !important;
            transition: all 0.2s ease-in-out;
        }

        .custom-input:focus {
            background-color: #ffffff !important;
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
            color: #212529 !important;
        }

        select.custom-input option {
            color: #212529;
            background-color: #ffffff;
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3) !important;
        }

        /* Qidiruvli dropdown stili */
        .search-dropdown {
            display: none;
            position: absolute;
            background: #fff;
            border: 1px solid #e9ecef;
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
            font-size: 0.9rem;
            color: #212529;
        }

        .search-item:hover {
            background: #eef4ff;
        }

        .search-item-badge {
            background: #e7f0ff;
            color: #0d6efd;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
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
                    // Maydon bo'shatilsa, tanlangan qiymat ham tozalanadi
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
                    searchInput.value = this.getAttribute('data-name');
                    hiddenInput.value = this.getAttribute('data-id');
                    resultsBox.style.display = 'none';
                    searchInput.style.borderColor = '#0d6efd';
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