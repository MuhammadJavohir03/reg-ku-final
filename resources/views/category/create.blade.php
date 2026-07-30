<x-layouts.sidebar>

    <x-slot:title>
        Yo'nalish qo'shish
    </x-slot:title>

    <link rel="stylesheet" href="{{ asset('css/jadvallar.css') }}">

    <div class="oz-wrap">

        <div class="card custom-card mb-4">
            <div class="card-body p-4">
                <div class="oz-title" style="margin-bottom:20px;">Yangi yo'nalish yaratish</div>

                <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Yo'nalish Nomini Kiriting</label>
                            <input type="text" name="nomi" class="form-control"
                                placeholder="Sarlavhani kiriting...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ushbu Yo'nalishga tegishli guruh Kodi ?</label>
                            <input type="text" name="guruh" class="form-control"
                                placeholder="Sarlavhani kiriting...">
                        </div>
                    </div>
                    <button type="submit" class="ar-btn ar-btn-ok">
                        <i class="bx bx-check"></i> Yo'nalishni faollashtirish
                    </button>
                </form>
            </div>
        </div>

        <div class="card custom-card">
            <div class="card-body p-4">
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
                    <div class="oz-title" style="margin:0;">Yo'nalishlar</div>
                    <a href="{{ route('category.create') }}" class="ar-btn ar-btn-ok">
                        <i class="bx bx-plus"></i> Yangi yo'nalish qo'shish
                    </a>
                </div>

                <form action="{{ route('category.index') }}" method="GET" style="margin-bottom:16px; max-width:320px;">
                    <div style="position:relative;">
                        <i class="bx bx-search"
                            style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                        <input type="text" name="search" class="arizalar-search" style="width:100%; padding-left:36px;"
                            placeholder="Qidirish..." value="{{ request('search') }}">
                    </div>
                </form>

                <div class="arizalar-table-wrap">
                    <table class="arizalar-table">
                        <thead>
                            <tr>
                                <th style="width:70px;">ID</th>
                                <th>Nomi</th>
                                <th>Qisqa nomi</th>
                                <th style="width:140px;">Yaratilgan vaqt</th>
                                <th style="width:170px;">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr style="cursor:pointer;" onclick="window.location='{{ route('category.show', $category->id) }}'">
                                    <td class="ar-id">#{{ $category->id }}</td>
                                    <td style="font-weight:500;">{{ $category->nomi }}</td>
                                    <td style="color:#888;">{{ $category->guruh }}</td>
                                    <td style="color:#888; font-size:13px;">{{ $category->created_at->format('d-m-Y') }}</td>
                                    <td onclick="event.stopPropagation()">
                                        <div style="display:flex; gap:6px;">
                                            <a href="{{ route('category.edit', $category->id) }}" class="ar-btn" style="padding:6px 10px;">
                                                <i class="bx bx-edit"></i> Tahrirlash
                                            </a>
                                            <form action="{{ route('category.destroy', $category->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ar-btn ar-btn-rej" style="padding:6px 10px;"
                                                    onclick="return confirm('Haqiqatan ham bu fanni o\'chirmoqchimisiz?')">
                                                    <i class="bx bx-trash"></i> O'chirish
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; padding:2rem; color:#888;">
                                        Yo'nalishlar topilmadi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="ar-pagination" style="margin-top:16px;">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>

    </div>
</x-layouts.sidebar>