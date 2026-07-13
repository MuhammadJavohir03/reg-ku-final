<x-layouts.sidebar>

    <x-slot:title>
        Foydalanuvchilar Ro'yxati
    </x-slot:title>

    <div class="arizalar-toolbar"
        style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <form action="{{ route('users.index') }}" method="GET"
            style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <input type="text" name="search" class="arizalar-search"
                placeholder="Ism, ID yoki Email bo'yicha qidirish..." value="{{ request('search') }}">
            @if (request('search'))
                <a href="{{ route('users.index') }}" class="ar-btn ar-btn-rej">✕</a>
            @endif
            <select name="page_size" class="arizalar-search" style="width:80px;" onchange="this.form.submit()">
                <option value="20" {{ request('page_size') == 20 ? 'selected' : '' }}>20</option>
                <option value="50" {{ request('page_size') == 50 ? 'selected' : '' }}>50</option>
                <option value="70" {{ request('page_size') == 70 ? 'selected' : '' }}>70</option>
                <option value="100" {{ request('page_size') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </form>

        <form id="importForm" enctype="multipart/form-data" style="display:flex; align-items:center; gap:8px;">
            @csrf
            <input type="file" name="file" id="fileInput" class="arizalar-search" style="width:220px;" required>
            <button type="submit" id="uploadBtn" class="ar-btn ar-btn-ok">
                ↑ <span id="btnText">Import</span>
            </button>
            <div id="progressContainer" style="display:none; position:relative; width:60px; height:60px;">
                <svg width="60" height="60" style="transform:rotate(-90deg)">
                    <circle cx="30" cy="30" r="24" fill="none" stroke="#f0f0f0" stroke-width="5" />
                    <circle id="progressBar" cx="30" cy="30" r="24" fill="none" stroke="#3B6D11"
                        stroke-width="5" stroke-dasharray="150.8" stroke-dashoffset="150.8"
                        style="transition:stroke-dashoffset 0.3s;" />
                </svg>
                <span id="progressPercent"
                    style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:11px;font-weight:600;color:#27500A;">0%</span>
            </div>
        </form>
    </div>

    <style>
        .arizalar-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .arizalar-table th {
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            background: #fafafa;
        }

        .arizalar-table td {
            padding: 12px 16px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .arizalar-table tbody tr {
            transition: .2s;
        }

        .arizalar-table tbody tr:hover {
            background: #f8fbff;
        }

        .ar-id {
            width: 70px;
        }

        .ar-talaba-id {
            width: 140px;
        }

        .ar-email {
            min-width: 260px;
        }

        .ar-fullname {
            min-width: 360px;
        }

        .ar-name-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ar-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ede9fe;
            color: #6d28d9;
            font-weight: 700;
            flex-shrink: 0;
        }

        .ar-name-cell span {
            font-weight: 500;
            color: #222;
        }

        .ar-btn-group {
            display: flex;
            gap: 8px;
        }

        .ar-btn {
            white-space: nowrap;
        }

        .arizalar-table td:last-child {
            width: 190px;
        }
    </style>

    <div class="arizalar-table-wrap">
        <table class="arizalar-table">
            <thead>
                <tr>
                    <th style="width:70px;">ID</th>
                    <th style="width:140px;">Talaba ID</th>
                    <th style="width:260px;">Email</th>
                    <th style="min-width:350px;">To'liq ismi</th>
                    <th style="width:120px;">Roli</th>
                    <th style="width:100px;">Kurs</th>
                    <th style="width:150px;">Guruh</th>
                    <th style="width:90px;">GPA</th>
                    <th style="width:190px;">Amallar</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($users as $user)
                    <tr style="cursor:pointer;" onclick="window.location='{{ route('users.edit', $user->id) }}'">

                        <td class="ar-id">
                            #{{ $user->id }}
                        </td>

                        <td class="ar-talaba-id">
                            {{ $user->Talaba_ID }}
                        </td>

                        <td class="ar-email" title="{{ $user->email }}">
                            {{ $user->email }}
                        </td>

                        <td class="ar-fullname">
                            <div class="ar-name-cell">
                                <div class="ar-avatar">
                                    {{ mb_substr($user['To‘liq_ismi'], 0, 2) }}
                                </div>

                                <span>
                                    {{ $user['To‘liq_ismi'] }}
                                </span>
                            </div>
                        </td>

                        <td>
                            @if ($user->role == 'admin')
                                <span class="ar-badge ar-badge-info">
                                    Admin
                                </span>
                            @elseif($user->role == 'teacher')
                                <span class="ar-badge ar-badge-ok">
                                    O'qituvchi
                                </span>
                            @else
                                <span class="ar-badge ar-badge-ok">
                                    Talaba
                                </span>
                            @endif
                        </td>

                        <td>
                            @if ($user->role == 'admin')
                                -
                            @else
                                {{ $user->Kurs }}-Kurs
                            @endif
                        </td>

                        <td>
                            {{ $user->Guruh }}
                        </td>

                        <td style="font-weight:600;color:#27500A;">
                            {{ $user->GPA }}
                        </td>

                        <td onclick="event.stopPropagation()">
                            <div class="ar-btn-group">

                                <a href="{{ route('users.edit', $user->id) }}" class="ar-btn">
                                    ✎ Tahrirlash
                                </a>

                                <form action="{{ route('users.destroy', $user->id) }}" method="POST">

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
    </div>

    <div class="ar-pagination">
        <div class="ar-pagination">
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
                var circumference = 150.8;
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

                // Soxta progress: har 300ms da 1% qo'shib boradi, 90% da to'xtaydi
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
