<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use Illuminate\Http\Request;
use App\Models\category;

class CategoryController extends Controller
{
    
    public function index()
    {
        $categories = category::paginate(100);

        return view('category.index')->with('categories', $categories);
    }

    
    public function create()
    {
        $categories = category::paginate(100);
        return view('category.create')->with('categories', $categories);
    }

    
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

    
    public function show(string $id)
    {

    }

    
    public function edit(category $category)
    {
        return view('category.edit')->with('category', $category);
    }

    
    public function update(StoreCategoryRequest $request, category $category)
    {
        $category->update([
            'nomi' => $request->input('nomi'),
            'guruh' => $request->input('guruh')
        ]);

        \App\Models\User::where('Guruh', 'LIKE', $request->guruh . '-%')
            ->update(['category_id' => $category->id]);

        return redirect()->route('category.create')->with('success', 'Yo\'nalish yangilandi.');
    }

    
    public function destroy(category $category)
    {
        $category->delete();

        return redirect()->route('category.create')->with('success', "Yo'nalish o'chirildi");
    }
}