<x-layouts.sidebar title="Bosh sahifa">

    <x-slot:title>
        Dashboard
    </x-slot:title>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <div class="oz-title" style="margin:0;">
                <i class="bx bx-megaphone" style="color:#3C3489;"></i> E'lonlar
            </div>

            @if (auth()->user()?->role === 'admin')
                <a href="{{ route('elons.create') }}" class="ar-btn ar-btn-ok">
                    <i class="bx bx-plus"></i> E'lon yaratish
                </a>
            @endif
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:16px;">

            @forelse ($elons as $elon)
                <div style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; overflow:hidden;">

                    <div style="position:relative; aspect-ratio:16/9; background:#f5f5f5;">
                        <img src="{{ asset('storage/' . ($elon->photo ?? 'elons/default.png')) }}"
                            alt="Post" style="width:100%; height:100%; object-fit:cover; display:block;">

                        <div style="position:absolute; top:10px; left:10px;">
                            <span class="ar-badge" style="background:rgba(255,255,255,.9); color:#333;">
                                <i class="bx bx-time" style="color:#f5a623;"></i>
                                {{ $elon->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <div style="padding:16px;">
                        <div style="font-size:11px; font-weight:700; color:#3C3489; text-transform:uppercase; letter-spacing:.5px;">
                            {{ $elon->category->nomi ?? 'Umumiy' }}
                        </div>

                        <h5 style="margin:6px 0 10px; font-weight:700; color:#222;">
                            {{ $elon->title }}
                        </h5>

                        <p style="font-size:13px; color:#888; margin-bottom:16px;">
                            {{ $elon->short_content }}
                        </p>

                        <div style="display:flex; align-items:center; justify-content:space-between; padding-top:12px; border-top:1px solid #f0f0f0;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div class="ar-avatar" style="width:28px; height:28px; font-size:12px;">
                                    <i class="bx bx-user"></i>
                                </div>
                                <span style="font-size:13px; font-weight:600; color:#555;">{{ $elon->kurs }} - Kurs</span>
                            </div>

                            <a href="{{ route('elons.show', $elon->id) }}" class="ar-btn ar-btn-ok" style="padding:6px 14px; font-size:13px;">
                                Ko'rish
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column:1/-1; background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:30px; text-align:center; color:#888;">
                    E'lonlar topilmadi.
                </div>
            @endforelse

        </div>

        <div class="ar-pagination" style="margin-top:16px;">
            {{ $elons->links() }}
        </div>

    </div>

</x-layouts.sidebar>