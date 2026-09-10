<?php

namespace App\Http\Controllers;

use App\Models\bolim;
use App\Models\category;
use App\Models\free_semestr;
use App\Models\grade;
use App\Models\subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class FreeuserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->id();
        $activeBolim = Bolim::where('status', 1)->first();

        $submittedSubjectIds = free_semestr::where('user_id', $userId)
            ->where('bolim_id', $activeBolim?->id)
            ->pluck('subject_id')
            ->toArray();

        $free_semestrs = free_semestr::where('user_id', $userId)
            ->where('bolim_id', $activeBolim?->id)
            ->get();

        $subjects = $this->availableSubjectsForFree($userId);

        $categories = Category::all();
        $userCategory = auth()->user()->category; // relation orqali
        return view('free_semestr_user.index', compact('categories', 'submittedSubjectIds', 'free_semestrs', 'activeBolim', 'subjects', 'userCategory'));
    }

    private function availableSubjectsForFree(int $userId)
    {
        return Subject::query()
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
            ->whereHas('grades', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->where('davomat', '<=', 33)
                    ->where('joriy_oraliq', '>=', 20)
                    ->where('umumiy', '<=', 60)
                    ->where(function ($qq) {
                        $qq->whereNull('bepul')
                            ->orWhere('bepul', '!=', 0);
                    });
            })
            ->where(function ($q) use ($userId) {
                $q->whereDoesntHave('freeSemestrs', function ($qq) use ($userId) {
                    $qq->where('user_id', $userId);
                })->orWhereHas('freeSemestrs', function ($qq) use ($userId) {
                    $qq->where('user_id', $userId)
                        ->where('joriy_oraliq', '>=', 20)
                        ->where('umumiy', '<=', 60);
                });
            })
            ->where(function ($q) use ($userId) {
                $q->whereDoesntHave('miniSemestrs', function ($qq) use ($userId) {
                    $qq->where('user_id', $userId);
                })->orWhereHas('miniSemestrs', function ($qq) use ($userId) {
                    $qq->where('user_id', $userId)
                        ->where('joriy_oraliq', '>=', 20)
                        ->where('umumiy', '<=', 60);
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
        $activeBolim = Bolim::where('status', 1)->get();

        if ($activeBolim->count() > 1) {
            return back()->with('error', 'Tizim xatosi: bir nechta active bo‘lim mavjud!');
        }

        if ($activeBolim->isEmpty()) {
            return back()->with('error', 'Hozirda aktiv bo‘lim mavjud emas!');
        }

        $bolimId = $activeBolim->first()->id;

        $alreadySubmitted = free_semestr::where('user_id', $userId)
            ->where('bolim_id', $bolimId)
            ->pluck('subject_id')
            ->toArray();

        $blockedSubjectNames = [];

        foreach ($request->subject_ids as $subjectId) {

            if (in_array($subjectId, $alreadySubmitted)) {
                continue;
            }

            $grade = grade::where('user_id', $userId)
                ->where('subject_id', $subjectId)
                ->where('davomat', '<=', 33)
                ->where('joriy_oraliq', '>=', 20)
                ->where('umumiy', '<=', 60)
                ->where(function ($qq) {
                    $qq->whereNull('bepul')
                        ->orWhere('bepul', '!=', 0);
                })
                ->latest()
                ->first();

            if (!$grade) {
                $blockedGrade = grade::where('user_id', $userId)
                    ->where('subject_id', $subjectId)
                    ->where('bepul', 0)
                    ->latest()
                    ->first();

                if ($blockedGrade) {
                    $blockedSubjectNames[] = subject::find($subjectId)?->nomi ?? "ID:{$subjectId}";
                    continue;
                }

                $grade = grade::where('user_id', $userId)
                    ->where('subject_id', $subjectId)
                    ->latest()
                    ->first();
            }

            free_semestr::create([
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

        if (!empty($blockedSubjectNames)) {
            $xabar = 'Ariza yuborildi! Diqqat: quyidagi fan(lar) uchun ariza qabul qilinmadi (bepul emas): '
                . implode(', ', $blockedSubjectNames);
            return redirect()->back()->with('warning', $xabar);
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