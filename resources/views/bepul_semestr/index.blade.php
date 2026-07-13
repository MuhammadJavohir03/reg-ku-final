<x-layouts.sidebar>
    <x-slot:title>Bo'limlar</x-slot:title>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <div>
                <div class="oz-title" style="margin:0;">
                    <i class="fas fa-book" style="color:#3C3489;"></i> Bo'limlar
                </div>
            </div>
            <a href="{{ route('bepul_semestr.create') }}" class="ar-btn ar-btn-ok">
                <i class="fas fa-plus"></i> Yangi qo'shish
            </a>
        </div>

        @forelse ($bepul_semestr as $bolim)
            <div
                style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:16px 18px; margin-bottom:12px; display:flex; align-items:center; gap:14px;">

                {{-- STATUS BAR --}}
                <div
                    style="width:4px; align-self:stretch; border-radius:4px; background:{{ $loop->index % 2 == 0 ? '#6366f1' : '#10b981' }};">
                </div>

                {{-- INFO --}}
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <h3 style="margin:0; font-size:15px; font-weight:600; color:#333;">{{ $bolim->nomi }}</h3>
                        <span
                            style="background:#EEEDFE; color:#3C3489; font-size:11px; font-weight:600; padding:3px 8px; border-radius:6px;">
                            ID: {{ $bolim->id }}
                        </span>
                    </div>
                    <div style="margin-top:4px; font-size:12px; color:#888;">
                        <i class="far fa-calendar-alt" style="margin-right:4px;"></i>
                        {{ $bolim->created_at->format('d.m.Y') }}
                    </div>
                </div>

                {{-- STATUS BADGE --}}
                <div>
                    @if ($bolim->status == 1)
                        <span class="ar-badge" style="background:#d4edda; color:#155724;">
                            <i class="fas fa-circle-check"></i> Active
                        </span>
                    @else
                        <span class="ar-badge" style="background:#fde2e2; color:#b91c1c;">
                            <i class="fas fa-circle-xmark"></i> Block
                        </span>
                    @endif
                </div>

                {{-- ACTIONS --}}
                <div style="display:flex; align-items:center; gap:6px;">
                    <a href="{{ route('bepul_semestr.edit', $bolim->id) }}" title="Tahrirlash"
                        style="background:#EEEDFE; color:#3C3489; padding:8px 10px; border-radius:8px; text-decoration:none; font-size:13px;">
                        <i class="fas fa-pencil-alt"></i>
                    </a>

                    <form action="{{ route('bepul_semestr.destroy', $bolim->id) }}" method="POST"
                        style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('O\'chirilsinmi?')"
                            style="background:#fde2e2; color:#b91c1c; border:none; padding:8px 10px; border-radius:8px; cursor:pointer; font-size:13px;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div
                style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:30px; text-align:center; color:#888;">
                Bo'limlar topilmadi.
            </div>
        @endforelse

    </div>
</x-layouts.sidebar>
