<?php

namespace App\Http\Controllers\Api\MasterItem;

// Import app nor providers below
use App\Exports\StockCardExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterItem\StockCard\IndexRequest;
use App\Http\Requests\MasterItem\StockCard\StoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Excel;

// Import models below
use App\Models\MasterItem\Stock;
use App\Models\MasterItem\StockCard;

class StockCardController extends Controller
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
        $query = StockCard::query();

        $query->selectRaw('stock_cards.id as id, items.name as name, units.short as unit, stock_cards.qty as qty, stock_cards.qty_remain as qty_remain, stock_cards.type as type, stock_cards.note as note, stock_cards.created_at as created_at, stock_cards.updated_at as updated_at');
        $query->join('stocks', 'stock_cards.stock_id', '=', 'stocks.id');
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
                    $query->orderBy('stock_cards.updated_at', 'asc');
                    break;
                case 'update-desc':
                    $query->orderBy('stock_cards.updated_at', 'desc');
                    break;
                default:
                    break;        
            }
        } else {
            $query->orderBy('stock_cards.updated_at', 'desc');
        }

        if ($input['page'] ?? false) return json_success_response(200, '', $query->paginate(10));

        return json_success_response(200, '', $query->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\MasterItem\StockCard\StoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $input = $request->validated();
        $user  = $request->user();

        $stockCard = StockCard::find($input['stock_card_id']);
        if (!$stockCard) return json_error_response(404, 'Data kartu stok tidak ditemukan.');
        
        $stock = Stock::find($stockCard->stock_id);
        if (!$stock) return json_error_response(404, 'Data stok tidak ditemukan.');

        try {
            DB::beginTransaction();

            $qtyRemain = $stock->total;
            if ($input['type'] == 'IN') {
                $qtyRemain += $input['qty'];
            } else if ($input['type'] == 'OUT') {
                $qtyRemain -= $input['qty'];
            }

            StockCard::create([
                'stock_id'   => $stock->id,
                'type'       => $input['type'],
                'qty'        => $input['qty'],
                'qty_remain' => $qtyRemain,
                'note'       => 'Data diubah oleh ' . $user->name
            ]);

            $stock->total = $qtyRemain;
            $stock->save();

            DB::commit();
            return json_success_response(201, 'Data berhasil dibuat.');

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }

    /**
     * Export stock card data to an Excel file.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        $query = StockCard::query();

        if ($request->has('from') && $request->has('to')) {
            $query->whereDate('updated_at', '>=', $request->from)
                ->whereDate('updated_at', '<=', $request->to);
        }

        $data = $query->orderBy('updated_at', 'desc')->get();

        return Excel::download(new StockCardExport([
            'data' => $data
        ]), 'stock-card-report.xlsx');
    }
}
