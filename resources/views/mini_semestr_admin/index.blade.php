<x-layouts.sidebar>

    <x-slot:title>
        Arizalar | Natijalari
    </x-slot:title>

    <div class="arizalar-toolbar">
        <form action="{{ route('mini_semestr_admin.index') }}" method="GET">
            <input type="text" name="search" class="arizalar-search"
                placeholder="Ism, email, guruh bo'yicha qidirish..." value="{{ request('search') }}">
        </form>
    </div>

    <div class="arizalar-table-wrap">
        <table class="arizalar-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>To'liq ismi</th>
                    <th>Guruh</th>
                    <th>Talaba ID</th>
                    <th>Email</th>
                    <th>Fan</th>
                    <th>Natijalar</th>
                    <th>Amallar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($talabalar as $talaba)
                    <tr>
                        <td class="ar-id">{{ $talaba->id }}</td>
                        <td>
                            <div class="ar-name-cell">
                                <div class="ar-avatar">
                                    {{ mb_substr($talaba->user->{'To‘liq_ismi'}, 0, 2) }}
                                </div>
                                <span>{{ $talaba->user->{'To‘liq_ismi'} }}</span>
                            </div>
                        </td>
                        <td>{{ $talaba->user->Guruh }}</td>
                        <td class="ar-talaba-id">{{ $talaba->user->Talaba_ID }}</td>
                        <td class="ar-email">{{ $talaba->user->email }}</td>
                        <td>{{ $talaba->subject->nomi }}</td>
                        <td>
                            <div class="ar-natija">
                                <span class="ar-natija-item">
                                    <span class="ar-natija-label">Umumiy</span>
                                    <span class="ar-natija-val">{{ $talaba->grade->umumiy ?? '-' }}</span>
                                </span>
                                <span class="ar-natija-item">
                                    <span class="ar-natija-label">Joriy</span>
                                    <span class="ar-natija-val">{{ $talaba->grade->joriy_oraliq ?? '-' }}</span>
                                </span>
                                <span class="ar-natija-item">
                                    <span class="ar-natija-label">Davomat</span>
                                    <span class="ar-natija-val">{{ $talaba->grade->davomat ?? '-' }}</span>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="ar-btn-group">
                                <form action="{{ route('free_semestr.destroy', $talaba->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="ar-btn ar-btn-rej"
                                        onclick="return confirm('O\'chirishni tasdiqlaysizmi?')">
                                        ✕ O'chirish
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($talabalar->hasPages())
            <div class="ar-pagination">
                {{-- Oldingi --}}
                @if ($talabalar->onFirstPage())
                    <span class="ar-page-btn ar-page-disabled">← Oldingi</span>
                @else
                    <a href="{{ $talabalar->previousPageUrl() }}&search={{ request('search') }}" class="ar-page-btn">←
                        Oldingi</a>
                @endif

                {{-- Sahifalar --}}
                @foreach ($talabalar->getUrlRange(1, $talabalar->lastPage()) as $page => $url)
                    @if ($page == $talabalar->currentPage())
                        <span class="ar-page-btn ar-page-active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}&search={{ request('search') }}"
                            class="ar-page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Keyingi --}}
                @if ($talabalar->hasMorePages())
                    <a href="{{ $talabalar->nextPageUrl() }}&search={{ request('search') }}"
                        class="ar-page-btn">Keyingi →</a>
                @else
                    <span class="ar-page-btn ar-page-disabled">Keyingi →</span>
                @endif
            </div>
        @endif
    </div>

</x-layouts.sidebar>
