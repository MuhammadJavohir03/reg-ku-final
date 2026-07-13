<?php

namespace App\Http\Controllers;

use App\Models\subject;
use App\Models\category;
use Illuminate\Http\Request;
use App\Models\bolim;

class BepulFanlarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($bolim_id)
    {
        // 1. Bosilgan bo'limni topamiz
        $bolim = bolim::findOrFail($bolim_id);

        // 2. Shu bo'limga tegishli barcha fanlarni olamiz
        $subjects = subject::with('category', 'teacher')->get();

        // 3. Ikkalasini ham Blade-ga berib yuboramiz (Mana shu yerda sir yashingan!)
        return view('bepul_semestr.fanlar.index', compact('subjects', 'bolim'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($bepul_semestr, $fanlar)
    {
        // $bepul_semestr ichida $bolim->id keladi
        // $fanlar ichida $subject->id keladi

        $bolim = bolim::findOrFail($bepul_semestr);
        $subject = subject::findOrFail($fanlar);

        return view('bepul_semestr.fanlar.show', compact('bolim', 'subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
