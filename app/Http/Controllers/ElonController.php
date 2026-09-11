<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreElonRequest;
use App\Models\Category;
use App\Models\Elon;
use Illuminate\Support\Facades\Storage;

class ElonController extends Controller
{
    
    private const DEFAULT_PHOTO = 'elons/default.png';

    public function index()
    {
        $user = auth()->user();
        $query = Elon::with('admin', 'category');

        if ($user) {

            if ($user->role !== 'admin') {
                $query->where(function ($q) use ($user) {

                    $q->orWhere(function ($sub) {
                        $sub->whereNull('category_id')
                            ->whereNull('kurs');
                    });

                    $q->orWhere(function ($sub) use ($user) {
                        $sub->where('category_id', $user->category_id)
                            ->whereNull('kurs');
                    });

                    $q->orWhere(function ($sub) use ($user) {
                        $sub->whereNull('category_id')
                            ->where('kurs', $user->Kurs);
                    });

                    $q->orWhere(function ($sub) use ($user) {
                        $sub->where('category_id', $user->category_id)
                            ->where('kurs', $user->Kurs);
                    });
                });
            }
        } else {

            $query->whereNull('category_id')
                ->whereNull('kurs');
        }

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('short_content', 'like', "%{$search}%");
            });
        }

        if ($categoryId = request('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($kurs = request('kurs')) {
            $query->where('kurs', $kurs);
        }

        $elons = $query->latest()
            ->paginate(9)
            ->withQueryString();

        $categories = Category::all();

        return view('elons.index', compact('elons', 'categories'));
    }

    
    public function create()
    {
        $categories = Category::all();

        return view('elons.create', compact('categories'));
    }

    
    public function store(StoreElonRequest $request)
    {
        $path = self::DEFAULT_PHOTO;

        if ($request->hasFile('photo')) {
            $name = time() . '_' . $request->file('photo')->getClientOriginalName();
            $path = $request->file('photo')->storeAs('elons', $name, 'public');
        }

        Elon::create([
            'admin_id' => $request->user()->id,
            'title' => $request->input('title'),
            'short_content' => $request->input('short_content'),
            'full_content' => $request->input('full_content'),
            'category_id' => $request->input('category_id') ?: null,
            'photo' => $path,
            'kurs' => $request->input('kurs') ?: null,
        ]);

        return redirect()->route('elons.index')
            ->with('success', 'E\'lon muvaffaqiyatli yaratildi.');
    }

    
    public function show(Elon $elon)
    {
        $students = collect();


        if (auth()->check() && auth()->user()->role === 'admin' && ($elon->category_id || $elon->kurs)) {
            $studentsQuery = \App\Models\User::where('role', '!=', 'admin');

            if ($elon->category_id && $elon->kurs) {

                $studentsQuery->where('category_id', $elon->category_id)
                    ->where('Kurs', $elon->kurs);
            } elseif ($elon->category_id) {

                $studentsQuery->where('category_id', $elon->category_id);
            } elseif ($elon->kurs) {

                $studentsQuery->where('Kurs', $elon->kurs);
            }

            $students = $studentsQuery->get();
        }

        return view('elons.show', compact('elon', 'students'));
    }

    
    public function edit(Elon $elon)
    {
        $categories = Category::all();

        return view('elons.edit', compact('elon', 'categories'));
    }

    
    public function update(StoreElonRequest $request, Elon $elon)
    {
        $path = $elon->photo;

        if ($request->hasFile('photo')) {

            $name = time() . '_' . $request->file('photo')->getClientOriginalName();
            $path = $request->file('photo')->storeAs('elons', $name, 'public');

            $this->deletePhotoIfNotDefault($elon->photo);
        } elseif ($request->boolean('remove_photo')) {

            $this->deletePhotoIfNotDefault($elon->photo);
            $path = self::DEFAULT_PHOTO;
        }

        $elon->update([
            'title' => $request->input('title'),
            'short_content' => $request->input('short_content'),
            'full_content' => $request->input('full_content'),
            'category_id' => $request->input('category_id') ?: null,
            'kurs' => $request->input('kurs') ?: null,
            'photo' => $path,
        ]);

        return redirect()->route('elons.show', $elon->id)
            ->with('success', 'E\'lon muvaffaqiyatli yangilandi.');
    }

    
    public function destroy(Elon $elon)
    {
        $this->deletePhotoIfNotDefault($elon->photo);

        $elon->delete();

        return redirect()->route('elons.index')
            ->with('success', 'E\'lon muvaffaqiyatli o\'chirildi.');
    }

    
    private function deletePhotoIfNotDefault(?string $photo): void
    {
        if (!empty($photo) && $photo !== self::DEFAULT_PHOTO) {
            Storage::disk('public')->delete($photo);
        }
    }
}