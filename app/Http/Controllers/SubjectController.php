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

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        $pageSize = request('page_size', 10);

        $subjects = subject::with(['category', 'teacher', 'kafedra', 'lesson_type'])
            ->withExists('grades')
            ->when($search, function ($query, $search) {
                // Fan nomi yoki fan biriktirilgan o'qituvchining to'liq ismi bo'yicha qidiradi
                return $query->where(function ($q) use ($search) {
                    $q->where('nomi', 'like', "%{$search}%")
                        ->orWhereHas('teacher', function ($q2) use ($search) {
                            $q2->where('To‘liq_ismi', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate($pageSize)
            ->withQueryString();

        return view('subject.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Faqat 'teacher' rolidagi foydalanuvchilarni olamiz (edit() bilan bir xil mantiq)
        $teachers = User::where('role', 'teacher')->get();
        $categories = category::all();
        $kafedralar = kafedra::all();
        $fakultetlar = fakultet::all();
        $lesson_types = lesson_type::all();
        $oquv_yillari = OquvYili::all(); // O'quv yillari ro'yxatini olish
        return view('subject.create', compact('teachers', 'categories', 'kafedralar', 'fakultetlar', 'lesson_types', 'oquv_yillari'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request)
    {
        $subject = subject::create([
            'nomi' => $request->input('nomi'),
            'category_id' => $request->input('category_id'),
            'kafedra_id' => $request->input('kafedra_id'),
            'fakultet_id' => $request->input('fakultet_id'),
            'oquv_yili_id' => $request->input('oquv_yili_id'),
            'talim_tili' => $request->input('talim_tili'),
            'teacher_id' => $request->input('teacher_id'),
            'lesson_type_id' => $request->input('lesson_type_id'),
            'semster' => $request->input('semster'),
            'kredit' => $request->input('kredit'),
        ]);

        return redirect()->route('subject.index')->with('success', 'Fan muvaffaqiyatli yaratildi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(subject $subject)
    {
        return view('subject.show', compact('subject'));
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
        return view('subject.edit', compact('subject', 'teachers', 'categories', 'kafedralar', 'fakultetlar', 'lesson_types', 'oquv_yillari'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSubjectRequest $request, subject $subject)
    {
        // Eslatma: teacher_id 'users' jadvaliga ishora qiladi (User modeli), 'teachers' emas
        $request->validate([
            'nomi' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'kafedra_id' => 'nullable|exists:kafedra,id',
            'fakultet_id' => 'nullable|exists:fakultet,id',
            'oquv_yili_id' => 'nullable|exists:oquv_yili,id',
            'talim_tili' => 'nullable|string|max:255',
            'kredit' => 'nullable|integer|min:0',
            'teacher_id' => 'nullable|exists:users,id',
            'lesson_type_id' => 'nullable|exists:lesson_types,id',
            'semster' => 'required|integer|min:1|max:8',
            'kredit' => 'required|integer|min:1|max:10',
        ]);

        $subject->update([
            'nomi' => $request->input('nomi'),
            'category_id' => $request->input('category_id'),
            'kafedra_id' => $request->input('kafedra_id'),
            'fakultet_id' => $request->input('fakultet_id'),
            'oquv_yili_id' => $request->input('oquv_yili_id'),
            'talim_tili' => $request->input('talim_tili'),
            'teacher_id' => $request->input('teacher_id'),
            'lesson_type_id' => $request->input('lesson_type_id'),
            'semster' => $request->input('semster'),
            'kredit' => $request->input('kredit'),
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
}
