<?php

namespace App\Http\Controllers;

use App\Models\Bolim;
use App\Models\Subject;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\free_semestr;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TestSession;
use App\Models\QuestionUser;

class BepulMaktabController extends Controller
{
    // Bolimlar ro'yxati
    public function index()
    {
        $bolimlar = Bolim::paginate(50);
        return view('bepul_maktab.index', compact('bolimlar'));
    }

    // Bolim ichidagi fanlar
    public function fanlar($bolim_id)
    {
        $bolim   = Bolim::findOrFail($bolim_id);
        $fanlar  = free_semestr::where('bolim_id', $bolim_id)
            ->with('subject')
            ->select('subject_id')
            ->distinct()
            ->get();

        return view('bepul_maktab.fanlar', compact('bolim', 'fanlar'));
    }

    // Fan sozlamalari
    public function sozlamalar($bolim_id, $subject_id)
    {
        $bolim   = Bolim::findOrFail($bolim_id);
        $subject = Subject::findOrFail($subject_id);

        $bank = QuestionBank::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->first();
        $banklar = QuestionBank::withCount('questions')
            ->whereHas('bolim', function ($query) {
                $query->where('status', 1);
            })
            ->get();

        $talabalar = free_semestr::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->with(['user'])
            ->paginate(20);

        return view('bepul_maktab.sozlamalar', compact('bolim', 'subject', 'bank', 'banklar', 'talabalar'));
    }
    // Sozlamalarni saqlash
    public function saqlash(Request $request, $bolim_id, $subject_id)
    {
        $request->validate([
            'bank_id'    => 'required|exists:question_banks,id',
            'vaqt_limit' => 'required|integer|min:1|max:180',
            'urinish'    => 'required|integer|min:1|max:10',
            'savollar_soni' => 'required|integer|min:1',
            'ball'       => 'required|integer|min:1',
        ]);

        $bank = QuestionBank::findOrFail($request->bank_id);

        // Bank sozlamalarini yangilash
        $bank->update([
            'subject_id'      => $subject_id,
            'savollar_soni'      => $request->savollar_soni,
            'vaqt_limit'      => $request->vaqt_limit,
            'urinish'         => $request->urinish,
            'boshlanish_vaqti' => $request->boshlanish_vaqti ?: null,
            'tugash_vaqti'    => $request->tugash_vaqti ?: null,
        ]);

        // Shu bankdagi barcha savollar balini yangilash
        Question::where('bank_id', $bank->id)
            ->update(['ball' => $request->ball]);

        return redirect()->back()->with('success', 'Sozlamalar saqlandi!');
    }

    public function statusToggle($id)
    {
        $ariza = free_semestr::findOrFail($id);
        $ariza->update(['status' => !$ariza->status]);

        return response()->json([
            'success' => true,
            'status'  => $ariza->status,
        ]);
    }

    public function allStatusToggle(Request $request, $bolim_id, $subject_id)
    {
        $status = $request->status; // 1 yoki 0

        free_semestr::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->update(['status' => $status]);

        return redirect()->back()->with(
            'success',
            $status ? 'Barcha talabalar aktivlashtirildi!' : 'Barcha talabalar bloklandi!'
        );
    }

    public function harakat($bolim_id, $subject_id, $user_id, $session_id)
    {
        $session = TestSession::where('id', $session_id)
            ->where('user_id', $user_id)
            ->with('bank')
            ->firstOrFail();

        $user = User::findOrFail($user_id);

        $harakatlar = QuestionUser::where('session_id', $session_id)
            ->whereNotNull('tanlov') // Faqat javob tanlanganlari
            ->with('question')
            ->get();

        return view('bepul_maktab.harakat', compact(
            'session',
            'user',
            'harakatlar'
        ));
    }

    public function sessionDelete($id)
    {
        $session = TestSession::findOrFail($id);

        // 1. question_user larni o‘chiramiz
        QuestionUser::where('session_id', $session->id)->delete();

        // 2. sessionni o‘chiramiz
        $session->delete();

        $bank = $session->bank;

        return redirect()->route('bepul_maktab.sozlamalar', [
            $bank->bolim_id,
            $bank->subject_id
        ]);
    }

    // BepulMaktabController.php ichiga qo'shiladi

    // Talabaning barcha urinishlari (sessiyalari)
    public function talabaSessions($bolim_id, $subject_id, $user_id)
    {
        $bolim   = Bolim::findOrFail($bolim_id);
        $subject = Subject::findOrFail($subject_id);
        $user    = User::findOrFail($user_id);

        $bank = QuestionBank::where('bolim_id', $bolim_id)
            ->where('subject_id', $subject_id)
            ->firstOrFail();

        $sessions = TestSession::where('user_id', $user_id)
            ->where('bank_id', $bank->id)
            ->withCount(['questionUsers as togri_soni' => function ($q) {
                $q->where('status', 1);
            }])
            ->withCount('questionUsers as jami_soni')
            ->orderBy('id')
            ->get();

        // Faqat bitta urinish bo'lsa — to'g'ridan-to'g'ri harakat sahifasiga otkazamiz
        if ($sessions->count() === 1) {
            return redirect()->route('bepul_maktab.harakat', [
                $bolim_id,
                $subject_id,
                $user_id,
                $sessions->first()->id
            ]);
        }

        // Hech qanday urinish bo'lmasa
        if ($sessions->isEmpty()) {
            return redirect()->back()->with('error', 'Bu talaba hali test yechmagan!');
        }

        return view('bepul_maktab.urinishlar', compact(
            'bolim',
            'subject',
            'user',
            'sessions'
        ));
    }
}
