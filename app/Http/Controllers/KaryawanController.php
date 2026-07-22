<?php

namespace App\Http\Controllers;

use App\Exports\KaryawanTemplateExport;
use App\Imports\KaryawanImport;
use App\Repositories\KaryawanRepository;
use App\Repositories\DivisiRepository;
use App\Repositories\JabatanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class KaryawanController extends Controller
{
    protected $karyawanRepository;
    protected $divisiRepository;
    protected $jabatanRepository;

    public function __construct(
        KaryawanRepository $karyawanRepository,
        DivisiRepository $divisiRepository,
        JabatanRepository $jabatanRepository
    ) {
        $this->karyawanRepository = $karyawanRepository;
        $this->divisiRepository = $divisiRepository;
        $this->jabatanRepository = $jabatanRepository;
    }

    /**
     * Display a listing of the employees.
     */
    public function index()
    {
        $karyawans = $this->karyawanRepository->all();
        return view('pages.karyawan.index', compact('karyawans'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $divisi = $this->divisiRepository->all();
        $jabatan = $this->jabatanRepository->all();
        return view('pages.karyawan.create', compact('divisi', 'jabatan'));
    }

    /**
     * Store a newly created employee in database.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string|max:20|unique:tb_karyawan,nip',
            'nik' => 'nullable|string|max:20|unique:tb_karyawan,nik',
            'nama_karyawan' => 'required|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'id_divisi' => 'required|exists:tb_divisi,id_divisi',
            'id_jabatan' => 'required|exists:tb_jabatan,id_jabatan',
            'no_wa' => 'nullable|string|max:20',
            'nomor_rekening' => 'nullable|string|max:50',
            'cabang' => 'required|string|max:20',
            'periode_cut_off' => 'required|integer|in:15,21',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->karyawanRepository->create($request->all());

        return redirect()->route('karyawan.index')->with('success', 'Karyawan created successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show($id)
    {
        $karyawan = $this->karyawanRepository->find($id);
        $skemaBpjstk = \App\Models\SkemaBPJSTK::where('is_active', true)->first();
        $skemaBpjsk = \App\Models\SkemaBPJSK::where('is_active', true)->first();
        return view('pages.karyawan.show', compact('karyawan', 'skemaBpjstk', 'skemaBpjsk'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit($id)
    {
        $karyawan = $this->karyawanRepository->find($id);
        $divisi = $this->divisiRepository->all();
        $jabatan = $this->jabatanRepository->all();
        return view('pages.karyawan.edit', compact('karyawan', 'divisi', 'jabatan'));
    }

    /**
     * Update the specified employee in database.
     */
    public function update(Request $request, $id)
    {
        // For unique validation, we check the exception rule of unique
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string|max:20|unique:tb_karyawan,nip,' . $id . ',id_karyawan',
            'nik' => 'nullable|string|max:20|unique:tb_karyawan,nik,' . $id . ',id_karyawan',
            'nama_karyawan' => 'required|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'id_divisi' => 'required|exists:tb_divisi,id_divisi',
            'id_jabatan' => 'required|exists:tb_jabatan,id_jabatan',
            'no_wa' => 'nullable|string|max:20',
            'nomor_rekening' => 'nullable|string|max:50',
            'cabang' => 'required|string|max:20',
            'periode_cut_off' => 'required|integer|in:15,21',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->karyawanRepository->update($id, $request->all());

        return redirect()->route('karyawan.index')->with('success', 'Karyawan updated successfully.');
    }

    /**
     * Remove the specified employee from database.
     */
    public function destroy($id)
    {
        $this->karyawanRepository->delete($id);
        return redirect()->route('karyawan.index')->with('success', 'Karyawan deleted successfully.');
    }

    /**
     * Store or update employee salary and deductions in one submit.
     */
    public function saveKompensasi(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'gaji_pokok' => 'required|numeric|min:0',
            't_pengalaman_kerja' => 'nullable|numeric|min:0',
            't_jabatan' => 'nullable|numeric|min:0',
            't_profesi' => 'nullable|numeric|min:0',
            't_kehadiran' => 'nullable|numeric|min:0',
            't_kinerja' => 'nullable|numeric|min:0',
            't_operasional' => 'nullable|numeric|min:0',
            'potongan_sedekah_rombongan' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menyimpan data gaji dan potongan');
        }

        $gajiData = [
            'gaji_pokok' => $request->gaji_pokok,
            't_pengalaman_kerja' => $request->t_pengalaman_kerja ?? 0,
            't_jabatan' => $request->t_jabatan ?? 0,
            't_profesi' => $request->t_profesi ?? 0,
            't_kehadiran' => $request->t_kehadiran ?? 0,
            't_kinerja' => $request->t_kinerja ?? 0,
            't_operasional' => $request->t_operasional ?? 0,
        ];

        $potonganData = [
            'potongan_sedekah_rombongan' => $request->potongan_sedekah_rombongan,
        ];

        $this->karyawanRepository->updateOrCreateKompensasi($id, $gajiData, $potonganData);

        return redirect()->route('karyawan.show', $id)->with('success', 'Data gaji dan potongan karyawan berhasil disimpan.');
    }

    /**
     * Store or update employee BPJS Ketenagakerjaan and BPJS Kesehatan data.
     */
    public function saveBpjs(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nomor_referensi' => 'nullable|string|max:100',
            'upah_didaftarkan_tk' => 'required|numeric|min:0',
            'iuran_jkk' => 'required|numeric|min:0',
            'iuran_jkm' => 'required|numeric|min:0',
            'pemberi_kerja' => 'required|numeric|min:0',
            'tenaga_kerja' => 'required|numeric|min:0',
            'total_iuran' => 'required|numeric|min:0',
            'no_jkn_peserta' => 'required|string|max:50',
            'beban_bpjsk' => 'required|integer|min:0',
            'npp' => 'required|string|max:50',
            'upah_didaftarkan_ks' => 'required|numeric|min:0',
            'premi' => 'required|numeric|min:0',
            'tanggungan_perusahaan' => 'required|numeric|min:0',
            'tanggungan_karyawan' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menyimpan data BPJS.');
        }

        $bpjstkData = [
            'id_karyawan' => $id,
            'no_referensi' => $request->nomor_referensi,
            'upah_didaftarkan' => $request->upah_didaftarkan_tk,
            'iuran_jkk' => $request->iuran_jkk,
            'iuran_jkm' => $request->iuran_jkm,
            'pemberi_kerja' => $request->pemberi_kerja,
            'tenaga_kerja' => $request->tenaga_kerja,
            'total_iuran' => $request->total_iuran,
        ];

        $bpjskData = [
            'id_karyawan' => $id,
            'no_jkn_peserta' => $request->no_jkn_peserta,
            'beban_bpjsk' => $request->beban_bpjsk,
            'npp' => $request->npp,
            'upah_didaftarkan' => $request->upah_didaftarkan_ks,
            'premi' => $request->premi,
            'tanggungan_perusahaan' => $request->tanggungan_perusahaan,
            'tanggungan_karyawan' => $request->tanggungan_karyawan,
        ];

        $this->karyawanRepository->updateOrCreateBpjs($id, $bpjstkData, $bpjskData);

        return redirect()->route('karyawan.show', $id)->with('success', 'Data BPJS karyawan berhasil disimpan.');
    }

    /**
     * Store or update employee PPh 21 data.
     */
    public function savePph21(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'identitas' => 'required|string|max:20',
            'ptkp' => 'required|string|max:20',
            'kategori' => 'required|string|max:2',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menyimpan data PPh 21.');
        }

        $pphData = [
            'identitas' => $request->identitas,
            'ptkp' => $request->ptkp,
            'kategori' => $request->kategori,
        ];

        $this->karyawanRepository->updateOrCreatePph21($id, $pphData);

        return redirect()->route('karyawan.show', $id)->with('success', 'Data PPh 21 karyawan berhasil disimpan.');
    }

    /**
     * Download Excel template for bulk karyawan import.
     */
    public function downloadTemplate()
    {
        return Excel::download(new KaryawanTemplateExport(), 'Template_Import_Karyawan.xlsx');
    }

    /**
     * Import karyawan data from uploaded Excel file.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'file_import.required' => 'File Excel wajib dipilih.',
            'file_import.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_import.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            $import = new KaryawanImport();
            Excel::import($import, $request->file('file_import'));

            $message = "Import selesai. {$import->imported} karyawan berhasil diimport.";
            if ($import->skipped > 0) {
                $message .= " {$import->skipped} baris dilewati.";
            }
            if (!empty($import->errors)) {
                $errorText = implode(' | ', array_slice($import->errors, 0, 5));
                return redirect()->route('karyawan.index')
                    ->with('success', $message)
                    ->with('import_errors', $import->errors);
            }

            return redirect()->route('karyawan.index')->with('success', $message);
        } catch (\Throwable $e) {
            return redirect()->route('karyawan.index')
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
