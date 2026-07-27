<?php

namespace App\Http\Controllers;

use App\Models\fakultet;
use Illuminate\Http\Request;

class FakultetController extends Controller
{
    /**
     * Store a newly created fakultet in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomi' => 'required|string|max:255|unique:fakultet,nomi',
        ]);

        fakultet::create([
            'nomi' => $request->input('nomi'),
        ]);

        return redirect()->back()->with('success', 'Fakultet muvaffaqiyatli qo\'shildi.');
    }

    /**
     * Update the specified fakultet in storage.
     */
    public function update(Request $request, fakultet $fakultet)
    {
        $request->validate([
            'nomi' => 'required|string|max:255|unique:fakultets,nomi,' . $fakultet->id,
        ]);

        $fakultet->update([
            'nomi' => $request->input('nomi'),
        ]);

        return redirect()->back()->with('success', 'Fakultet muvaffaqiyatli yangilandi.');
    }

    /**
     * Remove the specified fakultet from storage.
     */
    public function destroy(fakultet $fakultet)
    {
        $fakultet->delete();

        return redirect()->back()->with('success', 'Fakultet muvaffaqiyatli o\'chirildi.');
    }
}