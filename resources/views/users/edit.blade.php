<x-layouts.sidebar>
    <x-slot:title>Foydalanuvchini tahrirlash</x-slot:title>

    <div class="oz-wrap">

        <div class="oz-toolbar">
            <a href="{{ route('users.index') }}" class="ar-btn">← Orqaga</a>

            <div style="display:flex; align-items:center; gap:10px;">
                <span class="oz-title">Foydalanuvchi ID: {{ $user->id }}</span>

                <form action="{{ route('users.login_as', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="ar-btn ar-btn-info" title="Bu talaba nomidan kirish">
                        <i class="bx bx-show"></i> Login qilish
                    </button>
                </form>
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

            <div class="oz-filters" style="grid-template-columns:repeat(auto-fit, minmax(220px,1fr));">
                @foreach ($fields as $field)
                    @continue(in_array($field, $excludedFields))

                    <div style="display:flex; flex-direction:column; gap:4px;">
                        <label style="font-size:12px; color:#888; font-weight:600;">
                            {{ str_replace('_', ' ', $field) }}
                        </label>

                        @if ($field == 'Kurs')
                            <select name="Kurs" class="arizalar-search" style="width:100%">
                                @for ($i = 1; $i <= 4; $i++)
                                    <option value="{{ $i }}" {{ $user->$field == $i ? 'selected' : '' }}>
                                        {{ $i }}-kurs
                                    </option>
                                @endfor
                            </select>
                        @elseif($field == 'Bitiruvchi')
                            <select name="Bitiruvchi" class="arizalar-search" style="width:100%">
                                <option value="Yo'q" {{ $user->$field == "Yo'q" ? 'selected' : '' }}>Yo'q</option>
                                <option value="Ha" {{ $user->$field == 'Ha' ? 'selected' : '' }}>Ha</option>
                            </select>
                        @else
                            <input type="text" name="{{ $field }}" value="{{ $user->getAttribute($field) }}"
                                class="arizalar-search" style="width:100%">
                        @endif
                    </div>
                @endforeach

                {{-- Parol alohida, xavfsiz tarzda: joriy hash hech qachon
                     ko'rsatilmaydi va input bo'sh qoldirilsa parol o'zgarmaydi --}}
                <div style="display:flex; flex-direction:column; gap:4px;">
                    <label style="font-size:12px; color:#888; font-weight:600;">
                        Yangi parol (bo'sh qoldirsangiz o'zgarmaydi)
                    </label>
                    <input type="text" name="password" value=""
                        class="arizalar-search" style="width:100%" autocomplete="new-password"
                        placeholder="Yangi parolni kiriting">
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
</x-layouts.sidebar>