<x-layouts.sidebar>

    <x-slot:title>
        O'qituvchi yaratish
    </x-slot:title>

    <div class="container">
        <div class="form-container">
            <h2 class="form-title
                mb-4 fw-bold text-dark border-start border-primary border-4 ps-3">Yangi o'qituvchi qo'shish</h2>
            <form action="{{ route('teacher.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="Toliq_ismi" class="form-label">To'liq ismi</label>
                    <input type="text" name="To‘liq_ismi" id="Toliq_ismi" class="form-control"
                        placeholder="Ism va familiyani kiriting" required>
                </div>
                <div class="mb-3">
                    <label for="Email" class="form-label">Email manzili</label>
                    <input type="email" name="email" id="Email" class="form-control"
                        placeholder="Email manzilini kiriting" required>
                </div>
                <div class="mb-3">
                    <label for="Password" class="form-label">Parol</label>
                    <input type="password" name="password" id="Password" class="form-control"
                        placeholder="Parolni kiriting" required>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Rasm</label>
                    <input type="file" name="photo" id="photo" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">Yaratish</button>
            </form>
        </div>
    </div>
</x-layouts.sidebar>