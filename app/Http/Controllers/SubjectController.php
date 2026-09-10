<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use Illuminate\Http\Request;
use App\Models\subject;
use App\Models\User;
use App\Models\category;
use App\Models\lesson_type;
use App\Models\grade;
use App\Models\kafedra;
use App\Models\fakultet;
use App\Models\OquvYili;
use App\Models\SubjectsToSubject;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        $pageSize = request('page_size', 10);

        // Alohida filterlar: Yo'nalish (category), Kursi, Semestr
        $categoryId = request('category_id');
        $kurs = request('kurs');
        $semester = request('semster');

        $subjects = subject::with(['category', 'teacher', 'kafedra', 'lesson_type'])
            ->withExists('grades')
            // Faniga birikkan (baho yozilgan) distinct talabalar soni
            ->withCount(['grades as students_count' => function ($q) {
                $q->select(DB::raw('count(distinct user_id)'));
            }])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nomi', 'like', "%{$search}%")
                        ->orWhere('semster', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($q3) use ($search) {
                            $q3->where('guruh', 'like', "%{$search}%");
                        })
                        ->orWhereHas('teacher', function ($q2) use ($search) {
                            $q2->where('To‘liq_ismi', 'like', "%{$search}%");
                        });
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($semester, function ($query, $semester) {
                $query->where('semster', $semester);
            })
            ->when($kurs, function ($query, $kurs) {
                $startSem = ($kurs - 1) * 2 + 1;
                $endSem = $kurs * 2;
                $query->whereBetween('semster', [$startSem, $endSem]);
            })
            ->latest()
            ->paginate($pageSize)
            ->withQueryString();

        $subjectCounts = [
            'subject' => subject::count(),
        ];

        $teachers = User::where('role', 'teacher')->get();
        $categories = category::all();

        return view('subject.index', compact('subjects', 'subjectCounts', 'teachers', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        $categories = category::all();
        $kafedralar = kafedra::all();
        $fakultetlar = fakultet::all();
        $lesson_types = lesson_type::all();
        $oquv_yillari = OquvYili::all();

        return view('subject.create', compact(
            'teachers',
            'categories',
            'kafedralar',
            'fakultetlar',
            'lesson_types',
            'oquv_yillari'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request)
    {
        $nomi = $request->input('nomi');

        $group = SubjectsToSubject::firstOrCreate(
            ['nomi' => $nomi]
        );

        subject::create([
            'nomi'                   => $nomi,
            'category_id'            => $request->input('category_id'),
            'kafedra_id'             => $request->input('kafedra_id'),
            'fakultet_id'            => $request->input('fakultet_id'),
            'oquv_yili_id'           => $request->input('oquv_yili_id'),
            'talim_tili'             => $request->input('talim_tili'),
            'teacher_id'             => $request->input('teacher_id'),
            'lesson_type_id'         => $request->input('lesson_type_id'),
            'semster'                => $request->input('semster'),
            'kredit'                 => $request->input('kredit'),
            'subjects_to_subject_id' => $group->id,
        ]);

        return redirect()->route('subject.index')->with('success', 'Fan muvaffaqiyatli yaratildi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(subject $subject)
    {
        $subject->load(['category', 'teacher', 'kafedra', 'lesson_type', 'oquv_yili']);

        // Faniga birikkan talabalar soni
        $countStudent = $subject->grades()->distinct('user_id')->count('user_id');

        return view('subject.show', compact('subject', 'countStudent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(subject $subject)
    {
        $teachers = User::where('role', 'teacher')->get();
        $categories = category::all();
        $kafedralar = kafedra::all();
        $fakultetlar = fakultet::all();
        $lesson_types = lesson_type::all();
        $oquv_yillari = OquvYili::all();

        return view('subject.edit', compact(
            'subject',
            'teachers',
            'categories',
            'kafedralar',
            'fakultetlar',
            'lesson_types',
            'oquv_yillari'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSubjectRequest $request, subject $subject)
    {
        $request->validate([
            'nomi'            => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'kafedra_id'      => 'nullable|exists:kafedra,id',
            'fakultet_id'     => 'nullable|exists:fakultet,id',
            'oquv_yili_id'    => 'nullable|exists:oquv_yili,id',
            'talim_tili'      => 'nullable|string|max:255',
            'teacher_id'      => 'nullable|exists:users,id',
            'lesson_type_id'  => 'nullable|exists:lesson_types,id',
            'semster'         => 'required|integer|min:1|max:8',
            'kredit'          => 'required|integer|min:1|max:10',
        ]);

        $subject->update([
            'nomi'            => $request->input('nomi'),
            'category_id'     => $request->input('category_id'),
            'kafedra_id'      => $request->input('kafedra_id'),
            'fakultet_id'     => $request->input('fakultet_id'),
            'oquv_yili_id'    => $request->input('oquv_yili_id'),
            'talim_tili'      => $request->input('talim_tili'),
            'teacher_id'      => $request->input('teacher_id'),
            'lesson_type_id'  => $request->input('lesson_type_id'),
            'semster'         => $request->input('semster'),
            'kredit'          => $request->input('kredit'),
        ]);

        return redirect()->route('subject.index')->with('success', 'Fan muvaffaqiyatli yangilandi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(subject $subject)
    {
        $subject->delete();

        return redirect()->route('subject.index')->with('success', 'Fan muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Mavjud fanni nusxalaydi: barcha parametrlar saqlanadi,
     * faqat tanlangan yangi o'qituvchi biriktiriladi.
     */
    public function duplicate(Request $request, subject $subject)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
        ]);

        subject::create([
            'nomi'                   => $subject->nomi,
            'category_id'            => $subject->category_id,
            'kafedra_id'             => $subject->kafedra_id,
            'fakultet_id'            => $subject->fakultet_id,
            'oquv_yili_id'           => $subject->oquv_yili_id,
            'talim_tili'             => $subject->talim_tili,
            'teacher_id'             => $request->input('teacher_id'),
            'lesson_type_id'         => $subject->lesson_type_id,
            'semster'                => $subject->semster,
            'kredit'                 => $subject->kredit,
            'subjects_to_subject_id' => $subject->subjects_to_subject_id,
        ]);

        return redirect()->route('subject.index')->with('success', 'Fan muvaffaqiyatli nusxalandi.');
    }

    /**
     * Biriktirish sahifasini ko'rsatadi.
     */
    public function biriktirish()
    {
        $subjects = subject::with(['teacher', 'category', 'oquv_yili'])
            ->whereNull('subjects_to_subject_id')
            ->latest()
            ->get();

        $groups = SubjectsToSubject::withCount('subjects')->orderBy('nomi')->get();
        $kattacount = ['kattacount' => SubjectsToSubject::count()];

        return view('subject.biriktirish', compact('subjects', 'kattacount', 'groups'));
    }

    /**
     * AJAX qidiruv: fan nomi bo'yicha subjects qaytaradi.
     */
    public function biriktirishSearch(Request $request)
    {
        $q = $this->normalizeNomi($request->get('q', ''));

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $qNoApostrophe = str_replace("'", '', $q);

        $subjects = subject::with(['teacher', 'category', 'oquv_yili'])
            ->where(function ($query) use ($q, $qNoApostrophe) {
                $query->where('nomi', 'like', "%{$q}%")
                    ->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(nomi, '’', ''), '‘', ''), '`', ''), \"'\", '') LIKE ?",
                        ["%{$qNoApostrophe}%"]
                    );
            })
            ->orderBy('nomi')
            ->limit(50)
            ->get()
            ->map(function ($s) {
                return [
                    'id'             => $s->id,
                    'nomi'           => $s->nomi,
                    'teacher'        => $s->teacher['To‘liq_ismi'] ?? 'Tayinlanmagan',
                    'category'       => $s->category->nomi ?? ($s->category->guruh ?? '—'),
                    'oquv_yili'      => $s->oquv_yili->nomi ?? '—',
                    'semster'        => $s->semster,
                    'already_linked' => !is_null($s->subjects_to_subject_id),
                ];
            });

        return response()->json($subjects);
    }

    /**
     * Tanlangan fanlarni yangi (yoki mavjud) subjects_to_subject ga biriktiradi.
     */
    public function biriktirishStore(Request $request)
    {
        $request->validate([
            'nomi'          => 'required|string|max:255',
            'subject_ids'   => 'required|array|min:1',
            'subject_ids.*' => 'integer|exists:subjects,id',
        ]);

        $nomiNormalized = $this->normalizeNomi($request->input('nomi'));

        $existingGroup = SubjectsToSubject::get()
            ->first(fn($g) => $this->normalizeNomi($g->nomi) === $nomiNormalized);

        $group = $existingGroup ?? SubjectsToSubject::create(['nomi' => $nomiNormalized]);

        subject::whereIn('id', $request->input('subject_ids'))
            ->update(['subjects_to_subject_id' => $group->id]);

        $count = count($request->input('subject_ids'));

        return redirect()
            ->route('subject.biriktirish')
            ->with('success', "{$count} ta fan \"{$group->nomi}\" guruhiga muvaffaqiyatli biriktirildi.");
    }

    /**
     * Barcha bir xil nomdagi fanlarni avtomatik guruhlaydi (sinxron).
     */
    public function biriktirishSync()
    {
        $subjects = subject::select('id', 'nomi')
            ->whereNotNull('nomi')
            ->where('nomi', '!=', '')
            ->get();

        $grouped = $subjects->groupBy(function ($s) {
            return $this->normalizeNomi($s->nomi);
        });

        $existingGroups = SubjectsToSubject::all();
        $existingMap = [];
        foreach ($existingGroups as $g) {
            $existingMap[$this->normalizeNomi($g->nomi)] = $g;
        }

        $linked = 0;
        $groupsCreated = 0;

        foreach ($grouped as $normalizedNomi => $items) {
            if ($normalizedNomi === '') {
                continue;
            }

            if (isset($existingMap[$normalizedNomi])) {
                $group = $existingMap[$normalizedNomi];
            } else {
                $displayNomi = $items->first()->nomi;
                $group = SubjectsToSubject::create(['nomi' => $displayNomi]);
                $existingMap[$normalizedNomi] = $group;
                $groupsCreated++;
            }

            $ids = $items->pluck('id');

            $updated = subject::whereIn('id', $ids)
                ->where(function ($q) use ($group) {
                    $q->whereNull('subjects_to_subject_id')
                        ->orWhere('subjects_to_subject_id', '!=', $group->id);
                })
                ->update(['subjects_to_subject_id' => $group->id]);

            $linked += $updated;
        }

        return redirect()
            ->route('subject.biriktirish')
            ->with('success', "Sinxron yakunlandi: {$groupsCreated} ta yangi guruh, {$linked} ta fan biriktirildi.");
    }

    /**
     * Fan nomidagi apostrof/qo'shtirnoq belgilarni bir xillashtirish.
     */
    private function normalizeNomi(?string $nomi): string
    {
        if (is_null($nomi)) {
            return '';
        }

        $variants = ["’", "‘", "`", "´", "ʻ", "ʼ", "′", "‛"];
        $nomi = str_replace($variants, "'", $nomi);
        $nomi = preg_replace('/\s+/u', ' ', $nomi);

        return trim($nomi);
    }
}