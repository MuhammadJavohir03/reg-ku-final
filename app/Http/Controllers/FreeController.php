<?php

namespace App\Http\Controllers;

use App\Models\free_semestr;
use App\Models\User;
use App\Models\subject;
use App\Models\bolim;
use App\Models\grade;
use Illuminate\Http\Request;

class FreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $talabalar = free_semestr::with(['user', 'subject'])
            ->when($request->search, function ($query) use ($request) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where("To‘liq_ismi", 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('Guruh', 'like', '%' . $request->search . '%');
                })
                    ->orWhereHas('subject', function ($q) use ($request) {
                        $q->where('nomi', 'like', '%' . $request->search . '%');
                    });
            })
            ->paginate(100);

        // each() o'rniga map ishlatamiz — Paginator saqlanib qoladi
        $talabalar->getCollection()->transform(function ($talaba) {
            $talaba->grade = grade::where('user_id', $talaba->user_id)
                ->where('subject_id', $talaba->subject_id)
                ->first();
            return $talaba;
        });

        return view('free_semestr.index', compact('talabalar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
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
    public function show(string $id)
    {
        //
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
    public function destroy($id)
    {
        free_semestr::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Ariza o\'chirildi!');
    }
}
