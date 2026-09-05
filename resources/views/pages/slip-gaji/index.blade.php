@extends('layouts.main')

@push('styles')
<style>
    .btn-dny-primary {
        background-color: #dc2626;
        border-color: #dc2626;
        color: #ffffff !important;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
    }
    .btn-dny-primary:hover, .btn-dny-primary:focus {
        background-color: #b91c1c;
        border-color: #b91c1c;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(185, 28, 28, 0.35);
        transform: translateY(-1px);
    }
    .btn-dny-primary:active {
        transform: translateY(0);
    }

    /* Standardized Control Height & Padding */
    .filter-control {
        height: 38px;
        font-size: 0.875rem;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .form-select.filter-control {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
        padding-left: 0.75rem;
        padding-right: 2.25rem;
    }

    .dropdown-menu .dropdown-item {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        transition: background-color 0.15s ease;
    }
    .dropdown-menu .dropdown-item:hover {
        background-color: #f1f5f9;
    }

    .toolbar-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .table-card-wrapper {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header Title -->
        <div class="row mb-3">
            <div class="col-12">
                <h1 class="fs-3 fw-bold mb-1 text-dark">Slip Gaji</h1>
                <p class="text-muted mb-0">Mengelola, mengimpor, dan merekap data slip gaji karyawan</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('import_errors'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong><i class="ti ti-alert-triangle me-1"></i> Beberapa baris gagal diimport:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach (session('import_errors') as $err)
                        <li style="font-size: 0.85rem;">{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Unified Toolbar (Filters on Left, Actions on Right) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="toolbar-card d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <!-- Left: Filters Group -->
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="d-flex align-items-center me-1 text-muted">
                            <i class="ti ti-filter me-1 fs-5"></i>
                            <span class="fw-semibold text-secondary fs-7 d-none d-sm-inline">Filter:</span>
                        </div>
                        <div>
                            <select id="klinikFilter" class="form-select filter-control" style="min-width: 140px;">
                                <option value="">Semua Klinik</option>
                                @foreach ($kliniks as $klinik)
                                    <option value="{{ $klinik }}">{{ $klinik }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select id="bulanFilter" class="form-select filter-control" style="min-width: 130px;">
                                <option value="">Semua Bulan</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ date('F', mktime(0, 0, 0, $i, 10)) }}">
                                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <select id="tahunFilter" class="form-select filter-control" style="min-width: 120px;">
                                <option value="">Semua Tahun</option>
                                @foreach ($tahuns as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Right: Actions Group -->
                    <div class="d-flex flex-wrap align-items-center gap-2 justify-content-start justify-content-lg-end">
                        <!-- Import Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary filter-control dropdown-toggle px-3" type="button" id="dropdownImportMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-file-import me-1"></i> Import / Template <i class="ti ti-chevron-down ms-1 fs-7"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-1" aria-labelledby="dropdownImportMenu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('slip-gaji.template-excel') }}">
                                        <i class="ti ti-download me-2 text-success fs-5"></i> Download Template Excel
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                                        <i class="ti ti-file-spreadsheet me-2 text-primary fs-5"></i> Impor Slip Gaji (Excel)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importLemburModal">
                                        <i class="ti ti-clock me-2 text-info fs-5"></i> Import Excel Lembur
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importBpjstkModal">
                                        <i class="ti ti-file-text me-2 text-danger fs-5"></i> Import PDF BPJSTK
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Secondary Action: Export Excel -->
                        <a href="#" id="exportExcelBtn" class="btn btn-outline-success filter-control px-3">
                            <i class="ti ti-file-type-xls me-1"></i> Export Excel
                        </a>

                        <!-- Secondary Action: Broadcast WA -->
                        <form action="{{ route('slip-gaji.broadcast-bulk') }}" method="POST" id="broadcastBulkForm" class="d-inline">
                            @csrf
                            <input type="hidden" name="klinik" id="klinikHidden">
                            <input type="hidden" name="bulan" id="bulanHidden">
                            <input type="hidden" name="tahun" id="tahunHidden">
                            <button type="submit" class="btn btn-outline-info filter-control px-3"
                                onclick="return confirm('Broadcast ke semua data yang difilter?')">
                                <i class="ti ti-brand-whatsapp me-1"></i> Broadcast WA
                            </button>
                        </form>

                        <!-- Primary Action: Tambah Manual -->
                        <a href="{{ route('slip-gaji.create') }}" class="btn btn-dny-primary filter-control px-3">
                            <i class="ti ti-plus me-1"></i> Tambah Manual
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card table-card-wrapper table-responsive p-4">
                    <table id="slipGajiTable" class="table mb-0 text-nowrap table-hover">
                        <thead class="table-light border-light">
                            <tr>
                                <th>Nama Karyawan</th>
                                <th>Bulan / Tahun</th>
                                <th>Divisi</th>
                                <th>Klinik</th>
                                <th>Nominal Transfer</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slips as $slip)
                                <tr class="align-middle">
                                    <td>{{ $slip->nama_karyawan }}</td>
                                    <td>{{ date('F', mktime(0, 0, 0, $slip->bulan, 10)) }} / {{ $slip->tahun }}</td>
                                    <td>{{ $slip->divisi }}</td>
                                    <td>{{ $slip->klinik }}</td>
                                    <td>Rp {{ number_format($slip->nominal_transfer, 0, ',', '.') }}</td>
                                    <td>
                                        <form action="{{ route('slip-gaji.broadcast-single', $slip->id_slip) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-info" title="Broadcast WA"
                                                onclick="return confirm('Send WhatsApp broadcast to {{ $slip->nama_karyawan }}?')">
                                                <i class="ti ti-brand-whatsapp"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('slip-gaji.view-pdf', $slip->id_slip) }}"
                                            class="btn btn-sm btn-outline-success" title="View PDF" target="_blank">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('slip-gaji.export-pdf', $slip->id_slip) }}"
                                            class="btn btn-sm btn-outline-danger" title="Download PDF" target="_blank">
                                            <i class="ti ti-file-type-pdf"></i>
                                        </a>
                                        <a href="{{ route('slip-gaji.edit', $slip->id_slip) }}"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form action="{{ route('slip-gaji.destroy', $slip->id_slip) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('slip-gaji.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Slip Gaji</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select name="bulan" id="bulan" class="form-select" required>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select name="tahun" id="tahun" class="form-select" required>
                                @for ($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">File Excel</label>
                            <input type="file" name="file" id="file" class="form-control" required accept=".xlsx">
                            <small class="text-muted d-block mt-1">
                                Wajib gunakan file <strong>.xlsx</strong> dari tombol <strong>Download Template</strong>.
                                Baris ke-5 adalah contoh pengisian dan bisa dihapus sebelum import.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Lembur Modal -->
    <div class="modal fade" id="importLemburModal" tabindex="-1" aria-labelledby="importLemburModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('slip-gaji.import-lembur') }}" method="POST" enctype="multipart/form-data" id="importLemburForm">
                @csrf
                <input type="hidden" name="overwrite" id="overwriteInput" value="0">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importLemburModalLabel">Import Excel Lembur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="lembur_bulan" class="form-label">Bulan</label>
                            <select name="bulan" id="lembur_bulan" class="form-select" required>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="lembur_tahun" class="form-label">Tahun</label>
                            <select name="tahun" id="lembur_tahun" class="form-select" required>
                                @for ($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="lembur_file" class="form-label">File Excel Lembur (.xlsx)</label>
                            <input type="file" name="file" id="lembur_file" class="form-control" required accept=".xlsx">
                            <small class="text-muted d-block mt-1">
                                Data yang diambil: Kolom DS (Lembur Jam & Menit), Kolom DU (Nominal Lembur), dicocokkan berdasarkan Kolom C (Nama Karyawan).
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-info text-white" id="btnSubmitLembur">
                            <i class="ti ti-upload me-1"></i> Import Lembur
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Duplicate Warning Modal -->
    <div class="modal fade" id="duplicateWarningModal" tabindex="-1" aria-labelledby="duplicateWarningModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="duplicateWarningModalLabel">
                        <i class="ti ti-alert-triangle me-1"></i> Peringatan Data Slip Gaji Sudah Ada
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">
                        Slip gaji untuk karyawan berikut pada bulan & tahun yang dipilih <strong>sudah ada di sistem</strong>:
                    </p>
                    <div class="alert alert-light border mb-3" style="max-height: 180px; overflow-y: auto;">
                        <ul id="duplicateList" class="mb-0 ps-3"></ul>
                    </div>
                    <p class="mb-0 text-muted fs-7">
                        Apakah Anda ingin tetap melanjutkan dan <strong>meng-update</strong> data slip gaji tersebut, atau membatalkan import?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancelDuplicate">Batal</button>
                    <button type="button" class="btn btn-warning text-dark" id="btnConfirmUpdate">
                        <i class="ti ti-check me-1"></i> Tetap Lanjut & Update Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import BPJSTK PDF Modal -->
    <div class="modal fade" id="importBpjstkModal" tabindex="-1" aria-labelledby="importBpjstkModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('slip-gaji.import-bpjstk-pdf') }}" method="POST" enctype="multipart/form-data" id="importBpjstkForm">
                @csrf
                <input type="hidden" name="overwrite" id="bpjstkOverwriteInput" value="0">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importBpjstkModalLabel">Import PDF BPJSTK</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="bpjstk_bulan" class="form-label">Bulan</label>
                            <select name="bulan" id="bpjstk_bulan" class="form-select" required>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bpjstk_tahun" class="form-label">Tahun</label>
                            <select name="tahun" id="bpjstk_tahun" class="form-select" required>
                                @for ($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bpjstk_file" class="form-label">File PDF BPJSTK (.pdf)</label>
                            <input type="file" name="file" id="bpjstk_file" class="form-control" required accept=".pdf">
                            <small class="text-muted d-block mt-1">
                                Validasi: Mencocokkan Nomor Pegawai & Nama Tenaga Kerja. Jika Nomor Pegawai kosong, pencocokan menggunakan Nama.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-secondary" id="btnSubmitBpjstk">
                            <i class="ti ti-upload me-1"></i> Import BPJSTK PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Duplicate BPJSTK Warning Modal -->
    <div class="modal fade" id="duplicateBpjstkWarningModal" tabindex="-1" aria-labelledby="duplicateBpjstkWarningModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="duplicateBpjstkWarningModalLabel">
                        <i class="ti ti-alert-triangle me-1"></i> Peringatan Data BPJSTK / Slip Gaji Sudah Ada
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">
                        Data BPJSTK / Slip gaji untuk karyawan berikut pada bulan & tahun yang dipilih <strong>sudah ada di sistem</strong>:
                    </p>
                    <div class="alert alert-light border mb-3" style="max-height: 180px; overflow-y: auto;">
                        <ul id="duplicateBpjstkList" class="mb-0 ps-3"></ul>
                    </div>
                    <p class="mb-0 text-muted fs-7">
                        Apakah Anda ingin tetap melanjutkan dan <strong>meng-update</strong> data BPJSTK & slip gaji tersebut, atau membatalkan import?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning text-dark" id="btnConfirmBpjstkUpdate">
                        <i class="ti ti-check me-1"></i> Tetap Lanjut & Update Data
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            var table = $('#slipGajiTable').DataTable();

            $('#klinikFilter').on('change', function () {
                var val = this.value;
                table.column(3).search(val).draw();
                $('#klinikHidden').val(val);
            });

            $('#bulanFilter').on('change', function () {
                var val = this.value;
                // column 1 is "Bulan / Tahun"
                table.column(1).search(val).draw();

                // Get month index (1-12) if needed for backend
                var monthMap = {
                    'January': 1, 'February': 2, 'March': 3, 'April': 4, 'May': 5, 'June': 6,
                    'July': 7, 'August': 8, 'September': 9, 'October': 10, 'November': 11, 'December': 12
                };
                $('#bulanHidden').val(val ? monthMap[val] : '');
            });

            $('#tahunFilter').on('change', function () {
                var val = this.value;
                table.column(1).search(val).draw();
                $('#tahunHidden').val(val);
            });

            $('#exportExcelBtn').on('click', function (e) {
                e.preventDefault();

                var klinik = $('#klinikFilter').val();
                var bulan = $('#bulanFilter').val();
                var tahun = $('#tahunFilter').val();

                var monthMap = {
                    'January': 1, 'February': 2, 'March': 3, 'April': 4, 'May': 5, 'June': 6,
                    'July': 7, 'August': 8, 'September': 9, 'October': 10, 'November': 11, 'December': 12
                };

                var url = new URL('{{ route('slip-gaji.export-excel') }}', window.location.origin);
                if (klinik) url.searchParams.set('klinik', klinik);
                if (bulan) url.searchParams.set('bulan', monthMap[bulan] ? monthMap[bulan] : bulan);
                if (tahun) url.searchParams.set('tahun', tahun);

                window.location.href = url.toString();
            });

            $('#btnSubmitLembur').on('click', function (e) {
                e.preventDefault();

                var fileInput = $('#lembur_file')[0];
                if (!fileInput.files.length) {
                    alert('Silakan pilih file Excel Lembur terlebih dahulu.');
                    return;
                }

                $('#overwriteInput').val('0');

                var formData = new FormData($('#importLemburForm')[0]);
                
                var btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memeriksa file...');

                $.ajax({
                    url: '{{ route('slip-gaji.check-lembur-duplicate') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        btn.prop('disabled', false).html('<i class="ti ti-upload me-1"></i> Import Lembur');
                        
                        if (res.has_duplicates) {
                            var listHtml = '';
                            res.duplicates.forEach(function (name) {
                                listHtml += '<li><strong>' + name + '</strong></li>';
                            });
                            $('#duplicateList').html(listHtml);
                            $('#importLemburModal').modal('hide');
                            $('#duplicateWarningModal').modal('show');
                        } else {
                            $('#importLemburForm')[0].submit();
                        }
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).html('<i class="ti ti-upload me-1"></i> Import Lembur');
                        var msg = 'Terjadi kesalahan saat memeriksa file.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        alert(msg);
                    }
                });
            });

            $('#btnConfirmUpdate').on('click', function () {
                $('#overwriteInput').val('1');
                $('#duplicateWarningModal').modal('hide');
                $('#importLemburForm')[0].submit();
            });

            $('#btnSubmitBpjstk').on('click', function (e) {
                e.preventDefault();

                var fileInput = $('#bpjstk_file')[0];
                if (!fileInput.files.length) {
                    alert('Silakan pilih file PDF BPJSTK terlebih dahulu.');
                    return;
                }

                $('#bpjstkOverwriteInput').val('0');

                var formData = new FormData($('#importBpjstkForm')[0]);
                
                var btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memeriksa file...');

                $.ajax({
                    url: '{{ route('slip-gaji.check-bpjstk-duplicate') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        btn.prop('disabled', false).html('<i class="ti ti-upload me-1"></i> Import BPJSTK PDF');
                        
                        if (res.has_duplicates) {
                            var listHtml = '';
                            res.duplicates.forEach(function (name) {
                                listHtml += '<li><strong>' + name + '</strong></li>';
                            });
                            $('#duplicateBpjstkList').html(listHtml);
                            $('#importBpjstkModal').modal('hide');
                            $('#duplicateBpjstkWarningModal').modal('show');
                        } else {
                            $('#importBpjstkForm')[0].submit();
                        }
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).html('<i class="ti ti-upload me-1"></i> Import BPJSTK PDF');
                        var msg = 'Terjadi kesalahan saat memeriksa file PDF.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        alert(msg);
                    }
                });
            });

            $('#btnConfirmBpjstkUpdate').on('click', function () {
                $('#bpjstkOverwriteInput').val('1');
                $('#duplicateBpjstkWarningModal').modal('hide');
                $('#importBpjstkForm')[0].submit();
            });
        });
    </script>
@endpush
