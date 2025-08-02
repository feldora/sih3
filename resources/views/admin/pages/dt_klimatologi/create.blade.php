@extends('layouts.admin')

@section('title', 'Tambah Data Klimatologi')

@section('content')
<div class="container mx-auto px-4">
    <!-- Breadcrumb -->
    <div class="breadcrumbs text-sm mb-6">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a></li>
            <li><a href="{{ route('admin.hidrologi.klimatologi.index') }}" class="text-primary">Data Klimatologi</a></li>
            <li>Tambah Data</li>
        </ul>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <!-- Card Header -->
        <div class="card-header px-6 py-4 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="card-title text-xl font-bold">Tambah Data Klimatologi</h3>
                <a href="{{ route('admin.hidrologi.klimatologi.index') }}" class="btn btn-ghost btn-sm">
                    <x-icons.back /> Kembali
                </a>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body p-6">
            <!-- Success Alert -->
            @if(session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif

            <!-- Error Alert -->
            @if($errors->any())
                <div class="alert alert-error mb-4">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.hidrologi.klimatologi.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pos Pantau -->
                    <x-dropdown-tom-select
                        name="pos_pantau_id"
                        label="Pos Pantau"
                        ajaxUrl="/api/pos-pantau"
                        SearchFlter="jenis_pos=Pos Klimatologi"
                        :multiple="false"
                        :selected="[]"
                    />

                    <!-- Tanggal -->
                    <div class="form-control w-full">
                        <label class="label" for="tanggal">
                            <span class="label-text font-medium">Tanggal <span class="text-error">*</span></span>
                        </label>
                        <input type="date" name="tanggal" id="tanggal"
                            value="{{ old('tanggal', date('Y-m-d')) }}"
                            class="input input-bordered w-full @error('tanggal') input-error @enderror">
                        @error('tanggal')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- Jam -->
                    <div class="form-control w-full">
                        <label class="label" for="jam">
                            <span class="label-text font-medium">Jam</span>
                        </label>
                        <input type="time" name="jam" id="jam"
                            value="{{ old('jam', date('H:i')) }}"
                            class="input input-bordered w-full @error('jam') input-error @enderror">
                        @error('jam')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- Kecepatan Angin -->
                    <div class="form-control w-full">
                        <label class="label" for="kecepatan_angin">
                            <span class="label-text font-medium">Kecepatan Angin (m/s)</span>
                        </label>
                        <input type="number" name="kecepatan_angin" id="kecepatan_angin"
                            value="{{ old('kecepatan_angin') }}" step="0.01" min="0"
                            class="input input-bordered w-full @error('kecepatan_angin') input-error @enderror">
                        @error('kecepatan_angin')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- Arah Angin -->
                    <div class="form-control w-full">
                        <label class="label" for="arah_angin">
                            <span class="label-text font-medium">Arah Angin</span>
                        </label>
                        <input type="text" name="arah_angin" id="arah_angin"
                            value="{{ old('arah_angin') }}"
                            class="input input-bordered w-full @error('arah_angin') input-error @enderror"
                            placeholder="Contoh: Timur Laut">
                        @error('arah_angin')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- Kelembapan -->
                    <div class="form-control w-full">
                        <label class="label" for="kelembapan">
                            <span class="label-text font-medium">Kelembapan (%)</span>
                        </label>
                        <input type="number" name="kelembapan" id="kelembapan"
                            value="{{ old('kelembapan') }}" min="0" max="100"
                            class="input input-bordered w-full @error('kelembapan') input-error @enderror">
                        @error('kelembapan')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- Suhu -->
                    <div class="form-control w-full">
                        <label class="label" for="suhu">
                            <span class="label-text font-medium">Suhu (°C)</span>
                        </label>
                        <input type="number" name="suhu" id="suhu"
                            value="{{ old('suhu') }}" step="0.1"
                            class="input input-bordered w-full @error('suhu') input-error @enderror">
                        @error('suhu')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <!-- Curah Hujan -->
                    <div class="form-control w-full">
                        <label class="label" for="curah_hujan">
                            <span class="label-text font-medium">Curah Hujan (mm)</span>
                        </label>
                        <input type="number" name="curah_hujan" id="curah_hujan"
                            value="{{ old('curah_hujan') }}" step="0.1"
                            class="input input-bordered w-full @error('curah_hujan') input-error @enderror">
                        @error('curah_hujan')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>
                </div>
                {{-- keterangan --}}
                <x-text-editor
                    name="keterangan"
                    label="Keterangan"
                    :value="old('keterangan')"
                    placeholder="Masukkan keterangan tambahan jika ada"
                    class="mt-4"
                />
                <!-- Tombol Aksi -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-base-300">
                    <button type="submit" class="btn btn-primary">
                        <x-icons.save /> Simpan Data
                    </button>

                    <a href="{{ route('admin.hidrologi.klimatologi.index') }}" class="btn btn-outline">
                        <x-icons.cancel /> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')
  <script>
    window.addEventListener('load', function() {
      if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded yet.');
        return;
      }

      function showToast(message, type = 'info') {
        
        const toast = document.createElement('div');
        toast.className = `toast toast-top toast-end`;
        toast.innerHTML = `
          <div class="alert alert-${type}">
            <span>${message}</span>
          </div>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
      }

      @if (session('error'))
        showToast("{{ session('error') }}", "error");
      @endif

      @if (session('success'))
        showToast("{{ session('success') }}", "success");
      @endif

      @if (session('warning'))
        showToast("{{ session('warning') }}", "warning");
      @endif
    });
  </script>
@endpush
