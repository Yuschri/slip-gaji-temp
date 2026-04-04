<?php

namespace App\Http\Controllers;

use App\Services\SlipGajiService;
use App\Http\Resources\SlipGajiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SlipGajiController extends Controller
{
    protected $slipGajiService;
    protected $qontakService;

    public function __construct(SlipGajiService $slipGajiService, \App\Services\QontakService $qontakService)
    {
        $this->slipGajiService = $slipGajiService;
        $this->qontakService = $qontakService;
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
        return view('pages.slip-gaji.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_karyawan' => 'required|string|max:255',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
            // Add other validations as needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->slipGajiService->store($request->all());

        return redirect()->route('slip-gaji.index')->with('success', 'Slip Gaji created successfully.');
    }

    public function edit($id)
    {
        $slip = $this->slipGajiService->findById($id);
        return view('pages.slip-gaji.edit', compact('slip'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_karyawan' => 'required|string|max:255',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->slipGajiService->update($id, $request->all());

        return redirect()->route('slip-gaji.index')->with('success', 'Slip Gaji updated successfully.');
    }

    public function destroy($id)
    {
        $this->slipGajiService->delete($id);
        return redirect()->route('slip-gaji.index')->with('success', 'Slip Gaji deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
        ]);

        try {
            $this->slipGajiService->import($request->file('file'), $request->bulan, $request->tahun);
            return redirect()->route('slip-gaji.index')->with('success', 'Slip Gaji imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing Slip Gaji: ' . $e->getMessage());
        }
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
            return $q->where('klinik', $klinik);
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
