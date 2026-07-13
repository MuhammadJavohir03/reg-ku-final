<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SectionController extends Controller
{
    /** Bo'limlar ro'yxati + har biriga biriktirilgan adminlar */
    public function index()
    {
        $sections = Section::with('admins')->orderBy('name')->get();
        $admins = User::where('role', 'admin')->orderBy('To‘liq_ismi')->get(['id', 'To‘liq_ismi', 'email']);

        return view('admin.sections.index', compact('sections', 'admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Section::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name).'-'.Str::random(4),
        ]);

        return back()->with('success', 'Bo\'lim qo\'shildi.');
    }

    public function update(Request $request, Section $section)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $section->update(['name' => $request->name]);

        return back()->with('success', 'Bo\'lim yangilandi.');
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return back()->with('success', 'Bo\'lim o\'chirildi.');
    }

    /**
     * Bitta bo'limga qaysi adminlar biriktirilganini yangilash (sync).
     * Kelayotgan admin_ids massivi bilan to'liq almashtiradi.
     */
    public function assignAdmins(Request $request, Section $section)
    {
        $request->validate([
            'admin_ids' => 'array',
            'admin_ids.*' => 'exists:users,id',
        ]);

        $section->admins()->sync($request->input('admin_ids', []));

        return back()->with('success', 'Adminlar bo\'limga biriktirildi.');
    }
}
