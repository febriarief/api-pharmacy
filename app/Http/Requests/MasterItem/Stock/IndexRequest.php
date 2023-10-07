<?php

namespace App\Http\Requests\MasterItem\Stock;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->hasPermissionTo('MasterItem.Stock.View', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'nullable|max:255',
            'page' => 'nullable|max:255',
            'sort' => 'nullable|in:name-asc,name-desc,update-asc,update-desc,total-asc,total-desc|max:255'
        ];
    }
}
