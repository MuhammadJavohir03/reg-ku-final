<?php

namespace App\Http\Controllers;

use App\Models\mini_semestr;
use App\Models\MsJoriyBaho;
use App\Models\MsMaterial;
use App\Models\MsMavzu;
use App\Models\Question;
use App\Models\QuestionUser;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MiniTmaktabController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $fanlar = mini_semestr::where('user_id', $user->id)
            ->where('status', 1)
            ->with(['subject', 'bolim'])
            ->get()
            ->filter(function ($item) {

                return MsMavzu::where('subject_id', $item->subject_id)
                    ->where('bolim_id', $item->bolim_id)
                    ->where('faol', 1)
                    ->exists();
            });

        return view('talaba.mini_maktab.index', compact('fanlar'));
    }

    public function fan($subject_id)
    {
        $user = Auth::user();

        $mini = mini_semestr::where('user_id', $user->id)
            ->where('subject_id', $subject_id)
            ->where('status', 1)
            ->firstOrFail();

        $mavzular = MsMavzu::where('subject_id', $subject_id)
            ->where('bolim_id', $mini->bolim_id)
            ->where('faol', 1)
            ->withCount('materiallar')
            ->orderBy('tartib')
            ->get();

        return view(
            'talaba.mini_maktab.fan',
            compact(
                'mini',
                'mavzular'
            )
        );
    }


    public function mavzu($mavzu_id)
    {
        $user = Auth::user();

        $mavzu = MsMavzu::with([
            'subject',
            'bolim'
        ])->findOrFail($mavzu_id);

        $mini = mini_semestr::where('user_id', $user->id)
            ->where('subject_id', $mavzu->subject_id)
            ->where('bolim_id', $mavzu->bolim_id)
            ->where('status', 1)
            ->firstOrFail();

        $videolar = MsMaterial::where('mavzu_id', $mavzu->id)
            ->where('faol', 1)
            ->where('tur', 'video')
            ->orderBy('tartib')
            ->get();

        $pdflar = MsMaterial::where('mavzu_id', $mavzu->id)
            ->where('faol', 1)
            ->where('tur', 'pdf')
            ->orderBy('tartib')
            ->get();

        $testlar = MsMaterial::where('mavzu_id', $mavzu->id)
            ->where('faol', 1)
            ->where('tur', 'test')
            ->orderBy('tartib')
            ->get();

        return view('talaba.mini_maktab.mavzu', compact(
            'mini',
            'mavzu',
            'videolar',
            'pdflar',
            'testlar'
        ));
    }

    public function boshlash($material_id)
    {
        $user = Auth::user();

        // Materialni topish
        $material = MsMaterial::with([
            'mavzu',
            'bank'
        ])->findOrFail($material_id);

        if ($material->tur != 'test') {
            abort(404);
        }

        $bank = $material->bank;

        if (!$bank) {
            return back()->with('error', 'Test banki topilmadi!');
        }

        // Talabaning mini semestri
        $mini = mini_semestr::where('user_id', $user->id)
            ->where('subject_id', $material->mavzu->subject_id)
            ->where('bolim_id', $material->mavzu->bolim_id)
            ->where('status', 1)
            ->firstOrFail();

        // Test boshlanish va tugash vaqtini tekshirish
        if ($bank->boshlanish_vaqti && now()->lt($bank->boshlanish_vaqti)) {
            return back()->with(
                'error',
                'Test hali boshlanmagan! Boshlanish: ' .
                    $bank->boshlanish_vaqti->format('d.m.Y H:i')
            );
        }

        if ($bank->tugash_vaqti && now()->gt($bank->tugash_vaqti)) {
            return back()->with(
                'error',
                'Test muddati tugagan! Tugash: ' .
                    $bank->tugash_vaqti->format('d.m.Y H:i')
            );
        }

        // Savollar soni
        $savollarSoni = Question::where('bank_id', $bank->id)->count();

        if ($savollarSoni < $bank->savollar_soni) {

            return back()->with(
                'error',
                "Bankda yetarli savol yo'q! Kerak: {$bank->savollar_soni}, Mavjud: {$savollarSoni}"
            );
        }

        // Urinishlar soni
        $ishlangan = TestSession::where('user_id', $user->id)
            ->where('bank_id', $bank->id)
            ->whereIn('status', ['finished', 'expired'])
            ->count();

        if ($ishlangan >= $bank->urinish) {
            return back()->with('error', 'Urinishlar soni tugagan!');
        }

        // Eski active session
        $activeSession = TestSession::where('user_id', $user->id)
            ->where('bank_id', $bank->id)
            ->where('status', 'active')
            ->first();

        if ($activeSession) {

            $activeSession->update([
                'ball' => $activeSession->hisoblaBall(),
                'status' => 'expired',
            ]);
        }

        // Session yaratish
        $session = TestSession::create([
            'bank_id' => $bank->id,
            'user_id' => $user->id,
            'savollar_soni' => $bank->savollar_soni,
            'boshlanish_vaqti' => now(),
            'tugash_vaqti' => now()->addMinutes($bank->vaqt_limit),
            'ball' => 0,
            'status' => 'active',
        ]);

        // Random savollar
        $savollar = Question::where('bank_id', $bank->id)
            ->inRandomOrder()
            ->limit($bank->savollar_soni)
            ->get();

        foreach ($savollar as $savol) {

            QuestionUser::create([
                'session_id' => $session->id,
                'question_id' => $savol->id,
                'tanlov' => null,
                'status' => 0,
            ]);
        }

        return redirect()->route(
            'talaba.mini_maktab.test',
            $session->id
        );
    }

    public function test($attempt_id)
    {
        $user = Auth::user();

        $attempt = TestSession::where('id', $attempt_id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->with('bank')
            ->firstOrFail();

        // Vaqt tugagan bo'lsa avtomatik yakunlash
        if (now()->gt($attempt->tugash_vaqti)) {

            $attempt->update([
                'ball'   => $attempt->hisoblaBall(),
                'status' => 'expired',
            ]);

            return redirect()
                ->route('talaba.mini_maktab.index')
                ->with('error', 'Test vaqti tugadi.');
        }

        $bank = $attempt->bank;

        // Materialni topamiz
        $material = MsMaterial::where('bank_id', $bank->id)
            ->with('mavzu')
            ->firstOrFail();

        $mavzu = $material->mavzu;

        $subject = $mavzu->subject;

        $bolim = $mavzu->bolim;

        $savollar = $attempt->questionUsers()
            ->with('question')
            ->get();

        $qolganVaqt = now()->diffInSeconds(
            $attempt->tugash_vaqti,
            false
        );

        if ($qolganVaqt < 0) {
            $qolganVaqt = 0;
        }

        return view(
            'talaba.mini_maktab.test',
            compact(
                'attempt',
                'bank',
                'material',
                'mavzu',
                'subject',
                'bolim',
                'savollar',
                'qolganVaqt'
            )
        );
    }

    public function yuborish(Request $request, $attempt_id)
    {
        $user = Auth::user();

        $attempt = TestSession::where('id', $attempt_id)
            ->where('user_id', $user->id)
            ->with('questionUsers.question', 'bank')
            ->firstOrFail();

        if ($attempt->status != 'active') {
            return redirect()->route('talaba.mini_maktab.index');
        }

        // Javoblarni tekshirish
        foreach ($attempt->questionUsers as $qu) {

            $javob = $request->input('javob_' . $qu->question_id);

            if ($javob !== null) {

                $togri = (string)$qu->question->togri_javob === (string)$javob ? 1 : 0;

                $qu->update([
                    'tanlov' => $javob,
                    'status' => $togri,
                ]);
            }
        }

        // Ball
        $ball = QuestionUser::where('session_id', $attempt->id)
            ->where('status', 1)
            ->with('question')
            ->get()
            ->sum(fn($q) => $q->question->ball ?? 1);

        $attempt->update([
            'ball' => $ball,
            'status' => 'finished',
        ]);

        // Material
        $material = MsMaterial::where('bank_id', $attempt->bank_id)
            ->with('mavzu')
            ->firstOrFail();

        $mavzu = $material->mavzu;

        // Mini semestr
        $mini = mini_semestr::where('user_id', $user->id)
            ->where('subject_id', $mavzu->subject_id)
            ->where('bolim_id', $mavzu->bolim_id)
            ->first();

        if ($mini) {

            /*
        |---------------------------------------
        | MAVZU
        |---------------------------------------
        */

            if ($mavzu->tur == 'mavzu') {

                MsJoriyBaho::updateOrCreate(

                    [
                        'user_id' => $user->id,
                        'mavzu_id' => $mavzu->id,
                    ],

                    [
                        'baho' => $ball,
                    ]

                );

                $ortalama = MsJoriyBaho::where('user_id', $user->id)
                    ->whereHas('mavzu', function ($q) use ($mavzu) {

                        $q->where('subject_id', $mavzu->subject_id)
                            ->where('bolim_id', $mavzu->bolim_id);
                    })
                    ->sum('baho');

                $mini->update([
                    'joriy_baho' => round($ortalama, 2)
                ]);
            }

            /*
        |---------------------------------------
        | ORALIQ
        |---------------------------------------
        */

            if ($mavzu->tur == 'oraliq') {

                if ($ball > ($mini->oraliq_baho ?? 0)) {

                    $mini->update([
                        'oraliq_baho' => $ball
                    ]);
                }
            }

            /*
        |---------------------------------------
        | YAKUNIY
        |---------------------------------------
        */

            if ($mavzu->tur == 'yakuniy') {

                if ($ball > ($mini->yakuniy_baho ?? 0)) {

                    $mini->update([
                        'yakuniy_baho' => $ball
                    ]);
                }
            }
        }

        return redirect()->route(
            'talaba.mini_maktab.natija',
            $attempt->id
        );
    }

    private function ballHisoblash(TestSession $attempt)
    {
        $ball = QuestionUser::where('session_id', $attempt->id)
            ->where('status', 1)
            ->with('question')
            ->get()
            ->sum(fn($q) => $q->question->ball ?? 1);

        $attempt->update([
            'ball' => $ball,
            'status' => 'finished',
        ]);

        $material = MsMaterial::where('bank_id', $attempt->bank_id)
            ->with('mavzu')
            ->first();

        if (!$material) {
            return;
        }

        $mavzu = $material->mavzu;

        $mini = mini_semestr::where('user_id', $attempt->user_id)
            ->where('subject_id', $mavzu->subject_id)
            ->where('bolim_id', $mavzu->bolim_id)
            ->first();

        if (!$mini) {
            return;
        }

        // Oddiy mavzu
        if ($mavzu->tur == 'mavzu') {

            MsJoriyBaho::updateOrCreate(
                [
                    'user_id' => $attempt->user_id,
                    'mavzu_id' => $mavzu->id,
                ],
                [
                    'baho' => $ball,
                ]
            );

            $yigindi = MsJoriyBaho::where('user_id', $attempt->user_id)
                ->whereHas('mavzu', function ($q) use ($mavzu) {
                    $q->where('subject_id', $mavzu->subject_id)
                        ->where('bolim_id', $mavzu->bolim_id);
                })
                ->sum('baho');

            $mini->update([
                'joriy_baho' => $yigindi
            ]);
        }

        // Oraliq
        elseif ($mavzu->tur == 'oraliq') {

            if ($ball > ($mini->oraliq_baho ?? 0)) {

                $mini->update([
                    'oraliq_baho' => $ball
                ]);
            }
        }

        // Yakuniy
        elseif ($mavzu->tur == 'yakuniy') {

            if ($ball > ($mini->yakuniy_baho ?? 0)) {

                $mini->update([
                    'yakuniy_baho' => $ball
                ]);
            }
        }
    }

    public function natija($attempt_id)
    {
        $user = Auth::user();

        $attempt = TestSession::where('id', $attempt_id)
            ->where('user_id', $user->id)
            ->with('questionUsers.question')
            ->firstOrFail();

        $togriSoni = $attempt->questionUsers->where('status', 1)->count();

        $notogriSoni = $attempt->questionUsers->where('status', 0)->count();

        $maxBall = $attempt->questionUsers
            ->sum(fn($q) => $q->question->ball ?? 1);

        $foiz = $maxBall > 0
            ? round($attempt->ball / $maxBall * 100)
            : 0;

        return view(
            'talaba.mini_maktab.natija',
            compact(
                'attempt',
                'togriSoni',
                'notogriSoni',
                'maxBall',
                'foiz'
            )
        );
    }
}
