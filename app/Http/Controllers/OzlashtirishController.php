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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // grades dagi user_id lar orqali faqat shu talabalarni olish
        $gradeUserIds = grade::distinct()->pluck('user_id');

        $userQuery = User::whereIn('id', $gradeUserIds);

        if ($request->category_id) {
            $userQuery->where('category_id', $request->category_id);
        }

        if ($request->guruh) {
            $userQuery->where('Guruh', $request->guruh);
        }

        if ($request->search) {
            $userQuery->where("To‘liq_ismi", 'like', '%' . $request->search . '%');
        }
        if ($request->semster) {
            $userQuery->whereHas('grades.subject', function ($q) use ($request) {
                $q->where('semster', $request->semster);
            });
        }

        $talabalar = User::whereIn('id', $gradeUserIds)
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->guruh, fn($q) => $q->where('Guruh', $request->guruh))
            ->when($request->search, fn($q) => $q->where("To‘liq_ismi", 'like', '%' . $request->search . '%'))
            ->with([
                'grades',
                'free_semestrs',
                'mini_semstrs'
            ])
            ->paginate(100);

        // faqat grades da mavjud user larga tegishli guruhlar
        $guruhlar = User::whereIn('id', $gradeUserIds)
            ->distinct()
            ->pluck('Guruh')
            ->filter();

        // semestrlar subjects dan
        $semestrlar = subject::distinct()->pluck('semster')->filter()->sort();

        // faqat grades da mavjud user larga tegishli kategoriyalar
        $yonalishlar = category::whereIn(
            'id',
            User::whereIn('id', $gradeUserIds)->distinct()->pluck('category_id')
        )->get();

        // grades da mavjud subject_id lar orqali fanlar
        $fanlar = Subject::whereIn('id', Grade::distinct()->pluck('subject_id'))
            ->when($request->semster, fn($q) => $q->where('semster', $request->semster))
            ->get();

        $hammasi = $userQuery->with([
            'grades',
            'free_semestrs',
            'mini_semstrs'
        ])->get();

        // statistika
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

                $g = $talaba->getMergedGrade($fan->id);

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
            'yonalishlar',
            'jami',
            'qarzdorlar',
            'muvaffaqiyatli',
            'umumiyQizil',
            'davomatQizil',
            'semestrlar',
            'joriyQizil'
        ));
    }

    public function export(Request $request)
    {
        $gradeUserIds = grade::distinct()->pluck('user_id');

        $talabalar = User::whereIn('id', $gradeUserIds)
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->guruh, fn($q) => $q->where('Guruh', $request->guruh))
            ->when($request->search, fn($q) => $q->where("To‘liq_ismi", 'like', '%' . $request->search . '%'))
            ->with([
                'grades',
                'free_semestrs',
                'mini_semstrs'
            ])
            ->get();

        $fanlar = subject::whereIn('id', grade::distinct()->pluck('subject_id'))
            ->when($request->semster, fn($q) => $q->where('semster', $request->semster))
            ->get();

        // Fayl nomi
        $parts = ['ozlashtirish'];

        if ($request->guruh) {
            $parts[] = $request->guruh;
        }

        if ($request->category_id) {
            $category = \App\Models\Category::find($request->category_id);
            $parts[] = $category?->nomi ?? $request->category_id;
        }

        if ($request->semster) {
            $parts[] = $request->semster;
        }

        $fileName = implode('_', $parts) . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\OzlashtirishExport($talabalar, $fanlar),
            $fileName
        );
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
        //
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
