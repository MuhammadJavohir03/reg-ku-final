<x-layouts.sidebar>
    <x-slot:title>Baholash qaydnomasi - {{ $subject->nomi }}</x-slot:title>

    <div class="oz-wrap">

        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
            <div class="oz-title" style="margin:0;">
                <i class="bx bx-file-blank"></i> Baholash qaydnomasi — {{ $subject->nomi }}
            </div>
            <a href="{{ route('grades.index', $subject->id) }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i> Orqaga
            </a>
        </div>

        <p style="color:#888; font-size:13px; margin-bottom:20px;">
            Fanda <b>{{ count($groups) }} ta guruh</b> aniqlandi. Har bir guruh uchun alohida
            "Baholash qaydnomasi" Excel fayli yaratiladi va barchasi bitta ZIP arxivda yuklab beriladi.
        </p>

        <form id="qaydnomaForm" action="{{ route('grades.vedomost.export', $subject->id) }}" method="POST">
            @csrf

            <div style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:20px; margin-bottom:20px;">
                <p style="font-size:13px; font-weight:600; color:#333; margin:0 0 16px;">
                    <i class="bx bx-edit-alt" style="color:#3C3489;"></i> Umumiy ma'lumotlar
                    <span style="font-weight:400; color:#aaa;">(barcha {{ count($groups) }} ta guruh uchun bir xil qo'llaniladi)</span>
                </p>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Fakultet</label>
                        <input type="text" name="fakultet" class="arizalar-search" style="width:100%;"
                            placeholder="Masalan: Turizm va iqtisodiyot fakulteti"
                            value="{{ $defaults['fakultet'] }}">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Kafedra</label>
                        <input type="text" name="kafedra" class="arizalar-search" style="width:100%;"
                            value="{{ $defaults['kafedra'] }}">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Fan</label>
                        <input type="text" class="arizalar-search" style="width:100%;" value="{{ $subject->nomi }}" disabled>
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Fan krediti</label>
                        <input type="text" name="fan_krediti" class="arizalar-search" style="width:100%;"
                            placeholder="Masalan: 5.0" value="{{ $defaults['fan_krediti'] }}">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Fan o'qituvchisi</label>
                        <input type="text" name="fan_oqituvchi" class="arizalar-search" style="width:100%;"
                            value="{{ $defaults['fan_oqituvchi'] }}">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Ta'lim tili</label>
                        <input type="text" name="talim_tili" class="arizalar-search" style="width:100%;"
                            value="{{ $defaults['talim_tili'] }}">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#888; display:block; margin-bottom:4px;">Semestr</label>
                        <input type="text" name="semestr" class="arizalar-search" style="width:100%;"
                            value="{{ $defaults['semestr'] }}">
                    </div>

                </div>
            </div>

            <button type="submit" id="exportBtn" class="ar-btn ar-btn-ok" style="padding:10px 20px;">
                <i class="bx bx-archive"></i> Barcha guruhlarni ZIP qilib yuklab olish ({{ count($groups) }} ta fayl)
            </button>

            {{-- PROGRESS --}}
            <div id="progressWrap" style="display:none; align-items:center; gap:12px; margin-top:16px;">
                <div style="position:relative; width:36px; height:36px; flex-shrink:0;">
                    <svg width="36" height="36" style="transform:rotate(-90deg);">
                        <circle cx="18" cy="18" r="15" fill="none" stroke="#e5e7eb" stroke-width="3" />
                        <circle id="progressCircle" cx="18" cy="18" r="15" fill="none" stroke="#217346"
                            stroke-width="3" stroke-dasharray="94.2" stroke-dashoffset="94.2"
                            style="transition:stroke-dashoffset 0.2s;" />
                    </svg>
                    <span id="progressPct" style="position:absolute;top:50%;left:50%;
                        transform:translate(-50%,-50%); font-size:9px;font-weight:700;color:#217346;">0%</span>
                </div>
                <span id="progressText" style="font-size:13px; color:#555;">Tayyorlanmoqda...</span>
            </div>
        </form>

        {{-- GURUHLAR RO'YXATI --}}
        <div style="margin-top:28px;">
            <p style="font-size:13px; font-weight:600; color:#333; margin:0 0 10px;">
                <i class="bx bx-group"></i> Aniqlangan guruhlar
            </p>
            <div class="arizalar-table-wrap">
                <table class="arizalar-table">
                    <thead>
                        <tr>
                            <th style="width:50px;">№</th>
                            <th>Guruh</th>
                            <th style="width:140px;">Talabalar soni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($groups as $index => $guruh)
                            <tr>
                                <td class="ar-id">{{ $index + 1 }}</td>
                                <td>{{ $guruh }}</td>
                                <td>{{ count($grouped[$guruh]) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center; padding:2rem; color:#888;">
                                    Bu fan uchun hali baholar import qilinmagan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        const form = document.getElementById('qaydnomaForm');
        const btn = document.getElementById('exportBtn');
        const progressWrap = document.getElementById('progressWrap');
        const progressCircle = document.getElementById('progressCircle');
        const progressPct = document.getElementById('progressPct');
        const progressText = document.getElementById('progressText');
        const totalGroups = {{ count($groups) }};
        const circumference = 94.2;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            btn.disabled = true;
            progressWrap.style.display = 'flex';
            let fakePct = 0;

            const interval = setInterval(function () {
                if (fakePct < 90) {
                    fakePct += Math.max(1, Math.round((90 - fakePct) / 10));
                    const doneCount = Math.min(totalGroups, Math.round((fakePct / 100) * totalGroups));
                    const offset = circumference - (fakePct / 100) * circumference;
                    progressCircle.setAttribute('stroke-dashoffset', offset);
                    progressPct.textContent = fakePct + '%';
                    progressText.textContent = doneCount + ' / ' + totalGroups + ' fayl tayyorlanmoqda...';
                }
            }, 200);

            fetch(form.action || window.location.href, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('Server xatosi');
                    return response.blob();
                })
                .then(function (blob) {
                    clearInterval(interval);
                    progressCircle.setAttribute('stroke-dashoffset', 0);
                    progressPct.textContent = '100%';
                    progressText.textContent = totalGroups + ' / ' + totalGroups + ' fayl tayyor. Yuklab olinmoqda...';

                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'Baholash_qaydnomalari_{{ \Illuminate\Support\Str::slug($subject->nomi) }}.zip';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);

                    btn.disabled = false;
                })
                .catch(function (err) {
                    clearInterval(interval);
                    progressText.textContent = 'Xatolik yuz berdi!';
                    btn.disabled = false;
                    alert('Xatolik: ' + err.message);
                });
        });
    </script>

</x-layouts.sidebar>