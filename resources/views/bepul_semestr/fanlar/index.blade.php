<x-layouts.sidebar>
    <x-slot:title>Bo'limlar | Bepul</x-slot:title>

    <div class="app-container">
        <div class="app-header">
            <div class="header-left">
                <span class="app-badge">Bepul Semestr</span>
                <h3><i class="fas fa-book text-primary"></i> <a class="text-decoration-none"
                        href="{{ route('bepul_semestr.index') }}">Bo'limlar</a>/Fanlar</h3>
            </div>
            <button class="add-new-glass" onclick="window.location.href='{{ route('bepul_semestr.create') }}'">
                <i class="fas fa-plus"></i> Yangi qo'shish
            </button>
        </div>

        <div class="app-list">
            @foreach ($subjects as $subject)
                <div class="app-row">
                    <div class="status-bar" style="background: {{ $loop->index % 2 == 0 ? '#6366f1' : '#10b981' }}">
                    </div>

                    <div class="row-content">

                        <a href="{{ route('bepul_semestr.fanlar.show', ['bepul_semestr' => $bolim->id, 'fanlar' => $subject->id]) }}"
                            class="col-info-link" style="text-decoration: none; color: inherit; flex: 1;">

                            <div class="col-info">
                                <div class="subject-main">
                                    <h3>{{ $subject->nomi }}</h3>
                                    <span class="category-pill">ID: {{ $subject->id }}</span>
                                </div>
                                <div class="subject-sub">
                                    <span class="type-box text-muted">
                                        Bo'lim: {{ $bolim->nomi }}
                                    </span>
                                </div>
                            </div>

                        </a>

                        <div class="col-teacher">
                            <div class="teacher-profile">
                                <div class="teacher-details">
                                    <span class="status-badge-block"><i class="fas fa-circle-user"></i>
                                        O'quvchilar soni: </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-actions">
                            <div class="action-group" style="display: flex; align-items: center; gap: 5px;">
                                <a href="{{ route('bepul_semestr.edit', $subject->id) }}" class="icon-btn edit-btn"
                                    title="Tahrirlash">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form action="{{ route('bepul_semestr.destroy', $subject->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-btn delete-btn" title="O'chirish"
                                        onclick="return confirm('O\'chirilsinmi?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>

</x-layouts.sidebar>
