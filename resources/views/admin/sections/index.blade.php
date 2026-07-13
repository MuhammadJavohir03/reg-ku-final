<x-layouts.sidebar>
    <x-slot name="title">Bo'limlar va adminlarni biriktirish</x-slot>
    <x-slot name="active">sections</x-slot>

    @section('styles')
        <style>
            .form-check {
                margin-bottom: 0.25rem;
            }
        </style>
    @endsection

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Bo'limlar va adminlarni biriktirish</h3>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Yangi bo'lim qo'shish</h5>
            <form method="POST" action="{{ route('admin.sections.store') }}" class="d-flex gap-2">
                @csrf
                <input type="text" name="name" class="form-control" placeholder="Masalan: Hemis bo'limi" required>
                <button class="btn btn-primary">Qo'shish</button>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @foreach ($sections as $section)
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <form method="POST" action="{{ route('admin.sections.update', $section) }}" class="d-flex gap-2 flex-grow-1 me-2">
                                @csrf @method('PUT')
                                <input type="text" name="name" value="{{ $section->name }}" class="form-control form-control-sm">
                                <button class="btn btn-sm btn-outline-secondary">Saqlash</button>
                            </form>
                            <form method="POST" action="{{ route('admin.sections.destroy', $section) }}"
                                  onsubmit="return confirm('O\'chirilsinmi?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">O'chirish</button>
                            </form>
                        </div>

                        <form method="POST" action="{{ route('admin.sections.assign', $section) }}">
                            @csrf
                            <label class="form-label fw-bold">Biriktirilgan adminlar:</label>
                            <div class="mb-2" style="max-height:160px; overflow-y:auto;">
                                @php $assignedIds = $section->admins->pluck('id')->toArray(); @endphp
                                @foreach ($admins as $admin)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="admin_ids[]"
                                               value="{{ $admin->id }}" id="adm-{{ $section->id }}-{{ $admin->id }}"
                                               {{ in_array($admin->id, $assignedIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="adm-{{ $section->id }}-{{ $admin->id }}">
                                            {{ $admin->{'To‘liq_ismi'} ?? $admin->email }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn btn-sm btn-primary">Biriktirishni saqlash</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
</x-layouts.sidebar>
