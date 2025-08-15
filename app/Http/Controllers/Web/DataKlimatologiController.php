<?php

namespace App\Http\Controllers\Web;

use App\Models\DataKlimatologi;
use App\Models\PosPantau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class DataKlimatologiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DataKlimatologi::with('posPantau')->select('data_klimatologi.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('pos_pantau_nama', fn($row) => $row->posPantau->nama_pos ?? '-')
                ->addColumn('tanggal_formatted', fn($row) => $row->created_at ? $row->created_at->format('d/m/Y') : '-')
                ->addColumn('action', function($row) {
                    $btn = '<div class="flex gap-2">';
                    $btn .= '<button class="btn btn-sm btn-info" onclick="showDetail('.$row->id.')"><i class="fas fa-eye"></i></button>';
                    $btn .= '<a href="'.route('admin.hidrologi.klimatologi.edit', $row->id).'" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>';
                    $btn .= '<button class="btn btn-sm btn-error" onclick="deleteData('.$row->id.')"><i class="fas fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.pages.dt_klimatologi.index');
    }

    public function create()
    {
        $posPantauList = PosPantau::all();
        return view('admin.pages.dt_klimatologi.create', compact('posPantauList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date',
            'pos_pantau_id'     => 'required|exists:pos_pantau,id',
            'kecepatan_angin'   => 'nullable|numeric|min:0',
            'arah_angin'        => 'nullable|numeric|max:360',
            'kelembapan'        => 'nullable|numeric|min:0|max:100',
            'suhu'              => 'nullable|numeric',
            'curah_hujan'       => 'nullable|numeric|min:0',
        ]);
        $saveData = [
            'pos_pantau_id'    => $request->pos_pantau_id,
            'tanggal'          => $request->tanggal,
            'jam'              => $request->jam,
            'kecepatan_angin'  => $request->kecepatan_angin,
            'arah_angin'       => $request->arah_angin,
            'kelembapan'       => $request->kelembapan,
            'suhu'             => $request->suhu,
            'curah_hujan'      => $request->curah_hujan,
            'keterangan'       => $request->keterangan,
        ];

        try {
            DataKlimatologi::create($saveData);

            return redirect()->route('admin.hidrologi.klimatologi.index')
                ->with('success', 'Data klimatologi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data: '.$e->getMessage());
        }
    }

    public function show(DataKlimatologi $dataKlimatologi)
    {
        $dataKlimatologi->load('posPantau');

        if (request()->ajax()) {
            return response()->json([
                'data' => $dataKlimatologi,
                'pos_pantau_nama' => $dataKlimatologi->posPantau->nama_pos ?? '-',
            ]);
        }

        return view('admin.pages.dt_klimatologi.show', compact('dataKlimatologi'));
    }

    public function edit(DataKlimatologi $dataKlimatologi)
    {
        $dataKlimatologi->load('posPantau');
        return view('admin.pages.dt_klimatologi.edit', compact('dataKlimatologi'));
    }

    public function update(Request $request, DataKlimatologi $dataKlimatologi)
    {
        $request->validate([
            'pos_pantau_id'     => 'required|exists:pos_pantau,id',
            'kecepatan_angin'   => 'nullable|numeric|min:0',
            'arah_angin'        => 'nullable|string|max:50',
            'kelembapan'        => 'nullable|numeric|min:0|max:100',
            'suhu'              => 'nullable|numeric',
            'curah_hujan'       => 'nullable|numeric|min:0',
        ]);

        try {
            $saveData = [
                'pos_pantau_id'    => $request->pos_pantau_id,
                'tanggal'          => $request->tanggal,
                'jam'              => $request->jam,
                'kecepatan_angin'  => $request->kecepatan_angin,
                'arah_angin'       => $request->arah_angin,
                'kelembapan'       => $request->kelembapan,
                'suhu'             => $request->suhu,
                'curah_hujan'      => $request->curah_hujan,
                'keterangan'       => $request->keterangan,
            ];

            $dataKlimatologi->update($saveData);

            return redirect()->route('admin.hidrologi.klimatologi.index')
                ->with('success', 'Data klimatologi berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data: '.$e->getMessage());
        }
    }

    public function destroy(DataKlimatologi $dataKlimatologi)
    {
        try {
            $dataKlimatologi->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    public function importExcel()
    {
        return view('admin.pages.import', [
            'route_template' => route('admin.hidrologi.klimatologi.import-excel.template'),
            'route_process'  => route('admin.hidrologi.klimatologi.import-excel.process'),
            'mapping'        => [],
            'title'          => 'Import Data Klimatologi',
            'template_label' => 'Template Klimatologi',
        ]);
    }

    public function prcImport(Request $request)
    {
        try {
            $data = json_decode($request->input('data'), true);

            if (!$data || !is_array($data)) {
                return response()->json(['success' => false, 'message' => 'Data tidak valid.'], 422);
            }

            $posMap = PosPantau::pluck('id', 'nama_pos')->toArray();

            // helper kecil
            $toIntOrNull = function ($v) {
                if ($v === null || $v === '' ) return null;
                // normalisasi koma desimal jika ada (meskipun untuk int seharusnya tidak diperlukan)
                if (is_string($v)) $v = trim(str_replace(',', '.', $v));
                return is_numeric($v) ? (int) $v : null;
            };

            $parseExcelDate = function ($v) {
                if ($v === null || $v === '') return null;

                // normalisasi string
                $raw = is_string($v) ? trim(str_replace(',', '.', $v)) : $v;

                // jika numeric -> anggap serial excel
                if (is_numeric($raw)) {
                    try {
                        return ExcelDate::excelToDateTimeObject((float)$raw)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        // fallback ke parse string
                    }
                }

                // coba parse dengan Carbon
                try {
                    return Carbon::parse($v)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return null;
                }
            };

            $parseExcelTime = function ($v) {
                if ($v === null || $v === '') return null;

                $raw = is_string($v) ? trim(str_replace(',', '.', $v)) : $v;

                if (is_numeric($raw)) {
                    try {
                        // excel time biasanya pecahan hari, excel->DateTime akan bekerja
                        return ExcelDate::excelToDateTimeObject((float)$raw)->format('H:i:s');
                    } catch (\Throwable $e) {
                        // fallback
                    }
                }

                // coba parse string seperti "12:34", "12:34:56" atau "2024-05-10 12:34"
                try {
                    // Carbon dapat mem-parse "12:34" menjadi hari ini + waktu
                    return Carbon::parse($v)->format('H:i:s');
                } catch (\Throwable $e) {
                    return null;
                }
            };

            $inserted = [];
            foreach ($data as $index => $row) {
                $validator = Validator::make($row, [
                    'pos_pantau'       => ['required', 'string'],
                    'kecepatan_angin'  => ['nullable', 'numeric'],
                    'arah_angin'       => ['nullable', 'numeric'],
                    'kelembapan'       => ['nullable', 'numeric'],
                    'suhu'             => ['nullable', 'numeric'],
                    'curah_hujan'      => ['nullable', 'numeric'],
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal di baris ' . ($index + 2),
                        'errors'  => $validator->errors(),
                    ], 422);
                }

                $namaPos = $row['pos_pantau'];
                if (!isset($posMap[$namaPos])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Pos Pantau '{$namaPos}' tidak ditemukan (baris " . ($index + 2) . ")",
                    ], 422);
                }

                // tanggal aman (excel serial atau string)
                $tanggal = $parseExcelDate($row['tanggal'] ?? null) ?? now()->format('Y-m-d');

                // jam aman
                $jam = $parseExcelTime($row['jam'] ?? null);

                $inserted[] = [
                    'pos_pantau_id'    => $posMap[$namaPos],
                    'kecepatan_angin'  => $toIntOrNull($row['kecepatan_angin'] ?? null),
                    'arah_angin'       => $toIntOrNull($row['arah_angin'] ?? null),
                    'kelembapan'       => $toIntOrNull($row['kelembapan'] ?? null),
                    'suhu'             => $toIntOrNull($row['suhu'] ?? null),
                    'curah_hujan'      => $toIntOrNull($row['curah_hujan'] ?? null),
                    'tanggal'          => $tanggal,
                    'jam'              => $jam,
                    'keterangan'       => $row['keterangan'] ?? null,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }

            // insert batch
            DB::table('data_klimatologi')->insert($inserted);

            return response()->json(['success' => true, 'message' => 'Data berhasil diimport.']);
        } catch (\Throwable $th) {
            // untuk debugging, bisa juga log stack trace
            \Log::error('Import error: '.$th->getMessage()."\n".$th->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $th->getMessage(),
            ], 500);
        }
    }


    public function templateExcel()
    {
        $PosPantau = PosPantau::where('jenis_pos', 'Pos Klimatologi')->get();
        if ($PosPantau->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'Tidak ada pos tinggi muka air yang tersedia untuk template.');
        }
        $pos_pantau = json_decode(json_encode($PosPantau));
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Form Data');

        $sheet1->fromArray([['pos_pantau', 'tanggal', 'jam', 'kecepatan_angin', 'arah_angin', 'kelembapan', 'suhu', 'curah_hujan', 'keterangan']]);

        // Sheet referensi
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Referensi Pos');

        $sheet2->fromArray([['nama_pos', 'id', 'latitude', 'longitude']]);
        $row = 2;
        foreach ($pos_pantau as $pos) {
            $sheet2->setCellValue("A{$row}", $pos->nama_pos);
            $sheet2->setCellValue("B{$row}", $pos->id);
            $sheet2->setCellValue("C{$row}", $pos->latitude);
            $sheet2->setCellValue("D{$row}", $pos->longitude);
            $row++;
        }

        $validationRange = "'Referensi Pos'!A2:A" . ($row - 1);
        for ($i = 2; $i <= 100; $i++) {
            $validation = $sheet1->getCell("A{$i}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST)
                ->setErrorStyle(DataValidation::STYLE_STOP)
                ->setAllowBlank(true)
                ->setShowInputMessage(true)
                ->setShowErrorMessage(true)
                ->setShowDropDown(true)
                ->setFormula1($validationRange);
        }

        $filename = 'template_import_klimatologi.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
