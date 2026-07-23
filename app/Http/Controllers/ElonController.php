<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreElonRequest;
use App\Models\Category;
use App\Models\Elon;
use Illuminate\Support\Facades\Storage;

class ElonController extends Controller
{
    /**
     * Rasm yuklanmagan holatlarda ishlatiladigan standart rasm yo'li.
     * Bu faylni HECH QACHON storage'dan o'chirmaslik kerak,
     * chunki u bir nechta e'lon tomonidan umumiy ishlatiladi.
     */
    private const DEFAULT_PHOTO = 'elons/default.png';

    public function index()
    {
        $user = auth()->user();
        $query = Elon::with('admin', 'category');

        // 1. Agar foydalanuvchi tizimga kirgan bo'lsa
        if ($user) {
            // Faqat admin bo'lmaganlar uchun filtrni qo'llaymiz
            if ($user->role !== 'admin') {
                $query->where(function ($q) use ($user) {
                    // 1. Hammaga mo'ljallangan (category ham, kurs ham bo'sh)
                    $q->orWhere(function ($sub) {
                        $sub->whereNull('category_id')
                            ->whereNull('kurs');
                    });

                    // 2. Faqat category tanlangan (kurs bo'sh) -> shu categoriyadagi HAR QANDAY kursga
                    $q->orWhere(function ($sub) use ($user) {
                        $sub->where('category_id', $user->category_id)
                            ->whereNull('kurs');
                    });

                    // 3. Faqat kurs tanlangan (category bo'sh) -> shu kursdagi HAR QANDAY yo'nalishga
                    $q->orWhere(function ($sub) use ($user) {
                        $sub->whereNull('category_id')
                            ->where('kurs', $user->Kurs);
                    });

                    // 4. Ikkalasi ham tanlangan -> aniq mos kelishi kerak
                    $q->orWhere(function ($sub) use ($user) {
                        $sub->where('category_id', $user->category_id)
                            ->where('kurs', $user->Kurs);
                    });
                });
            }
        } else {
            // Mehmon (login qilmagan) -> faqat hammaga mo'ljallangan e'lonlar
            $query->whereNull('category_id')
                ->whereNull('kurs');
        }

        $elons = $query->latest()->paginate(9);

        return view('elons.index', compact('elons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return view('elons.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(Elon $elon)
    {
        return view('elons.show', compact('elon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Elon $elon)
    {
        $categories = Category::all();

        return view('elons.edit', compact('elon', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreElonRequest $request, Elon $elon)
    {
        $path = $elon->photo; // Rasm o'zgarmasa, eski yo'l saqlanib qoladi

        if ($request->hasFile('photo')) {
            // Avval yangi faylni saqlaymiz
            $name = time() . '_' . $request->file('photo')->getClientOriginalName();
            $path = $request->file('photo')->storeAs('elons', $name, 'public');

            // Endi eski faylni o'chiramiz — lekin faqat u default rasm bo'lmasa
            $this->deletePhotoIfNotDefault($elon->photo);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Elon $elon)
    {
        $this->deletePhotoIfNotDefault($elon->photo);

        $elon->delete();

        return redirect()->route('elons.index')
            ->with('success', 'E\'lon muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Rasmni storage'dan xavfsiz o'chirish.
     * - $elon->photo bazada ALLAQACHON to'liq yo'l ('elons/xxx.png') sifatida
     *   saqlangani uchun oldiga yana 'elons/' qo'shilmaydi.
     * - Standart (umumiy) rasm hech qachon o'chirilmaydi.
     */
    private function deletePhotoIfNotDefault(?string $photo): void
    {
        if (!empty($photo) && $photo !== self::DEFAULT_PHOTO) {
            Storage::disk('public')->delete($photo);
        }
    }
}
