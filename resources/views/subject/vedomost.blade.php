<x-layouts.sidebar>
    <x-slot:title>Vedomost - {{ $subject->nomi }}</x-slot:title>

    <div class="oz-wrap">

        <div
            style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
            <div class="oz-title" style="margin:0;">
                <i class="bx bx-file-blank"></i> Vedomost eksporti — {{ $subject->nomi }}
            </div>
            <a href="{{ route('grades.index', $subject->id) }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i> Orqaga
            </a>
        </div>

        <p style="color:#888; font-size:13px; margin-bottom:20px;">
            Quyidagi maydonlarni tekshiring / tahrirlang. Talabalar ro'yxati fanga tegishli baholar asosida
            avtomatik shakllantirilgan va o'zgarmaydi. Tayyor bo'lgach, pastdagi "Excelga yuklab olish" tugmasini
            bosing.
        </p>

        <form action="{{ route('grades.vedomost.export', $subject->id) }}" method="POST" target="_blank">
            @csrf

            {{-- SARLAVHA MAYDONLARI --}}
            <div
                style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:20px; margin-bottom:20px;">
                <p style="font-size:13px; font-weight:600; color:#333; margin:0 0 16px;">
                    <i class="bx bx-edit-alt" style="color:#3C3489;"></i> Vedomost sarlavhasi
                </p>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">

                    <div style="grid-column:1/-1;">
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">
                            Universitet
                        </label>
                        <input type="text" class="arizalar-search" style="width:100%;" value="Kokand University"
                            disabled>
                    </div>

                    <div style="grid-column:1/-1;">
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">
                            Sarlavha (guruhlar avtomatik qo'shiladi)
                        </label>
                        <input type="text" class="arizalar-search" style="width:100%;"
                            value="Yakuniy qaydnoma {{ $groups->implode(', ') }}" disabled>
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">O'quv yili</label>
                        <input type="text" name="oquv_yili" class="arizalar-search" style="width:100%;"
                            value="{{ old('oquv_yili', $defaults['oquv_yili']) }}">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Kafedra</label>
                        <input type="text" name="kafedra" class="arizalar-search" style="width:100%;"
                            placeholder="Kafedra nomini kiriting" value="{{ old('kafedra', $defaults['kafedra']) }}">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Fan</label>
                        <input type="text" class="arizalar-search" style="width:100%;" value="{{ $subject->nomi }}"
                            disabled>
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Ta'lim tili</label>
                        <input type="text" name="talim_tili" class="arizalar-search" style="width:100%;"
                            value="{{ old('talim_tili', $defaults['talim_tili']) }}">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Imtihon
                            sanasi</label>
                        <input type="text" name="imtihon_sanasi" class="arizalar-search" style="width:100%;"
                            value="{{ old('imtihon_sanasi', $defaults['imtihon_sanasi']) }}">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Semestr</label>
                        <input type="text" name="semestr" class="arizalar-search" style="width:100%;"
                            value="{{ old('semestr', $defaults['semestr']) }}">
                    </div>

                </div>
            </div>

            <button type="submit" class="ar-btn ar-btn-ok" style="padding:10px 20px;">
                <i class="bx bx-download"></i> Excelga yuklab olish
            </button>
        </form>

        {{-- PREVIEW JADVALI --}}
        <div style="margin-top:28px;">
            <p style="font-size:13px; font-weight:600; color:#333; margin:0 0 10px;">
                <i class="bx bx-table"></i> Eksport qilinadigan talabalar ({{ $students->count() }} ta)
            </p>
            <div class="arizalar-table-wrap">
                <table class="arizalar-table">
                    <thead>
                        <tr>
                            <th style="width:50px;">№</th>
                            <th>Talaba</th>
                            <th style="width:100px;">Guruh</th>
                            <th style="width:80px;">Joriy</th>
                            <th style="width:80px;">Oraliq</th>
                            <th style="width:80px;">Reyting</th>
                            <th style="width:80px;">Yakuniy</th>
                            <th style="width:90px;">Umumiy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $index => $s)
                            <tr>
                                <td class="ar-id">{{ $index + 1 }}</td>
                                <td>{{ $s['ismi'] }}</td>
                                <td>{{ $s['guruh'] }}</td>
                                <td>{{ $s['joriy'] }}</td>
                                <td>{{ $s['oraliq'] }}</td>
                                <td>{{ $s['reyting'] }}</td>
                                <td>{{ $s['yakuniy'] }}</td>
                                <td>{{ $s['umumiy'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center; padding:2rem; color:#888;">
                                    Bu fan uchun hali baholar import qilinmagan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</x-layouts.sidebar>
