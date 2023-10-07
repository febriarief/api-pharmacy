<?php

namespace App\Http\Controllers\Api\Purchase;

// Import app nor providers below
use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\GoodReceived\IndexRequest;
use App\Http\Requests\Purchase\GoodReceived\StoreRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;

// Import models below
use App\Models\Purchase\GoodReceived;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\PurchaseOrderDetail;

class GoodReceivedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\Purchase\GoodReceived\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $input = $request->validated();
        $query = GoodReceived::query();

        $query->when($input['id'] ?? false, function($q) use($input) {
            return $q->where('id', 'like', '%' . $input['id'] . '%');
        });

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
     * @param  \App\Http\Requests\Purchase\GoodReceived\StoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();

            $id = GoodReceived::generateId();
            $input['id'] = $id;
            $input['created_by'] = $request->user()->name;

            $poIds = [];
            
            foreach ($input['detail'] as $detail) {
                $poIds[] = $detail['purchase_order_id'];
            }

            $gr = GoodReceived::create($input);

            $details = [];
            if (count($poIds) > 0) {
                $counter = 0;
                foreach($input['detail'] as $detail) {
                    foreach ($detail['items'] as $item) {
                        $poDetail = PurchaseOrderDetail::where('id', $item['purchase_order_detail_id'])->first();

                        $details[$counter]['good_received_id']  = $id;
                        $details[$counter]['purchase_order_id'] = $detail['purchase_order_id'];
                        $details[$counter]['item_name']         = $poDetail->item_name;
                        $details[$counter]['item_unit']         = $poDetail->item_unit;
                        $details[$counter]['supplier_name']     = $poDetail->supplier_name;
                        $details[$counter]['qty']               = $item['qty'];
                        $details[$counter]['qty_order']         = $poDetail->qty;

                        $counter++;
                    }
                }
            }

            $gr->goodReceivedDetail()->sync($details);

            DB::commit();
            return json_success_response(201, 'Data berhasil dibuat.');

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }

    /**
     * Download a PDF for the given Purchase Request.
     *
     * @param  \App\Models\Purchase\GoodReceived  $GoodReceived
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadPdf(GoodReceived $goodReceived)
    {
        $goodReceivedDetail = $goodReceived->goodReceivedDetail;

        $details = [];
        foreach($goodReceivedDetail as $detail) {
            if (!isset($details[$detail->purchase_order_id])) {
                $details[$detail->purchase_order_id] = [
                    'id'    => $detail->purchase_order_id,
                    'note'  => $detail->purchaseOrder->note,
                    'items' => [[
                        'item'      => $detail->item_name,
                        'supplier'  => $detail->supplier_name,
                        'qty_order' => $detail->qty_order,
                        'qty'       => $detail->qty,
                    ]]
                ];
            } else {
                $details[$detail->purchase_order_id]['items'][] = [
                    'item'      => $detail->item_name,
                    'supplier'  => $detail->supplier_name,
                    'qty_order' => $detail->qty_order,
                    'qty'       => $detail->qty,
                ];
            }
        }

        $storage = Storage::disk('gcs');
        $filepath = 'export/pdf/' . $goodReceived->id . '.pdf';
        if ($storage->exists($filepath)) {
            return json_success_response(200, null, [ 'filepath' => $filepath ]);
        }

        $pdf = PDF::loadView('pdf.good-received', [
            'goodReceived' => $goodReceived,
            'details'      => $details
        ])->setPaper('a4', 'potrait');

        $content = $pdf->download()->getOriginalContent();
        $storage->put($filepath, $content);

        return json_success_response(200, null, [ 'filepath' => $filepath ]);
    }
}
