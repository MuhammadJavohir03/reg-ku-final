<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')->paginate(50);

        return view('admins.index')->with('admins', $admins);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminRequest $request)
    {
        $admin = User::create([
            'role'        => 'admin',
            'To‘liq_ismi' => $request->input('To‘liq_ismi'),
            'email'       => $request->input('email'),
            'password'    => bcrypt($request->input('password')),
            'photo'       => $request->input('photo'),

        ]);
        return redirect()->route('admins.index');
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
    public function edit(User $admin)
    {
        return view('admins.edit')->with('admin', $admin);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $admin)
    {
        // Validatsiya (Update uchun alohida Request yoki shunchaki Request ishlatsa bo'ladi)
        $validated = $request->validate([
            'To‘liq_ismi' => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $admin->id, // O'zining emailini hisobga olmaydi
            'password'   => 'nullable|min:8', // Parol bo'sh bo'lishi mumkin
            'photo'      => 'nullable|image|max:2048',
        ]);

        // Asosiy ma'lumotlarni yangilash
        $admin->To‘liq_ismi = $request->input('To‘liq_ismi');
        $admin->email = $request->input('email');

        // Agar parol kiritilgan bo'lsagina yangilaymiz
        if ($request->filled('password')) {
            $admin->password = bcrypt($request->input('password'));
        }

        // Rasm yuklangan bo'lsa
        if ($request->hasFile('photo')) {
            $admin->photo = $request->file('photo')->store('admins', 'public');
        }

        $admin->save();

        return redirect()->route('admins.index')->with('success', 'Admin ma’lumotlar yangilandi');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin)
    {
        $admin->delete();


        return redirect()->route('admins.index')->with('success', 'Admin muvaffaqiyatli o\'chirildi.');
    }
}
