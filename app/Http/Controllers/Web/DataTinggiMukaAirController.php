<?php

namespace App\Http\Controllers\Web;

use App\Models\DataTinggiMukaAir;
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

class DataTinggiMukaAirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DataTinggiMukaAir::with('posPantau')
                ->select('data_tinggi_muka_air.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('pos_pantau_nama', function($row) {
                    return $row->posPantau ? $row->posPantau->nama : '-';
                })
                ->addColumn('tanggal_formatted', function($row) {
                    return $row->tanggal ? $row->tanggal->format('d/m/Y') : '-';
                })
                ->addColumn('jam_formatted', function($row) {
                    return $row->jam ? $row->jam->format('H:i') : '-';
                })
                ->addColumn('tinggi_muka_air_formatted', function($row) {
                    return $row->tinggi_muka_air . ' cm';
                })
                ->addColumn('action', function($row) {
                    $btn = '<div class="flex gap-2">';
                    $btn .= '<button class="btn btn-sm btn-info" onclick="showDetail('.$row->id.')">
                                <i class="fas fa-eye"></i>
                            </button>';
                    $btn .= '<a href="'.route('admin.hidrologi.tma.edit', $row->id).'" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>';
                    $btn .= '<button class="btn btn-sm btn-error" onclick="deleteData('.$row->id.')">
                                <i class="fas fa-trash"></i>
                            </button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.pages.dt_tma.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $posPantauList = PosPantau::all();
        return view('admin.pages.dt_tma.create', compact('posPantauList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pos_pantau_id' => 'required|exists:pos_pantau,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'tinggi_muka_air' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            DataTinggiMukaAir::create($request->all());
            
            return redirect()->route('admin.hidrologi.tma.index')
                ->with('success', 'Data tinggi muka air berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DataTinggiMukaAir $dataTinggiMukaAir)
    {
        $dataTinggiMukaAir->load('posPantau');
        
        if (request()->ajax()) {
            return response()->json([
                'data' => $dataTinggiMukaAir,
                'pos_pantau_nama' => $dataTinggiMukaAir->posPantau ? $dataTinggiMukaAir->posPantau->nama : '-',
                'tanggal_formatted' => $dataTinggiMukaAir->tanggal ? $dataTinggiMukaAir->tanggal->format('d/m/Y') : '-',
                'jam_formatted' => $dataTinggiMukaAir->jam ? $dataTinggiMukaAir->jam->format('H:i') : '-',
            ]);
        }

        return view('admin.pages.dt_tma.show', compact('dataTinggiMukaAir'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataTinggiMukaAir $dataTinggiMukaAir)
    {
        $posPantauList = PosPantau::all();
        return view('admin.pages.dt_tma.edit', compact('dataTinggiMukaAir', 'posPantauList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataTinggiMukaAir $dataTinggiMukaAir)
    {
        $request->validate([
            'pos_pantau_id' => 'required|exists:pos_pantau,id',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'tinggi_muka_air' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            $dataTinggiMukaAir->update($request->all());
            
            return redirect()->route('admin.hidrologi.tma.index')
                ->with('success', 'Data tinggi muka air berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataTinggiMukaAir $dataTinggiMukaAir)
    {
        try {
            if ($dataTinggiMukaAir->delete()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data tinggi muka air berhasil dihapus.'
                ]);
            }
            
            return redirect()->route('admin.hidrologi.tma.index')
                ->with('success', 'Data tinggi muka air berhasil dihapus.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function importExcel()
    {
        // Logic for displaying the import form
        return view('admin.pages.import', [
            'route_template' => route('admin.hidrologi.tma.import-excel.template'),
            'route_process' => route('admin.hidrologi.tma.import-excel.process'),
            'mapping' => [
            ],
            'title' => 'Import Data Tinggi Muka Air',
            'template_label' => 'Template Tinggi Muka Air',
        ]);
    }

    public function prcImport(Request $request)
    {
        try {
            $data = json_decode($request->input('data'), true);

            if (!$data || !is_array($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid.',
                ], 422);
            }

            // Ambil mapping nama_pos => id
            $posMap = PosPantau::where('jenis_pos', 'Pos TMA')
                ->pluck('id', 'nama_pos')
                ->toArray();

            $inserted = [];
            foreach ($data as $index => $row) {
                $validator = Validator::make($row, [
                    'pos_pantau' => ['required', 'string'],
                    // 'tanggal' => ['required', 'date'],
                    'tinggi_muka_air (cm)' => ['required', 'numeric'],
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal di baris ' . ($index + 2),
                        'errors' => $validator->errors(),
                    ], 422);
                }

                $namaPos = $row['pos_pantau'];
                if (!isset($posMap[$namaPos])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Pos TMA '{$namaPos}' tidak ditemukan pada baris " . ($index + 2),
                    ], 422);
                }
                if (!preg_match('/\d{4}-\d{2}-\d{2}/', $row['tanggal'])) {
                    $tanggal = Date::excelToDateTimeObject($row['tanggal'])->format('Y-m-d');
                // }elseif (!preg_match('/^(19|20)\d{2}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/', $row['tanggal'])) {
                //     $tanggal = $this->convertToYYYYMMDD($row['tanggal']);
                }else {
                    $tanggal = $row['tanggal'];
                }

                $inserted[] = [
                    'pos_pantau_id' => $posMap[$namaPos],
                    'tanggal' => $tanggal,
                    'tinggi_muka_air' => $row['tinggi_muka_air (cm)'],
                    'jam' => isset($row['jam']) ? $row['jam'] : null,
                    // 'kategori' => isset($row['kategori']) ? $row['kategori'] : null,
                    'keterangan' => isset($row['keterangan']) ? $row['keterangan'] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Simpan batch
            DB::table('data_tinggi_muka_air')->insert($inserted);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diimport.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $th->getMessage(),
                'errors' => $th->getTraceAsString(),
            ], 500);
        }
    }

    public function templateExcel()
    {
        $pos_pantau = PosPantau::where('jenis_pos', 'Pos TMA')->get();
        if ($pos_pantau->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'Tidak ada pos tinggi muka air yang tersedia untuk template.');
        }

        $spreadsheet = new Spreadsheet();

        // === Sheet 1 === //
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Form Data');

        $sheet1->setCellValue('A1', 'pos_pantau');
        $sheet1->setCellValue('B1', 'tanggal');
        $sheet1->setCellValue('C1', 'tinggi_muka_air (cm)');
        $sheet1->setCellValue('D1', 'keterangan');

        $sheet1->getStyle('B:B')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        
        // === Sheet 2 === //
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Referensi Pos');

        $sheet2->setCellValue('A1', 'nama_pos');
        $sheet2->setCellValue('B1', 'id');
        $sheet2->setCellValue('C1', 'latitude');
        $sheet2->setCellValue('D1', 'longitude');

        $row = 2;
        foreach ($pos_pantau as $pos) {
            $sheet2->setCellValue("A{$row}", $pos->nama_pos);
            $sheet2->setCellValue("B{$row}", $pos->id);
            $sheet2->setCellValue("C{$row}", $pos->latitude);
            $sheet2->setCellValue("D{$row}", $pos->longitude);
            $row++;
        }

        // === Dropdown Data Validation di Sheet 1 kolom A === //
        // Buat named range untuk data dropdown
        $validationRange = "'Referensi Pos'!A2:A" . ($row - 1);
        for ($i = 2; $i <= 100; $i++) {
            $validation = $sheet1->getCell("A{$i}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1($validationRange);
        }

        // === Download file Excel === //
        $fileName = 'template_import_tinggi_muka_air.xlsx';

        // Output to browser
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        
    }

}