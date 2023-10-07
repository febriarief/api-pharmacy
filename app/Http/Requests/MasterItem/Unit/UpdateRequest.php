<?php

namespace App\Http\Requests\MasterItem\Unit;

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
        return auth()->user()->hasPermissionTo('MasterItem.Unit.Update', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $id = $this->route('unit')->id;

        return [
            'name' => [
                'required', 
                'string',
                'max:50',
                Rule::unique('units')->ignore($id)
            ],
            'short' => [
                'required', 
                'string',
                'max:50',
                Rule::unique('units')->ignore($id)
            ]
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
            'name.max'       => 'Kolom isian nama tidak boleh lebih dari 50 karakter',
            'name.unique'    => 'Kolom isian nama sudah ada sebelumnya',
            'short.required' => 'Kolom isian singkatan tidak boleh kosong',
            'short.string'   => 'Kolom isian singkatan harus berupa string',
            'short.max'      => 'Kolom isian singkatan tidak boleh lebih dari 50 karakter',
            'short.unique'   => 'Kolom isian singkatan sudah ada sebelumnya'
        ];
    }
}
