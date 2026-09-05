<?php

namespace App\Http\Controllers;

use App\Exports\SlipGajiExport;
use App\Exports\SlipGajiTemplateExport;
use App\Services\SlipGajiService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Karyawan;

class SlipGajiController extends Controller
{
    protected $slipGajiService;
    protected $qontakService;
    protected $lemburImportService;
    protected $bpjstkImportService;

    public function __construct(
        SlipGajiService $slipGajiService,
        \App\Services\QontakService $qontakService,
        \App\Services\LemburImportService $lemburImportService,
        \App\Services\BpjstkImportService $bpjstkImportService
    ) {
        $this->slipGajiService = $slipGajiService;
        $this->qontakService = $qontakService;
        $this->lemburImportService = $lemburImportService;
        $this->bpjstkImportService = $bpjstkImportService;
    }

    public function index()
    {
        $slips = $this->slipGajiService->getAll();
        $kliniks = $this->slipGajiService->getUniqueKlinik();
        $tahuns = $this->slipGajiService->getUniqueTahun();
        return view('pages.slip-gaji.index', compact('slips', 'kliniks', 'tahuns'));
    }

    public function create()
    {
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        return view('pages.slip-gaji.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bulan' => 'required',
            'tahun' => 'required',
            'gaji_pokok' => 'required',
            'id_karyawan' => [
                'required',
                'exists:tb_karyawan,id_karyawan',
                Rule::unique('tb_slip_gaji')->where(function ($query) use ($request) {
                    return $query->where('bulan', $request->bulan)
                        ->where('tahun', $request->tahun);
                }),
            ],
        ], [
            'id_karyawan.unique' => 'Slip gaji untuk pegawai, bulan, dan tahun yang dipilih sudah ada.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->slipGajiService->store($request->all());

        return redirect()->route('slip-gaji.index')->with('success', 'Slip Gaji berhasil disimpan.');
    }

    public function edit($id)
    {
        $slip = $this->slipGajiService->findById($id);
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        return view('pages.slip-gaji.edit', compact('slip', 'karyawans'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_karyawan' => [
                'required',
                'exists:tb_karyawan,id_karyawan',
                Rule::unique('tb_slip_gaji')
                    ->ignore($id, 'id_slip')
                    ->where(function ($query) use ($request) {
                        return $query->where('bulan', $request->bulan)
                            ->where('tahun', $request->tahun);
                    }),
            ],
            'bulan' => 'required',
            'tahun' => 'required',
            'gaji_pokok' => 'required',
        ], [
            'id_karyawan.unique' => 'Slip gaji untuk pegawai, bulan, dan tahun yang dipilih sudah ada.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->slipGajiService->update($id, $request->all());

        return redirect()->route('slip-gaji.index')->with('success', 'Slip Gaji berhasil diperbarui.');
    }

    public function getKaryawanDetails($id, Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $karyawan = Karyawan::with(['divisi', 'jabatan', 'gaji', 'potongan', 'bpjstk', 'bpjsk', 'pph21'])->find($id);

        if (!$karyawan) {
            return response()->json(['error' => 'Karyawan not found'], 404);
        }

        // Find attendance for the specified month and year
        $kehadiran = null;
        if ($bulan && $tahun) {
            $kehadiran = \App\Models\Kehadiran::where('id_karyawan', $id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();
        }

        // Hitung akhir masa training (3 bulan penuh setelah tanggal masuk)
        $akhirTraining = null;
        if ($karyawan->tanggal_masuk) {
            $akhirTraining = \Carbon\Carbon::parse($karyawan->tanggal_masuk)
                ->addMonths(3)
                ->subDay()
                ->format('Y-m-d');
        }

        return response()->json([
            'karyawan' => [
                'nama' => $karyawan->nama_karyawan,
                'nip' => $karyawan->nip ?? '-',
                'tanggal_masuk' => $karyawan->tanggal_masuk ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('Y-m-d') : null,
                'akhir_training' => $akhirTraining,
                'divisi' => $karyawan->divisi ? $karyawan->divisi->nama_divisi : '-',
                'cabang' => $karyawan->cabang ?? '-',
                'no_wa' => $karyawan->no_wa ?? '-',
                'nomor_rekening' => $karyawan->nomor_rekening ?? '-',
                'periode_cut_off' => $karyawan->periode_cut_off ?? 21,
            ],
            'gaji' => $karyawan->gaji ? [
                'gaji_pokok' => $karyawan->gaji->gaji_pokok,
                't_pengalaman_kerja' => $karyawan->gaji->t_pengalaman_kerja,
                't_jabatan' => $karyawan->gaji->t_jabatan,
                't_profesi' => $karyawan->gaji->t_profesi,
                't_kehadiran' => $karyawan->gaji->t_kehadiran,
                't_kinerja' => $karyawan->gaji->t_kinerja,
                't_operasional' => $karyawan->gaji->t_operasional,
            ] : null,
            'potongan' => $karyawan->potongan ? [
                'potongan_sedekah_rombongan' => $karyawan->potongan->potongan_sedekah_rombongan,
            ] : null,
            'bpjstk' => $karyawan->bpjstk ? [
                'no_referensi' => $karyawan->bpjstk->no_referensi,
                'tanggal_kepesertaan' => $karyawan->bpjstk->tanggal_kepesertaan,
                'upah_didaftarkan' => $karyawan->bpjstk->upah_didaftarkan,
                'iuran_jkk' => $karyawan->bpjstk->iuran_jkk,
                'iuran_jkm' => $karyawan->bpjstk->iuran_jkm,
                'pemberi_kerja' => $karyawan->bpjstk->pemberi_kerja,
                'tenaga_kerja' => $karyawan->bpjstk->tenaga_kerja,
                'total_iuran' => $karyawan->bpjstk->total_iuran,
            ] : null,
            'bpjsk' => $karyawan->bpjsk ? [
                'no_jkn_peserta' => $karyawan->bpjsk->no_jkn_peserta,
                'beban_bpjsk' => $karyawan->bpjsk->beban_bpjsk,
                'npp' => $karyawan->bpjsk->npp,
                'upah_didaftarkan' => $karyawan->bpjsk->upah_didaftarkan,
                'premi' => $karyawan->bpjsk->premi,
                'tanggungan_perusahaan' => $karyawan->bpjsk->tanggungan_perusahaan,
                'tanggungan_karyawan' => $karyawan->bpjsk->tanggungan_karyawan,
            ] : null,
            'pph21' => $karyawan->pph21 ? [
                'identitas' => $karyawan->pph21->identitas,
                'ptkp' => $karyawan->pph21->ptkp,
                'kategori' => $karyawan->pph21->kategori,
            ] : null,
            'kehadiran' => $kehadiran ? [
                'id_kehadiran' => $kehadiran->id_kehadiran,
                'cuti' => $kehadiran->cuti,
                'lembur' => $kehadiran->lembur,
                'lembur_menit' => $kehadiran->lembur_menit,
                'terlambat' => $kehadiran->terlambat,
                'terlambat_menit' => $kehadiran->terlambat_menit,
                'ijin_pulang_cepat' => $kehadiran->ijin_pulang_cepat,
                'ijin_tidak_masuk' => $kehadiran->ijin_tidak_masuk,
                'no_check_in_or_out' => $kehadiran->no_check_in_or_out,
                'no_check_in_and_out' => $kehadiran->no_check_in_and_out,
            ] : null,
        ]);
    }

    /**
     * Hitung gaji terakhir karyawan yang resign.
     * Menggunakan rumus pro-rata berbasis cutoff dengan mempertimbangkan masa training.
     */
    public function calculateGajiResign(Request $request)
    {
        $tanggalMasuk = $request->input('tanggal_masuk');   // Y-m-d
        $tanggalResign = $request->input('tanggal_resign');   // Y-m-d
        $bulan = (int) $request->input('bulan');       // bulan payroll
        $tahun = (int) $request->input('tahun');       // tahun payroll
        $cutoff = (int) $request->input('cutoff', 21); // 15 atau 21
        $thpFull = (float) $request->input('thp_full', 0); // THP 100%

        if (!$tanggalMasuk || !$tanggalResign || !$bulan || !$tahun || $thpFull <= 0) {
            return response()->json(['error' => 'Parameter tidak lengkap'], 422);
        }

        $tglMasuk = \Carbon\Carbon::parse($tanggalMasuk)->startOfDay();
        $tglResign = \Carbon\Carbon::parse($tanggalResign)->startOfDay();
        $akhirTraining = $tglMasuk->copy()->addMonths(3)->subDay(); // masuk + 3 bulan - 1 hari

        // --- Penentuan Periode Payroll ---
        // Akhir Periode  = tanggal {cutoff} bulan & tahun payroll
        $periodeAkhir = \Carbon\Carbon::create($tahun, $bulan, $cutoff)->startOfDay();
        // Awal Periode   = tanggal ({cutoff}+1) bulan sebelumnya
        $periodeAwal = $periodeAkhir->copy()->subMonthNoOverflow()->addDay(); // cutoff+1 bulan lalu

        $totalHariPeriode = $periodeAwal->diffInDays($periodeAkhir) + 1;

        // Tanggal kerja efektif: maks antara tglMasuk dan periodeAwal
        $tglMulaiHitung = $tglMasuk->gt($periodeAwal) ? $tglMasuk->copy() : $periodeAwal->copy();

        // Tanggal akhir kerja efektif: min antara tglResign dan periodeAkhir
        $tglAkhirHitung = $tglResign->lt($periodeAkhir) ? $tglResign->copy() : $periodeAkhir->copy();

        // Belum mulai bekerja di periode ini
        if ($tglMasuk->gt($periodeAkhir) || $tglResign->lt($periodeAwal)) {
            return response()->json([
                'gaji_resign' => 0,
                'periode_awal' => $periodeAwal->format('Y-m-d'),
                'periode_akhir' => $periodeAkhir->format('Y-m-d'),
                'total_hari_periode' => $totalHariPeriode,
                'hari_kerja' => 0,
                'akhir_training' => $akhirTraining->format('Y-m-d'),
                'skenario' => 'belum_mulai_atau_sudah_selesai',
            ]);
        }

        $hariKerjaTotal = $tglMulaiHitung->diffInDays($tglAkhirHitung) + 1;
        $gajiResign = 0;
        $skenario = '';

        // SKENARIO A: Masa Transisi - akhirTraining jatuh di dalam periode & sebelum tglResign
        if ($akhirTraining->gte($tglMulaiHitung) && $akhirTraining->lt($tglAkhirHitung)) {
            $hariTraining = $tglMulaiHitung->diffInDays($akhirTraining) + 1;
            $gajiTraining = ($hariTraining / $totalHariPeriode) * 0.8 * $thpFull;

            $tglMulaiLulus = $akhirTraining->copy()->addDay();
            $hariLulus = $tglMulaiLulus->diffInDays($tglAkhirHitung) + 1;
            $gajiLulus = ($hariLulus / $totalHariPeriode) * 1.0 * $thpFull;

            $gajiResign = $gajiTraining + $gajiLulus;
            $skenario = 'A_transisi';
        }
        // SKENARIO B: Masih full dalam masa training s/d akhir periode / akhir resign
        elseif ($tglAkhirHitung->lte($akhirTraining)) {
            $gajiResign = ($hariKerjaTotal / $totalHariPeriode) * 0.8 * $thpFull;
            $skenario = ($hariKerjaTotal === $totalHariPeriode) ? 'B_full_training' : 'B_prorata_training';
        }
        // SKENARIO C: Sudah lulus training
        else {
            $gajiResign = ($hariKerjaTotal / $totalHariPeriode) * 1.0 * $thpFull;
            $skenario = ($hariKerjaTotal === $totalHariPeriode) ? 'C_full_lulus' : 'C_prorata_lulus';
        }

        return response()->json([
            'gaji_resign' => round($gajiResign),
            'periode_awal' => $periodeAwal->format('Y-m-d'),
            'periode_akhir' => $periodeAkhir->format('Y-m-d'),
            'total_hari_periode' => $totalHariPeriode,
            'hari_kerja' => $hariKerjaTotal,
            'akhir_training' => $akhirTraining->format('Y-m-d'),
            'skenario' => $skenario,
        ]);
    }

    public function calculatePph21(Request $request)
    {
        $kategori = $request->input('kategori');
        $totalGaji = (float) $request->input('total_gaji', 0);

        $golongan = $kategori;

        $skema = \App\Models\SkemaPph21::where('golongan', $golongan)
            ->where('batas_uang', '<=', $totalGaji)
            ->orderBy('batas_uang', 'desc')
            ->first();

        $ter = $skema ? (float) $skema->persen : 0;
        $pph21 = $totalGaji * $ter;

        return response()->json([
            'golongan' => $golongan,
            'ter' => $ter,
            'pph21' => $pph21,
        ]);
    }

    public function destroy($id)
    {
        $this->slipGajiService->delete($id);
        return redirect()->route('slip-gaji.index')->with('success', 'Slip Gaji deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:5120',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes' => 'File harus berformat .xlsx.',
            'file.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            $import = $this->slipGajiService->import($request->file('file'), $request->bulan, $request->tahun);

            $message = "Import selesai. {$import->imported} slip gaji berhasil diproses.";
            if ($import->skipped > 0) {
                $message .= " {$import->skipped} baris dilewati.";
            }

            $redirect = redirect()->route('slip-gaji.index')->with('success', $message);
            if (!empty($import->errors)) {
                $redirect->with('import_errors', $import->errors);
            }

            return $redirect;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing Slip Gaji: ' . $e->getMessage());
        }
    }

    public function checkLemburDuplicate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx|max:5120',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
        ], [
            'file.required' => 'File Excel lembur wajib dipilih.',
            'file.mimes' => 'File harus berformat .xlsx.',
            'file.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $result = $this->lemburImportService->checkDuplicates(
                $request->file('file'),
                $request->bulan,
                $request->tahun
            );

            return response()->json([
                'success' => true,
                'has_duplicates' => $result['has_duplicates'],
                'duplicates' => $result['duplicates'],
                'count' => $result['count'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa file Excel lembur: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function importLembur(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:5120',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
        ], [
            'file.required' => 'File Excel lembur wajib dipilih.',
            'file.mimes' => 'File harus berformat .xlsx.',
            'file.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        $overwrite = $request->boolean('overwrite', false);

        try {
            $result = $this->lemburImportService->processImport(
                $request->file('file'),
                $request->bulan,
                $request->tahun,
                $overwrite
            );

            if ($result['failed_count'] > 0 && !empty($result['error_file']) && file_exists($result['error_file'])) {
                $filename = 'Hasil_Import_Lembur_Gagal_' . date('Ymd_His') . '.xlsx';
                return response()->download($result['error_file'], $filename)->deleteFileAfterSend(true);
            }

            $message = "Import Lembur berhasil! {$result['success_count']} slip gaji berhasil diproses.";
            return redirect()->route('slip-gaji.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error import lembur: ' . $e->getMessage());
        }
    }

    public function checkBpjstkDuplicate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf|max:10240',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
        ], [
            'file.required' => 'File PDF BPJSTK wajib dipilih.',
            'file.mimes' => 'File harus berformat .pdf.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $result = $this->bpjstkImportService->checkDuplicates(
                $request->file('file'),
                $request->bulan,
                $request->tahun
            );

            return response()->json([
                'success' => true,
                'has_duplicates' => $result['has_duplicates'],
                'duplicates' => $result['duplicates'],
                'count' => $result['count'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa file PDF BPJSTK: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function importBpjstkPdf(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
        ], [
            'file.required' => 'File PDF BPJSTK wajib dipilih.',
            'file.mimes' => 'File harus berformat .pdf.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $overwrite = $request->boolean('overwrite', false);

        try {
            $result = $this->bpjstkImportService->processImport(
                $request->file('file'),
                $request->bulan,
                $request->tahun,
                $overwrite
            );

            if ($result['failed_count'] > 0 && !empty($result['error_file']) && file_exists($result['error_file'])) {
                $filename = 'Hasil_Import_BPJSTK_Gagal_' . date('Ymd_His') . '.pdf';
                return response()->download($result['error_file'], $filename)->deleteFileAfterSend(true);
            }

            $message = "Import PDF BPJSTK berhasil! {$result['success_count']} data BPJSTK karyawan berhasil diproses.";
            return redirect()->route('slip-gaji.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error import PDF BPJSTK: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new SlipGajiTemplateExport(), 'Template_Import_Slip_Gaji.xlsx');
    }

    public function exportExcel(Request $request)
    {
        $klinik = $request->input('klinik');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        if ($bulan && !is_numeric($bulan)) {
            $monthMap = [
                'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6,
                'July' => 7, 'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12,
            ];
            $bulan = $monthMap[$bulan] ?? null;
        }

        $query = \App\Models\SlipGaji::query()
            ->with(['karyawan.divisi', 'karyawan.jabatan'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc');

        if ($klinik) {
            $query->whereHas('karyawan', function ($q) use ($klinik) {
                $q->where('cabang', 'like', '%' . $klinik . '%');
            });
        }

        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        $slips = $query->get();

        $rows = [];
        foreach ($slips as $index => $slip) {
            $tanggalMasuk = $slip->karyawan && $slip->karyawan->tanggal_masuk
                ? \Carbon\Carbon::parse($slip->karyawan->tanggal_masuk)
                : null;

            $thp = (float) $slip->gaji_pokok
                + (float) $slip->t_jabatan
                + (float) $slip->t_profesi
                + (float) $slip->t_kehadiran
                + (float) $slip->t_kinerja;

            $prosentaseValue = $this->calculateProrataNominalForExport($slip);

            $tanggalAcuan = $slip->created_at
                ? \Carbon\Carbon::parse($slip->created_at)
                : \Carbon\Carbon::create((int) $slip->tahun, (int) $slip->bulan, 1)->endOfMonth();
            $jmlHariGabung = $tanggalMasuk
                ? max(0, $tanggalMasuk->startOfDay()->diffInDays($tanggalAcuan->copy()->startOfDay()))
                : 0;

            $rows[] = [
                $index + 1,
                $slip->nip,
                $slip->no_wa,
                $slip->nama_karyawan,
                $tanggalMasuk ? $tanggalMasuk->format('Y-m-d') : '',
                $slip->divisi,
                $slip->nomor_rekening,
                (float) $thp,
                (float) $slip->gaji_pokok,
                (float) $slip->t_jabatan,
                (float) $slip->t_profesi,
                (float) $slip->t_kehadiran,
                (float) $slip->t_kinerja,
                (float) $prosentaseValue,
                (int) $jmlHariGabung,
                (float) $slip->nominal_lembur,
                (float) $slip->fee_beautician,
                (float) $slip->punishment,
                (float) $slip->bpjstk_karyawan,
                (float) $slip->bpjsk_karyawan,
                (float) $slip->pph21,
                (float) $slip->sedekah_rombongan,
                (float) $slip->potongan_lainnya,
                (float) $slip->total_diterima,
                (int) $slip->lembur_kali,
                (int) $slip->lembur_menit,
                (int) $slip->terlambat_kali,
                (int) $slip->terlambat_menit,
                (int) $slip->ijin_pulang_awal,
                (int) $slip->ijin_tidak_masuk,
                (int) $slip->no_checkin_or_checkout,
                (int) $slip->no_checkin_and_checkout,
                (int) $slip->cuti,
                (int) $slip->kehadiran_lainnya,
            ];
        }

        $filterLabel = 'Filter: ';
        $filterParts = [];
        if ($klinik) $filterParts[] = 'Klinik ' . $klinik;
        if ($bulan) $filterParts[] = 'Bulan ' . date('F', mktime(0, 0, 0, $bulan, 10));
        if ($tahun) $filterParts[] = 'Tahun ' . $tahun;
        $filterLabel .= $filterParts ? implode(' | ', $filterParts) : 'Semua data';

        $filename = 'Slip_Gaji_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new SlipGajiExport($rows, $filterLabel), $filename);
    }

    /**
     * Mirror create.blade.php prorata logic for export display column "PROSENTASE GAJI".
     */
    private function calculateProrataNominalForExport(\App\Models\SlipGaji $slip): float
    {
        $karyawan = $slip->karyawan;

        $prorataBase = (float) $slip->gaji_pokok
            + (float) $slip->t_pengalaman_kerja
            + (float) $slip->t_jabatan
            + (float) $slip->t_profesi
            + (float) $slip->t_kehadiran
            + (float) $slip->t_kinerja
            + (float) $slip->t_operasional;

        if ($prorataBase <= 0) {
            return 0;
        }

        if (!$karyawan || empty($karyawan->tanggal_masuk)) {
            return round((float) $prorataBase, 2);
        }

        $cutoff = (int) ($karyawan->periode_cut_off ?? 21);
        if (!in_array($cutoff, [15, 21], true)) {
            $cutoff = 21;
        }

        $tglMasuk = \Carbon\Carbon::parse($karyawan->tanggal_masuk)->startOfDay();
        $akhirTraining = $tglMasuk->copy()->addMonths(3)->subDay();

        $periodeAkhir = \Carbon\Carbon::create((int) $slip->tahun, (int) $slip->bulan, $cutoff)->startOfDay();
        $periodeAwal = $periodeAkhir->copy()->subMonthNoOverflow()->addDay();

        $prorataNominal = $this->hitungGajiPerPeriodeForExport(
            $tglMasuk,
            $akhirTraining,
            $periodeAwal,
            $periodeAkhir,
            $prorataBase
        );

        if (!empty($slip->is_resign) && !empty($slip->tanggal_resign)) {
            $tglResign = \Carbon\Carbon::parse($slip->tanggal_resign)->startOfDay();
            $prorataNominal = $this->hitungGajiResignProrataForExport(
                $tglMasuk,
                $tglResign,
                $akhirTraining,
                $periodeAwal,
                $periodeAkhir,
                $prorataBase
            );
        }

        return round($prorataNominal, 2);
    }

    private function hitungGajiPerPeriodeForExport(
        \Carbon\Carbon $tglMasuk,
        \Carbon\Carbon $akhirTraining,
        \Carbon\Carbon $periodeAwal,
        \Carbon\Carbon $periodeAkhir,
        float $thpFull
    ): float {
        $totalHariPeriode = $periodeAwal->diffInDays($periodeAkhir) + 1;

        if ($tglMasuk->gt($periodeAkhir)) {
            return 0;
        }

        $tglMulaiHitung = $tglMasuk->gt($periodeAwal) ? $tglMasuk->copy() : $periodeAwal->copy();

        if ($akhirTraining->gte($tglMulaiHitung) && $akhirTraining->lt($periodeAkhir)) {
            $hariTraining = $tglMulaiHitung->diffInDays($akhirTraining) + 1;
            $gajiTraining = ($hariTraining / $totalHariPeriode) * 0.8 * $thpFull;

            $tglMulaiLulus = $akhirTraining->copy()->addDay();
            $hariLulus = $tglMulaiLulus->diffInDays($periodeAkhir) + 1;
            $gajiLulus = ($hariLulus / $totalHariPeriode) * $thpFull;

            return $gajiTraining + $gajiLulus;
        }

        $hariKerja = $tglMulaiHitung->diffInDays($periodeAkhir) + 1;

        if ($periodeAkhir->lte($akhirTraining)) {
            return ($hariKerja / $totalHariPeriode) * 0.8 * $thpFull;
        }

        return ($hariKerja / $totalHariPeriode) * $thpFull;
    }

    private function hitungGajiResignProrataForExport(
        \Carbon\Carbon $tglMasuk,
        \Carbon\Carbon $tglResign,
        \Carbon\Carbon $akhirTraining,
        \Carbon\Carbon $periodeAwal,
        \Carbon\Carbon $periodeAkhir,
        float $thpFull
    ): float {
        $totalHariPeriode = $periodeAwal->diffInDays($periodeAkhir) + 1;

        if ($tglMasuk->gt($periodeAkhir) || $tglResign->lt($periodeAwal)) {
            return 0;
        }

        $tglMulaiHitung = $tglMasuk->gt($periodeAwal) ? $tglMasuk->copy() : $periodeAwal->copy();
        $tglAkhirHitung = $tglResign->lt($periodeAkhir) ? $tglResign->copy() : $periodeAkhir->copy();

        $hariKerjaTotal = $tglMulaiHitung->diffInDays($tglAkhirHitung) + 1;
        if ($hariKerjaTotal <= 0) {
            return 0;
        }

        if ($akhirTraining->gte($tglMulaiHitung) && $akhirTraining->lt($tglAkhirHitung)) {
            $hariTraining = $tglMulaiHitung->diffInDays($akhirTraining) + 1;
            $gajiTraining = ($hariTraining / $totalHariPeriode) * 0.8 * $thpFull;

            $tglMulaiLulus = $akhirTraining->copy()->addDay();
            $hariLulus = $tglMulaiLulus->diffInDays($tglAkhirHitung) + 1;
            $gajiLulus = ($hariLulus / $totalHariPeriode) * $thpFull;

            return $gajiTraining + $gajiLulus;
        }

        if ($tglAkhirHitung->lte($akhirTraining)) {
            return ($hariKerjaTotal / $totalHariPeriode) * 0.8 * $thpFull;
        }

        return ($hariKerjaTotal / $totalHariPeriode) * $thpFull;
    }

    public function exportPdf($id)
    {
        try {
            $slip = $this->slipGajiService->findById($id);
            $filename = 'Slip_Gaji_' . str_replace(' ', '_', $slip->nama_karyawan) . '_' . $slip->bulan . '_' . $slip->tahun . '.pdf';

            return $this->slipGajiService->generatePdf($id)->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    public function viewPdf($id)
    {
        try {
            $slip = $this->slipGajiService->findById($id);
            $filename = 'Slip_Gaji_' . str_replace(' ', '_', $slip->nama_karyawan) . '_' . $slip->bulan . '_' . $slip->tahun . '.pdf';

            return $this->slipGajiService->generatePdf($id)->stream($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error viewing PDF: ' . $e->getMessage());
        }
    }

    public function broadcastSingle($id)
    {
        try {
            $slip = $this->slipGajiService->findById($id);
            $monthYear = date('F Y', mktime(0, 0, 0, $slip->bulan, 1, $slip->tahun));

            // 1. Generate PDF in memory
            $pdf = $this->slipGajiService->generatePdf($id);
            $filename = 'Slip_Gaji_' . str_replace(' ', '_', $slip->nama_karyawan) . '_' . $slip->bulan . '_' . $slip->tahun . '.pdf';

            // 2. Upload to Qontak
            $uploadedUrl = $this->qontakService->uploadFile($pdf->output(), $filename);

            if (!$uploadedUrl) {
                throw new \Exception('Failed to obtain URL from Qontak Uploader.');
            }

            // 3. Prepare parameters (Param 1: Month Year)
            $params = [
                ['key' => '1', 'value' => $monthYear, 'value_text' => $monthYear],
            ];

            // 4. Prepare header (Document)
            $header = [
                'format' => 'DOCUMENT',
                'params' => [
                    ['key' => 'url', 'value' => $uploadedUrl],
                    ['key' => 'filename', 'value' => $filename],
                ]
            ];

            $this->qontakService->sendDirectBroadcast(
                $slip->no_wa,
                $slip->nama_karyawan,
                [['key' => 'bulan_tahun', 'value' => $monthYear]],
                $header
            );

            return redirect()->back()->with('success', 'Broadcast sent successfully to ' . $slip->nama_karyawan);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Broadcast failed: ' . $e->getMessage());
        }
    }

    public function broadcastBulk(Request $request)
    {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        $klinik = $request->klinik;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $slips = \App\Models\SlipGaji::when($klinik, function ($q) use ($klinik) {
            return $q->whereHas('karyawan', function ($query) use ($klinik) {
                $query->where('cabang', $klinik);
            });
        })->when($bulan, function ($q) use ($bulan) {
            return $q->where('bulan', $bulan);
        })->when($tahun, function ($q) use ($tahun) {
            return $q->where('tahun', $tahun);
        })->get();

        if ($slips->isEmpty()) {
            return redirect()->back()->with('error', 'No data found for the selected filter.');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($slips as $slip) {
            try {
                $monthYear = date('F Y', mktime(0, 0, 0, $slip->bulan, 1, $slip->tahun));

                // 1. Generate and Upload PDF
                $pdf = $this->slipGajiService->generatePdf($slip->id_slip);
                $filename = 'Slip_Gaji_' . str_replace(' ', '_', $slip->nama_karyawan) . '_' . $slip->bulan . '_' . $slip->tahun . '.pdf';

                $uploadedUrl = $this->qontakService->uploadFile($pdf->output(), $filename);

                $header = [
                    'format' => 'DOCUMENT',
                    'params' => [
                        ['key' => 'url', 'value' => $uploadedUrl],
                        ['key' => 'filename', 'value' => $filename],
                    ]
                ];

                $this->qontakService->sendDirectBroadcast(
                    $slip->no_wa,
                    $slip->nama_karyawan,
                    [['key' => 'bulan_tahun', 'value' => $monthYear]],
                    $header
                );
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                \Illuminate\Support\Facades\Log::error("Bulk Broadcast Error for {$slip->nama_karyawan}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "Bulk broadcast completed: $successCount success, $failCount failed.");
    }
}
