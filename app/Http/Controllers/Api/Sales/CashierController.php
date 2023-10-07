<?php

namespace App\Http\Controllers\Api\Sales;

// Import app nor providers below
use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\Cashier\IndexRequest;
use App\Http\Requests\Sales\Cashier\StoreRequest;
use App\Models\MasterItem\Item;
use Illuminate\Support\Facades\DB;

// Import models below
use App\Models\MasterItem\Stock;
use App\Models\Sales\Sales;

class CashierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\Sales\Cashier\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $input = $request->validated();
        $query = Sales::query();

        $query->when($input['with'] ?? false, function($q) use($input) {
            $relations = explode(',', $input['with']);
            return $q->with($relations);
        });

        if ($input['sort'] ?? false) {
            switch($input['sort']) {
                case 'id-asc':
                    $query->orderBy('id', 'asc');
                    break;
                case 'id-desc':
                    $query->orderBy('id', 'desc');
                    break;
                default:
                    break;        
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        if ($input['page'] ?? false) return json_success_response(200, '', $query->paginate(10));

        return json_success_response(200, '', $query->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Sales\Cashier\StoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();

            $total = 0;
            $salesDetail = [];

            // Item stock validation
            foreach($input['sales_detail'] as $k => $detail) {
                $item = Item::find($detail['item_id']);
                $stock = Stock::where('item_id', $detail['item_id'])->first();

                if ($stock->total == 0 || ($stock->total - $detail['qty']) < 0) {
                    return json_error_response(422, 'Stock barang "' . $item->name . '" tidak mencukupi');
                }


                $salesDetail[$k]['item_id']   = $detail['item_id'];
                $salesDetail[$k]['item_name'] = $item->name;
                $salesDetail[$k]['qty']       = $detail['qty'];
                $salesDetail[$k]['price']     = $detail['price'];

                $total = $total + ($detail['qty'] * $detail['price']);
            }

            $change = $input['money_received'] - $total;
            if ($change < 0) {
                return json_error_response(422, "Uang yang diterima tidak cukup.");
            }

            $input['total'] = $total;
            $input['money_change'] = $change;
            $input['cashier_name'] = $request->user()->name;

            $sales = Sales::create($input);
            $sales->salesDetail()->sync($salesDetail);

            DB::commit();
            return json_success_response(201, 'Penjualan berhasil disimpan. Uang kembaliannya adalah: Rp ' . number_format($change, 0, ',', '.'));

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }
}
