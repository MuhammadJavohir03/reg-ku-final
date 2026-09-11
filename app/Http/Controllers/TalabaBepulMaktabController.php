<?php
namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\TestSession;
use App\Models\QuestionBank;
use App\Models\QuestionUser;
use App\Models\free_semestr;
use App\Models\Subject;
use App\Models\Bolim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TalabaBepulMaktabController extends Controller
{
    
    private const STATUS_ACTIVE   = 'active';
    private const STATUS_FINISHED = 'finished';
    private const STATUS_EXPIRED  = 'expired';

    public function index()
    {
        $user = Auth::user();

        $fanlar = free_semestr::where('user_id', $user->id)
            ->where('status', 1)
            ->with(['subject', 'bolim'])
            ->get();


        $bankJuftliklari = QuestionBank::where('tur', 'free')
            ->whereIn('subject_id', $fanlar->pluck('subject_id')->unique())
            ->whereIn('bolim_id', $fanlar->pluck('bolim_id')->unique())
            ->get(['subject_id', 'bolim_id'])
            ->map(fn($b) => $b->subject_id . '-' . $b->bolim_id)
            ->flip();

        $fanlar = $fanlar->filter(
            fn($ariza) => isset($bankJuftliklari[$ariza->subject_id . '-' . $ariza->bolim_id])
        )->values();

        return view('talaba.bepul_maktab.index', compact('fanlar'));
    }





    public function boshlash(Request $request, $ariza_id)
    {
        $user = Auth::user();

        $ariza = free_semestr::where('id', $ariza_id)
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->firstOrFail();

        $bank = QuestionBank::where('subject_id', $ariza->subject_id)
            ->where('bolim_id', $ariza->bolim_id)
            ->where('tur', 'free')
            ->firstOrFail();

        if ($bank->boshlanish_vaqti && now()->lt($bank->boshlanish_vaqti)) {
            return redirect()->route('talaba.bepul_maktab.index')
                ->with('error', 'Test hali boshlanmagan! Boshlanish: ' . $bank->boshlanish_vaqti->format('d.m.Y H:i'));
        }

        if ($bank->tugash_vaqti && now()->gt($bank->tugash_vaqti)) {
            return redirect()->route('talaba.bepul_maktab.index')
                ->with('error', 'Test muddati tugagan! Tugash: ' . $bank->tugash_vaqti->format('d.m.Y H:i'));
        }

        $savollarSoni = Question::where('bank_id', $bank->id)->count();
        if ($savollarSoni < $bank->savollar_soni) {
            return redirect()->route('talaba.bepul_maktab.index')
                ->with('error', "Bankda yetarli savol yo'q! Kerak: {$bank->savollar_soni}, Mavjud: {$savollarSoni}");
        }



        $lockKey = "test-boshlash:{$user->id}:{$bank->id}";

        $natija = Cache::lock($lockKey, 10)->block(5, function () use ($user, $bank, $ariza) {
            return DB::transaction(function () use ($user, $bank, $ariza) {


                $ishlangan = TestSession::where('user_id', $user->id)
                    ->where('bank_id', $bank->id)
                    ->whereIn('status', [self::STATUS_FINISHED, self::STATUS_EXPIRED])
                    ->lockForUpdate()
                    ->count();

                if ($ishlangan >= $bank->urinish) {
                    return redirect()->route('talaba.bepul_maktab.index')
                        ->with('error', 'Urinishlar soni tugadi!');
                }

                $activeSession = TestSession::where('user_id', $user->id)
                    ->where('bank_id', $bank->id)
                    ->where('status', self::STATUS_ACTIVE)
                    ->lockForUpdate()
                    ->first();

                if ($activeSession) {
                    $this->ballHisoblash($activeSession, $ariza);
                }

                $session = TestSession::create([
                    'bank_id'          => $bank->id,
                    'user_id'          => $user->id,
                    'savollar_soni'    => $bank->savollar_soni,
                    'boshlanish_vaqti' => now(),
                    'tugash_vaqti'     => now()->addMinutes($bank->vaqt_limit),
                    'ball'             => 0,
                    'status'           => self::STATUS_ACTIVE,
                ]);

                $savollar = Question::where('bank_id', $bank->id)
                    ->inRandomOrder()
                    ->limit($bank->savollar_soni)
                    ->get();


                $hozir = now();
                $rows = $savollar->map(fn($savol) => [
                    'session_id'  => $session->id,
                    'question_id' => $savol->id,
                    'tanlov'      => null,
                    'status'      => 0,
                    'created_at'  => $hozir,
                    'updated_at'  => $hozir,
                ])->all();

                if (!empty($rows)) {
                    QuestionUser::insert($rows);
                }

                return redirect()->route('talaba.bepul_maktab.test', $session->id);
            });
        });

        return $natija ?? redirect()->route('talaba.bepul_maktab.index')
            ->with('error', 'Tizim band, birozdan so\'ng qayta urinib ko\'ring.');
    }

    public function test($attempt_id)
    {
        $user = Auth::user();

        $attempt = TestSession::where('id', $attempt_id)
            ->where('user_id', $user->id)
            ->where('status', self::STATUS_ACTIVE)
            ->firstOrFail();

        if (now()->gt($attempt->tugash_vaqti)) {
            DB::transaction(function () use ($attempt, $user) {


                $tozaAttempt = TestSession::where('id', $attempt->id)
                    ->lockForUpdate()
                    ->first();

                if ($tozaAttempt && $tozaAttempt->status === self::STATUS_ACTIVE) {
                    $ariza = free_semestr::where('user_id', $user->id)
                        ->where('bolim_id', $tozaAttempt->bank->bolim_id)
                        ->where('subject_id', $tozaAttempt->bank->subject_id)
                        ->first();

                    $this->ballHisoblash($tozaAttempt, $ariza);
                }
            });

            return redirect()->route('talaba.bepul_maktab.index')
                ->with('error', 'Vaqt tugadi! Natija saqlandi.');
        }

        $bank     = $attempt->bank;
        $bolim    = $bank->bolim;
        $subject  = $bank->subject;
        $savollar = $attempt->questionUsers()->with('question')->get();


        $savollar->each(function ($qu) {
            if ($qu->question) {
                $qu->question->makeHidden('togri_javob');
            }
        });

        $qolganVaqt = (int) now()->diffInSeconds($attempt->tugash_vaqti);

        return view('talaba.bepul_maktab.test', compact(
            'attempt',
            'bank',
            'bolim',
            'subject',
            'savollar',
            'qolganVaqt'
        ));
    }

    public function yuborish(Request $request, $attempt_id)
    {
        $user = Auth::user();

        return DB::transaction(function () use ($request, $user, $attempt_id) {



            $attempt = TestSession::where('id', $attempt_id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($attempt->status !== self::STATUS_ACTIVE) {

                return redirect()->route('talaba.bepul_maktab.index');
            }



            $vaqtTugaganmi = now()->gt((clone $attempt->tugash_vaqti)->addSeconds(3));

            $attempt->load('questionUsers.question');

            if (!$vaqtTugaganmi) {
                foreach ($attempt->questionUsers as $qu) {
                    $javob = $request->input('javob_' . $qu->question_id);

                    if ($javob === null || !is_scalar($javob)) {
                        continue;
                    }

                    $javob = mb_substr((string) $javob, 0, 20);

                    $togri = (string) $qu->question->togri_javob === $javob ? 1 : 0;
                    $qu->update([
                        'tanlov' => $javob,
                        'status' => $togri,
                    ]);
                }
            }

            $jamiBall = $attempt->questionUsers->where('status', 1)
                ->sum(fn($qu) => $qu->question->ball ?? 1);

            $attempt->update([
                'ball'   => $jamiBall,
                'status' => $vaqtTugaganmi ? self::STATUS_EXPIRED : self::STATUS_FINISHED,
            ]);

            $ariza = free_semestr::where('user_id', $user->id)
                ->where('bolim_id', $attempt->bank->bolim_id)
                ->where('subject_id', $attempt->bank->subject_id)
                ->first();

            if ($ariza && $jamiBall >= ($ariza->yakuniy_baho ?? 0)) {
                $ariza->update([
                    'yakuniy_baho' => $jamiBall,
                    'umumiy'       => ($ariza->joriy_oraliq ?? 0) + $jamiBall,
                ]);
            }

            $togriSoni = $attempt->questionUsers->where('status', 1)->count();
            $jami      = $attempt->questionUsers->count();
            $maxBall   = $attempt->questionUsers->sum(fn($qu) => $qu->question->ball ?? 1);
            $foiz      = $maxBall > 0 ? round($jamiBall / $maxBall * 100) : 0;

            return redirect()->route('talaba.bepul_maktab.index')
                ->with('natija', [
                    'ball'           => $jamiBall,
                    'max_ball'       => $maxBall,
                    'togri'          => $togriSoni,
                    'notogri'        => $jami - $togriSoni,
                    'foiz'           => $foiz,
                    'muddat_tugagan' => $vaqtTugaganmi,
                ]);
        });
    }


    public function chiqish($attempt_id)
    {
        $user = Auth::user();

        return DB::transaction(function () use ($user, $attempt_id) {
            $attempt = TestSession::where('id', $attempt_id)
                ->where('user_id', $user->id)
                ->where('status', self::STATUS_ACTIVE)
                ->lockForUpdate()
                ->first();

            if (!$attempt) {

                return response()->json(['status' => 'ok']);
            }

            $ariza = free_semestr::where('user_id', $user->id)
                ->where('bolim_id', $attempt->bank->bolim_id)
                ->where('subject_id', $attempt->bank->subject_id)
                ->first();

            $this->ballHisoblash($attempt, $ariza);

            return response()->json(['status' => self::STATUS_EXPIRED]);
        });
    }

    
    private function ballHisoblash(TestSession $attempt, $ariza = null, string $status = self::STATUS_EXPIRED)
    {
        $attempt->loadMissing('questionUsers.question');

        $jamiBall = $attempt->questionUsers->where('status', 1)
            ->sum(fn($qu) => $qu->question->ball ?? 1);

        $attempt->update([
            'ball'   => $jamiBall,
            'status' => $status,
        ]);

        if ($ariza && $jamiBall >= ($ariza->yakuniy_baho ?? 0)) {
            $ariza->update([
                'yakuniy_baho' => $jamiBall,
                'umumiy'       => ($ariza->joriy_oraliq ?? 0) + $jamiBall,
            ]);
        }

        return $jamiBall;
    }

    public function natija($attempt_id)
    {
        $user = Auth::user();
        $attempt = TestSession::where('id', $attempt_id)
            ->where('user_id', $user->id)
            ->with('questionUsers.question')
            ->firstOrFail();

        $togriSoni   = $attempt->questionUsers->where('status', 1)->count();
        $notogriSoni = $attempt->questionUsers->where('status', 0)->count();
        $maxBall     = $attempt->questionUsers->sum(fn($qu) => $qu->question->ball ?? 1);
        $foiz        = $maxBall > 0 ? round($attempt->ball / $maxBall * 100) : 0;

        return view('talaba.bepul_maktab.natija', compact(
            'attempt',
            'togriSoni',
            'notogriSoni',
            'maxBall',
            'foiz'
        ));
    }
}