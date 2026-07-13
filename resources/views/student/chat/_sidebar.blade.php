@php
    $initials = function ($name) {
        $parts = preg_split('/\s+/', trim($name ?? '?'));
        return mb_strtoupper(mb_substr($parts[0] ?? '?', 0, 1) . mb_substr($parts[1] ?? '', 0, 1));
    };
@endphp

<script>
    window.ChatSearchUrl = @json(route('chat.search'));
    window.ChatUserUrlTemplate = @json(route('chat.user', ['user' => '__ID__']));
    window.ChatOverviewUrl = @json(route('chat.poll.overview'));
</script>
<div class="chat-sidebar__header">
    <input type="text" class="chat-sidebar__search" id="student-search"
           placeholder="Talaba qidirish (yozishish uchun)...">
    <div id="search-results" style="margin-top:8px;"></div>
</div>

<div class="chat-sidebar__list" id="sidebar-list">
    <div style="padding:10px 16px; font-size:12px; font-weight:700; color:#9ca3af;">BO'LIMLAR</div>
    @foreach ($sections as $row)
        <a href="{{ route('chat.section', $row['section']) }}"
           class="chat-item {{ request()->route('section')?->id === $row['section']->id ? 'active' : '' }}">
            <div class="chat-avatar" style="background:#0ea5e9;">{{ mb_substr($row['section']->name, 0, 1) }}</div>
            <div class="chat-item__body">
                <div class="chat-item__top">
                    <span class="chat-item__name">{{ $row['section']->name }}</span>
                    @if ($row['last_message'])
                        <span class="chat-item__time">{{ $row['last_message']->created_at->format('H:i') }}</span>
                    @endif
                </div>
                <div class="chat-item__top">
                    <span class="chat-item__preview">{{ Str::limit(optional($row['last_message'])->body, 40, '') ?: 'Xabar yo\'q' }}</span>
                    @if ($row['unread'] > 0)
                        <span class="chat-badge">{{ $row['unread'] }}</span>
                    @endif
                </div>
            </div>
        </a>
    @endforeach

    <div style="padding:10px 16px; font-size:12px; font-weight:700; color:#9ca3af;">SHAXSIY SUHBATLAR</div>
    @forelse ($directChats as $row)
        <a href="{{ route('chat.user', $row['user']) }}"
           class="chat-item {{ (request()->route('user')?->id ?? null) === $row['user']->id ? 'active' : '' }}">
            <div class="chat-avatar">{{ $initials($row['user']->{'To‘liq_ismi'}) }}</div>
            <div class="chat-item__body">
                <div class="chat-item__top">
                    <span class="chat-item__name">{{ $row['user']->{'To‘liq_ismi'} }}</span>
                    @if ($row['last_message'])
                        <span class="chat-item__time">{{ $row['last_message']->created_at->format('H:i') }}</span>
                    @endif
                </div>
                <div class="chat-item__top">
                    <span class="chat-item__preview">
                        {{ $row['pending_for_me'] ? 'Sizga suhbat so\'rovi yuborildi' : (Str::limit(optional($row['last_message'])->body, 40, '') ?: 'Xabar yo\'q') }}
                    </span>
                    @if ($row['unread'] > 0)
                        <span class="chat-badge">{{ $row['unread'] }}</span>
                    @endif
                </div>
            </div>
        </a>
    @empty
        <div style="padding:16px; color:#9ca3af; font-size:13px;">Hali hech kim bilan yozishmagansiz.</div>
    @endforelse
</div>
