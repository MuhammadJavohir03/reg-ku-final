<?php

namespace App\Http\Controllers;

use App\Models\Mudir;
use App\Models\kafedra;
use App\Models\OquvYili;
use Illuminate\Http\Request;

class MudirController extends Controller
{
    
    public function index()
    {
        $search = request('search');

        $mudirlar = Mudir::with(['kafedra', 'oquvYili'])
            ->when($search, function ($query, $search) {
                $query->where('mudir', 'like', "%{$search}%")
                    ->orWhereHas('kafedra', function ($q) use ($search) {
                        $q->where('nomi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('oquvYili', function ($q) use ($search) {
                        $q->where('nomi', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('mudir.index', compact('mudirlar'));
    }

    
    public function create()
    {
        $kafedralar = kafedra::orderBy('nomi')->get();
        $oquv_yillari = OquvYili::orderBy('nomi')->get();

        return view('mudir.create', compact('kafedralar', 'oquv_yillari'));
    }

    
    public function store(Request $request)
    {
        $data = $request->validate([
            'mudir'        => 'required|string|max:255',
            'kafedra_id'   => 'required|exists:kafedra,id',
            'oquv_yili_id' => 'required|exists:oquv_yili,id',
        ]);

        $exists = Mudir::where('kafedra_id', $data['kafedra_id'])
            ->where('oquv_yili_id', $data['oquv_yili_id'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', "Bu kafedra va o'quv yili uchun mudir allaqachon kiritilgan. Uni ro'yxatdan tahrirlang.");
        }

        Mudir::create($data);

        return redirect()->route('mudir.index')->with('success', 'Mudir muvaffaqiyatli qo\'shildi.');
    }

    
    public function edit(Mudir $mudir)
    {
        $kafedralar = kafedra::orderBy('nomi')->get();
        $oquv_yillari = OquvYili::orderBy('nomi')->get();

        return view('mudir.edit', compact('mudir', 'kafedralar', 'oquv_yillari'));
    }

    
    public function update(Request $request, Mudir $mudir)
    {
        $data = $request->validate([
            'mudir'        => 'required|string|max:255',
            'kafedra_id'   => 'required|exists:kafedra,id',
            'oquv_yili_id' => 'required|exists:oquv_yili,id',
        ]);

        $exists = Mudir::where('kafedra_id', $data['kafedra_id'])
            ->where('oquv_yili_id', $data['oquv_yili_id'])
            ->where('id', '!=', $mudir->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', "Bu kafedra va o'quv yili uchun boshqa mudir allaqachon kiritilgan.");
        }

        $mudir->update($data);

        return redirect()->route('mudir.index')->with('success', 'Mudir muvaffaqiyatli yangilandi.');
    }

    
    public function destroy(Mudir $mudir)
    {
        $mudir->delete();

        return redirect()->route('mudir.index')->with('success', 'Mudir muvaffaqiyatli o\'chirildi.');
    }
}