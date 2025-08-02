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
                $tanggal = Date::excelToDateTimeObject($row['tanggal'] ?? now())->format('Y-m-d');
                $inserted[] = [
                    'pos_pantau_id'    => $posMap[$namaPos],
                    'kecepatan_angin'  => $row['kecepatan_angin'] ?? null,
                    'arah_angin'       => $row['arah_angin'] ?? null,
                    'kelembapan'       => $row['kelembapan'] ?? null,
                    'suhu'             => $row['suhu'] ?? null,
                    'curah_hujan'      => $row['curah_hujan'] ?? null,
                    'tanggal'          => $tanggal,
                    'jam'              => isset($row['jam']) ? Date::excelToDateTimeObject($row['jam'])->format('H:i:s') : null,
                    'keterangan'       => $row['keterangan'] ?? null,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }

            DB::table('data_klimatologi')->insert($inserted);

            return response()->json(['success' => true, 'message' => 'Data berhasil diimport.']);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function templateExcel()
    {
        $pos_pantau = PosPantau::all();

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
