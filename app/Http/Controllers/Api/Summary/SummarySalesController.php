<?php

namespace App\Http\Controllers\Api\Summary;

// Import app nor providers below
use App\Http\Controllers\Controller;
use App\Http\Requests\Summary\SummarySales\IndexRequest;

// Import models below
use App\Models\Summary\SummarySales;

class SummarySalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\Summary\SummarySales\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $input = $request->validated();
        $query = SummarySales::query();
        $query->selectRaw('MONTH(created_at) as month, total as total');

        $query->whereYear('created_at', now())
            ->orderBy('id', 'desc');

        $summary = $query->get();

        $months = [
            1 => 'Jan', 
            2 => 'Feb', 
            3 => 'Mar',
            4 => 'Apr', 
            5 => 'Mei',
            6 => 'Jun', 
            7 => 'Jul', 
            8 => 'Agu', 
            9 => 'Sep', 
            10 => 'Okt', 
            11 => 'Nov', 
            12 => 'Des'
        ];
        
        $data = [];
        $index = 0;
        foreach($months as $k => $month) {
            $data[$index] = [
                'label' => $month,
                'y'     => $summary->where('month', $k)->first()->total ?? 0
            ];

            $index++;
        }

        return json_success_response(200, '', $data);
    }
}
