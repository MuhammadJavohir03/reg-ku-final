@php
    $admInitials = function ($name) {
        $parts = preg_split('/\s+/', trim($name ?? '?'));
        return mb_strtoupper(mb_substr($parts[0] ?? '?', 0, 1) . mb_substr($parts[1] ?? '', 0, 1));
    };
@endphp

<script>
    window.ChatSearchUrl = @json(route('admin_chat.search', $section));
    window.ChatUserUrlTemplate = @json(route('admin_chat.conversation', ['section' => $section->id, 'student' => '__ID__']));
    window.ChatOverviewUrl = @json(route('admin_chat.poll.overview', $section));
</script>

<div class="chat-sidebar__header">
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
        <a href="{{ route('admin_chat') }}" style="text-decoration:none; color:#374151;">&#8592;</a>
        <strong>{{ $section->name }}</strong>
    </div>
    <input type="text" class="chat-sidebar__search" id="student-search"
           placeholder="Talaba qidirish...">
    <div id="search-results" style="margin-top:8px;"></div>
</div>

<div class="chat-sidebar__list" id="sidebar-list">
    @forelse ($conversations as $row)
        <a href="{{ route('admin_chat.conversation', [$section, $row['student']]) }}"
           class="chat-item {{ (request()->route('student')?->id ?? null) === $row['student']->id ? 'active' : '' }}">
            <div class="chat-avatar">{{ $admInitials($row['student']->{'To‘liq_ismi'}) }}</div>
            <div class="chat-item__body">
                <div class="chat-item__top">
                    <span class="chat-item__name">{{ $row['student']->{'To‘liq_ismi'} }}</span>
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
    @empty
        <div style="padding:16px; color:#9ca3af; font-size:13px;">Hali hech kim yozmagan.</div>
    @endforelse
</div>
