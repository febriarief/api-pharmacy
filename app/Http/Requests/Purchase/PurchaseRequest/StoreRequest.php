<?php

namespace App\Http\Requests\Purchase\PurchaseRequest;

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
        return auth()->user()->hasPermissionTo('Purchase.PurchaseRequest.Create', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'note'                 => 'nullable|string',
            'detail.*.item_id'     => 'required',
            'detail.*.supplier_id' => 'required',
            'detail.*.qty'         => 'required|numeric'
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
            'note.string'                   => 'Kolom isian catatan harus berupa string.',
            'detail.*.item_id.required'     => 'Terdapat detail barang yang masih kosong.',
            'detail.*.supplier_id.required' => 'Terdapat detail supplier yang masih kosong.',
            'detail.*.qty.required'         => 'Terdapat detail qty yang masih kosong.',
            'detail.*.qty.numeric'          => 'Terdapat detail qty yang tidak menggunakan format angka.',
        ];
    }
}
