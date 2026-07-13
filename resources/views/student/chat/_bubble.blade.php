@php $mine = $message->sender_id === auth()->id(); @endphp
<div class="bubble {{ $mine ? 'mine' : 'theirs' }}" data-message-id="{{ $message->id }}">
    <div>{{ $message->body }}</div>
    <div class="bubble__meta">
        {{ $message->created_at->format('H:i') }}
        @if ($mine)
            {{ $message->status === 1 ? '✓✓' : '✓' }}
        @endif
    </div>
</div>
