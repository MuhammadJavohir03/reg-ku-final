# Qo'qon Universiteti — Akademik Boshqaruv Tizimi

Laravel asosidagi to‘liq funksional universitet platformasi.  
Talabalar, o‘qituvchilar va adminlar uchun yagona muhit: baholar, mini/bepul semestrlar, testlar, jurnal, vedomostlar, e’lonlar va ichki chat.

---

## 📋 Loyiha haqida

Tizim Qo‘qon Universitetining o‘quv jarayonini raqamlashtirish uchun yaratilgan. Asosiy yo‘nalishlar:

- Talabalar va o‘qituvchilar bazasini boshqarish
- Fanlar, kafedra, fakultet, o‘quv yili
- Baholar (joriy, oraliq, yakuniy) va ularni import qilish
- **Mini semestr** va **Bepul maktab** (retake / qo‘shimcha o‘qish)
- Savol banklari + onlayn testlar
- Baholash qaydnomasi (vedomost) eksporti
- Jurnal (baholarni jonli tahrirlash + tarix)
- E’lonlar tizimi
- Admin–talaba va talaba–talaba chat

---

## 🛠️ Texnologiyalar

| Qatlam          | Texnologiya                          |
|-----------------|--------------------------------------|
| Backend         | Laravel (PHP)                        |
| Auth            | Laravel Auth + Impersonation         |
| Excel/PDF       | Maatwebsite/Excel, PhpSpreadsheet, PhpWord |
| Storage         | Laravel Storage (public disk)        |
| Frontend        | Blade + Alpine/Vanilla JS (AJAX)     |
| Cache / Lock    | Cache + Cache::lock (test boshlash)  |

---

## 📁 Asosiy modullar va Controllerlar

### 1. Foydalanuvchilar

| Controller              | Vazifasi |
|-------------------------|----------|
| `UserController`        | Talabalar CRUD, Excel import, ism farqlarini tekshirish, impersonation (`loginAs` / `backToAdmin`), o‘z baholarini ko‘rish |
| `AdminController`       | Adminlar CRUD |
| `TeacherController`     | O‘qituvchilar CRUD + Excel import (FISh + Elektron pochta) |
| `AuthController`        | Login / Logout + statistika |

### 2. O‘quv tuzilmasi

| Controller                    | Vazifasi |
|-------------------------------|----------|
| `SubjectController`           | Fanlar CRUD, dublikat, biriktirish (SubjectsToSubject), sinxronizatsiya |
| `CategoryController`          | Yo‘nalishlar (guruh prefiksi bo‘yicha avtomatik bog‘lash) |
| `KafedraController` / `FakultetController` / `KafedraFakultetController` | Kafedra va fakultet |
| `OquvYiliController`          | O‘quv yillari |
| `MudirController`             | Kafedra mudirlari (o‘quv yili + kafedra bo‘yicha unique) |
| `BepulSemestrController`      | Bo‘limlar (aktiv bo‘limni boshqarish) |

### 3. Baholar va import

| Controller          | Vazifasi |
|---------------------|----------|
| `GradeController`   | Excel import, Hemis PDF import, Bepul import, tozalash |
| `OzlashtirishController` | Qarzdorlik hisoboti + Excel eksport |
| `VedomostController` | Baholash qaydnomasi (guruhlar bo‘yicha Excel + ZIP, bulk eksport) |
| `JurnalController`  | Jurnal (free/mini), mavzu baholari, tarix (`GradeEditLog`), vedomost eksport |

### 4. Mini Semestr

| Controller                      | Vazifasi |
|---------------------------------|----------|
| `MiniSemestrController`         | Talaba ariza topshirish (maks. 3 fan), umumiy < 60 |
| `MiniSemestrAdminController`    | Admin ro‘yxat |
| `ArizaAdminController`          | Barcha arizalar (mini + free), qo‘lda yaratish, tahrirlash |
| `MiniMaktabController`          | Admin/o‘qituvchi paneli: mavzular, materiallar (test/video/pdf/topshiriq), o‘qituvchi taqsimoti, avto-taqsimlash |
| `MiniTmaktabController`         | Talaba tomoni (eski versiya) |
| `TalabaMiniMaktabController`    | Talaba tomoni (asosiy): mavzular, test, topshiriq yuklash, natijalar |

### 5. Bepul Maktab (Free Semester)

| Controller                    | Vazifasi |
|-------------------------------|----------|
| `FreeuserController`          | Talaba ariza topshirish faqatgina J+O > 20, qoldirilgan darslar foizi 33% < va umumiy < 60 |
| `FreeController`              | Admin ro‘yxat |
| `BepulMaktabController`       | Admin paneli: sozlamalar, status, urinishlar, harakatlar |
| `TalabaBepulMaktabController` | Talaba tomoni: test boshlash, yuborish, chiqish, natija |
| `BepulFanlarController`       | Fanlar ko‘rinishi |

### 6. Savol banki va testlar

| Controller            | Vazifasi |
|-----------------------|----------|
| `SavolBankController` | Bank yaratish, DOCX import (maxsus format: `~` to‘g‘ri, `#` noto‘g‘ri), savollarni tahrirlash |

**DOCX format misoli:**

```
Savol matni
{
~To‘g‘ri javob
#Noto‘g‘ri 1
#Noto‘g‘ri 2
#Noto‘g‘ri 3
}
```

### 7. E’lonlar

| Controller       | Vazifasi |
|------------------|----------|
| `ElonController` | CRUD, kategoriya + kurs bo‘yicha filtrlash, rasm yuklash |

### 8. Chat

| Controller (namespace)              | Vazifasi |
|-------------------------------------|----------|
| `Admin\ChatController`              | Admin tomoni: bo‘limlar, suhbatlar, poll |
| `Student\ChatController`            | Talaba tomoni: bo‘lim + to‘g‘ridan-to‘g‘ri chat, rozilik tizimi |
| `Admin\SectionController`           | Chat bo‘limlari + admin biriktirish |

### 9. Boshqa

| Controller            | Vazifasi |
|-----------------------|----------|
| `SidebarController`   | Sidebar ko‘rinishini Cache orqali yoqish/o‘chirish |
| `PageController`      | Oddiy sahifalar va vaqtincha yaratilgan test sahifalar uchun yonaltiruvchi |

---

## 🔑 Asosiy Logic qoidalari

### Mini Semestr arizasi

- Faol bo‘lim (`bolim.status = 1`) bo‘lishi shart
- Bir bo‘limda maksimal **3 ta** fan
- Faqat `umumiy < 60` bo‘lgan fanlar
- Joriy+oraliq < 20 va umumiy > 60 bo‘lgan fanlar bloklanadi

### Bepul Maktab arizasi

- `davomat ≤ 33`, `joriy_oraliq ≥ 20`, `umumiy ≤ 60`
- `bepul != 0` (yoki null) bo‘lishi kerak

### Baholar hisoblash (Mini)

- Joriy (mavzular yig‘indisi) ≤ 40
- Oraliq ≤ 20
- Yakuniy ≤ 40
- `joriy_oraliq = joriy + oraliq`
- `umumiy = joriy_oraliq + yakuniy`

### Test tizimi

- Cache lock + DB transaction (race condition oldini olish)
- Vaqt tugasa avtomatik `expired`
- Eng yuqori ball saqlanadi
- Urinishlar soni cheklangan

### Impersonation

- Admin talaba/o‘qituvchi nomidan kirishi mumkin
- Himoyalangan email’lar (`javohir8386@gmail.com`, `samiyusuf@gmail.com`) himoyalangan
- Admin nomidan oddiy admin kira olmaydi

---

## 🚀 O‘rnatish

```bash
# 1. Loyihani klonlash
git clone <repo-url>
cd <project>

# 2. Bog‘liqliklarni o‘rnatish
composer install
npm install && npm run build   # agar frontend assetlar bo‘lsa

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Ma’lumotlar bazasi
# .env da DB sozlamalarini to‘ldiring
php artisan migrate
php artisan db:seed            # kerak bo‘lsa

# 5. Storage
php artisan storage:link

# 6. Ishga tushirish
php artisan serve
```

### Muhim sozlamalar (.env)

```env
FILESYSTEM_DISK=public
# Excel/PDF import uchun memory va time limit oshirilgan
```

---

## 📂 Muhim papkalar

```
app/
├── Http/Controllers/          # Barcha controllerlar
├── Models/                    # Eloquent modellar
├── Imports/                   # Excel import klasslari
├── Exports/                   # Excel eksport klasslari
├── Services/                  # HemisPdfParser, VedomostReportBuilder
resources/views/               # Blade shablonlar
storage/app/public/            # Yuklangan fayllar (elons, admins, ms_videos, ms_pdfs ...)
```

---

## 🔐 Ruxsatlar va himoya

- Role asosida: `admin`, `teacher`, `talaba`
- Teacher faqat o‘ziga biriktirilgan fan/arizalarni ko‘radi
- Baholarni tahrirlash ba’zi joylarda maxsus email bilan cheklangan
- Test boshlashda race-condition himoyasi (`Cache::lock` + `lockForUpdate`)
- Impersonation himoyalangan hisoblar uchun yopiq

---

## 📤 Eksportlar

| Tur                    | Format     | Joylashuv |
|------------------------|------------|---------|
| Baholash qaydnomasi    | Excel / ZIP | `VedomostController`, `JurnalController` |
| Ozlashtirish hisoboti  | Excel      | `OzlashtirishController` |
| Ism farqlari           | Excel      | `UserController@checkImport` |
| Bulk vedomost          | Master ZIP | `VedomostController` (start → step → finish) |

---

## 🧪 Test tizimi oqimi (Talaba)

1. Ariza topshiriladi va status = 1 qilinadi
2. Savol banki biriktiriladi va sozlanadi
3. Talaba testni boshlaydi → `TestSession` yaratiladi
4. Savollar random tanlanadi → `QuestionUser`
5. Vaqt tugasa yoki yuborilsa → ball hisoblanadi
6. Eng yuqori ball `free_semestr` / `mini_semestr` ga yoziladi

---

## 📝 Eslatmalar

- Model nomlarida aralash case ishlatilgan (`bolim` / `Bolim`, `subject` / `Subject`) — diqqat qiling.
- Ba’zi joylarda `To‘liq_ismi` (apostrof bilan) ishlatilgan.
- `BepulSemestr` va `bolim` o‘rtasida bog‘liqlik bor — faol bo‘lim bitta bo‘lishi kerak.
- Chat tizimida `rozilik` (pending/accepted) mexanizmi mavjud.

---

## 👥 Mualliflar
Asosiy ishlab chiquvchi va admin: `javohir8386@gmail.com`