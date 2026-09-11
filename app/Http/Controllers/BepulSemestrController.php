<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBepulSemestrRequest;
use App\Models\BepulSemestr;
use Illuminate\Http\Request;

class BepulSemestrController extends Controller
{
    
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

    
    public function create()
    {
        return view('bepul_semestr.create_bolim');
    }

    
    public function store(Request $request)
    {


        \App\Models\BepulSemestr::query()->update(['status' => 0]);

        \App\Models\BepulSemestr::create([
            'nomi'   => $request->input('nomi'),
            'status' => 1,
        ]);

        return redirect()->route('bepul_semestr.index')->with('success', 'Yangi faol bo\'lim yaratildi!');
    }

    
    public function show(string $id)
    {

    }

    
    public function edit(BepulSemestr $bepul_semestr)
    {
        return view('bepul_semestr.edit')->with('bepul_semestr', $bepul_semestr);
    }

    public function update(Request $request, $id)
    {
        $bolim = \App\Models\BepulSemestr::findOrFail($id);

        $yangiStatus = $request->input('status');

        if ($yangiStatus == 1) {

            \App\Models\BepulSemestr::where('id', '!=', $id)->update(['status' => 0]);
        }

        $bolim->update([
            'nomi' => $request->input('nomi'),
            'status' => $yangiStatus
        ]);

        return redirect()->route('bepul_semestr.index')->with('success', 'Yangilandi!');
    }

    
    public function destroy(BepulSemestr $bepul_semestr)
    {
        $bepul_semestr->delete();

        return redirect('bepul_semestr');
    }
}
