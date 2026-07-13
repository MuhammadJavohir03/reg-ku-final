<?php

namespace Database\Seeders;

use App\Models\lesson_type;
use Illuminate\Database\Seeder;

class LessonTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Ma\'ruza', 'Amaliy'];

        foreach ($types as $type) {
            lesson_type::firstOrCreate(['nomi' => $type]);
        }
    }
}
