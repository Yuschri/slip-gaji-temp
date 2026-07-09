@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="">
                        <h1 class="fs-3 mb-1">Detail Karyawan</h1>
                        <p class="mb-0">Employee details & salary setup</p>
                    </div>
                    <div>
                        <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">Back to List</a>
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

        @if (session('error_gaji'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error_gaji') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error_potongan'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error_potongan') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Left Side: Employee Profile Card -->
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="card shadow-sm border-0 pb-3">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0 text-white"><i class="ti ti-user me-2"></i> Profil Karyawan</h5>
                    </div>
                    <div class="card-body pt-4">
                        <div class="text-center mb-4">
                            <img src="{{ asset('assets/images/avatar/blank.png') }}"
                                class="avatar avatar-xl rounded-circle border border-primary border-2 p-1" alt="Avatar"
                                width="100">
                            <h4 class="mt-3 mb-1 font-weight-bold">{{ $karyawan->nama_karyawan }}</h4>
                            <span class="badge bg-light-primary text-primary px-3 py-2 rounded-2 fs-6">NIP:
                                {{ $karyawan->nip }}</span>
                        </div>
                        <hr class="text-muted opacity-25">
                        <div class="mt-3 space-y-3">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted"><i class="ti ti-calendar me-2"></i> Tanggal Masuk:</span>
                                <strong>{{ $karyawan->tanggal_masuk ? $karyawan->tanggal_masuk->format('d M Y') : '-' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted"><i class="ti ti-layout-grid me-2"></i> Divisi:</span>
                                <strong>{{ $karyawan->divisi ? $karyawan->divisi->nama_divisi : '-' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted"><i class="ti ti-briefcase me-2"></i> Jabatan:</span>
                                <strong>{{ $karyawan->jabatan ? $karyawan->jabatan->nama_jabatan : '-' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted"><i class="ti ti-brand-whatsapp me-2"></i> WhatsApp:</span>
                                <strong>{{ $karyawan->no_wa ?? '-' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted"><i class="ti ti-credit-card me-2"></i> Nomor Rekening:</span>
                                <strong>{{ $karyawan->nomor_rekening ?? '-' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted"><i class="ti ti-building me-2"></i> Cabang:</span>
                                <strong><span
                                        class="badge bg-info text-white">{{ $karyawan->cabang ?? '-' }}</span></strong>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('karyawan.edit', $karyawan->id_karyawan) }}"
                                class="btn btn-outline-primary w-100">
                                <i class="ti ti-edit"></i> Edit Profil Karyawan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Salary Config (tb_gaji) -->
            <div class="col-lg-7 col-md-12">
                <form action="{{ route('karyawan.kompensasi.store', $karyawan->id_karyawan) }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_karyawan" value="{{ $karyawan->id_karyawan }}">

                    <div class="card shadow-sm border-0 pb-3">
                        <div class="card-header bg-success text-white py-3">
                            <h5 class="mb-0 text-white"><i class="ti ti-cash me-2"></i> Gaji Pokok & Tunjangan</h5>
                        </div>
                        <div class="card-body pt-4">
                            @if ($karyawan->gaji)
                                <div class="alert alert-light-success border border-success border-dashed text-success-emphasis d-flex align-items-center mb-4"
                                    role="alert">
                                    <i class="ti ti-circle-check fs-4 me-2"></i>
                                    <div>
                                        Data gaji untuk karyawan ini <strong>sudah diatur</strong>. Anda dapat
                                        memperbaruinya di
                                        bawah.
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-light-warning border border-warning border-dashed text-warning-emphasis d-flex align-items-center mb-4"
                                    role="alert">
                                    <i class="ti ti-alert-triangle fs-4 me-2"></i>
                                    <div>
                                        Data gaji untuk karyawan ini <strong>belum diatur</strong>. Silakan isi form di
                                        bawah untuk
                                        mengisi data gaji.
                                    </div>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label font-weight-bold">Gaji Pokok <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" min="0" name="gaji_pokok"
                                            class="form-control form-control-lg text-end font-weight-bold border-success text-success rupiah-mask"
                                            required placeholder="0"
                                            value="{{ old('gaji_pokok', $karyawan->gaji ? (int) $karyawan->gaji->gaji_pokok : '') }}">
                                    </div>
                                    @error('gaji_pokok')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">Tunjangan Pengalaman Kerja</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" min="0" name="t_pengalaman_kerja"
                                            class="form-control text-end rupiah-mask" placeholder="0"
                                            value="{{ old('t_pengalaman_kerja', $karyawan->gaji ? (int) $karyawan->gaji->t_pengalaman_kerja : '0') }}">
                                    </div>
                                    @error('t_pengalaman_kerja')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">Tunjangan Jabatan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" min="0" name="t_jabatan"
                                            class="form-control text-end rupiah-mask" placeholder="0"
                                            value="{{ old('t_jabatan', $karyawan->gaji ? (int) $karyawan->gaji->t_jabatan : '0') }}">
                                    </div>
                                    @error('t_jabatan')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">Tunjangan Profesi</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" min="0" name="t_profesi"
                                            class="form-control text-end rupiah-mask" placeholder="0"
                                            value="{{ old('t_profesi', $karyawan->gaji ? (int) $karyawan->gaji->t_profesi : '0') }}">
                                    </div>
                                    @error('t_profesi')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">Tunjangan Kehadiran</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" min="0" name="t_kehadiran"
                                            class="form-control text-end rupiah-mask" placeholder="0"
                                            value="{{ old('t_kehadiran', $karyawan->gaji ? (int) $karyawan->gaji->t_kehadiran : '0') }}">
                                    </div>
                                    @error('t_kehadiran')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">Tunjangan Kinerja</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" min="0" name="t_kinerja"
                                            class="form-control text-end rupiah-mask" placeholder="0"
                                            value="{{ old('t_kinerja', $karyawan->gaji ? (int) $karyawan->gaji->t_kinerja : '0') }}">
                                    </div>
                                    @error('t_kinerja')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Tunjangan Operasional</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" min="0" name="t_operasional"
                                            class="form-control text-end rupiah-mask" placeholder="0"
                                            value="{{ old('t_operasional', $karyawan->gaji ? (int) $karyawan->gaji->t_operasional : '0') }}">
                                    </div>
                                    @error('t_kinerja')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Potongan (tb_potongan) -->
                    <div class="card shadow-sm border-0 pb-3 mt-4">
                        <div class="card-header bg-danger text-white py-3">
                            <h5 class="mb-0 text-white"><i class="ti ti-scissors me-2"></i> Potongan Gaji</h5>
                        </div>
                        <div class="card-body pt-4">
                            @if ($karyawan->potongan)
                                <div class="alert alert-light-danger border border-danger border-dashed text-danger-emphasis d-flex align-items-center mb-4"
                                    role="alert">
                                    <i class="ti ti-circle-check fs-4 me-2"></i>
                                    <div>
                                        Data potongan untuk karyawan ini <strong>sudah diatur</strong>. Anda dapat
                                        memperbaruinya di
                                        bawah.
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-light-warning border border-warning border-dashed text-warning-emphasis d-flex align-items-center mb-4"
                                    role="alert">
                                    <i class="ti ti-alert-triangle fs-4 me-2"></i>
                                    <div>
                                        Data potongan untuk karyawan ini <strong>belum diatur</strong>. Silakan isi form di
                                        bawah
                                        untuk
                                        mengisi data potongan.
                                    </div>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label font-weight-bold">Sedekah Rombongan <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" min="0" name="potongan_sedekah_rombongan"
                                            class="form-control form-control-lg text-end font-weight-bold border-danger text-danger rupiah-mask"
                                            required placeholder="0"
                                            value="{{ old('potongan_sedekah_rombongan', $karyawan->potongan ? (int) $karyawan->potongan->potongan_sedekah_rombongan : '0') }}">
                                    </div>
                                    @error('potongan_sedekah_rombongan')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                        </div>

                    </div>
                    <div class="mt-2 pt-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Data Gaji & Potongan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            function formatRupiah(value) {
                if (value === undefined || value === null || value === '') return '0';
                var str = value.toString().trim();
                var dotCount = (str.match(/\./g) || []).length;
                var clean;
                if (dotCount === 1) {
                    var parts = str.split('.');
                    var afterDot = parts[1];
                    if (afterDot !== undefined && afterDot.length === 2 && /^\d+$/.test(afterDot)) {
                        clean = parts[0].replace(/[^0-9]/g, '');
                    } else {
                        clean = str.replace(/\./g, '').replace(/[^0-9]/g, '');
                    }
                } else {
                    clean = str.replace(/\./g, '').replace(/[^0-9]/g, '');
                }
                if (clean === '') return '0';
                var num = parseInt(clean, 10);
                if (isNaN(num) || num === 0) return '0';
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Format on keyup
            $(document).on('input', '.rupiah-mask', function () {
                var el = this;
                var rawVal = el.value.replace(/\./g, '').replace(/[^0-9]/g, '');
                var formatted = rawVal === '' ? '0' : parseInt(rawVal, 10).toString().replace(
                    /\B(?=(\d{3})+(?!\d))/g, '.');
                var oldLen = el.value.length;
                el.value = formatted;
                var newLen = formatted.length;
                var pos = el.selectionStart + (newLen - oldLen);
                el.setSelectionRange(Math.max(0, pos), Math.max(0, pos));
            });

            // Format existing values on load
            $('.rupiah-mask').each(function () {
                $(this).val(formatRupiah($(this).val()));
            });

            // Strip formatting before submit
            $('form').on('submit', function () {
                $(this).find('.rupiah-mask').each(function () {
                    $(this).val($(this).val().replace(/\./g, ''));
                });
            });
        });
    </script>
@endpush