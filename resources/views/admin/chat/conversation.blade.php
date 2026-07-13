@extends('layouts.chat')
@section('title', $student->{'To‘liq_ismi'})

@section('sidebar')
    @include('admin.chat._sidebar')
@endsection

@section('chat')
    <div class="chat-panel__header">
        <a href="{{ route('admin_chat.section', $section) }}" class="chat-panel__back">&#8592;</a>
        <div class="chat-avatar">{{ mb_substr($student->{'To‘liq_ismi'}, 0, 1) }}</div>
        <strong>{{ $student->{'To‘liq_ismi'} }}</strong>
    </div>

    <div class="chat-messages"
         id="chat-messages"
         data-poll-url="{{ route('admin_chat.poll', [$section, $student]) }}"
         data-last-id="{{ optional($messages->last())->id ?? 0 }}">
        @foreach ($messages as $message)
            @include('student.chat._bubble', ['message' => $message])
        @endforeach
    </div>

    <form class="chat-panel__form" id="send-form" data-send-url="{{ route('admin_chat.send', [$section, $student]) }}">
        @csrf
        <input type="text" name="body" class="chat-panel__input" placeholder="Javob yozing..." autocomplete="off" required>
        <button type="submit" class="chat-panel__send">Yuborish</button>
    </form>
@endsection

@section('info')
    <h4>Talaba ma'lumoti</h4>
    <div class="info-row"><b>F.I.SH</b>{{ $student->{'To‘liq_ismi'} }}</div>
    <div class="info-row"><b>Talaba ID</b>{{ $student->Talaba_ID }}</div>
    <div class="info-row"><b>Fakultet</b>{{ $student->Fakultet }}</div>
    <div class="info-row"><b>Guruh</b>{{ $student->Guruh }}</div>
    <div class="info-row"><b>Kurs</b>{{ $student->Kurs }}</div>
    <div class="info-row"><b>Ta'lim shakli</b>{{ $student->{'Ta’lim_shakli'} }}</div>
    <div class="info-row"><b>To'lov shakli</b>{{ $student->{'To‘lov_shakli'} }}</div>
    <div class="info-row"><b>Email</b>{{ $student->email }}</div>
@endsection

@section('page-script')
    <script>window.ChatConfig = { mode: 'admin' };</script>
@endsection
