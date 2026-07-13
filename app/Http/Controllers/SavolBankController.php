<?php

namespace App\Http\Controllers;

use App\Models\Bolim;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\subject;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;

class SavolBankController extends Controller
{
    public function index(Request $request)
    {
        $bolim   = Bolim::where('status', 1)->first();
        $subject = subject::all();
        $banklar = QuestionBank::where('bolim_id', $bolim?->id)
            ->withCount('questions')
            ->get();

        return view('savol_bank.index', compact('banklar', 'bolim', 'subject'));
    }

    public function store(Request $request)
    {
        $request->validate(['nomi' => 'required|string|max:255']);
        $bolim = Bolim::where('status', 1)->first();
        QuestionBank::create([
            'nomi'     => $request->nomi,
            'bolim_id' => $bolim?->id,
            // tur yo'q — null bo'lib yaratiladi
        ]);
        return redirect()->back()->with('success', 'Savol bank yaratildi!');
    }

    public function import(Request $request, $bank_id)
    {
        $bank = QuestionBank::findOrFail($bank_id);

        // Faqat tur saqlash
        if ($request->action === 'save_tur') {
            $bank->update(['tur' => $request->tur]);
            return redirect()->back()->with('success', 'Tur saqlandi!');
        }

        // Import validatsiya
        $request->validate([
            'docx_file' => 'required|file|mimes:docx',
        ]);

        // Tur saqlash
        if ($request->tur) {
            $bank->update(['tur' => $request->tur]);
        }

        // Tozalash
        if ($request->tozalash) {
            $bank->questions()->delete();
        }

        // Docx o'qish
        $path    = $request->file('docx_file')->getPathname();
        $phpWord = IOFactory::load($path);
        $fullText = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $fullText .= $element->getText() . "\n";
                } elseif (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $child) {
                        if (method_exists($child, 'getText')) {
                            $fullText .= $child->getText() . "\n";
                        }
                    }
                }
            }
        }

        // Parse qilish
        $savollar = $this->parseQuestions($fullText);

        if (empty($savollar)) {
            return redirect()->back()->withErrors(['docx_file' => 'Faylda hech qanday savol topilmadi! Format to\'g\'riligini tekshiring.']);
        }

        // Saqlash
        foreach ($savollar as $savol) {
            Question::create([
                'bank_id'     => $bank->id,
                'savol'       => $savol['savol'],
                'togri_javob' => $savol['togri_javob'],
                'variant_1'   => $savol['variant_1'],
                'variant_2'   => $savol['variant_2'],
                'variant_3'   => $savol['variant_3'] ?? null,
                'variant_4'   => $savol['variant_4'] ?? null,
                'variant_5'   => $savol['variant_5'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', count($savollar) . ' ta savol muvaffaqiyatli yuklandi!');
    }

    public function destroy($id)
    {
        QuestionBank::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Bank o\'chirildi!');
    }

    public function destroyQuestion($id)
    {
        Question::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Savol o\'chirildi!');
    }

    private function parseQuestions($text)
    {
        $savollar = [];
        $lines    = explode("\n", $text);
        $lines    = array_map('trim', $lines);
        $lines    = array_filter($lines, fn($l) => $l !== '');
        $lines    = array_values($lines);

        $i = 0;
        while ($i < count($lines)) {
            if (isset($lines[$i + 1]) && trim($lines[$i + 1]) === '{') {
                $savolMatni = html_entity_decode($lines[$i], ENT_QUOTES, 'UTF-8');
                $i += 2;

                $togri   = null;
                $notogri = [];

                while ($i < count($lines) && trim($lines[$i]) !== '}') {
                    $line = $lines[$i];
                    if (str_starts_with($line, '~')) {
                        $togri = html_entity_decode(ltrim($line, '~'), ENT_QUOTES, 'UTF-8');
                    } elseif (str_starts_with($line, '#')) {
                        $notogri[] = html_entity_decode(ltrim($line, '#'), ENT_QUOTES, 'UTF-8');
                    }
                    $i++;
                }
                $i++;

                if ($togri && count($notogri) >= 1) {
                    $variantlar = array_merge([$togri], $notogri);
                    shuffle($variantlar);

                    $togriIndex = null;
                    $result     = [];

                    foreach ($variantlar as $index => $variant) {
                        $key = 'variant_' . ($index + 1);
                        $result[$key] = $variant;
                        if ($variant === $togri) {
                            $togriIndex = $index + 1;
                        }
                    }

                    $savollar[] = array_merge([
                        'savol'        => $savolMatni,
                        'togri_javob'  => (string) $togriIndex,
                        'variant_3'    => null,
                        'variant_4'    => null,
                        'variant_5'    => null,
                    ], $result);
                }
            } else {
                $i++;
            }
        }

        return $savollar;
    }
    public function show($bank_id)
    {
        $bank     = QuestionBank::with('questions')->findOrFail($bank_id);
        $savollar = $bank->questions()->paginate(500);

        return view('savol_bank.show_savol', compact('bank', 'savollar'));
    }

    public function updateQuestion(Request $request, $id)
    {
        $request->validate([
            'savol'       => 'required|string',
            'togri_javob' => 'required',
            'variant_1'   => 'required|string',
            'variant_2'   => 'required|string',
        ]);

        Question::findOrFail($id)->update([
            'savol'       => $request->savol,
            'togri_javob' => $request->togri_javob,
            'variant_1'   => $request->variant_1,
            'variant_2'   => $request->variant_2,
            'variant_3'   => $request->variant_3,
            'variant_4'   => $request->variant_4,
            'variant_5'   => $request->variant_5,
        ]);

        return redirect()->back()->with('success', 'Savol yangilandi!');
    }
}
