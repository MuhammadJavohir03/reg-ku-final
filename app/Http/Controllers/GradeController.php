<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\GradeImport;
use App\Models\grade;
use Maatwebsite\Excel\Facades\Excel;

class GradeController extends Controller
{
    public function import(Request $request, $subject_id)
    {
        // Faylni tekshirish
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            // Import klasiga subject_id ni berib yuboramiz
            Excel::import(new GradeImport($subject_id), $request->file('excel_file'));

            return redirect()->back()->with('success', 'Baholar muvaffaqiyatli import qilindi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function index($subject_id)
    {
        $search = request('search');
        $grades = grade::with(['user', 'subject.category'])
            ->where('subject_id', $subject_id)
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('To‘liq_ismi', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(75)
            ->withQueryString();

        return view('grades.index', compact('grades', 'subject_id'));
    }

    public function clearAll($subject_id)
    {
        // 1. Shu fanga tegishli barcha baholarni o'chirib tashlaymiz
        \App\Models\grade::where('subject_id', $subject_id)->delete();

        // 2. O'chirib bo'lingach, to'g'ridan-to'g'ri fanlar ro'yxatiga (subject.index) qaytaramiz
        return redirect()->route('subject.index')->with('success', 'Fanning barcha baholari muvaffaqiyatli tozalandi.');
    }
}
