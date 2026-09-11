<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TeacherController extends Controller
{
    
    public function index()
    {

        $teachers = User::where('role', 'teacher')->paginate(50);

        return view('teacher.index')->with('teachers', $teachers);
    }

    
    public function create()
    {
        return view('teacher.create');
    }

    
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

    
    public function import()
    {
        return view('teacher.import');
    }

    
    public function importStore(Request $request)
    {


        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $path = $request->file('file')->getRealPath();

        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $header = array_shift($rows);

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

        $incoming = [];

        foreach ($rows as $row) {
            $fullName = $nameColumn ? trim((string) ($row[$nameColumn] ?? '')) : null;
            $email    = $emailColumn ? trim((string) ($row[$emailColumn] ?? '')) : null;

            if (empty($fullName) || empty($email)) {
                continue;
            }

            $incoming[$email] = $fullName;
        }

        $skipped = count($rows) - count($incoming);


        $existingEmails = User::whereIn('email', array_keys($incoming))
            ->pluck('id', 'email');

        $now = now();
        $insertRows = [];
        $created = 0;
        $updated = 0;

        foreach ($incoming as $email => $fullName) {
            if ($existingEmails->has($email)) {

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



        foreach (array_chunk($insertRows, 200) as $chunk) {
            User::insert($chunk);
        }

        return redirect()->route('teacher.index')->with(
            'success',
            "Import yakunlandi: {$created} ta yangi, {$updated} ta yangilandi, {$skipped} ta o'tkazib yuborildi."
        );
    }

    
    public function show(string $id)
    {

    }

    
    public function edit(User $teacher)
    {
        return view('teacher.edit')->with('teacher', $teacher);
    }

    
    public function update(Request $request, User $teacher)
    {

        $validated = $request->validate([
            'To‘liq_ismi' => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $teacher->id,
            'password'   => 'nullable|min:8',
            'photo'      => 'nullable|image|max:2048',
        ]);

        $teacher->To‘liq_ismi = $request->input('To‘liq_ismi');
        $teacher->email = $request->input('email');

        if ($request->filled('password')) {
            $teacher->password = bcrypt($request->input('password'));
        }

        if ($request->hasFile('photo')) {
            $teacher->photo = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->save();

        return redirect()->route('teacher.index')->with('success', 'Ma’lumotlar yangilandi');
    }

    
    public function destroy(User $teacher)
    {
        $teacher->delete();
        
        return redirect('teacher.index')->with('success', 'O\'qituvchi ma\'lumotlari o\'chirildi');
    }
}
