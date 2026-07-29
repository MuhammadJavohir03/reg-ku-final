<?php

namespace App\Http\Controllers;

use App\Models\OquvYili;
use Illuminate\Http\Request;

class OquvYiliController extends Controller
{
    public function index()
    {
        $oquv_yillari = OquvYili::orderBy('created_at', 'desc')->get();

        return view('oquv_yili.index', compact('oquv_yillari'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomi' => ['required', 'string', 'max:255'],
        ]);

        OquvYili::create([
            'nomi' => $request->nomi,
        ]);

        return redirect()->route('oquv_yili.index')
            ->with('success', "O'quv yili muvaffaqiyatli qo'shildi.");
    }

    public function update(Request $request, OquvYili $oquv_yili)
    {
        $request->validate([
            'nomi' => ['required', 'string', 'max:255'],
        ]);

        $oquv_yili->update([
            'nomi' => $request->nomi,
        ]);

        return redirect()->route('oquv_yili.index')
            ->with('success', "O'quv yili muvaffaqiyatli yangilandi.");
    }

    public function destroy(OquvYili $oquv_yili)
    {
        $oquv_yili->delete();

        return redirect()->route('oquv_yili.index')
            ->with('success', "O'quv yili muvaffaqiyatli o'chirildi.");
    }
}