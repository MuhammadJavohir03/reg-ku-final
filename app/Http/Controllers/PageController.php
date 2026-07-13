<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function ozlashtirish()
    {
        return view('ozlashtirish');
    }

    public function fanlar()
    {
        return view('fanlar.index');
    }

    public function umumiyNatijalar()
    {
        return view('umumiy_natijalar');
    }

    public function chat()
    {
        return view('chat');
    }

    public function adminChat()
    {
        return view('admin_chat');
    }

    public function elonYaratish()
    {
        return view('elon_yaratish');
    }
}
