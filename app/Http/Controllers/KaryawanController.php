<?php

namespace App\Http\Controllers;

use App\Repositories\KaryawanRepository;
use App\Repositories\DivisiRepository;
use App\Repositories\JabatanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'nama_karyawan' => 'required|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'id_divisi' => 'required|exists:tb_divisi,id_divisi',
            'id_jabatan' => 'required|exists:tb_jabatan,id_jabatan',
            'no_wa' => 'nullable|string|max:20',
            'nomor_rekening' => 'nullable|string|max:50',
            'cabang' => 'required|string|max:20',
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
        return view('pages.karyawan.show', compact('karyawan'));
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
            'nama_karyawan' => 'required|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'id_divisi' => 'required|exists:tb_divisi,id_divisi',
            'id_jabatan' => 'required|exists:tb_jabatan,id_jabatan',
            'no_wa' => 'nullable|string|max:20',
            'nomor_rekening' => 'nullable|string|max:50',
            'cabang' => 'required|string|max:20',
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
}
