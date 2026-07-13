<?php

namespace App\Http\Controllers;

use App\Models\free_semestr;
use App\Models\User;
use App\Models\subject;
use App\Models\bolim;
use App\Models\grade;
use App\Models\category;
use App\Models\mini_semestr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MiniSemestrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->id();
        $activeBolim = Bolim::where('status', 1)->first();

        $submittedSubjectIds = mini_semestr::where('user_id', $userId)
            ->where('bolim_id', $activeBolim?->id)
            ->pluck('subject_id')
            ->toArray();

        $mini_semestrs = mini_semestr::where('user_id', $userId)
            ->where('bolim_id', $activeBolim?->id)
            ->get();

        $subjects = $this->availableSubjectsForMini($userId);

        $categories = Category::all();
        $userCategory = auth()->user()->category;

        return view(
            'mini_semestr_user.index',
            compact(
                'categories',
                'submittedSubjectIds',
                'mini_semestrs',
                'activeBolim',
                'subjects',
                'userCategory'
            )
        );
    }

    /**
     * mini_semestr uchun ariza topshirish mumkin bo'lgan fanlar ro'yxati.
     *
     * Qoida (free_semestr bilan bir xil, faqat shart faqat umumiy < 60):
     * 1) Agar grades / free_semestr / mini_semestr — qaysi birida bo'lsa ham
     *    joriy_oraliq < 20 VA umumiy > 60 bo'lsa — fan "o'tilgan" hisoblanadi
     *    va HECH QACHON ko'rsatilmaydi.
     * 2) gradesda bazaviy shart: umumiy < 60.
     * 3) Agar shu fan bo'yicha free_semestr yoki mini_semestr da ham yozuv
     *    mavjud bo'lsa, o'shalarda ham umumiy < 60 bo'lishi kerak (AND).
     *    Yozuv umuman bo'lmasa — bloklanmaydi.
     */
    private function availableSubjectsForMini(int $userId)
    {
        return Subject::query()
            // 1) o'tgan fan — hamisha chetlab o'tiladi
            ->whereDoesntHave('grades', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->where('joriy_oraliq', '<', 20)
                    ->where('umumiy', '>', 60);
            })
            ->whereDoesntHave('freeSemestrs', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->where('joriy_oraliq', '<', 20)
                    ->where('umumiy', '>', 60);
            })
            ->whereDoesntHave('miniSemestrs', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->where('joriy_oraliq', '<', 20)
                    ->where('umumiy', '>', 60);
            })
            // 2) gradesdagi bazaviy shart
            ->whereHas('grades', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->where('umumiy', '<', 60);
            })
            // 3) free_semestrda yozuv bo'lsa — u yerda ham umumiy < 60 bo'lishi kerak
            ->where(function ($q) use ($userId) {
                $q->whereDoesntHave('freeSemestrs', function ($qq) use ($userId) {
                    $qq->where('user_id', $userId);
                })->orWhereHas('freeSemestrs', function ($qq) use ($userId) {
                    $qq->where('user_id', $userId)
                        ->where('umumiy', '<', 60);
                });
            })
            // 3) mini_semestrda yozuv bo'lsa — u yerda ham umumiy < 60 bo'lishi kerak
            ->where(function ($q) use ($userId) {
                $q->whereDoesntHave('miniSemestrs', function ($qq) use ($userId) {
                    $qq->where('user_id', $userId);
                })->orWhereHas('miniSemestrs', function ($qq) use ($userId) {
                    $qq->where('user_id', $userId)
                        ->where('umumiy', '<', 60);
                });
            })
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_ids'   => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $userId = auth()->id();
        $activeBolim = bolim::where('status', 1)->get();

        if ($activeBolim->count() > 1) {
            return redirect()->back()->with('error', 'Tizim xatosi: bir nechta active bo\'lim mavjud!');
        }

        if ($activeBolim->isEmpty()) {
            return redirect()->back()->with('error', 'Hozirda aktiv bo\'lim mavjud emas!');
        }

        $bolimId = $activeBolim->first()->id;

        $alreadySubmitted = mini_semestr::where('user_id', $userId)
            ->where('bolim_id', $bolimId)
            ->pluck('subject_id')
            ->toArray();

        foreach ($request->subject_ids as $subjectId) {

            // agar allaqachon topshirilgan bo'lsa — o'tkazib yuborish
            if (in_array($subjectId, $alreadySubmitted)) {
                continue;
            }

            // gradesdagi eng so'nggi baholarni mini_semestrga ko'chiramiz
            $grade = grade::where('user_id', $userId)
                ->where('subject_id', $subjectId)
                ->latest()
                ->first();

            mini_semestr::create([
                'user_id'      => $userId,
                'subject_id'   => $subjectId,
                'bolim_id'     => $bolimId,
                'joriy_baho'   => $grade?->joriy_baho,
                'oraliq_baho'  => $grade?->oraliq_baho,
                'joriy_oraliq' => $grade?->joriy_oraliq,
                'yakuniy_baho' => $grade?->yakuniy_baho,
                'umumiy'       => $grade?->umumiy,
                'davomat'      => $grade?->davomat,
            ]);
        }

        return redirect()->back()->with('success', 'Ariza yuborildi!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}