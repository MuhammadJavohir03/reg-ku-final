<x-layouts.sidebar>
    <x-slot:title>Kafedra mudirlari</x-slot:title>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
            <div class="oz-title" style="margin:0;">
                <i class="bx bx-id-card"></i> Kafedra mudirlari ({{ $mudirlar->total() }} ta)
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('subject.index') }}" class="ar-btn">
                    <i class="bx bx-arrow-back"></i> Fanlarga qaytish
                </a>
                <a href="{{ route('mudir.create') }}" class="ar-btn ar-btn-ok">
                    <i class="bx bx-plus"></i> Yangi mudir
                </a>
            </div>
        </div>

        <p style="color:#888; font-size:13px; margin-bottom:20px;">
            Har bir kafedra + o'quv yili juftligi uchun bitta mudir kiritiladi. Vedomost (baholash qaydnomasi)
            eksport qilinganda, fanning kafedrasi va o'quv yiliga mos mudir avtomatik ravishda
            "Kafedra mudiri" imzo qatoriga qo'yiladi.
        </p>

        <form action="{{ route('mudir.index') }}" method="GET" style="display:flex; gap:8px; margin-bottom:16px;">
            <input type="text" name="search" class="arizalar-search" style="width:100%; max-width:360px;"
                placeholder="Mudir, kafedra yoki o'quv yili bo'yicha qidirish..." value="{{ request('search') }}">
            <button type="submit" class="ar-btn ar-btn-ok"><i class="bx bx-search"></i> Qidirish</button>
            @if (request('search'))
                <a href="{{ route('mudir.index') }}" class="ar-btn">✕ Tozalash</a>
            @endif
        </form>

        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:60px;">ID</th>
                        <th>Mudir F.I.Sh.</th>
                        <th>Kafedra</th>
                        <th>O'quv yili</th>
                        <th style="width:170px;">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mudirlar as $m)
                        <tr>
                            <td class="ar-id">#{{ $m->id }}</td>
                            <td>
                                {{ $m->mudir }}
                                <div style="font-size:11px; color:#aaa; margin-top:2px;">
                                    Vedomostda: {{ \App\Models\Mudir::formatSignature($m->mudir) }}
                                </div>
                            </td>
                            <td>{{ $m->kafedra->nomi ?? '—' }}</td>
                            <td>{{ $m->oquvYili->nomi ?? '—' }}</td>
                            <td>
                                <a href="{{ route('mudir.edit', $m->id) }}" class="btn btn-sm btn-warning">Tahrirlash</a>
                                <form action="{{ route('mudir.destroy', $m->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Haqiqatan ham bu mudirni o\'chirmoqchimisiz?')">O'chirish</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:2rem; color:#888;">
                                Hali hech qanday mudir kiritilmagan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="justify-content-center m-3 pb-4">
            {{ $mudirlar->links() }}
        </div>

    </div>
</x-layouts.sidebar>