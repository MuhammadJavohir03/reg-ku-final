<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Models\User;
use Illuminate\Http\Request;

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
