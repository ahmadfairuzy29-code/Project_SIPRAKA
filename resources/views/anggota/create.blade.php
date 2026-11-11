@extends('layouts.app')

@section('title', 'Tambah Anggota')

@section('content')
    <h1>Tambah Anggota</h1>
    <form action="{{ route('admin.anggota.store') }}" method="POST">
        @if ($errors->any())
            <div style="color:red; border:1px solid red; padding:10px;
        margin-bottom:10px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </div>
        @endif
        @csrf
        <label>Nama:</label>
        <input type="text" name="nama" value="{{ old('nama') }}" required><br>
        <label>Alamat:</label>
        <input type="text" name="alamat" value="{{ old('alamat') }}" required><br>
        <label>No Telp:</label>
        <input type="text" name="no_telp" value="{{ old('no_telp') }}" required><br>
        <label>Tanggal lahir:</label>
        <input type="text" name="tgl_lhr" value="{{ old('tgl_lhr') }}" required><br>
        <button type="submit">Simpan</button>
</form>
@endsection