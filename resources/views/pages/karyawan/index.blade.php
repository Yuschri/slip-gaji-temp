@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="">
                        <h1 class="fs-3 mb-1">Data Karyawan</h1>
                        <p class="mb-0">Manage employee personal records, roles and bank details</p>
                    </div>
                    <div>
                        <a href="{{ route('karyawan.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus"></i> Add Karyawan
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

        <div class="row">
            <div class="col-12">
                <div class="card table-responsive p-4">
                    <table id="karyawanTable" class="table mb-0 text-nowrap table-hover">
                        <thead class="table-light border-light">
                            <tr>
                                <th>NIP</th>
                                <th>Nama Karyawan</th>
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
                                                onclick="return confirm('Are you sure you want to delete database record for {{ $karyawan->nama_karyawan }}?')">
                                                <i class="ti ti-trash"></i> Delete
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#karyawanTable').DataTable({
                "order": [[1, "asc"]]
            });
        });
    </script>
@endpush