<x-layouts.sidebar>
    <x-slot name="title">Bo'limlar va adminlarni biriktirish</x-slot>
    <x-slot name="active">sections</x-slot>

    @section('styles')
        <style>
            /* Mini-scroll scrollbar uslubi */
            .admin-scroll-box::-webkit-scrollbar {
                width: 5px;
            }
            .admin-scroll-box::-webkit-scrollbar-track {
                background: #f5f4fa;
                border-radius: 10px;
            }
            .admin-scroll-box::-webkit-scrollbar-thumb {
                background: var(--jd-border);
                border-radius: 10px;
            }
            .admin-scroll-box::-webkit-scrollbar-thumb:hover {
                background: var(--jd-muted);
            }
        </style>
    @endsection

    <div class="oz-wrap container-fluid py-4">
        <!-- Sahifa Sarlavhasi -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="oz-title m-0">Bo'limlar va adminlarni biriktirish</h3>
        </div>

        <!-- Yangi bo'lim qo'shish kartasi -->
        <div class="soz-card mb-4">
            <h5 class="fw-bold mb-3" style="font-size: 15px; color: var(--jd-ink);">
                <i class="bi bi-plus-circle me-1" style="color: var(--jd-accent);"></i> Yangi bo'lim qo'shish
            </h5>
            <form method="POST" action="{{ route('admin.sections.store') }}" class="d-flex gap-2 flex-wrap flex-sm-nowrap">
                @csrf
                <input type="text" name="name" class="soz-input" placeholder="Masalan: Hemis bo'limi" required>
                <button type="submit" class="ar-btn ar-btn-ok text-nowrap">
                    <i class="bi bi-plus-lg"></i> Qo'shish
                </button>
            </form>
        </div>

        <!-- Bo'limlar ro'yxati (Grid) -->
        <div class="row g-3">
            @foreach ($sections as $section)
                <div class="col-md-6 col-xl-4">
                    <div class="soz-card h-100 d-flex flex-column justify-content-between p-3 m-0">
                        <div>
                            <!-- Section Tahrirlash va O'chirish -->
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-3 pb-3 border-bottom">
                                <form method="POST" action="{{ route('admin.sections.update', $section) }}" class="d-flex gap-2 flex-grow-1">
                                    @csrf 
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $section->name }}" class="soz-input py-1 px-2" style="font-size: 13px;" required>
                                    <button class="ar-btn" title="Saqlash">
                                        <i class="bi bi-check-lg text-success"></i>
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('admin.sections.destroy', $section) }}" onsubmit="return confirm('O\'chirilsinmi?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button class="ar-btn ar-btn-rej" title="O'chirish">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Admin biriktirish formasi -->
                            <form id="assign-form-{{ $section->id }}" method="POST" action="{{ route('admin.sections.assign', $section) }}">
                                @csrf
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold m-0" style="font-size: 12px; color: var(--jd-muted);">
                                        Biriktirilgan adminlar:
                                    </label>
                                    <span class="ar-badge ar-badge-accent">
                                        {{ $section->admins->count() }} ta admin
                                    </span>
                                </div>

                                <div class="admin-scroll-box border rounded-3 p-2 mb-3" style="max-height: 170px; overflow-y: auto; background: #fafaff;">
                                    @php $assignedIds = $section->admins->pluck('id')->toArray(); @endphp
                                    @foreach ($admins as $admin)
                                        <div class="form-check py-1">
                                            <input class="form-check-input" type="checkbox" name="admin_ids[]"
                                                   value="{{ $admin->id }}" id="adm-{{ $section->id }}-{{ $admin->id }}"
                                                   {{ in_array($admin->id, $assignedIds) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-1" for="adm-{{ $section->id }}-{{ $admin->id }}" style="font-size: 13px; cursor: pointer;">
                                                {{ $admin->{'To‘liq_ismi'} ?? $admin->email }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </form>
                        </div>

                        <!-- Biriktirishni saqlash tugmasi -->
                        <div>
                            <button type="submit" form="assign-form-{{ $section->id }}" class="ar-btn ar-btn-ok w-100 justify-content-center">
                                <i class="bi bi-save"></i> Biriktirishni saqlash
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.sidebar>