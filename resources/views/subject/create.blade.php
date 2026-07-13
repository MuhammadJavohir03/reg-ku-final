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

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Yo'nalish</label>
                        <select name="category_id" class="arizalar-search" style="width:100%;">
                            <option value="" disabled selected>Tanlang...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->nomi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Dars turi</label>
                        <select name="lesson_type_id" class="arizalar-search" style="width:100%;">
                            <option value="" disabled selected>Tanlang...</option>
                            @foreach ($lesson_types as $lesson_type)
                                <option value="{{ $lesson_type->id }}"
                                    {{ old('lesson_type_id') == $lesson_type->id ? 'selected' : '' }}>
                                    {{ $lesson_type->nomi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Semestr</label>
                        <input type="number" name="semster" class="arizalar-search" style="width:100%;"
                            placeholder="1-8" min="1" max="8" value="{{ old('semster') }}" required>
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">O'qituvchi</label>
                        <div style="position:relative;">
                            <i class="bx bx-search"
                                style="position:absolute; left:10px; top:50%;
                                transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                            <input type="text" id="teacher_search" class="arizalar-search"
                                style="width:100%; padding-left:34px;" placeholder="ID yoki ismni yozing..."
                                autocomplete="off">
                        </div>

                        {{-- DROPDOWN --}}
                        <div id="teacher_results"
                            style="display:none; position:absolute; background:#fff; border:1px solid #e8e8e8;
                            border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.1); z-index:1050;
                            max-height:220px; overflow-y:auto; width:280px; margin-top:4px;">
                            @foreach ($teachers as $teacher)
                                @if ($teacher->role === 'teacher')
                                    <div class="teacher-item" data-id="{{ $teacher->id }}"
                                        data-name="{{ $teacher['To\'liq_ismi'] }}"
                                        style="display:flex; align-items:center; gap:10px; padding:10px 14px;
                                        cursor:pointer; border-bottom:1px solid #f5f5f5; transition:background 0.15s;">
                                        <span
                                            style="background:#EEEDFE; color:#3C3489; padding:2px 8px;
                                            border-radius:6px; font-size:11px; font-weight:700; flex-shrink:0;">
                                            #{{ $teacher->id }}
                                        </span>
                                        <span style="font-size:13px; color:#333; font-weight:500;">
                                            {{ $teacher['To\'liq_ismi'] }}
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <input type="hidden" name="teacher_id" id="hidden_teacher_id">
                    </div>

                </div>
            </div>

            <button type="submit" class="ar-btn ar-btn-ok" style="width:100%; justify-content:center; padding:10px;">
                <i class="bx bx-save"></i> Saqlash
            </button>

        </form>
    </div>

    <script>
        const searchInput = document.getElementById('teacher_search');
        const resultsBox = document.getElementById('teacher_results');
        const hiddenInput = document.getElementById('hidden_teacher_id');
        const items = document.querySelectorAll('.teacher-item');

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

        items.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.background = '#EEEDFE';
            });
            item.addEventListener('mouseleave', function() {
                this.style.background = '#fff';
            });
            item.addEventListener('click', function() {
                searchInput.value = this.getAttribute('data-name');
                hiddenInput.value = this.getAttribute('data-id');
                resultsBox.style.display = 'none';
                searchInput.style.borderColor = '#3C3489';
            });
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.style.display = 'none';
            }
        });
    </script>

</x-layouts.sidebar>
