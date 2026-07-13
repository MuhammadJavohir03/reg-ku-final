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
    /**
     * Sessiya statuslari:
     *  - active   : test hozir yechilyapti
     *  - finished : talaba "Yuborish" tugmasi orqali o'zi yakunladi
     *  - expired  : vaqt tugab ketdi YOKI talaba sahifani yopib/orqaga qaytib chiqib ketdi
     *
     * Ikkalasi ham "urinish" sifatida hisoblanadi (pastdagi $ishlangan so'roviga qarang).
     */
    private const STATUS_ACTIVE   = 'active';
    private const STATUS_FINISHED = 'finished';
    private const STATUS_EXPIRED  = 'expired';

    // Fanlar ro'yxati
    public function index()
    {
        $user = Auth::user();

        $fanlar = free_semestr::where('user_id', $user->id)
            ->where('status', 1)
            ->with(['subject', 'bolim'])
            ->get();

        // N+1 muammosining oldini olish: har bir ariza uchun alohida so'rov
        // yubormasdan, barcha mos banklarni BITTA so'rov bilan olib, xotirada filtrlaymiz.
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

    // Testni boshlash
    // MUHIM: bu yerga subject_id emas, aynan ARIZA ID (free_semestr->id) yuboriladi.
    // Sabab: bitta talabaning bitta fandan, lekin turli bolim_id bilan bir nechta
    // arizasi bo'lishi mumkin. Faqat subject_id bo'yicha qidirsak, qaysi ariza
    // (demak, qaysi bolim va qaysi bank) nazarda tutilgani noaniq bo'lib qoladi.
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

        // Sana tekshirish
        if ($bank->boshlanish_vaqti && now()->lt($bank->boshlanish_vaqti)) {
            return redirect()->route('talaba.bepul_maktab.index')
                ->with('error', 'Test hali boshlanmagan! Boshlanish: ' . $bank->boshlanish_vaqti->format('d.m.Y H:i'));
        }

        if ($bank->tugash_vaqti && now()->gt($bank->tugash_vaqti)) {
            return redirect()->route('talaba.bepul_maktab.index')
                ->with('error', 'Test muddati tugagan! Tugash: ' . $bank->tugash_vaqti->format('d.m.Y H:i'));
        }

        // Savollar soni yetarlimi tekshirish
        $savollarSoni = Question::where('bank_id', $bank->id)->count();
        if ($savollarSoni < $bank->savollar_soni) {
            return redirect()->route('talaba.bepul_maktab.index')
                ->with('error', "Bankda yetarli savol yo'q! Kerak: {$bank->savollar_soni}, Mavjud: {$savollarSoni}");
        }

        // XAVFSIZLIK: bitta user + bank uchun bir vaqtning o'zida bir nechta
        // "Boshlash" so'rovi (tez-tez bosish, ikkita tab, sekin tarmoq tufayli
        // qayta yuborish) ikkita alohida sessiya yaratib qo'yishining oldini olamiz.
        $lockKey = "test-boshlash:{$user->id}:{$bank->id}";

        $natija = Cache::lock($lockKey, 10)->block(5, function () use ($user, $bank, $ariza) {
            return DB::transaction(function () use ($user, $bank, $ariza) {

                // Urinishlar sonini tekshirish (lockForUpdate — parallel so'rovlarda
                // eski qiymat o'qilib qolmasligi uchun)
                $ishlangan = TestSession::where('user_id', $user->id)
                    ->where('bank_id', $bank->id)
                    ->whereIn('status', [self::STATUS_FINISHED, self::STATUS_EXPIRED])
                    ->lockForUpdate()
                    ->count();

                if ($ishlangan >= $bank->urinish) {
                    return redirect()->route('talaba.bepul_maktab.index')
                        ->with('error', 'Urinishlar soni tugadi!');
                }

                // Oldingi active session bo'lsa — uni "expired" qilib yakunlash
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

                // TEZLIK: har bir savol uchun alohida INSERT o'rniga bitta so'rov
                // bilan barchasini birdaniga yozamiz.
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

        // Cache::lock::block() vaqt ichida qulf ochilmasa null qaytaradi
        return $natija ?? redirect()->route('talaba.bepul_maktab.index')
            ->with('error', 'Tizim band, birozdan so\'ng qayta urinib ko\'ring.');
    }

    // Test sahifasi
    public function test($attempt_id)
    {
        $user = Auth::user();

        $attempt = TestSession::where('id', $attempt_id)
            ->where('user_id', $user->id)
            ->where('status', self::STATUS_ACTIVE)
            ->firstOrFail();

        // Vaqt tugaganmi tekshirish
        if (now()->gt($attempt->tugash_vaqti)) {
            DB::transaction(function () use ($attempt, $user) {
                // lockForUpdate — bir vaqtda "chiqish" (beacon) so'rovi ham kelib
                // qolsa, ikkalasi sessiyani ikki marta yakunlab qo'ymasligi uchun
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

        // XAVFSIZLIK: to'g'ri javob talabaga hech qanday holatda
        // (browser konsoli, "view source", tarmoq so'rovi orqali ham) ko'rinmasligi kerak.
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

    // Testni yuborish
    public function yuborish(Request $request, $attempt_id)
    {
        $user = Auth::user();

        return DB::transaction(function () use ($request, $user, $attempt_id) {

            // lockForUpdate — parallel so'rovlar (masalan, "Yuborish" tugmasi va
            // vaqt tugashi bilan avtomatik yuborilgan JS so'rovi bir vaqtda kelsa)
            // bir xil sessiyani ikki marta qayta ishlab qo'ymasligi uchun.
            $attempt = TestSession::where('id', $attempt_id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($attempt->status !== self::STATUS_ACTIVE) {
                // Sessiya allaqachon yakunlangan — qayta ishlov berilmaydi (double-submit himoyasi)
                return redirect()->route('talaba.bepul_maktab.index');
            }

            // XAVFSIZLIK: talaba vaqt tugaganidan keyin ham tabni ochiq qoldirib,
            // keyinroq "Yuborish" bossa — bu javoblar qabul qilinmaydi.
            // (3 soniyalik chegara — faqat tarmoq kechikishiga tolerantlik uchun.)
            $vaqtTugaganmi = now()->gt((clone $attempt->tugash_vaqti)->addSeconds(3));

            $attempt->load('questionUsers.question');

            if (!$vaqtTugaganmi) {
                foreach ($attempt->questionUsers as $qu) {
                    $javob = $request->input('javob_' . $qu->question_id);

                    if ($javob === null || !is_scalar($javob)) {
                        continue;
                    }

                    // Kiruvchi qiymatni tozalash — cheksiz uzun/anomal ma'lumot yozilmasin
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

    // Talaba testni orqaga qaytib/sahifani yopib chiqib ketsa — sessiyani yakunlash.
    // Frontenddan navigator.sendBeacon() orqali chaqiriladi.
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
                // Allaqachon yakunlangan yoki mavjud emas — xavfsiz javob
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

    /**
     * Ball hisoblash va sessiyani yakunlash (vaqt tugashi yoki chiqib ketish holatlari uchun).
     * Talaba o'zi "Yuborish" tugmasini bosgan holat bu yerdan o'tmaydi — u yuborish()da
     * to'g'ridan-to'g'ri 'finished' statusi bilan yakunlanadi.
     */
    private function ballHisoblash(TestSession $attempt, $ariza = null, string $status = self::STATUS_EXPIRED)
    {
        $attempt->loadMissing('questionUsers.question');

        $jamiBall = $attempt->questionUsers->where('status', 1)
            ->sum(fn($qu) => $qu->question->ball ?? 1);

        $attempt->update([
            'ball'   => $jamiBall,
            'status' => $status,
        ]);

        // Faqat eng yuqori ball yoziladi
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