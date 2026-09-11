<?php

namespace App\Http\Controllers;

use App\Imports\BepulImport;
use Illuminate\Http\Request;
use App\Imports\GradeImport;
use App\Imports\HemisImport;
use App\Services\HemisPdfParser;
use App\Models\grade;
use Maatwebsite\Excel\Facades\Excel;

class GradeController extends Controller
{
    public function import(Request $request, $subject_id)
    {

        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {

            $import = new GradeImport($subject_id);
            Excel::import($import, $request->file('excel_file'));

            $xabar = "Yangi qo'shildi: {$import->yangiQoshildi} ta, "
                . "Yangilandi (takroriy): {$import->yangilandi} ta";

            if ($import->talabaTopilmadi > 0) {
                $xabar .= ", Talaba topilmadi: {$import->talabaTopilmadi} ta";
            }

            return redirect()->back()->with('success', $xabar);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }


    public function importBepul(Request $request)
    {
        $request->validate([
            'bepul_excel' => 'required|mimes:xlsx,xls',
        ]);

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $import = new \App\Imports\BepulImport();

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($import, $request) {
                $import->import($request->file('bepul_excel')->getRealPath());
            });

            $xabar = "Jami yangilandi: {$import->yangilandi} ta "
                . "(bepul: {$import->bepulYangilandi}, ball: {$import->ballYangilandi}), "
                . "Grade topilmadi: {$import->topilmadiGrade}, "
                . "Talaba topilmadi: {$import->talabaTopilmadi}, "
                . "Fan topilmadi: {$import->fanTopilmadi}";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $xabar]);
            }

            return redirect()->back()->with('success', $xabar);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[Bepul import] ' . $e->getMessage(), [
                'exception' => $e,
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);

            $xabar = 'Import vaqtida xatolik yuz berdi: ' . $e->getMessage();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $xabar], 500);
            }

            return redirect()->back()->with('error', $xabar);
        }
    }

    public function importHemis(Request $request, $subject_id)
    {
        $request->validate([
            'hemis_pdf' => 'required|mimes:pdf'
        ]);

        try {
            $parser = new \App\Services\HemisPdfParser();
            $rows = $parser->parse($request->file('hemis_pdf')->getRealPath());

            $import = new \App\Imports\HemisImport($subject_id);
            $import->processRows($rows);

            $xabar = "Hemis orqali yangi qo'shildi: {$import->yangiQoshildi} ta, "
                . "Yangilandi (takroriy): {$import->yangilandi} ta";

            if ($import->talabaTopilmadi > 0) {
                $xabar .= ", Talaba topilmadi: {$import->talabaTopilmadi} ta";
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $xabar,
                ]);
            }

            return redirect()->back()->with('success', $xabar);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xatolik yuz berdi: ' . $e->getMessage(),
                ], 500);
            }
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
            ->paginate(200)
            ->withQueryString();

        return view('grades.index', compact('grades', 'subject_id'));
    }

    public function clearAll($subject_id)
    {

        \App\Models\grade::where('subject_id', $subject_id)->delete();

        return redirect()->route('subject.index')->with('success', 'Fanning barcha baholari muvaffaqiyatli tozalandi.');
    }

    public function destroy(grade $grade)
    {
        $grade->delete();
        return redirect()->back()->with('success', 'Natija muvaffaqiyatli o\'chirildi.');
    }

    
    public function update(Request $request, grade $grade)
    {
        if (auth()->user()?->email !== 'javohir8386@gmail.com') {
            return response()->json([
                'success' => false,
                'message' => 'Sizda baholarni tahrirlash uchun ruxsat yo\'q.',
            ], 403);
        }

        $validated = $request->validate([
            'joriy_baho'   => 'required|numeric|min:0',
            'oraliq_baho'  => 'required|numeric|min:0',
            'yakuniy_baho' => 'required|numeric|min:0',
        ]);

        $joriy   = (float) $validated['joriy_baho'];
        $oraliq  = (float) $validated['oraliq_baho'];
        $yakuniy = (float) $validated['yakuniy_baho'];

        $grade->joriy_baho   = $joriy;
        $grade->oraliq_baho  = $oraliq;
        $grade->yakuniy_baho = $yakuniy;
        $grade->joriy_oraliq = $joriy + $oraliq;
        $grade->umumiy       = $joriy + $oraliq + $yakuniy;
        $grade->save();

        return response()->json([
            'success'      => true,
            'joriy_baho'   => $grade->joriy_baho,
            'oraliq_baho'  => $grade->oraliq_baho,
            'yakuniy_baho' => $grade->yakuniy_baho,
            'joriy_oraliq' => $grade->joriy_oraliq,
            'umumiy'       => $grade->umumiy,
        ]);
    }
}
