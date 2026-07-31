<!DOCTYPE html>
<html lang="uz" dir="ltr">

<head>
    <title>{{ $title ?? 'Register Office' }}</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ads.css') }}">
    <link rel="stylesheet" href="{{ asset('css/talaba_table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/subject.css') }}">
    <link rel="stylesheet" href="{{ asset('css/grade.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bepul_fanlar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ariza_free.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ariza.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ozlashtirish.css') }}">
    <link rel="stylesheet" href="{{ asset('css/test.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jadvallar.css') }}">

    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" href="{{ asset('img/Logo-title.png') }}" type="image/x-icon">

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/uz.js"></script>
</head>

<style>
    a {
        text-decoration: none;
    }
</style>

{{--
    Diqqat: toast (success/error/info/warning) xabarnomalarining butun dizayni
    endi jadvallar.css ichida ".jd-toast-*" nomi bilan yashaydi — shu sahifada
    inline saqlanmaydi, shunda barcha sahifalarda bir xil, markazlashgan
    dizayn ishlatiladi.
--}}

<body>
    <div class="jd-toast-stack" id="jdToastStack">

        @if (session('success'))
            <div class="jd-toast jd-toast-success" data-autohide="4000">
                <div class="jd-toast-icon"><i class="bx bx-check-circle"></i></div>
                <div class="jd-toast-body">
                    <span class="jd-toast-title">Muvaffaqiyatli</span>
                    <div class="jd-toast-text">{{ session('success') }}</div>
                </div>
                <button type="button" class="jd-toast-close" aria-label="Yopish"><i class="bx bx-x"></i></button>
                <div class="jd-toast-bar"><span style="animation-duration:4000ms;"></span></div>
            </div>
        @endif

        @if (session('error'))
            <div class="jd-toast jd-toast-error" data-autohide="5000">
                <div class="jd-toast-icon"><i class="bx bx-error-circle"></i></div>
                <div class="jd-toast-body">
                    <span class="jd-toast-title">Xatolik</span>
                    <div class="jd-toast-text">{{ session('error') }}</div>
                </div>
                <button type="button" class="jd-toast-close" aria-label="Yopish"><i class="bx bx-x"></i></button>
                <div class="jd-toast-bar"><span style="animation-duration:5000ms;"></span></div>
            </div>
        @endif

        @if (session('info'))
            <div class="jd-toast jd-toast-info" data-autohide="4000">
                <div class="jd-toast-icon"><i class="bx bx-info-circle"></i></div>
                <div class="jd-toast-body">
                    <span class="jd-toast-title">Ma'lumot</span>
                    <div class="jd-toast-text">{{ session('info') }}</div>
                </div>
                <button type="button" class="jd-toast-close" aria-label="Yopish"><i class="bx bx-x"></i></button>
                <div class="jd-toast-bar"><span style="animation-duration:4000ms;"></span></div>
            </div>
        @endif

        @if (session('warning'))
            <div class="jd-toast jd-toast-warning" data-autohide="4500">
                <div class="jd-toast-icon"><i class="bx bx-error"></i></div>
                <div class="jd-toast-body">
                    <span class="jd-toast-title">Diqqat</span>
                    <div class="jd-toast-text">{{ session('warning') }}</div>
                </div>
                <button type="button" class="jd-toast-close" aria-label="Yopish"><i class="bx bx-x"></i></button>
                <div class="jd-toast-bar"><span style="animation-duration:4500ms;"></span></div>
            </div>
        @endif

        @if ($errors->any())
            <div class="jd-toast jd-toast-error" data-autohide="6000">
                <div class="jd-toast-icon"><i class="bx bx-error-circle"></i></div>
                <div class="jd-toast-body">
                    <span class="jd-toast-title">
                        Formada {{ $errors->count() }} ta xatolik bor
                    </span>
                    <div class="jd-toast-text">{{ $errors->first() }}</div>
                </div>
                <button type="button" class="jd-toast-close" aria-label="Yopish"><i class="bx bx-x"></i></button>
                <div class="jd-toast-bar"><span style="animation-duration:6000ms;"></span></div>
            </div>
        @endif

    </div>

    {{-- ===================== PC SIDEBAR ===================== --}}
    <div class="sidebar close" id="pc-sidebar">
        {{-- Logo --}}
        <div class="logo-area">
            <i class="bx bx-user-check"></i>
            <img src="{{ asset('img/Logo2.png') }}" alt="Logo" class="logo-img">
        </div>

        {{-- Nav Links --}}
        <ul class="nav-links" id="pc-nav">
            <li>
                <a href="{{ route('index') }}">
                    <i class="bx bx-grid-alt"></i>
                    <span class="link_name">Dashboard</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="{{ route('index') }}">Dashboard</a></li>
                </ul>
            </li>

            @if (auth()->user()?->role === 'admin')
                <li>
                    <div class="iocn-link">
                        <a href="{{ route('users.index') }}">
                            <i class="bx bx-group"></i>
                            <span class="link_name">Foydalanuvchilar</span>
                        </a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="sub-menu">
                        <li class="my-1 fs-3"><a href="{{ route('users.index') }}">Barchasi</a></li>
                        <li class="my-1 fs-3"><a href="{{ route('teacher.index') }}">O'qituvchilar</a></li>
                        <li class="my-1 fs-3"><a href="{{ route('admins.index') }}">Adminlar</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('sidebar_boshqaruv.index') }}">
                        <i class="bx bx-slider-alt"></i>
                        <span class="link_name">Ariza boshqaruvi</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('sidebar_boshqaruv.index') }}">Ariza boshqaruvi</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <div class="iocn-link">
                        <a href="{{ route('free_semestr.index') }}">
                            <i class="bx bx-collection"></i>
                            <span class="link_name">Arizalar</span>
                        </a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="sub-menu">
                        <li><a href="{{ route('free_semestr.index') }}">Bepul imkoniyatlar</a></li>
                        <li><a href="{{ route('mini_semestr_admin.index') }}">Mini Semestr</a></li>
                        @if (auth()->check() && in_array(auth()->user()->email, ['javohir8386@gmail.com', 'samiyusuf@gmail.com']))
                            <li><a class="text-danger" href="{{ route('ariza_admin.index') }}">Arizalar (admin)</a>
                            </li>
                        @endif
                    </ul>
                </li>
                <li>
                    <a href="{{ route('bepul_semestr.index') }}">
                        <i class="bx bx-award"></i>
                        <span class="link_name">Bo'limlar</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('bepul_semestr.index') }}">Bo'limlar</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('ozlashtirish') }}">
                        <i class="bx bx-pie-chart-alt-2"></i>
                        <span class="link_name">O'zlashtirish</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('ozlashtirish') }}">O'zlashtirish</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('bepul_maktab.index') }}">
                        <i class="fas fa-tag"></i>
                        <span class="link_name">Bepul Maktab</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('bepul_maktab.index') }}">Bepul Maktab</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('mini_maktab.index') }}">
                        <i class="fas fa-money-bill-wave"></i>
                        <span class="link_name">Mini Maktab</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('mini_maktab.index') }}">Mini Maktab</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('subject.index') }}">
                        <i class="bx bx-book"></i>
                        <span class="link_name">Fanlar</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('subject.index') }}">Fanlar</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('kafedra-fakultet.index') }}">
                        <i class="bx bx-buildings"></i>
                        <span class="link_name">Kafedra va Fakultetlar</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('kafedra-fakultet.index') }}">Kafedra va
                                Fakultetlar</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('oquv_yili.index') }}">
                        <i class="bx bx-buildings"></i>
                        <span class="link_name">O'quv yili</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('oquv_yili.index') }}">O'quv yili</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('jurnal.index') }}">
                        <i class="bx bx-book-open"></i>
                        <span class="link_name">Jurnal</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('jurnal.index') }}">Jurnal</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('umumiy_natijalar') }}">
                        <i class="bx bx-medal"></i>
                        <span class="link_name">Umumiy Natijalar</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('umumiy_natijalar') }}">Umumiy Natijalar</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('savol_bank.index') }}">
                        <i class="bx bx-question-mark"></i>
                        <span class="link_name">Savol banki</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('savol_bank.index') }}">Savol banki</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('admin_chat') }}">
                        <i class="bx bx-chat"></i>
                        <span class="link_name">Chat</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('admin_chat') }}">Chat</a></li>
                    </ul>
                </li>
                @if (auth()->check() && in_array(auth()->user()->email, ['javohir8386@gmail.com', 'samiyusuf@gmail.com']))
                    <li>
                        <a href="{{ route('admin.sections.index') }}">
                            <i class="bx bx-collection"></i>
                            <span class="link_name">Sections</span>
                        </a>
                        <ul class="sub-menu blank">
                            <li><a class="link_name" href="{{ route('admin.sections.index') }}">Sections</a></li>
                        </ul>
                    </li>
                @endif
            @endif

            @if (auth()->user()?->role === 'talaba')
                @php
                    $showFree = Cache::get('sidebar_free_semestr', true);
                    $showMini = Cache::get('sidebar_mini_semestr', true);
                @endphp
                <li>
                    <div class="iocn-link">
                        <a href="#"><i class="bx bx-collection"></i>
                            <span class="link_name">Arizalar</span>
                        </a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="sub-menu">
                        @if ($showFree)
                            <li><a href="{{ route('free_semestr_user.index') }}">Bepul Maktab</a></li>
                        @endif
                        @if ($showMini)
                            <li><a href="{{ route('mini_semestr_user.index') }}">Mini Maktab</a></li>
                        @endif
                    </ul>
                </li>
                <li>
                    <a href="{{ route('talaba.bepul_maktab.index') }}">
                        <i class="fas fa-tag"></i>
                        <span class="link_name">Bepul Maktab</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('talaba.bepul_maktab.index') }}">Bepul Maktab</a>
                        </li>
                    </ul>
                </li>
                <li class="{{ request()->routeIs('talaba.mini_maktab.*') ? 'active' : '' }}">
                    <a href="{{ route('talaba.mini_maktab.index') }}">
                        <i class="fas fa-money-bill-wave"></i>
                        <span class="link_name">Mini Maktab</span>
                    </a>

                    <ul class="sub-menu blank">
                        <li>
                            <a class="link_name" href="{{ route('talaba.mini_maktab.index') }}">
                                Mini Maktab
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('chat') }}">
                        <i class="bx bx-chat"></i>
                        <span class="link_name">Chat</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('chat') }}">Chat</a></li>
                    </ul>
                </li>
            @endif

            @if (auth()->user()?->role === 'teacher')
                <li class="{{ request()->routeIs('bepul_maktab.*') ? 'active' : '' }}">
                    <a href="{{ route('bepul_maktab.index') }}">
                        <i class="bx bx-book-open"></i>
                        <span class="link_name">Bepul Maktab</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('bepul_maktab.index') }}">Bepul Maktab</a></li>
                    </ul>
                </li>
                <li class="{{ request()->routeIs('mini_maktab.*') ? 'active' : '' }}">
                    <a href="{{ route('mini_maktab.index') }}">
                        <i class="bx bx-book-reader"></i>
                        <span class="link_name">Mini Semestr</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('mini_maktab.index') }}">Mini Semestr</a></li>
                    </ul>
                </li>
                <li class="{{ request()->routeIs('jurnal.*') ? 'active' : '' }}">
                    <a href="{{ route('jurnal.index') }}">
                        <i class="bx bx-book-content"></i>
                        <span class="link_name">Jurnal</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('jurnal.index') }}">Jurnal</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('admin_chat') }}">
                        <i class="bx bx-chat"></i>
                        <span class="link_name">Chat</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="{{ route('admin_chat') }}">Chat</a></li>
                    </ul>
                </li>
            @endif
        </ul>

        @auth
            <div class="profile-area">
                <div class="profile-avatar">
                    {{ mb_substr(auth()->user()->getAttribute('To‘liq_ismi') ?? 'U', 0, 2) }}
                </div>

                <div class="profile-info">
                    <div class="profile-name">
                        {{ auth()->user()->getAttribute('To‘liq_ismi') ?? 'Foydalanuvchi' }}
                    </div>

                    <div class="profile-meta">
                        {{ auth()->user()->role }}
                        @if (auth()->user()->Guruh)
                            • {{ auth()->user()->Guruh }}
                        @endif
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn" title="Chiqish">
                        <i class="bx bx-log-out"></i>
                    </button>
                </form>
            </div>
        @endauth

        @guest
            <div class="profile-area">
                <div class="profile-avatar">
                    <i class="bx bx-user"></i>
                </div>

                <div class="profile-info">
                    <div class="profile-name">
                        Mehmon
                    </div>

                    <div class="profile-meta">
                        Tizimga kirmagansiz
                    </div>
                </div>

                <a href="{{ route('login') }}" class="logout-btn" title="Kirish">
                    <i class="bx bx-log-in"></i>
                </a>
            </div>
        @endguest
    </div>

    {{-- ===================== MOBILE TOP NAVBAR ===================== --}}
    <div class="mobile-navbar">
        <button class="mn-hamburger" id="mob-menu-btn">
            <i class="bx bx-menu" id="mob-menu-icon"></i>
        </button>
        <a href="{{ route('index') }}" class="mn-logo">
            <img src="{{ asset('img/Logo2.png') }}" alt="Logo">
        </a>
        <div class="mn-spacer"></div>
        @auth
            <div class="mn-profile">
                <div class="mn-avatar">
                    {{ mb_substr(auth()->user()->getAttribute('To‘liq_ismi') ?? 'U', 0, 2) }}
                </div>
                <div>
                    <div class="mn-name">
                        {{ auth()->user()->getAttribute('To‘liq_ismi') ?? 'Foydalanuvchi' }}
                    </div>
                    <div style="font-size:10px; color:#888;">
                        {{ auth()->user()->role }}
                        @if (auth()->user()->Guruh)
                            • {{ auth()->user()->Guruh }}
                        @endif
                    </div>
                </div>
            </div>
        @endauth
    </div>

    {{-- ===================== MOBILE DRAWER ===================== --}}
    <div class="drawer-overlay" id="drawer-overlay"></div>
    <div class="mobile-drawer" id="mobile-drawer">
        <ul class="drawer-nav">
            <li>
                <a href="{{ route('index') }}">
                    <i class="bx bx-grid-alt"></i>
                    <span class="link_name">Dashboard</span>
                </a>
            </li>

            @if (auth()->user()?->role === 'admin')
                <li>
                    <div class="d-iocn-link" onclick="toggleDrawerMenu(this)">
                        <a href="{{ route('users.index') }}">
                            <i class="bx bx-group"></i>
                            <span class="link_name">Foydalanuvchilar</span>
                        </a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="d-sub-menu">
                        <li class="my-1 fs-3"><a href="{{ route('users.index') }}">Barchasi</a></li>
                        <li class="my-1 fs-3"><a href="{{ route('teacher.index') }}">O'qituvchilar</a></li>
                        <li class="my-1 fs-3"><a href="{{ route('admins.index') }}">Adminlar</a></li>
                    </ul>
                </li>
                <li><a href="{{ route('sidebar_boshqaruv.index') }}"><i class="bx bx-slider-alt"></i><span
                            class="link_name">Ariza boshqaruvi</span></a></li>
                <li>
                    <div class="d-iocn-link" onclick="toggleDrawerMenu(this)">
                        <a href="{{ route('free_semestr.index') }}"><i class="bx bx-collection"></i><span
                                class="link_name">Arizalar</span></a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="d-sub-menu">
                        <li class="my-2"><a href="{{ route('free_semestr.index') }}">Bepul imkoniyatlar</a></li>
                        <li class="my-2"><a href="{{ route('mini_semestr_admin.index') }}">Mini Semestr</a></li>
                        @if (auth()->check() && in_array(auth()->user()->email, ['javohir8386@gmail.com', 'samiyusuf@gmail.com']))
                            <li class="my-2"><a class="text-danger"
                                    href="{{ route('ariza_admin.index') }}">Arizalar (admin)</a></li>
                        @endif
                    </ul>
                </li>
                <li><a href="{{ route('bepul_semestr.index') }}"><i class="bx bx-award"></i><span
                            class="link_name">Bo'limlar</span></a></li>
                <li><a href="{{ route('ozlashtirish') }}"><i class="bx bx-pie-chart-alt-2"></i><span
                            class="link_name">O'zlashtirish</span></a></li>
                <li><a href="{{ route('bepul_maktab.index') }}"><i class="fas fa-chalkboard-user"></i><span
                            class="link_name">Bepul Maktab</span></a></li>
                <li><a href="{{ route('subject.index') }}"><i class="bx bx-book"></i><span
                            class="link_name">Fanlar</span></a></li>
                <li><a href="{{ route('umumiy_natijalar') }}"><i class="bx bx-medal"></i><span
                            class="link_name">Natijalar</span></a></li>
                <li><a href="{{ route('savol_bank.index') }}"><i class="bx bx-question-mark"></i><span
                            class="link_name">Savol banki</span></a></li>
                <li><a class="link_name" href="{{ route('jurnal.index') }}"><i
                            class="bx bx-book-open"></i>Jurnal</a></li>
                <li><a href="{{ route('admin_chat') }}"><i class="bx bx-chat"></i><span
                            class="link_name">Chat</span></a></li>
                @if (auth()->check() && in_array(auth()->user()->email, ['javohir8386@gmail.com', 'samiyusuf@gmail.com']))
                    <li><a href="{{ route('admin.sections.index') }}"><i
                                class="bx bx-collection text-danger"></i><span
                                class="text-danger link_name">Sections</span></a></li>
                @endif
            @endif

            @if (auth()->user()?->role === 'talaba')
                @php
                    $showFree = Cache::get('sidebar_free_semestr', true);
                    $showMini = Cache::get('sidebar_mini_semestr', true);
                @endphp
                <li>
                    <div class="d-iocn-link" onclick="toggleDrawerMenu(this)">
                        <a href="#"><i class="bx bx-collection"></i><span class="link_name">Arizalar</span></a>
                        <i class="bx bxs-chevron-down arrow"></i>
                    </div>
                    <ul class="d-sub-menu">
                        @if ($showFree)
                            <li class='my-2'><a href="{{ route('free_semestr_user.index') }}">Bepul
                                    imkoniyatlar</a></li>
                        @endif
                        @if ($showMini)
                            <li class='my-2'><a href="{{ route('mini_semestr_user.index') }}">Mini Semestr</a>
                            </li>
                        @endif
                    </ul>
                </li>
                <li><a href="{{ route('talaba.bepul_maktab.index') }}"><i class="bx bx-book-open"></i><span
                            class="link_name">Bepul Maktab</span></a></li>
                <li><a href="{{ route('talaba.mini_maktab.index') }}"><i class="bx bx-book-reader"></i><span
                            class="link_name">Mini Semestr</span></a></li>
                <li><a href="{{ route('chat') }}"><i class="bx bx-chat"></i><span class="link_name">Chat</span></a>
                </li>
            @endif

            @if (auth()->user()?->role === 'teacher')
                <li><a href="{{ route('bepul_maktab.index') }}"><i class="bx bx-book-open"></i><span
                            class="link_name">Bepul Maktab</span></a></li>
                <li><a href="{{ route('mini_maktab.index') }}"><i class="bx bx-book-reader"></i><span
                            class="link_name">Mini Semestr</span></a></li>
                <li><a href="{{ route('jurnal.index') }}"><i class="bx bx-book-content"></i><span
                            class="link_name">Jurnal</span></a></li>
                <li><a href="{{ route('admin_chat') }}"><i class="bx bx-chat"></i><span
                            class="link_name">Chat</span></a></li>
            @endif

            @auth
                {{-- Logout --}}
                <li style="margin-top:8px; border-top:1px solid rgba(255,255,255,0.06); padding-top:8px;">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            style="background:none; border:none; width:100%; cursor:pointer;
                display:flex; align-items:center; color:#ff6b6b; padding:0;">

                            <i class="bx bx-log-out"
                                style="font-size:20px; min-width:60px; height:48px; line-height:48px; text-align:center;"></i>

                            <span style="font-size:14px;">Chiqish</span>
                        </button>
                    </form>
                </li>
            @endauth

            @guest
                {{-- Login --}}
                <li style="margin-top:8px; border-top:1px solid rgba(255,255,255,0.06); padding-top:8px;">
                    <a href="{{ route('login') }}"
                        style="display:flex; align-items:center; color:#fff; text-decoration:none;">

                        <i class="bx bx-log-in"
                            style="font-size:20px; min-width:60px; height:48px; line-height:48px; text-align:center;"></i>

                        <span style="font-size:14px;">Kirish</span>
                    </a>
                </li>
            @endguest

        </ul>
    </div>

    {{-- ===================== BOTTOM NAV (MOBILE) ===================== --}}
    <nav class="bottom-nav">

        @auth

            @if (auth()->user()->role === 'admin')
                <a href="{{ route('index') }}"><i class="bx bx-grid-alt"></i><span>Bosh</span></a>
                <a href="{{ route('users.index') }}"><i class="bx bx-group"></i><span>Users</span></a>
                <a href="{{ route('bepul_maktab.index') }}"><i class="fas fa-chalkboard-user"></i><span>Bepul
                        Maktab</span></a>
                <a href="{{ route('subject.index') }}"><i class="bx bx-book"></i><span>Fanlar</span></a>
                <a href="{{ route('admin_chat') }}"><i class="bx bx-chat"></i><span>Chat</span></a>
                @if (auth()->check() && in_array(auth()->user()->email, ['javohir8386@gmail.com', 'samiyusuf@gmail.com']))
                    <a class="text-danger" href="{{ route('ariza_admin.index') }}"><i
                            class="text-danger bx bx-file"></i><span>Arizalar
                            (admin)</span></a>
                @endif
            @elseif (auth()->user()->role === 'teacher')
                <a href="{{ route('index') }}"><i class="bx bx-grid-alt"></i><span>Bosh</span></a>
                <a href="{{ route('bepul_maktab.index') }}"><i class="bx bx-book-open"></i><span>Bepul
                        Maktab</span></a>
                <a href="{{ route('mini_maktab.index') }}"><i class="bx bx-book-reader"></i><span>Mini
                        Semestr</span></a>
                <a href="{{ route('jurnal.index') }}"><i class="bx bx-book-content"></i><span>Jurnal</span></a>
                <a href="{{ route('admin_chat') }}"><i class="bx bx-chat"></i><span>Chat</span></a>

                <a href="#" onclick="event.preventDefault(); document.getElementById('mob-logout').submit();">
                    <i class="bx bx-log-out"></i>
                    <span>Chiqish</span>
                </a>

                <form id="mob-logout" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            @else
                <a href="{{ route('index') }}"><i class="bx bx-grid-alt"></i><span>Bosh</span></a>
                <a href="{{ route('free_semestr_user.index') }}"><i
                        class="bx bx-collection"></i><span>Arizalar</span></a>
                <a href="{{ route('talaba.bepul_maktab.index') }}"><i class="bx bx-book-open"></i><span>Bepul
                        Maktab</span></a>
                <a href="{{ route('talaba.mini_maktab.index') }}"><i class="bx bx-book-reader"></i><span>Mini
                        Semestr</span></a>
                <a href="{{ route('chat') }}"><i class="bx bx-chat"></i><span>Chat</span></a>

                <a href="#" onclick="event.preventDefault(); document.getElementById('mob-logout').submit();">
                    <i class="bx bx-log-out"></i>
                    <span>Chiqish</span>
                </a>

                <form id="mob-logout" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            @endif

        @endauth


        @guest

            <a href="{{ route('index') }}">
                <i class="bx bx-grid-alt"></i>
                <span>Bosh</span>
            </a>

            <a href="{{ route('login') }}">
                <i class="bx bx-log-in"></i>
                <span>Kirish</span>
            </a>

        @endguest

    </nav>

    {{-- ===================== MAIN CONTENT ===================== --}}
    <section class="home-section">
        {{-- PC Topbar --}}
        <div class="topbar">
            <i class="bx bx-menu" id="pc-menu-btn"></i>
            <span class="topbar-title">{{ $title ?? 'Register Office' }}</span>
            @auth
                <div class="topbar-profile">
                    <div class="tp-avatar">
                        {{ mb_substr(auth()->user()->getAttribute('To‘liq_ismi') ?? 'U', 0, 2) }}
                    </div>
                    <div class="tp-info">
                        <div class="tp-name">{{ auth()->user()->getAttribute('To‘liq_ismi') ?? 'Foydalanuvchi' }}</div>
                        <div class="tp-role">
                            {{ auth()->user()->role }}
                            @if (auth()->user()->Guruh)
                                • {{ auth()->user()->Guruh }}
                            @endif
                            @if (auth()->user()->Kurs)
                                • {{ auth()->user()->Kurs }}-kurs
                            @endif
                        </div>
                    </div>
                </div>
            @endauth
        </div>

        {{ $slot }}
    </section>

    <script>
        // PC Sidebar toggle
        const pcSidebar = document.getElementById('pc-sidebar');
        const pcMenuBtn = document.getElementById('pc-menu-btn');
        if (pcMenuBtn) {
            pcMenuBtn.addEventListener('click', () => {
                pcSidebar.classList.toggle('close');
            });
        }

        // PC dropdown
        document.querySelectorAll('#pc-nav .iocn-link').forEach(link => {
            link.addEventListener('click', () => {
                link.parentElement.classList.toggle('showMenu');
            });
        });

        // Mobile drawer
        const mobMenuBtn = document.getElementById('mob-menu-btn');
        const mobMenuIcon = document.getElementById('mob-menu-icon');
        const drawer = document.getElementById('mobile-drawer');
        const overlay = document.getElementById('drawer-overlay');

        function openDrawer() {
            drawer.classList.add('open');
            overlay.classList.add('show');
            mobMenuIcon.className = 'bx bx-x';
        }

        function closeDrawer() {
            drawer.classList.remove('open');
            overlay.classList.remove('show');
            mobMenuIcon.className = 'bx bx-menu';
        }

        if (mobMenuBtn) mobMenuBtn.addEventListener('click', () => {
            drawer.classList.contains('open') ? closeDrawer() : openDrawer();
        });
        if (overlay) overlay.addEventListener('click', closeDrawer);

        // Mobile drawer dropdown
        function toggleDrawerMenu(el) {
            el.parentElement.classList.toggle('showMenu');
        }

        // Bottom nav active link
        document.querySelectorAll('.bottom-nav a').forEach(a => {
            if (a.href === window.location.href) a.classList.add('active');
        });
    </script>

    <script>
        // Barcha toast'lar (success/error/info/warning/validatsiya) uchun
        // mustaqil ishlaydigan yopilish mantig'i.
        // Eslatma: avvalgi versiyada faqat BITTA (birinchi) toast avtomatik
        // yopilardi, qolganlari abadiy ekranda qolib ketardi — shu bug shu yerda tuzatildi.
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.jd-toast').forEach(toast => {
                let timer;

                const hideToast = () => {
                    toast.classList.add('jd-toast-hide');
                    setTimeout(() => toast.remove(), 350);
                };

                const startTimer = (ms) => {
                    clearTimeout(timer);
                    timer = setTimeout(hideToast, ms);
                };

                const duration = parseInt(toast.dataset.autohide || '4000', 10);
                startTimer(duration);

                // Sichqoncha ustida turganda avtomatik yopilishni to'xtatib turadi
                toast.addEventListener('mouseenter', () => clearTimeout(timer));
                toast.addEventListener('mouseleave', () => startTimer(1500));

                // Qo'lda yopish tugmasi
                const closeBtn = toast.querySelector('.jd-toast-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        clearTimeout(timer);
                        hideToast();
                    });
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
