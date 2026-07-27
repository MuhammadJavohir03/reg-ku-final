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
                // Faqat va faqat fanning nomi ustunidan qidiradi
                return $query->where('nomi', 'like', "%{$search}%");
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
        return view('subject.create', compact('teachers', 'categories', 'kafedralar', 'fakultetlar', 'lesson_types'));
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
        return view('subject.edit', compact('subject', 'teachers', 'categories', 'kafedralar', 'fakultetlar', 'lesson_types'));
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
            'kafedra_id' => 'nullable|exists:kafedras,id',
            'fakultet_id' => 'nullable|exists:fakultets,id',
            'kredit' => 'nullable|integer|min:0',
            'teacher_id' => 'nullable|exists:users,id',
            'lesson_type_id' => 'required|exists:lesson_types,id',
            'semster' => 'required|integer|min:1|max:8',
            'kredit' => 'required|integer|min:1|max:10',
        ]);

        $subject->update([
            'nomi' => $request->input('nomi'),
            'category_id' => $request->input('category_id'),
            'kafedra_id' => $request->input('kafedra_id'),
            'fakultet_id' => $request->input('fakultet_id'),
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