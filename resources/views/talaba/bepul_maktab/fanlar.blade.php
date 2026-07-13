<x-layouts.sidebar>
    <x-slot:title>{{ $bolim->nomi }} — Fanlar</x-slot:title>

    <div class="oz-wrap">

        <div class="oz-toolbar">
            <div>
                <a href="{{ route('talaba.bepul_maktab.index') }}" class="ar-btn">← Orqaga</a>
            </div>
            <span class="oz-title">{{ $bolim->nomi }}</span>
        </div>

        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Fan nomi</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fanlar as $i => $fan)
                        <tr style="cursor:pointer" onclick="window.location='{{ route('talaba.bepul_maktab.show', [$bolim->id, $fan->id]) }}'">
                            <td class="ar-id">{{ $i + 1 }}</td>
                            <td>{{ $fan->nomi }}</td>
                            <td>
                                <a href="{{ route('talaba.bepul_maktab.show', [$bolim->id, $fan->id]) }}" class="ar-btn ar-btn-ok">
                                    Testga kirish →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center; padding:2rem; color:#888;">
                                Sizga tegishli fan mavjud emas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-layouts.sidebar>