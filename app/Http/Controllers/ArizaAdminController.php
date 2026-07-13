<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\bolim;
use App\Models\free_semestr;
use App\Models\grade;
use App\Models\mini_semestr;
use App\Models\subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;


class ArizaAdminController extends Controller
{
    /**
     * Asosiy sahifa: ariza yaratish paneli (bo'lim -> talaba -> fan -> maktab turi) + arizalar jadvali
     */

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $mini = mini_semestr::with(['user', 'subject', 'bolim'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->whereHas('user', function ($u) use ($q) {
                        $u->where('To‘liq_ismi', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%")
                            ->orWhere('Talaba_ID', 'like', "%{$q}%");
                    })
                        ->orWhereHas('subject', function ($s) use ($q) {
                            $s->where('nomi', 'like', "%{$q}%");
                        })
                        ->orWhereHas('bolim', function ($b) use ($q) {
                            $b->where('nomi', 'like', "%{$q}%");
                        });
                });
            })
            ->get()
            ->map(function ($item) {
                $item->maktab_turi = 'Mini';
                return $item;
            });

        $free = free_semestr::with(['user', 'subject', 'bolim'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->whereHas('user', function ($u) use ($q) {
                        $u->where('To‘liq_ismi', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%")
                            ->orWhere('Talaba_ID', 'like', "%{$q}%");
                    })
                        ->orWhereHas('subject', function ($s) use ($q) {
                            $s->where('nomi', 'like', "%{$q}%");
                        })
                        ->orWhereHas('bolim', function ($b) use ($q) {
                            $b->where('nomi', 'like', "%{$q}%");
                        });
                });
            })
            ->get()
            ->map(function ($item) {
                $item->maktab_turi = 'Free';
                return $item;
            });

        $collection = $mini
            ->concat($free)
            ->sortByDesc('id')
            ->values();

        $perPage = 100;
        $page = LengthAwarePaginator::resolveCurrentPage();

        $arizalar = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        $bolimlar = bolim::orderBy('nomi')->get();
        $subjects = subject::orderBy('nomi')->get();

        return view('arizaadmin.index', compact('arizalar', 'bolimlar', 'subjects', 'q'));
    }

    /**
     * AJAX: user_id (yoki ism/email) bo'yicha talabani qidirish
     */
    public function searchUser(Request $request)
    {
        $request->validate(['q' => ['required', 'string']]);

        $q = trim($request->input('q'));

        $query = User::query();

        if (is_numeric($q)) {
            $query->where('id', $q);
        } else {
            $query->where(function ($sub) use ($q) {
                $sub->where('Talaba_ID', 'like', "%{$q}%")
                    ->orWhere('To‘liq_ismi', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query->limit(10)->get(['id', 'Talaba_ID', 'To‘liq_ismi', 'email']);

        return response()->json([
            'users' => $users->map(function ($user) {
                return [
                    'id'        => $user->id,
                    'name'      => $user->{'To‘liq_ismi'},
                    'talaba_id' => $user->Talaba_ID,
                    'email'     => $user->email,
                ];
            }),
        ]);
    }

    /**
     * AJAX: talaba + fan tanlanganda gradesda shu fan bo'yicha bahosi bormi yo'qmi tekshirish.
     * Hech qanday filtr qo'llanilmaydi — shunchaki mavjud/mavjud emasligini bildiradi.
     */
    public function checkGrade(Request $request)
    {
        $validated = $request->validate([
            'user_id'    => ['required', 'integer', 'exists:users,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
        ]);

        $baho = grade::where('user_id', $validated['user_id'])
            ->where('subject_id', $validated['subject_id'])
            ->first();

        if (! $baho) {
            return response()->json([
                'exists' => false,
                'baho'   => null,
            ]);
        }

        return response()->json([
            'exists' => true,
            'baho'   => [
                'joriy_baho'   => $baho->joriy_baho,
                'oraliq_baho'  => $baho->oraliq_baho,
                'joriy_oraliq' => $baho->joriy_oraliq,
                'yakuniy_baho' => $baho->yakuniy_baho,
                'umumiy'       => $baho->umumiy,
            ],
        ]);
    }

    /**
     * Yangi ariza yaratish.
     * Tartib: bolim -> talaba -> fan -> maktab turi (mini_semestr yoki free_semestr).
     * Eligibility filtri yo'q. Gradesda bahosi bo'lsa o'sha baholar bilan, bo'lmasa 0 lar bilan yozuv yaratiladi.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => ['required', 'integer', 'exists:users,id'],
            'subject_id'  => ['required', 'integer', 'exists:subjects,id'],
            'bolim_id'    => ['required', 'integer', 'exists:bolims,id'],
            'maktab_turi' => ['required', Rule::in(['mini', 'free'])],
        ]);

        $model = $validated['maktab_turi'] === 'free' ? free_semestr::class : mini_semestr::class;

        $alreadySubmitted = $model::where('user_id', $validated['user_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('bolim_id', $validated['bolim_id'])
            ->exists();

        if ($alreadySubmitted) {
            return response()->json([
                'message' => "Bu talaba ushbu fan va bo'lim uchun allaqachon ariza topshirgan.",
            ], 422);
        }

        $baho = grade::where('user_id', $validated['user_id'])
            ->where('subject_id', $validated['subject_id'])
            ->first();

        $data = [
            'user_id'      => $validated['user_id'],
            'subject_id'   => $validated['subject_id'],
            'bolim_id'     => $validated['bolim_id'],
            'status'       => 0,
            'joriy_baho'   => $baho->joriy_baho ?? 0,
            'oraliq_baho'  => $baho->oraliq_baho ?? 0,
            'joriy_oraliq' => $baho->joriy_oraliq ?? 0,
            'yakuniy_baho' => $baho->yakuniy_baho ?? 0,
            'umumiy'       => $baho->umumiy ?? 0,
        ];

        $model::create($data);

        return response()->json(['message' => 'Ariza muvaffaqiyatli yaratildi.']);
    }

    /**
     * Arizani tahrirlash formasi
     */
    public function edit(mini_semestr $ariza_admin)
    {
        $ariza    = $ariza_admin;
        $subjects = subject::orderBy('nomi')->get();
        $bolimlar = bolim::orderBy('nomi')->get();

        return view('arizaadmin.edit', compact('ariza', 'subjects', 'bolimlar'));
    }

    /**
     * Arizani yangilash (baholarni kiritish, statusni o'zgartirish)
     */
    public function update(Request $request, mini_semestr $ariza_admin)
    {
        $validated = $request->validate([
            'joriy_baho'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'oraliq_baho'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'joriy_oraliq' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'yakuniy_baho' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'status'       => ['required', Rule::in([0, 1])], // 1 = active, 0 = block
        ]);

        $ariza_admin->update($validated);

        return redirect()
            ->route('ariza_admin.index')
            ->with('success', 'Ariza muvaffaqiyatli yangilandi.');
    }

    /**
     * Arizani o'chirish
     */
    public function destroy(mini_semestr $ariza_admin)
    {
        $ariza_admin->delete();

        return redirect()
            ->route('ariza_admin.index')
            ->with('success', "Ariza o'chirildi.");
    }
}
