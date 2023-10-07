<?php

namespace App\Http\Requests\Purchase\PurchaseOrder;

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
        return auth()->user()->hasPermissionTo('Purchase.PurchaseOrder.Create', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'note'                                        => 'nullable|string',
            'detail.*.purchase_request_id'                => 'required|string',
            'detail.*.items.*.purchase_request_detail_id' => 'required',
            'detail.*.items.*.price'                      => 'required|numeric'
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
            'note.string'                                          => 'Kolom isian catatan harus berupa string.',
            'detail.*.purchase_request_id.required'                => 'Terdapat detail nomor PR yang masih kosong.',
            'detail.*.purchase_request_id.string'                  => 'Terdapat detail nomor PR yang tidak menggunakan format string.',
            'detail.*.items.*.purchase_request_detail_id.required' => 'Terdapat detail barang yang masih kosong.',
            'detail.*.items.*.price.required'                      => 'Terdapat detail harga yang masih kosong.',
            'detail.*.items.*.price.numeric'                       => 'Terdapat detail harga yang tidak menggunakan format angka.'
        ];
    }
}
