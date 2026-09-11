<?php

namespace App\Http\Controllers;

use App\Models\Bolim;
use App\Models\mini_semestr;
use App\Models\Subject;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\MsMavzu;
use App\Models\MsMaterial;
use App\Models\MsTopshiriq;
use App\Models\User;
use App\Models\TestSession;
use App\Models\QuestionUser;
use App\Models\SubjectsToSubject;
use App\Models\SubjectsToSubjectTeacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MiniMaktabController extends Controller
{



    public function index()
    {
        $bolimlar = Bolim::paginate(50);
        return view('mini_maktab.index', compact('bolimlar'));
    }



    public function fanlar($bolim_id)
    {
        $bolim = Bolim::findOrFail($bolim_id);

        $query = mini_semestr::where('bolim_id', $bolim_id)
            ->with(['subject.subjectsToSubject', 'teacher']);

        if (auth()->user()?->role === 'teacher') {
            $query->where('teacher_id', auth()->id());
        }

        $arizalar = $query->get();



        $fanlar = $arizalar
            ->groupBy(function ($ariza) {
                return $ariza->subject->subjects_to_subject_id
                    ? 'G' . $ariza->subject->subjects_to_subject_id
                    : 'S' . $ariza->subject_id;
            })
            ->map(function ($guruhArizalari) {
                $vakilSubject = $guruhArizalari->first()->subject;
                $guruh        = $vakilSubject->subjectsToSubject;

                return (object) [
                    'subject'           => $vakilSubject,
                    'nomi'              => $guruh->nomi ?? $vakilSubject->nomi,
                    'arizalar_soni'     => $guruhArizalari->count(),
                    'oqituvchilar_soni' => $guruhArizalari->pluck('teacher_id')->filter()->unique()->count(),
                ];
            })
            ->sortBy('nomi')
            ->values();

        return view('mini_maktab.fanlar', compact('bolim', 'fanlar'));
    }



    public function mavzular(Request $request, $bolim_id, $subject_id)
    {
        $bolim   = Bolim::findOrFail($bolim_id);
        $subject = Subject::with('subjectsToSubject')->findOrFail($subject_id);
        $foydalanuvchi = auth()->user();



        $guruh = $subject->subjectsToSubject;

        $subjectIds = $guruh
            ? Subject::where('subjects_to_subject_id', $guruh->id)->pluck('id')
            : collect([$subject->id]);

        if ($guruh) {
            $guruh->load('teachers.teacher');
        }



        if ($foydalanuvchi->role === 'teacher') {
            $tanlanganTeacherId = $foydalanuvchi->id;
        } else {
            $tanlanganTeacherId = $request->filled('teacher_id') ? (int) $request->teacher_id : null;
        }




        $mavzular = collect(['mavzu' => collect(), 'oraliq' => collect(), 'yakuniy' => collect()]);
        if ($tanlanganTeacherId) {
            $topilganlar = MsMavzu::where('bolim_id', $bolim_id)
                ->where('subject_id', $subject_id)
                ->where('teacher_id', $tanlanganTeacherId)
                ->withCount('materiallar')
                ->orderBy('tartib')
                ->orderBy('id')
                ->get()
                ->groupBy('tur');

            $mavzular = $mavzular->merge($topilganlar);
        }

        $talabalarQuery = mini_semestr::where('bolim_id', $bolim_id)
            ->whereIn('subject_id', $subjectIds)
            ->with(['user', 'teacher']);

        if ($foydalanuvchi->role === 'teacher') {

            $talabalarQuery->where('teacher_id', $foydalanuvchi->id);
        } elseif ($request->filled('teacher_id')) {
            $talabalarQuery->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('ism')) {
            $talabalarQuery->whereHas('user', fn($q) => $q->where('To‘liq_ismi', 'like', '%' . $request->ism . '%'));
        }
        if ($request->filled('guruh_nomi')) {
            $talabalarQuery->whereHas('user', fn($q) => $q->where('Guruh', $request->guruh_nomi));
        }
        if ($request->filled('kurs')) {
            $talabalarQuery->whereHas('user', fn($q) => $q->where('Kurs', $request->kurs));
        }

        $talabalar = $talabalarQuery->paginate(20)->withQueryString();

        $bandSoni = mini_semestr::where('bolim_id', $bolim_id)
            ->whereIn('subject_id', $subjectIds)
            ->whereNotNull('teacher_id')
            ->selectRaw('teacher_id, count(*) as soni')
            ->groupBy('teacher_id')
            ->pluck('soni', 'teacher_id');

        $jamiTalaba     = mini_semestr::where('bolim_id', $bolim_id)->whereIn('subject_id', $subjectIds)->count();
        $bolinmagan     = mini_semestr::where('bolim_id', $bolim_id)->whereIn('subject_id', $subjectIds)->whereNull('teacher_id')->count();
        $kerakliTeacher = $jamiTalaba > 0 ? (int) ceil($jamiTalaba / 30) : 0;

        $oqituvchilar = collect();
        if ($foydalanuvchi->role === 'admin') {
            $oqituvchilar = User::where('role', 'teacher')->get();
        }

        return view('mini_maktab.mavzular', compact(
            'bolim', 'subject', 'guruh', 'mavzular', 'talabalar',
            'bandSoni', 'jamiTalaba', 'bolinmagan', 'kerakliTeacher',
            'oqituvchilar', 'tanlanganTeacherId'
        ));
    }



    public function mavzuYarat(Request $request, $bolim_id, $subject_id)
    {
        $foydalanuvchi = auth()->user();

        $request->validate([
            'nomi' => 'required|string|max:255',
            'tur'  => 'required|in:mavzu,oraliq,yakuniy',
        ]);


        if ($foydalanuvchi->role === 'teacher') {
            $teacherId = $foydalanuvchi->id;
        } else {
            $teacherId = $request->filled('teacher_id') ? (int) $request->teacher_id : null;
        }




        if (! $teacherId || ! User::where('id', $teacherId)->where('role', 'teacher')->exists()) {
            return redirect()->back()->with('error', "Avval o'qituvchini tanlang, keyin mavzu/oraliq/yakuniy yarating!");
        }

        if ($foydalanuvchi->role === 'teacher' && $teacherId !== $foydalanuvchi->id) {
            abort(403);
        }

        $tartib = (int) MsMavzu::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->where('teacher_id', $teacherId)
            ->where('tur', $request->tur)
            ->max('tartib') + 1;

        MsMavzu::create([
            'bolim_id'   => $bolim_id,
            'subject_id' => $subject_id,
            'teacher_id' => $teacherId,
            'nomi'       => $request->nomi,
            'tur'        => $request->tur,
            'tartib'     => $tartib,
        ]);

        $turNomlari = ['mavzu' => 'Mavzu', 'oraliq' => 'Oraliq', 'yakuniy' => 'Yakuniy'];
        return redirect()->back()->with('success', ($turNomlari[$request->tur] ?? 'Mavzu') . ' yaratildi!');
    }



    public function mavzuOchir($id)
    {
        $mavzu = MsMavzu::with('materiallar')->findOrFail($id);

        foreach ($mavzu->materiallar as $material) {
            $this->materialFaylOchir($material);
        }

        $bolim_id   = $mavzu->bolim_id;
        $subject_id = $mavzu->subject_id;
        $mavzu->delete();

        return redirect()->route('mini_maktab.mavzular', [$bolim_id, $subject_id])
            ->with('success', 'Mavzu o\'chirildi!');
    }



    public function mavzuShow($bolim_id, $subject_id, $mavzu_id)
    {
        $bolim   = Bolim::findOrFail($bolim_id);
        $subject = Subject::findOrFail($subject_id);
        $mavzu   = MsMavzu::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->findOrFail($mavzu_id);

        $materiallar = MsMaterial::where('mavzu_id', $mavzu_id)
            ->with('bank')
            ->orderBy('tartib')
            ->get();

        $banklar = QuestionBank::withCount('questions')->get();

        $talabalarList = mini_semestr::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->with('user')
            ->get();

        $topshiriqlarByMaterial = [];
        foreach ($materiallar->where('tur', 'topshiriq') as $tm) {
            $topshiriqlarByMaterial[$tm->id] = MsTopshiriq::where('ms_material_id', $tm->id)
                ->get()
                ->keyBy('user_id');
        }

        $biriktirilganTeacherId = mini_semestr::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->value('teacher_id');

        $oqituvchilar = collect();
        if (auth()->user()?->role === 'admin') {
            $oqituvchilar = User::where('role', 'teacher')->get();
        }

        return view('mini_maktab.mavzu_show', compact(
            'bolim',
            'subject',
            'mavzu',
            'materiallar',
            'banklar',
            'talabalarList',
            'topshiriqlarByMaterial',
            'biriktirilganTeacherId',
            'oqituvchilar'
        ));
    }



    public function materialQosh(Request $request, $mavzu_id)
    {
        $mavzu = MsMavzu::findOrFail($mavzu_id);

        $request->validate([
            'tur'  => 'required|in:test,video,pdf,topshiriq',
            'nomi' => 'required|string|max:255',
        ]);

        $data = [
            'mavzu_id' => $mavzu_id,
            'tur'      => $request->tur,
            'nomi'     => $request->nomi,
            'tartib'   => MsMaterial::where('mavzu_id', $mavzu_id)->max('tartib') + 1,
            'faol'     => 1,
        ];

        if ($request->tur === 'test') {
            $request->validate([
                'bank_id'       => 'required|exists:question_banks,id',
                'savollar_soni' => 'required|integer|min:1',
                'vaqt_limit'    => 'required|integer|min:1|max:180',
                'urinish'       => 'required|integer|min:1|max:10',
                'ball'          => 'required|integer|min:1',
            ]);

            $bank = QuestionBank::findOrFail($request->bank_id);

            Question::where('bank_id', $bank->id)
                ->update(['ball' => $request->ball]);

            $data += [
                'bank_id'          => $request->bank_id,
                'savollar_soni'    => $request->savollar_soni,
                'vaqt_limit'       => $request->vaqt_limit,
                'urinish'          => $request->urinish,
                'boshlanish_vaqti' => $request->boshlanish_vaqti ?: null,
                'tugash_vaqti'     => $request->tugash_vaqti ?: null,
            ];
        }

        if ($request->tur === 'video') {
            $request->validate([
                'video' => 'required|file|mimes:mp4,mov,avi,webm|max:512000',
            ]);

            $file = $request->file('video');
            $path = $file->store('ms_videos', 'public');

            $data += [
                'video_path' => $path,
                'video_size' => round($file->getSize() / 1048576, 2) . ' MB',
                'video_mime' => $file->getMimeType(),
            ];
        }

        if ($request->tur === 'pdf') {
            $request->validate([
                'pdf' => 'required|file|mimes:pdf|max:51200',
            ]);

            $file = $request->file('pdf');
            $path = $file->store('ms_pdfs', 'public');

            $data += [
                'pdf_path'      => $path,
                'pdf_size'      => round($file->getSize() / 1048576, 2) . ' MB',
                'pdf_sahifalar' => $request->pdf_sahifalar ?: null,
            ];
        }

        if ($request->tur === 'topshiriq') {
            $request->validate([
                'pdf' => 'required|file|mimes:pdf|max:51200',
            ]);

            $file = $request->file('pdf');
            $path = $file->store('ms_topshiriqlar', 'public');

            $data += [
                'pdf_path'      => $path,
                'pdf_size'      => round($file->getSize() / 1048576, 2) . ' MB',
                'pdf_sahifalar' => $request->pdf_sahifalar ?: null,
            ];
        }

        MsMaterial::create($data);

        return redirect()->back()->with('success', ucfirst($request->tur) . ' material qo\'shildi!');
    }



    public function materialOchir($id)
    {
        $material = MsMaterial::findOrFail($id);
        $mavzu_id = $material->mavzu_id;

        $this->materialFaylOchir($material);
        $material->delete();

        $mavzu = MsMavzu::findOrFail($mavzu_id);
        return redirect()->route('mini_maktab.mavzu.show', [
            $mavzu->bolim_id,
            $mavzu->subject_id,
            $mavzu_id
        ])->with('success', 'Material o\'chirildi!');
    }



    public function testSozlama(Request $request, $id)
    {
        $material = MsMaterial::where('tur', 'test')->findOrFail($id);

        $request->validate([
            'bank_id'       => 'required|exists:question_banks,id',
            'savollar_soni' => 'required|integer|min:1',
            'vaqt_limit'    => 'required|integer|min:1|max:180',
            'urinish'       => 'required|integer|min:1|max:10',
            'ball'          => 'required|integer|min:1',
        ]);

        Question::where('bank_id', $request->bank_id)
            ->update(['ball' => $request->ball]);

        $material->update([
            'bank_id'          => $request->bank_id,
            'savollar_soni'    => $request->savollar_soni,
            'vaqt_limit'       => $request->vaqt_limit,
            'urinish'          => $request->urinish,
            'boshlanish_vaqti' => $request->boshlanish_vaqti ?: null,
            'tugash_vaqti'     => $request->tugash_vaqti ?: null,
        ]);

        return redirect()->back()->with('success', 'Test sozlamalari yangilandi!');
    }



    public function materialStatusToggle($id)
    {
        $material = MsMaterial::findOrFail($id);
        $material->update(['faol' => ! $material->faol]);

        return response()->json(['status' => (bool) $material->faol]);
    }



    public function statusToggle($id)
    {
        $ariza = mini_semestr::findOrFail($id);
        $ariza->update(['status' => !$ariza->status]);

        return redirect()->back()->with(
            'success',
            $ariza->status ? 'Talaba aktivlashtirildi!' : 'Talaba bloklandi!'
        );
    }

    public function allStatusToggle(Request $request, $bolim_id, $subject_id)
    {
        mini_semestr::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->update(['status' => $request->status]);

        return redirect()->back()->with(
            'success',
            $request->status ? 'Barcha talabalar aktivlashtirildi!' : 'Barcha talabalar bloklandi!'
        );
    }



    public function talabaSessions($bolim_id, $subject_id, $user_id, $material_id)
    {
        $bolim    = Bolim::findOrFail($bolim_id);
        $subject  = Subject::findOrFail($subject_id);
        $user     = User::findOrFail($user_id);
        $material = MsMaterial::with(['bank', 'mavzu'])->findOrFail($material_id);

        $sessions = TestSession::where('user_id', $user_id)
            ->where('ms_material_id', $material_id)
            ->orderBy('created_at')
            ->get()
            ->map(function ($s) {
                $s->togri_soni = QuestionUser::where('session_id', $s->id)->where('status', 1)->count();
                $s->jami_soni  = QuestionUser::where('session_id', $s->id)->count();
                return $s;
            });

        return view('mini_maktab.urinishlar', compact('bolim', 'subject', 'user', 'material', 'sessions'));
    }



    public function harakat($bolim_id, $subject_id, $user_id, $session_id)
    {
        $bolim   = Bolim::findOrFail($bolim_id);
        $subject = Subject::findOrFail($subject_id);
        $user    = User::findOrFail($user_id);

        $session = TestSession::where('id', $session_id)
            ->where('user_id', $user_id)
            ->with('bank')
            ->firstOrFail();

        $material   = MsMaterial::with('mavzu')->find($session->ms_material_id);
        $harakatlar = QuestionUser::where('session_id', $session_id)
            ->with('question')
            ->get();

        return view('mini_maktab.harakat', compact(
            'bolim',
            'subject',
            'user',
            'session',
            'material',
            'harakatlar'
        ));
    }



    public function sessionDelete($id)
    {
        $session = TestSession::findOrFail($id);

        $userId     = $session->user_id;
        $materialId = $session->ms_material_id;
        $material   = MsMaterial::with('mavzu')->find($materialId);

        QuestionUser::where('session_id', $session->id)->delete();
        $session->delete();

        if ($material && $material->mavzu) {
            return redirect()->route('mini_maktab.talaba.sessions', [
                $material->mavzu->bolim_id,
                $material->mavzu->subject_id,
                $userId,
                $materialId,
            ])->with('success', 'Urinish o\'chirildi!');
        }

        return redirect()->route('mini_maktab.index')->with('success', 'Urinish o\'chirildi!');
    }



    public function topshiriqBaholar(Request $request, $material_id)
    {
        $material = MsMaterial::where('tur', 'topshiriq')
            ->with('mavzu')
            ->findOrFail($material_id);

        $request->validate([
            'ballar'   => 'required|array',
            'ballar.*' => 'nullable|numeric|min:0|max:100',
        ]);

        $mavzu     = $material->mavzu;
        $bolimId   = $mavzu->bolim_id;
        $subjectId = $mavzu->subject_id;

        foreach ($request->ballar as $userId => $ball) {
            $topshiriq = MsTopshiriq::firstOrCreate(
                [
                    'ms_material_id' => $material_id,
                    'user_id'        => $userId,
                ],
                ['pdf_path' => null]
            );

            $topshiriq->update([
                'ball' => ($ball === '' || $ball === null) ? null : (float) $ball,
            ]);

            $this->recalcMiniSemestrScores((int) $userId, $bolimId, $subjectId);
        }

        return redirect()->back()->with('success', 'Baholar saqlandi!');
    }



    public function topshiriqYukla(Request $request, $material_id)
    {
        $material = MsMaterial::where('tur', 'topshiriq')
            ->where('faol', 1)
            ->with('mavzu')
            ->findOrFail($material_id);

        $user = auth()->user();

        $ariza = mini_semestr::where('bolim_id', $material->mavzu->bolim_id)
            ->where('subject_id', $material->mavzu->subject_id)
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->first();

        if (! $ariza) {
            return redirect()->back()->with('error', 'Bu fanga arizangiz yo\'q yoki bloklangansiz.');
        }

        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:51200',
        ]);

        $file = $request->file('pdf');

        $fio = preg_replace(
            '/[^a-zA-Z0-9_\-а-яА-ЯёЁўқғҳʼ\' ]/u',
            '',
            $user->getAttribute('To‘liq_ismi') ?? $user->name ?? 'talaba'
        );
        $fio = str_replace(' ', '_', trim($fio));
        $filename = $fio . '_' . time() . '.pdf';

        $path = $file->storeAs('ms_topshiriq_javoblar', $filename, 'public');

        $topshiriq = MsTopshiriq::firstOrNew([
            'ms_material_id' => $material_id,
            'user_id'        => $user->id,
        ]);

        if ($topshiriq->pdf_path && Storage::disk('public')->exists($topshiriq->pdf_path)) {
            Storage::disk('public')->delete($topshiriq->pdf_path);
        }

        $topshiriq->pdf_path = $path;
        $topshiriq->save();

        return redirect()->back()->with('success', 'Topshiriq yuklandi!');
    }



    private function materialFaylOchir(MsMaterial $material): void
    {
        if ($material->video_path && Storage::disk('public')->exists($material->video_path)) {
            Storage::disk('public')->delete($material->video_path);
        }
        if ($material->pdf_path && Storage::disk('public')->exists($material->pdf_path)) {
            Storage::disk('public')->delete($material->pdf_path);
        }
    }

    
    private function recalcMiniSemestrScores(int $userId, int $bolimId, int $subjectId): void
    {
        $ariza = mini_semestr::where('bolim_id', $bolimId)
            ->where('subject_id', $subjectId)
            ->where('user_id', $userId)
            ->first();

        if (! $ariza) {
            return;
        }

        $base = MsTopshiriq::query()
            ->where('user_id', $userId)
            ->whereNotNull('ball')
            ->whereHas('material.mavzu', function ($q) use ($bolimId, $subjectId) {
                $q->where('bolim_id', $bolimId)
                    ->where('subject_id', $subjectId);
            });

        $joriy = (clone $base)
            ->whereHas('material.mavzu', fn($q) => $q->where('tur', 'mavzu'))
            ->sum('ball');

        $oraliq = (clone $base)
            ->whereHas('material.mavzu', fn($q) => $q->where('tur', 'oraliq'))
            ->sum('ball');

        $yakuniy = (clone $base)
            ->whereHas('material.mavzu', fn($q) => $q->where('tur', 'yakuniy'))
            ->sum('ball');

        $ariza->update([
            'joriy_baho'   => $joriy,
            'oraliq_baho'  => $oraliq,
            'joriy_oraliq' => $joriy + $oraliq,
            'yakuniy_baho' => $yakuniy,
            'umumiy'       => $joriy + $oraliq + $yakuniy,
        ]);
    }



    public function guruhTeacherQosh(Request $request, $subjectsToSubjectId)
    {
        if (auth()->user()?->role !== 'admin') abort(403);

        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'max_talaba' => 'nullable|integer|min:1|max:200',
        ]);

        SubjectsToSubjectTeacher::firstOrCreate(
            [
                'subjects_to_subject_id' => $subjectsToSubjectId,
                'teacher_id'             => $request->teacher_id,
            ],
            ['max_talaba' => $request->max_talaba ?: 30]
        );

        return redirect()->back()->with('success', "O'qituvchi kartochkasi qo'shildi!");
    }



    public function guruhTeacherOchir($id)
    {
        if (auth()->user()?->role !== 'admin') abort(403);

        $card = SubjectsToSubjectTeacher::findOrFail($id);

        $subjectIds = Subject::where('subjects_to_subject_id', $card->subjects_to_subject_id)->pluck('id');

        mini_semestr::where('teacher_id', $card->teacher_id)
            ->where(function ($q) use ($subjectIds, $card) {
                if ($subjectIds->isNotEmpty()) {
                    $q->whereIn('subject_id', $subjectIds);
                }
                if ($card->subjects_to_subject_id) {
                    $q->orWhere('subjects_to_subject_id', $card->subjects_to_subject_id);
                }
            })
            ->update(['teacher_id' => null]);

        $card->delete();

        return redirect()->back()->with('success', "O'qituvchi olib tashlandi, talabalari bo'shab qoldi.");
    }



    public function guruhAvtoTaqsimla(Request $request, $subjectsToSubjectId)
    {
        if (auth()->user()?->role !== 'admin') abort(403);

        $teachers = SubjectsToSubjectTeacher::where('subjects_to_subject_id', $subjectsToSubjectId)->get();

        if ($teachers->isEmpty()) {
            return redirect()->back()->with('error', "Avval kamida bitta o'qituvchi kartochkasini qo'shing.");
        }

        $subjectIds = Subject::where('subjects_to_subject_id', $subjectsToSubjectId)->pluck('id');

        if ($subjectIds->isEmpty()) {
            return redirect()->back()->with('error', "Bu guruhga hali hech qanday fan biriktirilmagan.");
        }

        $bolimId = $request->filled('bolim_id') ? (int) $request->bolim_id : null;

        $bandSoni = [];
        foreach ($teachers as $t) {
            $q = mini_semestr::whereIn('subject_id', $subjectIds)
                ->where('teacher_id', $t->teacher_id);
            if ($bolimId) {
                $q->where('bolim_id', $bolimId);
            }
            $bandSoni[$t->teacher_id] = $q->count();
        }

        $boshQuery = mini_semestr::whereIn('subject_id', $subjectIds)
            ->whereNull('teacher_id');
        if ($bolimId) {
            $boshQuery->where('bolim_id', $bolimId);
        }
        $boshTalabalar = $boshQuery->pluck('id')->shuffle();

        if ($boshTalabalar->isEmpty()) {
            return redirect()->back()->with('success', "Bo'sh talaba qolmadi — hammasi allaqachon biriktirilgan.");
        }

        $taqsimlandi = 0;
        foreach ($boshTalabalar as $arizaId) {

            $tanlangan = $teachers->sortByDesc(function ($t) use ($bandSoni) {
                return $t->max_talaba - ($bandSoni[$t->teacher_id] ?? 0);
            })->first();

            $updated = mini_semestr::where('id', $arizaId)->update([
                'teacher_id' => $tanlangan->teacher_id,
            ]);

            if ($updated) {
                $bandSoni[$tanlangan->teacher_id] = ($bandSoni[$tanlangan->teacher_id] ?? 0) + 1;
                $taqsimlandi++;
            }
        }

        return redirect()->back()->with('success', "Bo'sh talabalar taqsimlandi ({$taqsimlandi} ta)!");
    }



    public function talabaTeacherOzgartir(Request $request, $arizaId)
    {
        if (auth()->user()?->role !== 'admin') abort(403);

        $request->validate(['teacher_id' => 'nullable|exists:users,id']);

        $ariza = mini_semestr::findOrFail($arizaId);
        $ariza->update(['teacher_id' => $request->teacher_id]);

        $subject = Subject::find($ariza->subject_id);
        $guruhId = $subject?->subjects_to_subject_id;

        $subjectIds = $guruhId
            ? Subject::where('subjects_to_subject_id', $guruhId)->pluck('id')
            : collect([$ariza->subject_id]);

        $band = $request->teacher_id
            ? mini_semestr::whereIn('subject_id', $subjectIds)
                ->where('teacher_id', $request->teacher_id)
                ->count()
            : 0;

        $max = $request->teacher_id && $guruhId
            ? (SubjectsToSubjectTeacher::where('subjects_to_subject_id', $guruhId)
                ->where('teacher_id', $request->teacher_id)
                ->value('max_talaba') ?? 30)
            : 30;

        return response()->json([
            'success'     => true,
            'teacher_id'  => $request->teacher_id,
            'band'        => $band,
            'max'         => $max,
            'oshib_ketdi' => $request->teacher_id ? $band > $max : false,
        ]);
    }
}