<x-layouts.sidebar>

    <x-slot:title>
        Yo'nalish Tahrirlash
    </x-slot:title>

    <div class="container">
        <div class="card custom-card mb-5">
            <div class="card-body p-4">
                <h4 class="mb-4" style="font-weight: 700;">Tahrirlash</h4>
                <form action="{{ route('category.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Yo'nalish nomi</label>
                            <input value="{{$category->nomi}}" type="text" name="nomi" class="form-control border-0 bg-light p-3"
                                style="border-radius: 12px;" placeholder="Nomini kiriting...">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Yo'nalish nomi</label>
                            <input value="{{$category->guruh}}" type="text" name="guruh" class="form-control border-0 bg-light p-3"
                                style="border-radius: 12px;" placeholder="Nomini kiriting...">
                        </div>
                    <button type="submit" class="mb-3 btn btn-primary border shadow-sm rounded-pill px-4">Saqlash</button>
                </form>
            </div>
        </div>

    </div>
</x-layouts.sidebar>
