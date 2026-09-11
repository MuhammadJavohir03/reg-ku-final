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

    
    private function availableSubjectsForMini(int $userId)
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
                    ->where('umumiy', '<', 60);
            })

            ->where(function ($q) use ($userId) {
                $q->whereDoesntHave('freeSemestrs', function ($qq) use ($userId) {
                    $qq->where('user_id', $userId);
                })->orWhereHas('freeSemestrs', function ($qq) use ($userId) {
                    $qq->where('user_id', $userId)
                        ->where('umumiy', '<', 60);
                });
            })

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

    
    public function create()
    {

    }

    
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




        $totalAfterSubmit = count(array_unique(array_merge($alreadySubmitted, $request->subject_ids)));

        if ($totalAfterSubmit > 3) {
            return redirect()->back()->with('error', 'Bir bo\'lim uchun ko\'pi bilan 3 ta fanga ariza topshirish mumkin.');
        }

        foreach ($request->subject_ids as $subjectId) {

            if (in_array($subjectId, $alreadySubmitted)) {
                continue;
            }

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

    
    public function show(string $id)
    {

    }

    
    public function edit(string $id)
    {

    }

    
    public function update(Request $request, string $id)
    {

    }

    
    public function destroy(string $id)
    {

    }
}