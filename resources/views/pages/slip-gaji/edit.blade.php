@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="fs-3 mb-1">Edit Slip Gaji</h1>
                        <p class="mb-0">Modify employee payslip details with real-time recalculation</p>
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
            <form action="{{ route('slip-gaji.update', $slip->id_slip) }}" method="POST" id="slipGajiForm">
                @csrf

                <h5 class="mb-3 text-primary"><i class="ti ti-user me-2"></i> 1. Karyawan & Periode</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Nama Karyawan <span class="text-danger">*</span></label>
                        <select name="id_karyawan" id="id_karyawan" class="form-select" required>
                            <option value="">-- select employee --</option>
                            @foreach ($karyawans as $emp)
                                <option value="{{ $emp->id_karyawan }}" {{ old('id_karyawan', $slip->id_karyawan) == $emp->id_karyawan ? 'selected' : '' }}>
                                    {{ $emp->nama_karyawan }} ({{ $emp->nip }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bulan <span class="text-danger">*</span></label>
                        <select name="bulan" id="bulan" class="form-select" required>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('bulan', $slip->bulan) == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tahun <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" id="tahun" class="form-control" required
                            value="{{ old('tahun', $slip->tahun) }}">
                    </div>

                    <!-- Readonly Karyawan Info -->
                    <div class="col-md-4">
                        <label class="form-label text-muted">Tanggal Masuk</label>
                        <input type="text" id="karyawan_tanggal_masuk" class="form-control bg-light" readonly
                            placeholder="-" value="{{ $slip->tanggal_masuk ? $slip->tanggal_masuk->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Divisi</label>
                        <input type="text" id="karyawan_divisi" class="form-control bg-light" readonly placeholder="-"
                            value="{{ $slip->divisi }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Klinik / Cabang</label>
                        <input type="text" id="karyawan_klinik" class="form-control bg-light" readonly placeholder="-"
                            value="{{ $slip->klinik }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">WhatsApp</label>
                        <input type="text" id="karyawan_no_wa" class="form-control bg-light" readonly placeholder="-"
                            value="{{ $slip->no_wa }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Nomor Rekening</label>
                        <input type="text" id="karyawan_nomor_rekening" class="form-control bg-light" readonly
                            placeholder="-" value="{{ $slip->nomor_rekening }}">
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <h5 class="mb-3 text-success"><i class="ti ti-cash me-2"></i> 2. Pendapatan & Tunjangan</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="gaji_pokok" id="gaji_pokok"
                                class="form-control entry-calc entry-calc-rupiah" required
                                value="{{ old('gaji_pokok', $slip->gaji_pokok) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">T. Pengalaman Kerja</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="t_pengalaman_kerja" id="t_pengalaman_kerja"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('t_pengalaman_kerja', $slip->t_pengalaman_kerja) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">T. Jabatan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="t_jabatan" id="t_jabatan"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('t_jabatan', $slip->t_jabatan) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">T. Profesi</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="t_profesi" id="t_profesi"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('t_profesi', $slip->t_profesi) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">T. Kehadiran</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="t_kehadiran" id="t_kehadiran"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('t_kehadiran', $slip->t_kehadiran) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">T. Kinerja</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="t_kinerja" id="t_kinerja"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('t_kinerja', $slip->t_kinerja) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">T. Hari Raya</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="t_hari_raya" id="t_hari_raya"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('t_hari_raya', $slip->t_hari_raya) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Operasional</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="operasional" id="operasional"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('operasional', $slip->operasional) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fee Beautician</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="fee_beautician" id="fee_beautician"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('fee_beautician', $slip->fee_beautician) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nominal Lembur</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="lembur" id="nominal_lembur"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('lembur', $slip->lembur) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Prosentase Gaji (%)</label>
                        <input type="number" step="0.01" name="prosentase_gaji" id="prosentase_gaji"
                            class="form-control entry-calc" value="{{ old('prosentase_gaji', $slip->prosentase_gaji) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jumlah Hari Gabung</label>
                        <input type="number" name="jumlah_hari_gabung" id="jumlah_hari_gabung" class="form-control"
                            value="{{ old('jumlah_hari_gabung', $slip->jumlah_hari_gabung) }}">
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <h5 class="mb-3 text-danger"><i class="ti ti-scissors me-2"></i> 3. Potongan & Pengurangan</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Punishment</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="punishment" id="punishment"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('punishment', $slip->punishment) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">BPJS TK (Tunjangan)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="bpjstk" id="bpjstk" class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('bpjstk', $slip->bpjstk) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">BPJS Kesehatan (Tunj.)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="bpjs_kesehatan" id="bpjs_kesehatan"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('bpjs_kesehatan', $slip->bpjs_kesehatan) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">PPh 21 (Tunjangan)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="pph_21" id="pph_21" class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('pph_21', $slip->pph_21) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Potongan BPJS TK</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="potongan_bpjs_tk" id="potongan_bpjs_tk"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('potongan_bpjs_tk', $slip->potongan_bpjs_tk) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Potongan BPJS Kes.</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="potongan_bpjs_kesehatan" id="potongan_bpjs_kesehatan"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('potongan_bpjs_kesehatan', $slip->potongan_bpjs_kesehatan) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Potongan PPh 21</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="potongan_pph_21" id="potongan_pph_21"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('potongan_pph_21', $slip->potongan_pph_21) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sedekah Rombongan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="sedekah_rombongan" id="sedekah_rombongan"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('sedekah_rombongan', $slip->sedekah_rombongan) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Lain-lain</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="lain_lain" id="lain_lain"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('lain_lain', $slip->lain_lain) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Potongan Lainnya</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="potongan_lainnya" id="potongan_lainnya"
                                class="form-control entry-calc entry-calc-rupiah"
                                value="{{ old('potongan_lainnya', $slip->potongan_lainnya) }}">
                        </div>
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <h5 class="mb-3 text-info"><i class="ti ti-calendar-stats me-2"></i> 4. Kehadiran & Status</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <label class="form-label">Cuti (Hari)</label>
                        <input type="number" name="cuti" id="cuti" class="form-control"
                            value="{{ old('cuti', $slip->cuti) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Lembur (Kali)</label>
                        <input type="number" name="lembur_kali" id="lembur_kali" class="form-control"
                            value="{{ old('lembur_kali', $slip->kehadiran ? $slip->kehadiran->lembur : 0) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Terlambat (Kali)</label>
                        <input type="number" name="terlambat" id="terlambat" class="form-control"
                            value="{{ old('terlambat', $slip->terlambat) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Ijin Pulang Cepat</label>
                        <input type="number" name="ijin_pulang_cepat" id="ijin_pulang_cepat" class="form-control"
                            value="{{ old('ijin_pulang_cepat', $slip->ijin_pulang_cepat) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Ijin Tdk Masuk</label>
                        <input type="number" name="ijin_tidak_masuk" id="ijin_tidak_masuk" class="form-control"
                            value="{{ old('ijin_tidak_masuk', $slip->ijin_tidak_masuk) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">No Check In/Out</label>
                        <input type="number" name="no_check_in_or_out" id="no_check_in_or_out" class="form-control"
                            value="{{ old('no_check_in_or_out', $slip->no_check_in_or_out) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">No Check In & Out</label>
                        <input type="number" name="no_check_in_and_out" id="no_check_in_and_out" class="form-control"
                            value="{{ old('no_check_in_and_out', $slip->no_check_in_and_out) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kehadiran Lainnya</label>
                        <input type="number" name="kehadiran_lainnya" id="kehadiran_lainnya" class="form-control"
                            value="{{ old('kehadiran_lainnya', $slip->kehadiran_lainnya ?? 0) }}">
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <h5 class="mb-3 text-warning"><i class="ti ti-calculator me-2"></i> 5. Ringkasan & Kalkulasi Akhir</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted font-weight-bold">Subtotal Penerimaan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="text" id="calculated_penerimaan" class="form-control bg-light text-end fw-bold"
                                readonly value="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted font-weight-bold">Subtotal Potongan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="text" id="calculated_potongan"
                                class="form-control bg-light text-end fw-bold text-danger" readonly value="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-primary font-weight-bold">Nominal Transfer / THP</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary-subtle text-primary border-primary">Rp</span>
                            <input type="text" name="nominal_transfer" id="nominal_transfer"
                                class="form-control form-control-lg border-primary fw-bold text-end text-primary entry-calc-rupiah"
                                required value="{{ old('nominal_transfer', $slip->nominal_transfer) }}">
                        </div>
                        <input type="hidden" name="thp" id="thp" value="{{ old('thp', $slip->thp) }}">
                    </div>
                </div>

                <div class="mt-5 border-top pt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="ti ti-device-floppy me-1"></i> Update Slip Gaji
                    </button>
                    <a href="{{ route('slip-gaji.index') }}" class="btn btn-light btn-lg px-4 ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            // Helper: strip karakter non-digit, tangani desimal PHP & pemisah ribuan
            function formatRupiah(value) {
                if (value === undefined || value === null || value === '') return '0';
                var str = value.toString().trim();

                var dotCount = (str.match(/\./g) || []).length;
                var clean;

                if (dotCount === 1) {
                    var parts = str.split('.');
                    var afterDot = parts[1];
                    // Jika desimal PHP (misal 1500000.00) → ambil integer part saja
                    if (afterDot !== undefined && afterDot.length === 2 && /^\d+$/.test(afterDot)) {
                        clean = parts[0].replace(/[^0-9]/g, '');
                    } else {
                        // Satu titik sebagai pemisah ribuan (misal 1.500) → hapus titik
                        clean = str.replace(/\./g, '').replace(/[^0-9]/g, '');
                    }
                } else {
                    // Banyak titik = pemisah ribuan, atau tidak ada titik
                    clean = str.replace(/\./g, '').replace(/[^0-9]/g, '');
                }

                if (clean === '') return '0';
                var num = parseInt(clean, 10);
                if (isNaN(num) || num === 0) return '0';
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Helper: ambil nilai numerik murni dari input Rupiah
            function getRawValue(selector) {
                var valString = $(selector).val() || '0';
                var clean = valString.replace(/\./g, '');
                return parseFloat(clean) || 0;
            }

            // Format saat user mengetik di field Rupiah
            $(document).on('input', '.entry-calc-rupiah', function () {
                var el = this;
                var rawVal = el.value.replace(/\./g, '').replace(/[^0-9]/g, '');
                var formatted = rawVal === '' ? '0' : parseInt(rawVal, 10).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                // Hitung delta panjang untuk mempertahankan posisi kursor
                var oldLen = el.value.length;
                el.value = formatted;
                var newLen = formatted.length;
                var pos = el.selectionStart + (newLen - oldLen);
                el.setSelectionRange(Math.max(0, pos), Math.max(0, pos));
            });


            // Function to fetch employee details
            function fetchEmployeeDetails() {
                var empId = $('#id_karyawan').val();
                var bulan = $('#bulan').val();
                var tahun = $('#tahun').val();

                if (!empId) {
                    $('#karyawan_tanggal_masuk').val('');
                    $('#karyawan_divisi').val('');
                    $('#karyawan_klinik').val('');
                    $('#karyawan_no_wa').val('');
                    $('#karyawan_nomor_rekening').val('');
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
                        $('#karyawan_tanggal_masuk').val(data.karyawan.tanggal_masuk || '-');
                        $('#karyawan_divisi').val(data.karyawan.divisi);
                        $('#karyawan_klinik').val(data.karyawan.cabang);
                        $('#karyawan_no_wa').val(data.karyawan.no_wa);
                        $('#karyawan_nomor_rekening').val(data.karyawan.nomor_rekening);

                        // We do not overwrite current form field values when editing existing slip 
                        // UNLESS we just selected a different employee or the ajax returns fresh presence
                        if (data.kehadiran) {
                            $('#cuti').val(data.kehadiran.cuti || 0);
                            $('#lembur_kali').val(data.kehadiran.lembur || 0);
                            $('#terlambat').val(data.kehadiran.terlambat || 0);
                            $('#ijin_pulang_cepat').val(data.kehadiran.ijin_pulang_cepat || 0);
                            $('#ijin_tidak_masuk').val(data.kehadiran.ijin_tidak_masuk || 0);
                            $('#no_check_in_or_out').val(data.kehadiran.no_check_in_or_out || 0);
                            $('#no_check_in_and_out').val(data.kehadiran.no_check_in_and_out || 0);
                        }

                        // --- Hitung prosentase_gaji & jumlah_hari_gabung ---
                        if (data.karyawan.tanggal_masuk && data.karyawan.tanggal_masuk !== '-') {
                            var tglMasuk = new Date(data.karyawan.tanggal_masuk);
                            var today = new Date();

                            // Jumlah hari gabung selalu dihitung
                            var diffMs = today - tglMasuk;
                            var diffHari = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                            $('#jumlah_hari_gabung').val(diffHari);

                            // Selisih dalam bulan untuk menentukan prosentase
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
                        // --- End hitung ---

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

            function calculateReceipt() {
                var gaji = getRawValue('#gaji_pokok');
                var t_pengalaman = getRawValue('#t_pengalaman_kerja');
                var t_jabatan = getRawValue('#t_jabatan');
                var t_profesi = getRawValue('#t_profesi');
                var t_hadir = getRawValue('#t_kehadiran');
                var t_kinerja = getRawValue('#t_kinerja');
                var t_hari_raya = getRawValue('#t_hari_raya');
                var operasional = getRawValue('#operasional');
                var fee_beautician = getRawValue('#fee_beautician');
                var lembur = getRawValue('#nominal_lembur');

                var subtotalReceipts = gaji + t_pengalaman + t_jabatan + t_profesi + t_hadir + t_kinerja + t_hari_raya + operasional + fee_beautician + lembur;

                var percentage = parseFloat($('#prosentase_gaji').val());
                if (isNaN(percentage) || percentage <= 0) {
                    percentage = 100;
                }
                var totalReceipts = subtotalReceipts * (percentage / 100);
                $('#calculated_penerimaan').val(totalReceipts.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }));

                var punishment = getRawValue('#punishment');
                var bpjstk = getRawValue('#bpjstk');
                var bpjs_kes = getRawValue('#bpjs_kesehatan');
                var pph21 = getRawValue('#pph_21');

                var pot_bpjstk = getRawValue('#potongan_bpjs_tk');
                var pot_bpjs_kes = getRawValue('#potongan_bpjs_kesehatan');
                var pot_pph21 = getRawValue('#potongan_pph_21');

                var sedekah = getRawValue('#sedekah_rombongan');
                var lain = getRawValue('#lain_lain');
                var pot_lainnya = getRawValue('#potongan_lainnya');

                var totalDeductions = punishment + bpjstk + bpjs_kes + pph21 + pot_bpjstk + pot_bpjs_kes + pot_pph21 + sedekah + lain + pot_lainnya;
                $('#calculated_potongan').val(totalDeductions.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }));

                var netTransfer = totalReceipts - totalDeductions;
                $('#nominal_transfer').val(formatRupiah(netTransfer));
                $('#thp').val(netTransfer);
            }

            $(document).on('input change', '.entry-calc, #prosentase_gaji', calculateReceipt);

            // Initial formatting on load
            $('.entry-calc-rupiah').each(function () {
                var currentVal = $(this).val();
                $(this).val(formatRupiah(currentVal));
            });

            calculateReceipt();

            // Intercept form submit to clean formatting
            $('#slipGajiForm').on('submit', function () {
                $('.entry-calc-rupiah').each(function () {
                    var cleanVal = $(this).val().replace(/\./g, '');
                    $(this).val(cleanVal);
                });
                var cleanTransfer = $('#nominal_transfer').val().replace(/\./g, '');
                $('#nominal_transfer').val(cleanTransfer);
                $('#thp').val(cleanTransfer);
            });
        });
    </script>
@endpush