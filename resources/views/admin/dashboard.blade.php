@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container mx-auto px-4">
    {{-- Overview Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Pos Pantau --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-primary">{{ number_format($overviewData['total_pos']) }}</div>
                <div class="text-sm text-gray-600">Total Pos Pantau</div>
            </div>
        </div>

        {{-- Wilayah Sungai --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-secondary">{{ number_format($overviewData['total_ws']) }}</div>
                <div class="text-sm text-gray-600">Wilayah Sungai</div>
            </div>
        </div>

        {{-- Total Data --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-accent">{{ number_format($overviewData['total_data']) }}</div>
                <div class="text-sm text-gray-600">Total Data Tersimpan</div>
            </div>
        </div>

        {{-- Update Terakhir --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body text-center">
                <div class="text-lg font-semibold text-info">
                    {{ $overviewData['last_update'] ? $overviewData['last_update']->format('d M Y') : 'Belum ada data' }}
                </div>
                <div class="text-sm text-gray-600">Update Terakhir</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Status Pos Pantau --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-lg mb-4">Status Pos Pantau</h3>
                
                <div class="space-y-3">
                    <div>
                        <div class="text-sm font-medium mb-2">Jenis Pos Pantau:</div>
                        @forelse($dataSummary['jenis_pos'] as $jenis => $total)
                            <div class="flex justify-between py-1">
                                <span class="capitalize">{{ str_replace('_', ' ', $jenis) }}</span>
                                <span class="font-medium">{{ number_format($total) }} pos</span>
                            </div>
                        @empty
                            <div class="text-gray-500">Belum ada data jenis pos</div>
                        @endforelse
                    </div>

                    <div class="divider my-3"></div>

                    <div>
                        <div class="text-sm font-medium mb-2">Status:</div>
                        @forelse($dataSummary['pos_status'] as $status => $total)
                            <div class="flex justify-between py-1">
                                <span class="capitalize flex items-center">
                                    @if($status === 'aktif')
                                        <span class="w-2 h-2 bg-success rounded-full mr-2"></span>
                                    @else
                                        <span class="w-2 h-2 bg-error rounded-full mr-2"></span>
                                    @endif
                                    {{ ucfirst($status) }}
                                </span>
                                <span class="font-medium">{{ number_format($total) }} pos</span>
                            </div>
                        @empty
                            <div class="text-gray-500">Belum ada data status</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Data --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-lg mb-4">Ringkasan Data</h3>
                
                <div class="space-y-3">
                    <div>
                        <div class="text-sm font-medium mb-2">Data Tersedia:</div>
                        <div class="flex justify-between py-1">
                            <span>Curah Hujan</span>
                            <span class="font-medium">{{ number_format($dataSummary['data_records']['curah_hujan']) }} record</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Tinggi Muka Air</span>
                            <span class="font-medium">{{ number_format($dataSummary['data_records']['tinggi_muka_air']) }} record</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Klimatologi</span>
                            <span class="font-medium">{{ number_format($dataSummary['data_records']['klimatologi']) }} record</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sebaran Pos by Kabupaten --}}
    <div class="card bg-base-100 shadow-xl mb-8">
        <div class="card-body">
            <h3 class="card-title text-lg mb-4">Sebaran Pos Pantau per Kabupaten</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($posByKabupaten as $item)
                    <div class="flex justify-between items-center p-3 bg-base-200 rounded-lg">
                        <span class="font-medium">{{ $item['kabupaten'] }}</span>
                        <span class="badge badge-primary">{{ $item['total'] }} pos</span>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500 py-8">
                        Belum ada data pos pantau
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Pos Pantau Terbaru --}}
    <div class="card bg-base-100 shadow-xl mb-8">
        <div class="card-body">
            <h3 class="card-title text-lg mb-4">Pos Pantau Terbaru</h3>
            
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Nama Pos</th>
                            <th>Jenis</th>
                            <th>Kabupaten</th>
                            <th>Tanggal Input</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestPosPantau as $pos)
                            <tr>
                                <td class="font-medium">{{ $pos['nama_pos'] }}</td>
                                <td>
                                    <span class="badge badge-outline">{{ ucfirst(str_replace('_', ' ', $pos['jenis_pos'])) }}</span>
                                </td>
                                <td>{{ $pos['kabupaten'] }}</td>
                                <td class="text-sm">{{ $pos['created_at']->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-gray-500 py-8">
                                    Belum ada data pos pantau
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .card {
        border: 1px solid rgba(0,0,0,0.1);
    }
    .badge {
        font-size: 0.75rem;
    }
</style>
@endpush

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

    // Auto refresh data setiap 5 menit (optional)
    // setInterval(function() {
    //     location.reload();
    // }, 300000);
});
</script>
@endpush