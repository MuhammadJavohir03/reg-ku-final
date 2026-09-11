<?php

namespace App\Http\Controllers;

use App\Models\subject;
use App\Models\category;
use Illuminate\Http\Request;
use App\Models\bolim;

class BepulFanlarController extends Controller
{
    
    public function index($bolim_id)
    {

        $bolim = bolim::findOrFail($bolim_id);

        $subjects = subject::with('category', 'teacher')->get();

        return view('bepul_semestr.fanlar.index', compact('subjects', 'bolim'));
    }

    
    public function create()
    {

    }

    
    public function store(Request $request)
    {

    }

    
    public function show($bepul_semestr, $fanlar)
    {



        $bolim = bolim::findOrFail($bepul_semestr);
        $subject = subject::findOrFail($fanlar);

        return view('bepul_semestr.fanlar.show', compact('bolim', 'subject'));
    }

    
    public function edit(string $id)
    {

    }

    
    public function update(Request $request, string $id)
    {

    }

    
    public function destroy(string $id)
    {

    }
}
