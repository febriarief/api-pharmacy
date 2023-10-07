<?php

namespace App\Http\Controllers\Api\Purchase;

// Import app nor providers below
use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\PurchaseRequest\IndexRequest;
use App\Http\Requests\Purchase\PurchaseRequest\StoreRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;

// Import models below
use App\Models\MasterItem\Item;
use App\Models\Purchase\PurchaseRequest;
use App\Models\Purchase\Supplier;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\Purchase\PurchaseRequest\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $input = $request->validated();
        $query = PurchaseRequest::query();

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
     * @param  \App\Http\Requests\Purchase\PurchaseRequest\StoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();

            $id = PurchaseRequest::generateId();
            $input['id'] = $id;
            $input['created_by'] = $request->user()->name;

            $pr = PurchaseRequest::create($input);

            $details = [];
            foreach($input['detail'] as $key => $detail) {
                $item = Item::where('id', $detail['item_id'])->first();
                $supplier = Supplier::where('id', $detail['supplier_id'])->first();

                $details[$key]['purchase_request_id'] = $pr->id;
                $details[$key]['item_name'] = $item->name;
                $details[$key]['item_unit'] = $item->unit->short;
                $details[$key]['supplier_name'] = $supplier->name;
                $details[$key]['qty'] = $detail['qty'];
            }

            $pr->purchaseRequestDetail()->sync($details);

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
     * @param  \App\Models\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadPdf(PurchaseRequest $purchaseRequest)
    {
        $storage = Storage::disk('gcs');
        $filepath = 'export/pdf/' . $purchaseRequest->id . '.pdf';
        if ($storage->exists($filepath)) {
            return json_success_response(200, null, [ 'filepath' => $filepath ]);
        }

        $pdf = PDF::loadView('pdf.purchase-request', [
            'purchaseRequest' => $purchaseRequest
        ])->setPaper('a4', 'potrait');

        $content = $pdf->download()->getOriginalContent();
        $storage->put($filepath, $content);

        return json_success_response(200, null, [ 'filepath' => $filepath ]);
    }
}
