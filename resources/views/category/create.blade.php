<x-layouts.sidebar>

    <x-slot:title>
        Yo'nalish qo'shish
    </x-slot:title>

    <div class="container">
        <div class="card custom-card mb-5">
            <div class="card-body p-4">
                <h4 class="mb-4" style="font-weight: 700;">Yangi yo'nalish yaratish</h4>
                <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Yo'nalish Nomini Kiriting</label>
                            <input type="text" name="nomi" class="form-control border-0 bg-light p-3"
                                style="border-radius: 12px;" placeholder="Sarlavhani kiriting...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Ushbu Yo'nalishga tegishli guruh Kodi ?</label>
                            <input type="text" name="guruh" class="form-control border-0 bg-light p-3"
                                style="border-radius: 12px;" placeholder="Sarlavhani kiriting...">
                        </div>
                    <button type="submit" class="mb-3 btn btn-primary border shadow-sm rounded-pill px-4">Yo'nalishni faollashtirish</button>
                </form>
            </div>
        </div>

    </div>
</x-layouts.sidebar>
