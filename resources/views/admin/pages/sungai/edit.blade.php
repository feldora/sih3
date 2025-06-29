@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Sungai</h1>
        <a href="{{ route('admin.sungai.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Sungai</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sungai.update', $sungai->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_sungai" class="form-label">Nama Sungai <span class="text-danger">*</span></label>
                            <input type="text" name="nama_sungai" id="nama_sungai" 
                                   value="{{ old('nama_sungai', $sungai->nama_sungai) }}" 
                                   class="form-control @error('nama_sungai') is-invalid @enderror" 
                                   placeholder="Masukkan nama sungai" required>
                            @error('nama_sungai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="panjang_sungai" class="form-label">Panjang Sungai (km) <span class="text-danger">*</span></label>
                            <input type="number" name="panjang_sungai" id="panjang_sungai" 
                                   value="{{ old('panjang_sungai', $sungai->panjang_sungai) }}" 
                                   class="form-control @error('panjang_sungai') is-invalid @enderror" 
                                   placeholder="Masukkan panjang sungai" 
                                   step="0.01" min="0" required>
                            @error('panjang_sungai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="luas_das" class="form-label">Luas DAS (km²) <span class="text-danger">*</span></label>
                            <input type="number" name="luas_das" id="luas_das" 
                                   value="{{ old('luas_das', $sungai->luas_das) }}" 
                                   class="form-control @error('luas_das') is-invalid @enderror" 
                                   placeholder="Masukkan luas DAS" 
                                   step="0.01" min="0" required>
                            @error('luas_das')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">DAS = Daerah Aliran Sungai</small>
                        </div>
                    </div>
                    <!-- Dropdown Wilayah Sungai -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="wilayah_sungai_id" class="form-label">Wilayah Sungai <span class="text-danger">*</span></label>
                            <select name="wilayah_sungai_id" id="wilayah_sungai_id" class="form-select @error('wilayah_sungai_id') is-invalid @enderror" required>
                                <option value="">Pilih Wilayah Sungai</option>
                                @foreach($wilayahSungai as $wilayah)
                                    <option value="{{ $wilayah->id }}" {{ old('wilayah_sungai_id', $sungai->wilayah_sungai_id) == $wilayah->id ? 'selected' : '' }}>{{ $wilayah->nama }}</option>
                                @endforeach
                            </select>
                            @error('wilayah_sungai_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Dropdown Status -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif" {{ old('status', $sungai->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $sungai->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Textarea GeoJSON -->
                <div class="mb-3">
                    <label for="geojson" class="form-label">GeoJSON</label>
                    <textarea name="geojson" id="geojson" class="form-control @error('geojson') is-invalid @enderror" rows="4" placeholder="Masukkan data GeoJSON (opsional)">{{ old('geojson', $sungai->geojson) }}</textarea>
                    @error('geojson')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.sungai.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
