<?php

namespace App\Http\Requests\Purchase\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasPermissionTo('Purchase.Supplier.Update', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone'   => 'nullable|max:20',
            'email'   => 'nullable|email|max:255'
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
            'name.required'  => 'Kolom isian nama tidak boleh kosong',
            'name.string'    => 'Kolom isian nama harus berupa string',
            'name.max'       => 'Kolom isian nama tidak boleh lebih dari 255 karakter',
            'address.string' => 'Kolom isian alamat harus berupa string',
            'phone.max'      => 'Kolom isian nomor handphone tidak boleh lebih dari 20 karakter',
            'email.email'    => 'Kolom isian email harus berupa format email',
            'email.max'      => 'Kolom isian email tidak boleh lebih dari 255 karakter'
        ];
    }
}
