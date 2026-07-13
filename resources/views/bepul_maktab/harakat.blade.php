<x-layouts.sidebar>
    <x-slot:title>Talaba harakati</x-slot:title>

    <div class="oz-wrap">

        {{-- HEADER --}}
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">

            <div>
                <div style="font-size:18px; font-weight:600;">
                    {{ $user->name ?? 'Talaba' }}
                </div>
                <div style="font-size:12px; color:#888;">
                    Test harakatlari va javoblar tahlili
                </div>
            </div>

            <a href="{{ route('bepul_maktab.talaba.sessions', [$session->bank->bolim_id, $session->bank->subject_id, $user->id]) }}"
                style="font-size:13px; color:#3C3489; text-decoration:none; margin-bottom:12px; display:inline-block;">
                ← Urinishlarga qaytish
            </a>

            {{-- 🔥 DELETE BUTTON --}}
            <form action="{{ route('bepul_maktab.session.delete', $session->id) }}" method="POST"
                onsubmit="return confirm('Diqqat! Natija va barcha javoblar o‘chiriladi. Davom etasizmi?')">

                @csrf
                @method('DELETE')

                <button type="submit"
                    style="background:#ef4444; color:#fff; border:none;
                   padding:8px 14px; border-radius:8px; cursor:pointer;
                   font-size:13px;">
                    🗑 O‘chirish
                </button>
            </form>

        </div>

        {{-- SESSION INFO --}}
        <div
            style="background:#fff; border:1px solid #eee; padding:12px 16px; border-radius:10px; margin-bottom:12px; font-size:13px;">
            <b>Bank:</b> {{ $session->bank->nomi ?? '—' }} <br>
            <b>Test boshlanish:</b> {{ $session->boshlanish_vaqti ?? '—' }} <br>
            <b>Test tugash:</b> {{ $session->tugash_vaqti ?? '—' }}
        </div>

        {{-- TABLE --}}
        <div class="arizalar-table-wrap">
            <table class="arizalar-table">
                <thead>
                    <tr>
                        <th style="width:50px;">№</th>
                        <th>Savol</th>
                        <th style="width:200px;">Talaba javobi</th>
                        <th style="width:200px;">To‘g‘ri javob</th>
                        <th style="width:80px;">Natija</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($harakatlar as $i => $item)
                        <tr>
                            <td class="ar-id">{{ $i + 1 }}</td>

                            <td style="font-size:13px;">
                                {{ $item->question->savol }}
                            </td>

                            <td style="font-size:13px;">
                                @if ($item->tanlov)
                                    <span style="color:#3C3489; font-weight:600;">
                                        {{ $item->tanlov }}
                                    </span>
                                    — {{ $item->question->{'variant_' . $item->tanlov} }}
                                @else
                                    <span style="color:#aaa;">Javob berilmagan</span>
                                @endif
                            </td>

                            <td style="font-size:13px; color:#27500A;">
                                {{ $item->question->togri_javob }}
                                — {{ $item->question->{'variant_' . $item->question->togri_javob} }}
                            </td>

                            <td>
                                @if ($item->status)
                                    <span class="ar-badge ar-badge-ok">✔</span>
                                @else
                                    <span class="ar-badge ar-badge-rej">✕</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-layouts.sidebar>
