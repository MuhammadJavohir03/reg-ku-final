<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Bosh sahifa: bo'limlar ro'yxati + oldin yozishgan talabalar ro'yxati.
     * (Telefonda faqat shu ro'yxat ko'rinadi, PC'da chapda shu, o'ngda bo'sh holat)
     */
    public function index()
    {
        ['sections' => $sections, 'directChats' => $directChats] = $this->buildSidebar();

        return view('student.chat.index', compact('sections', 'directChats'));
    }

    /**
     * Sidebar (bo'limlar + shaxsiy suhbatlar) ma'lumotini yig'ib beradi.
     * index(), section() va userChat() metodlari birgalikda ishlatadi,
     * shunda PC'da har doim chapda ro'yxat, o'ngda chat ko'rinib turadi.
     */
    private function buildSidebar(): array
    {
        $me = Auth::user();

        $sections = Section::orderBy('name')->get()->map(function (Section $section) use ($me) {
            $last = Message::forSectionConversation($section->id, $me->id)->latest()->first();
            $unread = Message::forSectionConversation($section->id, $me->id)
                ->where('receiver_id', $me->id)
                ->where('status', Message::STATUS_UNREAD)
                ->count();

            return [
                'section' => $section,
                'last_message' => $last,
                'unread' => $unread,
            ];
        })->sortByDesc(fn ($s) => optional($s['last_message'])->created_at)->values();

        $partnerIds = Message::whereNull('section_id')
            ->where(function ($q) use ($me) {
                $q->where('sender_id', $me->id)->orWhere('receiver_id', $me->id);
            })
            ->get()
            ->map(fn ($m) => $m->sender_id === $me->id ? $m->receiver_id : $m->sender_id)
            ->unique();

        $directChats = User::whereIn('id', $partnerIds)->get()->map(function (User $user) use ($me) {
            $last = Message::betweenUsers($me->id, $user->id)->latest()->first();
            $unread = Message::betweenUsers($me->id, $user->id)
                ->where('receiver_id', $me->id)
                ->where('status', Message::STATUS_UNREAD)
                ->count();
            $pendingSender = Message::pendingRequestSender($me->id, $user->id);

            return [
                'user' => $user,
                'last_message' => $last,
                'unread' => $unread,
                'pending_for_me' => $pendingSender !== null && $pendingSender !== $me->id,
            ];
        })->sortByDesc(fn ($c) => optional($c['last_message'])->created_at)->values();

        return compact('sections', 'directChats');
    }

    /** Talaba qidirish (yozishish uchun) */
    public function searchUsers(Request $request)
    {
        $q = trim($request->get('q', ''));
        $me = Auth::id();

        $users = User::where('id', '!=', $me)
            ->where('role', 'talaba')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('To‘liq_ismi', 'like', "%{$q}%")
                        ->orWhere('Talaba_ID', 'like', "%{$q}%");
                });
            })
            ->limit(20)
            ->get(['id', 'To‘liq_ismi', 'Talaba_ID', 'Guruh']);

        return response()->json($users);
    }

    /** Bo'lim bilan chat oynasi */
    public function section(Section $section)
    {
        $me = Auth::user();

        $messages = Message::forSectionConversation($section->id, $me->id)
            ->with(['sender'])
            ->oldest()
            ->get();

        Message::forSectionConversation($section->id, $me->id)
            ->where('receiver_id', $me->id)
            ->where('status', Message::STATUS_UNREAD)
            ->update(['status' => Message::STATUS_READ, 'read_at' => now()]);

        $sidebar = $this->buildSidebar();

        return view('student.chat.section', array_merge(compact('section', 'messages'), $sidebar));
    }

    public function sendToSection(Request $request, Section $section)
    {
        $request->validate(['body' => 'required|string|max:5000']);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'section_id' => $section->id,
            'receiver_id' => null,
            'body' => $request->body,
            'status' => Message::STATUS_UNREAD,
            'rozilik' => null,
        ]);

        return response()->json(['message' => $message->load('sender')]);
    }

    /** Bo'lim chatidagi yangi xabarlarni polling qilish */
    public function pollSection(Request $request, Section $section)
    {
        $afterId = (int) $request->get('after', 0);
        $me = Auth::user();

        $messages = Message::forSectionConversation($section->id, $me->id)
            ->where('id', '>', $afterId)
            ->with('sender')
            ->oldest()
            ->get();

        // Menga kelgan yangi xabarlarni o'qilgan deb belgilaymiz
        Message::forSectionConversation($section->id, $me->id)
            ->where('receiver_id', $me->id)
            ->where('status', Message::STATUS_UNREAD)
            ->update(['status' => Message::STATUS_READ, 'read_at' => now()]);

        return response()->json(['messages' => $messages]);
    }

    /** Boshqa talaba bilan chat oynasi */
    public function userChat(User $user)
    {
        abort_if($user->id === Auth::id() || $user->role !== 'talaba', 404);

        $me = Auth::user();

        $messages = Message::betweenUsers($me->id, $user->id)->with('sender')->oldest()->get();
        $pendingSender = Message::pendingRequestSender($me->id, $user->id);

        // Menga tegishli xabarni "o'qilgan" deb belgilaymiz
        Message::betweenUsers($me->id, $user->id)
            ->where('receiver_id', $me->id)
            ->where('status', Message::STATUS_UNREAD)
            ->update(['status' => Message::STATUS_READ, 'read_at' => now()]);

        $sidebar = $this->buildSidebar();

        return view('student.chat.user', array_merge([
            'otherUser' => $user,
            'messages' => $messages,
            // Agar so'rov bor va yuboruvchi men bo'lmasam - menga "qabul qilish" tugmasi chiqadi
            'needsMyApproval' => $pendingSender !== null && $pendingSender !== $me->id,
        ], $sidebar));
    }

    public function sendToUser(Request $request, User $user)
    {
        abort_if($user->id === Auth::id() || $user->role !== 'talaba', 404);

        $request->validate(['body' => 'required|string|max:5000']);
        $me = Auth::user();

        $pendingSender = Message::pendingRequestSender($me->id, $user->id);

        // Suhbat hali boshlanmagan bo'lsa -> bu birinchi so'rov xabari
        $isFirstMessage = !Message::betweenUsers($me->id, $user->id)->exists();

        if (!$isFirstMessage && $pendingSender !== null && $pendingSender !== $me->id) {
            // Boshqa talaba so'rov yuborgan, u hali qabul qilmagan - men yoza olmayman
            return response()->json([
                'error' => 'Suhbatni boshlash uchun avval qarshi tomon rozilik berishi kerak.',
            ], 403);
        }

        $message = Message::create([
            'sender_id' => $me->id,
            'receiver_id' => $user->id,
            'section_id' => null,
            'body' => $request->body,
            'status' => Message::STATUS_UNREAD,
            'rozilik' => $isFirstMessage ? Message::ROZILIK_PENDING : Message::ROZILIK_ACCEPTED,
        ]);

        return response()->json(['message' => $message->load('sender')]);
    }

    /** Kelgan so'rovni qabul qilish */
    public function acceptUser(User $user)
    {
        $me = Auth::user();
        $pendingSender = Message::pendingRequestSender($user->id, $me->id);

        if ($pendingSender === $user->id) {
            Message::acceptRequest($user->id, $me->id);
        }

        return response()->json(['ok' => true]);
    }

    public function pollUser(Request $request, User $user)
    {
        $afterId = (int) $request->get('after', 0);
        $me = Auth::user();

        $messages = Message::betweenUsers($me->id, $user->id)
            ->where('id', '>', $afterId)
            ->with('sender')
            ->oldest()
            ->get();

        Message::betweenUsers($me->id, $user->id)
            ->where('receiver_id', $me->id)
            ->where('status', Message::STATUS_UNREAD)
            ->update(['status' => Message::STATUS_READ, 'read_at' => now()]);

        $pendingSender = Message::pendingRequestSender($me->id, $user->id);

        return response()->json([
            'messages' => $messages,
            'needs_my_approval' => $pendingSender !== null && $pendingSender !== $me->id,
        ]);
    }

    /**
     * Sidebar uchun umumiy polling - yangi xabar kelgan-kelmaganini
     * (ovoz chiqarish va badge yangilash uchun) tekshiradi.
     */
    public function pollOverview()
    {
        $me = Auth::user();

        $unreadTotal = Message::where('receiver_id', $me->id)
            ->where('status', Message::STATUS_UNREAD)
            ->count();

        $lastMessageId = Message::where('receiver_id', $me->id)->max('id');

        return response()->json([
            'unread_total' => $unreadTotal,
            'last_message_id' => $lastMessageId,
        ]);
    }
}