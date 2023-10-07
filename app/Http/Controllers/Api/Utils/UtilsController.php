<?php

namespace App\Http\Controllers\Api\Utils;

// Import app nor providers below
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

// Import models below
use App\Models\MasterItem\Stock;

class UtilsController extends Controller
{
    public function getDate(Request $request) 
    {
        setlocale(LC_TIME, 'IND');
        $carbonDate = Carbon::now();

        return json_success_response(200, null, [
            'day'        => $carbonDate->isoFormat('dddd'),
            'date'       => $carbonDate->isoFormat('DD'),
            'month'      => $carbonDate->isoFormat('MM'),
            'month_name' => $carbonDate->isoFormat('MMMM'),
            'year'       => $carbonDate->isoFormat('YYYY'),
            'hour'       => $carbonDate->isoFormat('HH'),
            'minute'     => $carbonDate->isoFormat('mm'),
            'second'     => $carbonDate->isoFormat('ss')
        ]);
    }

    public function stockReminder(Request $request)
    {
        return json_success_response(200, null, Stock::where('total', '<=', 500)->with('item')->orderBy('total', 'asc')->get());
    }
}
