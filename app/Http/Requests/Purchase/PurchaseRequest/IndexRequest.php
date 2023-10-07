<?php

namespace App\Http\Requests\Purchase\PurchaseRequest;

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
        return auth()->user()->hasPermissionTo('Purchase.PurchaseRequest.View', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id'   => 'nullable|max:255',
            'page' => 'nullable|max:255',
            'with' => 'nullable|max:255',
            'sort' => 'nullable|in:id-asc,id-desc|max:255'
        ];
    }
}
