<?php

namespace App\Http\Controllers;

use App\Models\kafedra;
use Illuminate\Http\Request;

class KafedraController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'nomi' => 'required|string|max:255|unique:kafedra,nomi',
            'fakultet_id' => 'required|exists:fakultet,id',
        ]);

        kafedra::create([
            'nomi' => $request->input('nomi'),
            'fakultet_id' => $request->input('fakultet_id'),
        ]);

        return redirect()->back()->with('success', 'Kafedra muvaffaqiyatli qo\'shildi.');
    }

    
    public function update(Request $request, kafedra $kafedra)
    {
        $request->validate([
            'nomi' => 'required|string|max:255|unique:kafedras,nomi,' . $kafedra->id,
            'fakultet_id' => 'required|exists:fakultet,id',
        ]);

        $kafedra->update([
            'nomi' => $request->input('nomi'),
            'fakultet_id' => $request->input('fakultet_id'),
        ]);

        return redirect()->back()->with('success', 'Kafedra muvaffaqiyatli yangilandi.');
    }

    
    public function destroy(kafedra $kafedra)
    {
        $kafedra->delete();

        return redirect()->back()->with('success', 'Kafedra muvaffaqiyatli o\'chirildi.');
    }
}