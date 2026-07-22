<x-layouts.sidebar>

    <x-slot:title>
        Adminlar Ro'yxati
    </x-slot:title>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <div class="oz-title" style="margin:0;">
                <i class="bx bx-group" style="color:#3C3489;"></i> Adminlar Ro'yxati
            </div>
            @if (auth()->check() && in_array(auth()->user()->email, ['javohir8386@gmail.com', 'samiyusuf@gmail.com']))
                <a href="{{ route('admins.create') }}" class="ar-btn ar-btn-ok">
                    <i class="bx bx-plus"></i> Yangi Admin yaratish
                </a>
            @endif
        </div>

        <div style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:0; overflow:hidden;">
            <div class="arizalar-table-wrap">
                <table class="arizalar-table">
                    <thead>
                        <tr>
                            <th style="width:70px;">ID</th>
                            <th>To'liq Ismi</th>
                            <th>Email</th>
                            <th>Roli</th>
                            <th style="width:120px;" class="text-center">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($admins as $admin)
                            <tr>
                                <td class="text-muted small">
                                    <i class="bx bx-user"></i> {{ $admin->id }}
                                </td>

                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div class="ar-avatar">
                                            {{ mb_substr($admin['To‘liq_ismi'] ?? 'N', 0, 2) }}
                                        </div>
                                        <div style="font-weight:600;">
                                            {{ $admin['To‘liq_ismi'] }}
                                        </div>
                                    </div>
                                </td>

                                <td>{{ $admin->email }}</td>

                                <td>
                                    <span class="ar-badge" style="background:#EEEDFE; color:#3C3489;">
                                        {{ $admin->role }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div style="display:flex; gap:6px; justify-content:center;">
                                        <a href="{{ route('admins.edit', $admin->id) }}" title="Tahrirlash"
                                            style="background:#EEEDFE; color:#3C3489; padding:8px 10px; border-radius:8px; text-decoration:none; font-size:13px;">
                                            <i class="bx bx-edit"></i>
                                        </a>

                                        <form action="{{ route('admins.destroy', $admin->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('O\'chirilsinmi?')"
                                                style="background:#fde2e2; color:#b91c1c; border:none; padding:8px 10px; border-radius:8px; cursor:pointer; font-size:13px;">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:30px; text-align:center; color:#888;">
                                    Adminlar topilmadi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ar-pagination" style="margin-top:12px;">
            {{ $admins->links() }}
        </div>

    </div>

</x-layouts.sidebar>
