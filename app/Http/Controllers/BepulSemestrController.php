<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBepulSemestrRequest;
use App\Models\BepulSemestr;
use Illuminate\Http\Request;

class BepulSemestrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $bepul_semestr = \App\Models\BepulSemestr::when($search, function ($query, $search) {
            return $query->where('nomi', 'like', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate(request('page_size', 20))
            ->withQueryString();

        return view('bepul_semestr.index', compact('bepul_semestr'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bepul_semestr.create_bolim');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Avval bazadagi barcha mavjud bo'limlarni statusini 0 qilamiz
        // Chunki yangisi baribir Active (1) bo'ladi
        \App\Models\BepulSemestr::query()->update(['status' => 0]);

        // 2. Yangi bo'limni yaratamiz va statusni kod ichida MAJBURIY 1 qilamiz
        \App\Models\BepulSemestr::create([
            'nomi'   => $request->input('nomi'),
            'status' => 1, // Formadan kelishi shart emas, o'zimiz 1 beramiz
        ]);

        return redirect()->route('bepul_semestr.index')->with('success', 'Yangi faol bo\'lim yaratildi!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BepulSemestr $bepul_semestr) // $bolim emas!
    {
        return view('bepul_semestr.edit')->with('bepul_semestr', $bepul_semestr);
    }

    public function update(Request $request, $id) // Model binding o'rniga $id ishlatib ko'ramiz
    {
        $bolim = \App\Models\BepulSemestr::findOrFail($id);

        $yangiStatus = $request->input('status');

        if ($yangiStatus == 1) {
            // Model eventlariga ishonmasdan, shu yerning o'zida hammasini 0 qilamiz
            \App\Models\BepulSemestr::where('id', '!=', $id)->update(['status' => 0]);
        }

        $bolim->update([
            'nomi' => $request->input('nomi'),
            'status' => $yangiStatus
        ]);

        return redirect()->route('bepul_semestr.index')->with('success', 'Yangilandi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BepulSemestr $bepul_semestr)
    {
        $bepul_semestr->delete();

        return redirect('bepul_semestr');
    }
}
