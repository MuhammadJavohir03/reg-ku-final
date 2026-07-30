<?php

namespace App\Http\Controllers;

use App\Models\bolim;
use App\Models\subject;
use App\Models\free_semestr;
use App\Models\mini_semestr;
use App\Models\MsMavzu;
use App\Models\MsJoriyBaho;
use App\Models\GradeEditLog;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use ZipArchive;

class JurnalController extends Controller
{
    /**
     * Jurnal sahifasi - chap panelda bo'limlar ro'yxati bilan ochiladi.
     */
    public function index()
    {
        // MUHIM: agar bazada ustun 'nomi' emas, 'name' bo'lsa,
        // shu yerdagi orderBy('nomi') ni orderBy('name') ga o'zgartiring
        // va blade faylda {{ $bolim->nomi }} o'rniga {{ $bolim->name }} yozing.
        $bolimlar = bolim::orderBy('nomi')->get();

        return view('jurnal.index', compact('bolimlar'));
    }

    /**
     * Bo'lim + maktab turi tanlangach, o'sha ikkalasiga tegishli fanlar ro'yxatini qaytaradi.
     * (AJAX: GET /jurnal/subjects?bolim_id=..&type=free|mini)
     */
    public function subjectsByType(Request $request)
    {
        $request->validate([
            'bolim_id' => 'required|integer',
            'type'     => 'required|in:free,mini',
        ]);

        $model = $request->type === 'free' ? free_semestr::class : mini_semestr::class;

        $subjectIds = $model::where('bolim_id', $request->bolim_id)
            ->distinct()
            ->pluck('subject_id');

        $subjectsQuery = subject::whereIn('id', $subjectIds);

        // Teacher bo'lsa faqat o'ziga tegishli fanlar, admin/boshqalar uchun cheklovsiz
        if (auth()->user()?->role === 'teacher') {
            $subjectsQuery->where('teacher_id', auth()->id());
        }

        $subjects = $subjectsQuery->orderBy('nomi')->get(['id', 'nomi']);

        return response()->json($subjects);
    }

    /**
     * Faqat MINI uchun: bo'lim + fan bo'yicha faol mavzular ro'yxatini qaytaradi.
     * Tartib: avval "mavzu" turlari, keyin "oraliq", keyin "yakuniy" (chapdan o'ngga),
     * har bir tur ichida esa 'tartib' ustuni bo'yicha.
     * (AJAX: GET /jurnal/topics?bolim_id=..&subject_id=..)
     *
     * ESLATMA: FIELD() funksiyasi MySQL/MariaDB uchun. Agar PostgreSQL ishlatilsa,
     * orderByRaw ni CASE WHEN tur='mavzu' THEN 1 WHEN tur='oraliq' THEN 2 ELSE 3 END ga almashtiring.
     */
    public function topicsList(Request $request)
    {
        $request->validate([
            'bolim_id'   => 'required|integer',
            'subject_id' => 'required|integer',
        ]);

        $this->ensureSubjectAccessOrAbort((int) $request->subject_id);

        $mavzular = MsMavzu::where('bolim_id', $request->bolim_id)
            ->where('subject_id', $request->subject_id)
            ->where('faol', 1)
            ->where('tur', 'mavzu')
            ->orderBy('tartib')
            ->get(['id', 'nomi', 'tur', 'tartib']);

        return response()->json($mavzular);
    }

    /**
     * Bo'lim + maktab turi + fan tanlangach, talabalar ro'yxatini (baholari bilan) qaytaradi.
     * mini uchun: har bir mavzu bo'yicha baho + har bir ustun uchun "qo'lda o'zgartirilganmi" (edited) belgisi.
     * joriy_oraliq va umumiy - agar bazada qiymat NULL bo'lsa, avtomatik hisoblanadi
     * (joriy_oraliq = joriy_baho + oraliq_baho; umumiy = joriy_oraliq + yakuniy_baho).
     * Agar bu ustunlarga qo'lda qiymat kiritilgan bo'lsa (bazada NULL emas), o'sha qiymat ko'rsatiladi.
     * (AJAX: GET /jurnal/students?bolim_id=..&type=free|mini&subject_id=..)
     */
    public function students(Request $request)
    {
        $request->validate([
            'bolim_id'   => 'required|integer',
            'type'       => 'required|in:free,mini',
            'subject_id' => 'required|integer',
        ]);

        $this->ensureSubjectAccessOrAbort((int) $request->subject_id);

        if ($request->type === 'free') {
            $records = free_semestr::with('user')
                ->where('bolim_id', $request->bolim_id)
                ->where('subject_id', $request->subject_id)
                ->get();

            $editedIds = GradeEditLog::where('editable_type', 'free_yakuniy')
                ->whereIn('record_id', $records->pluck('id'))
                ->pluck('record_id')
                ->unique();

            $data = $records
                ->filter(fn($r) => $r->user !== null)
                ->map(function ($r) use ($editedIds) {
                    return [
                        'record_id'           => $r->id,
                        'user_id'             => $r->user->id,
                        'name'                => $r->user->{'To‘liq_ismi'} ?? '—',
                        'group'               => $r->user->Guruh ?? '—',
                        'yakuniy_baho'        => $r->yakuniy_baho,
                        'yakuniy_baho_edited' => $editedIds->contains($r->id),
                    ];
                })
                ->values();

            return response()->json($data);
        }

        // ---------- MINI ----------

        $records = mini_semestr::with('user')
            ->where('bolim_id', $request->bolim_id)
            ->where('subject_id', $request->subject_id)
            ->get();

        // Shu fan mavzulari (faqat faol - jadval sarlavhasi bilan bir xil bo'lishi uchun)
        $mavzuIds = MsMavzu::where('bolim_id', $request->bolim_id)
            ->where('subject_id', $request->subject_id)
            ->where('faol', 1)
            ->pluck('id')
            ->unique();

        $userIds = $records->pluck('user_id')->unique();

        $baholar = MsJoriyBaho::whereIn('user_id', $userIds)
            ->whereIn('mavzu_id', $mavzuIds)
            ->get()
            ->groupBy('user_id');

        // "joriy_baho / oraliq_baho / yakuniy_baho" ustunlari bo'yicha qo'lda o'zgartirilgan yozuvlar
        $summaryEdits = GradeEditLog::where('editable_type', 'mini_summary')
            ->whereIn('record_id', $records->pluck('id'))
            ->get(['record_id', 'field'])
            ->map(fn($l) => $l->record_id . '|' . $l->field)
            ->unique();

        // Mavzu (topic) baholari bo'yicha qo'lda o'zgartirilgan yozuvlar
        $topicEdits = GradeEditLog::where('editable_type', 'mini_topic')
            ->whereIn('student_id', $userIds)
            ->whereIn('mavzu_id', $mavzuIds)
            ->get(['student_id', 'mavzu_id'])
            ->map(fn($l) => $l->student_id . '|' . $l->mavzu_id)
            ->unique();

        $data = $records
            ->filter(fn($r) => $r->user)
            ->map(function ($r) use ($baholar, $summaryEdits, $topicEdits) {

                $topicMap = $baholar
                    ->get($r->user_id, collect())
                    ->pluck('baho', 'mavzu_id')
                    ->toArray();

                $editedTopics = [];
                foreach (array_keys($topicMap) as $mavzuId) {
                    if ($topicEdits->contains($r->user_id . '|' . $mavzuId)) {
                        $editedTopics[] = (int) $mavzuId;
                    }
                }

                $joriyBaho   = $r->joriy_baho;
                $oraliqBaho  = $r->oraliq_baho;
                $yakuniyBaho = $r->yakuniy_baho;

                // joriy_oraliq: bazada qiymat bo'lsa (qo'lda kiritilgan) o'shani ko'rsatamiz,
                // aks holda avtomatik hisoblaymiz.
                $joriyOraliq = $r->joriy_oraliq !== null
                    ? $r->joriy_oraliq
                    : (($joriyBaho !== null && $oraliqBaho !== null) ? $joriyBaho + $oraliqBaho : null);

                // umumiy: xuddi shunday mantiq
                $umumiy = $r->umumiy !== null
                    ? $r->umumiy
                    : (($joriyOraliq !== null && $yakuniyBaho !== null) ? $joriyOraliq + $yakuniyBaho : null);

                return [
                    'record_id'    => $r->id,
                    'user_id'      => $r->user->id,
                    'name'         => $r->user->{'To‘liq_ismi'} ?? '—',
                    'group'        => $r->user->Guruh ?? '—',
                    'talaba_id'     => $r->user->Talaba_ID ?? '—',
                    'topics'       => $topicMap,
                    'edited_topics' => $editedTopics,

                    'joriy_baho'         => $joriyBaho,
                    'joriy_baho_edited'  => $summaryEdits->contains($r->id . '|joriy_baho'),

                    'oraliq_baho'        => $oraliqBaho,
                    'oraliq_baho_edited' => $summaryEdits->contains($r->id . '|oraliq_baho'),

                    'joriy_oraliq'        => $joriyOraliq,
                    'joriy_oraliq_manual' => $r->joriy_oraliq !== null, // true = qo'lda kiritilgan (avtomatik emas)

                    'yakuniy_baho'        => $yakuniyBaho,
                    'yakuniy_baho_edited' => $summaryEdits->contains($r->id . '|yakuniy_baho'),

                    'umumiy'        => $umumiy,
                    'umumiy_manual' => $r->umumiy !== null,
                ];
            })
            ->values();

        return response()->json($data);
    }

    /**
     * Bo'lim + maktab turi + fan bo'yicha talabalar baholarini Excel (.xlsx) formatida eksport qiladi.
     *
     * ESLATMA (YANGI): endi ikkita rejimda ishlaydi ('rejim' parametri orqali):
     *   - 'yagona'   (default): barcha talabalar BITTA Excel faylda (eski xatti-harakat, o'zgarmagan).
     *   - 'guruhlab': har bir guruh (Guruh ustuni) uchun ALOHIDA Excel yaratiladi va
     *                 barchasi bitta ZIP arxivda yuklab beriladi (vedomost eksportidagi mantiq bilan bir xil).
     *
     * Har qanday holatda 5 ta ustun chiqadi:
     * Joriy baho, Oraliq baho, Joriy+Oraliq, Yakuniy baho, Umumiy baho.
     * Fayl nomi: {bolim_nomi}_{fan_nomi}_{maktab_turi}.xlsx (yoki _guruhlab.zip)
     * (GET/POST /jurnal/export?bolim_id=..&type=free|mini&subject_id=..&rejim=yagona|guruhlab)
     */
    public function export(Request $request)
    {
        $request->validate([
            'bolim_id'   => 'required|integer',
            'type'       => 'required|in:free,mini',
            'subject_id' => 'required|integer',
            'rejim'      => 'nullable|in:yagona,guruhlab',
        ]);

        $this->ensureSubjectAccessOrAbort((int) $request->subject_id);

        $bolimModel   = bolim::findOrFail($request->bolim_id);
        $subjectModel = subject::findOrFail($request->subject_id);

        $rows  = $this->collectExportRows((int) $request->bolim_id, $request->type, (int) $request->subject_id);
        $rejim = $request->input('rejim', 'yagona');

        $typeLabel = $request->type === 'free' ? 'Bepul_maktab' : 'Mini_semestr';
        $baseName  = $this->sanitizeFileName($bolimModel->nomi) . '_'
            . $this->sanitizeFileName($subjectModel->nomi) . '_'
            . $typeLabel;

        if ($rejim === 'guruhlab') {
            return $this->exportGroupedZip($rows, $subjectModel->nomi, $baseName);
        }

        return $this->exportSingleFile($rows, $subjectModel->nomi, $baseName);
    }

    /**
     * YAGONA rejim: barcha talabalarni bitta Excel faylga yozadi va yuklab beradi.
     * (Eski export() metodining asosiy qismi shu yerga ko'chirildi, mantiq o'zgarmagan.)
     */
    private function exportSingleFile(array $rows, string $subjectNomi, string $baseName)
    {
        $spreadsheet = $this->buildSpreadsheetForRows($rows, $subjectNomi);

        $fileName = $baseName . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * GURUHLAB rejim: talabalarni 'group' (Guruh) ustuni bo'yicha guruhlarga bo'ladi,
     * har bir guruh uchun alohida Excel yaratadi va barchasini bitta ZIP arxivga yig'ib yuklab beradi.
     * Mantiq VedomostController'dagi ZIP eksport bilan bir xil g'oyaga asoslangan.
     */
    private function exportGroupedZip(array $rows, string $subjectNomi, string $baseName)
    {
        $grouped = collect($rows)->groupBy(fn($row) => $row['group'] ?: 'Nomalum_guruh');

        if ($grouped->isEmpty()) {
            abort(404, 'Eksport qilish uchun ma\'lumot topilmadi.');
        }

        $tmpDir = storage_path('app/tmp/jurnal_export_' . uniqid());
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $zipFileName = $baseName . '_guruhlab.zip';
        $zipPath = $tmpDir . '/' . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'ZIP arxiv yaratib bo\'lmadi.');
        }

        $generatedFiles = [];

        foreach ($grouped as $guruhNomi => $guruhRows) {
            $spreadsheet = $this->buildSpreadsheetForRows($guruhRows->values()->all(), $subjectNomi);
            $writer = new Xlsx($spreadsheet);

            $groupFileName = $this->sanitizeFileName((string) $guruhNomi) . '.xlsx';
            $groupFilePath = $tmpDir . '/' . $groupFileName;

            $writer->save($groupFilePath);
            $zip->addFile($groupFilePath, $groupFileName);
            $generatedFiles[] = $groupFilePath;
        }

        $zip->close();

        // Alohida .xlsx fayllar endi ZIP ichida, disk ustidagi nusxalarga endi ehtiyoj yo'q
        foreach ($generatedFiles as $filePath) {
            @unlink($filePath);
        }

        // Javob yuborilgach vaqtinchalik papkani ham tozalaymiz
        app()->terminating(function () use ($tmpDir) {
            @rmdir($tmpDir);
        });

        return response()->download($zipPath, $zipFileName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Berilgan qatorlar (talaba + baholar) asosida bitta Excel Spreadsheet obyektini quradi.
     * Ham "yagona" (barcha talabalar), ham "guruhlab" (bitta guruh talabalari) rejimida ishlatiladi.
     */
    private function buildSpreadsheetForRows(array $rows, string $subjectNomi): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Baholar');

        // ---------- Sarlavhalar ----------
        // 1-qator: F.I.O (A1:A2 birlashtiriladi) | Fan nomi (B1:F1 birlashtiriladi)
        $sheet->mergeCells('A1:A2');
        $sheet->setCellValue('A1', 'Talaba F.I.O.');

        $sheet->mergeCells('B1:F1');
        $sheet->setCellValue('B1', $subjectNomi);

        $sheet->setCellValue('B2', 'Joriy baho');
        $sheet->setCellValue('C2', 'Oraliq baho');
        $sheet->setCellValue('D2', 'Joriy+Oraliq');
        $sheet->setCellValue('E2', 'Yakuniy baho');
        $sheet->setCellValue('F2', 'Umumiy baho');

        // ---------- Sarlavha stili ----------
        $headerRange = 'A1:F2';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('EEEDFE');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // ---------- Ma'lumotlar ----------
        $rowNum = 3;
        foreach ($rows as $row) {
            $sheet->setCellValue("A{$rowNum}", $row['name']);
            $sheet->setCellValue("B{$rowNum}", $row['joriy_baho']);
            $sheet->setCellValue("C{$rowNum}", $row['oraliq_baho']);
            $sheet->setCellValue("D{$rowNum}", $row['joriy_oraliq']);
            $sheet->setCellValue("E{$rowNum}", $row['yakuniy_baho']);
            $sheet->setCellValue("F{$rowNum}", $row['umumiy']);
            $rowNum++;
        }

        $lastRow = $rowNum - 1;
        if ($lastRow >= 3) {
            $sheet->getStyle("A3:F{$lastRow}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("B3:F{$lastRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // ---------- Ustun kengliklari ----------
        $sheet->getColumnDimension('A')->setWidth(32);
        foreach (['B', 'C', 'D', 'E', 'F'] as $col) {
            $sheet->getColumnDimension($col)->setWidth(14);
        }

        return $spreadsheet;
    }

    /**
     * Eksport uchun bo'lim+fan+maktab turiga tegishli har bir talabaning
     * FIO, guruhi va 5 ta baho ustuni (joriy, oraliq, joriy_oraliq, yakuniy, umumiy) ni tayyorlaydi.
     * Mantiq students() metodidagi bilan bir xil: bazadagi qiymat bo'lsa o'shani,
     * bo'lmasa avtomatik hisoblangan qiymatni oladi.
     *
     * YANGI: endi har bir qatorda 'group' kaliti ham bor - "guruhlab" eksport rejimi
     * shu qiymat bo'yicha talabalarni ajratadi.
     */
    private function collectExportRows(int $bolimId, string $type, int $subjectId): array
    {
        if ($type === 'free') {
            return free_semestr::with('user')
                ->where('bolim_id', $bolimId)
                ->where('subject_id', $subjectId)
                ->get()
                ->filter(fn($r) => $r->user !== null)
                ->map(function ($r) {
                    $joriyBaho   = $r->joriy_baho;
                    $oraliqBaho  = $r->oraliq_baho;
                    $yakuniyBaho = $r->yakuniy_baho;

                    $joriyOraliq = $r->joriy_oraliq !== null
                        ? $r->joriy_oraliq
                        : (($joriyBaho !== null && $oraliqBaho !== null) ? $joriyBaho + $oraliqBaho : null);

                    $umumiy = $r->umumiy !== null
                        ? $r->umumiy
                        : (($joriyOraliq !== null && $yakuniyBaho !== null) ? $joriyOraliq + $yakuniyBaho : null);

                    return [
                        'name'         => $r->user->{'To‘liq_ismi'} ?? '—',
                        'group'        => $r->user->Guruh ?? null,
                        'joriy_baho'   => $joriyBaho,
                        'oraliq_baho'  => $oraliqBaho,
                        'joriy_oraliq' => $joriyOraliq,
                        'yakuniy_baho' => $yakuniyBaho,
                        'umumiy'       => $umumiy,
                    ];
                })
                ->values()
                ->all();
        }

        // ---------- MINI ----------
        return mini_semestr::with('user')
            ->where('bolim_id', $bolimId)
            ->where('subject_id', $subjectId)
            ->get()
            ->filter(fn($r) => $r->user !== null)
            ->map(function ($r) {
                $joriyBaho   = $r->joriy_baho;
                $oraliqBaho  = $r->oraliq_baho;
                $yakuniyBaho = $r->yakuniy_baho;

                $joriyOraliq = $r->joriy_oraliq !== null
                    ? $r->joriy_oraliq
                    : (($joriyBaho !== null && $oraliqBaho !== null) ? $joriyBaho + $oraliqBaho : null);

                $umumiy = $r->umumiy !== null
                    ? $r->umumiy
                    : (($joriyOraliq !== null && $yakuniyBaho !== null) ? $joriyOraliq + $yakuniyBaho : null);

                return [
                    'name'         => $r->user->{'To‘liq_ismi'} ?? '—',
                    'group'        => $r->user->Guruh ?? null,
                    'joriy_baho'   => $joriyBaho,
                    'oraliq_baho'  => $oraliqBaho,
                    'joriy_oraliq' => $joriyOraliq,
                    'yakuniy_baho' => $yakuniyBaho,
                    'umumiy'       => $umumiy,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Teacher rolidagi foydalanuvchi faqat o'ziga biriktirilgan (subject.teacher_id)
     * fanlarga kira oladi. Admin va boshqa rollar uchun cheklov yo'q.
     * Ruxsat bo'lmasa 403 bilan to'xtatadi (frontend dropdown'dan tashqari,
     * to'g'ridan-to'g'ri AJAX so'rov yuborilgan holatlar uchun ham himoya).
     */
    private function ensureSubjectAccessOrAbort(int $subjectId): void
    {
        $user = auth()->user();

        if ($user?->role !== 'teacher') {
            return;
        }

        $belongsToTeacher = subject::where('id', $subjectId)
            ->where('teacher_id', $user->id)
            ->exists();

        if (!$belongsToTeacher) {
            abort(403, 'Bu fanga kirish huquqingiz yo\'q.');
        }
    }

    /**
     * Fayl nomi uchun xavfsiz matn: bo'sh joy -> "_", ruxsat etilmagan belgilar olib tashlanadi.
     */
    private function sanitizeFileName(?string $value): string
    {
        $value = $value ?? 'nomsiz';
        $value = trim($value);
        $value = preg_replace('/\s+/u', '_', $value);
        $value = preg_replace('/[\/\\\\:*?"<>|]/u', '', $value);
        return $value === '' ? 'nomsiz' : $value;
    }

    /**
     * free_semestr / mini_semestr jadvalidagi umumiy ustunlarni yangilash.
     * value = null yuborilsa - qiymat tozalanadi (joriy_oraliq/umumiy uchun bu avtomatik
     * hisoblashga qaytishni anglatadi).
     * (AJAX: POST /jurnal/grade  { type, record_id, field, value })
     */
    public function updateGrade(Request $request)
    {
        $request->validate([
            'type'      => 'required|in:free,mini',
            'record_id' => 'required|integer',
            'field'     => 'required|string',
        ]);

        $allowedFields = [
            'free' => ['yakuniy_baho'],
            'mini' => ['joriy_baho', 'oraliq_baho', 'yakuniy_baho'],
        ];

        if (!in_array($request->field, $allowedFields[$request->type], true)) {
            return response()->json(['message' => 'Bu ustunni bu turda yangilab bo\'lmaydi.'], 422);
        }

        // Har bir ustun uchun maksimal ball: Joriy=40, Oraliq=20, Yakuniy=40
        $maxByField = [
            'joriy_baho'   => 40,
            'oraliq_baho'  => 20,
            'yakuniy_baho' => 40,
        ];
        $max = $maxByField[$request->field] ?? 100;

        $request->validate([
            'value' => "nullable|numeric|min:0|max:{$max}",
        ]);

        $model = $request->type === 'free' ? free_semestr::class : mini_semestr::class;
        $record = $model::findOrFail($request->record_id);

        $old = $record->{$request->field};
        $record->{$request->field} = $request->value;
        $record->save();

        if ($request->type === 'free') {
            // free_semestrda joriy_oraliq o'zgarmas (arizadan tayyor), faqat
            // yakuniy_baho o'zgaradi — umumiy shu yangi qiymatga qarab qayta hisoblanadi
            $record->umumiy = ($record->joriy_oraliq ?? 0) + ($record->yakuniy_baho ?? 0);
            $record->save();
        }

        if ($request->type === 'mini') {
            $this->recalculateMiniSemester(
                $record->user_id,
                $record->subject_id
            );
        }

        $this->logGradeEdit(
            editableType: $request->type === 'free' ? 'free_yakuniy' : 'mini_summary',
            recordId: $record->id,
            field: $request->field,
            studentId: $record->user_id ?? null,
            mavzuId: null,
            old: $old,
            new: $request->value,
            request: $request,
        );

        return response()->json(['success' => true, 'value' => $record->{$request->field}]);
    }

    /**
     * Faqat MINI: bitta talabaning bitta mavzu (ms_mavzular) bo'yicha bahosini saqlaydi/yangilaydi.
     * baho = null yuborilsa - yozuv butunlay o'chiriladi.
     * (AJAX: POST /jurnal/topic-grade  { user_id, mavzu_id, baho })
     */
    public function updateTopicGrade(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|integer|exists:users,id',
            'mavzu_id' => 'required|integer|exists:ms_mavzular,id',
            'baho'     => 'nullable|numeric|min:0|max:100',
        ]);

        $mavzu = MsMavzu::findOrFail($request->mavzu_id);
        $subjectId = $mavzu->subject_id;

        // YANGI: Joriy baho = shu fandagi barcha "mavzu" turidagi baholarning yig'indisi,
        // va bu yig'indi 40 balldan OSHMASLIGI kerak. Shuning uchun yangi qiymat saqlanishidan
        // oldin, "shu mavzudan tashqari qolgan barcha mavzular yig'indisi + yangi qiymat"ni
        // tekshiramiz. Agar 40 dan oshsa - saqlamay, aniq xato xabari bilan qaytaramiz.
        if ($request->baho !== null) {
            $mavzuIds = MsMavzu::where('bolim_id', $mavzu->bolim_id)
                ->where('subject_id', $subjectId)
                ->where('tur', 'mavzu')
                ->pluck('id');

            $boshqaMavzularYigindisi = MsJoriyBaho::where('user_id', $request->user_id)
                ->whereIn('mavzu_id', $mavzuIds)
                ->where('mavzu_id', '!=', $request->mavzu_id)
                ->sum('baho');

            $yangiYigindi = $boshqaMavzularYigindisi + $request->baho;

            if ($yangiYigindi > 40) {
                $maksimalRuxsat = max(0, 40 - $boshqaMavzularYigindisi);

                return response()->json([
                    'message' => "Joriy baho (barcha mavzular yig'indisi) 40 balldan oshmasligi kerak. "
                        . "Boshqa mavzular yig'indisi: {$boshqaMavzularYigindisi} ball, "
                        . "shu mavzu uchun maksimal qiymat: {$maksimalRuxsat} ball.",
                ], 422);
            }
        }

        $existing = MsJoriyBaho::where('user_id', $request->user_id)
            ->where('mavzu_id', $request->mavzu_id)
            ->first();

        $old = $existing?->baho;
        $newId = null;

        if ($request->baho === null) {
            $existing?->delete();
        } else {
            $saved = MsJoriyBaho::updateOrCreate(
                ['user_id' => $request->user_id, 'mavzu_id' => $request->mavzu_id],
                ['baho' => $request->baho]
            );

            $this->recalculateMiniSemester(
                $request->user_id,
                $subjectId
            );

            $newId = $saved->id;
        }

        $this->logGradeEdit(
            editableType: 'mini_topic',
            recordId: null,
            field: null,
            studentId: $request->user_id,
            mavzuId: $request->mavzu_id,
            old: $old,
            new: $request->baho,
            request: $request,
        );

        return response()->json(['success' => true, 'id' => $newId, 'baho' => $request->baho]);
    }

    /**
     * Bitta katakcha bo'yicha o'zgartirishlar tarixini qaytaradi:
     * kim (admin), qachon, eski/yangi qiymat, qaysi IP.
     * (AJAX: GET /jurnal/grade-history?kind=free|summary|topic&record_id=..&field=..&user_id=..&mavzu_id=..)
     */
    public function gradeHistory(Request $request)
    {
        $request->validate([
            'kind'   => 'required|in:free,summary,topic',
            'record' => 'nullable|integer',   // <-- record_id emas, record
            'field'  => 'nullable|string',
            'user'   => 'nullable|integer',
            'mavzu'  => 'nullable|integer',
        ]);

        $query = GradeEditLog::with('editor')->latest();

        if ($request->kind === 'topic') {
            $query->where('editable_type', 'mini_topic')
                ->where('student_id', $request->user)
                ->where('mavzu_id', $request->mavzu);
        } elseif ($request->kind === 'free') {
            $query->where('editable_type', 'free_yakuniy')
                ->where('record_id', $request->record);   // <-- shu yerda ham
        } else { // summary
            $query->where('editable_type', 'mini_summary')
                ->where('record_id', $request->record)     // <-- va shu yerda
                ->where('field', $request->field);
        }

        $logs = $query->get()->map(function ($log) {
            return [
                'admin'      => $log->editor?->{'To‘liq_ismi'} ?? $log->editor?->email ?? 'Noma\'lum',
                'old_value'  => $log->old_value,
                'new_value'  => $log->new_value,
                'ip_address' => $log->ip_address,
                'created_at' => optional($log->created_at)->format('d.m.Y H:i'),
            ];
        });

        return response()->json($logs);
    }

    /**
     * Baho o'zgarishini grade_edit_logs jadvaliga yozadi.
     * Agar eski va yangi qiymat farq qilmasa - yozilmaydi.
     */
    private function logGradeEdit(
        string $editableType,
        ?int $recordId,
        ?string $field,
        ?int $studentId,
        ?int $mavzuId,
        $old,
        $new,
        Request $request
    ): void {
        if ((string) $old === (string) $new) {
            return;
        }

        GradeEditLog::create([
            'editor_id'     => $request->user()?->id,
            'editable_type' => $editableType,
            'record_id'     => $recordId,
            'field'         => $field,
            'student_id'    => $studentId,
            'mavzu_id'      => $mavzuId,
            'old_value'     => $old,
            'new_value'     => $new,
            'ip_address'    => $request->ip(),
        ]);
    }

    private function recalculateMiniSemester(int $userId, int $subjectId): void
    {
        $mini = mini_semestr::where('user_id', $userId)
            ->where('subject_id', $subjectId)
            ->first();

        if (!$mini) {
            return;
        }

        // Faqat shu bo'lim va shu fandagi "mavzu" turlari
        $mavzuIds = MsMavzu::where('bolim_id', $mini->bolim_id)
            ->where('subject_id', $subjectId)
            ->where('tur', 'mavzu')
            ->pluck('id');

        // Joriy baho = barcha mavzular yig'indisi
        $joriy = MsJoriyBaho::where('user_id', $userId)
            ->whereIn('mavzu_id', $mavzuIds)
            ->sum('baho');

        // XAVFSIZLIK UCHUN QO'SHIMCHA CHEKLOV (clamp): updateTopicGrade() da yig'indi
        // 40 dan oshmasligi allaqachon tekshiriladi, lekin shu yerda ham 40 dan
        // oshib ketmasligini kafolatlaymiz (masalan eski ma'lumotlar uchun).
        $joriy = min($joriy, 40);

        // Joriy
        $mini->joriy_baho = $joriy;

        // Joriy + Oraliq (Oraliq ham updateGrade() da 0-20 oralig'ida tekshiriladi)
        $oraliq = min($mini->oraliq_baho ?? 0, 20);
        $mini->joriy_oraliq = $joriy + $oraliq;

        // Umumiy (Yakuniy ham updateGrade() da 0-40 oralig'ida tekshiriladi,
        // shuning uchun umumiy hech qachon 100 dan oshmaydi: 40 + 20 + 40 = 100)
        $yakuniy = min($mini->yakuniy_baho ?? 0, 40);
        $mini->umumiy = $mini->joriy_oraliq + $yakuniy;

        $mini->save();
    }
}