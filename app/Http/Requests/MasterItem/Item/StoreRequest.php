<?php

namespace App\Http\Requests\MasterItem\Item;

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
        return auth()->user()->hasPermissionTo('MasterItem.Item.Create', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'    => 'required|string|max:255|unique:items',
            'unit_id' => 'required',
            'price'   => 'nullable'
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
            'name.required'    => 'Kolom isian nama tidak boleh kosong',
            'name.string'      => 'Kolom isian nama harus berupa string',
            'name.max'         => 'Kolom isian nama tidak boleh lebih dari 255 karakter',
            'name.unique'      => 'Kolom isian nama sudah ada sebelumnya',
            'unit_id.required' => 'Kolom isian satuan tidak boleh kosong'
        ];
    }
}
