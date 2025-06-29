@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Sungai</h1>
        <div>
            <a href="{{ route('admin.sungai.edit', $sungai->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.sungai.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Sungai</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 40%;">Nama Sungai</td>
                            <td>: {{ $sungai->nama_sungai }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Panjang Sungai</td>
                            <td>: {{ number_format($sungai->panjang_sungai, 2) }} km</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Luas DAS</td>
                            <td>: {{ number_format($sungai->luas_das, 2) }} km²</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Wilayah Sungai</td>
                            <td>: {{ $sungai->wilayahSungai->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status</td>
                            <td>:
                                @if($sungai->status == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat</td>
                            <td>: {{ $sungai->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diperbarui</td>
                            <td>: {{ $sungai->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            @if($sungai->hasil_uji_kualitas_air)
                <div class="mt-4">
                    <h6 class="fw-bold text-primary">Hasil Uji Kualitas Air</h6>
                    <div class="bg-light p-3 rounded">
                        {{ $sungai->hasil_uji_kualitas_air }}
                    </div>
                </div>
            @endif

            @if($sungai->geojson)
                <div class="mt-4">
                    <h6 class="fw-bold text-primary">GeoJSON</h6>
                    <pre class="bg-light p-3 rounded" style="max-height:200px;overflow:auto;">{{ Str::limit($sungai->geojson, 500) }}</pre>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
