<x-layouts.sidebar>
    <x-slot:title>Fan natijalari</x-slot:title>

    <div class="oz-wrap">

        <div
            style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
            <div>
                <div style="font-size:11px; color:#aaa; margin-bottom:2px;">
                    {{ $grades->first()?->subject?->category?->nomi ?? 'Yo\'nalish topilmadi' }}
                </div>
                <div class="oz-title" style="margin:0;">
                    {{ $grades->first()?->subject?->nomi ?? 'Fan topilmadi' }}
                </div>
            </div>

            <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">

                <form action="{{ url()->current() }}" method="GET" style="display:flex; align-items:center; gap:6px;">
                    <div style="position:relative;">
                        <i class="bx bx-search"
                            style="position:absolute; left:10px; top:50%;
                            transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                        <input type="text" name="search" class="arizalar-search"
                            style="padding-left:34px; width:200px;" placeholder="Talaba ismi..."
                            value="{{ request('search') }}">
                    </div>
                    @if (request('search'))
                        <a href="{{ url()->current() }}" class="ar-btn ar-btn-rej">✕</a>
                    @endif
                </form>

                <form action="{{ route('grades.clear', $grades->first()?->subject_id ?? 0) }}" method="POST"
                    style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="ar-btn ar-btn-rej"
                        onclick="return confirm('Barcha baholar ochirisinmi?')">
                        <i class="bx bx-trash"></i> Tozalash
                    </button>
                </form>

                <a href="{{ route('subject.index') }}" class="ar-btn">
                    <i class="bx bx-arrow-back"></i> Orqaga
                </a>

            </div>
        </div>

        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:10px;">№</th>
                        <th>Talaba</th>
                        <th style="width:100px;">Guruh</th>
                        <th style="width:80px; text-align:center;">Joriy</th>
                        <th style="width:80px; text-align:center;">Oraliq</th>
                        <th style="width:80px; text-align:center;">Reyting</th>
                        <th style="width:80px; text-align:center;">Yakuniy</th>
                        <th style="width:110px; text-align:center;">Umumiy ball</th>
                        <th style="width:140px;">Davomat</th>
                        <th style="width:140px;">Bepul bormi ?</th>
                        @if (in_array(auth()->user()?->email, ['javohir8386@gmail.com', 'paulwalker3637@gmail.com']))
                            <th style="width:80px;">Amallar</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($grades as $index => $grade)
                        <tr data-grade-id="{{ $grade->id }}">
                            <td class="ar-id">
                                {{ $grades->firstItem() + $index }}
                            </td>

                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div class="ar-avatar">
                                        {{ mb_substr($grade->user['To‘liq_ismi'] ?? 'N', 0, 2) }}
                                    </div>
                                    <span style="font-size:13px; font-weight:500;">
                                        {{ $grade->user['To‘liq_ismi'] ?? 'Noma\'lum' }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                <span
                                    style="background:#f5f5f5; color:#555; padding:3px 10px;
                                    border-radius:6px; font-size:12px; font-weight:600;">
                                    {{ $grade->user->Guruh ?? '—' }}
                                </span>
                            </td>

                            <td style="text-align:center; font-size:13px; font-weight:600; color:#333;">
                                @if (auth()->user()?->email === 'javohir8386@gmail.com')
                                    <input type="number" step="0.01" min="0" class="grade-input"
                                        data-field="joriy_baho" value="{{ $grade->joriy_baho }}"
                                        style="width:60px; text-align:center; border:1px solid #ddd; border-radius:6px; padding:3px 4px; font-weight:600; font-size:13px;">
                                @else
                                    {{ $grade->joriy_baho }}
                                @endif
                            </td>

                            <td style="text-align:center; font-size:13px; font-weight:600; color:#333;">
                                @if (auth()->user()?->email === 'javohir8386@gmail.com')
                                    <input type="number" step="0.01" min="0" class="grade-input"
                                        data-field="oraliq_baho" value="{{ $grade->oraliq_baho }}"
                                        style="width:60px; text-align:center; border:1px solid #ddd; border-radius:6px; padding:3px 4px; font-weight:600; font-size:13px;">
                                @else
                                    {{ $grade->oraliq_baho }}
                                @endif
                            </td>

                            <td style="text-align:center; font-size:13px; font-weight:600;">
                                <span class="js-reyting"
                                    style="color: {{ $grade->joriy_oraliq >= 20 ? '#27500A' : '#ff0000' }}">
                                    {{ $grade->joriy_oraliq }}
                                </span>
                            </td>

                            <td style="text-align:center; font-size:13px; font-weight:600; color:#333;">
                                @if (auth()->user()?->email === 'javohir8386@gmail.com')
                                    <input type="number" step="0.01" min="0" class="grade-input"
                                        data-field="yakuniy_baho" value="{{ $grade->yakuniy_baho }}"
                                        style="width:60px; text-align:center; border:1px solid #ddd; border-radius:6px; padding:3px 4px; font-weight:600; font-size:13px;">
                                @else
                                    {{ $grade->yakuniy_baho }}
                                @endif
                            </td>

                            <td style="text-align:center;">
                                @if ($grade->umumiy > 70)
                                    <span class="ar-badge ar-badge-ok js-umumiy"
                                        style="font-size:13px; font-weight:700;">
                                        {{ $grade->umumiy }}
                                    </span>
                                @elseif($grade->umumiy >= 60)
                                    <span class="ar-badge js-umumiy"
                                        style="background:#fff3cd; color:#ff0000; font-size:13px; font-weight:700;">
                                        {{ $grade->umumiy }}
                                    </span>
                                @else
                                    <span class="ar-badge ar-badge-rej js-umumiy"
                                        style="font-size:13px; font-weight:700;">
                                        {{ $grade->umumiy }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span
                                        style="font-size:12px; font-weight:600; min-width:36px;
                                        color: {{ $grade->davomat > 33 ? '#791F1F' : ($grade->davomat > 15 ? '#856404' : '#27500A') }}">
                                        {{ $grade->davomat }}%
                                    </span>
                                    <div
                                        style="flex:1; height:6px; background:#f0f0f0; border-radius:4px; overflow:hidden;">
                                        <div
                                            style="height:100%; border-radius:4px;
                                            width:{{ min($grade->davomat, 100) }}%;
                                            background:{{ $grade->davomat > 33 ? '#ef4444' : ($grade->davomat > 15 ? '#f59e0b' : '#10b981') }};
                                            transition:width 0.3s;">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="ar-badge js-umumiy"
                                    style="background:#ffffff; color:#000000; font-size:13px; font-weight:700;">
                                    {{ $grade->bepul ? 'Ha' : 'Yo‘q' }}
                                </span>
                            </td>
                            @if (in_array(auth()->user()?->email, ['javohir8386@gmail.com', 'paulwalker3637@gmail.com']))
                                <td>
                                    <form action="{{ route('grades.destroy', $grade->id) }}" method="POST">
                                        @csrf @method('DELETE')

                                        <button type="submit" class="ar-btn ar-btn-rej"
                                            onclick="return confirm('Barcha baholar ochirisinmi?')">
                                            <i class="bx bx-trash"></i> O'chirish
                                        </button>
                                    </form>
                                </td>
                            @endif

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:2.5rem; color:#888;">
                                <i class="bx bx-inbox" style="font-size:36px; display:block; margin-bottom:8px;"></i>
                                Hech qanday natija topilmadi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ar-pagination" style="margin-top:16px;">
            {{ $grades->withQueryString()->links() }}
        </div>

    </div>

    @if (auth()->user()?->email === 'javohir8386@gmail.com')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const csrfToken = '{{ csrf_token() }}';
                const urlTemplate = "{{ route('grades.update', ['grade' => '__ID__']) }}";

                function num(row, field) {
                    return parseFloat(row.querySelector(`[data-field="${field}"]`).value) || 0;
                }

                function paintReyting(row, reyting) {
                    const el = row.querySelector('.js-reyting');
                    el.textContent = reyting;
                    el.style.color = reyting >= 20 ? '#27500A' : '#ff0000';
                }

                function paintUmumiy(row, umumiy) {
                    const el = row.querySelector('.js-umumiy');
                    el.textContent = umumiy;
                    el.className = 'ar-badge js-umumiy';
                    el.style.background = '';
                    el.style.color = '';
                    if (umumiy > 70) {
                        el.classList.add('ar-badge-ok');
                    } else if (umumiy >= 60) {
                        el.style.background = '#fff3cd';
                        el.style.color = '#ff0000';
                    } else {
                        el.classList.add('ar-badge-rej');
                    }
                }

                function recalcRow(row) {
                    const joriy = num(row, 'joriy_baho');
                    const oraliq = num(row, 'oraliq_baho');
                    const yakuniy = num(row, 'yakuniy_baho');

                    paintReyting(row, joriy + oraliq);
                    paintUmumiy(row, joriy + oraliq + yakuniy);
                }

                function saveRow(row) {
                    const id = row.dataset.gradeId;
                    const url = urlTemplate.replace('__ID__', id);

                    fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                joriy_baho: num(row, 'joriy_baho'),
                                oraliq_baho: num(row, 'oraliq_baho'),
                                yakuniy_baho: num(row, 'yakuniy_baho'),
                            }),
                        })
                        .then(function(res) {
                            if (!res.ok) throw new Error('Saqlashda xatolik: ' + res.status);
                            return res.json();
                        })
                        .then(function(data) {
                            if (data.success) {
                                paintReyting(row, data.joriy_oraliq);
                                paintUmumiy(row, data.umumiy);
                            }
                        })
                        .catch(function(err) {
                            console.error(err);
                            alert('Saqlashda xatolik yuz berdi. Sahifani yangilab, qayta urinib ko\'ring.');
                        });
                }

                document.querySelectorAll('.grade-input').forEach(function(input) {
                    input.addEventListener('input', function() {
                        recalcRow(this.closest('tr'));
                    });
                    input.addEventListener('change', function() {
                        saveRow(this.closest('tr'));
                    });
                });
            });
        </script>
    @endif
</x-layouts.sidebar>
