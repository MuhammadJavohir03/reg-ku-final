@extends('layouts.chat')
@section('title', $section->name)

@section('sidebar')
    @include('student.chat._sidebar')
@endsection

@section('chat')
    <div class="chat-panel__header">
        <a href="{{ route('chat') }}" class="chat-panel__back">&#8592;</a>
        <div class="chat-avatar" style="background:#0ea5e9;">{{ mb_substr($section->name, 0, 1) }}</div>
        <strong>{{ $section->name }}</strong>
    </div>

    <div class="chat-messages"
         id="chat-messages"
         data-poll-url="{{ route('chat.section.poll', $section) }}"
         data-last-id="{{ optional($messages->last())->id ?? 0 }}">
        @foreach ($messages as $message)
            @include('student.chat._bubble', ['message' => $message])
        @endforeach
    </div>

    <form class="chat-panel__form" id="send-form" data-send-url="{{ route('chat.section.send', $section) }}">
        @csrf
        <input type="text" name="body" class="chat-panel__input" placeholder="Xabar yozing..." autocomplete="off" required>
        <button type="submit" class="chat-panel__send">Yuborish</button>
    </form>
@endsection

@section('page-script')
    <script>window.ChatConfig = { mode: 'section' };</script>
@endsection
