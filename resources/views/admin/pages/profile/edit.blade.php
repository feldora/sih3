@extends('layouts.admin')

@section('content')
<div>
    <h1 class="text-2xl font-bold mb-4">Edit Profil</h1>
    <form action="{{ route('admin.profile.update', auth()->user()->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <x-input label="Nama" name="name" type="text" :value="old('name', auth()->user()->name)" required class="flex-1" />

        <x-input label="Email" name="email" type="email" :value="old('email', auth()->user()->email)" required class="flex-1" />

        <x-input label="Password (Biarkan kosong jika tidak ingin mengubah)" name="password" type="password" class="flex-1" />

        <x-input label="Konfirmasi Password" name="password_confirmation" type="password" class="flex-1" />

        <div class="form-control flex flex-row justify-end">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script>
    </script>
@endpush
