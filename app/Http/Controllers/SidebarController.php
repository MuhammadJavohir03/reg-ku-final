<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SidebarController extends Controller
{
    public function index()
    {
        $settings = [
            'free_semestr' => Cache::get('sidebar_free_semestr', true),
            'mini_semestr' => Cache::get('sidebar_mini_semestr', true),
        ];
        return view('sidebar_boshqaruv', compact('settings'));
    }

    public function toggle($key)
    {
        $current = Cache::get('sidebar_' . $key, true);
        Cache::forever('sidebar_' . $key, !$current);
        return redirect()->back()->with('success', 'O\'zgartirildi!');
    }
}
