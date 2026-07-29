<x-layouts.sidebar>

    <x-slot:title>
        O'quv yillari
    </x-slot:title>

    <div class="arizalar-toolbar"
         style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">

        <form action="{{ route('oquv_yili.index') }}" method="GET"
              style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">

            <input type="text"
                   name="search"
                   class="arizalar-search"
                   placeholder="O'quv yili bo'yicha qidirish..."
                   value="{{ request('search') }}">

            @if (request('search'))
                <a href="{{ route('oquv_yili.index') }}" class="ar-btn ar-btn-rej">✕</a>
            @endif
        </form>

        <form action="{{ route('oquv_yili.store') }}" method="POST"
              style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            @csrf

            <input type="text"
                   name="nomi"
                   class="arizalar-search"
                   placeholder="Masalan: 2026-2027"
                   style="width:220px;"
                   required>

            <button type="submit" class="ar-btn ar-btn-ok">
                ＋ Qo'shish
            </button>
        </form>
    </div>

    @if($errors->any())
        <div style="margin:12px 0; padding:10px 14px; border-radius:10px; background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
            text-align: left;
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

        .ar-btn-group {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .ar-btn {
            white-space: nowrap;
        }

        .ar-year-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ar-year-icon {
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

        .ar-year-name {
            font-weight: 600;
            color: #222;
        }

        .ar-edit-input {
            width: 170px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 8px 10px;
            outline: none;
        }

        .ar-edit-input:focus {
            border-color: #6d28d9;
            box-shadow: 0 0 0 3px rgba(109, 40, 217, .12);
        }

        .ar-empty {
            text-align: center;
            color: #6b7280;
            padding: 28px 16px !important;
        }
    </style>

    <div class="arizalar-table-wrap">
        <table class="arizalar-table">
            <thead>
                <tr>
                    <th style="width:90px;">ID</th>
                    <th>O'quv yili</th>
                    <th style="width:260px;">Amallar</th>
                </tr>
            </thead>

            <tbody>
                @forelse($oquv_yillari as $oquv_yili)
                    <tr>
                        <td style="font-weight:600;">#{{ $oquv_yili->id }}</td>

                        <td>
                            <div class="ar-year-cell">
                                <div class="ar-year-icon">
                                    {{ mb_substr($oquv_yili->nomi, 0, 2) }}
                                </div>

                                <div>
                                    <div class="ar-year-name">{{ $oquv_yili->nomi }}</div>
                                    <div style="font-size:12px; color:#6b7280;">
                                        O'quv yili yozuvi
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="ar-btn-group">
                                <form action="{{ route('oquv_yili.update', $oquv_yili->id) }}"
                                      method="POST"
                                      style="display:flex; gap:6px; align-items:center;">
                                    @csrf
                                    @method('PUT')

                                    <input type="text"
                                           name="nomi"
                                           value="{{ $oquv_yili->nomi }}"
                                           class="ar-edit-input"
                                           required>

                                    <button type="submit" class="ar-btn">
                                        ✎ Saqlash
                                    </button>
                                </form>

                                <form action="{{ route('oquv_yili.destroy', $oquv_yili->id) }}"
                                      method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="ar-btn ar-btn-rej"
                                            onclick="return confirm('O‘chirishni tasdiqlaysizmi?')">
                                        ✕ O'chirish
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="ar-empty">
                            Hozircha o'quv yillari mavjud emas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-layouts.sidebar>