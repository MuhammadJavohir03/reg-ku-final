<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Override;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'Talaba_ID',
        'category_id',
        'email',
        'password',
        'role',
        'To‘liq_ismi',
        'Fuqarolik',
        'Davlat',
        'Millat',
        'Viloyat',
        'Tuman',
        'Jins',
        'Tug‘ilgan_sana',
        'Pasport_raqami',
        'JSHSHIR_kod',
        'Pasport_berilgan_sana',
        'Kurs',
        'Fakultet',
        'Guruh',
        'Ta_lim_tili',
        'O‘quv_yili',
        'Semestr',
        'Bitiruvchi',
        'Mutaxassislik',
        'Ta’lim_turi',
        'Ta’lim_shakli',
        'To‘lov_shakli',
        'Grant_turi',
        'Avvalgi_ta_lim_ma_lumoti',
        'Talaba_toifasi',
        'Ijtimoiy_toifa',
        'Birga_yashaydiganlar_soni',
        'Birga_yashaydiganlar_toifasi',
        'Yashash_joyi_statusi',
        'Yashash_joyi_geolokatsiyasi',
        'Buyruq',
        'GPA',
        'Kontrakt_N',
        'Shartnoma_turi'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function grades()
    {
        return $this->hasMany(grade::class);
    }

    public function free_semestrs()
    {
        return $this->hasMany(free_semestr::class);
    }

    public function mini_semstrs()
    {
        return $this->hasMany(mini_semestr::class);
    }

    public function category()
    {
        return $this->belongsTo(category::class, 'category_id');
    }

    /* ------------------------------------------------------------------ */
    /*  CHAT bog'lanishlari                                                */
    /* ------------------------------------------------------------------ */

    /** Agar bu foydalanuvchi admin bo'lsa - biriktirilgan bo'limlar */
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_user')->withTimestamps();
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function getMergedGrade($subjectId)
    {
        $grade = $this->grades->firstWhere('subject_id', $subjectId);
        $mini  = $this->mini_semstrs->firstWhere('subject_id', $subjectId);
        $free  = $this->free_semestrs->firstWhere('subject_id', $subjectId);

        $result = new \stdClass();

        // Grades qiymatlari
        $result->joriy_baho   = $grade->joriy_baho ?? 0;
        $result->oraliq_baho  = $grade->oraliq_baho ?? 0;
        $result->yakuniy_baho = $grade->yakuniy_baho ?? 0;
        $result->davomat      = $grade->davomat ?? 0;

        // Mini semestrdan balandroq baholarni olish
        if ($mini) {

            $result->joriy_baho = max($result->joriy_baho, $mini->joriy_baho ?? 0);
            $result->oraliq_baho = max($result->oraliq_baho, $mini->oraliq_baho ?? 0);
            $result->yakuniy_baho = max($result->yakuniy_baho, $mini->yakuniy_baho ?? 0);
        }

        // Free semestr faqat yakuniy bahoni beradi
        if ($free) {
            $result->yakuniy_baho = max($result->yakuniy_baho, $free->yakuniy_baho ?? 0);
        }

        // Joriy + Oraliq
        $result->joriy_oraliq = $result->joriy_baho + $result->oraliq_baho;

        // Umumiy = Joriy + Oraliq + Yakuniy
        $result->umumiy = $result->joriy_oraliq + $result->yakuniy_baho;

        return $result;
    }
}