<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BukuStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $kodeBuku = Rule::unique('bukus', 'kode_buku');
        if ($buku = $this->route('buku')) {
            $kodeBuku->ignore($buku);
        }

        return [
            'kode_buku' => ['required', 'string', 'min:3', 'max:255', $kodeBuku],
            'judul' => ['required', 'string', 'min:3', 'max:255'],
            'penulis' => ['required', 'string', 'min:3', 'max:255'],
            'penerbit' => ['required', 'string', 'min:3', 'max:255'],
            'tahun_terbit' => ['required', 'integer', 'digits:4'],
            'stok' => ['required', 'integer', 'min:0'],
        ];
    }
    public function messages(): array
    {
        return [
            'kode_buku.required' => 'Kode buku harus diisi.',
            'kode_buku.unique' => 'Kode buku sudah digunakan.',
            'judul.required' => 'Judul buku harus diisi.',
            'judul.string' => 'Judul buku harus berupa teks.',
            'judul.min' => 'Judul buku minimal :min karakter.',
            'judul.max' => 'Judul buku maksimal :max karakter.',
            'penulis.required' => 'Nama penulis harus diisi.',
            'penulis.string' => 'Nama penulis harus berupa teks.',
            'penulis.min' => 'Nama penulis minimal :min karakter.',
            'penulis.max' => 'Nama penulis maksimal :max karakter.',
            'penerbit.required' => 'Nama penerbit harus diisi.',
            'penerbit.string' => 'Nama penerbit harus berupa teks.',
            'penerbit.min' => 'Nama penerbit minimal :min karakter.',
            'penerbit.max' => 'Nama penerbit maksimal :max karakter.',
            'tahun_terbit.required' => 'Tahun terbit harus diisi.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka.',
            'tahun_terbit.digits' => 'Tahun terbit harus terdiri dari :digits digit.',
            'jumlah_halaman.required' => 'Jumlah halaman harus diisi.',
            'jumlah_halaman.integer' => 'Jumlah halaman harus berupa angka.',
            'sinopsis.string' => 'Sinopsis harus berupa teks.',
            'stok.required' => 'Stok buku harus diisi.',
            'stok.integer' => 'Stok buku harus berupa angka.',
            'stok.min' => 'Stok buku tidak boleh kurang dari :min.'
        ];
    }
}   
