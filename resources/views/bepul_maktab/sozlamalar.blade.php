<x-layouts.sidebar>
    <x-slot:title>{{ $subject->nomi }} — Sozlamalar</x-slot:title>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
            <a href="{{ route('bepul_maktab.fanlar', $bolim->id) }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i>
            </a>
            <div>
                <div class="oz-title" style="margin:0;">{{ $subject->nomi }}</div>
                <span style="font-size:12px; color:#888;">{{ $bolim->nomi }}</span>
            </div>
        </div>

        <form action="{{ route('bepul_maktab.saqlash', [$bolim->id, $subject->id]) }}" method="POST">
            @csrf

            {{-- BANK TANLASH --}}
            <div
                style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:18px; margin-bottom:12px;">
                <p style="font-size:13px; font-weight:600; color:#333; margin:0 0 12px;">
                    <i class="bx bx-data" style="color:#3C3489;"></i> Savol banki
                </p>

                <select id="bankSelect" name="bank_id" required>
                    <option value="">Bank tanlang...</option>

                    @foreach ($banklar as $b)
                        <option value="{{ $b->id }}" {{ $bank && $bank->id == $b->id ? 'selected' : '' }}>
                            {{ $b->nomi }} - ({{ $b->tur }}) - ({{ $b->questions_count }} ta savol)
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- SOZLAMALAR --}}
            <div class="soz-card">
                <p class="soz-card-title">
                    <i class="bx bx-cog" style="color:#3C3489;"></i> Test sozlamalari
                </p>

                <div class="soz-grid">

                    <div class="soz-field">
                        <label>
                            <i class="bx bx-time" style="color:#3C3489;"></i> Vaqt limiti
                        </label>
                        <div style="position:relative;">
                            <input type="number" name="vaqt_limit" class="soz-input"
                                value="{{ $bank->vaqt_limit ?? 20 }}" min="1" max="180" required>
                            <span
                                style="position:absolute; right:10px; top:50%; transform:translateY(-50%);
                    font-size:11px; color:#aaa;">daqiqa</span>
                        </div>
                    </div>

                    <div class="soz-field">
                        <label>
                            <i class="bx bx-revision" style="color:#3C3489;"></i> Urinish soni
                        </label>
                        <div style="position:relative;">
                            <input type="number" name="urinish" class="soz-input" value="{{ $bank->urinish ?? 1 }}"
                                min="1" max="10" required>
                            <span
                                style="position:absolute; right:10px; top:50%; transform:translateY(-50%);
                    font-size:11px; color:#aaa;">marta</span>
                        </div>
                    </div>

                    <div class="soz-field">
                        <label>
                            <i class="bx bx-shuffle" style="color:#3C3489;"></i> Random savol soni
                        </label>
                        <div style="position:relative;">
                            <input type="number" name="savollar_soni" class="soz-input"
                                value="{{ $bank->savollar_soni ?? 20 }}" min="1" required>
                            <span
                                style="position:absolute; right:10px; top:50%; transform:translateY(-50%);
                    font-size:11px; color:#aaa;">ta</span>
                        </div>
                    </div>

                    <div class="soz-field">
                        <label>
                            <i class="bx bx-star" style="color:#3C3489;"></i> To'g'ri javob uchun ball
                        </label>
                        <div style="position:relative;">
                            <input type="number" name="ball" class="soz-input"
                                value="{{ $bank ? $bank->questions()->value('ball') ?? 1 : 1 }}" min="1"
                                required>
                            <span
                                style="position:absolute; right:10px; top:50%; transform:translateY(-50%);
                    font-size:11px; color:#aaa;">ball</span>
                        </div>
                    </div>

                </div>

                <div class="soz-divider"></div>

                {{-- SANA VA VAQT --}}
                <p class="soz-card-title" style="margin-bottom:12px;">
                    <i class="bx bx-calendar" style="color:#3C3489;"></i> Test davri
                </p>

                <div class="soz-grid">

                    <div class="soz-field">
                        <label>
                            <i class="bx bx-calendar-plus" style="color:#10b981;"></i> Boshlanish
                        </label>
                        <div class="soz-dt-wrap">
                            <i class="bx bx-time-five" style="color:#10b981;"></i>
                            <input type="text" name="boshlanish_vaqti" id="boshlanish_vaqti"
                                class="soz-dt-input start" placeholder="Sana va vaqt tanlang..."
                                value="{{ $bank && $bank->boshlanish_vaqti ? \Carbon\Carbon::parse($bank->boshlanish_vaqti)->format('Y-m-d H:i') : '' }}">
                        </div>
                    </div>

                    <div class="soz-field">
                        <label>
                            <i class="bx bx-calendar-minus" style="color:#ef4444;"></i> Tugash
                        </label>
                        <div class="soz-dt-wrap">
                            <i class="bx bx-time-five" style="color:#ef4444;"></i>
                            <input type="text" name="tugash_vaqti" id="tugash_vaqti" class="soz-dt-input end"
                                placeholder="Sana va vaqt tanlang..."
                                value="{{ $bank && $bank->tugash_vaqti ? \Carbon\Carbon::parse($bank->tugash_vaqti)->format('Y-m-d H:i') : '' }}">
                        </div>
                    </div>

                </div>
                @php
                    $now = now();
                    $bosh = $bank && $bank->boshlanish_vaqti ? \Carbon\Carbon::parse($bank->boshlanish_vaqti) : null;
                    $tug = $bank && $bank->tugash_vaqti ? \Carbon\Carbon::parse($bank->tugash_vaqti) : null;

                    function formatUz($min)
                    {
                        $d = floor($min / 1440);
                        $h = floor(($min % 1440) / 60);
                        $m = $min % 60;

                        return trim(($d ? $d . ' kun ' : '') . ($h ? $h . ' soat ' : '') . ($m ? $m . ' daqiqa' : ''));
                    }
                @endphp

                <div class="mt-3"
                    style="
                        background:#fff;
                        border:1px solid #eee;
                        border-radius:12px;
                        padding:14px 16px;
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                    ">

                    @if (!$bosh || !$tug)
                        <div style="font-size:14px; color:#666;">
                            Test vaqti belgilanmagan
                        </div>
                    @elseif($now->lt($bosh))
                        @php $diff = $now->diffInMinutes($bosh); @endphp

                        <div>
                            <div style="font-size:13px; color:#888;">Boshlanishiga qoldi</div>
                            <div style="font-size:18px; font-weight:600; color:#3C3489;">
                                {{ formatUz($diff) }}
                            </div>
                        </div>
                    @elseif($now->between($bosh, $tug))
                        @php $diff = $now->diffInMinutes($tug); @endphp

                        <div>
                            <div style="font-size:13px; color:#888;">Test ochiq — tugashiga qoldi</div>
                            <div style="font-size:18px; font-weight:600; color:#1f7a1f;">
                                {{ formatUz($diff) }}
                            </div>
                        </div>
                    @else
                        <div style="font-size:14px; color:#b91c1c; font-weight:600;">
                            Test tugagan
                        </div>
                    @endif

                </div>

            </div>

    </div>

    {{-- BANK MA'LUMOTLARI --}}
    @if ($bank)
        <div
            style="background:#EEEDFE; border-radius:12px; padding:14px 18px; margin-bottom:12px; font-size:13px; color:#3C3489;">
            <strong>Hozirgi bank:</strong> {{ $bank->nomi }} —
            {{ $bank->questions()->count() }} ta savol,
            {{ $bank->vaqt_limit }} daqiqa,
            {{ $bank->urinish }} urinish,
            {{ $bank->savollar_soni }} ta random savol
            {{ $bank->boshlanish_vaqti ? 'Boshlanish: ' . \Carbon\Carbon::parse($bank->boshlanish_vaqti)->format('d.m.Y - (H:i)') : 'Boshlanish vaqti belgilanmagan' }},
            {{ $bank->tugash_vaqti ? 'Tugash: ' . \Carbon\Carbon::parse($bank->tugash_vaqti)->format('d.m.Y - (H:i)') : 'Tugash vaqti belgilanmagan' }}
        </div>
    @endif

    <button type="submit" class="mb-2 ar-btn ar-btn-ok" style="width:100%; justify-content:center; padding:.7rem;">
        <i class="bx bx-save"></i> Saqlash
    </button>

    </form>

    <div style="display:flex; gap:8px; margin-bottom:12px;">
        <form action="{{ route('bepul_maktab.all_status', [$bolim->id, $subject->id]) }}" method="POST">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="1">
            <button type="submit" class="ar-btn ar-btn-ok">
                <i class="bx bx-check-circle"></i> Hammasini ochish
            </button>
        </form>

        <form action="{{ route('bepul_maktab.all_status', [$bolim->id, $subject->id]) }}" method="POST">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="0">
            <button type="submit" class="ar-btn ar-btn-rej"
                onclick="return confirm('Barcha talabalar bloklansinmi?')">
                <i class="bx bx-block"></i> Hammasini bloklash
            </button>
        </form>
    </div>

    {{-- TALABALAR --}}
    <div style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:18px; margin-top:16px;">
        <p style="font-size:13px; font-weight:600; color:#333; margin:0 0 12px;">
            <i class="bx bx-group" style="color:#3C3489;"></i>
            Talabalar — {{ $talabalar->total() }} ta ishtirokchi
        </p>
        @php
            $sessions = \App\Models\TestSession::where('bank_id', $bank?->id)->get();

            // Har bir talaba bo'yicha guruhlab, faqat eng oxirgi sessiyasini olamiz
$latestSessions = $sessions->groupBy('user_id')->map(function ($userSessions) {
    return $userSessions->sortByDesc('created_at')->first();
});

$boshlamagan = $talabalar->total() - $latestSessions->count();
$jarayonda = $latestSessions->where('status', 'active')->count();
$yakunlagan = $latestSessions->where('status', 'finished')->count();
        @endphp

        @if (!$bank)
            @php
                $boshlamagan = $talabalar->total();
                $jarayonda = 0;
                $yakunlagan = 0;
            @endphp
        @endif

        <div style="display:flex; gap:10px; margin-bottom:15px;">

            <div style="flex:1; background:#f5f5f5; padding:10px; border-radius:10px;">
                <div style="font-size:20px; font-weight:700;">{{ $boshlamagan }}</div>
                <div style="font-size:12px; color:#888;">Boshlamagan</div>
            </div>

            <div style="flex:1; background:#fff3cd; padding:10px; border-radius:10px;">
                <div style="font-size:20px; font-weight:700; color:#856404;">
                    {{ $jarayonda }}
                </div>
                <div style="font-size:12px; color:#856404;">Jarayonda</div>
            </div>

            <div style="flex:1; background:#eaf3de; padding:10px; border-radius:10px;">
                <div style="font-size:20px; font-weight:700; color:#27500A;">
                    {{ $yakunlagan }}
                </div>
                <div style="font-size:12px; color:#27500A;">Yakunlagan</div>
            </div>

        </div>


        {{-- ===================== TABLE ===================== --}}
        <div class="arizalar-table-wrap">
            <table class="arizalar-table">

                <thead>
                    <tr>
                        <th style="width:50px;">№</th>
                        <th>Talaba</th>
                        <th>Email</th>
                        <th>Kurs</th>
                        <th>Guruh</th>
                        <th>Test holati</th>
                        <th>Ball</th>
                        <th style="width:160px;">Amal</th> {{-- YANGI --}}
                    </tr>
                </thead>

                <tbody>

                    @forelse ($talabalar as $ariza)
                        @php
                            $session = \App\Models\TestSession::where('user_id', $ariza->user_id)
                                ->where('bank_id', $bank?->id)
                                ->latest()
                                ->first();
                        @endphp

                        <tr>

                            {{-- № --}}
                            <td class="ar-id text-center">
                                {{ $talabalar->firstItem() + $loop->index }}
                            </td>

                            {{-- TALABA --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="ar-avatar">
                                        {{ mb_substr($ariza->user['To‘liq_ismi'] ?? 'N', 0, 2) }}
                                    </div>

                                    <div>
                                        <div style="font-weight:600;">
                                            {{ $ariza->user['To‘liq_ismi'] ?? '—' }}
                                        </div>

                                        <small style="color:#888;">
                                            {{ $ariza->user->email ?? '—' }}
                                        </small>
                                    </div>
                                </div>
                            </td>

                            {{-- KURS --}}
                            <td class="text-center">
                                {{ $ariza->user->Kurs ?? '—' }}-kurs
                            </td>

                            {{-- GURUH --}}
                            <td class="text-center">
                                {{ $ariza->user->Guruh ?? '—' }}
                            </td>

                            {{-- NATIJALAR --}}
                            <td class="text-center">
                                <a href="{{ route('bepul_maktab.talaba.sessions', [$bolim->id, $subject->id, $ariza->user_id]) }}"
                                    class="btn btn-sm"
                                    style="background:#3C3489;color:#fff;border-radius:8px;padding:6px 12px;text-decoration:none;">
                                    <i class="fas fa-chart-line"></i>
                                    Natijalar
                                </a>
                            </td>

                            {{-- TEST HOLATI --}}
                            <td class="text-center">
                                @if (!$session)
                                    <span class="ar-badge" style="background:#ececec;color:#666;">
                                        Boshlamagan
                                    </span>
                                @elseif($session->status == 'active')
                                    <span class="ar-badge" style="background:#fff3cd;color:#856404;">
                                        Jarayonda
                                    </span>
                                @elseif($session->status == 'finished')
                                    <span class="ar-badge" style="background:#d4edda;color:#155724;">
                                        Yakunlandi
                                    </span>
                                @else
                                    <span class="ar-badge" style="background:#fde2e2;color:#b91c1c;">
                                        Tugagan
                                    </span>
                                @endif
                            </td>


                            {{-- BALL --}}
                            <td style="font-weight:600; font-size:13px;">
                                {{ $session->ball ?? 0 }}
                            </td>

                            {{-- AMAL --}}
                            <td>
                                <div style="display:flex; gap:6px; align-items:center;">

                                    {{-- Urinishlarni ko'rish --}}
                                    @if ($session)
                                        <a href="{{ route('bepul_maktab.talaba.sessions', [$bolim->id, $subject->id, $ariza->user_id]) }}"
                                            title="Urinishlarni ko'rish"
                                            style="background:#EEEDFE; color:#3C3489; padding:6px 8px;
                                            border-radius:8px; text-decoration:none; font-size:13px;">
                                            <i class="bx bx-history"></i>
                                        </a>
                                    @else
                                        <span style="color:#ccc; padding:6px 8px;" title="Hali test yechmagan">
                                            <i class="bx bx-history"></i>
                                        </span>
                                    @endif

                                    {{-- Individual block/active --}}
                                    <button type="button" class="status-toggle-btn" data-id="{{ $ariza->id }}"
                                        data-status="{{ $ariza->status ? 1 : 0 }}"
                                        data-url="{{ route('bepul_maktab.status.toggle', $ariza->id) }}"
                                        style="background:{{ $ariza->status ? '#10b981' : '#ef4444' }};
                                            color:#fff; border:none; padding:6px 10px;
                                            border-radius:8px; cursor:pointer; font-size:12px;">
                                        <i class="bx {{ $ariza->status ? 'bx-check-circle' : 'bx-block' }}"></i>
                                        <span class="status-label">{{ $ariza->status ? 'Active' : 'Block' }}</span>
                                    </button>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" style="padding:30px;text-align:center;color:#888;">
                                Talabalar topilmadi.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <div class="ar-pagination" style="margin-top:12px;">
            {{ $talabalar->links() }}
        </div>
    </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script>
        new TomSelect("#bankSelect", {
            create: false,
            maxOptions: 100,
            placeholder: "Savol bankini yozib qidiring...",
            searchField: ["text"],
            allowEmptyOption: true
        });
    </script>

    <script>
        flatpickr("#boshlanish_vaqti", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            locale: "uz",
            minDate: "today",
            onChange: function(selectedDates) {
                // Tugash vaqti boshlanishdan keyin bo'lsin
                tugashPicker.set('minDate', selectedDates[0]);
            }
        });

        var tugashPicker = flatpickr("#tugash_vaqti", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            locale: "uz",
        });
    </script>

    <script>
        function formatTime(diff) {
            if (diff <= 0) return "0s";

            let total = Math.floor(diff / 1000);

            let days = Math.floor(total / 86400);
            total %= 86400;

            let hours = Math.floor(total / 3600);
            total %= 3600;

            let minutes = Math.floor(total / 60);
            let seconds = total % 60;

            return `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }

        @if ($bosh && $now->lt($bosh))
            let startTime = new Date("{{ $bosh }}").getTime();

            setInterval(() => {
                let now = new Date().getTime();
                let diff = startTime - now;
                document.getElementById("countdown-start").innerText = formatTime(diff);
            }, 1000);
        @endif


        @if ($bosh && $tug && $now->between($bosh, $tug))
            let endTime = new Date("{{ $tug }}").getTime();

            setInterval(() => {
                let now = new Date().getTime();
                let diff = endTime - now;
                document.getElementById("countdown-end").innerText = formatTime(diff);
            }, 1000);
        @endif
    </script>

    <script>
        document.querySelectorAll('.status-toggle-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const url = this.dataset.url;
                const btnEl = this;

                btnEl.disabled = true;

                fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Server xatosi');
                        return res.json();
                    })
                    .then(data => {
                        const isActive = data.status;

                        btnEl.dataset.status = isActive ? '1' : '0';
                        btnEl.style.background = isActive ? '#10b981' : '#ef4444';

                        const icon = btnEl.querySelector('i');
                        icon.className = 'bx ' + (isActive ? 'bx-check-circle' : 'bx-block');

                        btnEl.querySelector('.status-label').textContent = isActive ? 'Active' :
                            'Block';
                    })
                    .catch(() => {
                        alert('Xatolik yuz berdi, qayta urinib ko\'ring!');
                    })
                    .finally(() => {
                        btnEl.disabled = false;
                    });
            });
        });
    </script>
</x-layouts.sidebar>
