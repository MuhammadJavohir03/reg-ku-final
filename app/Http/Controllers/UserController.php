<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use App\Imports\StudentsImport;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $joriyYil = (int) date('y');
        $bugun    = date('m-d');

        $students = User::where('Bitiruvchi', '!=', 'Ha')->get();

        foreach ($students as $user) {
            $guruh     = $user->Guruh;
            $guruhYili = (int) substr(strrchr($guruh, "-"), 1);

            if ($guruhYili > 0) {
                $yangiKurs = $joriyYil - $guruhYili;

                if ($bugun >= '09-02') {
                    $yangiKurs++;
                }

                if ($user->Kurs != $yangiKurs || ($yangiKurs > 4 && $user->Bitiruvchi != 'Ha')) {
                    $user->update([
                        'Kurs'       => min(4, $yangiKurs),
                        'Bitiruvchi' => ($yangiKurs > 4) ? 'Ha' : 'Yo\'q',
                    ]);
                }
            }
        }

        $users = User::when($search, function ($query, $search) {
            return $query->where("To‘liq_ismi", 'like', "%{$search}%")
                ->orWhere('Talaba_ID', 'like', "%{$search}%")
                ->orWhere('Guruh', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->orderBy('Kurs', 'asc')
            ->orderBy('category_id', 'asc')
            ->orderBy("To‘liq_ismi", 'asc')
            ->paginate($request->input('page_size', 20))
            ->withQueryString();

        return view('users.users', compact('users'));
    }

    /**
     * Talaba nomidan kirish (impersonation)
     */
    public function loginAs($id)
    {
        // O'zini o'zi impersonation qilishni oldini olish
        if (Auth::id() == $id) {
            return redirect()->back()->with('error', 'O\'zingiz sifatida kira olmaysiz!');
        }

        // Allaqachon impersonation rejimida bo'lsa, asl admin id ni saqlab qolish
        if (!session()->has('impersonator_id')) {
            session(['impersonator_id' => Auth::id()]);
        }

        $user = User::findOrFail($id);
        Auth::login($user);

        return redirect()->route('index')
            ->with('success', 'Siz ' . ($user->{"To‘liq_ismi"} ?? $user->email) . ' nomidan kirdingiz');
    }

    /**
     * Admin hisobiga qaytish
     */
    public function backToAdmin()
    {
        $adminId = session('impersonator_id');

        if ($adminId) {
            Auth::loginUsingId($adminId);
            session()->forget('impersonator_id');
            return redirect()->route('users.index')->with('success', 'Admin hisobiga qaytdingiz');
        }

        return redirect()->route('index');
    }

    /**
     * AJAX: Search teachers by name (role = teacher)
     */
    public function searchTeachers(Request $request)
    {
        $q = $request->input('q');

        try {
            Log::debug('searchTeachers request q=' . $q);

            $teachers = User::where('role', 'teacher')
                ->when($q, function ($query, $q) {
                    return $query->where("To‘liq_ismi", 'like', "%{$q}%");
                })
                ->select('id', "To‘liq_ismi")
                ->limit(15)
                ->get();

            Log::debug('searchTeachers result count=' . $teachers->count());

            return response()->json($teachers);
        } catch (\Exception $e) {
            Log::error('searchTeachers error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage (Excel import).
     */
    public function store(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        ignore_user_abort(true);

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));

            return response()->json([
                'status'  => 'success',
                'message' => '12,350 ta talaba muvaffaqiyatli bazaga yuklandi!',
            ]);
        } catch (\Exception $e) {
            Log::error("Import Xatosi: " . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Xatolik: ' . $e->getMessage(),
            ], 500);
        }
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
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = [
            'email'                         => $request->input('email'),
            'role'                          => $request->input('role'),
            "To‘liq_ismi"                   => $request->input("To‘liq_ismi"),
            'Fuqarolik'                     => $request->input('Fuqarolik'),
            'Davlat'                        => $request->input('Davlat'),
            'Millat'                        => $request->input('Millat'),
            'Viloyat'                       => $request->input('Viloyat'),
            'Tuman'                         => $request->input('Tuman'),
            'Jins'                          => $request->input('Jins'),
            "Tug'ilgan_sana"                => $request->input("Tug'ilgan_sana"),
            'Pasport_raqami'                => $request->input('Pasport_raqami'),
            'JSHSHIR_kod'                   => $request->input('JSHSHIR_kod'),
            'Pasport_berilgan_sana'         => $request->input('Pasport_berilgan_sana'),
            'Kurs'                          => $request->input('Kurs'),
            'Fakultet'                      => $request->input('Fakultet'),
            'Guruh'                         => $request->input('Guruh'),
            'Ta_lim_tili'                   => $request->input('Ta_lim_tili'),
            "O'quv_yili"                    => $request->input("O'quv_yili"),
            'Semestr'                       => $request->input('Semestr'),
            'Bitiruvchi'                    => $request->input('Bitiruvchi'),
            'Mutaxassislik'                 => $request->input('Mutaxassislik'),
            "Ta'lim_turi"                   => $request->input("Ta'lim_turi"),
            "Ta'lim_shakli"                 => $request->input("Ta'lim_shakli"),
            "To'lov_shakli"                 => $request->input("To'lov_shakli"),
            'Grant_turi'                    => $request->input('Grant_turi'),
            'Avvalgi_ta_lim_ma_lumoti'      => $request->input('Avvalgi_ta_lim_ma_lumoti'),
            'Talaba_toifasi'                => $request->input('Talaba_toifasi'),
            'Ijtimoiy_toifa'                => $request->input('Ijtimoiy_toifa'),
            'Birga_yashaydiganlar_soni'     => $request->input('Birga_yashaydiganlar_soni'),
            'Birga_yashaydiganlar_toifasi'  => $request->input('Birga_yashaydiganlar_toifasi'),
            'Yashash_joyi_statusi'          => $request->input('Yashash_joyi_statusi'),
            'Yashash_joyi_geolokatsiyasi'   => $request->input('Yashash_joyi_geolokatsiyasi'),
            'Buyruq'                        => $request->input('Buyruq'),
            'GPA'                           => $request->input('GPA'),
            'Kontrakt_N'                    => $request->input('Kontrakt_N'),
            'Shartnoma_turi'                => $request->input('Shartnoma_turi'),
        ];

        // Parol maydoni bo'sh qoldirilgan bo'lsa, eski parolni saqlab qolamiz.
        // 'password' => 'hashed' cast (User modelida) qiymatni avtomatik hash qiladi.
        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
        }

        if ($request->filled('Talaba_ID')) {
            $data['Talaba_ID'] = $request->input('Talaba_ID');
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Foydalanuvchi ma\'lumotlari muvaffaqiyatli yangilandi.');
    }

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Foydalanuvchi ma\'lumotlari o\'chirildi');
    }
}