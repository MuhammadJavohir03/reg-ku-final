<x-layouts.sidebar>

    <x-slot:title>
        Bo'lim yaratish
    </x-slot:title>

    <div class="container">
        <div class="card custom-card mb-5">
            <div class="card-body p-4">
                <h4 class="mb-4" style="font-weight: 700;">Yangi bo'lim yaratish</h4>
                <form action="{{ route('bepul_semestr.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Bo'lim nomini</label>
                            <input type="text" name="nomi" class="form-control border-0 bg-light p-3"
                                style="border-radius: 12px;" placeholder="Bo'lim nomini kiriting...">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-soft mt-2">Bo'limni faollashtirish</button>
                </form>
            </div>
        </div>

    </div>
</x-layouts.sidebar>
