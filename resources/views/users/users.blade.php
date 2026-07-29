<x-layouts.sidebar>

    <x-slot:title>
        Foydalanuvchilar Ro'yxati
    </x-slot:title>

    <link rel="stylesheet" href="{{ asset('css/jadvallar.css') }}">

    <div class="oz-wrap">

        <div
            style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
            <div class="oz-title" style="margin:0;">Foydalanuvchilar Ro'yxati</div>
        </div>

        <div class="arizalar-toolbar"
            style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px;">

            <form action="{{ route('users.index') }}" method="GET"
                style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <div style="position:relative;">
                    <i class="bx bx-search"
                        style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                    <input type="text" name="search" class="arizalar-search" style="width:260px; padding-left:34px;"
                        placeholder="Ism, ID yoki Email bo'yicha qidirish..." value="{{ request('search') }}">
                </div>
                @if (request('search'))
                    <a href="{{ route('users.index') }}" class="ar-btn ar-btn-rej">✕</a>
                @endif
                <select name="page_size" class="arizalar-search" style="width:90px;" onchange="this.form.submit()">
                    <option value="20" {{ request('page_size') == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('page_size') == 50 ? 'selected' : '' }}>50</option>
                    <option value="70" {{ request('page_size') == 70 ? 'selected' : '' }}>70</option>
                    <option value="100" {{ request('page_size') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </form>

            <form id="importForm" enctype="multipart/form-data" style="display:flex; align-items:center; gap:8px;">
                @csrf
                <input type="file" name="file" id="fileInput" class="arizalar-search" style="width:220px;"
                    required>
                <button type="submit" id="uploadBtn" class="ar-btn ar-btn-ok">
                    <i class="bx bx-import"></i> <span id="btnText">Import</span>
                </button>
                <div id="progressContainer" style="display:none; position:relative; width:44px; height:44px;">
                    <svg width="44" height="44" style="transform:rotate(-90deg)">
                        <circle cx="22" cy="22" r="18" fill="none" stroke="#edecf5" stroke-width="4" />
                        <circle id="progressBar" cx="22" cy="22" r="18" fill="none" stroke="#7C6CF5"
                            stroke-width="4" stroke-dasharray="113" stroke-dashoffset="113"
                            style="transition:stroke-dashoffset 0.3s;" />
                    </svg>
                    <span id="progressPercent"
                        style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:10px;font-weight:700;color:#5B4FE0;">0%</span>
                </div>
            </form>
        </div>

        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th style="width:80px;">Talaba ID</th>
                        <th style="width:180px;">Email</th>
                        <th style="width:280px;">To'liq ismi</th>
                        <th style="width:110px;">Roli</th>
                        <th style="width:90px;">Kurs</th>
                        <th style="width:120px;">Guruh</th>
                        <th style="width:80px;">GPA</th>
                        <th style="width:170px;">Amallar</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($users as $user)
                        <tr style="cursor:pointer;" onclick="window.location='{{ route('users.edit', $user->id) }}'">

                            <td class="ar-id">
                                #{{ $user->id }}
                            </td>

                            <td>
                                {{ $user->Talaba_ID }}
                            </td>

                            <td style="font-size:13px; color:#888;" title="{{ $user->email }}">
                                {{ $user->email }}
                            </td>

                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div class="ar-avatar">
                                        {{ mb_substr($user['To‘liq_ismi'], 0, 2) }}
                                    </div>
                                    <span style="font-size:13px; font-weight:500;">
                                        {{ $user['To‘liq_ismi'] }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                @if ($user->role == 'admin')
                                    <span class="ar-badge ar-badge-accent">Admin</span>
                                @elseif($user->role == 'teacher')
                                    <span class="ar-badge ar-badge-ok">O'qituvchi</span>
                                @else
                                    <span class="ar-badge ar-badge-muted">Talaba</span>
                                @endif
                            </td>

                            <td style="font-size:13px; color:#888;">
                                @if ($user->role == 'admin')
                                    -
                                @else
                                    {{ $user->Kurs }}-Kurs
                                @endif
                            </td>

                            <td style="font-size:13px; color:#888;">
                                {{ $user->Guruh }}
                            </td>

                            <td style="font-weight:700; color:#10b981;">
                                {{ $user->GPA }}
                            </td>

                            <td onclick="event.stopPropagation()">
                                <div style="display:flex; gap:6px;">

                                    <a href="{{ route('users.edit', $user->id) }}" class="ar-btn"
                                        style="padding:6px 10px;">
                                        <i class="bx bx-edit"></i> Tahrirlash
                                    </a>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="ar-btn ar-btn-rej" style="padding:6px 10px;"
                                            onclick="return confirm('O\'chirishni tasdiqlaysizmi?')">
                                            <i class="bx bx-trash"></i> O'chirish
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="ar-pagination" style="margin-top:16px;">
            {{ $users->withQueryString()->links() }}
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#importForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var $btn = $('#uploadBtn');
                var $btnText = $('#btnText');
                var $container = $('#progressContainer');
                var $circle = $('#progressBar');
                var $pct = $('#progressPercent');
                var circumference = 113;
                var currentPct = 0;
                var fakeInterval = null;

                function setProgress(pct) {
                    var offset = circumference - (pct / 100) * circumference;
                    $circle.attr('stroke-dashoffset', offset);
                    $pct.text(pct + '%');
                }

                $btn.prop('disabled', true);
                $btnText.text('...');
                $container.show();
                setProgress(0);

                fakeInterval = setInterval(function() {
                    if (currentPct < 90) {
                        currentPct++;
                        setProgress(currentPct);
                    }
                }, 300);

                $.ajax({
                    url: "{{ route('students.import') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function() {
                        clearInterval(fakeInterval);
                        setProgress(100);
                        setTimeout(function() {
                            alert('Talabalar muvaffaqiyatli import qilindi!');
                            location.reload();
                        }, 500);
                    },
                    error: function(xhr) {
                        clearInterval(fakeInterval);
                        setProgress(0);
                        var msg = xhr.responseJSON ?
                            (xhr.responseJSON.message || JSON.stringify(xhr.responseJSON)) :
                            xhr.responseText;
                        alert('Xatolik: ' + msg);
                        $btn.prop('disabled', false);
                        $btnText.text('Import');
                        $container.hide();
                    }
                });
            });
        });
    </script>

</x-layouts.sidebar>
