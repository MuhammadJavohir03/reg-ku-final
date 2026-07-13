<x-layouts.sidebar>
    <x-slot:title>Ariza boshqaruvi</x-slot:title>

    <div class="arizalar-table-wrap">
        <table class="arizalar-table">
            <thead>
                <tr>
                    <th>Ariza turi</th>
                    <th>Talabalar uchun holati</th>
                    <th>Amal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Bepul imkoniyatlar</td>
                    <td>
                        @if(Cache::get('sidebar_free_semestr', true))
                            <span class="ar-badge ar-badge-ok">✓ Ochiq</span>
                        @else
                            <span class="ar-badge ar-badge-rej">✕ Yopiq</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('sidebar_boshqaruv.toggle', 'free_semestr') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="ar-btn {{ Cache::get('sidebar_free_semestr', true) ? 'ar-btn-rej' : 'ar-btn-ok' }}">
                                {{ Cache::get('sidebar_free_semestr', true) ? '⏸ Yopish' : '▶ Ochish' }}
                            </button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td>Mini semestr</td>
                    <td>
                        @if(Cache::get('sidebar_mini_semestr', true))
                            <span class="ar-badge ar-badge-ok">✓ Ochiq</span>
                        @else
                            <span class="ar-badge ar-badge-rej">✕ Yopiq</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('sidebar_boshqaruv.toggle', 'mini_semestr') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="ar-btn {{ Cache::get('sidebar_mini_semestr', true) ? 'ar-btn-rej' : 'ar-btn-ok' }}">
                                {{ Cache::get('sidebar_mini_semestr', true) ? '⏸ Yopish' : '▶ Ochish' }}
                            </button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</x-layouts.sidebar>