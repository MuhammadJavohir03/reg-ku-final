@if ($paginator->hasPages())
    <style>
        .app-pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 16px 8px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            box-sizing: border-box;
        }

        /* Asosiy harakat tugmalari */
        .app-pg-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 44px;
            padding: 0 18px;
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 99px; /* Pill style */
            text-decoration: none;
            box-shadow: 0 4px 14px -2px rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(8px);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }

        .app-pg-action:hover:not(.disabled) {
            background: #11101d;
            color: #ffffff;
            border-color: #11101d;
            box-shadow: 0 6px 20px -3px rgba(37, 99, 235, 0.4);
            transform: translateY(-1px);
        }

        .app-pg-action:active:not(.disabled) {
            transform: scale(0.96);
        }

        .app-pg-action.disabled {
            opacity: 0.4;
            background: #f1f5f9;
            color: #94a3b8;
            border-color: transparent;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Mobil uchun Markaziy status badj */
        .app-pg-status-mobile {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 99px;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.03);
        }

        .app-pg-status-mobile span {
            color: #11101d;
        }

        /* Desktop konteyner va raqamlar */
        .app-pg-desktop {
            display: none;
            align-items: center;
            background: #f8fafc;
            padding: 4px;
            border-radius: 99px;
            border: 1px solid #e2e8f0;
            gap: 2px;
        }

        .app-pg-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 8px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            border-radius: 99px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .app-pg-num:hover:not(.active):not(.dots) {
            color: #0f172a;
            background: rgba(226, 232, 240, 0.6);
        }

        .app-pg-num.active {
            background: linear-gradient(135deg, #11101de3, #11101daf);
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.603);
        }

        .app-pg-num.dots {
            color: #94a3b8;
            cursor: default;
        }

        /* MEDIA RESPONSIVE */
        @media (min-width: 640px) {
            .app-pg-status-mobile {
                display: none;
            }
            .app-pg-desktop {
                display: flex;
            }
        }
    </style>

    <div class="app-pagination-container">
        
        <!-- Orqaga tugmasi -->
        @if ($paginator->onFirstPage())
            <span class="app-pg-action disabled">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                <span>Orqaga</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="app-pg-action">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                <span>Orqaga</span>
            </a>
        @endif

        <!-- MOBIL: App-style Status Indicator -->
        <div class="app-pg-status-mobile">
            <span>{{ $paginator->currentPage() }}</span> / {{ $paginator->lastPage() }}
        </div>

        <!-- PC: Floating Capsule Numbers -->
        <div class="app-pg-desktop">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="app-pg-num dots">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="app-pg-num active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="app-pg-num">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        <!-- Keyingi tugmasi -->
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="app-pg-action">
                <span>Oldinga</span>
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="app-pg-action disabled">
                <span>Oldinga</span>
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif

    </div>
@endif