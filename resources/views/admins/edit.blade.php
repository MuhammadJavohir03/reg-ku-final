<x-layouts.sidebar>

    <x-slot:title>
        Adminlarni tahrirlash
    </x-slot:title>

    <div class="container">
        <div class="form-container">
            <h2 class="form-title
                mb-4 fw-bold text-dark border-start border-primary border-4 ps-3">Adminlarni tahrirlash</h2>
            <form action="{{ route('admins.update', $admin->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="To‘liq_ismi" class="form-label">To'liq ismi</label>
                    <input value="{{ $admin['To‘liq_ismi'] }}" type="text" name="To‘liq_ismi" class="form-control"
                        placeholder="Ism va familiyani kiriting" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email manzili</label>
                    <input value="{{ $admin->email }}" type="email" name="email" class="form-control"
                        placeholder="Email manzilini kiriting" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Parol</label>
                    <input type="text" name="password" class="form-control"
                        placeholder="Parolni kiriting">
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Rasm</label>
                    <input type="file" name="photo" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">O'zgartirish</button>
            </form>
        </div>
    </div>
</x-layouts.sidebar>