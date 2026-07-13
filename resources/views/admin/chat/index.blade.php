@extends('layouts.chat')
@section('title', 'Admin Chat')

@section('sidebar')
    <div class="chat-sidebar__header"><strong>Mening bo'limlarim</strong></div>
    <div class="chat-sidebar__list">
        @forelse ($sections as $section)
            <a href="{{ route('admin_chat.section', $section) }}" class="chat-item">
                <div class="chat-avatar" style="background:#0ea5e9;">{{ mb_substr($section->name, 0, 1) }}</div>
                <div class="chat-item__body">
                    <span class="chat-item__name">{{ $section->name }}</span>
                </div>
            </a>
        @empty
            <div style="padding:16px; color:#9ca3af; font-size:13px;">Sizga hali bo'lim biriktirilmagan.</div>
        @endforelse
    </div>
@endsection
