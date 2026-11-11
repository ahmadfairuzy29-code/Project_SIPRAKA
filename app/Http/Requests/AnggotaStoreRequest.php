<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AnggotaStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'min:3', 'max:255'],
            'alamat' => ['required', 'string', 'min:3', 'max:255'],
            'no_telp' => ['required', 'string', 'min:10', 'max:15'],
            'tgl_lhr' => ['required', 'date'],
        ];
    }
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama anggota harus diisi.',
            'nama.string' => 'Nama anggota harus berupa teks.',
            'nama.min' => 'Nama anggota minimal :min karakter.',
            'nama.max' => 'Nama anggota maksimal :max karakter.',
            'alamat.required' => 'Alamat anggota harus diisi.',
            'alamat.string' => 'Alamat anggota harus berupa teks.',
            'alamat.min' => 'Alamat anggota minimal :min karakter.',
            'alamat.max' => 'Alamat anggota maksimal :max karakter.',
            'no_telp.required' => 'Nomor telepon anggota harus diisi.',
            'no_telp.string' => 'Nomor telepon anggota harus berupa teks.',
            'no_telp.min' => 'Nomor telepon anggota minimal :min karakter.',
            'no_telp.max' => 'Nomor telepon anggota maksimal :max karakter.',
            'tgl_lhr.required' => 'Tanggal lahir anggota harus diisi.',
            'tgl_lhr.date' => 'Tanggal lahir anggota harus berupa tanggal yang valid (YYYY-MM-DD).',
        ];
    }
}
