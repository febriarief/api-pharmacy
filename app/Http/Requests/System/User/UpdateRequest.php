<?php

namespace App\Http\Requests\System\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasPermissionTo('System.User.Update', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'     => 'required|string|max:255',
            'role'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore(request()->id)],
            'password' => 'nullable|string|max:255'
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
            'name.required'     => 'Kolom isian nama tidak boleh kosong',
            'name.string'       => 'Kolom isian nama harus berupa string',
            'name.max'          => 'Kolom isian nama tidak boleh lebih dari 255 karakter',
            'role.required'     => 'Kolom pilihan role tidak boleh kosong',
            'role.string'       => 'Kolom pilihan role harus berupa string',
            'role.max'          => 'Kolom pilihan role tidak boleh lebih dari 255 karakter',
            'email.required'    => 'Kolom isian email tidak boleh kosong',
            'email.email'       => 'Kolom isian email harus berupa format email',
            'email.max'         => 'Kolom isian email tidak boleh lebih dari 255 karakter',
            'email.unique'      => 'Kolom isian email sudah ada sebelumnya',
            'password.string'   => 'Kolom isian kata sandi harus berupa string',
            'password.max'      => 'Kolom isian kata sandi tidak boleh lebih dari 255 karakter'
        ];
    }
}
