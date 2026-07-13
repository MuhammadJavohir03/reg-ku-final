<x-layouts.sidebar>

    <x-slot:title>
        Natijalar
    </x-slot:title>

    <div class="container">
        <div class="card custom-card mb-5">
            <div class="card-body p-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>To'liq ismi</th>
                            <th>Joriy</th>
                            <th>Oraliq</th>
                            <th>Reyting</th>
                            <th>Yakuniy</th>
                            <th>Davomat</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjects as $subject)
                            <tr onclick="window.location='{{ route('subject.show', $user->id) }}'">
                                <td>{{ $subject->id }}</td>
                                <td>{{ $subject->nomi }}</td>
                                <td>{{ $subject->teacher->Toliq_ismi }}</td>
                                <td>{{ $subject->category->nomi }}</td>
                                <td>{{ $subject->semster }}</td>
                                <td>{{ $subject->lesson_type->nomi }}</td>
                                <td>{{ $subject->created_at->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('subject.edit', $subject->id) }}"
                                        class="btn btn-sm btn-warning">Tahrirlash</a>

                                    <form action="{{ route('subject.destroy', $subject->id) }}" method="POST"
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
                {{ $subjects->links() }}
            </div>
        </div>
    </div>

</x-layouts.sidebar>
