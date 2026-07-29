<x-layouts.sidebar>

    <x-slot:title>
        O'qituvchilar Ro'yxati
    </x-slot:title>

    <link rel="stylesheet" href="{{ asset('css/jadvallar.css') }}">

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
            <div class="oz-title" style="margin:0;">O'qituvchilar Ro'yxati</div>

            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('teacher.import') }}" class="ar-btn">
                    <i class="bx bx-import"></i> Excel'dan import qilish
                </a>

                <a href="{{ route('teacher.create') }}" class="ar-btn ar-btn-ok">
                    <i class="bx bx-plus"></i> Yangi O'qituvchi yaratish
                </a>
            </div>
        </div>

        @if (session('success'))
            <div style="background:var(--jd-success-soft); color:var(--jd-success); border:1px solid #a7f3d0; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:13px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th>To'liq Ismi</th>
                        <th>Email</th>
                        <th style="width:120px;">Roli</th>
                        <th style="width:120px; text-align:center;">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr>
                            <td class="ar-id">
                                #{{ $teacher->id }}
                            </td>

                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div class="ar-avatar">
                                        {{ mb_substr($teacher['To‘liq_ismi'] ?? 'N', 0, 2) }}
                                    </div>
                                    <span style="font-weight:500; font-size:13px;">
                                        {{ $teacher['To‘liq_ismi'] }}
                                    </span>
                                </div>
                            </td>

                            <td style="font-size:13px; color:#888;">{{ $teacher->email }}</td>

                            <td>
                                <span class="ar-badge ar-badge-ok">O'qituvchi</span>
                            </td>

                            <td style="text-align:center;">
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <a href="{{ route('teacher.edit', $teacher->id) }}" class="ar-btn" title="Tahrirlash" style="padding:6px 9px;">
                                        <i class="bx bx-edit"></i>
                                    </a>

                                    <form action="{{ route('teacher.destroy', $teacher->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ar-btn ar-btn-rej" style="padding:6px 9px;"
                                            onclick="return confirm('O\'chirilsinmi?')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:30px; text-align:center; color:#888;">
                                <i class="bx bx-group" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                                O'qituvchilar topilmadi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ar-pagination" style="margin-top:16px;">
            {{ $teachers->links() }}
        </div>

    </div>

</x-layouts.sidebar>