<x-layouts.sidebar>

    <x-slot:title>
        Category
    </x-slot:title>

    <div class="container">
        <div class="card custom-card mb-5">
            <div class="card-body p-4">
                <h4 class="mb-4" style="font-weight: 700;">Fanlar</h4>
                <a href="{{ route('category.create') }}"class="mb-3 btn btn-primary border shadow-sm rounded-pill px-4">Yangi
                    yo'nalish qo'shish</a>
                <form action="{{ route('category.index') }}" method="GET" class="mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Qidirish..."
                        value="{{ request('search') }}">
                </form>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nomi</th>
                            <th>Yaratilgan vaqt</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr onclick="window.location='{{ route('category.show', $category->id) }}'">
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->nomi }}</td>
                                <td>{{ $category->created_at->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('category.edit', $category->id) }}"
                                        class="btn btn-sm btn-warning">Tahrirlash</a>

                                    <form action="{{ route('category.destroy', $category->id) }}" method="POST"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Haqiqatan ham bu fanni o\'chirmoqchimisiz?')">O'chirish</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="justify-content-center m-3 pb-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

</x-layouts.sidebar>
