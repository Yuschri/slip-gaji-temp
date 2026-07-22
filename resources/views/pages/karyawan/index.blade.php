@extends('layouts.main')

@push('styles')
    <style>
        .btn-success {
            --bs-btn-color: #fff;
            --bs-btn-bg: #198754;
            --bs-btn-border-color: #198754;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #157347;
            --bs-btn-hover-border-color: #146c43;
            --bs-btn-focus-shadow-rgb: 60, 153, 110;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #146c43;
            --bs-btn-active-border-color: #13653f;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
            --bs-btn-disabled-color: #fff;
            --bs-btn-disabled-bg: #198754;
            --bs-btn-disabled-border-color: #198754;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="">
                        <h1 class="fs-3 mb-1">Data Karyawan</h1>
                        <p class="mb-0">Mengelola catatan pribadi karyawan, peran, dan detail rekening bank.</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap justify-content-end">
                        <a href="{{ route('karyawan.template-excel') }}" class="btn btn-success">
                            <i class="ti ti-download me-1"></i> Download Template
                        </a>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                            data-bs-target="#modalImportExcel">
                            <i class="ti ti-file-import me-1"></i> Import Excel
                        </button>
                        <a href="{{ route('karyawan.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus"></i> Tambah Karyawan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('import_errors'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong><i class="ti ti-alert-triangle me-1"></i> Beberapa baris gagal diimport:</strong>
                <ul class="mb-0 mt-2">
                    @foreach (session('import_errors') as $err)
                        <li style="font-size: 0.85rem;">{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card table-responsive p-4">
                    <table id="karyawanTable" class="table mb-0 text-nowrap table-hover">
                        <thead class="table-light border-light">
                            <tr>
                                <th>NIP</th>
                                <th>Nama Karyawan</th>
                                <th>NIK</th>
                                <th>Tanggal Masuk</th>
                                <th>Divisi</th>
                                <th>Jabatan</th>
                                <th>No WA</th>
                                <th>Nomor Rekening</th>
                                <th>Cabang</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($karyawans as $karyawan)
                                <tr class="align-middle">
                                    <td><strong>{{ $karyawan->nip }}</strong></td>
                                    <td>{{ $karyawan->nama_karyawan }}</td>
                                    <td>{{ $karyawan->nik ?? '-' }}</td>
                                    <td>{{ $karyawan->tanggal_masuk ? $karyawan->tanggal_masuk->format('d M Y') : '-' }}</td>
                                    <td>{{ $karyawan->divisi ? $karyawan->divisi->nama_divisi : '-' }}</td>
                                    <td>{{ $karyawan->jabatan ? $karyawan->jabatan->nama_jabatan : '-' }}</td>
                                    <td>{{ $karyawan->no_wa ?? '-' }}</td>
                                    <td>{{ $karyawan->nomor_rekening ?? '-' }}</td>
                                    <td>{{ $karyawan->cabang ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('karyawan.show', $karyawan->id_karyawan) }}"
                                            class="btn btn-sm btn-outline-info" title="Detail">
                                            <i class="ti ti-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('karyawan.edit', $karyawan->id_karyawan) }}"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="ti ti-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('karyawan.destroy', $karyawan->id_karyawan) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data karyawan {{ $karyawan->nama_karyawan }}?')">
                                                <i class="ti ti-trash"></i> Hapus
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

    {{-- ===== MODAL IMPORT EXCEL ===== --}}
    <div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning bg-opacity-10 border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="modalImportExcelLabel">
                        <i class="ti ti-file-import me-2 text-warning"></i> Import Data Karyawan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('karyawan.import-excel') }}" method="POST" enctype="multipart/form-data"
                    id="formImportExcel">
                    @csrf
                    <div class="modal-body py-4">
                        <div class="alert alert-info d-flex align-items-start gap-2 mb-4 py-2" style="font-size:0.85rem;">
                            <i class="ti ti-info-circle fs-5 mt-1 text-info flex-shrink-0"></i>
                            <div>
                                Gunakan file template yang bisa didownload dengan tombol <strong>Download Template</strong>.
                                Isi data mulai baris ke-5. <strong>Hapus baris contoh</strong> sebelum upload.
                                Format yang didukung: <strong>.xlsx</strong> / <strong>.xls</strong> (maks 5 MB).
                            </div>
                        </div>

                        {{-- Drop zone --}}
                        <div id="dropZone"
                            class="border border-2 border-dashed border-warning rounded-3 p-4 text-center mb-3"
                            style="cursor:pointer; transition: background 0.2s;">
                            <i class="ti ti-cloud-upload fs-1 text-warning d-block mb-2"></i>
                            <p class="mb-1 fw-semibold text-dark">Drag & drop file di sini, atau</p>
                            <label for="file_import" class="btn btn-outline-warning btn-sm mt-1">
                                <i class="ti ti-folder-open me-1"></i> Pilih File
                            </label>
                            <input type="file" name="file_import" id="file_import" accept=".xlsx,.xls" class="d-none">
                        </div>

                        <div id="filePreview" class="d-none">
                            <div class="d-flex align-items-center gap-3 p-3 rounded bg-light border">
                                <i class="ti ti-file-spreadsheet fs-2 text-success"></i>
                                <div class="flex-grow-1 text-start overflow-hidden">
                                    <div id="fileName" class="fw-semibold text-dark text-truncate"></div>
                                    <div id="fileSize" class="text-muted" style="font-size:0.8rem;"></div>
                                </div>
                                <button type="button" id="removeFile" class="btn btn-sm btn-outline-danger">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                        </div>

                        @error('file_import')
                            <div class="text-danger mt-2" style="font-size:0.85rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-warning" id="btnImport" disabled>
                            <i class="ti ti-upload me-1"></i> Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- ===== END MODAL ===== --}}

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#karyawanTable').DataTable({
                "order": [[1, "asc"]]
            });

            // ─── File Upload Handling ───────────────────────────────────────────
            var fileInput = document.getElementById('file_import');
            var dropZone = document.getElementById('dropZone');
            var filePreview = document.getElementById('filePreview');
            var fileName = document.getElementById('fileName');
            var fileSize = document.getElementById('fileSize');
            var btnImport = document.getElementById('btnImport');
            var removeBtn = document.getElementById('removeFile');

            function formatSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / 1048576).toFixed(2) + ' MB';
            }

            function showFile(file) {
                if (!file) return;
                var allowed = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel'];
                if (!file.name.match(/\.(xlsx|xls)$/i)) {
                    alert('File harus berformat .xlsx atau .xls');
                    return;
                }
                fileName.textContent = file.name;
                fileSize.textContent = formatSize(file.size);
                filePreview.classList.remove('d-none');
                dropZone.classList.add('d-none');
                btnImport.disabled = false;
            }

            // Click to pick file
            dropZone.addEventListener('click', function () { fileInput.click(); });
            fileInput.addEventListener('change', function () {
                if (this.files[0]) showFile(this.files[0]);
            });

            // Drag & drop
            dropZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                dropZone.style.background = 'rgba(255,193,7,0.12)';
            });
            dropZone.addEventListener('dragleave', function () {
                dropZone.style.background = '';
            });
            dropZone.addEventListener('drop', function (e) {
                e.preventDefault();
                dropZone.style.background = '';
                var file = e.dataTransfer.files[0];
                if (file) {
                    // Transfer to input
                    var dt = new DataTransfer();
                    dt.items.add(file);
                    fileInput.files = dt.files;
                    showFile(file);
                }
            });

            // Remove file
            removeBtn.addEventListener('click', function () {
                fileInput.value = '';
                filePreview.classList.add('d-none');
                dropZone.classList.remove('d-none');
                btnImport.disabled = true;
            });

            // Show spinner on submit
            document.getElementById('formImportExcel').addEventListener('submit', function () {
                btnImport.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';
                btnImport.disabled = true;
            });
        });
    </script>
@endpush
