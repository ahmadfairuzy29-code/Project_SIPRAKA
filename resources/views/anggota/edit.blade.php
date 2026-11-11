@extends('layouts.app')

@section('title', 'Edit Anggota')

@section('content')
    <h1>Edit Anggota</h1>
    @if ($errors->any())
        <div style="color:red; border:1px solid red; padding:10px; margin-bottom:10px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.anggota.update', $anggota->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label>Nama:</label>
        <input type="text" name="nama" value="{{ old('nama', $anggota->nama) }}" required><br>
        <label>Alamat:</label>
        <input type="text" name="alamat" value="{{ old('alamat', $anggota->alamat) }}" required><br>
        <label>No Telp:</label>
        <input type="text" name="no_telp" value="{{ old('no_telp', $anggota->no_telp) }}" required><br>
        <label>Tanggal Lahir:</label>
        <input type="text" name="tgl_lhr" value="{{ old('tgl_lhr', $anggota->tgl_lhr) }}" required><br>
        <button type="submit">Perbarui</button>
    </form>
@endsection