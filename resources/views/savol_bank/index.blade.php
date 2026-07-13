<x-layouts.sidebar>
    <x-slot:title>Savol banki</x-slot:title>


    <div class="oz-wrap">

        <div class="oz-title">Savol banki</div>

        {{-- QIDIRISH + YARATISH --}}
        <div style="display:flex; gap:8px; align-items:center;">
            <div style="position:relative; flex:1;">
                <i class="bx bx-search"
                    style="position:absolute; left:10px; top:50%; transform:translateY(-50%); font-size:16px; color:#aaa;"></i>
                <input type="text" placeholder="Savol bank nomi bo'yicha qidirish..." class="arizalar-search"
                    style="width:100%; padding-left:34px;">
            </div>
            <button type="button" class="ar-btn ar-btn-ok" style="height:38px;"
                onclick="document.getElementById('create-modal').style.display='flex'">
                <i class="bx bx-plus"></i> Yangi bank
            </button>
        </div>

        {{-- BANKLAR RO'YXATI --}}
        @foreach ($banklar as $bank)
            <div style="display:flex; flex-direction:column; gap:8px;">

                <div class="bank-card"
                    style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; overflow:hidden;">

                    <div onclick="toggleBank(this)"
                        style="display:flex; align-items:center; justify-content:space-between; padding:14px 18px; cursor:pointer;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div
                                style="width:36px; height:36px; border-radius:8px; background:#EEEDFE; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="bx bx-folder" style="font-size:18px; color:#3C3489;"></i>
                            </div>
                            <div>
                                <p style="font-size:14px; font-weight:500; margin:0; color:#333;">{{ $bank->nomi }} - ({{ $bank->tur }})
                                </p>
                                <p style="font-size:12px; color:#888; margin:0;">Bo'lim: {{$bank->bolim->nomi ?? 'N/A'}}</p>
                                <p style="font-size:12px; color:#888; margin:0;">Fan: {{$bank->subject->nomi ?? 'N/A'}}</p>
                                <p style="font-size:12px; color:#888; margin:0;">Yaratilgan: {{$bank->created_at->format('d.m.Y, H:i')}}</p>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span
                                style="font-size:12px; color:#888; background:#f5f5f5; padding:3px 10px; border-radius:8px;">{{ $bank->questions_count }}
                                savol</span>
                            <i class="bx bx-chevron-down chevron"
                                style="font-size:18px; color:#888; transition:transform 0.2s;"></i>
                        </div>
                    </div>

                    <div class="bank-body" style="display:none; padding:0 18px 16px; border-top:1px solid #f0f0f0;">
                        <div style="display:flex; gap:8px; padding-top:14px; flex-wrap:wrap;">

                            {{-- IMPORT FORMA --}}
                            <form action="{{ route('savol_bank.import', $bank->id) }}" method="POST"
                                enctype="multipart/form-data"
                                style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                                @csrf

                                <input type="file" name="docx_file" accept=".docx" class="arizalar-search"
                                    style="flex:1; min-width:200px;">

                                <select name="tur" class="arizalar-search" style="width:110px;">
                                    <option value="">-- Tur --</option>
                                    <option value="free" {{ $bank->tur == 'free' ? 'selected' : '' }}>Free</option>
                                    <option value="mini" {{ $bank->tur == 'mini' ? 'selected' : '' }}>Mini</option>
                                </select>

                                <label
                                    style="display:flex; align-items:center; gap:4px; font-size:13px; cursor:pointer;">
                                    <input type="checkbox" name="tozalash" value="1"> Avvalgilarni tozalash
                                </label>

                                <button type="submit" name="action" value="import" class="ar-btn ar-btn-ok"
                                    style="white-space:nowrap;">
                                    <i class="bx bx-upload"></i> Import
                                </button>

                                <button type="submit" name="action" value="save_tur" class="ar-btn"
                                    style="white-space:nowrap;">
                                    <i class="bx bx-save"></i> Saqlash
                                </button>

                            </form>

                            {{-- KORISH --}}
                            <a href="{{ route('savol_bank.show', $bank->id) }}" class="ar-btn">
                                <i class="bx bx-show"></i> Savollarni ko'rish
                            </a>

                            {{-- OCHIRISH --}}
                            <form action="{{ route('savol_bank.destroy', $bank->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="ar-btn ar-btn-rej">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>

                        {{-- SAVOLLAR JADVALI (yashirin) --}}
                        <div class="savollar-list" style="display:none; margin-top:14px;">
                            <div class="arizalar-table-wrap">
                                <table class="arizalar-table">
                                    <thead>
                                        <tr>
                                            <th style="width:50px;">№</th>
                                            <th>Savol</th>
                                            <th style="width:180px;">To'g'ri javob</th>
                                            <th style="width:60px;">Amal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="ar-id">1</td>
                                            <td style="white-space:normal; font-size:13px;">2x + 5 = 15 tenglamani
                                                yeching</td>
                                            <td
                                                style="color:#27500A; font-weight:500; white-space:normal; font-size:12px;">
                                                x = 5</td>
                                            <td>
                                                <button class="ar-btn ar-btn-rej">✕</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ar-id">2</td>
                                            <td style="white-space:normal; font-size:13px;">Funksiya nima?</td>
                                            <td
                                                style="color:#27500A; font-weight:500; white-space:normal; font-size:12px;">
                                                O'zgaruvchilar orasidagi bog'lanish</td>
                                            <td>
                                                <button class="ar-btn ar-btn-rej">✕</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
        @endforeach
        {{-- FORMAT HAQIDA --}}
        <div
            style="margin-top:1rem; padding:12px 16px; background:#EEEDFE; border-radius:8px; font-size:12px; color:#3C3489;">
            <strong>Docx format qoidasi:</strong><br>
            Savol matni?<br>
            {<br>
            &nbsp;&nbsp;~To'g'ri javob<br>
            &nbsp;&nbsp;#Noto'g'ri javob 1<br>
            &nbsp;&nbsp;#Noto'g'ri javob 2<br>
            &nbsp;&nbsp;#Noto'g'ri javob 3<br>
            }
        </div>

    </div>

    {{-- YARATISH MODAL --}}
    <div id="create-modal"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); align-items:center; justify-content:center; z-index:1000;">
        <div style="background:#fff; border-radius:12px; padding:1.5rem; width:340px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                <span style="font-size:15px; font-weight:500;">Yangi savol bank</span>
                <i class="bx bx-x" style="font-size:20px; cursor:pointer; color:#888;"
                    onclick="document.getElementById('create-modal').style.display='none'"></i>
            </div>
            <form action="{{ route('savol_bank.store') }}" method="POST">
                @csrf
                <label style="display:block; font-size:12px; color:#888; margin-bottom:4px;">Bank nomi</label>
                <input name="nomi" type="text" placeholder="Bank nomini kiritish.." class="arizalar-search"
                    style="width:100%; margin-bottom:14px;" autofocus>

                <button type="button" class="ar-btn"
                    onclick="document.getElementById('create-modal').style.display='none'">
                    Bekor qilish
                </button>
                <button type="submit" class="ar-btn ar-btn-ok">
                    <i class="bx bx-check"></i> Yaratish
                </button>
            </form>
        </div>
    </div>
    </div>

    <script>
        function toggleBank(header) {
            const body = header.nextElementSibling;
            const chevron = header.querySelector('.chevron');
            if (body.style.display === 'none') {
                body.style.display = 'block';
                chevron.style.transform = 'rotate(180deg)';
            } else {
                body.style.display = 'none';
                chevron.style.transform = 'rotate(0deg)';
            }
        }

        function toggleSavollar(btn) {
            const el = btn.closest('.bank-body').querySelector('.savollar-list');
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }
    </script>

</x-layouts.sidebar>
