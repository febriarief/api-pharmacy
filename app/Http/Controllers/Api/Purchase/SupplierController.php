<?php

namespace App\Http\Controllers\Api\Purchase;

// Import app nor providers below
use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\Supplier\DestroyRequest;
use App\Http\Requests\Purchase\Supplier\IndexRequest;
use App\Http\Requests\Purchase\Supplier\StoreRequest;
use App\Http\Requests\Purchase\Supplier\UpdateRequest;
use Illuminate\Support\Facades\DB;

// Import models below
use App\Models\Purchase\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\Purchase\Supplier\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $input = $request->validated();
        $query = Supplier::query();

        $query->when($input['name'] ?? false, function($q) use($input) {
            return $q->where('name', 'like', '%' . $input['name'] . '%');
        });

        if ($input['sort'] ?? false) {
            switch($input['sort']) {
                case 'name-asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name-desc':
                    $query->orderBy('name', 'desc');
                    break;
                default:
                    break;        
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        if ($input['page'] ?? false) return json_success_response(200, '', $query->paginate(10));

        return json_success_response(200, '', $query->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Purchase\Supplier\StoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();

            if ($input['phone'] ?? false) {
                $input['phone'] = format_phonenumber($input['phone']);
            }

            Supplier::create($input);
            DB::commit();
            return json_success_response(201, 'Data berhasil dibuat.');

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Purchase\Supplier\UpdateRequest  $request
     * @param  \App\Models\Purchase\Supplier  $supplier
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Supplier $supplier)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();

            if ($input['phone'] ?? false) {
                $input['phone'] = format_phonenumber($input['phone']);
            }

            $supplier->update($input);
            
            DB::commit();
            return json_success_response(200, 'Data berhasil disimpan.');

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\Purchase\Supplier\Destroy  $request
     * @param  \App\Models\Purchase\Supplier  $supplier
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DestroyRequest $request, Supplier $supplier)
    {
        $input = $request->validated();
        
        try {
            DB::beginTransaction();
            $supplier->delete();
            DB::commit();
            return json_success_response(200, 'Data berhasil dihapus.');

        } catch(\Exception $e) {
            DB::rollBack();
            return json_error_response(500, $e->getMessage());
        }
    }
}
