<?php

namespace App\Http\Controllers;

use App\Models\kafedra;
use App\Models\fakultet;

class KafedraFakultetController extends Controller
{
    /**
     * Kafedra va Fakultetlarni bitta sahifada ko'rsatadi.
     */
    public function index()
    {
        $kafedralar = kafedra::with('fakultet')->latest()->get();
        $fakultetlar = fakultet::latest()->get();

        return view('kafedra_fakultet.index', compact('kafedralar', 'fakultetlar'));
    }
}