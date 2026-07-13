<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends Controller
{
    /** Admin biriktirilgan bo'limlar ro'yxati */
    public function index()
    {
        $admin = Auth::user();
        $sections = $admin->sections()->orderBy('name')->get();

        return view('admin.chat.index', compact('sections'));
    }

    /** Bitta bo'lim ichidagi barcha talaba-suhbatlar ro'yxati */
    public function section(Section $section)
    {
        $this->authorizeSection($section);
        $conversations = $this->buildConversations($section);

        return view('admin.chat.section', compact('section', 'conversations'));
    }

    /** Bo'lim ichida qaysi talabalar yozishgani va oxirgi xabar / unread sonini yig'ib beradi */
    private function buildConversations(Section $section)
    {
        $studentIds = Message::where('section_id', $section->id)
            ->get(['sender_id', 'receiver_id'])
            ->flatMap(fn($m) => [$m->sender_id, $m->receiver_id])
            ->filter()
            ->unique();

        return User::whereIn('id', $studentIds)
            ->where('role', 'talaba')   // <-- faqat mana shu qatorni qo'shing
            ->get()
            ->map(function (User $student) use ($section) {

                $last = Message::forSectionConversation($section->id, $student->id)
                    ->latest()
                    ->first();

                $unread = Message::forSectionConversation($section->id, $student->id)
                    ->where('sender_id', $student->id)
                    ->where('status', Message::STATUS_UNREAD)
                    ->count();

                return [
                    'student' => $student,
                    'last_message' => $last,
                    'unread' => $unread,
                ];
            })
            ->sortByDesc(fn($c) => optional($c['last_message'])->created_at)
            ->values();
    }

    /** Qidiruv - shu bo'limga hali yozmagan talabani ham topib, suhbat ochish uchun */
    public function searchStudents(Request $request, Section $section)
    {
        $this->authorizeSection($section);
        $q = trim($request->get('q', ''));

        $students = User::where('role', 'talaba')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('To‘liq_ismi', 'like', "%{$q}%")
                        ->orWhere('Talaba_ID', 'like', "%{$q}%");
                });
            })
            ->limit(20)
            ->get(['id', 'To‘liq_ismi', 'Talaba_ID', 'Guruh']);

        return response()->json($students);
    }

    /** Bitta talaba bilan bo'lim doirasidagi suhbat oynasi (o'ngda talaba ma'lumoti bilan) */
    public function conversation(Section $section, User $student)
    {
        $this->authorizeSection($section);

        $messages = Message::where('section_id', $section->id)
            ->where(function ($q) use ($student) {
                $q->where('sender_id', $student->id)
                    ->orWhere('receiver_id', $student->id);
            })
            ->with('sender')
            ->oldest()
            ->get();

        Message::where('section_id', $section->id)
            ->where('sender_id', $student->id)
            ->whereNull('receiver_id')
            ->where('status', Message::STATUS_UNREAD)
            ->update([
                'status' => Message::STATUS_READ,
                'read_at' => now(),
            ]);

        $conversations = $this->buildConversations($section);

        return view('admin.chat.conversation', compact('section', 'student', 'messages', 'conversations'));
    }

    public function send(Request $request, Section $section, User $student)
    {
        $this->authorizeSection($section);
        $request->validate(['body' => 'required|string|max:5000']);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $student->id,
            'section_id' => $section->id,
            'body' => $request->body,
            'status' => Message::STATUS_UNREAD,
            'rozilik' => null,
        ]);

        return response()->json(['message' => $message->load('sender')]);
    }

    public function poll(Request $request, Section $section, User $student)
    {
        $this->authorizeSection($section);
        $afterId = (int) $request->get('after', 0);

        $messages = Message::where('section_id', $section->id)
            ->where(function ($q) use ($student) {
                $q->where('sender_id', $student->id)
                    ->orWhere('receiver_id', $student->id);
            })
            ->where('id', '>', $afterId)
            ->with('sender')
            ->oldest()
            ->get();

        Message::where('section_id', $section->id)
            ->where('sender_id', $student->id)
            ->whereNull('receiver_id')
            ->where('status', Message::STATUS_UNREAD)
            ->update([
                'status' => Message::STATUS_READ,
                'read_at' => now(),
            ]);

        return response()->json(['messages' => $messages]);
    }

    /** Bo'lim doirasidagi barcha suhbatlar ro'yxatini yangilash uchun umumiy polling */
    public function pollOverview(Section $section)
    {
        $this->authorizeSection($section);

        $unreadTotal = Message::where('section_id', $section->id)
            ->whereNull('receiver_id')
            ->where('status', Message::STATUS_UNREAD)
            ->count();

        $lastMessageId = Message::where('section_id', $section->id)->max('id');

        return response()->json(['unread_total' => $unreadTotal, 'last_message_id' => $lastMessageId]);
    }

    private function authorizeSection(Section $section): void
    {
        $admin = Auth::user();
        if (!$admin->sections()->where('sections.id', $section->id)->exists()) {
            abort(Response::HTTP_FORBIDDEN, 'Siz bu bo\'limga biriktirilmagansiz.');
        }
    }
}
