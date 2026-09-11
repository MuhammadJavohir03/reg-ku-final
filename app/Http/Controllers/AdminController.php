<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    
    public function index()
    {
        $admins = User::where('role', 'admin')->paginate(50);

        return view('admins.index')->with('admins', $admins);
    }

    
    public function create()
    {
        return view('admins.create');
    }

    
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

    
    public function show(string $id)
    {

    }

    
    public function edit(User $admin)
    {
        return view('admins.edit')->with('admin', $admin);
    }

    
    public function update(Request $request, User $admin)
    {
        $validated = $request->validate([
            'To‘liq_ismi' => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $admin->id,
            'password'   => 'nullable|min:8',
            'photo'      => 'nullable|image|max:2048',
        ]);

        $admin->To‘liq_ismi = $request->input('To‘liq_ismi');
        $admin->email = $request->input('email');

        if ($request->filled('password')) {
            $admin->password = bcrypt($request->input('password'));
        }
        if ($request->hasFile('photo')) {
            $admin->photo = $request->file('photo')->store('admins', 'public');
        }

        $admin->save();

        return redirect()->route('admins.index')->with('success', 'Admin ma’lumotlar yangilandi');
    }

    
    public function destroy(User $admin)
    {
        $admin->delete();


        return redirect()->route('admins.index')->with('success', 'Admin muvaffaqiyatli o\'chirildi.');
    }
}
