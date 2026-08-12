@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="">
                        <h1 class="fs-3 mb-1">Edit Karyawan</h1>
                        <p class="mb-0">Modify employee profile, role or bank details</p>
                    </div>
                    <div>
                        <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">Back to List</a>
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

        <div class="card p-4">
            <form action="{{ route('karyawan.update', $karyawan->id_karyawan) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">NIP <span class="text-danger">*</span></label>
                        <input type="text" name="nip" class="form-control" placeholder="Enter NIP" required
                            value="{{ old('nip', $karyawan->nip) }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Nama Karyawan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_karyawan" class="form-control" placeholder="Enter full name" required
                            value="{{ old('nama_karyawan', $karyawan->nama_karyawan) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control"
                            value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control"
                            value="{{ old('tanggal_masuk', $karyawan->tanggal_masuk ? $karyawan->tanggal_masuk->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Divisi <span class="text-danger">*</span></label>
                        <select name="id_divisi" class="form-select" required>
                            <option value="">-- Select Divisi --</option>
                            @foreach ($divisi as $d)
                                <option value="{{ $d->id_divisi }}" {{ old('id_divisi', $karyawan->id_divisi) == $d->id_divisi ? 'selected' : '' }}>
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <select name="id_jabatan" class="form-select" required>
                            <option value="">-- Select Jabatan --</option>
                            @foreach ($jabatan as $j)
                                <option value="{{ $j->id_jabatan }}" {{ old('id_jabatan', $karyawan->id_jabatan) == $j->id_jabatan ? 'selected' : '' }}>
                                    {{ $j->nama_jabatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nik" class="form-control" placeholder="NIK"
                            value="{{ old('nik', $karyawan->nik) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cabang <span class="text-danger">*</span></label>
                        <select name="cabang" class="form-select" required>
                            <option value="">-- Select Cabang --</option>
                            <option value="HO" {{ old('cabang', $karyawan->cabang) == 'HO' ? 'selected' : '' }}>HO</option>
                            <option value="Klinik Paris" {{ old('cabang', $karyawan->cabang) == 'Klinik Paris' ? 'selected' : '' }}>Klinik Paris</option>
                            <option value="Klinik Kavling DPR" {{ old('cabang', $karyawan->cabang) == 'Klinik Kavling DPR' ? 'selected' : '' }}>Klinik Kavling DPR</option>
                            <option value="Klinik Kutisari" {{ old('cabang', $karyawan->cabang) == 'Klinik Kutisari' ? 'selected' : '' }}>Klinik Kutisari</option>
                            <option value="Klinik Mulyosari" {{ old('cabang', $karyawan->cabang) == 'Klinik Mulyosari' ? 'selected' : '' }}>Klinik Mulyosari</option>
                            <option value="Klinik Kutai" {{ old('cabang', $karyawan->cabang) == 'Klinik Kutai' ? 'selected' : '' }}>Klinik Kutai</option>
                            <option value="Klinik Mojokerto" {{ old('cabang', $karyawan->cabang) == 'Klinik Mojokerto' ? 'selected' : '' }}>Klinik Mojokerto</option>
                            <option value="Klinik Madiun" {{ old('cabang', $karyawan->cabang) == 'Klinik Madiun' ? 'selected' : '' }}>Klinik Madiun</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" name="nomor_rekening" class="form-control" placeholder="Nomor Rekening"
                            value="{{ old('nomor_rekening', $karyawan->nomor_rekening) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="text" name="no_wa" class="form-control" placeholder="Nomor WhatsApp"
                            value="{{ old('no_wa', $karyawan->no_wa) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Periode Cut Off <span class="text-danger">*</span></label>
                        <select name="periode_cut_off" class="form-select" required>
                            <option value="">-- Pilih Tanggal Cut Off --</option>
                            <option value="15" {{ old('periode_cut_off', $karyawan->periode_cut_off) == '15' ? 'selected' : '' }}>Tanggal 15</option>
                            <option value="21" {{ old('periode_cut_off', $karyawan->periode_cut_off) == '21' ? 'selected' : '' }}>Tanggal 21</option>
                        </select>
                        <div class="form-text">Tanggal akhir periode penggajian setiap bulan.</div>
                    </div>
                </div>

                <div class="mt-5">
                    <button type="submit" class="btn btn-primary btn-lg">Update Karyawan</button>
                    <a href="{{ route('karyawan.index') }}" class="btn btn-light btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
