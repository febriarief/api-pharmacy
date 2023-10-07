<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'     => 'required|email|string|max:255',
            'password'  => 'required|max:255'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'    => 'Kolom isian email tidak boleh kosong',
            'email.email'       => 'Kolom isian email harus berupa format email',
            'email.string'      => 'Kolom isian email harus berupa string',
            'email.max'         => 'Kolom isian email maksimal terdiri dari 255 karakter',
            'password.required' => 'Kolom isian kata sandi tidak boleh kosong',
            'password.max'      => 'Kolom isian kata sandi maksimal terdiri dari 255 karakter'
        ];
    }
}
