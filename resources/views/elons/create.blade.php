<x-layouts.sidebar>

    <x-slot:title>
        E'lon yaratish
    </x-slot:title>

    <div class="container">
        <div class="card custom-card mb-5">
            <div class="card-body p-4">
                <h4 class="mb-4" style="font-weight: 700;">Yangi e'lon yaratish</h4>
                <form action="{{ route('elons.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">E'lon sarlavhasi</label>
                            <input type="text" name="title" class="form-control border-0 bg-light p-3"
                                style="border-radius: 12px;" placeholder="Sarlavhani kiriting...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Qisqacha</label>
                            <input type="text" name="short_content" class="form-control border-0 bg-light p-3"
                                style="border-radius: 12px;" placeholder="Qisqacha tavsif...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Yonalishlar</label>
                            <select name="category_id" class="form-select ...">
                                <option value="">Barcha yo'nalishlar (Hammaga)</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nomi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Kursi</label>
                            <select name="kurs" class="form-select ...">
                                <option value="">Barcha kurslar</option>
                                <option value="1">1-kurs</option>
                                <option value="2">2-kurs</option>
                                <option value="3">3-kurs</option>
                                <option value="4">4-kurs</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Rasm yuklash</label>
                            <div class="upload-section">
                                <i class="bx bx-cloud-upload fs-1 text-primary"></i>
                                <p class="mb-0">Rasmni shu yerga tashlang yoki tanlang</p>
                                <input type="file" name="photo" class="form-control mt-2">
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">E'lon matni</label>
                            <textarea name="full_content" class="form-control border-0 bg-light p-3" style="border-radius: 12px;" rows="3"
                                placeholder="Batafsil ma'lumot..."></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-soft mt-2">E'lonni faollashtirish</button>
                </form>
            </div>
        </div>

    </div>
</x-layouts.sidebar>
