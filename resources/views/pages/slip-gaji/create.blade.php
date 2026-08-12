@extends('layouts.main')

@push('styles')
    <style>
        /* Tab Navigation Styling */
        .nav-tabs-custom {
            border-bottom: 2px solid #e9ecef;
            gap: 4px;
            flex-wrap: nowrap;
            overflow-x: auto;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            padding: 12px 20px;
            font-weight: 500;
            color: #6c757d;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-tabs-custom .nav-link:hover {
            color: #495057;
            border-bottom-color: #dee2e6;
            background-color: #f8f9fa;
            border-radius: 6px 6px 0 0;
        }

        .nav-tabs-custom .nav-link.active {
            color: #0d6efd;
            border-bottom-color: #0d6efd;
            background-color: transparent;
        }

        .nav-tabs-custom .nav-link .tab-icon {
            font-size: 1.2rem;
        }

        .tab-content>.tab-pane {
            padding: 24px 0 0 0;
        }

        /* Section sub-headers inside tabs */
        .section-subheader {
            font-size: 0.95rem;
            font-weight: 600;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 16px;
        }

        .section-subheader-bpjstk {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .section-subheader-bpjsk {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }

        .section-subheader-pph21 {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .section-subheader-lainnya {
            background-color: #e2e3e5;
            color: #383d41;
            border-left: 4px solid #6c757d;
        }

        /* Summary cards */
        .summary-card {
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }

        .summary-card-income {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 1px solid #b1dfbb;
        }

        .summary-card-deduction {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border: 1px solid #f1b0b7;
        }

        .summary-card-thp {
            background: linear-gradient(135deg, #cce5ff 0%, #b8daff 100%);
            border: 1px solid #9fcdff;
        }

        .summary-card .summary-label {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .summary-card .summary-value {
            font-size: 1.4rem;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="fs-3 mb-1">Add Slip Gaji Manually</h1>
                        <p class="mb-0">Create employee payslip with automatic data pre-fetching</p>
                    </div>
                    <div>
                        <a href="{{ route('slip-gaji.index') }}" class="btn btn-outline-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card p-4 shadow-sm border-0">
            <form action="{{ route('slip-gaji.store') }}" method="POST" id="slipGajiForm">
                @csrf

                {{-- ==================== TAB NAVIGATION ==================== --}}
                <ul class="nav nav-tabs nav-tabs-custom mb-0" id="slipGajiTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-karyawan-tab" data-bs-toggle="tab"
                            data-bs-target="#tab-karyawan" type="button" role="tab" aria-controls="tab-karyawan"
                            aria-selected="true">
                            <i class="ti ti-user tab-icon"></i> Data Karyawan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-pendapatan-tab" data-bs-toggle="tab"
                            data-bs-target="#tab-pendapatan" type="button" role="tab" aria-controls="tab-pendapatan"
                            aria-selected="false">
                            <i class="ti ti-cash tab-icon"></i> Pendapatan & Tunjangan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-potongan-tab" data-bs-toggle="tab" data-bs-target="#tab-potongan"
                            type="button" role="tab" aria-controls="tab-potongan" aria-selected="false">
                            <i class="ti ti-scissors tab-icon"></i> Potongan & Pengurangan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-kehadiran-tab" data-bs-toggle="tab" data-bs-target="#tab-kehadiran"
                            type="button" role="tab" aria-controls="tab-kehadiran" aria-selected="false">
                            <i class="ti ti-calendar-stats tab-icon"></i> Kehadiran & Status
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-ringkasan-tab" data-bs-toggle="tab" data-bs-target="#tab-ringkasan"
                            type="button" role="tab" aria-controls="tab-ringkasan" aria-selected="false">
                            <i class="ti ti-calculator tab-icon"></i> Ringkasan & Kalkulasi
                        </button>
                    </li>
                </ul>

                {{-- ==================== TAB CONTENT ==================== --}}
                <div class="tab-content" id="slipGajiTabContent">

                    {{-- ========== TAB 1: DATA KARYAWAN ========== --}}
                    <div class="tab-pane fade show active" id="tab-karyawan" role="tabpanel"
                        aria-labelledby="tab-karyawan-tab">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nama Karyawan <span
                                        class="text-danger">*</span></label>
                                <select name="id_karyawan" id="id_karyawan" class="form-select" required>
                                    <option value="">-- select employee --</option>
                                    @foreach ($karyawans as $emp)
                                        <option value="{{ $emp->id_karyawan }}" {{ old('id_karyawan') == $emp->id_karyawan ? 'selected' : '' }}>
                                            {{ $emp->nama_karyawan }} ({{ $emp->nip }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bulan <span class="text-danger">*</span></label>
                                <select name="bulan" id="bulan" class="form-select" required>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('bulan', date('m')) == $i ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                <input type="number" name="tahun" id="tahun" class="form-control" required
                                    value="{{ old('tahun', date('Y')) }}">
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-muted">Tanggal Masuk</label>
                                <input type="text" id="karyawan_tanggal_masuk" class="form-control bg-light" readonly
                                    placeholder="-">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">NIP</label>
                                <input type="text" id="karyawan_nip" class="form-control bg-light" readonly placeholder="-">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Divisi</label>
                                <input type="text" id="karyawan_divisi" class="form-control bg-light" readonly
                                    placeholder="-">
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-muted">Klinik / Cabang</label>
                                <input type="text" id="karyawan_klinik" class="form-control bg-light" readonly
                                    placeholder="-">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">WhatsApp</label>
                                <input type="text" id="karyawan_no_wa" class="form-control bg-light" readonly
                                    placeholder="-">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Nomor Rekening</label>
                                <input type="text" id="karyawan_nomor_rekening" class="form-control bg-light" readonly
                                    placeholder="-">
                            </div>
                        </div>

                        {{-- Section Status Resign --}}
                        <div class="row g-3 mb-4 border-top pt-3 mt-2">
                            <div class="col-md-5">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_resign" id="is_resign"
                                        value="1" {{ old('is_resign') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-danger" for="is_resign">
                                        <i class="ti ti-user-minus me-1"></i> Slip Gaji Karyawan Resign
                                    </label>
                                </div>
                                <div class="form-text text-muted">Centang jika ini adalah pembuatan slip gaji untuk karyawan
                                    yang resign.</div>
                            </div>
                            <div class="col-md-4" id="col_tanggal_resign"
                                style="{{ old('is_resign') ? '' : 'display:none;' }}">
                                <label class="form-label fw-semibold text-danger">Tanggal Terakhir Bekerja / Resign <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_resign" id="tanggal_resign"
                                    class="form-control border-danger" value="{{ old('tanggal_resign') }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-primary btn-next-tab" data-next="tab-pendapatan-tab">
                                Selanjutnya <i class="ti ti-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ========== TAB 2: PENDAPATAN & TUNJANGAN ========== --}}
                    <div class="tab-pane fade" id="tab-pendapatan" role="tabpanel" aria-labelledby="tab-pendapatan-tab">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gaji Pokok <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="gaji_pokok" id="gaji_pokok"
                                        class="form-control entry-calc entry-calc-rupiah" required
                                        value="{{ old('gaji_pokok', 0) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Tunjangan Pengalaman Kerja</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="t_pengalaman_kerja" id="t_pengalaman_kerja"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('t_pengalaman_kerja', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tunjangan Jabatan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="t_jabatan" id="t_jabatan"
                                        class="form-control entry-calc entry-calc-rupiah" value="{{ old('t_jabatan', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tunjangan Profesi</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="t_profesi" id="t_profesi"
                                        class="form-control entry-calc entry-calc-rupiah" value="{{ old('t_profesi', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tunjangan Operasional</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="t_operasional" id="t_operasional"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('t_operasional', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tunjangan Kehadiran</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="t_kehadiran" id="t_kehadiran"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('t_kehadiran', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tunjangan Kinerja</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="t_kinerja" id="t_kinerja"
                                        class="form-control entry-calc entry-calc-rupiah" value="{{ old('t_kinerja', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tunjangan Hari Raya</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="t_hari_raya" id="t_hari_raya"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('t_hari_raya', 0) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Fee Beautician</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="fee_beautician" id="fee_beautician"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('fee_beautician', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nominal Lembur</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="lembur" id="nominal_lembur"
                                        class="form-control entry-calc entry-calc-rupiah" value="{{ old('lembur', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Lain - lain</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="lain_lain" id="lain_lain"
                                        class="form-control entry-calc entry-calc-rupiah" value="{{ old('lain_lain', 0) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Prosentase Gaji (%)</label>
                                <input type="number" step="0.01" name="prosentase_gaji" id="prosentase_gaji"
                                    class="form-control entry-calc" value="{{ old('prosentase_gaji', 100) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jumlah Hari Gabung</label>
                                <input type="number" name="jumlah_hari_gabung" id="jumlah_hari_gabung" class="form-control"
                                    value="{{ old('jumlah_hari_gabung', 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Penyesuaian Gaji Lalu</label>
                                <input type="number" name="penyesuaian_gaji_lalu" id="penyesuaian_gaji_lalu"
                                    class="form-control" value="{{ old('penyesuaian_gaji_lalu', 0) }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-outline-secondary btn-prev-tab"
                                data-prev="tab-karyawan-tab">
                                <i class="ti ti-arrow-left me-1"></i> Sebelumnya
                            </button>
                            <button type="button" class="btn btn-primary btn-next-tab" data-next="tab-potongan-tab">
                                Selanjutnya <i class="ti ti-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ========== TAB 3: POTONGAN & PENGURANGAN ========== --}}
                    <div class="tab-pane fade" id="tab-potongan" role="tabpanel" aria-labelledby="tab-potongan-tab">

                        {{-- A. BPJS Ketenagakerjaan --}}
                        <div class="section-subheader section-subheader-bpjstk">
                            <i class="ti ti-shield-check me-1"></i> A. BPJS Ketenagakerjaan
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">No. Referensi</label>
                                <input type="text" name="bpjsk_no_ref" id="bpjsk_no_ref" class="form-control bg-light"
                                    readonly value="{{ old('bpjsk_no_ref') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Kepesertaan</label>
                                <input type="text" id="bpjstk_tanggal_kepesertaan" class="form-control bg-light" readonly
                                    placeholder="-">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Upah yg didaftarkan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjsk_upah_daftar" id="bpjsk_upah_daftar"
                                        class="form-control entry-calc entry-calc-rupiah bg-light" readonly
                                        value="{{ old('bpjsk_upah_daftar', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Iuran JKK</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjsk_iuran_jkk" id="bpjsk_iuran_jkk"
                                        class="form-control entry-calc entry-calc-rupiah bg-light" readonly
                                        value="{{ old('bpjsk_iuran_jkk', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Iuran JKM</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjsk_iuran_jkm" id="bpjsk_iuran_jkm"
                                        class="form-control entry-calc entry-calc-rupiah bg-light" readonly
                                        value="{{ old('bpjsk_iuran_jkm', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Iuran JHT Pemberi Kerja</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjstk_iuran_jht_pk" id="bpjstk_iuran_jht_pk"
                                        class="form-control entry-calc entry-calc-rupiah bg-light" readonly
                                        value="{{ old('bpjstk_iuran_jht_pk', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Iuran JHT Tenaga Kerja</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjstk_iuran_jht_tk" id="bpjstk_iuran_jht_tk"
                                        class="form-control entry-calc entry-calc-rupiah bg-light" readonly
                                        value="{{ old('bpjstk_iuran_jht_tk', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Total</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjstk_total" id="bpjstk_total"
                                        class="form-control entry-calc entry-calc-rupiah bg-light fw-bold" readonly
                                        value="{{ old('bpjstk_total', 0) }}">
                                </div>
                            </div>
                        </div>

                        {{-- B. BPJS Kesehatan --}}
                        <div class="section-subheader section-subheader-bpjsk">
                            <i class="ti ti-heart-rate-monitor me-1"></i> B. BPJS Kesehatan
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">No JKN Peserta</label>
                                <input type="text" name="bpjsk_no_jkn" id="bpjsk_no_jkn" class="form-control bg-light"
                                    readonly value="{{ old('bpjsk_no_jkn') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Beban BPJS Kesehatan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjsk_beban" id="bpjsk_beban"
                                        class="form-control entry-calc entry-calc-rupiah bg-light" readonly
                                        value="{{ old('bpjsk_beban', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">NPP</label>
                                <input type="text" name="bpjsk_npp" id="bpjsk_npp" class="form-control bg-light" readonly
                                    value="{{ old('bpjsk_npp') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Upah yang didaftarkan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjsk_upah" id="bpjsk_upah"
                                        class="form-control entry-calc entry-calc-rupiah bg-light" readonly
                                        value="{{ old('bpjsk_upah', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Premi</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjsk_premi" id="bpjsk_premi"
                                        class="form-control entry-calc entry-calc-rupiah bg-light" readonly
                                        value="{{ old('bpjsk_premi', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggungan Perusahaan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjsk_tg_gaji" id="bpjsk_tg_gaji"
                                        class="form-control entry-calc entry-calc-rupiah bg-light" readonly
                                        value="{{ old('bpjsk_tg_gaji', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggungan Karyawan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjsk_tg_karyawan" id="bpjsk_tg_karyawan"
                                        class="form-control entry-calc entry-calc-rupiah bg-light" readonly
                                        value="{{ old('bpjsk_tg_karyawan', 0) }}">
                                </div>
                            </div>
                        </div>

                        {{-- C. PPH 21 --}}
                        <div class="section-subheader section-subheader-pph21">
                            <i class="ti ti-receipt-tax me-1"></i> C. PPH 21
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">NPWP/KTP</label>
                                <input type="text" name="pph21_npwp" id="pph21_npwp" class="form-control bg-light" readonly
                                    value="{{ old('pph21_npwp') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">PTKP</label>
                                <div class="input-group">
                                    {{-- <span class="input-group-text">Rp</span> --}}
                                    <input type="text" name="pph21_ptkp" id="pph21_ptkp"
                                        class="form-control entry-calc bg-light" readonly
                                        value="{{ old('pph21_ptkp', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-4">

                                <label class="form-label">Kategori</label>
                                <div class="input-group">
                                    <input class="form-control entry-calc bg-light" type="text" name="pph21_kategori"
                                        id="pph21_kategori" value="{{ old('pph21_kategori') }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">TER (%)</label>
                                <input type="text" id="pph21_ter" class="form-control bg-light fw-bold" readonly
                                    placeholder="0" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tarif Persentase (%)</label>
                                <input type="text" id="pph21_tarif_persentase" class="form-control bg-light fw-bold"
                                    readonly placeholder="0" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">PPH 21</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="pph_21" id="pph_21"
                                        class="form-control entry-calc entry-calc-rupiah bg-light fw-bold" readonly
                                        value="{{ old('pph_21', 0) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Potongan lainnya --}}
                        <div class="section-subheader section-subheader-lainnya">
                            <i class="ti ti-minus me-1"></i> D. Potongan Lainnya
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Punishment</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="punishment" id="punishment"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('punishment', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sedekah Rombongan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="sedekah_rombongan" id="sedekah_rombongan"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('sedekah_rombongan', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Potongan Lainnya</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="potongan_lainnya" id="potongan_lainnya"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('potongan_lainnya', 0) }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-outline-secondary btn-prev-tab"
                                data-prev="tab-pendapatan-tab">
                                <i class="ti ti-arrow-left me-1"></i> Sebelumnya
                            </button>
                            <button type="button" class="btn btn-primary btn-next-tab" data-next="tab-kehadiran-tab">
                                Selanjutnya <i class="ti ti-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ========== TAB 4: KEHADIRAN & STATUS ========== --}}
                    <div class="tab-pane fade" id="tab-kehadiran" role="tabpanel" aria-labelledby="tab-kehadiran-tab">
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Lembur (Kali)</label>
                                <input type="number" name="lembur_kali" id="lembur_kali" class="form-control"
                                    value="{{ old('lembur_kali', 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Lembur (Menit)</label>
                                <input type="number" name="lembur_menit" id="lembur_menit" class="form-control"
                                    value="{{ old('lembur_menit', 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Terlambat (Kali)</label>
                                <input type="number" name="terlambat" id="terlambat" class="form-control"
                                    value="{{ old('terlambat', 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Terlambat (Menit)</label>
                                <input type="number" name="terlambat_menit" id="terlambat_menit" class="form-control"
                                    value="{{ old('terlambat_menit', 0) }}">
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Ijin Pulang Cepat</label>
                                <input type="number" name="ijin_pulang_cepat" id="ijin_pulang_cepat" class="form-control"
                                    value="{{ old('ijin_pulang_cepat', 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ijin Tdk Masuk</label>
                                <input type="number" name="ijin_tidak_masuk" id="ijin_tidak_masuk" class="form-control"
                                    value="{{ old('ijin_tidak_masuk', 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">No Check In/Out</label>
                                <input type="number" name="no_check_in_or_out" id="no_check_in_or_out" class="form-control"
                                    value="{{ old('no_check_in_or_out', 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">No Check In & Out</label>
                                <input type="number" name="no_check_in_and_out" id="no_check_in_and_out"
                                    class="form-control" value="{{ old('no_check_in_and_out', 0) }}">
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Cuti (Hari)</label>
                                <input type="number" name="cuti" id="cuti" class="form-control"
                                    value="{{ old('cuti', 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kehadiran Lainnya</label>
                                <input type="number" name="kehadiran_lainnya" id="kehadiran_lainnya" class="form-control"
                                    value="{{ old('kehadiran_lainnya', 0) }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-outline-secondary btn-prev-tab"
                                data-prev="tab-potongan-tab">
                                <i class="ti ti-arrow-left me-1"></i> Sebelumnya
                            </button>
                            <button type="button" class="btn btn-primary btn-next-tab" data-next="tab-ringkasan-tab">
                                Selanjutnya <i class="ti ti-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ========== TAB 5: RINGKASAN & KALKULASI AKHIR ========== --}}
                    <div class="tab-pane fade" id="tab-ringkasan" role="tabpanel" aria-labelledby="tab-ringkasan-tab">

                        <!-- RESIGN PRORATA BREAKDOWN ALERT -->
                        <div id="resign_info_container" class="alert alert-warning border border-warning shadow-sm mb-4"
                            style="display: none;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ti ti-user-minus text-warning fs-4 me-2"></i>
                                <h6 class="mb-0 fw-bold text-dark">Rincian Perhitungan Pro-Rata Gaji Karyawan Resign</h6>
                            </div>
                            <div id="resign_detail_breakdown"></div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <div class="summary-card summary-card-income">
                                    <div class="summary-label text-success">Subtotal Penerimaan</div>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-success">Rp</span>
                                        <input type="text" id="calculated_penerimaan"
                                            class="form-control bg-transparent border-success text-end fw-bold text-success fs-5"
                                            readonly value="0">
                                    </div>
                                    <div class="mt-2 p-2 rounded bg-white bg-opacity-50 border border-success border-opacity-25 text-start"
                                        style="font-size: 0.78rem; line-height: 1.4;">
                                        <div
                                            class="fw-semibold text-success mb-1 pb-1 border-bottom border-success border-opacity-25">
                                            <i class="ti ti-list-details me-1"></i> Rincian Penerimaan:
                                        </div>
                                        <div id="penerimaan_detail_list" class="text-secondary">
                                            <div class="text-muted fst-italic py-1">Belum ada data</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card summary-card-deduction">
                                    <div class="summary-label text-danger">Subtotal Potongan</div>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-danger">Rp</span>
                                        <input type="text" id="calculated_potongan"
                                            class="form-control bg-transparent border-danger text-end fw-bold text-danger fs-5"
                                            readonly value="0">
                                    </div>
                                    <div class="mt-2 p-2 rounded bg-white bg-opacity-50 border border-danger border-opacity-25 text-start"
                                        style="font-size: 0.78rem; line-height: 1.4;">
                                        <div
                                            class="fw-semibold text-danger mb-1 pb-1 border-bottom border-danger border-opacity-25">
                                            <i class="ti ti-list-details me-1"></i> Rincian Potongan:
                                        </div>
                                        <div id="potongan_detail_list" class="text-secondary">
                                            <div class="text-muted fst-italic py-1">Belum ada data</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-card summary-card-thp">
                                    <div class="summary-label text-primary">Nominal Transfer / THP</div>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-primary">Rp</span>
                                        <input type="text" name="nominal_transfer" id="nominal_transfer"
                                            class="form-control bg-transparent border-primary fw-bold text-end text-primary fs-5 entry-calc-rupiah"
                                            required value="{{ old('nominal_transfer', 0) }}">
                                    </div>
                                    <input type="hidden" name="bpjstk" id="bpjstk" value="{{ old('bpjstk', 0) }}">
                                    <input type="hidden" name="bpjsk" id="bpjsk" value="{{ old('bpjsk', 0) }}">
                                    <input type="hidden" name="potongan_bpjs_tk" id="potongan_bpjs_tk"
                                        value="{{ old('potongan_bpjs_tk', 0) }}">
                                    <input type="hidden" name="potongan_bpjs_kesehatan" id="potongan_bpjs_kesehatan"
                                        value="{{ old('potongan_bpjs_kesehatan', 0) }}">
                                    <input type="hidden" name="potongan_pph_21" id="potongan_pph_21"
                                        value="{{ old('potongan_pph_21', 0) }}">
                                    <input type="hidden" name="thp" id="thp" value="{{ old('thp', 0) }}">
                                    <div class="mt-2 p-2 rounded bg-white bg-opacity-50 border border-primary border-opacity-25 text-start"
                                        style="font-size: 0.78rem; line-height: 1.4;">
                                        <div
                                            class="fw-semibold text-primary mb-1 pb-1 border-bottom border-primary border-opacity-25">
                                            <i class="ti ti-calculator me-1"></i> Rincian Perhitungan THP:
                                        </div>
                                        <div id="thp_detail_list" class="text-secondary">
                                            <div class="text-muted fst-italic py-1">Belum ada data</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-4">
                            <button type="button" class="btn btn-outline-secondary btn-prev-tab"
                                data-prev="tab-kehadiran-tab">
                                <i class="ti ti-arrow-left me-1"></i> Sebelumnya
                            </button>
                            <div>
                                <a href="{{ route('slip-gaji.index') }}" class="btn btn-light btn-lg px-4 me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="ti ti-device-floppy me-1"></i> Save Slip Gaji
                                </button>
                            </div>
                        </div>
                        {{-- ===== PANEL: GAJI TERAKHIR (RESIGN) ===== --}}
                        <!-- <div class="card border-warning mt-4" id="panelResign">
                                <div class="card-header bg-warning bg-opacity-10 text-warning d-flex align-items-center gap-2">
                                    <i class="ti ti-user-minus fs-5"></i>
                                    <strong>Hitung Gaji Terakhir (Resign)</strong>
                                    <span class="ms-auto badge bg-warning text-dark">Opsional</span>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">Gunakan kalkulator ini untuk menghitung gaji terakhir
                                        karyawan yang resign, berdasarkan tanggal resign dan periode cut-off.</p>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Tanggal Resign</label>
                                            <input type="date" id="resign_tanggal_resign" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">THP Full (Otomatis)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" id="resign_thp_full" class="form-control bg-light" readonly
                                                    placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="button" id="btnHitungResign" class="btn btn-warning w-100">
                                                <i class="ti ti-calculator me-1"></i> Hitung Gaji Resign
                                            </button>
                                        </div>
                                    </div>
                                    <div id="resignResult" class="mt-3" style="display:none">
                                        <hr>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label text-muted">Periode</label>
                                                <input type="text" id="resign_info_periode" class="form-control bg-light"
                                                    readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label text-muted">Total Hari</label>
                                                <input type="text" id="resign_info_total_hari" class="form-control bg-light"
                                                    readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label text-muted">Hari Kerja</label>
                                                <input type="text" id="resign_info_hari_kerja" class="form-control bg-light"
                                                    readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-muted">Akhir Training</label>
                                                <input type="text" id="resign_info_akhir_training" class="form-control bg-light"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-1">
                                            <div class="col-md-4">
                                                <label class="form-label text-muted">Skenario</label>
                                                <input type="text" id="resign_info_skenario"
                                                    class="form-control bg-light fw-bold" readonly>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label fw-semibold text-warning">Gaji Terakhir
                                                    (Resign)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text border-warning">Rp</span>
                                                    <input type="text" id="resign_gaji_result"
                                                        class="form-control fw-bold fs-5 border-warning text-warning" readonly
                                                        placeholder="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        {{-- ===== END PANEL RESIGN ===== --}}
                    </div>

                </div>{{-- end .tab-content --}}
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // ==================== TAB NAVIGATION ====================
            $(document).on('click', '.btn-next-tab', function () {
                var nextTab = $(this).data('next');
                $('#' + nextTab).tab('show');
            });
            $(document).on('click', '.btn-prev-tab', function () {
                var prevTab = $(this).data('prev');
                $('#' + prevTab).tab('show');
            });

            // ==================== HELPERS ====================
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

            function getRawValue(selector) {
                var valString = $(selector).val() || '0';
                var clean = valString.replace(/\./g, '');
                return parseFloat(clean) || 0;
            }

            // Format saat user mengetik di field Rupiah
            $(document).on('input', '.entry-calc-rupiah', function () {
                var el = this;
                if ($(el).attr('readonly')) return; // skip readonly fields
                var rawVal = el.value.replace(/\./g, '').replace(/[^0-9]/g, '');
                var formatted = rawVal === '' ? '0' : parseInt(rawVal, 10).toString().replace(
                    /\B(?=(\d{3})+(?!\d))/g, '.');

                var oldLen = el.value.length;
                el.value = formatted;
                var newLen = formatted.length;
                var pos = el.selectionStart + (newLen - oldLen);
                el.setSelectionRange(Math.max(0, pos), Math.max(0, pos));
            });

            // ==================== FETCH EMPLOYEE DETAILS ====================
            function fetchEmployeeDetails() {
                var empId = $('#id_karyawan').val();
                var bulan = $('#bulan').val();
                var tahun = $('#tahun').val();

                if (!empId) {
                    // Clear all fields
                    $('#karyawan_tanggal_masuk, #karyawan_nip, #karyawan_divisi, #karyawan_klinik, #karyawan_no_wa, #karyawan_nomor_rekening')
                        .val('');
                    return;
                }

                $.ajax({
                    url: '/slip-gaji/karyawan-details/' + empId,
                    type: 'GET',
                    data: {
                        bulan: bulan,
                        tahun: tahun
                    },
                    success: function (data) {
                        // --- Tab 1: Data Karyawan ---
                        $('#karyawan_tanggal_masuk').val(data.karyawan.tanggal_masuk || '-');
                        $('#karyawan_nip').val(data.karyawan.nip || '-');
                        $('#karyawan_divisi').val(data.karyawan.divisi);
                        $('#karyawan_klinik').val(data.karyawan.cabang);
                        $('#karyawan_no_wa').val(data.karyawan.no_wa);
                        $('#karyawan_nomor_rekening').val(data.karyawan.nomor_rekening);

                        // --- Tab 2: Pendapatan & Tunjangan ---
                        if (data.gaji) {
                            $('#gaji_pokok').val(formatRupiah(data.gaji.gaji_pokok));
                            $('#t_pengalaman_kerja').val(formatRupiah(data.gaji.t_pengalaman_kerja));
                            $('#t_jabatan').val(formatRupiah(data.gaji.t_jabatan));
                            $('#t_profesi').val(formatRupiah(data.gaji.t_profesi));
                            $('#t_kehadiran').val(formatRupiah(data.gaji.t_kehadiran));
                            $('#t_kinerja').val(formatRupiah(data.gaji.t_kinerja));
                            $('#t_operasional').val(formatRupiah(data.gaji.t_operasional));
                        } else {
                            $('#gaji_pokok, #t_pengalaman_kerja, #t_jabatan, #t_profesi, #t_kehadiran, #t_kinerja, #t_operasional')
                                .val("0");
                        }

                        // --- Tab 3: Potongan - BPJS TK ---
                        if (data.bpjstk) {
                            $('#bpjsk_no_ref').val(data.bpjstk.no_referensi || '');
                            $('#bpjstk_tanggal_kepesertaan').val(data.bpjstk.tanggal_kepesertaan ||
                                '-');
                            $('#bpjsk_upah_daftar').val(formatRupiah(data.bpjstk.upah_didaftarkan));
                            $('#bpjsk_iuran_jkk').val(formatRupiah(data.bpjstk.iuran_jkk));
                            $('#bpjsk_iuran_jkm').val(formatRupiah(data.bpjstk.iuran_jkm));
                            $('#bpjstk_iuran_jht_pk').val(formatRupiah(data.bpjstk.pemberi_kerja));
                            $('#bpjstk_iuran_jht_tk').val(formatRupiah(data.bpjstk.tenaga_kerja));
                            $('#bpjstk_total').val(formatRupiah(data.bpjstk.total_iuran));
                        } else {
                            $('#bpjsk_no_ref, #bpjstk_tanggal_kepesertaan').val('');
                            $('#bpjsk_upah_daftar, #bpjsk_iuran_jkk, #bpjsk_iuran_jkm, #bpjstk_iuran_jht_pk, #bpjstk_iuran_jht_tk, #bpjstk_total')
                                .val('0');
                        }

                        // --- Tab 3: Potongan - BPJS Kesehatan ---
                        if (data.bpjsk) {
                            $('#bpjsk_no_jkn').val(data.bpjsk.no_jkn_peserta || '');
                            $('#bpjsk_beban').val(formatRupiah(data.bpjsk.beban_bpjsk));
                            $('#bpjsk_npp').val(data.bpjsk.npp || '');
                            $('#bpjsk_upah').val(formatRupiah(data.bpjsk.upah_didaftarkan));
                            $('#bpjsk_premi').val(formatRupiah(data.bpjsk.premi));
                            $('#bpjsk_tg_gaji').val(formatRupiah(data.bpjsk.tanggungan_perusahaan));
                            $('#bpjsk_tg_karyawan').val(formatRupiah(data.bpjsk.tanggungan_karyawan));
                        } else {
                            $('#bpjsk_no_jkn, #bpjsk_npp').val('');
                            $('#bpjsk_beban, #bpjsk_upah, #bpjsk_premi, #bpjsk_tg_gaji, #bpjsk_tg_karyawan')
                                .val('0');
                        }

                        // --- Tab 3: Potongan - PPH21 ---
                        if (data.pph21) {
                            $('#pph21_npwp').val(data.pph21.identitas || '');
                            $('#pph21_ptkp').val(data.pph21.ptkp);
                            // Set the select and hidden field
                            $('#pph21_kategori').val(data.pph21.kategori || '');
                        } else {
                            $('#pph21_npwp').val('');
                            $('#pph21_ptkp').val('0');
                            $('#pph21_kategori').val('')
                        }

                        // --- Tab 3: Potongan sedekah rombongan ---
                        if (data.potongan) {
                            $('#sedekah_rombongan').val(formatRupiah(data.potongan
                                .potongan_sedekah_rombongan));
                        } else {
                            $('#sedekah_rombongan').val("0");
                        }

                        // --- Tab 4: Kehadiran ---
                        if (data.kehadiran) {
                            $('#cuti').val(data.kehadiran.cuti || 0);
                            $('#lembur_kali').val(data.kehadiran.lembur || 0);
                            $('#lembur_menit').val(data.kehadiran.lembur_menit || 0);
                            $('#terlambat').val(data.kehadiran.terlambat || 0);
                            $('#terlambat_menit').val(data.kehadiran.terlambat_menit || 0);
                            $('#ijin_pulang_cepat').val(data.kehadiran.ijin_pulang_cepat || 0);
                            $('#ijin_tidak_masuk').val(data.kehadiran.ijin_tidak_masuk || 0);
                            $('#no_check_in_or_out').val(data.kehadiran.no_check_in_or_out || 0);
                            $('#no_check_in_and_out').val(data.kehadiran.no_check_in_and_out || 0);
                        } else {
                            $('#cuti, #lembur_kali, #lembur_menit, #terlambat, #terlambat_menit, #ijin_pulang_cepat, #ijin_tidak_masuk, #no_check_in_or_out, #no_check_in_and_out')
                                .val(0);
                        }

                        // Simpan data training untuk dipakai calculateReceipt()
                        window._karyawanTanggalMasuk = data.karyawan.tanggal_masuk || null;
                        window._karyawanAkhirTraining = data.karyawan.akhir_training || null;
                        window._cutoffDate = data.karyawan.periode_cut_off || 21;

                        // --- Hitung jumlah hari gabung (dari tanggal masuk ke hari ini) ---
                        if (data.karyawan.tanggal_masuk && data.karyawan.tanggal_masuk !== '-') {
                            var tglMasuk = new Date(data.karyawan.tanggal_masuk);
                            var today = new Date();
                            var diffMs = today - tglMasuk;
                            var diffHari = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                            $('#jumlah_hari_gabung').val(diffHari);
                        } else {
                            $('#jumlah_hari_gabung').val(0);
                        }

                        calculateReceipt();
                    },
                    error: function () {
                        console.error('Failed to retrieve employee details');
                    }
                });
            }

            $('#id_karyawan').on('change', fetchEmployeeDetails);
            $('#bulan, #tahun').on('change', function () {
                if ($('#id_karyawan').val()) {
                    fetchEmployeeDetails();
                }
            });

            // ==================== PPH21 CALCULATION ====================
            var pphTimeout;
            function calculatePph21(totalGajiVal, totalDeductions) {
                var kategori = $('#pph21_kategori').val();
                if (!kategori) {
                    $('#pph21_ter').val('0');
                    $('#pph21_tarif_persentase').val('0');
                    $('#pph_21').val('0');
                    $('#potongan_pph_21').val('0');
                    updateFinalTransfer(totalGajiVal, 0, totalDeductions);
                    return;
                }
                $.ajax({
                    url: '/slip-gaji/calculate-pph21',
                    type: 'GET',
                    data: {
                        kategori: kategori,
                        total_gaji: totalGajiVal
                    },
                    success: function (result) {
                        $('#pph21_ter').val(result.ter * 100);
                        $('#pph21_tarif_persentase').val(result.ter * 100);
                        var pph21Value = Math.round(result.pph21);
                        $('#pph_21').val(formatRupiah(pph21Value));
                        $('#potongan_pph_21').val(pph21Value);
                        updateFinalTransfer(totalGajiVal, pph21Value, totalDeductions);
                    },
                    error: function () {
                        console.error('Failed to calculate PPH21');
                    }
                });
            }

            // totalDeductions = punishment + sedekah + potongan_lainnya (without PPh21)
            function updateFinalTransfer(totalGajiVal, pph21Value, totalDeductions) {
                var bpjstkTotal = getRawValue('#bpjstk_total') || getRawValue('#bpjstk');
                var bpjskPremi = getRawValue('#bpjsk_premi') || getRawValue('#bpjsk');
                var netTransfer = totalGajiVal - totalDeductions - pph21Value - bpjstkTotal - bpjskPremi;
                if (netTransfer < 0) netTransfer = 0;
                $('#nominal_transfer').val(formatRupiah(netTransfer));
                $('#thp').val(netTransfer);

                // Subtotal Potongan = Punishment + Sedekah + Potongan Lainnya + PPh21
                var subtotalPotongan = totalDeductions + pph21Value;
                $('#calculated_potongan').val(subtotalPotongan.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }));
                renderBreakdownDetails();
            }

            function renderBreakdownDetails() {
                // === 1. RINCIAN PENERIMAAN ===
                var gaji = getRawValue('#gaji_pokok');
                var t_pengalaman = getRawValue('#t_pengalaman_kerja');
                var t_jabatan = getRawValue('#t_jabatan');
                var t_profesi = getRawValue('#t_profesi');
                var t_operasional = getRawValue('#t_operasional');
                var t_hadir = getRawValue('#t_kehadiran');
                var t_kinerja = getRawValue('#t_kinerja');
                var t_hari_raya = getRawValue('#t_hari_raya');
                var fee_beautician = getRawValue('#fee_beautician');
                var lembur = getRawValue('#nominal_lembur');
                var lain = getRawValue('#lain_lain');

                var totalCalcPenerimaan = getRawValue('#calculated_penerimaan');
                var prorataBase = gaji + t_jabatan + t_profesi + t_hadir + t_kinerja + t_operasional;
                var nonProrata = t_pengalaman + t_hari_raya + fee_beautician + lembur + lain;
                var prorataNominal = totalCalcPenerimaan - nonProrata;
                if (prorataNominal < 0) prorataNominal = 0;
                var factor = prorataBase > 0 ? (prorataNominal / prorataBase) : 1;

                var penerimaanItems = [
                    { label: 'Gaji Pokok', val: gaji, isProrata: true },
                    { label: 'T. Pengalaman Kerja', val: t_pengalaman, isProrata: false },
                    { label: 'T. Jabatan', val: t_jabatan, isProrata: true },
                    { label: 'T. Profesi', val: t_profesi, isProrata: true },
                    { label: 'T. Operasional', val: t_operasional, isProrata: true },
                    { label: 'T. Kehadiran', val: t_hadir, isProrata: true },
                    { label: 'T. Kinerja', val: t_kinerja, isProrata: true },
                    { label: 'T. Hari Raya', val: t_hari_raya, isProrata: false },
                    { label: 'Fee Beautician', val: fee_beautician, isProrata: false },
                    { label: 'Nominal Lembur', val: lembur, isProrata: false },
                    { label: 'Lain-lain', val: lain, isProrata: false }
                ];

                var penerimaanHtml = '';
                var activePenerimaanCount = 0;

                penerimaanItems.forEach(function (item) {
                    if (item.val > 0) {
                        activePenerimaanCount++;
                        var adjustedVal = item.isProrata ? Math.round(item.val * factor) : Math.round(item.val);
                        penerimaanHtml += '<div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light text-start">' +
                            '<span class="text-secondary">' + item.label + '</span>' +
                            '<span class="fw-semibold text-dark">Rp ' + formatRupiah(adjustedVal) + '</span>' +
                            '</div>';
                    }
                });

                if (activePenerimaanCount === 0) {
                    penerimaanHtml = '<div class="text-muted fst-italic py-1 text-start">Tidak ada penerimaan</div>';
                } else if (factor < 0.999 && factor > 0) {
                    var pctStr = (factor * 100).toFixed(1).replace('.0', '');
                    penerimaanHtml += '<div class="text-primary fst-italic mt-1 text-start" style="font-size:0.72rem;">* Nilai telah disesuaikan prorata (' + pctStr + '%)</div>';
                }

                $('#penerimaan_detail_list').html(penerimaanHtml);

                // === 2. RINCIAN POTONGAN ===
                var punishment = getRawValue('#punishment');
                var sedekah = getRawValue('#sedekah_rombongan');
                var pot_lainnya = getRawValue('#potongan_lainnya');
                var totalDeductions = punishment + sedekah + pot_lainnya;
                var pph21 = getRawValue('#potongan_pph_21') || getRawValue('#pph_21');

                var potonganItems = [
                    { label: 'Punishment', val: punishment },
                    { label: 'Sedekah Rombongan', val: sedekah },
                    { label: 'Potongan Lainnya', val: pot_lainnya },
                    { label: 'Potongan PPh 21', val: pph21 }
                ];

                var potonganHtml = '';
                var activePotonganCount = 0;

                potonganItems.forEach(function (item) {
                    if (item.val > 0) {
                        activePotonganCount++;
                        potonganHtml += '<div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light text-start">' +
                            '<span class="text-secondary">' + item.label + '</span>' +
                            '<span class="fw-semibold text-danger">Rp ' + formatRupiah(item.val) + '</span>' +
                            '</div>';
                    }
                });

                if (activePotonganCount === 0) {
                    potonganHtml = '<div class="text-muted fst-italic py-1 text-start">Tidak ada potongan</div>';
                }

                $('#potongan_detail_list').html(potonganHtml);

                // === 3. RINCIAN PERHITUNGAN THP / NOMINAL TRANSFER ===
                var bpjstk_total = getRawValue('#bpjstk_total') || getRawValue('#bpjstk');
                var bpjsk_premi = getRawValue('#bpjsk_premi') || getRawValue('#bpjsk');
                var jht_tk = getRawValue('#bpjstk_iuran_jht_tk') || getRawValue('#potongan_bpjs_tk');
                var tg_karyawan = getRawValue('#bpjsk_tg_karyawan') || getRawValue('#potongan_bpjs_kesehatan');
                var netTransfer = getRawValue('#nominal_transfer');

                var thpHtml = '';
                thpHtml += '<div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light text-start">' +
                    '<span class="text-secondary">Subtotal Penerimaan</span>' +
                    '<span class="fw-semibold text-success">+ Rp ' + formatRupiah(totalCalcPenerimaan) + '</span>' +
                    '</div>';

                if (totalDeductions > 0) {
                    thpHtml += '<div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light text-start">' +
                        '<span class="text-secondary">Potongan Langsung</span>' +
                        '<span class="fw-semibold text-danger">- Rp ' + formatRupiah(totalDeductions) + '</span>' +
                        '</div>';
                }

                if (jht_tk > 0) {
                    thpHtml += '<div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light text-start">' +
                        '<span class="text-secondary">Potongan BPJS TK</span>' +
                        '<span class="fw-semibold text-danger">- Rp ' + formatRupiah(jht_tk) + '</span>' +
                        '</div>';
                }

                if (tg_karyawan > 0) {
                    thpHtml += '<div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light text-start">' +
                        '<span class="text-secondary">Potongan BPJS Kes</span>' +
                        '<span class="fw-semibold text-danger">- Rp ' + formatRupiah(tg_karyawan) + '</span>' +
                        '</div>';
                }

                if (pph21 > 0) {
                    thpHtml += '<div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light text-start">' +
                        '<span class="text-secondary">Potongan PPh 21</span>' +
                        '<span class="fw-semibold text-danger">- Rp ' + formatRupiah(pph21) + '</span>' +
                        '</div>';
                }

                thpHtml += '<div class="d-flex justify-content-between align-items-center pt-2 mt-1 text-start fw-bold text-primary">' +
                    '<span>Nominal Transfer</span>' +
                    '<span>Rp ' + formatRupiah(netTransfer) + '</span>' +
                    '</div>';

                $('#thp_detail_list').html(thpHtml);
            }

            // Sync disabled select to hidden input
            $('#pph21_kategori').on('change', function () {
                $('#pph21_kategori_hidden').val($(this).val());
                var totalReceipts = getRawValue('#calculated_penerimaan');
                var punishment = getRawValue('#punishment');
                var sedekah = getRawValue('#sedekah_rombongan');
                var pot_lainnya = getRawValue('#potongan_lainnya');
                var totalDeductions = punishment + sedekah + pot_lainnya;

                var bpjstk_total = getRawValue('#bpjstk_total');
                var bpjsk_premi = getRawValue('#bpjsk_premi');
                var jht_tk = getRawValue('#bpjstk_iuran_jht_tk');
                var tg_karyawan = getRawValue('#bpjsk_tg_karyawan');

                var totalGaji = totalReceipts + bpjstk_total + bpjsk_premi - jht_tk - tg_karyawan;
                if (totalGaji < 0) totalGaji = 0;
                calculatePph21(totalGaji, totalDeductions);
            });

            // ==================== TRAINING / PRO-RATA CALCULATION ====================
            /**
             * Menghitung total gaji untuk 1 periode dengan mempertimbangkan masa training.
             * Training = 80% THP_Full, Lulus training = 100% THP_Full.
             * Masa training = 3 bulan penuh setelah tanggal masuk (akhirTraining = masuk + 3 bulan - 1 hari).
             *
             * @param {Date}   tglMasuk
             * @param {Date}   akhirTraining
             * @param {Date}   periodeAwal
             * @param {Date}   periodeAkhir
             * @param {number} thpFull
             * @returns {number}
             */
            function hitungGajiPerPeriode(tglMasuk, akhirTraining, periodeAwal, periodeAkhir, thpFull) {
                var selisihHari = function (a, b) {
                    return Math.round((b - a) / (1000 * 60 * 60 * 24));
                };

                var totalHariPeriode = selisihHari(periodeAwal, periodeAkhir) + 1;

                // 1. Belum mulai bekerja di periode ini
                if (tglMasuk > periodeAkhir) {
                    return 0;
                }

                // Tanggal mulai hitung (antisipasi bulan pertama bergabung)
                var tglMulaiHitung = tglMasuk > periodeAwal ? tglMasuk : periodeAwal;

                // 2. SKENARIO A: MASA TRANSISI — akhirTraining jatuh di DALAM periode ini
                if (akhirTraining >= tglMulaiHitung && akhirTraining < periodeAkhir) {
                    var hariTraining = selisihHari(tglMulaiHitung, akhirTraining) + 1;
                    var gajiTraining = (hariTraining / totalHariPeriode) * 0.8 * thpFull;

                    var tglMulaiMaju = new Date(akhirTraining);
                    tglMulaiMaju.setDate(tglMulaiMaju.getDate() + 1);
                    var hariLulus = selisihHari(tglMulaiMaju, periodeAkhir) + 1;
                    var gajiLulus = (hariLulus / totalHariPeriode) * 1.0 * thpFull;

                    return gajiTraining + gajiLulus;
                }

                // 3. SKENARIO B: FULL TRAINING atau PRO-RATA awal masuk (masih dalam masa training)
                if (periodeAkhir <= akhirTraining) {
                    var hariKerja = selisihHari(tglMulaiHitung, periodeAkhir) + 1;
                    if (hariKerja === totalHariPeriode) {
                        return 0.8 * thpFull;
                    } else {
                        return (hariKerja / totalHariPeriode) * 0.8 * thpFull;
                    }
                }

                // 4. SKENARIO C: SUDAH LULUS TRAINING SEPENUHNYA
                var hariKerja2 = selisihHari(tglMulaiHitung, periodeAkhir) + 1;
                if (hariKerja2 === totalHariPeriode) {
                    return thpFull;
                } else {
                    return (hariKerja2 / totalHariPeriode) * 1.0 * thpFull;
                }
            }

            // ==================== KALKULASI PRO-RATA GAJI RESIGN ====================
            function calculateResignProrata() {
                var isResign = $('#is_resign').is(':checked');
                if (!isResign) {
                    $('#resign_info_container').slideUp(200);
                    return null;
                }

                var tglResignStr = $('#tanggal_resign').val();
                if (!tglResignStr) {
                    $('#resign_info_container').slideDown(200);
                    $('#resign_detail_breakdown').html('<div class="text-danger fst-italic py-1"><i class="ti ti-alert-circle me-1"></i>Harap isi Tanggal Terakhir Bekerja / Resign pada Tab Data Karyawan.</div>');
                    return null;
                }

                var tglMasukStr = window._karyawanTanggalMasuk || null;
                if (!tglMasukStr || tglMasukStr === '-') {
                    $('#resign_info_container').slideDown(200);
                    $('#resign_detail_breakdown').html('<div class="text-danger fst-italic py-1"><i class="ti ti-alert-circle me-1"></i>Data tanggal masuk karyawan tidak ditemukan.</div>');
                    return null;
                }

                var bulan = parseInt($('#bulan').val()) || new Date().getMonth() + 1;
                var tahun = parseInt($('#tahun').val()) || new Date().getFullYear();
                var cutoff = window._cutoffDate || 21;

                var gaji = getRawValue('#gaji_pokok');
                var t_pengalaman = getRawValue('#t_pengalaman_kerja');
                var t_jabatan = getRawValue('#t_jabatan');
                var t_profesi = getRawValue('#t_profesi');
                var t_hadir = getRawValue('#t_kehadiran');
                var t_kinerja = getRawValue('#t_kinerja');
                var t_hari_raya = getRawValue('#t_hari_raya');
                var t_operasional = getRawValue('#t_operasional');
                var fee_beautician = getRawValue('#fee_beautician');
                var lembur = getRawValue('#nominal_lembur');
                var lain = getRawValue('#lain_lain');

                var prorataBase = gaji + t_jabatan + t_profesi + t_hadir + t_kinerja + t_operasional;
                var nonProrata = t_pengalaman + t_hari_raya + fee_beautician + lembur + lain;

                var tglMasuk = new Date(tglMasukStr);
                var tglResign = new Date(tglResignStr);

                var akhirTraining = new Date(tglMasuk);
                akhirTraining.setMonth(akhirTraining.getMonth() + 3);
                akhirTraining.setDate(akhirTraining.getDate() - 1);

                var periodeAkhir = new Date(tahun, bulan - 1, cutoff);
                var periodeAwalTmp = new Date(tahun, bulan - 1, cutoff);
                periodeAwalTmp.setMonth(periodeAwalTmp.getMonth() - 1);
                periodeAwalTmp.setDate(periodeAwalTmp.getDate() + 1);
                var periodeAwal = periodeAwalTmp;

                var selisihHari = function (a, b) {
                    return Math.round((b - a) / (1000 * 60 * 60 * 24));
                };

                var totalHariPeriode = selisihHari(periodeAwal, periodeAkhir) + 1;

                var tglMulaiHitung = tglMasuk > periodeAwal ? new Date(tglMasuk) : new Date(periodeAwal);
                var tglAkhirHitung = tglResign < periodeAkhir ? new Date(tglResign) : new Date(periodeAkhir);

                var hariKerjaTotal = selisihHari(tglMulaiHitung, tglAkhirHitung) + 1;
                if (hariKerjaTotal < 0 || tglMasuk > periodeAkhir || tglResign < periodeAwal) {
                    hariKerjaTotal = 0;
                }

                var gajiResign = 0;
                var skenarioText = '';

                if (tglMasuk > periodeAkhir || tglResign < periodeAwal || hariKerjaTotal <= 0) {
                    gajiResign = 0;
                    skenarioText = 'Belum mulai / sudah selesai bekerja pada periode ini';
                } else if (akhirTraining >= tglMulaiHitung && akhirTraining < tglAkhirHitung) {
                    var hariTraining = selisihHari(tglMulaiHitung, akhirTraining) + 1;
                    var gajiTraining = (hariTraining / totalHariPeriode) * 0.8 * prorataBase;

                    var tglMulaiLulus = new Date(akhirTraining);
                    tglMulaiLulus.setDate(tglMulaiLulus.getDate() + 1);
                    var hariLulus = selisihHari(tglMulaiLulus, tglAkhirHitung) + 1;
                    var gajiLulus = (hariLulus / totalHariPeriode) * 1.0 * prorataBase;

                    gajiResign = gajiTraining + gajiLulus;
                    skenarioText = 'Skenario A — Masa Transisi (Training ' + hariTraining + ' hari [80%] + Lulus ' + hariLulus + ' hari [100%])';
                } else if (tglAkhirHitung <= akhirTraining) {
                    gajiResign = (hariKerjaTotal / totalHariPeriode) * 0.8 * prorataBase;
                    skenarioText = 'Skenario B — Masa Training (' + hariKerjaTotal + '/' + totalHariPeriode + ' hari [80%])';
                } else {
                    gajiResign = (hariKerjaTotal / totalHariPeriode) * 1.0 * prorataBase;
                    skenarioText = 'Skenario C — Lulus Training (' + hariKerjaTotal + '/' + totalHariPeriode + ' hari [100%])';
                }

                gajiResign = Math.round(gajiResign + nonProrata);

                var formatDateStr = function (d) {
                    if (!d || isNaN(d)) return '-';
                    var yyyy = d.getFullYear();
                    var mm = String(d.getMonth() + 1).padStart(2, '0');
                    var dd = String(d.getDate()).padStart(2, '0');
                    return dd + '/' + mm + '/' + yyyy;
                };

                var detailHtml = '<div class="row g-2 text-start" style="font-size: 0.82rem;">' +
                    '<div class="col-md-4"><span class="text-muted">Tanggal Resign:</span> <strong class="text-dark">' + formatDateStr(tglResign) + '</strong></div>' +
                    '<div class="col-md-4"><span class="text-muted">Periode Payroll:</span> <strong class="text-dark">' + formatDateStr(periodeAwal) + ' s/d ' + formatDateStr(periodeAkhir) + ' (' + totalHariPeriode + ' hari)</strong></div>' +
                    '<div class="col-md-4"><span class="text-muted">Hari Kerja Efektif:</span> <strong class="text-dark">' + hariKerjaTotal + ' hari</strong></div>' +
                    '<div class="col-md-4"><span class="text-muted">Akhir Training:</span> <strong class="text-dark">' + formatDateStr(akhirTraining) + '</strong></div>' +
                    '<div class="col-md-8"><span class="text-muted">Status Skenario:</span> <strong class="text-warning">' + skenarioText + '</strong></div>' +
                    '<div class="col-12 border-top pt-2 mt-1 d-flex justify-content-between align-items-center"><span class="fw-bold text-dark fs-6">Hasil Gaji Resign (Pro-rata):</span> <span class="fw-bold text-success fs-5">Rp ' + formatRupiah(gajiResign) + '</span></div>' +
                    '</div>';

                $('#resign_detail_breakdown').html(detailHtml);
                $('#resign_info_container').slideDown(200);

                return gajiResign;
            }

            $('#is_resign').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#col_tanggal_resign').slideDown(200);
                } else {
                    $('#col_tanggal_resign').slideUp(200);
                    $('#resign_info_container').slideUp(200);
                }
                calculateReceipt();
            });

            $('#tanggal_resign').on('change input', function () {
                calculateReceipt();
            });

            // ==================== RECEIPT CALCULATION ====================
            function calculateReceipt() {
                var gaji = getRawValue('#gaji_pokok');
                var t_pengalaman = getRawValue('#t_pengalaman_kerja');
                var t_jabatan = getRawValue('#t_jabatan');
                var t_profesi = getRawValue('#t_profesi');
                var t_hadir = getRawValue('#t_kehadiran');
                var t_kinerja = getRawValue('#t_kinerja');
                var t_hari_raya = getRawValue('#t_hari_raya');
                var t_operasional = getRawValue('#t_operasional');
                var fee_beautician = getRawValue('#fee_beautician');
                var lembur = getRawValue('#nominal_lembur');
                var lain = getRawValue('#lain_lain');

                var prorataBase = gaji + t_jabatan + t_profesi + t_hadir + t_kinerja + t_operasional;
                var nonProrata = t_pengalaman + t_hari_raya + fee_beautician + lembur + lain;

                // --- Hitung gaji dengan mempertimbangkan training & pro-rata ---
                var totalReceipts = prorataBase + nonProrata; // default: sudah lulus training, full bulan

                var bulan = parseInt($('#bulan').val()) || new Date().getMonth() + 1;
                var tahun = parseInt($('#tahun').val()) || new Date().getFullYear();
                var cutoff = window._cutoffDate || 21;

                // --- Periode berbasis cutoff ---
                // Akhir Periode = tanggal {cutoff} pada bulan & tahun payroll
                var periodeAkhir = new Date(tahun, bulan - 1, cutoff);
                // Awal Periode  = tanggal ({cutoff}+1) bulan sebelumnya
                var periodeAwalTmp = new Date(tahun, bulan - 1, cutoff);
                periodeAwalTmp.setMonth(periodeAwalTmp.getMonth() - 1);
                periodeAwalTmp.setDate(periodeAwalTmp.getDate() + 1);
                var periodeAwal = periodeAwalTmp;

                var tglMasukStr = window._karyawanTanggalMasuk || null;
                var akhirTrainStr = window._karyawanAkhirTraining || null;

                if (tglMasukStr && tglMasukStr !== '-' && akhirTrainStr) {
                    var tglMasuk = new Date(tglMasukStr);
                    var akhirTraining = new Date(akhirTrainStr);
                    var prorataNominal = hitungGajiPerPeriode(tglMasuk, akhirTraining, periodeAwal, periodeAkhir, prorataBase);
                    totalReceipts = prorataNominal + nonProrata;

                    // Update prosentase_gaji sebagai informasi (read-only representatif)
                    var pct = prorataBase > 0 ? Math.round((prorataNominal / prorataBase) * 100 * 100) / 100 : 100;
                    $('#prosentase_gaji').val(pct);
                } else {
                    // Tidak ada data tanggal masuk: gunakan prosentase_gaji manual
                    var percentage = parseFloat($('#prosentase_gaji').val());
                    if (isNaN(percentage) || percentage <= 0) percentage = 100;
                    var prorataNominal = prorataBase * (percentage / 100);
                    totalReceipts = prorataNominal + nonProrata;
                }

                // --- Jika Karyawan RESIGN ---
                if ($('#is_resign').is(':checked')) {
                    var resGaji = calculateResignProrata();
                    if (resGaji !== null) {
                        totalReceipts = resGaji;
                        var prorataNominalResign = totalReceipts - nonProrata;
                        var pctResign = prorataBase > 0 ? Math.round((prorataNominalResign / prorataBase) * 100 * 100) / 100 : 100;
                        $('#prosentase_gaji').val(pctResign);
                    }
                } else {
                    $('#resign_info_container').slideUp(200);
                }

                $('#calculated_penerimaan').val(Math.round(totalReceipts).toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }));

                // Potongan langsung: Punishment + Sedekah + Potongan Lainnya
                var punishment = getRawValue('#punishment');
                var sedekah = getRawValue('#sedekah_rombongan');
                var pot_lainnya = getRawValue('#potongan_lainnya');
                var totalDeductions = punishment + sedekah + pot_lainnya;

                // Tampilkan subtotal potongan sementara (tanpa PPh21, akan diupdate saat AJAX selesai)
                var currentPph21 = getRawValue('#potongan_pph_21');
                $('#calculated_potongan').val((totalDeductions + currentPph21).toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }));

                var bpjstk_total = getRawValue('#bpjstk_total');
                var bpjsk_premi = getRawValue('#bpjsk_premi');
                var jht_tk = getRawValue('#bpjstk_iuran_jht_tk');
                var tg_karyawan = getRawValue('#bpjsk_tg_karyawan');

                var totalGaji = Math.round(totalReceipts) + bpjstk_total + bpjsk_premi - jht_tk - tg_karyawan;
                if (totalGaji < 0) totalGaji = 0;

                // Sync hidden inputs for backend storage
                $('#bpjstk').val(bpjstk_total);
                $('#bpjsk').val(bpjsk_premi);
                $('#potongan_bpjs_tk').val(jht_tk);
                $('#potongan_bpjs_kesehatan').val(tg_karyawan);

                renderBreakdownDetails();

                // Debounce calculation of PPh 21
                clearTimeout(pphTimeout);
                pphTimeout = setTimeout(function () {
                    calculatePph21(totalGaji, totalDeductions);
                }, 300);
            }

            $(document).on('input change', '.entry-calc, #prosentase_gaji', calculateReceipt);

            // ==================== GAJI TERAKHIR (RESIGN) ====================
            function syncResignThpFull() {
                // THP Full = subtotal penerimaan SEBELUM faktor training/pro-rata
                var gaji = getRawValue('#gaji_pokok');
                var t_pengalaman = getRawValue('#t_pengalaman_kerja');
                var t_jabatan = getRawValue('#t_jabatan');
                var t_profesi = getRawValue('#t_profesi');
                var t_hadir = getRawValue('#t_kehadiran');
                var t_kinerja = getRawValue('#t_kinerja');
                var t_hari_raya = getRawValue('#t_hari_raya');
                var t_operasional = getRawValue('#t_operasional');
                var fee = getRawValue('#fee_beautician');
                var lembur = getRawValue('#nominal_lembur');
                var lain = getRawValue('#lain_lain');
                var prorataBase = gaji + t_jabatan + t_profesi + t_hadir + t_kinerja + t_operasional;
                $('#resign_thp_full').val(formatRupiah(prorataBase));
                return prorataBase;
            }

            // Sync THP Full ke panel resign setiap kali perhitungan berubah
            $(document).on('input change', '.entry-calc, #prosentase_gaji', syncResignThpFull);

            $('#tab-ringkasan-tab').on('shown.bs.tab', syncResignThpFull);

            $('#btnHitungResign').on('click', function () {
                var tanggalResign = $('#resign_tanggal_resign').val();
                if (!tanggalResign) {
                    alert('Harap isi tanggal resign terlebih dahulu.');
                    return;
                }

                var tanggalMasuk = window._karyawanTanggalMasuk || null;
                if (!tanggalMasuk || tanggalMasuk === '-') {
                    alert('Data tanggal masuk karyawan tidak tersedia.');
                    return;
                }

                var bulan = parseInt($('#bulan').val()) || new Date().getMonth() + 1;
                var tahun = parseInt($('#tahun').val()) || new Date().getFullYear();
                var cutoff = window._cutoffDate || 21;
                var thpFull = syncResignThpFull();

                $('#btnHitungResign').prop('disabled', true).html('<i class="ti ti-loader me-1"></i> Menghitung...');

                $.ajax({
                    url: '/slip-gaji/calculate-gaji-resign',
                    type: 'GET',
                    data: {
                        tanggal_masuk: tanggalMasuk,
                        tanggal_resign: tanggalResign,
                        bulan: bulan,
                        tahun: tahun,
                        cutoff: cutoff,
                        thp_full: thpFull
                    },
                    success: function (res) {
                        $('#resign_info_periode').val(res.periode_awal + ' s/d ' + res.periode_akhir);
                        $('#resign_info_total_hari').val(res.total_hari_periode + ' hari');
                        $('#resign_info_hari_kerja').val(res.hari_kerja + ' hari');
                        $('#resign_info_akhir_training').val(res.akhir_training);
                        var skenarioLabel = {
                            'A_transisi': 'A — Transisi Training',
                            'B_full_training': 'B — Full Training (80%)',
                            'B_prorata_training': 'B — Pro-rata Training (80%)',
                            'C_full_lulus': 'C — Full Lulus (100%)',
                            'C_prorata_lulus': 'C — Pro-rata Lulus (100%)',
                            'belum_mulai_atau_sudah_selesai': '—'
                        };
                        $('#resign_info_skenario').val(skenarioLabel[res.skenario] || res.skenario);
                        $('#resign_gaji_result').val(formatRupiah(res.gaji_resign));
                        $('#resignResult').slideDown(200);
                    },
                    error: function (xhr) {
                        var msg = xhr.responseJSON ? xhr.responseJSON.error : 'Gagal menghitung gaji resign.';
                        alert(msg);
                    },
                    complete: function () {
                        $('#btnHitungResign').prop('disabled', false).html('<i class="ti ti-calculator me-1"></i> Hitung Gaji Resign');
                    }
                });
            });

            // ==================== INITIAL SETUP ====================
            // Format on load
            $('.entry-calc-rupiah').each(function () {
                var currentVal = $(this).val();
                $(this).val(formatRupiah(currentVal));
            });

            calculateReceipt();
            syncResignThpFull();

            // Intercept form submit to clean formatting
            $('#slipGajiForm').on('submit', function () {
                // Re-enable disabled select so its value is submitted
                $('#pph21_kategori').prop('disabled', false);

                $('.entry-calc-rupiah').each(function () {
                    var cleanVal = $(this).val().replace(/\./g, '');
                    $(this).val(cleanVal);
                });
                var cleanTransfer = $('#nominal_transfer').val().replace(/\./g, '');
                $('#nominal_transfer').val(cleanTransfer);
                $('#thp').val(cleanTransfer);
            });

            if ($('#id_karyawan').val()) {
                fetchEmployeeDetails();
            }
        });
    </script>
@endpush
