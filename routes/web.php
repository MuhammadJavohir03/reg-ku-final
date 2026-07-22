<?php

use App\Http\Controllers\ElonController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArizaAdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BepulFanlarController;
use App\Http\Controllers\BepulMaktabController;
use App\Http\Controllers\BepulSemestrController;
use App\Http\Controllers\FreeController;
use App\Http\Controllers\FreeuserController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\MiniMaktabController;
use App\Http\Controllers\MiniSemestrAdminController;
use App\Http\Controllers\MiniSemestrController;
use App\Http\Controllers\MiniTmaktabController;
use App\Http\Controllers\OzlashtirishController;
use App\Http\Controllers\SavolBankController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\TalabaBepulMaktabController as TalabaBepulMaktabController;
use App\Http\Controllers\TalabaMiniMaktabController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Student\ChatController as StudentChatController;

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/free_semestr', [PageController::class, 'freeSemestr'])->name('free_semestr');
Route::get('/mini_semestr', [PageController::class, 'miniSemestr'])->name('mini_semestr');

Route::resource('subject', SubjectController::class);

Route::get('/ozlashtirish', [PageController::class, 'ozlashtirish'])->name('ozlashtirish');
Route::get('/umumiy_natijalar', [PageController::class, 'umumiyNatijalar'])->name('umumiy_natijalar');
Route::get('/chat', [PageController::class, 'chat'])->name('chat');
Route::get('/admin_chat', [PageController::class, 'adminChat'])->name('admin_chat');

Route::get('/', [ElonController::class, 'index'])->name('index');
Route::resource('elons', ElonController::class)->middleware('auth');;

Route::resource('teacher', TeacherController::class);
Route::resource('admins', AdminController::class);

Route::post('/users/import', [UserController::class, 'store'])->name('students.import');
Route::resource('users', UserController::class);
Route::post('/users/{id}/login-as', [UserController::class, 'loginAs'])->name('users.login_as');
Route::post('/admin/back-to-admin', [UserController::class, 'backToAdmin'])->name('users.back_to_admin');

Route::get('/teachers/search', [UserController::class, 'searchTeachers'])->name('teachers.search');

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::resource('free_semestr', FreeController::class);
Route::resource('free_semestr_user', FreeuserController::class);

Route::resource('mini_semestr_admin', MiniSemestrAdminController::class);

Route::resource('category', CategoryController::class);

Route::resource('bepul_semestr', BepulSemestrController::class);

Route::resource('bepul_semestr.fanlar', BepulFanlarController::class)->names([
    'index' => 'bepul_semestr.fanlar.index',
    'create' => 'bepul_semestr.fanlar.create',
    'show' => 'bepul_semestr.fanlar.show',
]);

Route::get('bepul_semestr/{bolim_id}/fanlar/{subject_id}', [BepulFanlarController::class, 'show'])
    ->name('bepul_semestr.fanlar.show');

Route::post('/grades/import/{subject_id}', [GradeController::class, 'import'])->name('grades.import');
Route::get('subject/{subject_id}/grades', [GradeController::class, 'index'])->name('grades.index');
Route::delete('subject/{subject_id}/grades/clear', [GradeController::class, 'clearAll'])->name('grades.clear');


Route::get('/sidebar-boshqaruv', [SidebarController::class, 'index'])->name('sidebar_boshqaruv.index');
Route::patch('/sidebar-boshqaruv/{key}/toggle', [SidebarController::class, 'toggle'])->name('sidebar_boshqaruv.toggle');
Route::get('/ozlashtirish', [OzlashtirishController::class, 'index'])->name('ozlashtirish');
Route::get('/ozlashtirish/export', [OzlashtirishController::class, 'export'])->name('ozlashtirish.export');
// Route::resource('ozlashtirish', OzlashtirishController::class);

// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::resource('admins', AdminController::class);
//     Route::resource('/users', UserController::class);
//     Route::resource('bepul_semestr.fanlar', BepulFanlarController::class)->names([
//         'index' => 'bepul_semestr.fanlar.index',
//         'create' => 'bepul_semestr.fanlar.create',
//         'show' => 'bepul_semestr.fanlar.show',
//     ]);
//     Route::resource('bepul_semestr', BepulSemestrController::class);
//     Route::resource('mini_semestr_admin', MiniSemestrAdminController::class);
// });

Route::resource('mini_semestr_user', MiniSemestrController::class);
// Route::get('/bepul_maktab', [BepulMaktabController::class, 'index'])->name('bepul_maktab.index');
// Route::get('/bepul_maktab/{bolim_id}', [BepulMaktabController::class, 'fanlar'])->name('bepul_maktab.fanlar');
// Route::get('/bepul_maktab/{bolim_id}/{subject_id}', [BepulMaktabController::class, 'show'])->name('bepul_maktab.show');
// Route::post('/bepul_maktab/{bolim_id}/{subject_id}/settings', [BepulMaktabController::class, 'settings'])->name('bepul_maktab.settings');
// Route::patch('/bepul_maktab/toggle/{id}', [BepulMaktabController::class, 'toggle'])->name('bepul_maktab.toggle');
//Savollar Banki
Route::get('/savol-bank', [SavolBankController::class, 'index'])->name('savol_bank.index');
Route::post('/savol-bank', [SavolBankController::class, 'store'])->name('savol_bank.store');
Route::post('/savol-bank/{bank_id}/import', [SavolBankController::class, 'import'])->name('savol_bank.import');
Route::delete('/savol-bank/{id}', [SavolBankController::class, 'destroy'])->name('savol_bank.destroy');
Route::delete('/savol-bank/question/{id}', [SavolBankController::class, 'destroyQuestion'])->name('savol_bank.question.destroy');
Route::get('/savol-bank/{bank_id}/savollar', [SavolBankController::class, 'show'])->name('savol_bank.show');
Route::delete('/savol-bank/question/{id}', [SavolBankController::class, 'destroyQuestion'])->name('savol_bank.question.destroy');
Route::put('/savol-bank/question/{id}', [SavolBankController::class, 'updateQuestion'])->name('savol_bank.question.update');
//bepul maktab
Route::prefix('bepul-maktab')->name('bepul_maktab.')->group(function () {
    Route::get('/', [BepulMaktabController::class, 'index'])->name('index');
    Route::get('/{bolim_id}', [BepulMaktabController::class, 'fanlar'])->name('fanlar');
    Route::get('/{bolim_id}/{subject_id}', [BepulMaktabController::class, 'sozlamalar'])->name('sozlamalar');
    Route::post('/{bolim_id}/{subject_id}', [BepulMaktabController::class, 'saqlash'])->name('saqlash');
    Route::get(
        '/{bolim_id}/{subject_id}/{user_id}/{session_id}/harakat',
        [BepulMaktabController::class, 'harakat']
    )
        ->name('harakat');
    Route::delete('/session/{id}', [BepulMaktabController::class, 'sessionDelete'])
        ->name('session.delete');
});

Route::patch('/bepul-maktab/status/{id}', [BepulMaktabController::class, 'statusToggle'])->name('bepul_maktab.status');
Route::patch('/bepul-maktab/{bolim_id}/{subject_id}/all-status', [BepulMaktabController::class, 'allStatusToggle'])->name('bepul_maktab.all_status');


//talaba bepul maktab
Route::prefix('talaba/bepul-maktab')->name('talaba.bepul_maktab.')->group(function () {
    Route::get('/', [TalabaBepulMaktabController::class, 'index'])->name('index');
    Route::post('/{ariza_id}/boshlash', [TalabaBepulMaktabController::class, 'boshlash'])->name('boshlash');
    Route::get('/{attempt_id}/test', [TalabaBepulMaktabController::class, 'test'])->name('test');
    Route::post('/{attempt_id}/yuborish', [TalabaBepulMaktabController::class, 'yuborish'])->name('yuborish');
    Route::post('/{attempt_id}/chiqish', [TalabaBepulMaktabController::class, 'chiqish'])->name('chiqish');
});
Route::get('/talaba/bepul-maktab/{attempt_id}/natija', [TalabaBepulMaktabController::class, 'natija'])->name('talaba.bepul_maktab.natija');


Route::prefix('ariza_admin')->name('ariza_admin.')->group(function () {
    Route::get('/', [ArizaAdminController::class, 'index'])->name('index');
    Route::get('/qidirish-talaba', [ArizaAdminController::class, 'searchUser'])->name('search_user');
    Route::get('/gradesni-tekshirish', [ArizaAdminController::class, 'checkGrade'])->name('check_grade');
    Route::post('/', [ArizaAdminController::class, 'store'])->name('store');
    Route::get('/{ariza_admin}/edit', [ArizaAdminController::class, 'edit'])->name('edit');
    Route::put('/{ariza_admin}', [ArizaAdminController::class, 'update'])->name('update');
    Route::delete('/{type}/{ariza_admin}', [ArizaAdminController::class, 'destroy'])
        ->where('type', 'mini|free')
        ->name('destroy');
});


Route::prefix('mini_maktab')->name('mini_maktab.')->group(function () {
    // Asosiy sahifalar
    Route::get('/', [MiniMaktabController::class, 'index'])->name('index');
    Route::get('/{bolim_id}/fanlar', [MiniMaktabController::class, 'fanlar'])->name('fanlar');
    Route::get('/{bolim_id}/fan/{subject_id}', [MiniMaktabController::class, 'mavzular'])->name('mavzular');
    // Mavzular
    Route::post('/{bolim_id}/fan/{subject_id}/mavzu', [MiniMaktabController::class, 'mavzuYarat'])
        ->name('mavzu.yarat');
    Route::delete('/mavzu/{id}', [MiniMaktabController::class, 'mavzuOchir'])
        ->name('mavzu.ochir');
    Route::get('/{bolim_id}/fan/{subject_id}/mavzu/{mavzu_id}', [MiniMaktabController::class, 'mavzuShow'])
        ->name('mavzu.show');
    // Materiallar
    Route::post('/mavzu/{mavzu_id}/material', [MiniMaktabController::class, 'materialQosh'])
        ->name('material.qosh');
    Route::delete('/material/{id}', [MiniMaktabController::class, 'materialOchir'])
        ->name('material.ochir');

    Route::put('/material/{id}/test-sozlama', [MiniMaktabController::class, 'testSozlama'])
        ->name('material.test_sozlama');
    // Talabalar statusi
    Route::post('/status/{id}', [MiniMaktabController::class, 'statusToggle'])
        ->name('status.toggle');

    Route::post('/{bolim_id}/fan/{subject_id}/barcha-status', [MiniMaktabController::class, 'allStatusToggle'])
        ->name('status.all');
    // Talabaning test urinishlari
    Route::get(
        '/{bolim}/fan/{subject}/talaba/{user}/sessions/{material}',
        [MiniMaktabController::class, 'talabaSessions']
    )
        ->name('talaba.sessions');
    // Bitta urinish tafsiloti
    Route::get(
        '/{bolim}/fan/{subject}/talaba/{user}/harakat/{session}',
        [MiniMaktabController::class, 'harakat']
    )
        ->name('harakat');
    // Urinishni o'chirish
    Route::delete(
        '/session/{session}',
        [MiniMaktabController::class, 'sessionDelete']
    )
        ->name('session.delete');


    Route::prefix('mini-maktab')->name('mini_maktab.')->group(function () {

        // 1. Bo'limlar ro'yxati
        Route::get('/', [MiniMaktabController::class, 'index'])->name('index');

        // 2. Bo'lim ichidagi fanlar
        Route::get('/{bolim_id}', [MiniMaktabController::class, 'fanlar'])->name('fanlar');

        // 3. Fan ichidagi mavzular (asosiy sahifa)
        Route::get('/{bolim_id}/{subject_id}', [MiniMaktabController::class, 'mavzular'])->name('mavzular');

        // 4. Mavzu yaratish / o'chirish
        Route::post('/{bolim_id}/{subject_id}/mavzu', [MiniMaktabController::class, 'mavzuYarat'])->name('mavzu.yarat');
        Route::delete('/mavzu/{id}', [MiniMaktabController::class, 'mavzuOchir'])->name('mavzu.ochir');

        // 5. Mavzu ichidagi materiallar
        Route::get('/{bolim_id}/{subject_id}/mavzu/{mavzu_id}', [MiniMaktabController::class, 'mavzuShow'])->name('mavzu.show');

        // 6. Material qo'shish / o'chirish / sozlash / status
        Route::post('/mavzu/{mavzu_id}/material', [MiniMaktabController::class, 'materialQosh'])->name('material.qosh');
        Route::delete('/material/{id}', [MiniMaktabController::class, 'materialOchir'])->name('material.ochir');
        Route::put('/material/{id}/test-sozlama', [MiniMaktabController::class, 'testSozlama'])->name('material.test_sozlama');
        Route::patch('/material/{id}/status', [MiniMaktabController::class, 'materialStatusToggle'])->name('material.status.toggle');

        // 7. Talaba status (faqat shu fan uchun — status=0 bo'lsa yakuniy yashiriladi)
        Route::post('/status/{id}', [MiniMaktabController::class, 'statusToggle'])->name('status.toggle');
        Route::post('/{bolim_id}/{subject_id}/status-all', [MiniMaktabController::class, 'allStatusToggle'])->name('status.all');

        // 8. Talaba urinishlari, javoblar tahlili, urinishni o'chirish
        Route::get('/{bolim_id}/{subject_id}/talaba/{user_id}/sessions/{material_id}', [MiniMaktabController::class, 'talabaSessions'])
            ->name('talaba.sessions');
        Route::get('/{bolim_id}/{subject_id}/talaba/{user_id}/harakat/{session_id}', [MiniMaktabController::class, 'harakat'])
            ->name('harakat');
        Route::delete('/session/{id}', [MiniMaktabController::class, 'sessionDelete'])->name('session.delete');
    });
});



Route::prefix('bepul-maktab')->name('bepul_maktab.')->group(function () {
    Route::get('/', [BepulMaktabController::class, 'index'])->name('index');
    Route::get('/{bolim_id}/fanlar', [BepulMaktabController::class, 'fanlar'])->name('fanlar');
    Route::get('/{bolim_id}/{subject_id}/sozlamalar', [BepulMaktabController::class, 'sozlamalar'])->name('sozlamalar');
    Route::post('/{bolim_id}/{subject_id}/saqlash', [BepulMaktabController::class, 'saqlash'])->name('saqlash');

    Route::post('/status/{id}', [BepulMaktabController::class, 'statusToggle'])->name('status.toggle');
    Route::post('/{bolim_id}/{subject_id}/all-status', [BepulMaktabController::class, 'allStatusToggle'])->name('all.status.toggle');
    // YANGI: talabaning urinishlari
    Route::get('/{bolim_id}/{subject_id}/{user_id}/sessions', [BepulMaktabController::class, 'talabaSessions'])
        ->name('talaba.sessions');
    // Harakat (javoblar tahlili)
    Route::get('/{bolim_id}/{subject_id}/{user_id}/{session_id}/harakat', [BepulMaktabController::class, 'harakat'])
        ->name('harakat');
    Route::delete('/session/{id}', [BepulMaktabController::class, 'sessionDelete'])->name('session.delete');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
    Route::get('/jurnal/subjects', [JurnalController::class, 'subjectsByType'])->name('jurnal.subjects');
    Route::get('/jurnal/topics', [JurnalController::class, 'topicsList'])->name('jurnal.topics');
    Route::get('/jurnal/students', [JurnalController::class, 'students'])->name('jurnal.students');
    Route::post('/jurnal/grade', [JurnalController::class, 'updateGrade'])->name('jurnal.grade.update');
    Route::post('/jurnal/topic-grade', [JurnalController::class, 'updateTopicGrade'])->name('jurnal.topic.grade.update');
    Route::get('/jurnal/grade-history', [JurnalController::class, 'gradeHistory'])
        ->name('jurnal.grade.history');
    Route::get('/jurnal/export', [JurnalController::class, 'export'])->name('jurnal.export');
});

Route::middleware('auth')->group(function () {
    Route::get('/mini-maktab', [TalabaMiniMaktabController::class, 'index'])->name('talaba.mini_maktab.index');
    Route::get('/mini-maktab/{miniSemestr}/mavzular', [TalabaMiniMaktabController::class, 'mavzular'])->name('talaba.mini_maktab.mavzular');
    Route::get('/mini-maktab/{miniSemestr}/mavzu/{mavzu}', [TalabaMiniMaktabController::class, 'mavzuShow'])->name('talaba.mini_maktab.mavzu.show');
    Route::post('/mini-maktab/{miniSemestr}/material/{material}/boshlash', [TalabaMiniMaktabController::class, 'boshlash'])->name('talaba.mini_maktab.boshlash');
    Route::get('/mini-maktab/test/{attempt}', [TalabaMiniMaktabController::class, 'test'])->name('talaba.mini_maktab.test');
    Route::post('/mini-maktab/test/{attempt}/yuborish', [TalabaMiniMaktabController::class, 'yuborish'])->name('talaba.mini_maktab.yuborish');
    Route::get('/mini-maktab/natija/{attempt}', [TalabaMiniMaktabController::class, 'natija'])->name('talaba.mini_maktab.natija');
});

Route::middleware(['auth'])->prefix('chat')->group(function () {
    Route::get('/', [StudentChatController::class, 'index'])->name('chat');
    Route::get('/qidiruv', [StudentChatController::class, 'searchUsers'])->name('chat.search');
    Route::get('/overview-poll', [StudentChatController::class, 'pollOverview'])->name('chat.poll.overview');

    // Bo'lim (admin) bilan chat
    Route::get('/bolim/{section}', [StudentChatController::class, 'section'])->name('chat.section');
    Route::post('/bolim/{section}/send', [StudentChatController::class, 'sendToSection'])->name('chat.section.send');
    Route::get('/bolim/{section}/poll', [StudentChatController::class, 'pollSection'])->name('chat.section.poll');

    // Talaba <-> talaba chat
    Route::get('/foydalanuvchi/{user}', [StudentChatController::class, 'userChat'])->name('chat.user');
    Route::post('/foydalanuvchi/{user}/send', [StudentChatController::class, 'sendToUser'])->name('chat.user.send');
    Route::post('/foydalanuvchi/{user}/accept', [StudentChatController::class, 'acceptUser'])->name('chat.user.accept');
    Route::get('/foydalanuvchi/{user}/poll', [StudentChatController::class, 'pollUser'])->name('chat.user.poll');
});

/*
|--------------------------------------------------------------------------
| ADMIN CHAT (eski /admin_chat route'i o'rniga to'liq funksional versiya)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin_chat')->group(function () {
    Route::get('/', [AdminChatController::class, 'index'])->name('admin_chat');

    Route::get('/bolim/{section}', [AdminChatController::class, 'section'])->name('admin_chat.section');
    Route::get('/bolim/{section}/qidiruv', [AdminChatController::class, 'searchStudents'])->name('admin_chat.search');
    Route::get('/bolim/{section}/overview-poll', [AdminChatController::class, 'pollOverview'])->name('admin_chat.poll.overview');

    Route::get('/bolim/{section}/talaba/{student}', [AdminChatController::class, 'conversation'])->name('admin_chat.conversation');
    Route::post('/bolim/{section}/talaba/{student}/send', [AdminChatController::class, 'send'])->name('admin_chat.send');
    Route::get('/bolim/{section}/talaba/{student}/poll', [AdminChatController::class, 'poll'])->name('admin_chat.poll');
});

/*
|--------------------------------------------------------------------------
| ADMIN PANEL: bo'limlar va adminlarni biriktirish
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin/sections')->name('admin.sections.')->group(function () {
    Route::get('/', [AdminSectionController::class, 'index'])->name('index');
    Route::post('/', [AdminSectionController::class, 'store'])->name('store');
    Route::put('/{section}', [AdminSectionController::class, 'update'])->name('update');
    Route::delete('/{section}', [AdminSectionController::class, 'destroy'])->name('destroy');
    Route::post('/{section}/assign', [AdminSectionController::class, 'assignAdmins'])->name('assign');
});
