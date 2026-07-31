<x-layouts.sidebar>

    <x-slot:title>
        E'lon yaratish
    </x-slot:title>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
            <a href="{{ route('elons.index') }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i>
            </a>
            <div class="oz-title" style="margin:0;">
                <i class="bx bx-plus-circle" style="color:#3C3489;"></i> Yangi e'lon yaratish
            </div>
        </div>

        @if (session('success'))
            <div class="oz-alert oz-alert-success">
                <i class="bx bx-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="oz-alert oz-alert-danger">
                <i class="bx bx-error-circle"></i>
                Formada xatoliklar bor, iltimos, quyidagi maydonlarni tekshiring.
            </div>
        @endif

        <form action="{{ route('elons.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="soz-card">

                <div class="soz-grid">

                    <div class="soz-field">
                        <label><i class="bx bx-heading" style="color:#3C3489;"></i> E'lon sarlavhasi</label>
                        <input value="{{ old('title') }}" type="text" name="title"
                            class="soz-input @error('title') is-invalid @enderror"
                            placeholder="Sarlavhani kiriting...">
                        @error('title')
                            <span class="soz-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="soz-field">
                        <label><i class="bx bx-text" style="color:#3C3489;"></i> Qisqacha</label>
                        <input value="{{ old('short_content') }}" type="text" name="short_content"
                            id="shortContent" maxlength="150"
                            class="soz-input @error('short_content') is-invalid @enderror"
                            placeholder="Qisqacha tavsif...">
                        <div class="soz-hint"><span id="shortCount">0</span>/150</div>
                        @error('short_content')
                            <span class="soz-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="soz-field">
                        <label><i class="bx bx-category" style="color:#3C3489;"></i> Yo'nalishlar</label>
                        <select name="category_id" class="soz-input">
                            <option value="">Barcha yo'nalishlar (Hammaga)</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->nomi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="soz-field">
                        <label><i class="bx bx-layer" style="color:#3C3489;"></i> Kursi</label>
                        <select name="kurs" class="soz-input">
                            <option value="">Barcha kurslar</option>
                            @for ($i = 1; $i <= 4; $i++)
                                <option value="{{ $i }}" {{ old('kurs') == $i ? 'selected' : '' }}>
                                    {{ $i }}-kurs
                                </option>
                            @endfor
                        </select>
                    </div>

                </div>

                <div class="soz-divider"></div>

                <div class="soz-field" style="margin-bottom:16px;">
                    <label><i class="bx bx-image-add" style="color:#3C3489;"></i> Rasm yuklash</label>

                    <label class="soz-dropzone" id="dropzone">
                        <i class="bx bx-cloud-upload"></i>
                        <p>Rasmni shu yerga tashlang yoki tanlash uchun bosing</p>
                        <input type="file" name="photo" id="photoInput" accept="image/*">
                    </label>

                    <div class="soz-preview" id="preview">
                        <img id="previewImg" src="" alt="Oldindan ko'rish">
                        <button type="button" class="soz-preview-remove" id="removePreview">
                            <i class="bx bx-trash"></i> Rasmni bekor qilish
                        </button>
                    </div>

                    @error('photo')
                        <span class="soz-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="soz-field">
                    <label><i class="bx bx-file" style="color:#3C3489;"></i> E'lon matni</label>
                    <textarea name="full_content" id="fullContent"
                        class="soz-input @error('full_content') is-invalid @enderror" rows="5"
                        style="resize:vertical;" placeholder="Batafsil ma'lumot...">{{ old('full_content') }}</textarea>
                    <div class="soz-hint"><span id="fullCount">0</span> ta belgi</div>
                    @error('full_content')
                        <span class="soz-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <button type="submit" class="ar-btn ar-btn-ok" style="padding:.7rem 1.4rem;">
                <i class="bx bx-check"></i> E'lonni faollashtirish
            </button>
        </form>

    </div>

    <script>
        // Belgilar hisoblagichi
        const shortInput = document.getElementById('shortContent');
        const shortCount = document.getElementById('shortCount');
        const updateShortCount = () => shortCount.textContent = shortInput.value.length;
        shortInput.addEventListener('input', updateShortCount);
        updateShortCount();

        const fullInput = document.getElementById('fullContent');
        const fullCount = document.getElementById('fullCount');
        const updateFullCount = () => fullCount.textContent = fullInput.value.length;
        fullInput.addEventListener('input', updateFullCount);
        updateFullCount();

        // Rasm oldindan ko'rish + drag & drop
        const dropzone = document.getElementById('dropzone');
        const photoInput = document.getElementById('photoInput');
        const preview = document.getElementById('preview');
        const previewImg = document.getElementById('previewImg');
        const removePreview = document.getElementById('removePreview');

        function showPreview(file) {
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
                dropzone.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }

        photoInput.addEventListener('change', () => showPreview(photoInput.files[0]));

        ['dragover', 'dragenter'].forEach(evt =>
            dropzone.addEventListener(evt, e => { e.preventDefault(); dropzone.classList.add('is-dragover'); })
        );
        ['dragleave', 'drop'].forEach(evt =>
            dropzone.addEventListener(evt, e => { e.preventDefault(); dropzone.classList.remove('is-dragover'); })
        );
        dropzone.addEventListener('drop', e => {
            if (e.dataTransfer.files.length) {
                photoInput.files = e.dataTransfer.files;
                showPreview(e.dataTransfer.files[0]);
            }
        });

        removePreview.addEventListener('click', () => {
            photoInput.value = '';
            preview.style.display = 'none';
            dropzone.style.display = 'block';
        });
    </script>

</x-layouts.sidebar>