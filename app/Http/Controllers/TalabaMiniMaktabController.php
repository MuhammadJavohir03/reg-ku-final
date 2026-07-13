<?php

namespace App\Http\Controllers;

use App\Models\mini_semestr;
use App\Models\MsMavzu;
use App\Models\MsMaterial;
use App\Models\MsJoriyBaho;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TalabaMiniMaktabController extends Controller
{
    /**
     * 1-qadam: talabaning barcha mini-semestr fanlari (flat ro'yxat, bepul maktab index'iga o'xshash).
     */
    public function index()
    {
        $fanlar = mini_semestr::with(['subject', 'bolim'])
            ->where('user_id', Auth::id())
            ->get();

        return view('talaba.mini_maktab.index', compact('fanlar'));
    }

    /**
     * 2-qadam: fan ustiga bosilganda o'sha fanning mavzulari (mavzu/oraliq/yakuniy guruhlab).
     *
     * MUHIM: agar shu mini_semestr yozuvida status = 0 bo'lsa (ya'ni shu user_id + bolim_id + subject_id
     * uchun yakuniy nazorat hali ochilmagan bo'lsa), "yakuniy" turidagi mavzular ro'yxatga umuman qo'shilmaydi.
     * Boshqa turlar (mavzu, oraliq) statusdan qat'i nazar har doim ko'rinadi.
     */
    public function mavzular($miniSemestrId)
    {
        $miniSemestr = mini_semestr::with(['subject', 'bolim'])
            ->where('user_id', Auth::id())
            ->findOrFail($miniSemestrId);

        $mavzuQuery = MsMavzu::where('bolim_id', $miniSemestr->bolim_id)
            ->where('subject_id', $miniSemestr->subject_id)
            ->where('faol', 1);

        // status = 0 bo'lsa - yakuniy nazoratni ro'yxatdan chiqarib tashlaymiz
        if (!$miniSemestr->status) {
            $mavzuQuery->where('tur', '!=', 'yakuniy');
        }

        $barchaMavzular = $mavzuQuery->orderBy('tartib')->get();

        // Talabaning har bir "mavzu" turidagi mavjud ballarini bir martada olamiz
        $joriyBaholar = MsJoriyBaho::where('user_id', Auth::id())
            ->whereIn('mavzu_id', $barchaMavzular->pluck('id'))
            ->pluck('baho', 'mavzu_id');

        $mavzular = $barchaMavzular->groupBy('tur');

        return view('talaba.mini_maktab.mavzular', compact('miniSemestr', 'mavzular', 'joriyBaholar'));
    }

    /**
     * 3-qadam: mavzu ustiga bosilganda unga biriktirilgan materiallar (test/video/pdf) - hammasi.
     *
     * MUHIM: agar talaba "yakuniy" turidagi mavzuga to'g'ridan-to'g'ri URL orqali kirmoqchi bo'lsa-yu,
     * status hali 0 bo'lsa - kirish taqiqlanadi (ro'yxatda ko'rsatmaslik yetarli emas, backend ham tekshirishi kerak).
     */
    public function mavzuShow($miniSemestrId, $mavzuId)
    {
        $miniSemestr = mini_semestr::where('user_id', Auth::id())
            ->findOrFail($miniSemestrId);

        $mavzu = MsMavzu::where('bolim_id', $miniSemestr->bolim_id)
            ->where('subject_id', $miniSemestr->subject_id)
            ->findOrFail($mavzuId);

        if ($mavzu->tur === 'yakuniy' && !$miniSemestr->status) {
            abort(403, 'Yakuniy nazorat hali sizga ochilmagan.');
        }

        $materiallar = $mavzu->materiallar()->where('faol', 1)->get();

        // Test materiallari uchun urinish/holat ma'lumotlari
        $testHolatlari = [];
        foreach ($materiallar->where('tur', 'test') as $m) {
            $ishlangan = TestSession::where('user_id', Auth::id())
                ->where('bank_id', $m->bank_id)
                ->whereIn('status', ['finished', 'expired'])
                ->count();

            $jarayonda = TestSession::where('user_id', Auth::id())
                ->where('bank_id', $m->bank_id)
                ->where('status', 'active')
                ->where('tugash_vaqti', '>', now())
                ->first();

            $hozir = now();
            $bosh = $m->boshlanish_vaqti;
            $tug = $m->tugash_vaqti;

            $urinishlar = TestSession::where('user_id', Auth::id())
                ->where('bank_id', $m->bank_id)
                ->whereIn('status', ['finished', 'expired'])
                ->orderBy('created_at')
                ->get();

            $testHolatlari[$m->id] = [
                'ishlangan'       => $ishlangan,
                'qolgan_urinish'  => $m->urinish ? max(0, $m->urinish - $ishlangan) : null,
                'jarayonda'       => $jarayonda,
                'hali_ochilmagan' => $bosh && $hozir->lt($bosh),
                'muddat_tugagan'  => $tug && $hozir->gt($tug),
                'urinishlar'      => $urinishlar,
                'eng_yuqori'      => (int) $urinishlar->max('ball'),
            ];
        }

        return view('talaba.mini_maktab.mavzu_show', compact('miniSemestr', 'mavzu', 'materiallar', 'testHolatlari'));
    }

    /**
     * Test materialini boshlash - random savollar tanlab yangi TestSession (attempt) yaratadi.
     *
     * MUHIM: agar bu material "yakuniy" turidagi mavzuga tegishli bo'lsa-yu, mini_semestr->status = 0 bo'lsa,
     * test boshlashga ruxsat berilmaydi (URL orqali chetlab o'tishning oldini olish uchun).
     */
    public function boshlash($miniSemestrId, $materialId)
    {
        $miniSemestr = mini_semestr::where('user_id', Auth::id())->findOrFail($miniSemestrId);
        $material = MsMaterial::where('tur', 'test')->findOrFail($materialId);

        $mavzu = $material->mavzu;

        if ($mavzu && $mavzu->tur === 'yakuniy' && !$miniSemestr->status) {
            return back()->with('error', 'Yakuniy nazorat hali sizga ochilmagan.');
        }

        $ishlangan = TestSession::where('user_id', Auth::id())
            ->where('bank_id', $material->bank_id)
            ->whereIn('status', ['finished', 'expired'])
            ->count();

        if ($material->urinish && $ishlangan >= $material->urinish) {
            return back()->with('error', 'Urinish huquqingiz tugagan.');
        }

        $jarayonda = TestSession::where('user_id', Auth::id())
            ->where('bank_id', $material->bank_id)
            ->where('status', 'active')
            ->where('tugash_vaqti', '>', now())
            ->first();

        if ($jarayonda) {
            return redirect()->route('talaba.mini_maktab.test', $jarayonda->id);
        }

        $attempt = TestSession::create([
            'user_id'          => Auth::id(),
            'bank_id'          => $material->bank_id,
            'ms_material_id'   => $material->id,
            'status'           => 'active',
            'boshlanish_vaqti' => now(),
            'tugash_vaqti'     => now()->addMinutes($material->vaqt_limit ?? 20),
        ]);

        $savollar = $material->bank->questions()
            ->inRandomOrder()
            ->limit($material->savollar_soni ?? 20)
            ->get();

        foreach ($savollar as $savol) {
            $attempt->questionUsers()->create([
                'question_id' => $savol->id,
            ]);
        }

        return redirect()->route('talaba.mini_maktab.test', $attempt->id);
    }

    /**
     * Test sahifasi (dizayn bepul maktabnikiga aynan bir xil).
     */
    public function test($attemptId)
    {
        $attempt = TestSession::where('user_id', Auth::id())->findOrFail($attemptId);
        $savollar = $attempt->questionUsers()->with('question')->get();

        $material = MsMaterial::find($attempt->ms_material_id)
            ?? MsMaterial::where('bank_id', $attempt->bank_id)->first();
        $mavzu = $material?->mavzu;
        $subject = $mavzu?->subject;
        $bolim = $mavzu?->bolim;

        $qolganVaqt = max(0, now()->diffInSeconds($attempt->tugash_vaqti, false));

        return view('talaba.mini_maktab.test', compact('attempt', 'savollar', 'qolganVaqt', 'subject', 'bolim'));
    }

    /**
     * Test yakunlanganda javoblarni tekshiradi, ballni hisoblaydi va
     * mavzu turiga qarab mini_semestr ustunlariga (joriy/oraliq/yakuniy) joylaydi.
     */
    public function yuborish(Request $request, $attemptId)
    {
        $attempt = TestSession::where('user_id', Auth::id())->findOrFail($attemptId);

        $togri = 0;
        $jami = 0;
        $ball = 0;

        foreach ($attempt->questionUsers()->with('question')->get() as $qu) {
            $tanlov = $request->input('javob_' . $qu->question_id);
            $togriJavobmi = $tanlov && $tanlov == $qu->question->togri_javob;

            $qu->tanlov = $tanlov;
            $qu->status = $togriJavobmi;
            $qu->save();

            $jami++;
            if ($togriJavobmi) {
                $togri++;
                $ball += $qu->question->ball ?? 1;
            }
        }

        $attempt->status = 'finished';
        $attempt->ball = $ball;
        $attempt->save();

        $material = MsMaterial::find($attempt->ms_material_id)
            ?? MsMaterial::where('bank_id', $attempt->bank_id)->first();
        $mavzu = $material?->mavzu;

        if ($mavzu) {
            $miniSemestr = mini_semestr::where('user_id', Auth::id())
                ->where('bolim_id', $mavzu->bolim_id)
                ->where('subject_id', $mavzu->subject_id)
                ->first();

            if ($miniSemestr) {
                // Shu mavzu (bank) bo'yicha talabaning barcha tugagan urinishlari orasidan
                // ENG YUQORI ballni olamiz (masalan: 20, 12, 19 -> 20 hisobga olinadi).
                $engYuqoriBall = (int) TestSession::where('user_id', Auth::id())
                    ->where('bank_id', $attempt->bank_id)
                    ->where('status', 'finished')
                    ->max('ball');

                if ($mavzu->tur === 'mavzu') {
                    MsJoriyBaho::updateOrCreate(
                        ['user_id' => Auth::id(), 'mavzu_id' => $mavzu->id],
                        ['baho' => $engYuqoriBall]
                    );

                    $mavzuIdlar = MsMavzu::where('bolim_id', $mavzu->bolim_id)
                        ->where('subject_id', $mavzu->subject_id)
                        ->where('tur', 'mavzu')
                        ->pluck('id');

                    $miniSemestr->joriy_baho = (int) MsJoriyBaho::where('user_id', Auth::id())
                        ->whereIn('mavzu_id', $mavzuIdlar)
                        ->sum('baho');
                } elseif ($mavzu->tur === 'oraliq') {
                    $miniSemestr->oraliq_baho = $engYuqoriBall;
                } elseif ($mavzu->tur === 'yakuniy') {
                    // Status = 0 bo'lsa, bu yerga umuman kelib qolmasligi kerak
                    // (boshlash() bosqichida to'silgan), lekin qo'shimcha xavfsizlik uchun tekshiramiz.
                    if (!$miniSemestr->status) {
                        return redirect()->route('talaba.mini_maktab.index')
                            ->with('error', 'Yakuniy nazorat hali sizga ochilmagan.');
                    }
                    $miniSemestr->yakuniy_baho = $engYuqoriBall;
                }

                $miniSemestr->joriy_oraliq = ($miniSemestr->joriy_baho ?? 0) + ($miniSemestr->oraliq_baho ?? 0);
                $miniSemestr->umumiy = $miniSemestr->joriy_oraliq + ($miniSemestr->yakuniy_baho ?? 0);
                $miniSemestr->save();
            }
        }

        $maxBall = $attempt->questionUsers->sum(fn($qu) => $qu->question->ball ?? 1);
        $foiz = $jami ? round($togri / $jami * 100) : 0;

        session()->flash('natija', [
            'ball'     => $ball,
            'max_ball' => $maxBall,
            'togri'    => $togri,
            'notogri'  => $jami - $togri,
            'foiz'     => $foiz,
        ]);

        if ($mavzu && isset($miniSemestr)) {
            return redirect()->route('talaba.mini_maktab.mavzu.show', [$miniSemestr->id, $mavzu->id]);
        }

        return redirect()->route('talaba.mini_maktab.index');
    }

    /**
     * Natija va javoblar tahlili sahifasi (dizayn bepul maktabnikiga aynan bir xil).
     */
    public function natija($attemptId)
    {
        $attempt = TestSession::where('user_id', Auth::id())
            ->with('questionUsers.question')
            ->findOrFail($attemptId);

        $togriSoni = $attempt->questionUsers->where('status', true)->count();
        $notogriSoni = $attempt->questionUsers->count() - $togriSoni;
        $foiz = $attempt->questionUsers->count()
            ? round($togriSoni / $attempt->questionUsers->count() * 100)
            : 0;
        $maxBall = $attempt->questionUsers->sum(fn($qu) => $qu->question->ball ?? 1);

        return view('talaba.mini_maktab.natija', compact('attempt', 'togriSoni', 'notogriSoni', 'foiz', 'maxBall'));
    }
}