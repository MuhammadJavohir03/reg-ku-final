@extends('layouts.chat')
@section('title', $otherUser->{'To‘liq_ismi'})

@section('sidebar')
    @include('student.chat._sidebar')
@endsection

@section('chat')
    <div class="chat-panel__header">
        <a href="{{ route('chat') }}" class="chat-panel__back">&#8592;</a>
        <div class="chat-avatar">{{ mb_substr($otherUser->{'To‘liq_ismi'}, 0, 1) }}</div>
        <strong>{{ $otherUser->{'To‘liq_ismi'} }}</strong>
    </div>

    @if ($needsMyApproval)
        <div class="approval-banner" id="approval-banner">
            <span>{{ $otherUser->{'To‘liq_ismi'} }} sizga yozishmoqchi. Ruxsat berasizmi?</span>
            <button type="button" class="btn-accept" id="accept-btn"
                    data-accept-url="{{ route('chat.user.accept', $otherUser) }}">Qabul qilish</button>
        </div>
    @endif

    <div class="chat-messages"
         id="chat-messages"
         data-poll-url="{{ route('chat.user.poll', $otherUser) }}"
         data-last-id="{{ optional($messages->last())->id ?? 0 }}">
        @foreach ($messages as $message)
            @include('student.chat._bubble', ['message' => $message])
        @endforeach
    </div>

    <form class="chat-panel__form" id="send-form" data-send-url="{{ route('chat.user.send', $otherUser) }}">
        @csrf
        <input type="text" name="body" class="chat-panel__input" placeholder="Xabar yozing..." autocomplete="off" required>
        <button type="submit" class="chat-panel__send">Yuborish</button>
    </form>
@endsection

@section('page-script')
    <script>window.ChatConfig = { mode: 'user' };</script>
@endsection
