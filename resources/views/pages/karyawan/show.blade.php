@extends('layouts.main')

@push('styles')
    <style>
        .btn-info {
            --bs-btn-color: #fff;
            --bs-btn-bg: #0ea5e9;
            --bs-btn-border-color: #0ea5e9;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #0284c7;
            --bs-btn-hover-border-color: #0369a1;
            --bs-btn-focus-shadow-rgb: 14, 165, 233;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #0369a1;
            --bs-btn-active-border-color: #075985;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
            --bs-btn-disabled-color: #fff;
            --bs-btn-disabled-bg: #0ea5e9;
            --bs-btn-disabled-border-color: #0ea5e9;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="">
                        <h1 class="fs-3 mb-1">Detail Karyawan</h1>
                        <p class="mb-0">Rincian karyawan & pengaturan gaji</p>
                    </div>
                    <div>
                        <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">Kembali ke Daftar</a>
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
            <div class="col-lg-6 col-md-12 mb-4">
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
                        <hr class="text-muted opacity-25 my-10">
                        <div class="mt-3 mb-9 space-y-3">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted"><i class="ti ti-calendar me-2"></i> Tanggal Lahir:</span>
                                <strong>{{ $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('d M Y') : '-' }}</strong>
                            </div>
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
                                <span class="text-muted"><i class="ti ti-id me-2"></i> NIK:</span>
                                <strong>{{ $karyawan->nik ?? '-' }}</strong>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('karyawan.edit', $karyawan->id_karyawan) }}" class="btn btn-info w-100 mt-6">
                                <i class="ti ti-edit"></i> Edit Profil Karyawan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Original salary form (tb_gaji and tb_potongan) -->
            <div class="col-lg-6 col-md-12">
                <form action="{{ route('karyawan.kompensasi.store', $karyawan->id_karyawan) }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_karyawan" value="{{ $karyawan->id_karyawan }}">

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 text-white"><i class="ti ti-wallet me-2"></i> Gaji Pokok, Tunjangan & Potongan
                            </h5>
                        </div>
                        <div class="card-body pt-4">

                            {{-- Gaji Pokok & Tunjangan --}}
                            <h6 class="fw-bold mb-3"><i class="ti ti-cash me-2 text-primary"></i> Gaji Pokok & Tunjangan
                            </h6>
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

                            <hr class="my-4">

                            {{-- Potongan Gaji --}}
                            <h6 class="fw-bold mb-3"><i class="ti ti-scissors me-2 text-danger"></i> Potongan Gaji</h6>
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
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-4">
                            <button type="submit" class="btn btn-info btn-lg w-100">
                                <i class="ti ti-device-floppy me-1"></i> Simpan Data Gaji & Potongan
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- BPJS Ketenagakerjaan & Kesehatan -->
            <div class="col-lg-12 col-md-12 mb-4 mt-4">
                <form action="{{ route('karyawan.bpjs.store', $karyawan->id_karyawan) }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_karyawan" value="{{ $karyawan->id_karyawan }}">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 text-white"><i class="ti ti-shield-heart me-2"></i> BPJS Ketenagakerjaan &
                                Kesehatan</h5>
                        </div>
                        <div class="card-body pt-4">

                            {{-- BPJS Ketenagakerjaan --}}
                            <h6 class="fw-bold mb-3"><i class="ti ti-shield me-2 text-primary"></i> BPJS Ketenagakerjaan
                            </h6>
                            @if ($karyawan->bpjsk)
                                <div class="alert alert-light-success border border-success border-dashed text-success-emphasis d-flex align-items-center mb-4"
                                    role="alert">
                                    <i class="ti ti-circle-check fs-4 me-2"></i>
                                    <div>
                                        Data BPJS Ketenagakerjaan untuk karyawan ini <strong>sudah diatur</strong>. Anda dapat
                                        memperbaruinya di bawah.
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-light-warning border border-warning border-dashed text-warning-emphasis d-flex align-items-center mb-4"
                                    role="alert">
                                    <i class="ti ti-alert-triangle fs-4 me-2"></i>
                                    <div>
                                        Data BPJS Ketenagakerjaan untuk karyawan ini <strong>belum diatur</strong>. Silakan isi
                                        form
                                        di bawah untuk mengisi data BPJS Ketenagakerjaan.
                                    </div>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Nomor Referensi</label>
                                    <input type="text" name="nomor_referensi" class="form-control"
                                        placeholder="No. Referensi"
                                        value="{{ old('nomor_referensi', $karyawan->bpjstk ? $karyawan->bpjstk->no_referensi : '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Upah yang didaftarkan<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="bpjstk_upah" name="upah_didaftarkan_tk"
                                            class="form-control text-end rupiah-mask" required placeholder="0"
                                            value="{{ old('upah_didaftarkan_tk', $karyawan->bpjstk ? (int) $karyawan->bpjstk->upah_didaftarkan : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Iuran JKK <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="bpjstk_iuran_jkk" name="iuran_jkk"
                                            class="form-control text-end rupiah-mask bg-light" readonly required
                                            placeholder="0"
                                            value="{{ old('iuran_jkk', $karyawan->bpjstk ? (int) $karyawan->bpjstk->iuran_jkk : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Iuran JKM <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="bpjstk_iuran_jkm" name="iuran_jkm"
                                            class="form-control text-end rupiah-mask bg-light" readonly required
                                            placeholder="0"
                                            value="{{ old('iuran_jkm', $karyawan->bpjstk ? (int) $karyawan->bpjstk->iuran_jkm : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Iuran JHT (Pemberi Kerja)<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="bpjstk_pemberi_kerja" name="pemberi_kerja"
                                            class="form-control text-end rupiah-mask bg-light" readonly required
                                            placeholder="0"
                                            value="{{ old('pemberi_kerja', $karyawan->bpjstk ? (int) $karyawan->bpjstk->pemberi_kerja : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Iuran JHT (Tenaga Kerja)<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="bpjstk_tenaga_kerja" name="tenaga_kerja"
                                            class="form-control text-end rupiah-mask bg-light" readonly required
                                            placeholder="0"
                                            value="{{ old('tenaga_kerja', $karyawan->bpjstk ? (int) $karyawan->bpjstk->tenaga_kerja : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label font-weight-bold">Total Iuran<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="bpjstk_total_iuran" name="total_iuran"
                                            class="form-control text-end rupiah-mask bg-light" readonly required
                                            placeholder="0"
                                            value="{{ old('total_iuran', $karyawan->bpjstk ? (int) $karyawan->bpjstk->total_iuran : '') }}">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- BPJS Kesehatan --}}
                            <h6 class="fw-bold mb-3"><i class="ti ti-heartbeat me-2 text-danger"></i> BPJS Kesehatan</h6>
                            @if ($karyawan->bpjsk)
                                <div class="alert alert-light-info border border-info border-dashed text-info-emphasis d-flex align-items-center mb-4"
                                    role="alert">
                                    <i class="ti ti-circle-check fs-4 me-2"></i>
                                    <div>
                                        Data BPJS Kesehatan untuk karyawan ini <strong>sudah diatur</strong>. Anda dapat
                                        memperbaruinya di bawah.
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-light-warning border border-warning border-dashed text-warning-emphasis d-flex align-items-center mb-4"
                                    role="alert">
                                    <i class="ti ti-alert-triangle fs-4 me-2"></i>
                                    <div>
                                        Data BPJS Kesehatan untuk karyawan ini <strong>belum diatur</strong>. Silakan isi form
                                        di
                                        bawah untuk mengisi data BPJS Kesehatan.
                                    </div>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">No JKN Peserta<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="no_jkn_peserta" class="form-control" required
                                            placeholder="0"
                                            value="{{ old('no_jkn_peserta', $karyawan->bpjsk ? $karyawan->bpjsk->no_jkn_peserta : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Beban BPJS Kesehatan<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="bpjsk_beban" name="beban_bpjsk" class="form-control" required
                                            placeholder="Jumlah orang"
                                            value="{{ old('beban_bpjsk', $karyawan->bpjsk ? (int) $karyawan->bpjsk->beban_bpjsk : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">NPP<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="npp" class="form-control" required placeholder="0"
                                            value="{{ old('npp', $karyawan->bpjsk ? $karyawan->bpjsk->npp : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label font-weight-bold">Upah yang didaftarkan<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="bpjsk_upah" name="upah_didaftarkan_ks"
                                            class="form-control text-end rupiah-mask" required placeholder="0"
                                            value="{{ old('upah_didaftarkan_ks', $karyawan->bpjsk ? (int) $karyawan->bpjsk->upah_didaftarkan : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Premi<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="bpjsk_premi" name="premi"
                                            class="form-control text-end rupiah-mask bg-light" readonly required
                                            placeholder="0"
                                            value="{{ old('premi', $karyawan->bpjsk ? (int) $karyawan->bpjsk->premi : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Tanggungan Perusahaan<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="bpjsk_tanggungan_perusahaan" name="tanggungan_perusahaan"
                                            class="form-control text-end rupiah-mask bg-light" readonly required
                                            placeholder="0"
                                            value="{{ old('tanggungan_perusahaan', $karyawan->bpjsk ? (int) $karyawan->bpjsk->tanggungan_perusahaan : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-weight-bold">Tanggungan Karyawan<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="bpjsk_tanggungan_karyawan" name="tanggungan_karyawan"
                                            class="form-control text-end rupiah-mask bg-light" readonly required
                                            placeholder="0"
                                            value="{{ old('tanggungan_karyawan', $karyawan->bpjsk ? (int) $karyawan->bpjsk->tanggungan_karyawan : '') }}">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-4">
                            <button type="submit" class="btn btn-info btn-lg w-100">
                                <i class="ti ti-device-floppy me-1"></i> Simpan Data BPJS
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- PPH 21 -->
            <div class="col-lg-12 col-md-12 mb-4">
                <form action="{{ route('karyawan.pph21.store', $karyawan->id_karyawan) }}" method="POST">
                    @csrf
                    <div class="card shadow-sm border-0 pb-3">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 text-white"><i class="ti ti-receipt me-2"></i> PPh 21</h5>
                        </div>
                        <div class="card-body pt-4">
                            @if ($karyawan->pph21)
                                <div class="alert alert-light-warning border border-warning border-dashed text-warning-emphasis d-flex align-items-center mb-4"
                                    role="alert">
                                    <i class="ti ti-circle-check fs-4 me-2"></i>
                                    <div>
                                        Data PPh 21 untuk karyawan ini <strong>sudah diatur</strong>. Anda dapat
                                        memperbaruinya di
                                        bawah.
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-light-info border border-info border-dashed text-info-emphasis d-flex align-items-center mb-4"
                                    role="alert">
                                    <i class="ti ti-alert-triangle fs-4 me-2"></i>
                                    <div>
                                        Data PPh 21 untuk karyawan ini <strong>belum diatur</strong>. Silakan isi form di
                                        bawah untuk
                                        mengisi data PPh 21.
                                    </div>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label font-weight-bold">Identitas (NPWP/KTP) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="identitas" class="form-control" required
                                        placeholder="NPWP atau KTP"
                                        value="{{ old('identitas', $karyawan->pph21 ? $karyawan->pph21->identitas : '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold">PTKP<span
                                            class="text-danger">*</span></label>
                                    <select id="pph_ptkp" name="ptkp" class="form-control" required>
                                        @php
                                            $selectedPtkp = old('ptkp', $karyawan->pph21 ? $karyawan->pph21->ptkp : '');
                                        @endphp
                                        <option value="">Pilih PTKP</option>
                                        <option value="TK/0" {{ $selectedPtkp == 'TK/0' ? 'selected' : '' }}>TK/0</option>
                                        <option value="TK/1" {{ $selectedPtkp == 'TK/1' ? 'selected' : '' }}>TK/1</option>
                                        <option value="TK/2" {{ $selectedPtkp == 'TK/2' ? 'selected' : '' }}>TK/2</option>
                                        <option value="TK/3" {{ $selectedPtkp == 'TK/3' ? 'selected' : '' }}>TK/3</option>
                                        <option value="K/0" {{ $selectedPtkp == 'K/0' ? 'selected' : '' }}>K/0</option>
                                        <option value="K/1" {{ $selectedPtkp == 'K/1' ? 'selected' : '' }}>K/1</option>
                                        <option value="K/2" {{ $selectedPtkp == 'K/2' ? 'selected' : '' }}>K/2</option>
                                        <option value="K/3" {{ $selectedPtkp == 'K/3' ? 'selected' : '' }}>K/3</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold">Kategori<span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="pph_kategori" name="kategori" class="form-control" required
                                        readonly placeholder="Masukkan Kategori"
                                        value="{{ old('kategori', $karyawan->pph21 ? $karyawan->pph21->kategori : '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 pt-3 px-4">
                            <button type="submit" class="btn btn-info btn-lg w-100">
                                <i class="ti ti-device-floppy me-1"></i> Simpan Data PPh 21
                            </button>
                        </div>
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

            // Active BPJS Ketenagakerjaan schema rates (persentase)
            var bpjstkRates = {
                iuran_jkk: {{ $skemaBpjstk ? $skemaBpjstk->iuran_jkk : 0 }},
                iuran_jkm: {{ $skemaBpjstk ? $skemaBpjstk->iuran_jkm : 0 }},
                pemberi_kerja: {{ $skemaBpjstk ? $skemaBpjstk->pemberi_kerja : 0 }},
                tenaga_kerja: {{ $skemaBpjstk ? $skemaBpjstk->tenaga_kerja : 0 }},
            };

            // Auto-calculate Iuran JKK, JKM, JHT (Pemberi Kerja & Tenaga Kerja) and Total Iuran
            function hitungBPJSTK() {
                var upahRaw = $('#bpjstk_upah').val().replace(/\./g, '').replace(/[^0-9]/g, '');
                var upah = upahRaw === '' ? 0 : parseInt(upahRaw, 10);

                var jkk = upah * bpjstkRates.iuran_jkk;
                var jkm = upah * bpjstkRates.iuran_jkm;
                var pk = upah * bpjstkRates.pemberi_kerja;
                var tk = upah * bpjstkRates.tenaga_kerja;
                var total = jkk + jkm + pk + tk;

                $('#bpjstk_iuran_jkk').val(formatRupiah(Math.round(jkk)));
                $('#bpjstk_iuran_jkm').val(formatRupiah(Math.round(jkm)));
                $('#bpjstk_pemberi_kerja').val(formatRupiah(Math.round(pk)));
                $('#bpjstk_tenaga_kerja').val(formatRupiah(Math.round(tk)));
                $('#bpjstk_total_iuran').val(formatRupiah(Math.round(total)));
            }

            // Active BPJS Kesehatan schema rates (persentase)
            var bpjskRates = {
                premi: {{ $skemaBpjsk ? $skemaBpjsk->premi : 0 }},
                tanggungan_perusahaan: {{ $skemaBpjsk ? $skemaBpjsk->tanggungan_perusahaan : 0 }},
                tanggungan_karyawan: {{ $skemaBpjsk ? $skemaBpjsk->tanggungan_karyawan : 0 }},
            };
            var BPJSK_PREMI_MAX = 600000;

            // Auto-calculate Premi, Tanggungan Perusahaan and Tanggungan Karyawan (BPJS Kesehatan)
            function hitungBPJSK() {
                var upahRaw = $('#bpjsk_upah').val().replace(/\./g, '').replace(/[^0-9]/g, '');
                var upah = upahRaw === '' ? 0 : parseInt(upahRaw, 10);

                var bebanRaw = $('#bpjsk_beban').val().replace(/[^0-9]/g, '');
                var beban = bebanRaw === '' ? 0 : parseInt(bebanRaw, 10);
                if (isNaN(beban) || beban < 0) {
                    beban = 0;
                }

                var premi = upah * bpjskRates.premi;
                if (premi > BPJSK_PREMI_MAX) {
                    premi = BPJSK_PREMI_MAX;
                }

                // Setiap 1 beban menambah 1% pada persentase tanggungan karyawan
                var tkRate = bpjskRates.tanggungan_karyawan;
                if (beban > 0) {
                    tkRate += beban * 0.01;
                }

                var tp = upah * bpjskRates.tanggungan_perusahaan;
                var tk = upah * tkRate;

                $('#bpjsk_premi').val(formatRupiah(Math.round(premi)));
                $('#bpjsk_tanggungan_perusahaan').val(formatRupiah(Math.round(tp)));
                $('#bpjsk_tanggungan_karyawan').val(formatRupiah(Math.round(tk)));
            }

            // Pemetaan PTKP -> Kategori (golongan) sesuai aturan
            var ptkpToKategori = {
                'TK/0': 'A',
                'TK/1': 'A',
                'K/0': 'A',
                'TK/2': 'B',
                'TK/3': 'B',
                'K/1': 'B',
                'K/2': 'B',
                'K/3': 'C'
            };

            // Auto-isi Kategori berdasarkan PTKP yang dipilih
            function isiKategoriPph() {
                var ptkp = $('#pph_ptkp').val();
                $('#pph_kategori').val(ptkpToKategori[ptkp] || '');
            }

            $('#pph_ptkp').on('change', isiKategoriPph);
            isiKategoriPph();

            $('#bpjsk_beban, #bpjsk_upah').on('input', hitungBPJSK);
            hitungBPJSK();

            $('#bpjstk_upah').on('input', hitungBPJSTK);
            hitungBPJSTK();

            // Strip formatting before submit
            $('form').on('submit', function () {
                $(this).find('.rupiah-mask').each(function () {
                    $(this).val($(this).val().replace(/\./g, ''));
                });
            });
        });
    </script>
@endpush