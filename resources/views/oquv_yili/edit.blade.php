<x-layouts.sidebar>
    <x-slot name="title">O'quv yili</x-slot>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Yangi o'quv yili qo'shish</h1>

                <form action="{{ route('oquv_yili.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nomi" class="form-label">O'quv yili nomi</label>
                        <input type="text" name="nomi" id="nomi" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Saqlash</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete(event) {
            event.preventDefault();
            if (confirm('Haqiqatan ham o\'chirmoqchimisiz?')) {
                event.target.submit();
            }
        }
    </script>
</x-layouts.sidebar>