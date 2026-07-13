<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use Illuminate\Http\Request;
use App\Models\category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = category::paginate(10);

        return view('category.index')->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = category::create([
            'nomi' => $request->input('nomi'),
            'guruh' => $request->input('guruh')
        ]);

        $updatedCount = \App\Models\User::where('Guruh', 'LIKE', $request->guruh . '-%')
            ->update([
                'category_id' => $category->id
            ]);

        return redirect()->route('subject.index')->with('success', 'Yangi yo\'nalish qo\'shildi.');
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
    public function edit(category $category)
    {
        return view('category.edit')->with('category', $category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCategoryRequest $request, category $category)
    {
        $category->update([
            'nomi' => $request->input('nomi'),
            'guruh' => $request->input('guruh')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(category $category)
{
    $category->delete();

    // route() funksiyasini qo'shing va index sahifasiga qaytaring
    return redirect()->route('category.index')->with('success', "Yo'nalish o'chirildi");
}
}
