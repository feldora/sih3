@extends('layouts.admin')

@section('title', 'Detail Data Tinggi Muka Air')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<div class="container mx-auto p-6">
    <!-- Header -->
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.hidrologi.tma.index') }}" class="btn btn-ghost mr-4">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-base-content">Detail Data Tinggi Muka Air</h1>
            <p class="text-base-content/70 mt-1">Tampilan detail data tinggi muka air</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.hidrologi.tma.edit', $dataTinggiMukaAir->id) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <button class="btn btn-error" onclick="deleteData({{ $dataTinggiMukaAir->id }})">
                <i class="fas fa-trash mr-2"></i>
                Hapus
            </button>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pos Pantau -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold text-lg">Pos Pantau</span>
                    </label>
                    <div class="p-4 bg-base-200 rounded-lg">
                        <p class="text-base-content font-medium">
                            {{ $dataTinggiMukaAir->posPantau ? $dataTinggiMukaAir->posPantau->nama : '-' }}
                        </p>
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold text-lg">Tanggal</span>
                    </label>
                    <div class="p-4 bg-base-200 rounded-lg">
                        <p class="text-base-content font-medium">
                            {{ $dataTinggiMukaAir->tanggal ? $dataTinggiMukaAir->tanggal->format('d/m/Y') : '-' }}
                        </p>
                    </div>
                </div>

                <!-- Jam -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold text-lg">Jam</span>
                    </label>
                    <div class="p-4 bg-base-200 rounded-lg">
                        <p class="text-base-content font-medium">
                            {{ $dataTinggiMukaAir->jam ? $dataTinggiMukaAir->jam->format('H:i') : '-' }} WIB
                        </p>
                    </div>
                </div>

                <!-- Tinggi Muka Air -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold text-lg">Tinggi Muka Air</span>
                    </label>
                    <div class="p-4 bg-base-200 rounded-lg">
                        <p class="text-base-content font-medium text-2xl">
                            {{ $dataTinggiMukaAir->tinggi_muka_air }} <span class="text-sm font-normal">cm</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="form-control mt-6">
                <label class="label">
                    <span class="label-text font-semibold text-lg">Keterangan</span>
                </label>
                <div class="p-4 bg-base-200 rounded-lg min-h-24">
                    <p class="text-base-content">
                        {{ $dataTinggiMukaAir->keterangan ?: 'Tidak ada keterangan' }}
                    </p>
                </div>
            </div>

            <!-- Metadata -->
            <div class="divider mt-8 mb-6"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Dibuat pada</span>
                    </label>
                    <p class="text-base-content/70 text-sm">
                        {{ $dataTinggiMukaAir->created_at ? $dataTinggiMukaAir->created_at->format('d/m/Y H:i:s') : '-' }}
                    </p>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Terakhir diupdate</span>
                    </label>
                    <p class="text-base-content/70 text-sm">
                        {{ $dataTinggiMukaAir->updated_at ? $dataTinggiMukaAir->updated_at->format('d/m/Y H:i:s') : '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<dialog id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-error">Konfirmasi Hapus</h3>
        <p class="py-4">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Batal</button>
            </form>
            <button id="confirmDelete" class="btn btn-error">Hapus</button>
        </div>
    </div>
</dialog>
@endsection

@push('scripts')
<script>
// Delete function
let deleteId = null;

function deleteData(id) {
    deleteId = id;
    document.getElementById('deleteModal').showModal();
}

$('#confirmDelete').click(function() {
    if (deleteId) {
        // Create a form for deletion
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('admin.hidrologi.tma.destroy', ':id') }}".replace(':id', deleteId);
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = $('meta[name="csrf-token"]').attr('content');
        form.appendChild(csrfInput);
        
        // Add method override
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Add to body and submit
        document.body.appendChild(form);
        form.submit();
    }
});

$(document).ready(function() {
    // Auto hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush