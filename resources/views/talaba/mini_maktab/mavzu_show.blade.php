<x-layouts.sidebar>
    <x-slot:title>{{ $mavzu->nomi }}</x-slot:title>
    @if (session('natija'))
        @php $n = session('natija'); @endphp
        <div class="natija-overlay" id="natija-modal"
            style="position:fixed;inset:0;background:rgba(0,0,0,.55);
             display:flex;align-items:center;justify-content:center;z-index:9999;">
            <div style="background:#fff;border-radius:20px;padding:2rem;text-align:center;width:300px;">
                <div style="font-size:15px;font-weight:600;margin-bottom:1rem;">🎉 Test yakunlandi!</div>
                <div style="font-size:40px;font-weight:800;color:#3C3489;">{{ $n['ball'] }}</div>
                <div style="font-size:12px;color:#888;margin-bottom:1rem;">/ {{ $n['max_ball'] }} ball</div>
                <div style="display:flex;justify-content:center;gap:16px;margin-bottom:1rem;">
                    <div>
                        <div style="font-weight:700;color:#27500A;">{{ $n['togri'] }}</div>
                        <div style="font-size:11px;color:#aaa;">To'g'ri</div>
                    </div>
                    <div>
                        <div style="font-weight:700;color:#791F1F;">{{ $n['notogri'] }}</div>
                        <div style="font-size:11px;color:#aaa;">Noto'g'ri</div>
                    </div>
                    <div>
                        <div style="font-weight:700;color:#3C3489;">{{ $n['foiz'] }}%</div>
                        <div style="font-size:11px;color:#aaa;">Foiz</div>
                    </div>
                </div>
                <button onclick="document.getElementById('natija-modal').style.display='none'" class="ar-btn ar-btn-ok"
                    style="width:100%;justify-content:center;">Yopish ✕</button>
            </div>
        </div>
    @endif
    <div class="oz-wrap">


        {{-- HEADER --}}
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px; flex-wrap:wrap;">
            <a href="{{ route('talaba.mini_maktab.mavzular', $miniSemestr->id) }}" class="ar-btn">
                <i class="bx bx-arrow-back"></i>
            </a>
            <div>
                @php
                    $turRangi = match ($mavzu->tur) {
                        'mavzu' => ['bg' => '#EEEDFE', 'txt' => '#3C3489', 'label' => 'Mavzu'],
                        'oraliq' => ['bg' => '#fff3cd', 'txt' => '#856404', 'label' => 'Oraliq'],
                        'yakuniy' => ['bg' => '#d1fae5', 'txt' => '#065f46', 'label' => 'Yakuniy'],
                        default => ['bg' => '#f0f0f0', 'txt' => '#444', 'label' => ucfirst($mavzu->tur)],
                    };
                @endphp
                <div style="display:flex; align-items:center; gap:8px;">
                    <span
                        style="font-size:11px; font-weight:600; padding:3px 9px; border-radius:20px;
                        background:{{ $turRangi['bg'] }}; color:{{ $turRangi['txt'] }};">
                        {{ $turRangi['label'] }}
                    </span>
                    <div class="oz-title" style="margin:0;">{{ $mavzu->nomi }}</div>
                </div>
                <div style="font-size:12px; color:#888; margin-top:2px;">
                    {{ $miniSemestr->subject->nomi }} · {{ $miniSemestr->bolims->nomi ?? '' }}
                </div>
            </div>
        </div>

        {{-- MATERIALLAR --}}
        @if ($materiallar->isEmpty())
            <div
                style="text-align:center; padding:48px 20px; background:#fafafa;
                 border:1px dashed #e0e0e0; border-radius:12px; color:#bbb; font-size:14px;">
                <i class="bx bx-folder-open" style="font-size:40px; margin-bottom:10px; display:block;"></i>
                Hozircha material biriktirilmagan.
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:14px;">
                @foreach ($materiallar as $m)
                    {{-- ============= TEST ============= --}}
                    @if ($m->tur === 'test')
                        @php $h = $testHolatlari[$m->id] ?? []; @endphp
                        <div style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:18px;">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                                <div
                                    style="width:36px; height:36px; border-radius:8px; background:#EEEDFE;
                                     display:flex; align-items:center; justify-content:center;">
                                    <i class="bx bx-clipboard" style="font-size:17px; color:#3C3489;"></i>
                                </div>
                                <div>
                                    <div style="font-size:14px; font-weight:600;">{{ $m->nomi }}</div>
                                    <div style="font-size:11px; font-weight:600; color:#3C3489;">TEST</div>
                                </div>
                            </div>

                            <div
                                style="display:grid; grid-template-columns:repeat(auto-fit, minmax(140px,1fr)); gap:10px; margin-bottom:14px;">
                                <div class="oz-card oz-card-info">
                                    <div class="oz-card-label">Vaqt limiti</div>
                                    <div class="oz-card-val">{{ $m->vaqt_limit ?? '—' }}</div>
                                    <div class="oz-card-sub">daqiqa</div>
                                </div>
                                <div class="oz-card oz-card-success">
                                    <div class="oz-card-label">Savol soni</div>
                                    <div class="oz-card-val">{{ $m->savollar_soni ?? '—' }}</div>
                                    <div class="oz-card-sub">ta random savol</div>
                                </div>
                                <div class="oz-card oz-card-info">
                                    <div class="oz-card-label">Urinish huquqi</div>
                                    <div class="oz-card-val">{{ $h['ishlangan'] ?? 0 }} / {{ $m->urinish ?? '—' }}
                                    </div>
                                    <div class="oz-card-sub">ishlatilgan</div>
                                </div>
                            </div>

                            @if ($h['hali_ochilmagan'] ?? false)
                                <span class="ar-badge" style="background:#fff3cd; color:#856404;">
                                    <i class="bx bx-time"></i> Hali boshlanmagan
                                </span>
                            @elseif ($h['muddat_tugagan'] ?? false)
                                <span class="ar-badge ar-badge-rej">
                                    <i class="bx bx-lock"></i> Muddat tugagan
                                </span>
                            @elseif ($h['jarayonda'] ?? false)
                                <a href="{{ route('talaba.mini_maktab.test', $h['jarayonda']->id) }}"
                                    class="ar-btn ar-btn-ok">
                                    <i class="bx bx-play"></i> Davom etish
                                </a>
                            @elseif (($h['qolgan_urinish'] ?? 1) <= 0)
                                <span class="ar-badge ar-badge-rej">Urinishlar tugadi</span>
                            @else
                                <form action="{{ route('talaba.mini_maktab.boshlash', [$miniSemestr->id, $m->id]) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit" class="ar-btn ar-btn-ok"
                                        onclick="return confirm('Testni boshlaysizmi?')">
                                        <i class="bx bx-play-circle"></i>
                                        {{ ($h['ishlangan'] ?? 0) > 0 ? 'Qayta boshlash' : 'Testni boshlash' }}
                                    </button>
                                </form>
                            @endif
                            @if (($h['urinishlar'] ?? collect())->isNotEmpty())
                                <div style="margin-top:14px; padding-top:14px; border-top:1px solid #f5f5f5;">
                                    <div style="font-size:12px; color:#888; margin-bottom:8px;">
                                        Urinishlar tarixi (eng yuqori: <strong
                                            style="color:#27500A;">{{ $h['eng_yuqori'] }}</strong> ball)
                                    </div>
                                    <table style="width:100%; font-size:12px; border-collapse:collapse;">
                                        <thead>
                                            <tr style="color:#aaa; text-align:left;">
                                                <th style="padding:4px 0;">Urinish</th>
                                                <th>Sana</th>
                                                <th>Ball</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($h['urinishlar'] as $i => $u)
                                                <tr style="border-top:1px solid #f5f5f5;">
                                                    <td style="padding:6px 0;">{{ $i + 1 }}-urinish</td>
                                                    <td>{{ $u->created_at->format('d.m.Y H:i') }}</td>
                                                    <td
                                                        style="font-weight:600; color:{{ $u->ball == $h['eng_yuqori'] ? '#27500A' : '#333' }};">
                                                        {{ $u->ball }}
                                                        @if ($u->ball == $h['eng_yuqori'])
                                                            <i class='bx bxs-star' style="color:#f59e0b;"></i>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        {{-- ============= VIDEO ============= --}}
                    @elseif ($m->tur === 'video')
                        <div style="background:#fff; border:1px solid #eee; border-radius:12px; overflow:hidden;">
                            <div onclick="toggleAcc('acc-{{ $m->id }}')"
                                style="display:flex; align-items:center; justify-content:space-between;
                                       padding:14px 16px; cursor:pointer;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div
                                        style="width:36px; height:36px; border-radius:8px; background:#fce7f3;
                                         display:flex; align-items:center; justify-content:center;">
                                        <i class="bx bx-video" style="font-size:17px; color:#9d174d;"></i>
                                    </div>
                                    <div>
                                        <div style="font-size:14px; font-weight:600;">{{ $m->nomi }}</div>
                                        <div style="font-size:11px; font-weight:600; color:#9d174d;">
                                            VIDEO @if ($m->video_size)
                                                · {{ $m->video_size }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <i class="bx bx-chevron-down acc-icon" id="icon-acc-{{ $m->id }}"></i>
                            </div>
                            <div id="acc-{{ $m->id }}" style="display:none; padding:0 16px 16px;">
                                @if ($m->videoUrl())
                                    <video controls preload="metadata"
                                        style="width:100%; border-radius:8px; background:#000;">
                                        <source src="{{ $m->videoUrl() }}" type="{{ $m->video_mime ?? 'video/mp4' }}">
                                        Brauzeringiz videoni qo'llab-quvvatlamaydi.
                                    </video>
                                @else
                                    <span style="color:#aaa; font-size:13px;">Video fayl topilmadi.</span>
                                @endif
                            </div>
                        </div>

                        {{-- ============= PDF (accordion) ============= --}}
                    @elseif ($m->tur === 'pdf')
                        <div style="background:#fff; border:1px solid #eee; border-radius:12px; overflow:hidden;">
                            <div onclick="toggleAcc('acc-{{ $m->id }}')"
                                style="display:flex; align-items:center; justify-content:space-between;
                                       padding:14px 16px; cursor:pointer;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div
                                        style="width:36px; height:36px; border-radius:8px; background:#fee2e2;
                                         display:flex; align-items:center; justify-content:center;">
                                        <i class="bx bx-file-pdf" style="font-size:17px; color:#b91c1c;"></i>
                                    </div>
                                    <div>
                                        <div style="font-size:14px; font-weight:600;">{{ $m->nomi }}</div>
                                        <div style="font-size:11px; font-weight:600; color:#b91c1c;">
                                            PDF
                                            @if ($m->pdf_size)
                                                · {{ $m->pdf_size }}
                                            @endif
                                            @if ($m->pdf_sahifalar)
                                                · {{ $m->pdf_sahifalar }} sahifa
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    @if ($m->pdfUrl())
                                        <a href="{{ $m->pdfUrl() }}" download onclick="event.stopPropagation()"
                                            class="ar-btn" style="font-size:12px;">
                                            <i class="bx bx-download"></i> Yuklab olish
                                        </a>
                                    @endif
                                    <i class="bx bx-chevron-down acc-icon" id="icon-acc-{{ $m->id }}"></i>
                                </div>
                            </div>
                            <div id="acc-{{ $m->id }}" style="display:none; padding:0 16px 16px;">
                                @if ($m->pdfUrl())
                                    <iframe src="{{ $m->pdfUrl() }}"
                                        style="width:100%; height:520px; border:1px solid #eee; border-radius:8px;"></iframe>
                                @else
                                    <span style="color:#aaa; font-size:13px;">PDF fayl topilmadi.</span>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

    </div>

    <style>
        .acc-icon {
            transition: transform 0.2s;
            color: #aaa;
        }

        .acc-icon.rotated {
            transform: rotate(180deg);
        }
    </style>
    <script>
        function toggleAcc(id) {
            const el = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            const isOpen = el.style.display === 'block';
            el.style.display = isOpen ? 'none' : 'block';
            icon?.classList.toggle('rotated', !isOpen);
        }
    </script>
</x-layouts.sidebar>
