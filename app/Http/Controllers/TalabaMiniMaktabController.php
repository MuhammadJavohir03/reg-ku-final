<?php

namespace App\Http\Controllers;

use App\Models\Bolim;
use App\Models\mini_semestr;
use App\Models\MsMavzu;
use App\Models\MsMaterial;
use App\Models\MsTopshiriq;
use App\Models\MsJoriyBaho;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TalabaMiniMaktabController extends Controller
{
    
    public function index()
    {
        $bolimIds = mini_semestr::where('user_id', Auth::id())
            ->pluck('bolim_id')
            ->unique();

        $bolimlar = Bolim::whereIn('id', $bolimIds)->get();

        return view('talaba.mini_maktab.bolimlar', compact('bolimlar'));
    }

    
    public function fanlar($bolim_id)
    {
        $bolim = Bolim::findOrFail($bolim_id);

        if (! $bolim->status) {
            abort(403, "Bu bo'lim hali faol emas.");
        }

        $fanlar = mini_semestr::with(['subject', 'bolim'])
            ->where('user_id', Auth::id())
            ->where('bolim_id', $bolim_id)
            ->get();

        return view('talaba.mini_maktab.fanlar', compact('bolim', 'fanlar'));
    }

    
    public function mavzular($miniSemestrId)
    {
        $miniSemestr = mini_semestr::with(['subject', 'bolim'])
            ->where('user_id', Auth::id())
            ->findOrFail($miniSemestrId);

        $mavzuQuery = MsMavzu::where('bolim_id', $miniSemestr->bolim_id)
            ->where('subject_id', $miniSemestr->subject_id)
            ->where('faol', 1);

        if (!$miniSemestr->status) {
            $mavzuQuery->where('tur', '!=', 'yakuniy');
        }

        $barchaMavzular = $mavzuQuery->orderBy('tartib')->get();

        $joriyBaholar = MsJoriyBaho::where('user_id', Auth::id())
            ->whereIn('mavzu_id', $barchaMavzular->pluck('id'))
            ->pluck('baho', 'mavzu_id');

        $mavzular = $barchaMavzular->groupBy('tur');

        return view('talaba.mini_maktab.mavzular', compact('miniSemestr', 'mavzular', 'joriyBaholar'));
    }

    
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

        $topshiriqlarMap = [];
        foreach ($materiallar->where('tur', 'topshiriq') as $m) {
            $topshiriqlarMap[$m->id] = MsTopshiriq::where('ms_material_id', $m->id)
                ->where('user_id', Auth::id())
                ->first();
        }

        return view('talaba.mini_maktab.mavzu_show', compact(
            'miniSemestr',
            'mavzu',
            'materiallar',
            'testHolatlari',
            'topshiriqlarMap'
        ));
    }

    
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

    
    public function topshiriqYukla(Request $request, $miniSemestrId, $materialId)
    {
        $miniSemestr = mini_semestr::where('user_id', Auth::id())
            ->findOrFail($miniSemestrId);

        $material = MsMaterial::where('tur', 'topshiriq')
            ->where('faol', 1)
            ->with('mavzu')
            ->findOrFail($materialId);

        if (
            !$material->mavzu ||
            $material->mavzu->bolim_id != $miniSemestr->bolim_id ||
            $material->mavzu->subject_id != $miniSemestr->subject_id
        ) {
            abort(403, 'Bu topshiriq sizga tegishli emas.');
        }

        if ($material->mavzu->tur === 'yakuniy' && !$miniSemestr->status) {
            return back()->with('error', 'Yakuniy nazorat hali sizga ochilmagan.');
        }

        if (!$miniSemestr->status && $material->mavzu->tur !== 'mavzu' && $material->mavzu->tur !== 'oraliq') {

        }

        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:51200',
        ]);

        $user = Auth::user();
        $file = $request->file('pdf');

        $fio = preg_replace(
            '/[^a-zA-Z0-9_\-а-яА-ЯёЁўқғҳʼ\' ]/u',
            '',
            $user->getAttribute('To‘liq_ismi') ?? $user->name ?? 'talaba'
        );
        $fio = str_replace(' ', '_', trim($fio));
        $filename = $fio . '_' . time() . '.pdf';

        $path = $file->storeAs('ms_topshiriq_javoblar', $filename, 'public');

        $topshiriq = MsTopshiriq::firstOrNew([
            'ms_material_id' => $material->id,
            'user_id'        => $user->id,
        ]);

        if ($topshiriq->pdf_path && Storage::disk('public')->exists($topshiriq->pdf_path)) {
            Storage::disk('public')->delete($topshiriq->pdf_path);
        }

        $topshiriq->pdf_path = $path;

        $topshiriq->save();

        return back()->with('success', 'Topshiriq muvaffaqiyatli yuklandi!');
    }
}