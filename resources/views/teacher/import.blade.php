<x-layouts.sidebar>

    <x-slot:title>
        O'qituvchilarni Import qilish
    </x-slot:title>

    <div class="oz-wrap">

        <div class="oz-title" style="margin-bottom:20px;">
            <i class="bx bx-import" style="color:#3C3489;"></i> Excel'dan O'qituvchilarni Import qilish
        </div>

        @if ($errors->any())
            <div style="background:#fde2e2; color:#b91c1c; border:1px solid #f5c2c2; padding:12px 16px; border-radius:10px; margin-bottom:16px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:24px;">

            <p style="color:#555; margin-bottom:16px;">
                Faylda <b>"FISh"</b> (To'liq ismi) va <b>"Elektron pochta"</b> (email) ustunlari bo'lishi kerak.
                Har bir yangi yaratilgan o'qituvchining paroli avtomatik ravishda
                <b>reg1234567</b> qilib qo'yiladi. Agar shu email bilan foydalanuvchi allaqachon
                mavjud bo'lsa, faqat ismi yangilanadi (paroli o'zgarmaydi).
            </p>

            <form action="{{ route('teacher.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="margin-bottom:20px;">
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Excel fayl (.xlsx)</label>
                    <input type="file" name="file" accept=".xlsx,.xls" required
                        style="border:1px solid #ddd; border-radius:8px; padding:10px; width:100%; max-width:400px;">
                </div>

                <div style="display:flex; gap:10px;">
                    <button type="submit" class="ar-btn ar-btn-ok">
                        <i class="bx bx-upload"></i> Import qilish
                    </button>

                    <a href="{{ route('teacher.index') }}" class="ar-btn" style="background:#f2f2f2; color:#555;">
                        Bekor qilish
                    </a>
                </div>
            </form>
        </div>

    </div>

</x-layouts.sidebar>