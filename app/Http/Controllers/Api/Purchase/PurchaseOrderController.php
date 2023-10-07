<?php

namespace App\Http\Controllers\Api\Purchase;

// Import app nor providers below
use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\PurchaseOrder\StoreRequest;
use App\Http\Requests\Purchase\PurchaseOrder\IndexRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;

// Import models below
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\PurchaseRequestDetail;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\Purchase\PurchaseOrder\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $input = $request->validated();
        $query = PurchaseOrder::query();

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
     * @param  \App\Http\Requests\Purchase\PurchaseOrder\StoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();

            $id = PurchaseOrder::generateId();
            $input['id'] = $id;
            $input['created_by'] = $request->user()->name;

            $prIds = [];
            $totalPrice = 0;
            
            foreach ($input['detail'] as $detail) {
                $prIds[] = $detail['purchase_request_id'];
                foreach ($detail['items'] as $item) {
                    $totalPrice += $item['price'];
                }
            }

            $input['total'] = $totalPrice;
            $po = PurchaseOrder::create($input);

            $details = [];
            if (count($prIds) > 0) {
                $counter = 0;
                foreach($input['detail'] as $detail) {
                    foreach ($detail['items'] as $item) {
                        $prDetails = PurchaseRequestDetail::where('id', $item['purchase_request_detail_id'])->first();

                        $details[$counter]['purchase_order_id']   = $id;
                        $details[$counter]['purchase_request_id'] = $detail['purchase_request_id'];
                        $details[$counter]['item_name']           = $prDetails->item_name;
                        $details[$counter]['item_unit']           = $prDetails->item_unit;
                        $details[$counter]['supplier_name']       = $prDetails->supplier_name;
                        $details[$counter]['qty']                 = $prDetails->qty;
                        $details[$counter]['price']               = $item['price'];

                        $counter++;
                    }
                }
            }

            $po->purchaseOrderDetail()->sync($details);

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
     * @param  \App\Models\PurchaseOrder  $purchaseRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadPdf(PurchaseOrder $purchaseOrder)
    {
        $poDetails = $purchaseOrder->purchaseOrderDetail;

        $details = [];
        foreach($poDetails as $detail) {
            if (!isset($details[$detail->purchase_request_id])) {
                $details[$detail->purchase_request_id] = [
                    'id'    => $detail->purchase_request_id,
                    'note'  => $detail->purchaseRequest->note,
                    'items' => [[
                        'item'      => $detail->item_name,
                        'item_unit' => $detail->item_unit,
                        'supplier'  => $detail->supplier_name,
                        'qty'       => $detail->qty,
                        'price'     => $detail->price
                    ]
                ]];
            } else {
                $details[$detail->purchase_request_id]['items'][] = [
                    'item'      => $detail->item_name,
                    'item_unit' => $detail->item_unit,
                    'supplier'  => $detail->supplier_name,
                    'qty'       => $detail->qty,
                    'price'     => $detail->price
                ];
            }
        }

        $storage = Storage::disk('gcs');
        $filepath = 'export/pdf/' . $purchaseOrder->id . '.pdf';
        if ($storage->exists($filepath)) {
            return json_success_response(200, null, [ 'filepath' => $filepath ]);
        }

        $pdf = PDF::loadView('pdf.purchase-order', [
            'purchaseOrder' => $purchaseOrder,
            'details'       => $details
        ])->setPaper('a4', 'potrait');

        $content = $pdf->download()->getOriginalContent();
        $storage->put($filepath, $content);

        return json_success_response(200, null, [ 'filepath' => $filepath ]);
    }
}
