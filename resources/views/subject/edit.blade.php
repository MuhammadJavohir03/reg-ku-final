<x-layouts.sidebar>
    <x-slot:title>Fanlar tahrirlash</x-slot:title>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm custom-card">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="mb-4 fw-bold text-dark border-start border-primary border-4 ps-3">Fanlar tahrirlash
                        </h2>

                        <form action="{{ route('subject.update', $subject->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="custom-label">Fan nomi</label>
                                    <input type="text" name="nomi" class="form-control custom-input"
                                        placeholder="Matematika..." required value="{{$subject->nomi}}">
                                </div>

                                <div class="col-md-6">
                                    <label class="custom-label">Yo'nalish (Kategoriya)</label>
                                    <select name="teacher_id" class="form-select custom-input" required>
                                        <option value="" selected disabled>O'qituvchini tanlang</option>
                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ $subject->teacher->Toliq_ismi}}>
                                                {{ $category->nomi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="custom-label">Yo'nalish (Kategoriya)</label>
                                    <select name="category_id" class="form-select custom-input" required>
                                        <option value="" selected disabled>Yo'nalishni tanlang</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ $subject->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->nomi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="custom-label">Dars turi</label>
                                    <select name="lesson_type_id" class="form-select custom-input">
                                        <option value="" selected disabled>Turini tanlang</option>
                                        @foreach ($lesson_types as $lesson_type)
                                            <option value="{{ $lesson_type->id }}" {{ $subject->lesson_type_id == $lesson_type->id ? 'selected' : '' }}>
                                                {{ $lesson_type->nomi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="custom-label">Semestr</label>
                                    <input type="number" name="semster" class="form-control custom-input"
                                        placeholder="1-8" min="1" max="8" required value="{{ $subject->semster }}">
                                </div>

                                <div class="col-12 mt-5">
                                    <button type="submit"
                                        class="btn btn-primary px-5 py-2 fw-bold rounded-pill shadow-sm">
                                        <i class="fas fa-save me-2"></i> Fanini saqlash
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Kartochka stili */
        .custom-card {
            border-radius: 15px;
            background-color: #ffffff;
        }

        /* Label stili */
        .custom-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #6c757d;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Input va Select stili */
        .custom-input {
            background-color: #f8f9fa !important;
            border: 1px solid #e9ecef !important;
            color: #212529 !important;
            /* Matn rangi qora bo'lishini ta'minlaydi */
            padding: 12px 15px !important;
            border-radius: 10px !important;
            transition: all 0.2s ease-in-out;
        }

        .custom-input:focus {
            background-color: #ffffff !important;
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
            color: #212529 !important;
        }

        /* Select ichidagi matn ko'rinishi uchun */
        select.custom-input option {
            color: #212529;
            background-color: #ffffff;
        }

        /* Tugma hover */
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3) !important;
        }
    </style>
</x-layouts.sidebar>
