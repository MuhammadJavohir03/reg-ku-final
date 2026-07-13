<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chat')</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f4f4f5;
        }

        .chat-app {
            display: flex;
            height: 100vh;
            width: 100%;
            overflow: hidden;
        }

        /* -------- SIDEBAR (chap qism) -------- */
        .chat-sidebar {
            width: 380px;
            min-width: 380px;
            background: #fff;
            border-right: 1px solid #e4e4e7;
            display: flex;
            flex-direction: column;
        }
        .chat-sidebar__header {
            padding: 14px 16px;
            border-bottom: 1px solid #e4e4e7;
        }
        .chat-sidebar__search {
            width: 100%;
            padding: 9px 12px;
            border-radius: 20px;
            border: 1px solid #e4e4e7;
            background: #f4f4f5;
            outline: none;
            font-size: 14px;
        }
        .chat-sidebar__list { flex: 1; overflow-y: auto; }

        .chat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            text-decoration: none;
            color: inherit;
            border-bottom: 1px solid #f4f4f5;
            cursor: pointer;
        }
        .chat-item:hover { background: #f4f4f5; }
        .chat-item.active { background: #eef2ff; }

        .chat-avatar {
            width: 46px; height: 46px; border-radius: 50%;
            background: #6366f1; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 16px; flex-shrink: 0;
        }
        .chat-item__body { flex: 1; min-width: 0; }
        .chat-item__top { display: flex; justify-content: space-between; align-items: center; }
        .chat-item__name { font-weight: 600; font-size: 14.5px; }
        .chat-item__time { font-size: 11.5px; color: #9ca3af; }
        .chat-item__preview {
            font-size: 13px; color: #6b7280; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis; max-width: 260px;
        }
        .chat-badge {
            background: #ef4444; color: #fff; border-radius: 999px;
            min-width: 20px; height: 20px; font-size: 11px; font-weight: 700;
            display: flex; align-items: center; justify-content: center; padding: 0 5px;
        }

        /* -------- CHAT PANEL (o'ng qism) -------- */
        .chat-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #eef1f5;
            min-width: 0;
        }
        .chat-panel__header {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 18px; background: #fff; border-bottom: 1px solid #e4e4e7;
        }
        .chat-panel__back { display: none; text-decoration: none; color: #374151; font-size: 20px; }

        .chat-messages {
            flex: 1; overflow-y: auto; padding: 18px;
            display: flex; flex-direction: column; gap: 6px;
        }
        .bubble {
            max-width: 62%; padding: 8px 12px; border-radius: 14px; font-size: 14.5px;
            line-height: 1.4; position: relative; word-wrap: break-word;
        }
        .bubble.mine { align-self: flex-end; background: #6366f1; color: #fff; border-bottom-right-radius: 4px; }
        .bubble.theirs { align-self: flex-start; background: #fff; color: #111827; border-bottom-left-radius: 4px; }
        .bubble__meta { font-size: 10.5px; opacity: .75; margin-top: 3px; text-align: right; }

        .chat-panel__form {
            display: flex; gap: 10px; padding: 12px 16px; background: #fff; border-top: 1px solid #e4e4e7;
        }
        .chat-panel__input {
            flex: 1; padding: 10px 14px; border-radius: 20px; border: 1px solid #e4e4e7; outline: none; font-size: 14.5px;
        }
        .chat-panel__send {
            background: #6366f1; color: #fff; border: none; border-radius: 20px;
            padding: 0 20px; font-weight: 600; cursor: pointer;
        }
        .chat-empty {
            flex: 1; display: flex; align-items: center; justify-content: center;
            color: #9ca3af; font-size: 15px;
        }

        .approval-banner {
            background: #fff7ed; border: 1px solid #fdba74; color: #9a3412;
            padding: 10px 16px; display: flex; justify-content: space-between; align-items: center; gap: 10px;
        }
        .approval-banner button {
            border: none; border-radius: 8px; padding: 6px 14px; font-weight: 600; cursor: pointer;
        }
        .btn-accept { background: #16a34a; color: #fff; }

        /* -------- INFO PANEL (admin uchun, o'ng tomonda talaba ma'lumoti) -------- */
        .chat-info-panel {
            width: 300px; min-width: 300px; background: #fff; border-left: 1px solid #e4e4e7;
            padding: 20px; overflow-y: auto;
        }
        .chat-info-panel h4 { margin: 0 0 14px; font-size: 15px; }
        .info-row { font-size: 13.5px; margin-bottom: 10px; color: #374151; }
        .info-row b { display: block; color: #9ca3af; font-size: 11px; text-transform: uppercase; }

        /* -------- MOBIL -------- */
        @media (max-width: 820px) {
            .chat-sidebar { width: 100%; min-width: 0; }
            .chat-panel { display: none; }
            .chat-info-panel { display: none; }
            .chat-panel__back { display: inline-block; }

            body.chat-open .chat-sidebar { display: none; }
            body.chat-open .chat-panel { display: flex; width: 100%; }
        }
    </style>
</head>
<body class="{{ request()->routeIs('*.section') || request()->routeIs('*.user') || request()->routeIs('*.conversation') ? 'chat-open' : '' }}">
    <div class="chat-app">
        <aside class="chat-sidebar">
            @yield('sidebar')
        </aside>
        <section class="chat-panel">
            @hasSection('chat')
                @yield('chat')
            @else
                <div class="chat-empty">Suhbatni tanlang</div>
            @endif
        </section>
        @hasSection('info')
            <aside class="chat-info-panel">
                @yield('info')
            </aside>
        @endif
    </div>

    <audio id="notify-sound" preload="auto"></audio>
    <script>window.CurrentUserId = {{ auth()->id() ?? 'null' }};</script>
    @yield('page-script')
    <script src="{{ asset('js/chat.js') }}"></script>
</body>
</html>
