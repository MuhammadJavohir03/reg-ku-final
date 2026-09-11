<?php

namespace App\Http\Controllers;

use App\Exports\OzlashtirishExport;
use App\Models\category;
use App\Models\grade;
use App\Models\subject;
use App\Models\User;
use Illuminate\Http\Request;

class OzlashtirishController extends Controller
{
    
    public function index(Request $request)
    {

        $gradeUserIds = grade::distinct()->pluck('user_id');



        $guruhlar = User::whereIn('id', $gradeUserIds)
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->kurs, fn($q) => $q->where('Kurs', $request->kurs))
            ->distinct()
            ->pluck('Guruh')
            ->filter();


        $kurslar = User::whereIn('id', $gradeUserIds)
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->guruh, fn($q) => $q->where('Guruh', $request->guruh))
            ->distinct()
            ->pluck('Kurs')
            ->filter();

        $semestrlar = subject::when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->distinct()
            ->pluck('semster')
            ->filter()
            ->sort();

        $yonalishlar = category::whereIn(
            'id',
            User::whereIn('id', $gradeUserIds)->distinct()->pluck('category_id')
        )->get();

        if (!$request->category_id) {
            $talabalar = User::whereIn('id', [])->paginate(100);

            return view('ozlashtirish.index', compact(
                'talabalar',
                'guruhlar',
                'yonalishlar',
                'semestrlar',
                'kurslar'
            ) + [
                'fanlar'              => collect(),
                'jami'                => 0,
                'qarzdorlar'          => 0,
                'muvaffaqiyatli'      => 0,
                'umumiyQizil'         => 0,
                'davomatQizil'        => 0,
                'joriyQizil'          => 0,
                'yonalishTanlanmagan' => true,
            ]);
        }





        $fanlar = self::groupDuplicateSubjects(
            subject::where('category_id', $request->category_id)
                ->when($request->semster, fn($q) => $q->where('semster', $request->semster))
                ->get()
        );

        $talabalarQuery = User::whereIn('id', $gradeUserIds)
            ->where('category_id', $request->category_id)
            ->when($request->guruh, fn($q) => $q->where('Guruh', $request->guruh))
            ->when($request->kurs, fn($q) => $q->where('Kurs', $request->kurs))
            ->when($request->search, fn($q) => $q->where("To‘liq_ismi", 'like', '%' . $request->search . '%'));

        $talabalar = (clone $talabalarQuery)
            ->with(['grades', 'free_semestrs', 'mini_semstrs'])
            ->paginate(100);

        $hammasi = (clone $talabalarQuery)
            ->with(['grades', 'free_semestrs', 'mini_semstrs'])
            ->get();

        $jami = $hammasi->count();

        $qarzdorlar = 0;
        $umumiyQizil = 0;
        $davomatQizil = 0;
        $joriyQizil = 0;

        foreach ($hammasi as $talaba) {

            $hasQarzdor = false;
            $hasUmumiy = false;
            $hasDavomat = false;
            $hasJoriy = false;

            foreach ($fanlar as $fan) {

                $g = $talaba->getMergedGradeForGroup($fan->subject_ids);

                if (($g->joriy_oraliq ?? 0) < 20) {
                    $hasJoriy = true;
                    $hasQarzdor = true;
                }

                if (($g->umumiy ?? 0) < 60) {
                    $hasUmumiy = true;
                    $hasQarzdor = true;
                }

                if (($g->davomat ?? 0) >= 33) {
                    $hasDavomat = true;
                    $hasQarzdor = true;
                }
            }

            if ($hasQarzdor) {
                $qarzdorlar++;
            }

            if ($hasUmumiy) {
                $umumiyQizil++;
            }

            if ($hasDavomat) {
                $davomatQizil++;
            }

            if ($hasJoriy) {
                $joriyQizil++;
            }
        }

        $muvaffaqiyatli = $jami - $qarzdorlar;

        return view('ozlashtirish.index', compact(
            'talabalar',
            'fanlar',
            'guruhlar',
            'kurslar',
            'yonalishlar',
            'jami',
            'qarzdorlar',
            'muvaffaqiyatli',
            'umumiyQizil',
            'davomatQizil',
            'semestrlar',
            'joriyQizil'
        ) + ['yonalishTanlanmagan' => false]);
    }

    
    private static function groupDuplicateSubjects($subjects)
    {
        return $subjects
            ->groupBy(fn($fan) => $fan->nomi . '|' . $fan->semster)
            ->map(function ($guruh) {
                $vakil = $guruh->first();

                $vakil->subject_ids = $guruh->pluck('id')->all();
                return $vakil;
            })
            ->values();
    }

    public function export(Request $request)
    {

        if (!$request->category_id) {
            return back()->with('error', "Eksport qilish uchun avval yo'nalishni tanlang.");
        }

        $gradeUserIds = grade::distinct()->pluck('user_id');

        $talabalar = User::whereIn('id', $gradeUserIds)
            ->where('category_id', $request->category_id)
            ->when($request->guruh, fn($q) => $q->where('Guruh', $request->guruh))
            ->when($request->kurs, fn($q) => $q->where('Kurs', $request->kurs))
            ->when($request->search, fn($q) => $q->where("To‘liq_ismi", 'like', '%' . $request->search . '%'))
            ->with([
                'grades',
                'free_semestrs',
                'mini_semstrs'
            ])
            ->get();


        $fanlar = self::groupDuplicateSubjects(
            subject::where('category_id', $request->category_id)
                ->when($request->semster, fn($q) => $q->where('semster', $request->semster))
                ->get()
        );

        $parts = ['ozlashtirish'];

        if ($request->guruh) {
            $parts[] = $request->guruh;
        }

        $category = \App\Models\Category::find($request->category_id);
        $parts[] = $category?->nomi ?? $request->category_id;

        if ($request->semster) {
            $parts[] = $request->semster;
        }

        $fileName = implode('_', $parts) . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\OzlashtirishExport($talabalar, $fanlar),
            $fileName
        );
    }

    
    public function create()
    {

    }

    
    public function store(Request $request)
    {

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