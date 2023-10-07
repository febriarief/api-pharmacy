<?php

namespace App\Http\Requests\MasterItem\StockCard;

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
        return auth()->user()->hasPermissionTo('MasterItem.StockCard.Create', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'stock_card_id' => 'required',
            'type'          => 'required|in:IN,OUT',
            'qty'           => 'required|numeric'
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
            'stock_card_id.required' => 'Invalid param stock_card_id',
            'type.required'          => 'Kolom isian tipe tidak boleh kosong',
            'type.in'                => 'Kolom isian tipe harus bernilai IN atau OUT',
            'qty.required'           => 'Kolom isian qty tidak boleh kosong',
            'qty.numeric'            => 'Kolom isian qty harus berupa angka'
        ];
    }
}
