<x-layouts.sidebar>
    <x-slot:title>Foydalanuvchini tahrirlash</x-slot:title>

    <div class="oz-wrap">

        <div class="oz-toolbar">
            <a href="{{ route('users.index') }}" class="ar-btn">← Orqaga</a>

            <div style="display:flex; align-items:center; gap:10px;">
                <span class="oz-title">Foydalanuvchi ID: {{ $user->id }}</span>
                @if ($user->role === 'talaba')
                    <a href="{{ route('users.grades', $user->id) }}" class="ar-btn ar-btn-ok"
                        title="Talabaning fanlar bo'yicha natijalari">
                        <i class="bx bx-bar-chart-alt-2"></i> Natijalarga o'tish
                    </a>
                @endif

                @php
                    $protectedEmails = ['javohir8386@gmail.com', 'samiyusuf@gmail.com'];
                    $isProtectedTarget = in_array($user->email, $protectedEmails);
                    $isSuperAdmin = in_array(auth()->user()->email, $protectedEmails);
                @endphp

                @if (!$isProtectedTarget && ($user->role !== 'admin' || $isSuperAdmin))
                    <form action="{{ route('users.login_as', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="ar-btn" title="Bu talaba nomidan kirish">
                            <i class="bx bx-show"></i> Login qilish
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            @php
                $fields = $user->getFillable();
                // Parolni umumiy tsikldan chiqarib tashlaymiz - uni pastda
                // alohida, xavfsiz tarzda ko'rsatamiz.
$excludedFields = ['password'];
            @endphp

            <div class="soz-card">
                <div class="soz-grid" style="grid-template-columns:repeat(auto-fit, minmax(220px,1fr));">
                    @foreach ($fields as $field)
                        @continue(in_array($field, $excludedFields))

                        <div class="soz-field">
                            <label>{{ str_replace('_', ' ', $field) }}</label>

                            @if ($field == 'Kurs')
                                <select name="Kurs" class="soz-input" style="width:100%">
                                    @for ($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}" {{ $user->$field == $i ? 'selected' : '' }}>
                                            {{ $i }}-kurs
                                        </option>
                                    @endfor
                                </select>
                            @elseif($field == 'Bitiruvchi')
                                <select name="Bitiruvchi" class="soz-input" style="width:100%">
                                    <option value="Yo'q" {{ $user->$field == "Yo'q" ? 'selected' : '' }}>Yo'q</option>
                                    <option value="Ha" {{ $user->$field == 'Ha' ? 'selected' : '' }}>Ha</option>
                                </select>
                            @else
                                <input type="text" name="{{ $field }}"
                                    value="{{ $user->getAttribute($field) }}" class="soz-input" style="width:100%">
                            @endif
                        </div>
                    @endforeach

                    {{-- Parol alohida, xavfsiz tarzda: joriy hash hech qachon
                         ko'rsatilmaydi va input bo'sh qoldirilsa parol o'zgarmaydi --}}
                    <div class="soz-field">
                        <label>Yangi parol</label>
                        <input type="text" name="password" value="" class="soz-input" style="width:100%"
                            autocomplete="new-password" placeholder="Yangi parolni kiriting">
                    </div>
                </div>
            </div>

            <div style="margin-top:1.5rem;">
                <button type="submit" class="ar-btn ar-btn-ok"
                    style="width:100%; justify-content:center; padding:12px;">
                    ✓ Barchasini saqlash
                </button>
            </div>
        </form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('select').forEach(select => {
                // Asl select elementini yashirish
                select.classList.add('glass-replaced');

                // Yangi custom wrapper yaratish
                const wrapper = document.createElement('div');
                wrapper.className = 'custom-glass-select';

                const trigger = document.createElement('div');
                trigger.className = 'glass-select-trigger';

                const selectedOption = select.options[select.selectedIndex];
                trigger.innerHTML = `<span>${selectedOption ? selectedOption.text : ''}</span>`;

                const menu = document.createElement('div');
                menu.className = 'glass-select-menu';

                // Option-larni o'qib custom menyuga o'tkazish
                Array.from(select.options).forEach((opt, idx) => {
                    const item = document.createElement('div');
                    item.className = 'glass-select-item' + (idx === select.selectedIndex ?
                        ' selected' : '');
                    item.textContent = opt.text;
                    item.dataset.value = opt.value;

                    item.addEventListener('click', (e) => {
                        e.stopPropagation();

                        // Select qiymatini almashtirish
                        select.value = opt.value;
                        select.dispatchEvent(new Event(
                            'change')); // Eventni ham ishga tushirish

                        // UI ni yangilash
                        trigger.querySelector('span').textContent = opt.text;
                        menu.querySelectorAll('.glass-select-item').forEach(i => i.classList
                            .remove('selected'));
                        item.classList.add('selected');
                        wrapper.classList.remove('open');
                    });

                    menu.appendChild(item);
                });

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    document.querySelectorAll('.custom-glass-select').forEach(w => {
                        if (w !== wrapper) w.classList.remove('open');
                    });
                    wrapper.classList.toggle('open');
                });

                wrapper.appendChild(trigger);
                wrapper.appendChild(menu);
                select.parentNode.insertBefore(wrapper, select.nextSibling);
            });

            // Tashqariga bosganda menyuni yopish
            document.addEventListener('click', () => {
                document.querySelectorAll('.custom-glass-select').forEach(w => w.classList.remove('open'));
            });
        });
    </script>
</x-layouts.sidebar>
