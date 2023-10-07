<?php

namespace App\Http\Requests\Summary\SummarySales;

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
        return auth()->user()->hasPermissionTo('Summary.SummarySales.View', 'api');
    }
}
