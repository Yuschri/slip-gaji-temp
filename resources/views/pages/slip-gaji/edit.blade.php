@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="">
                        <h1 class="fs-3 mb-1">Edit Slip Gaji</h1>
                        <p class="mb-0">Update employee payslip information</p>
                    </div>
                    <div>
                        <a href="{{ route('slip-gaji.index') }}" class="btn btn-outline-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <form action="{{ route('slip-gaji.update', $slip->id_slip) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nama Karyawan</label>
                        <input type="text" name="nama_karyawan" class="form-control" required
                            value="{{ old('nama_karyawan', $slip->nama_karyawan) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ID Karyawan</label>
                        <input type="number" name="id_karyawan" class="form-control"
                            value="{{ old('id_karyawan', $slip->id_karyawan) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control"
                            value="{{ old('tanggal_masuk', $slip->tanggal_masuk ? $slip->tanggal_masuk->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select" required>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('bulan', $slip->bulan) == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun" class="form-control" required
                            value="{{ old('tahun', $slip->tahun) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Divisi</label>
                        <input type="text" name="divisi" class="form-control" value="{{ old('divisi', $slip->divisi) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Klinik</label>
                        <input type="text" name="klinik" class="form-control" value="{{ old('klinik', $slip->klinik) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">No WA</label>
                        <input type="text" name="no_wa" class="form-control" value="{{ old('no_wa', $slip->no_wa) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" name="nomor_rekening" class="form-control"
                            value="{{ old('nomor_rekening', $slip->nomor_rekening) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">THP</label>
                        <input type="number" step="0.01" name="thp" class="form-control"
                            value="{{ old('thp', $slip->thp) }}">
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Pendapatan & Tunjangan</h5>

                    <div class="col-md-3">
                        <label class="form-label">Gaji Pokok</label>
                        <input type="number" step="0.01" name="gaji_pokok" class="form-control"
                            value="{{ old('gaji_pokok', $slip->gaji_pokok) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">T. Jabatan</label>
                        <input type="number" step="0.01" name="t_jabatan" class="form-control"
                            value="{{ old('t_jabatan', $slip->t_jabatan) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">T. Profesi</label>
                        <input type="number" step="0.01" name="t_profesi" class="form-control"
                            value="{{ old('t_profesi', $slip->t_profesi) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">T. Kehadiran</label>
                        <input type="number" step="0.01" name="t_kehadiran" class="form-control"
                            value="{{ old('t_kehadiran', $slip->t_kehadiran) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">T. Kinerja</label>
                        <input type="number" step="0.01" name="t_kinerja" class="form-control"
                            value="{{ old('t_kinerja', $slip->t_kinerja) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">T. Hari Raya</label>
                        <input type="number" step="0.01" name="t_hari_raya" class="form-control"
                            value="{{ old('t_hari_raya', $slip->t_hari_raya) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Lembur</label>
                        <input type="number" step="0.01" name="lembur" class="form-control"
                            value="{{ old('lembur', $slip->lembur) }}">
                    </div>


                    <hr class="my-4">
                    <h5 class="mb-3">Potongan & Pengurangan</h5>

                    <div class="col-md-3">
                        <label class="form-label">Punishment</label>
                        <input type="number" step="0.01" name="punishment" class="form-control"
                            value="{{ old('punishment', $slip->punishment) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">BPJS TK</label>
                        <input type="number" step="0.01" name="bpjstk" class="form-control"
                            value="{{ old('bpjstk', $slip->bpjstk) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">BPJS Kesehatan</label>
                        <input type="number" step="0.01" name="bpjs_kesehatan" class="form-control"
                            value="{{ old('bpjs_kesehatan', $slip->bpjs_kesehatan) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">PPh 21</label>
                        <input type="number" step="0.01" name="pph_21" class="form-control"
                            value="{{ old('pph_21', $slip->pph_21) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sedekah Rombongan</label>
                        <input type="number" step="0.01" name="sedekah_rombongan" class="form-control"
                            value="{{ old('sedekah_rombongan', $slip->sedekah_rombongan) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Lain-lain</label>
                        <input type="number" step="0.01" name="lain_lain" class="form-control"
                            value="{{ old('lain_lain', $slip->lain_lain) }}">
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Kehadiran & Status</h5>

                    <div class="col-md-2">
                        <label class="form-label">Cuti</label>
                        <input type="number" name="cuti" class="form-control" value="{{ old('cuti', $slip->cuti) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Terlambat</label>
                        <input type="number" name="terlambat" class="form-control"
                            value="{{ old('terlambat', $slip->terlambat) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Ijin Pulang Cepat</label>
                        <input type="number" name="ijin_pulang_cepat" class="form-control"
                            value="{{ old('ijin_pulang_cepat', $slip->ijin_pulang_cepat) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Ijin Tdk Masuk</label>
                        <input type="number" name="ijin_tidak_masuk" class="form-control"
                            value="{{ old('ijin_tidak_masuk', $slip->ijin_tidak_masuk) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">No Check In/Out</label>
                        <input type="number" name="no_check_in_or_out" class="form-control"
                            value="{{ old('no_check_in_or_out', $slip->no_check_in_or_out) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">No Check In & Out</label>
                        <input type="number" name="no_check_in_and_out" class="form-control"
                            value="{{ old('no_check_in_and_out', $slip->no_check_in_and_out) }}">
                    </div>

                    <hr class="my-4">
                    <div class="col-md-12">
                        <label class="form-label text-primary font-weight-bold">Nominal Transfer Akhir</label>
                        <input type="number" step="0.01" name="nominal_transfer"
                            class="form-control form-control-lg border-primary" required
                            value="{{ old('nominal_transfer', $slip->nominal_transfer) }}">
                    </div>
                </div>

                <div class="mt-5">
                    <button type="submit" class="btn btn-primary btn-lg">Update Slip Gaji</button>
                    <a href="{{ route('slip-gaji.index') }}" class="btn btn-light btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection