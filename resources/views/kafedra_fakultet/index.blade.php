<x-layouts.sidebar>
    <x-slot:title>Kafedra va Fakultetlar</x-slot:title>

    <link rel="stylesheet" href="{{ asset('css/jadvallar.css') }}">

    <div class="oz-wrap container-fluid py-4">

        <div class="row g-4">

            {{-- ===================== KAFEDRALAR ===================== --}}
            <div class="col-lg-6">
                <div class="card custom-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="fw-bold border-start border-primary border-4 ps-3 mb-0" style="font-size:18px;">Kafedralar</h2>
                            <button type="button" class="ar-btn ar-btn-ok" data-bs-toggle="modal"
                                data-bs-target="#kafedraAddModal">
                                <i class="bx bx-plus"></i> Yangi kafedra
                            </button>
                        </div>

                        <div class="arizalar-table-wrap">
                            <table class="arizalar-table">
                                <thead>
                                    <tr>
                                        <th style="width:60px;">#</th>
                                        <th>Nomi</th>
                                        <th>Fakultet</th>
                                        <th style="width:120px;">Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kafedralar as $kafedra)
                                        <tr>
                                            <td class="ar-id">{{ $kafedra->id }}</td>
                                            <td style="font-weight:500;">{{ $kafedra->nomi }}</td>
                                            <td style="color:#888;">{{ $kafedra->fakultet->nomi ?? '-' }}</td>
                                            <td>
                                                <div style="display:flex; gap:6px;">
                                                    <button type="button" class="ar-btn" style="padding:6px 9px;"
                                                        data-bs-toggle="modal" data-bs-target="#kafedraEditModal"
                                                        data-id="{{ $kafedra->id }}" data-nomi="{{ $kafedra->nomi }}"
                                                        data-fakultet-id="{{ $kafedra->fakultet_id }}"
                                                        onclick="fillKafedraEdit(this)">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                    <button type="button" class="ar-btn ar-btn-rej" style="padding:6px 9px;"
                                                        data-bs-toggle="modal" data-bs-target="#kafedraDeleteModal"
                                                        data-id="{{ $kafedra->id }}" data-nomi="{{ $kafedra->nomi }}"
                                                        onclick="fillKafedraDelete(this)">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" style="text-align:center; color:#888; padding:2rem;">Kafedralar mavjud emas
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== FAKULTETLAR ===================== --}}
            <div class="col-lg-6">
                <div class="card custom-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="fw-bold border-start border-primary border-4 ps-3 mb-0" style="font-size:18px;">Fakultetlar
                            </h2>
                            <button type="button" class="ar-btn ar-btn-ok" data-bs-toggle="modal"
                                data-bs-target="#fakultetAddModal">
                                <i class="bx bx-plus"></i> Yangi fakultet
                            </button>
                        </div>

                        <div class="arizalar-table-wrap">
                            <table class="arizalar-table">
                                <thead>
                                    <tr>
                                        <th style="width:60px;">#</th>
                                        <th>Nomi</th>
                                        <th style="width:120px;">Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($fakultetlar as $fakultet)
                                        <tr>
                                            <td class="ar-id">{{ $fakultet->id }}</td>
                                            <td style="font-weight:500;">{{ $fakultet->nomi }}</td>
                                            <td>
                                                <div style="display:flex; gap:6px;">
                                                    <button type="button" class="ar-btn" style="padding:6px 9px;"
                                                        data-bs-toggle="modal" data-bs-target="#fakultetEditModal"
                                                        data-id="{{ $fakultet->id }}" data-nomi="{{ $fakultet->nomi }}"
                                                        onclick="fillFakultetEdit(this)">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                    <button type="button" class="ar-btn ar-btn-rej" style="padding:6px 9px;"
                                                        data-bs-toggle="modal" data-bs-target="#fakultetDeleteModal"
                                                        data-id="{{ $fakultet->id }}" data-nomi="{{ $fakultet->nomi }}"
                                                        onclick="fillFakultetDelete(this)">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" style="text-align:center; color:#888; padding:2rem;">Fakultetlar mavjud emas
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ===================== KAFEDRA: QO'SHISH MODAL ===================== --}}
    <div class="modal fade" id="kafedraAddModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kafedra.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Yangi kafedra qo'shish</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="custom-label">Kafedra nomi</label>
                        <input type="text" name="nomi" class="form-control mb-3" placeholder="Kafedra nomi..." required>

                        <label class="custom-label">Fakultet</label>
                        <select name="fakultet_id" class="form-select" required>
                            <option value="" disabled selected>Fakultetni tanlang</option>
                            @foreach ($fakultetlar as $fakultet)
                                <option value="{{ $fakultet->id }}">{{ $fakultet->nomi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-primary">Saqlash</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== KAFEDRA: TAHRIRLASH MODAL ===================== --}}
    <div class="modal fade" id="kafedraEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="kafedraEditForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Kafedrani tahrirlash</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="custom-label">Kafedra nomi</label>
                        <input type="text" name="nomi" id="kafedraEditNomi" class="form-control mb-3" required>

                        <label class="custom-label">Fakultet</label>
                        <select name="fakultet_id" id="kafedraEditFakultetId" class="form-select" required>
                            <option value="" disabled>Fakultetni tanlang</option>
                            @foreach ($fakultetlar as $fakultet)
                                <option value="{{ $fakultet->id }}">{{ $fakultet->nomi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-primary">Yangilash</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== KAFEDRA: O'CHIRISH MODAL ===================== --}}
    <div class="modal fade" id="kafedraDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="kafedraDeleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Kafedrani o'chirish</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">"<span id="kafedraDeleteNomi"></span>" kafedrasini o'chirishni
                            tasdiqlaysizmi?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-danger">O'chirish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== FAKULTET: QO'SHISH MODAL ===================== --}}
    <div class="modal fade" id="fakultetAddModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('fakultet.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Yangi fakultet qo'shish</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="custom-label">Fakultet nomi</label>
                        <input type="text" name="nomi" class="form-control" placeholder="Fakultet nomi..." required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-primary">Saqlash</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== FAKULTET: TAHRIRLASH MODAL ===================== --}}
    <div class="modal fade" id="fakultetEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="fakultetEditForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Fakultetni tahrirlash</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="custom-label">Fakultet nomi</label>
                        <input type="text" name="nomi" id="fakultetEditNomi" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-primary">Yangilash</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== FAKULTET: O'CHIRISH MODAL ===================== --}}
    <div class="modal fade" id="fakultetDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="fakultetDeleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Fakultetni o'chirish</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">"<span id="fakultetDeleteNomi"></span>" fakultetini o'chirishni
                            tasdiqlaysizmi?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-danger">O'chirish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .custom-label {
            font-size: 11px;
            font-weight: 700;
            color: #8b87a8;
            margin-bottom: 6px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>

    <script>
        // Kafedra tahrirlash modalini tanlangan qatordagi ma'lumot bilan to'ldiradi
        function fillKafedraEdit(btn) {
            const id = btn.getAttribute('data-id');
            const nomi = btn.getAttribute('data-nomi');
            const fakultetId = btn.getAttribute('data-fakultet-id');
            document.getElementById('kafedraEditNomi').value = nomi;
            document.getElementById('kafedraEditFakultetId').value = fakultetId;
            document.getElementById('kafedraEditForm').action = '{{ url('kafedra') }}/' + id;
        }

        // Kafedra o'chirish modalini tanlangan qatordagi ma'lumot bilan to'ldiradi
        function fillKafedraDelete(btn) {
            const id = btn.getAttribute('data-id');
            const nomi = btn.getAttribute('data-nomi');
            document.getElementById('kafedraDeleteNomi').textContent = nomi;
            document.getElementById('kafedraDeleteForm').action = '{{ url('kafedra') }}/' + id;
        }

        // Fakultet tahrirlash modalini tanlangan qatordagi ma'lumot bilan to'ldiradi
        function fillFakultetEdit(btn) {
            const id = btn.getAttribute('data-id');
            const nomi = btn.getAttribute('data-nomi');
            document.getElementById('fakultetEditNomi').value = nomi;
            document.getElementById('fakultetEditForm').action = '{{ url('fakultet') }}/' + id;
        }

        // Fakultet o'chirish modalini tanlangan qatordagi ma'lumot bilan to'ldiradi
        function fillFakultetDelete(btn) {
            const id = btn.getAttribute('data-id');
            const nomi = btn.getAttribute('data-nomi');
            document.getElementById('fakultetDeleteNomi').textContent = nomi;
            document.getElementById('fakultetDeleteForm').action = '{{ url('fakultet') }}/' + id;
        }
    </script>
</x-layouts.sidebar>