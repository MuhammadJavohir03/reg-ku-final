<x-layouts.sidebar>
    <x-slot:title>{{ $bank->nomi }} — Savollar</x-slot:title>

    <div class="oz-wrap">

        {{-- HEADER --}}
        <div
            style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:8px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <a href="{{ route('savol_bank.index') }}" class="ar-btn">
                    <i class="bx bx-arrow-back"></i>
                </a>
                <div>
                    <div class="oz-title" style="margin:0;">{{ $bank->nomi }}</div>
                    <span style="font-size:12px; color:#888;">Jami: {{ $savollar->total() }} ta savol</span>
                </div>
            </div>
        </div>

        {{-- SAVOLLAR --}}
        @forelse ($savollar as $index => $savol)
            <div
                style="background:#fff; border:1px solid #f0f0f0; border-radius:12px; padding:16px 18px; margin-bottom:10px;">

                {{-- SAVOL MATNI --}}
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
                    <div style="display:flex; align-items:flex-start; gap:10px; flex:1;">
                        <span
                            style="background:#EEEDFE; color:#3C3489; font-size:12px; font-weight:600; padding:3px 8px; border-radius:6px; flex-shrink:0;">
                            {{ $savollar->firstItem() + $index }}
                        </span>
                        <p style="font-size:14px; font-weight:500; color:#333; margin:0; line-height:1.5;">
                            {{ $savol->savol }}
                        </p>
                    </div>

                    {{-- AMALLAR --}}
                    <div style="display:flex; gap:6px; flex-shrink:0;">
                        <button class="ar-btn" onclick="toggleEdit({{ $savol->id }})">
                            <i class="bx bx-edit"></i>
                        </button>
                        <form action="{{ route('savol_bank.question.destroy', $savol->id) }}" method="POST"
                            style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="ar-btn ar-btn-rej" onclick="return confirm('O\'chirilsinmi?')">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- VARIANTLAR --}}
                <div style="margin-top:12px; display:grid; grid-template-columns:1fr 1fr; gap:6px; padding-left:34px;">
                    @foreach (['variant_1', 'variant_2', 'variant_3', 'variant_4', 'variant_5'] as $i => $key)
                        @if ($savol->$key)
                            <div
                                style="display:flex; align-items:center; gap:6px; padding:6px 10px; border-radius:8px; font-size:13px;
                                {{ $savol->togri_javob == $i + 1 ? 'background:#e6f4ea; border:1px solid #a7f3d0; color:#27500A; font-weight:600;' : 'background:#f9f9f9; border:1px solid #f0f0f0; color:#555;' }}">
                                <span style="font-weight:700; flex-shrink:0;">{{ $i + 1 }}.</span>
                                {{ $savol->$key }}
                                @if ($savol->togri_javob == $i + 1)
                                    <i class="bx bx-check-circle" style="margin-left:auto; color:#10b981;"></i>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- EDIT FORMA (yashirin) --}}
                <div id="edit-{{ $savol->id }}"
                    style="display:none; margin-top:14px; border-top:1px solid #f0f0f0; padding-top:14px;">
                    <form action="{{ route('savol_bank.question.update', $savol->id) }}" method="POST">
                        @csrf @method('PUT')

                        <label style="font-size:12px; color:#888;">Savol</label>
                        <textarea name="savol" class="arizalar-search" rows="2" style="width:100%; margin-bottom:8px; resize:vertical;">{{ $savol->savol }}</textarea>

                        @foreach (['variant_1', 'variant_2', 'variant_3', 'variant_4', 'variant_5'] as $i => $key)
                            <div style="display:flex; align-items:center; gap:6px; margin-bottom:6px;">
                                <label
                                    style="font-size:12px; color:#888; width:24px; flex-shrink:0;">{{ $i + 1 }}.</label>
                                <input type="text" name="{{ $key }}" value="{{ $savol->$key }}"
                                    class="arizalar-search" style="flex:1;" placeholder="Variant {{ $i + 1 }}">
                                <label
                                    style="display:flex; align-items:center; gap:4px; font-size:12px; cursor:pointer; flex-shrink:0;">
                                    <input type="radio" name="togri_javob" value="{{ $i + 1 }}"
                                        {{ $savol->togri_javob == $i + 1 ? 'checked' : '' }}>
                                    To'g'ri
                                </label>
                            </div>
                        @endforeach

                        <div style="display:flex; gap:6px; margin-top:8px;">
                            <button type="submit" class="ar-btn ar-btn-ok">
                                <i class="bx bx-check"></i> Saqlash
                            </button>
                            <button type="button" class="ar-btn" onclick="toggleEdit({{ $savol->id }})">
                                Bekor qilish
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        @empty
            <div style="text-align:center; padding:2rem; color:#888; font-size:14px;">
                <i class="bx bx-file" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                Hali savollar yo'q
            </div>
        @endforelse

        {{-- PAGINATION --}}
        <div class="ar-pagination" style="margin-top:16px;">
            {{ $savollar->links() }}
        </div>

    </div>

    <script>
        function toggleEdit(id) {
            const el = document.getElementById('edit-' + id);
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }
    </script>

</x-layouts.sidebar>
