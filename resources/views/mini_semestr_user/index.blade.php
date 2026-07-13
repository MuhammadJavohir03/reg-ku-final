<x-layouts.sidebar>

    <x-slot:title>
        Ariza Topshirish | Mini Semestr
    </x-slot:title>

    <div class="modern-ariza-container">

        <div class="modern-banner">
            <div class="banner-badge"><i class="fas fa-file-signature"></i> ARIZA TIZIMI (Mini Semestr)</div>
            <h1>Mini Semestrga ariza topshirish </h1>
            <p>Hujjatlarni rasmiylashtirish va imtihon fanlarini tanlash paneli</p>
        </div>

        <div class="timeline-flow-wrapper">

            <div class="flow-step-card">
                <div class="step-header">
                    <div class="step-number">01</div>
                    <div class="step-title">
                        <h3>Shaxsiy Ma'lumotlar</h3>
                        <p>Tizimdagi joriy talaba ma'lumotlari (O'zgartirib bo'lmaydi)</p>
                    </div>
                </div>

                <div class="step-body-content">
                    <div class="modern-input-row">
                        <div class="modern-field">
                            <label>To'liq ism-sharifingiz</label>
                            <div class="static-value-box">
                                {{ auth()->user()->getAttribute('To‘liq_ismi') ?? 'Ism topilmadi' }}</div>
                        </div>
                    </div>

                    <div class="modern-input-grid-two">
                        <div class="modern-field">
                            <label>User ID</label>
                            <div class="static-value-box">{{ auth()->user()->getAttribute('id') ?? 'id topilmadi' }}
                            </div>
                        </div>
                        <div class="modern-field">
                            <label>Akademik Guruh</label>
                            <div class="static-value-box">
                                {{ auth()->user()->getAttribute('Guruh') ?? 'Guruh topilmadi' }}</div>
                        </div>
                        <div class="modern-field">
                            <label>Akademik kursi</label>
                            <div class="static-value-box">
                                {{ auth()->user()->getAttribute('Kurs') ?? 'Kurs topilmadi' }}-Kurs</div>
                        </div>
                        <div class="modern-field">
                            <label>Ta'lim Yo'nalishi</label>
                            <div class="static-value-box">{{ $userCategory->nomi }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flow-step-card">
                <div class="step-header">
                    <div class="step-number">02</div>
                    <div class="step-title">
                        <h3>Fanlarni Tanlang</h3>
                        <p>Imtihon topshirmoqchi bo'lgan fanlaringizni ro'yxatdan belgilang</p>
                    </div>
                </div>

                <div class="step-body-content">
                    <div class="modern-search-box">
                        <h4>Topshirilayotgan bo'lim : {{ $activeBolim->nomi ?? "Xozircha active bo'lgan bo'lim yo'q" }}
                        </h4>
                    </div>

                    <form action="{{ route('mini_semestr_user.store') }}" method="POST">
                        @csrf
                        <div class="modern-scroll-container">
                            @foreach ($subjects as $subject)
                                <div class="subject-neo-item">
                                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                        <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                                            {{ in_array($subject->id, $submittedSubjectIds) ? 'disabled checked' : '' }}>
                                        <span class="sub-name">{{ $subject->nomi }}</span>
                                    </label>
                                    @if (in_array($subject->id, $submittedSubjectIds))
                                        <span class="sub-tag tag-success">Topshirilgan</span>
                                    @else
                                        <span class="sub-tag tag-primary">Asosiy</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <button class="btn-primary-gold mt-3" type="submit">Saqlash</button>
                    </form>
                    <span class="hint-caption">* Fanni ustiga bosish orqali uni tanlaysiz yoki bekor qilasiz.</span>
                </div>
            </div>

            {{-- <div class="flow-step-card">
                <div class="step-header">
                    <div class="step-number">03</div>
                    <div class="step-title">
                        <h3>Tanlangan Fanlar Ro'yxati</h3>
                        <p>Siz tomoningizdan arizaga qo'shilgan fanlar ro'yxati</p>
                    </div>
                </div>

                <div class="step-body-content">
                    @if ($free_semestrs->isNotEmpty())
                        @foreach ($free_semestrs as $free_semestr)
                            <span class="sub-name"><i class="fas fa-book-open"></i>
                                {{ $free_semestr->subject->nomi }}</span>
                        @endforeach
                    @else
                        <div class="empty-state-card-box">
                            <div class="empty-icon"><i class="far fa-folder-open"></i></div>
                            <p>Hozircha hech qanday fan tanlanmadi. Yuqoridagi ro'yxatdan tanlang.</p>
                        </div>
                    @endif
                </div>
            </div> --}}

        </div>
    </div>

</x-layouts.sidebar>
