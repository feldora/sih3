<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataCurahHujan;
use App\Models\PosPantau;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DataHujanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DataCurahHujan::with('posPantau');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_pos', function ($row) {
                    return $row->posPantau->nama_pos ?? '-';
                })
                ->addColumn('kategori', function ($row) {
                    // Determine category based on rainfall amount
                    $curahHujan = $row->curah_hujan;
                    if ($curahHujan == 0) {
                        return '<span class="badge badge-ghost">Tidak Hujan</span>';
                    } elseif ($curahHujan <= 5) {
                        return '<span class="badge badge-info">Hujan Ringan</span>';
                    } elseif ($curahHujan <= 20) {
                        return '<span class="badge badge-warning">Hujan Sedang</span>';
                    } elseif ($curahHujan <= 50) {
                        return '<span class="badge badge-error">Hujan Lebat</span>';
                    } else {
                        return '<span class="badge badge-error">Hujan Sangat Lebat</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="flex gap-1 justify-center">';
                    $btn .= '<a href="' . route('admin.meteorologi.curah-hujan.edit', $row->id) . '" class="btn btn-primary btn-sm" title="Edit">';
                    $btn .= '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
                    $btn .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />';
                    $btn .= '</svg></a>';
                    $btn .= '<button type="button" data-id="' . $row->id . '" class="btn btn-error btn-sm delete-btn" title="Hapus">';
                    $btn .= '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
                    $btn .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />';
                    $btn .= '</svg></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->editColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal)->format('d/m/Y');
                })
                ->editColumn('curah_hujan', function ($row) {
                    return number_format($row->curah_hujan, 1) . ' mm';
                })
                ->rawColumns(['action', 'kategori'])
                ->make(true);
        }

        $pos_pantau = PosPantau::where('jenis_pos', 'curah_hujan')->get();
        return view('admin.pages.dt_hujan.index', compact('pos_pantau'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pos_pantau = PosPantau::where('jenis_pos', 'curah_hujan')->get();
        return view('admin.pages.dt_hujan.create', compact('pos_pantau'));
    }

    public function importExcel()
    {
        // Logic for displaying the import form
        return view('admin.pages.import', [
            'route_template' => route('admin.meteorologi.curah-hujan.import-excel.template'),
            'route_process' => route('admin.meteorologi.curah-hujan.import-excel.process'),
            'mapping' => [
            ],
            'title' => 'Import Data Curah Hujan',
            'template_label' => 'Template Curah Hujan',
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
            $posMap = PosPantau::where('jenis_pos', 'Pos Curah Hujan')
                ->pluck('id', 'nama_pos')
                ->toArray();

            $inserted = [];
            foreach ($data as $index => $row) {
                $validator = Validator::make($row, [
                    'pos_pantau' => ['required', 'string'],
                    // 'tanggal' => ['required', 'date'],
                    'curah_hujan (mm)' => ['required', 'numeric'],
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
                        'message' => "Pos pantau '{$namaPos}' tidak ditemukan pada baris " . ($index + 2),
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
                    'curah_hujan' => $row['curah_hujan (mm)'],
                    'jam' => isset($row['jam']) ? $row['jam'] : null,
                    'kategori' => isset($row['kategori']) ? $row['kategori'] : null,
                    'keterangan' => isset($row['keterangan']) ? $row['keterangan'] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Simpan batch
            DB::table('data_curah_hujan')->insert($inserted);

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
        $pos_pantau = PosPantau::where('jenis_pos', 'Pos Curah Hujan')->get();
        if ($pos_pantau->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'Tidak ada pos pantau curah hujan yang tersedia untuk template.');
        }

        $spreadsheet = new Spreadsheet();

        // === Sheet 1 === //
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Form Data');

        $sheet1->setCellValue('A1', 'pos_pantau');
        $sheet1->setCellValue('B1', 'tanggal');
        $sheet1->setCellValue('C1', 'curah_hujan (mm)');
        $sheet1->setCellValue('D1', 'Kategori');
        $sheet1->setCellValue('E1', 'keterangan');

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
        $fileName = 'template_import_curah_hujan.xlsx';

        // Output to browser
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        
    }

    public function show()
    {
        // Logic for showing a specific data entry
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pos_pantau_id' => 'required|exists:pos_pantau,id',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'curah_hujan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DataCurahHujan::create($request->all());
            return redirect()
                ->route('admin.meteorologi.curah-hujan.index')
                ->with('success', 'Data curah hujan berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = DataCurahHujan::findOrFail($id);
        $pos_pantau = PosPantau::where('jenis_pos', 'curah_hujan')->get();
        return view('admin.pages.dt_hujan.edit', compact('data', 'pos_pantau'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'pos_pantau_id' => 'required|exists:pos_pantau,id',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'curah_hujan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $data = DataCurahHujan::findOrFail($id);
            $data->update($request->all());
            
            return redirect()
                ->route('admin.meteorologi.curah-hujan.index')
                ->with('success', 'Data curah hujan berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data = DataCurahHujan::findOrFail($id);
            $data->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Data curah hujan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


function convertToYYYYMMDD($date_string) {
    // Array regex yang sama dari sebelumnya untuk mendeteksi format
    $date_regexes = [
        'DD/MM/YYYY'   => '^(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[0-2])/((19|20)\d{2})$',
        'MM/DD/YYYY'   => '^(0[1-9]|1[0-2])/(0[1-9]|[12][0-9]|3[01])/((19|20)\d{2})$',
        'YYYY/MM/DD'   => '^((19|20)\d{2})/(0[1-9]|1[0-2])/(0[1-9]|[12][0-9]|3[01])$',
        'YYYY-MM-DD'   => '^((19|20)\d{2})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$',
        'DD-MM-YYYY'   => '^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[0-2])-((19|20)\d{2})$',
        'DD.MM.YYYY'   => '^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[0-2])\.((19|20)\d{2})$',
        'DD Mon YYYY'  => '^(0[1-9]|[12][0-9]|3[01]) (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) ((19|20)\d{2})$',
        'Mon DD, YYYY' => '^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) (0[1-9]|[12][0-9]|3[01]), ((19|20)\d{2})$',
        'DD MMMM YYYY' => '^(0[1-9]|[12][0-9]|3[01]) (Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember) ((19|20)\d{2})$',
        'MMMM DD, YYYY' => '^(January|February|March|April|May|June|July|August|September|October|November|December) (0[1-9]|[12][0-9]|3[01]), ((19|20)\d{2})$',
    ];

    // Array untuk mapping nama bulan ke angka
    $month_map = [
        'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'May' => '05', 'Jun' => '06',
        'Jul' => '07', 'Aug' => '08', 'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12',
        'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04', 'Mei' => '05', 'Juni' => '06',
        'Juli' => '07', 'Agustus' => '08', 'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12',
        'January' => '01', 'February' => '02', 'March' => '03', 'April' => '04', 'May' => '05', 'June' => '06',
        'July' => '07', 'August' => '08', 'September' => '09', 'October' => '10', 'November' => '11', 'December' => '12',
    ];

    foreach ($date_regexes as $format_name => $regex) {
        // SOLUSI: Ganti delimiter '/' dengan '#'
        if (preg_match("#" . $regex . "#i", $date_string, $matches)) {
            // ... (sisa kode fungsi sama seperti sebelumnya)
            $day = '';
            $month = '';
            $year = '';

            switch ($format_name) {
                case 'DD/MM/YYYY':
                case 'DD-MM-YYYY':
                case 'DD.MM.YYYY':
                    $day = $matches[1];
                    $month = $matches[2];
                    $year = $matches[3];
                    break;
                case 'MM/DD/YYYY':
                    $month = $matches[1];
                    $day = $matches[2];
                    $year = $matches[3];
                    break;
                case 'YYYY/MM/DD':
                case 'YYYY-MM-DD':
                    $year = $matches[1];
                    $month = $matches[2];
                    $day = $matches[3];
                    break;
                case 'DD Mon YYYY':
                case 'DD MMMM YYYY': // Dengan nama bulan penuh Indonesia
                    $day = $matches[1];
                    $month = $month_map[$matches[2]]; // Konversi nama bulan ke angka
                    $year = $matches[3];
                    break;
                case 'Mon DD, YYYY':
                case 'MMMM DD, YYYY': // Dengan nama bulan penuh English
                    $month = $month_map[$matches[1]]; // Konversi nama bulan ke angka
                    $day = $matches[2];
                    $year = $matches[3];
                    break;
                default:
                    return false;
            }

            if (checkdate((int)$month, (int)$day, (int)$year)) {
                return sprintf("%04d-%02d-%02d", (int)$year, (int)$month, (int)$day);
            } else {
                return false;
            }
        }
    }

    return false;
}
}