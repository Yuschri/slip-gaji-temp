@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="">
                        <h1 class="fs-3 mb-1">Slip Gaji</h1>
                        <p class="mb-0">Manage and import employee payslips</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="ti ti-file-import"></i> Import Excel
                        </button>
                        <a href="{{ route('slip-gaji.create') }}" class="btn btn-primary">Add Manually</a>
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

        <div class="row mb-3">
            <div class="col-md-9 d-flex align-items-end">
                <div class="me-3">
                    <label for="klinikFilter" class="form-label">Filter Klinik</label>
                    <select id="klinikFilter" class="form-select">
                        <option value="">All Klinik</option>
                        @foreach ($kliniks as $klinik)
                            <option value="{{ $klinik }}">{{ $klinik }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="me-3">
                    <label for="bulanFilter" class="form-label">Filter Bulan</label>
                    <select id="bulanFilter" class="form-select">
                        <option value="">All Bulan</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ date('F', mktime(0, 0, 0, $i, 10)) }}">
                                {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="me-3">
                    <label for="tahunFilter" class="form-label">Filter Tahun</label>
                    <select id="tahunFilter" class="form-select">
                        <option value="">All Tahun</option>
                        @foreach ($tahuns as $tahun)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <form action="{{ route('slip-gaji.broadcast-bulk') }}" method="POST" id="broadcastBulkForm">
                    @csrf
                    <input type="hidden" name="klinik" id="klinikHidden">
                    <input type="hidden" name="bulan" id="bulanHidden">
                    <input type="hidden" name="tahun" id="tahunHidden">
                    <button type="submit" class="btn btn-info text-white"
                        onclick="return confirm('Broadcast to all filtered data?')">
                        <i class="ti ti-brand-whatsapp"></i> Broadcast Filtered
                    </button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card table-responsive p-4">
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
                            <input type="file" name="file" id="file" class="form-control" required accept=".xlsx,.xls,.csv">
                            <small class="text-muted">Download template: <a href="{{ asset('template_slip.xlsx') }}"
                                    download>template_slip.xlsx</a></small>
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
        });
    </script>
@endpush