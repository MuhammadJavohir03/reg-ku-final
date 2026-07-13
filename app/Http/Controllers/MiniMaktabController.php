<?php

namespace App\Http\Controllers;

use App\Models\Bolim;
use App\Models\mini_semestr;
use App\Models\Subject;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\MsMavzu;
use App\Models\MsMaterial;
use App\Models\User;
use App\Models\TestSession;
use App\Models\QuestionUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MiniMaktabController extends Controller
{
    // ═══════════════════════════════════════════════
    //  1. BOLIMLAR RO'YXATI
    // ═══════════════════════════════════════════════
    public function index()
    {
        $bolimlar = Bolim::paginate(50);
        return view('mini_maktab.index', compact('bolimlar'));
    }

    // ═══════════════════════════════════════════════
    //  2. BOLIM ICHIDAGI FANLAR
    // ═══════════════════════════════════════════════
    public function fanlar($bolim_id)
    {
        $bolim  = Bolim::findOrFail($bolim_id);
        $fanlar = mini_semestr::where('bolim_id', $bolim_id)
            ->with('subject')
            ->select('subject_id')
            ->distinct()
            ->get();

        return view('mini_maktab.fanlar', compact('bolim', 'fanlar'));
    }

    // ═══════════════════════════════════════════════
    //  3. FAN ICHIDAGI MAVZULAR (ASOSIY SAHIFA)
    // ═══════════════════════════════════════════════
    public function mavzular($bolim_id, $subject_id)
    {
        $bolim   = Bolim::findOrFail($bolim_id);
        $subject = Subject::findOrFail($subject_id);

        $mavzular = MsMavzu::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->withCount('materiallar')
            ->orderBy('tartib')
            ->orderBy('id')
            ->get()
            ->groupBy('tur'); // ['mavzu' => [...], 'oraliq' => [...], 'yakuniy' => [...]]

        $talabalar = mini_semestr::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->with('user')
            ->paginate(20);

        return view('mini_maktab.mavzular', compact('bolim', 'subject', 'mavzular', 'talabalar'));
    }

    // ═══════════════════════════════════════════════
    //  4. MAVZU YARATISH
    // ═══════════════════════════════════════════════
    public function mavzuYarat(Request $request, $bolim_id, $subject_id)
    {
        $request->validate([
            'nomi' => 'required|string|max:255',
            'tur'  => 'required|in:mavzu,oraliq,yakuniy',
        ]);

        $tartib = MsMavzu::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->where('tur', $request->tur)
            ->max('tartib') + 1;

        MsMavzu::create([
            'bolim_id'   => $bolim_id,
            'subject_id' => $subject_id,
            'nomi'       => $request->nomi,
            'tur'        => $request->tur,
            'tartib'     => $tartib,
        ]);

        return redirect()->back()->with('success', 'Mavzu yaratildi!');
    }

    // ═══════════════════════════════════════════════
    //  5. MAVZU O'CHIRISH
    // ═══════════════════════════════════════════════
    public function mavzuOchir($id)
    {
        $mavzu = MsMavzu::with('materiallar')->findOrFail($id);

        // Barcha materiallarni (fayllarini ham) o'chirish
        foreach ($mavzu->materiallar as $material) {
            $this->materialFaylOchir($material);
        }

        $bolim_id   = $mavzu->bolim_id;
        $subject_id = $mavzu->subject_id;
        $mavzu->delete();

        return redirect()->route('mini_maktab.mavzular', [$bolim_id, $subject_id])
            ->with('success', 'Mavzu o\'chirildi!');
    }

    // ═══════════════════════════════════════════════
    //  6. MAVZU ICHIDAGI MATERIALLAR
    // ═══════════════════════════════════════════════
    public function mavzuShow($bolim_id, $subject_id, $mavzu_id)
    {
        $bolim   = Bolim::findOrFail($bolim_id);
        $subject = Subject::findOrFail($subject_id);
        $mavzu   = MsMavzu::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->findOrFail($mavzu_id);

        $materiallar = MsMaterial::where('mavzu_id', $mavzu_id)
            ->with('bank')
            ->orderBy('tartib')
            ->get();

        $banklar = QuestionBank::withCount('questions')->get();

        return view('mini_maktab.mavzu_show', compact(
            'bolim',
            'subject',
            'mavzu',
            'materiallar',
            'banklar'
        ));
    }

    // ═══════════════════════════════════════════════
    //  7. MATERIAL QO'SHISH (test | video | pdf)
    // ═══════════════════════════════════════════════
    public function materialQosh(Request $request, $mavzu_id)
    {
        $mavzu = MsMavzu::findOrFail($mavzu_id);

        $request->validate([
            'tur'  => 'required|in:test,video,pdf',
            'nomi' => 'required|string|max:255',
        ]);

        $data = [
            'mavzu_id' => $mavzu_id,
            'tur'      => $request->tur,
            'nomi'     => $request->nomi,
            'tartib'   => MsMaterial::where('mavzu_id', $mavzu_id)->max('tartib') + 1,
            'faol'     => 1,
        ];

        // ── TEST ──
        if ($request->tur === 'test') {
            $request->validate([
                'bank_id'       => 'required|exists:question_banks,id',
                'savollar_soni' => 'required|integer|min:1',
                'vaqt_limit'    => 'required|integer|min:1|max:180',
                'urinish'       => 'required|integer|min:1|max:10',
                'ball'          => 'required|integer|min:1',
            ]);

            $bank = QuestionBank::findOrFail($request->bank_id);

            Question::where('bank_id', $bank->id)
                ->update(['ball' => $request->ball]);

            $data += [
                'bank_id'          => $request->bank_id,
                'savollar_soni'    => $request->savollar_soni,
                'vaqt_limit'       => $request->vaqt_limit,
                'urinish'          => $request->urinish,
                'boshlanish_vaqti' => $request->boshlanish_vaqti ?: null,
                'tugash_vaqti'     => $request->tugash_vaqti ?: null,
            ];
        }

        // ── VIDEO ──
        if ($request->tur === 'video') {
            $request->validate([
                'video' => 'required|file|mimes:mp4,mov,avi,webm|max:512000', // max 500MB
            ]);

            $file = $request->file('video');
            $path = $file->store('ms_videos', 'public');

            $data += [
                'video_path' => $path,
                'video_size' => round($file->getSize() / 1048576, 2) . ' MB',
                'video_mime' => $file->getMimeType(),
            ];
        }

        // ── PDF ──
        if ($request->tur === 'pdf') {
            $request->validate([
                'pdf' => 'required|file|mimes:pdf|max:51200', // max 50MB
            ]);

            $file = $request->file('pdf');
            $path = $file->store('ms_pdfs', 'public');

            $data += [
                'pdf_path'      => $path,
                'pdf_size'      => round($file->getSize() / 1048576, 2) . ' MB',
                'pdf_sahifalar' => $request->pdf_sahifalar ?: null,
            ];
        }

        MsMaterial::create($data);

        return redirect()->back()->with('success', ucfirst($request->tur) . ' material qo\'shildi!');
    }

    // ═══════════════════════════════════════════════
    //  8. MATERIAL O'CHIRISH
    // ═══════════════════════════════════════════════
    public function materialOchir($id)
    {
        $material = MsMaterial::findOrFail($id);
        $mavzu_id = $material->mavzu_id;

        $this->materialFaylOchir($material);
        $material->delete();

        $mavzu = MsMavzu::findOrFail($mavzu_id);
        return redirect()->route('mini_maktab.mavzu.show', [
            $mavzu->bolim_id,
            $mavzu->subject_id,
            $mavzu_id
        ])->with('success', 'Material o\'chirildi!');
    }

    // ═══════════════════════════════════════════════
    //  9. TEST SOZLAMALARINI YANGILASH
    // ═══════════════════════════════════════════════
    public function testSozlama(Request $request, $id)
    {
        $material = MsMaterial::where('tur', 'test')->findOrFail($id);

        $request->validate([
            'bank_id'       => 'required|exists:question_banks,id',
            'savollar_soni' => 'required|integer|min:1',
            'vaqt_limit'    => 'required|integer|min:1|max:180',
            'urinish'       => 'required|integer|min:1|max:10',
            'ball'          => 'required|integer|min:1',
        ]);

        Question::where('bank_id', $request->bank_id)
            ->update(['ball' => $request->ball]);

        $material->update([
            'bank_id'          => $request->bank_id,
            'savollar_soni'    => $request->savollar_soni,
            'vaqt_limit'       => $request->vaqt_limit,
            'urinish'          => $request->urinish,
            'boshlanish_vaqti' => $request->boshlanish_vaqti ?: null,
            'tugash_vaqti'     => $request->tugash_vaqti ?: null,
        ]);

        return redirect()->back()->with('success', 'Test sozlamalari yangilandi!');
    }

    // ═══════════════════════════════════════════════
    //  9b. MATERIALNI FAOLLASHTIRISH / BLOKLASH (AJAX)
    // ═══════════════════════════════════════════════
    public function materialStatusToggle($id)
    {
        $material = MsMaterial::findOrFail($id);
        $material->update(['faol' => ! $material->faol]);

        return response()->json(['status' => (bool) $material->faol]);
    }

    // ═══════════════════════════════════════════════
    //  10. TALABA STATUS TOGGLE (faqat shu fan uchun,
    //      status=0 bo'lsa talabaga "yakuniy" ko'rinmaydi)
    // ═══════════════════════════════════════════════
    public function statusToggle($id)
    {
        $ariza = mini_semestr::findOrFail($id);
        $ariza->update(['status' => !$ariza->status]);

        return redirect()->back()->with(
            'success',
            $ariza->status ? 'Talaba aktivlashtirildi!' : 'Talaba bloklandi!'
        );
    }

    public function allStatusToggle(Request $request, $bolim_id, $subject_id)
    {
        mini_semestr::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->update(['status' => $request->status]);

        return redirect()->back()->with(
            'success',
            $request->status ? 'Barcha talabalar aktivlashtirildi!' : 'Barcha talabalar bloklandi!'
        );
    }

    // ═══════════════════════════════════════════════
    //  11. TALABA TEST HARAKATI (urinishlar ro'yxati)
    // ═══════════════════════════════════════════════
    public function talabaSessions($bolim_id, $subject_id, $user_id, $material_id)
    {
        $bolim    = Bolim::findOrFail($bolim_id);
        $subject  = Subject::findOrFail($subject_id);
        $user     = User::findOrFail($user_id);
        $material = MsMaterial::with(['bank', 'mavzu'])->findOrFail($material_id);

        $sessions = TestSession::where('user_id', $user_id)
            ->where('ms_material_id', $material_id)
            // Statusdan qat'i nazar HAMMASINI ko'rsatamiz (active/finished/expired) —
            // shunda talaba testni tugatmagan bo'lsa ham admin buni ko'radi.
            ->orderBy('created_at')
            ->get()
            ->map(function ($s) {
                $s->togri_soni = QuestionUser::where('session_id', $s->id)->where('status', 1)->count();
                $s->jami_soni  = QuestionUser::where('session_id', $s->id)->count();
                return $s;
            });

        return view('mini_maktab.urinishlar', compact('bolim', 'subject', 'user', 'material', 'sessions'));
    }

    // ═══════════════════════════════════════════════
    //  12. BITTA URINISHNING JAVOBLAR TAHLILI
    // ═══════════════════════════════════════════════
    public function harakat($bolim_id, $subject_id, $user_id, $session_id)
    {
        $bolim   = Bolim::findOrFail($bolim_id);
        $subject = Subject::findOrFail($subject_id);
        $user    = User::findOrFail($user_id);

        $session = TestSession::where('id', $session_id)
            ->where('user_id', $user_id)
            ->with('bank')
            ->firstOrFail();

        $material   = MsMaterial::with('mavzu')->find($session->ms_material_id);
        $harakatlar = QuestionUser::where('session_id', $session_id)
            ->with('question')
            ->get();

        return view('mini_maktab.harakat', compact(
            'bolim',
            'subject',
            'user',
            'session',
            'material',
            'harakatlar'
        ));
    }

    // ═══════════════════════════════════════════════
    //  13. URINISHNI O'CHIRISH
    // ═══════════════════════════════════════════════
    public function sessionDelete($id)
    {
        $session = TestSession::findOrFail($id);

        // Redirect uchun kerakli ma'lumotlarni o'chirishdan OLDIN saqlab qolamiz
        $userId     = $session->user_id;
        $materialId = $session->ms_material_id;
        $material   = MsMaterial::with('mavzu')->find($materialId);

        QuestionUser::where('session_id', $session->id)->delete();
        $session->delete();

        // Urinishlar ro'yxatiga qaytaramiz (bank sahifasiga emas)
        if ($material && $material->mavzu) {
            return redirect()->route('mini_maktab.talaba.sessions', [
                $material->mavzu->bolim_id,
                $material->mavzu->subject_id,
                $userId,
                $materialId,
            ])->with('success', 'Urinish o\'chirildi!');
        }

        return redirect()->route('mini_maktab.index')->with('success', 'Urinish o\'chirildi!');
    }

    // ═══════════════════════════════════════════════
    //  PRIVATE HELPER
    // ═══════════════════════════════════════════════
    private function materialFaylOchir(MsMaterial $material): void
    {
        if ($material->video_path && Storage::disk('public')->exists($material->video_path)) {
            Storage::disk('public')->delete($material->video_path);
        }
        if ($material->pdf_path && Storage::disk('public')->exists($material->pdf_path)) {
            Storage::disk('public')->delete($material->pdf_path);
        }
    }
}