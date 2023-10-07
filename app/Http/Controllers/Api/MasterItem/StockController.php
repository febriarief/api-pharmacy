<?php

namespace App\Http\Controllers\Api\MasterItem;

// Import app nor providers below
use App\Exports\StockExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterItem\Stock\IndexRequest;
use Excel;

// Import models below
use App\Models\MasterItem\Stock;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\MasterItem\Stock\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $input = $request->validated();
        $query = Stock::query();

        $query->selectRaw('stocks.id as id, items.name as name, units.short as unit, stocks.total as total, stocks.created_at as created_at, stocks.updated_at as updated_at');
        $query->join('items', 'stocks.item_id', '=', 'items.id');
        $query->join('units', 'items.unit_id', '=', 'units.id');

        $query->when($input['name'] ?? false, function($q) use($input) {
            return $q->where('items.name', 'like', '%' . $input['name'] . '%');
        });

        if ($input['sort'] ?? false) {
            switch($input['sort']) {
                case 'name-asc':
                    $query->orderBy('items.name', 'asc');
                    break;
                case 'name-desc':
                    $query->orderBy('items.name', 'desc');
                    break;
                case 'update-asc':
                    $query->orderBy('stocks.updated_at', 'asc');
                    break;
                case 'update-desc':
                    $query->orderBy('stocks.updated_at', 'desc');
                    break;
                case 'total-asc':
                    $query->orderBy('stocks.total', 'asc');
                    break;
                case 'total-desc':
                    $query->orderBy('stocks.total', 'desc');
                    break;
                default:
                    break;        
            }
        } else {
            $query->orderBy('items.name', 'asc');
        }

        if ($input['page'] ?? false) return json_success_response(200, '', $query->paginate(10));

        return json_success_response(200, '', $query->get());
    }

    /**
     * Export stock data to an Excel file.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        $stock = new Stock(); 
        return Excel::download(new StockExport([
            'data' => $stock->get()->sortBy(function($query) {
                return $query->item->name;
            })->all()
        ]), 'stock-report.xlsx');
    }
}
