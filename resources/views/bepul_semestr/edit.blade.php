<x-layouts.sidebar>

    <x-slot:title>
        Bo'lim Tahrirlash
    </x-slot:title>

    <div class="container">
        <div class="card custom-card mb-5">
            <div class="card-body p-4">
                <h4 class="mb-4" style="font-weight: 700;">Tahrirlash</h4>
                <form action="{{ route('bepul_semestr.update', $bepul_semestr->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Bo'lim nomini</label>
                            <input value="{{ $bepul_semestr->nomi }}" type="text" name="nomi"
                                class="form-control border-0 bg-light p-3" style="border-radius: 12px;"
                                placeholder="Bo'lim nomini kiriting...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Bo'lim nomini</label>
                            <select name="status" id="status" class="form-control">
                                <!-- Active variant (qiymati 1) -->
                                <option value="1" @selected($bepul_semestr->status == 1)>
                                    Active
                                </option>

                                <!-- Block variant (qiymati 0) -->
                                <option value="0" @selected($bepul_semestr->status == 0)>
                                    Block
                                </option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-soft mt-2">Bo'limni faollashtirish</button>
                </form>
            </div>
        </div>

    </div>
</x-layouts.sidebar>
