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
                                        <option value="{{ $emp->id_karyawan }}"
                                            {{ old('id_karyawan') == $emp->id_karyawan ? 'selected' : '' }}>
                                            {{ $emp->nama_karyawan }} ({{ $emp->nip }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bulan <span class="text-danger">*</span></label>
                                <select name="bulan" id="bulan" class="form-select" required>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}"
                                            {{ old('bulan', date('m')) == $i ? 'selected' : '' }}>
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
                                <input type="text" id="karyawan_nip" class="form-control bg-light" readonly
                                    placeholder="-">
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
                                <label class="form-label fw-semibold">Gaji Pokok <span
                                        class="text-danger">*</span></label>
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
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('t_jabatan', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tunjangan Profesi</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="t_profesi" id="t_profesi"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('t_profesi', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tunjangan Operasional</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="t_ operasional" id="t_operasional"
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
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('t_kinerja', 0) }}">
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
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('lain_lain', 0) }}">
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
                                <input type="number" name="jumlah_hari_gabung" id="jumlah_hari_gabung"
                                    class="form-control" value="{{ old('jumlah_hari_gabung', 0) }}">
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
                                <input type="text" name="bpjsk_no_ref" id="bpjsk_no_ref"
                                    class="form-control bg-light" readonly value="{{ old('bpjsk_no_ref') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Kepesertaan</label>
                                <input type="text" id="bpjstk_tanggal_kepesertaan" class="form-control bg-light"
                                    readonly placeholder="-">
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
                                    <input type="text" name="bpjsk_iuran_jht_pk" id="bpjsk_iuran_jht_pk"
                                        class="form-control entry-calc entry-calc-rupiah bg-light" readonly
                                        value="{{ old('bpjsk_iuran_jht_pk', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Iuran JHT Tenaga Kerja</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjsk_iuran_jht_tk" id="bpjsk_iuran_jht_tk"
                                        class="form-control entry-calc entry-calc-rupiah bg-light" readonly
                                        value="{{ old('bpjsk_iuran_jht_tk', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Total</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjsk_total" id="bpjsk_total"
                                        class="form-control entry-calc entry-calc-rupiah bg-light fw-bold" readonly
                                        value="{{ old('bpjsk_total', 0) }}">
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
                                <input type="text" name="bpjsk_no_jkn" id="bpjsk_no_jkn"
                                    class="form-control bg-light" readonly value="{{ old('bpjsk_no_jkn') }}">
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
                                <input type="text" name="bpjsk_npp" id="bpjsk_npp" class="form-control bg-light"
                                    readonly value="{{ old('bpjsk_npp') }}">
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
                                <input type="text" name="pph21_npwp" id="pph21_npwp" class="form-control bg-light"
                                    readonly value="{{ old('pph21_npwp') }}">
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
                                    <input class="form-control entry-calc bg-light" type="text"
                                        name="pph21_kategori" id="pph21_kategori" value="{{ old('pph21_kategori') }}" readonly>
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
                                <label class="form-label">BPJS Ketenagakerjaan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjstk" id="bpjstk"
                                        class="form-control entry-calc entry-calc-rupiah" value="{{ old('bpjstk', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">BPJS Kesehatan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="bpjs_kesehatan" id="bpjs_kesehatan"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('bpjs_kesehatan', 0) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Potongan BPJS Ketenagakerjaan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="potongan_bpjs_tk" id="potongan_bpjs_tk"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('potongan_bpjs_tk', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Potongan BPJS Kesehatan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="potongan_bpjs_kesehatan" id="potongan_bpjs_kesehatan"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('potongan_bpjs_kesehatan', 0) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Potongan PPh 21</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="potongan_pph_21" id="potongan_pph_21"
                                        class="form-control entry-calc entry-calc-rupiah"
                                        value="{{ old('potongan_pph_21', 0) }}">
                                </div>
                            </div>
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
                                <input type="number" name="ijin_pulang_cepat" id="ijin_pulang_cepat"
                                    class="form-control" value="{{ old('ijin_pulang_cepat', 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ijin Tdk Masuk</label>
                                <input type="number" name="ijin_tidak_masuk" id="ijin_tidak_masuk" class="form-control"
                                    value="{{ old('ijin_tidak_masuk', 0) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">No Check In/Out</label>
                                <input type="number" name="no_check_in_or_out" id="no_check_in_or_out"
                                    class="form-control" value="{{ old('no_check_in_or_out', 0) }}">
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
                                <input type="number" name="kehadiran_lainnya" id="kehadiran_lainnya"
                                    class="form-control" value="{{ old('kehadiran_lainnya', 0) }}">
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
                                    <input type="hidden" name="thp" id="thp" value="{{ old('thp', 0) }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-4">
                            <button type="button" class="btn btn-outline-secondary btn-prev-tab"
                                data-prev="tab-kehadiran-tab">
                                <i class="ti ti-arrow-left me-1"></i> Sebelumnya
                            </button>
                            <div>
                                <a href="{{ route('slip-gaji.index') }}"
                                    class="btn btn-light btn-lg px-4 me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="ti ti-device-floppy me-1"></i> Save Slip Gaji
                                </button>
                            </div>
                        </div>
                    </div>

                </div>{{-- end .tab-content --}}
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ==================== TAB NAVIGATION ====================
            $(document).on('click', '.btn-next-tab', function() {
                var nextTab = $(this).data('next');
                $('#' + nextTab).tab('show');
            });
            $(document).on('click', '.btn-prev-tab', function() {
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
            $(document).on('input', '.entry-calc-rupiah', function() {
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
                    success: function(data) {
                        // console.log('Fetched employee details:', data);
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
                            $('#bpjsk_iuran_jht_pk').val(formatRupiah(data.bpjstk.pemberi_kerja));
                            $('#bpjsk_iuran_jht_tk').val(formatRupiah(data.bpjstk.tenaga_kerja));
                            $('#bpjsk_total').val(formatRupiah(data.bpjstk.total_iuran));
                        } else {
                            $('#bpjsk_no_ref, #bpjstk_tanggal_kepesertaan').val('');
                            $('#bpjsk_upah_daftar, #bpjsk_iuran_jkk, #bpjsk_iuran_jkm, #bpjsk_iuran_jht_pk, #bpjsk_iuran_jht_tk, #bpjsk_total')
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

                        // --- Hitung prosentase_gaji & jumlah_hari_gabung ---
                        if (data.karyawan.tanggal_masuk && data.karyawan.tanggal_masuk !== '-') {
                            var tglMasuk = new Date(data.karyawan.tanggal_masuk);
                            var today = new Date();

                            var diffMs = today - tglMasuk;
                            var diffHari = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                            $('#jumlah_hari_gabung').val(diffHari);

                            var bulanMasuk = tglMasuk.getFullYear() * 12 + tglMasuk.getMonth();
                            var bulanSekarang = today.getFullYear() * 12 + today.getMonth();
                            var selisihBulan = bulanSekarang - bulanMasuk;

                            if (selisihBulan < 3) {
                                $('#prosentase_gaji').val(80);
                            } else {
                                $('#prosentase_gaji').val(100);
                            }
                        } else {
                            $('#prosentase_gaji').val(100);
                            $('#jumlah_hari_gabung').val(0);
                        }

                        calculateReceipt();
                        calculatePph21();
                    },
                    error: function() {
                        console.error('Failed to retrieve employee details');
                    }
                });
            }

            $('#id_karyawan').on('change', fetchEmployeeDetails);
            $('#bulan, #tahun').on('change', function() {
                if ($('#id_karyawan').val()) {
                    fetchEmployeeDetails();
                }
            });

            // ==================== PPH21 CALCULATION ====================
            function calculatePph21() {
                var kategori = $('#pph21_kategori').val();
                if (!kategori) {
                    $('#pph21_ter').val('0');
                    $('#pph21_tarif_persentase').val('0');
                    $('#pph_21').val('0');
                    return;
                }

                // Total Gaji = Gaji + Nominal Lembur + BPJS TK (Premi Perusahaan) + BPJS Kesehatan (Premi Perusahaan)
                //            - BPJS TK Tanggungan Pegawai - BPJS Kesehatan Tanggungan Pegawai
                var gaji = getRawValue('#gaji_pokok');
                var lembur = getRawValue('#nominal_lembur');
                var bpjstkPremi = getRawValue('#bpjsk_iuran_jht_pk'); // Pemberi Kerja = JHT Pemberi Kerja
                var bpjskPremi = getRawValue('#bpjsk_tg_gaji'); // Tanggungan Perusahaan BPJS Kesehatan
                var bpjstkTgPegawai = getRawValue('#bpjsk_iuran_jht_tk'); // JHT Tenaga Kerja
                var bpjskTgPegawai = getRawValue('#bpjsk_tg_karyawan'); // Tanggungan Karyawan BPJS Kesehatan

                var totalGaji = gaji + lembur + bpjstkPremi + bpjskPremi - bpjstkTgPegawai - bpjskTgPegawai;
                if (totalGaji < 0) totalGaji = 0;

                $.ajax({
                    url: '/slip-gaji/calculate-pph21',
                    type: 'GET',
                    data: {
                        kategori: kategori,
                        total_gaji: totalGaji
                    },
                    success: function(result) {
                        $('#pph21_ter').val(result.ter*100);
                        $('#pph21_tarif_persentase').val(result.ter*100);
                        $('#pph_21').val(formatRupiah(result.pph21));
                        calculateReceipt(); // Recalculate totals
                    },
                    error: function() {
                        console.error('Failed to calculate PPH21');
                    }
                });
            }

            // Sync disabled select to hidden input
            $('#pph21_kategori').on('change', function() {
                $('#pph21_kategori_hidden').val($(this).val());
                calculatePph21();
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

                var subtotalReceipts = gaji + t_pengalaman + t_jabatan + t_profesi + t_hadir + t_kinerja +
                    t_hari_raya + t_operasional + fee_beautician + lembur + lain;

                var percentage = parseFloat($('#prosentase_gaji').val());
                if (isNaN(percentage) || percentage <= 0) {
                    percentage = 100;
                }
                var totalReceipts = subtotalReceipts * (percentage / 100);
                $('#calculated_penerimaan').val(totalReceipts.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }));

                var punishment = getRawValue('#punishment');
                var pot_bpjstk = getRawValue('#potongan_bpjs_tk');
                var pot_bpjs_kes = getRawValue('#potongan_bpjs_kesehatan');
                var pot_pph21 = getRawValue('#potongan_pph_21');
                var sedekah = getRawValue('#sedekah_rombongan');
                var pot_lainnya = getRawValue('#potongan_lainnya');

                var totalDeductions = punishment + pot_bpjstk + pot_bpjs_kes + pot_pph21 + sedekah + pot_lainnya;
                $('#calculated_potongan').val(totalDeductions.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }));

                var netTransfer = totalReceipts - totalDeductions;
                $('#nominal_transfer').val(formatRupiah(netTransfer));
                $('#thp').val(netTransfer);
            }

            $(document).on('input change', '.entry-calc, #prosentase_gaji', calculateReceipt);

            // ==================== INITIAL SETUP ====================
            // Format on load
            $('.entry-calc-rupiah').each(function() {
                var currentVal = $(this).val();
                $(this).val(formatRupiah(currentVal));
            });

            calculateReceipt();

            // Intercept form submit to clean formatting
            $('#slipGajiForm').on('submit', function() {
                // Re-enable disabled select so its value is submitted
                $('#pph21_kategori').prop('disabled', false);

                $('.entry-calc-rupiah').each(function() {
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
