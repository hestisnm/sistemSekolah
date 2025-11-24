<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|unique:dataadmin,username',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,guru,siswa',
            'nama_guru' => 'required_if:role,guru',
            'mapel' => 'required_if:role,guru',
            'nama_siswa' => 'required_if:role,siswa',
            'tb' => 'required_if:role,siswa|nullable|numeric',
            'bb' => 'required_if:role,siswa|nullable|numeric'
        ];
    }
}
