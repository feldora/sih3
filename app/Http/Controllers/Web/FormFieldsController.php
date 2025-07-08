<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FormFieldsController extends Controller
{
    public function getFields(Request $request)
    {

        if (isset($this->form[$request->type])) {
            return response()->json([
                'success'   => true,
                'type'      => $request->type,
                'fillable'  =>  $this->form[$request->type]
            ]);
        } else {
            return response()->json([
                'success'   => false,
                'type'      => $request->type,
                'fillable'  =>  []
            ]);
        }

    }

    public $form = [
        'Pos Pantau' => [
            "jenis_pos" => "input",
            "kewenangan"    => "input",
            "tahun_pembangunan" => "input",
            'nama_pos'  => "mapping",
            'alamat'    => "mapping",
            'kabupaten' => "mapping",
            'kecamatan' => "mapping",
            'desa'      => "mapping",
            'nama_pengamat' => "mapping",
            'status'    => "mapping",],
        'wilayah Sungai'    => [],
        'Sungai'    => [],
    ];
}
