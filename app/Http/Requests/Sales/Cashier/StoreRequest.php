<?php

namespace App\Http\Requests\Sales\Cashier;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasPermissionTo('Sales.Cashier.Create', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'money_received'         => 'required',
            'sales_detail'           => 'required',
            'sales_detail.*.item_id' => 'required',
            'sales_detail.*.qty'     => 'required',
            'sales_detail.*.price'   => 'required'
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
            'money_received.required'           => 'Kolom isian uang diterima tidak boleh kosong',
            'sales_detail.required'             => 'Minimah harus ada 1 produk yang dipilih',
            'sales_detail.*.item_id.required'   => 'Terdapat satu nama barang yang belum dipilih',
            'sales_detail.*.item_id.qty'        => 'Terdapat satu barang yang belum di isi kolom qty',
            'sales_detail.*.item_id.price'      => 'Terdapat satu barang yang belum di isi kolom harga'
        ];
    }
}
