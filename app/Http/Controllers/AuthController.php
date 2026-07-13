<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        $userCounts = [
            'admin' => \App\Models\User::where('role', 'admin')->count(),
            'teacher' => \App\Models\User::where('role', 'teacher')->count(),
            'talaba' => \App\Models\User::where('role', 'talaba')->count(),
        ];

        $subjectCounts = [
            'subject' => \App\Models\Subject::count(),
        ];
        return view('login.login', compact('userCounts', 'subjectCounts'));
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'=> ['required', 'email'],
            'password' => ['required']
        ]);

        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            return redirect('/');
        }
        return back()->withErrors([
            'email' => 'Tizimga kirishda xatolik yuz berdi.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
