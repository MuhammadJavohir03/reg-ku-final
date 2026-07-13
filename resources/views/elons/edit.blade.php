<x-layouts.sidebar>

    <x-slot:title>
        E'lon tahrirlash
    </x-slot:title>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
            <a href="{{ route('elons.index') }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i>
            </a>
            <div class="oz-title" style="margin:0;">
                <i class="bx bx-edit" style="color:#3C3489;"></i> Tahrirlash — ID: {{ $elon->id }}
            </div>
        </div>

        <form action="{{ route('elons.update', $elon->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="soz-card">

                <div class="soz-grid">

                    <div class="soz-field">
                        <label><i class="bx bx-heading" style="color:#3C3489;"></i> E'lon sarlavhasi</label>
                        <input value="{{ $elon->title }}" type="text" name="title" class="soz-input"
                            placeholder="Sarlavhani kiriting...">
                    </div>

                    <div class="soz-field">
                        <label><i class="bx bx-text" style="color:#3C3489;"></i> Qisqacha</label>
                        <input value="{{ $elon->short_content }}" type="text" name="short_content" class="soz-input"
                            placeholder="Qisqacha tavsif...">
                    </div>

                    <div class="soz-field">
                        <label><i class="bx bx-category" style="color:#3C3489;"></i> Yo'nalishlar</label>
                        <select name="category_id" class="soz-input">
                            <option value="">Barcha yo'nalishlar (Hammaga)</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $elon->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nomi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="soz-field">
                        <label><i class="bx bx-layer" style="color:#3C3489;"></i> Kursi</label>
                        <select name="kurs" class="soz-input">
                            <option value="">Barcha kurslar</option>
                            @for ($i = 1; $i <= 4; $i++)
                                <option value="{{ $i }}" {{ $elon->kurs == $i ? 'selected' : '' }}>
                                    {{ $i }}-kurs
                                </option>
                            @endfor
                        </select>
                    </div>

                </div>

                <div class="soz-divider"></div>

                @if ($elon->photo)
                    <div class="soz-field" style="margin-bottom:16px;">
                        <label><i class="bx bx-image" style="color:#3C3489;"></i> Joriy rasm</label>
                        <img src="{{ asset('storage/' . $elon->photo) }}" alt="Rasm"
                            style="max-width:220px; border-radius:12px; border:1px solid #f0f0f0; display:block;">
                    </div>
                @endif

                <div class="soz-field" style="margin-bottom:16px;">
                    <label><i class="bx bx-image-add" style="color:#3C3489;"></i> Rasm yuklash (yangilash uchun)</label>
                    <div style="border:2px dashed #e0e0e0; border-radius:12px; padding:24px; text-align:center; background:#fafafa;">
                        <i class="bx bx-cloud-upload" style="font-size:32px; color:#3C3489;"></i>
                        <p style="margin:6px 0 10px; color:#888; font-size:13px;">Rasmni shu yerga tashlang yoki tanlang</p>
                        <input type="file" name="photo" class="soz-input" style="max-width:280px; margin:0 auto;">
                    </div>
                </div>

                <div class="soz-field">
                    <label><i class="bx bx-file" style="color:#3C3489;"></i> E'lon matni</label>
                    <textarea name="full_content" class="soz-input" rows="5"
                        style="resize:vertical;">{{ $elon->full_content }}</textarea>
                </div>

            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="ar-btn ar-btn-ok" style="flex:1; justify-content:center; padding:.7rem;">
                    <i class="bx bx-save"></i> Saqlash
                </button>
                <button type="button" onclick="window.history.back()" class="ar-btn ar-btn-rej"
                    style="flex:1; justify-content:center; padding:.7rem;">
                    <i class="bx bx-x"></i> Bekor qilish
                </button>
            </div>

        </form>

    </div>

</x-layouts.sidebar>