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

        // Filtr dropdownlari uchun ro'yxatlar
        // Guruhlar - tanlangan yo'nalish VA tanlangan kursga mos guruhlar
        // (guruh ro'yxati kurs tanlovi bilan ham moslashadi)
        $guruhlar = User::whereIn('id', $gradeUserIds)
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->kurs, fn($q) => $q->where('Kurs', $request->kurs))
            ->distinct()
            ->pluck('Guruh')
            ->filter();

        // Kurslar - tanlangan yo'nalish VA tanlangan guruhga mos kurslar
        // (kurs ro'yxati guruh tanlovi bilan ham moslashadi)
        $kurslar = User::whereIn('id', $gradeUserIds)
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->guruh, fn($q) => $q->where('Guruh', $request->guruh))
            ->distinct()
            ->pluck('Kurs')
            ->filter();


        // Semestrlar - tanlangan yo'nalishga tegishli fanlarning semestrlari (agar yo'nalish tanlangan bo'lsa)
        $semestrlar = subject::when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->distinct()
            ->pluck('semster')
            ->filter()
            ->sort();

        $yonalishlar = category::whereIn(
            'id',
            User::whereIn('id', $gradeUserIds)->distinct()->pluck('category_id')
        )->get();

        // --- 1) YO'NALISH TANLANMAGUNCHA HECH NARSA KO'RSATMAYMIZ ---
        if (!$request->category_id) {
            $talabalar = User::whereIn('id', [])->paginate(100); // bo'sh paginator (view uchun)

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

        // --- 2) FANLAR ENDI FAQAT TANLANGAN YO'NALISHGA TEGISHLI BO'LADI ---
        // Diqqat: bitta fan bir nechta o'qituvchi tomonidan o'qitilgani uchun
        // `subjects` jadvalida bir xil nomli bir nechta qator bo'lishi mumkin.
        // Shu sababli fanlarni nomi+semestr bo'yicha guruhlab, har bir guruhga
        // tegishli barcha subject_id larni birlashtiramiz (self::groupDuplicateSubjects).
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

    /**
     * Bitta fan bir nechta o'qituvchi tomonidan o'qitilgani sababli
     * `subjects` jadvalida bir xil nomli (masalan, "Xorijiy til") bir nechta
     * qator hosil bo'lgan. Bu funksiya shunday nomdosh fanlarni nomi+semestr
     * bo'yicha bitta "virtual" fanga birlashtiradi va unga tegishli barcha
     * subject_id larni `subject_ids` maydonida saqlaydi. Natijada natijalar
     * jadvalida bitta fan endi faqat bitta marta chiqadi.
     *
     * Eslatma: bu DB arxitekturasini o'zgartirmaydi — faqat query/qatlam
     * darajasida guruhlaydi, xuddi so'ralganidek.
     */
    private static function groupDuplicateSubjects($subjects)
    {
        return $subjects
            ->groupBy(fn($fan) => $fan->nomi . '|' . $fan->semster)
            ->map(function ($guruh) {
                $vakil = $guruh->first();
                // Shu nomdagi fanga tegishli barcha subject_id lar (turli o'qituvchilar)
                $vakil->subject_ids = $guruh->pluck('id')->all();
                return $vakil;
            })
            ->values();
    }

    public function export(Request $request)
    {
        // Export ham yo'nalish tanlanmasa ishlamasin
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

        // fanlar endi tanlangan yo'nalishga qarab filtrlanadi
        // (index() dagi kabi bir xil nomli fanlar birlashtiriladi)
        $fanlar = self::groupDuplicateSubjects(
            subject::where('category_id', $request->category_id)
                ->when($request->semster, fn($q) => $q->where('semster', $request->semster))
                ->get()
        );

        // Fayl nomi
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