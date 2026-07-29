<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // paginate(10) — har sahifada 10 tadan o'qituvchi ko'rsatadi
        $teachers = User::where('role', 'teacher')->paginate(50);

        return view('teacher.index')->with('teachers', $teachers);
    }

    /**
     * Show the form for creating a new resource.
     **/
    public function create()
    {
        return view('teacher.create');
    }

    /**
     * Store a newly created resource in storage.
     **/
    public function store(StoreTeacherRequest $request)
    {
        $teacher = User::create([
            'role'        => 'teacher',
            'To‘liq_ismi' => $request->input('To‘liq_ismi'),
            'email'       => $request->input('email'),
            'password'    => bcrypt($request->input('password')),
            'photo'       => $request->input('photo'),

        ]);

        return redirect()->route('teacher.index');
    }

    /**
     * Show the form for importing teachers from an Excel (.xlsx) file.
     */
    public function import()
    {
        return view('teacher.import');
    }

    /**
     * Handle the uploaded Excel file: read "FISh" (To'liq ismi) and
     * "Elektron pochta" (email) columns and create teacher accounts.
     * Har bir yangi o'qituvchining paroli: reg1234567
     */
    public function importStore(Request $request)
    {
        // Katta fayllarni o'qish vaqt talab qilishi mumkin,
        // shuning uchun shu so'rov uchun limitni ko'taramiz
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $path = $request->file('file')->getRealPath();

        // Faqat qiymatlarni o'qiymiz (formatlash/stillarni emas) — tezroq ishlaydi
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        // Birinchi qator - sarlavhalar (header), shuning uchun uni o'tkazib yuboramiz
        $header = array_shift($rows);

        // Ustunlarni nomi bo'yicha topamiz (A, B, C, ... harflari)
        $nameColumn  = null;
        $emailColumn = null;

        foreach ($header as $col => $title) {
            $title = trim((string) $title);

            if ($title === 'FISh') {
                $nameColumn = $col;
            }

            if ($title === 'Elektron pochta') {
                $emailColumn = $col;
            }
        }

        // Faylda kelayotgan barcha email'larni to'playmiz
        $incoming = [];

        foreach ($rows as $row) {
            $fullName = $nameColumn ? trim((string) ($row[$nameColumn] ?? '')) : null;
            $email    = $emailColumn ? trim((string) ($row[$emailColumn] ?? '')) : null;

            if (empty($fullName) || empty($email)) {
                continue;
            }

            // Bir xil email fayl ichida qaytarilsa, oxirgisi qoladi
            $incoming[$email] = $fullName;
        }

        $skipped = count($rows) - count($incoming);

        // Mavjud foydalanuvchilarni BITTA so'rov bilan olib kelamiz
        // (har qator uchun alohida so'rov yubormaslik uchun)
        $existingEmails = User::whereIn('email', array_keys($incoming))
            ->pluck('id', 'email');

        $now = now();
        $insertRows = [];
        $created = 0;
        $updated = 0;

        foreach ($incoming as $email => $fullName) {
            if ($existingEmails->has($email)) {
                // Faqat ismini/rolini yangilaymiz — parolga tegmaymiz
                User::where('id', $existingEmails[$email])->update([
                    'To‘liq_ismi' => $fullName,
                    'role'        => 'teacher',
                    'updated_at'  => $now,
                ]);
                $updated++;
                continue;
            }

            $insertRows[] = [
                'role'        => 'teacher',
                'To‘liq_ismi' => $fullName,
                'email'       => $email,
                'password'    => bcrypt('reg1234567'),
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
            $created++;
        }

        // Yangi foydalanuvchilarni 200 talik bo'laklarga bo'lib, bitta-bitta
        // emas, bo'lak-bo'lak qilib qo'shamiz — bu bazaga yuzlab alohida
        // so'rov yuborishning oldini oladi
        foreach (array_chunk($insertRows, 200) as $chunk) {
            User::insert($chunk);
        }

        return redirect()->route('teacher.index')->with(
            'success',
            "Import yakunlandi: {$created} ta yangi, {$updated} ta yangilandi, {$skipped} ta o'tkazib yuborildi."
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $teacher)
    {
        return view('teacher.edit')->with('teacher', $teacher);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $teacher)
    {
        // Validatsiya (Update uchun alohida Request yoki shunchaki Request ishlatsa bo'ladi)
        $validated = $request->validate([
            'To‘liq_ismi' => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $teacher->id, // O'zining emailini hisobga olmaydi
            'password'   => 'nullable|min:8', // Parol bo'sh bo'lishi mumkin
            'photo'      => 'nullable|image|max:2048',
        ]);

        // Asosiy ma'lumotlarni yangilash
        $teacher->To‘liq_ismi = $request->input('To‘liq_ismi');
        $teacher->email = $request->input('email');

        // Agar parol kiritilgan bo'lsagina yangilaymiz
        if ($request->filled('password')) {
            $teacher->password = bcrypt($request->input('password'));
        }

        // Rasm yuklangan bo'lsa
        if ($request->hasFile('photo')) {
            $teacher->photo = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->save(); // create emas, save ishlatiladi!

        return redirect()->route('teacher.index')->with('success', 'Ma’lumotlar yangilandi');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $teacher)
    {
        $teacher->delete();
        
        return redirect('teacher.index')->with('success', 'O\'qituvchi ma\'lumotlari o\'chirildi');
    }
}
