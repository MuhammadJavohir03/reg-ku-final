<x-layouts.sidebar>

    <x-slot:title>
        Ariza Topshirish | Mini Semestr
    </x-slot:title>

    <div class="oz-wrap">

        <div class="oz-banner">
            <div class="oz-banner-badge"><i class="bx bx-file"></i> ARIZA TIZIMI (Mini Semestr)</div>
            <h1>Mini Semestrga ariza topshirish</h1>
            <p>Hujjatlarni rasmiylashtirish va imtihon fanlarini tanlash paneli</p>
        </div>

        <div class="oz-step">
            <div class="oz-step-header">
                <div class="oz-step-number">01</div>
                <div>
                    <h3>Shaxsiy Ma'lumotlar</h3>
                    <p>Tizimdagi joriy talaba ma'lumotlari (O'zgartirib bo'lmaydi)</p>
                </div>
            </div>

            <div class="oz-step-body">
                <div class="oz-static-field" style="margin-bottom:14px;">
                    <label>To'liq ism-sharifingiz</label>
                    <div class="oz-static-value">{{ auth()->user()->getAttribute('To‘liq_ismi') ?? 'Ism topilmadi' }}</div>
                </div>

                <div class="oz-static-grid">
                    <div class="oz-static-field">
                        <label>User ID</label>
                        <div class="oz-static-value">{{ auth()->user()->getAttribute('id') ?? 'id topilmadi' }}</div>
                    </div>
                    <div class="oz-static-field">
                        <label>Akademik Guruh</label>
                        <div class="oz-static-value">{{ auth()->user()->getAttribute('Guruh') ?? 'Guruh topilmadi' }}</div>
                    </div>
                    <div class="oz-static-field">
                        <label>Akademik kursi</label>
                        <div class="oz-static-value">{{ auth()->user()->getAttribute('Kurs') ?? 'Kurs topilmadi' }}-Kurs</div>
                    </div>
                    <div class="oz-static-field">
                        <label>Ta'lim Yo'nalishi</label>
                        <div class="oz-static-value">{{ $userCategory->nomi }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="oz-step">
            <div class="oz-step-header">
                <div class="oz-step-number">02</div>
                <div>
                    <h3>Fanlarni Tanlang</h3>
                    <p>Imtihon topshirmoqchi bo'lgan fanlaringizni ro'yxatdan belgilang (bir bo'lim uchun ko'pi bilan 3 ta)</p>
                </div>
            </div>

            <div class="oz-step-body">
                <div class="oz-highlight-box">
                    <h4>Topshirilayotgan bo'lim: {{ $activeBolim->nomi ?? "Xozircha active bo'lgan bo'lim yo'q" }}</h4>
                </div>

                @php
                    $submittedCount = count($submittedSubjectIds);
                    $remainingSlots = max(0, 3 - $submittedCount);
                @endphp

                <div class="oz-limit-counter">
                    <span>Tanlangan fanlar</span>
                    <span><strong id="selectedCount">{{ $submittedCount }}</strong> / 3</span>
                </div>

                <form action="{{ route('mini_semestr_user.store') }}" method="POST" id="subjectForm">
                    @csrf
                    <div class="oz-subject-list">
                        @foreach ($subjects as $subject)
                            @php $isSubmitted = in_array($subject->id, $submittedSubjectIds); @endphp
                            <div class="oz-subject-item {{ $isSubmitted ? 'is-checked' : '' }}"
                                data-locked="{{ $isSubmitted ? '1' : '0' }}">
                                <label class="oz-subject-label">
                                    <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                                        {{ $isSubmitted ? 'disabled checked' : ($remainingSlots <= 0 ? 'disabled' : '') }}>
                                    <span>{{ $subject->nomi }} - {{($subject->semster)}} - semestr</span>
                                </label>
                                @if ($isSubmitted)
                                    <span class="ar-badge ar-badge-ok">Topshirilgan</span>
                                @else
                                    <span class="ar-badge ar-badge-accent">Asosiy</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <button class="ar-btn ar-btn-ok" type="submit">
                        <i class="bx bx-check"></i> Saqlash
                    </button>
                </form>

                <span class="oz-hint-text">* Bir bo'lim uchun ko'pi bilan 3 ta fanga ariza topshirish mumkin. Limitga yetganda qolgan fanlar avtomatik bloklanadi.</span>
            </div>
        </div>

    </div>

    <script>
        (() => {
            const maxTotal = 3;
            const checkboxes = Array.from(document.querySelectorAll('#subjectForm input[type="checkbox"][name="subject_ids[]"]'));
            const counterEl = document.getElementById('selectedCount');

            function updateLimit() {
                const checkedCount = checkboxes.filter(cb => cb.checked).length;
                if (counterEl) counterEl.textContent = checkedCount;

                checkboxes.forEach(cb => {
                    const item = cb.closest('.oz-subject-item');
                    if (item && item.dataset.locked === '1') return; // avvaldan topshirilgan fanga tegilmaydi

                    item?.classList.toggle('is-checked', cb.checked);

                    if (!cb.checked && checkedCount >= maxTotal) {
                        cb.disabled = true;
                        item?.classList.add('is-limit-disabled');
                    } else {
                        cb.disabled = false;
                        item?.classList.remove('is-limit-disabled');
                    }
                });
            }

            checkboxes.forEach(cb => cb.addEventListener('change', updateLimit));
            updateLimit();
        })();
    </script>

</x-layouts.sidebar>