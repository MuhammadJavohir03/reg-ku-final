<x-layouts.sidebar>

    <x-slot:title>
        Adminlar Ro'yxati
    </x-slot:title>

    <link rel="stylesheet" href="{{ asset('css/jadvallar.css') }}">

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
            <div class="oz-title" style="margin:0;">Adminlar Ro'yxati</div>

            @if (auth()->check() && in_array(auth()->user()->email, ['javohir8386@gmail.com', 'samiyusuf@gmail.com']))
                <a href="{{ route('admins.create') }}" class="ar-btn ar-btn-ok">
                    <i class="bx bx-plus"></i> Yangi Admin yaratish
                </a>
            @endif
        </div>

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
                    @forelse ($admins as $admin)
                        <tr>
                            <td class="ar-id">
                                #{{ $admin->id }}
                            </td>

                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div class="ar-avatar">
                                        {{ mb_substr($admin['To‘liq_ismi'] ?? 'N', 0, 2) }}
                                    </div>
                                    <span style="font-weight:500; font-size:13px;">
                                        {{ $admin['To‘liq_ismi'] }}
                                    </span>
                                </div>
                            </td>

                            <td style="font-size:13px; color:#888;">{{ $admin->email }}</td>

                            <td>
                                <span class="ar-badge ar-badge-accent">Admin</span>
                            </td>

                            <td style="text-align:center;">
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <a href="{{ route('admins.edit', $admin->id) }}" class="ar-btn" title="Tahrirlash" style="padding:6px 9px;">
                                        <i class="bx bx-edit"></i>
                                    </a>

                                    <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" style="display:inline;">
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
                                Adminlar topilmadi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ar-pagination" style="margin-top:16px;">
            {{ $admins->links() }}
        </div>

    </div>

</x-layouts.sidebar>